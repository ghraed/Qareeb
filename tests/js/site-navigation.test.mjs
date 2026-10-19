import test from 'node:test';
import assert from 'node:assert/strict';
import siteNavigation from '../../resources/js/site-navigation.js';

function setup(t, desktop = false) {
    const originals = ['window', 'document', 'requestAnimationFrame'].map(key => [key, Object.getOwnPropertyDescriptor(globalThis, key)]);
    t.after(() => {
        for (const [key, descriptor] of originals) {
            if (descriptor) Object.defineProperty(globalThis, key, descriptor);
            else delete globalThis[key];
        }
    });
    const media = { matches: desktop };
    globalThis.window = { matchMedia: () => media };
    globalThis.document = { body: { style: { overflow: '' } }, activeElement: null };
    globalThis.requestAnimationFrame = callback => callback();
    const controls = Array.from({ length: 3 }, () => ({ focus() { document.activeElement = this; } }));
    const nav = siteNavigation();
    let menuOpen = nav.menuOpen;
    nav.$watch = (_, callback) => Object.defineProperty(nav, 'menuOpen', {
        get: () => menuOpen,
        set: value => { menuOpen = value; callback(value); },
    });
    nav.$nextTick = callback => callback();
    nav.$refs = { menuClose: controls[0], menuToggle: controls[2], menuPanel: { querySelectorAll: () => controls } };
    nav.init();
    return { nav, controls, media };
}

test('mobile menu locks body scroll, focuses close, and returns focus to the opener', t => {
    const { nav, controls } = setup(t);
    nav.openMenu();
    assert.equal(nav.menuOpen, true);
    assert.equal(document.body.style.overflow, 'hidden');
    assert.equal(document.activeElement, controls[0]);
    nav.closeMenu();
    assert.equal(nav.menuOpen, false);
    assert.equal(document.body.style.overflow, '');
    assert.equal(document.activeElement, controls[2]);
});

test('mobile menu traps forward and backward tab at its boundaries', t => {
    const { nav, controls } = setup(t);
    nav.openMenu();
    let prevented = 0;
    const event = shiftKey => ({ shiftKey, preventDefault() { prevented++; } });
    nav.trapFocus(event(true));
    assert.equal(document.activeElement, controls[2]);
    nav.trapFocus(event(false));
    assert.equal(document.activeElement, controls[0]);
    controls[1].focus();
    nav.trapFocus(event(false));
    assert.equal(document.activeElement, controls[1]);
    assert.equal(prevented, 2);
});

test('desktop resize closes the mobile menu and releases scroll without stealing focus', t => {
    const { nav, controls, media } = setup(t);
    nav.openMenu();
    controls[1].focus();
    media.matches = true;
    nav.syncViewport();
    assert.equal(nav.isDesktop, true);
    assert.equal(nav.menuOpen, false);
    assert.equal(document.body.style.overflow, '');
    assert.equal(document.activeElement, controls[1]);
    nav.trapFocus({ shiftKey: false, preventDefault() { assert.fail('Desktop focus must remain free'); } });
});

test('closed menu does not trap focus and destruction releases body scroll', t => {
    const { nav } = setup(t);
    nav.trapFocus({ shiftKey: true, preventDefault() { assert.fail('Closed menu must not trap focus'); } });
    nav.openMenu();
    nav.destroy();
    assert.equal(document.body.style.overflow, '');
});
