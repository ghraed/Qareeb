import test from 'node:test';
import assert from 'node:assert/strict';
import toast from '../../resources/js/toast.js';

test('initial success toast dismisses after five seconds', t => {
    t.mock.timers.enable({ apis: ['setTimeout'] });
    const feedback = toast({ message: 'Profile saved.' });
    feedback.init();
    assert.equal(feedback.visible, true);
    assert.equal(feedback.message, 'Profile saved.');
    t.mock.timers.tick(4999);
    assert.equal(feedback.visible, true);
    t.mock.timers.tick(1);
    assert.equal(feedback.visible, false);
    assert.equal(feedback.timer, null);
});

test('error toast stays visible until dismissed and cancels an earlier success timer', t => {
    t.mock.timers.enable({ apis: ['setTimeout'] });
    const feedback = toast();
    feedback.show({ message: 'Saved' });
    t.mock.timers.tick(4000);
    feedback.show({ message: 'Save failed', type: 'error' });
    t.mock.timers.tick(10000);
    assert.equal(feedback.visible, true);
    assert.equal(feedback.type, 'error');
    assert.equal(feedback.message, 'Save failed');
    assert.equal(feedback.timer, null);
    feedback.dismiss();
    assert.equal(feedback.visible, false);
});

test('replacing a success toast restarts its dismissal timer and destroy clears it', t => {
    t.mock.timers.enable({ apis: ['setTimeout'] });
    const feedback = toast();
    feedback.show({ message: 'First' });
    t.mock.timers.tick(4000);
    feedback.show({ message: 'Second', type: 'unknown' });
    t.mock.timers.tick(1000);
    assert.equal(feedback.visible, true);
    assert.equal(feedback.type, 'success');
    feedback.destroy();
    assert.equal(feedback.visible, false);
    assert.equal(feedback.timer, null);
});

test('missing and blank toast messages do not replace visible feedback', () => {
    const feedback = toast({ message: 'Important error', type: 'error' });
    feedback.init();
    for (const message of ['', '  ', null, 42, undefined]) feedback.show({ message });
    feedback.show();
    assert.equal(feedback.message, 'Important error');
    assert.equal(feedback.type, 'error');
    assert.equal(feedback.visible, true);
    feedback.destroy();
});
