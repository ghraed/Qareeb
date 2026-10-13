import test from 'node:test';
import assert from 'node:assert/strict';
import { setupInfiniteScroll } from '../../resources/js/infinite-scroll.js';

function setup() {
    let intersect;
    let click;
    let observations = 0;
    const attributes = {};
    const items = { appended: [], setAttribute() {}, append(...children) { this.appended.push(...children); } };
    const status = { textContent: '' };
    const more = {
        hidden: false,
        setAttribute(key, value) { attributes[key] = value; },
        removeAttribute(key) { delete attributes[key]; },
        addEventListener(_, callback) { click = callback; },
    };
    const root = {
        dataset: { infiniteScroll: 'providers', nextUrl: '/providers?category=Photography&page=2' },
        querySelector(selector) {
            return { '[data-scroll-items]': items, '[data-scroll-status]': status, '[data-scroll-more]': more, '[data-scroll-sentinel]': {} }[selector];
        },
    };
    globalThis.IntersectionObserver = class {
        constructor(callback) { intersect = callback; }
        observe() { observations++; }
        disconnect() {}
    };
    globalThis.DOMParser = class {
        parseFromString(html) {
            const incoming = JSON.parse(html);
            return { querySelectorAll: () => [{
                dataset: { infiniteScroll: 'providers', nextUrl: incoming.nextUrl },
                querySelector: () => ({ children: incoming.items }),
            }] };
        }
    };
    setupInfiniteScroll(root);
    return { root, items, status, more, attributes, intersect: () => intersect([{ isIntersecting: true }]), click: () => click({ preventDefault() {} }), observations: () => observations };
}

const tick = () => new Promise(resolve => setImmediate(resolve));

test('scroll appends batches, preserves filters, prevents overlapping requests and stops at the end', async () => {
    const list = setup();
    const urls = [];
    let complete;
    globalThis.fetch = url => { urls.push(url); return new Promise(resolve => { complete = resolve; }); };
    list.intersect();
    list.intersect();
    assert.equal(urls.length, 1);
    assert.equal(list.status.textContent, 'Loading more…');
    complete({ ok: true, text: async () => JSON.stringify({ nextUrl: '/providers?category=Photography&page=3', items: ['second batch'] }) });
    await tick();
    assert.deepEqual(list.items.appended, ['second batch']);
    assert.equal(list.attributes.href, '/providers?category=Photography&page=3');
    list.intersect();
    complete({ ok: true, text: async () => JSON.stringify({ nextUrl: '', items: ['last batch'] }) });
    await tick();
    assert.deepEqual(list.items.appended, ['second batch', 'last batch']);
    assert.equal(list.more.hidden, true);
    assert.equal(list.status.textContent, 'All items loaded.');
    list.intersect();
    assert.equal(urls.length, 2);
});

test('failure leaves the batch available for keyboard retry and does not auto retry', async () => {
    const list = setup();
    globalThis.fetch = async () => ({ ok: false });
    list.intersect();
    await tick();
    assert.equal(list.observations(), 1);
    assert.match(list.status.textContent, /try again/);
    assert.equal(list.root.dataset.nextUrl, '/providers?category=Photography&page=2');
    assert.deepEqual(list.items.appended, []);
    globalThis.fetch = async () => ({ ok: true, text: async () => JSON.stringify({ nextUrl: '', items: ['retried batch'] }) });
    list.click();
    await tick();
    assert.deepEqual(list.items.appended, ['retried batch']);
});

test('a redirected sign-in response never appends content or loses the retry URL', async () => {
    const list = setup();
    globalThis.fetch = async () => ({ ok: true, redirected: true });
    list.intersect();
    await tick();
    assert.deepEqual(list.items.appended, []);
    assert.match(list.status.textContent, /try again/);
    assert.ok(list.root.dataset.nextUrl);
});
