import test from 'node:test';
import assert from 'node:assert/strict';
import availabilityToggle from '../../resources/js/availability-toggle.js';

function setup(t, patch) {
    const events = [];
    const originalWindow = Object.getOwnPropertyDescriptor(globalThis, 'window');
    t.after(() => {
        if (originalWindow) Object.defineProperty(globalThis, 'window', originalWindow);
        else delete globalThis.window;
    });
    globalThis.window = {
        axios: { patch },
        dispatchEvent: event => events.push(event),
    };
    const form = { action: '/business/profile/hiring-availability', elements: { namedItem: () => ({ value: 'csrf-token' }) } };
    return { events, form };
}

test('availability saves with CSRF and broadcasts the server-confirmed state', async t => {
    let request;
    const { events, form } = setup(t, async (...args) => {
        request = args;
        return { data: { is_available_for_hire: true } };
    });
    const toggle = availabilityToggle(false);
    await toggle.save(form);
    assert.deepEqual(request, [form.action, { is_available_for_hire: true }, {
        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': 'csrf-token' },
    }]);
    assert.equal(toggle.available, true);
    assert.equal(toggle.saving, false);
    assert.deepEqual(events.map(({ type, detail }) => [type, detail]), [
        ['availability-saving', { saving: true }],
        ['availability-updated', { available: true }],
        ['toast', { message: 'You are now marked as available for hire.', type: 'success' }],
        ['availability-saving', { saving: false }],
    ]);
});

test('availability blocks overlapping requests across navigation and profile toggles', async t => {
    let finish;
    let calls = 0;
    const { form } = setup(t, () => { calls++; return new Promise(resolve => { finish = resolve; }); });
    const first = availabilityToggle(true);
    const second = availabilityToggle(true);
    const pending = first.save(form);
    await first.save(form);
    await second.save(form);
    assert.equal(calls, 1);
    assert.equal(first.available, true);
    finish({ data: { is_available_for_hire: false } });
    await pending;
    assert.equal(first.available, false);
    assert.equal(first.saving, false);
});

for (const [label, response, message] of [
    ['validation failure', { errors: { is_available_for_hire: ['Choose a valid status.'] }, message: 'Invalid' }, 'Choose a valid status.'],
    ['server failure', { message: 'Please sign in again.' }, 'Please sign in again.'],
    ['network failure', null, 'Availability could not be saved. Please try again.'],
]) {
    test(`availability preserves saved state after ${label} and allows retry`, async t => {
        let fail = true;
        const { events, form } = setup(t, async () => {
            if (fail) throw { response: response ? { data: response } : undefined };
            return { data: { is_available_for_hire: false } };
        });
        const toggle = availabilityToggle(true);
        await toggle.save(form);
        assert.equal(toggle.available, true);
        assert.equal(toggle.saving, false);
        assert.equal(toggle.error, message);
        assert.equal(events.some(event => event.type === 'availability-updated'), false);
        assert.deepEqual(events.find(event => event.type === 'toast').detail, { message, type: 'error' });
        fail = false;
        await toggle.save(form);
        assert.equal(toggle.available, false);
        assert.equal(toggle.error, '');
    });
}

test('malformed availability response cannot change the saved state', async t => {
    const { form } = setup(t, async () => ({ data: { is_available_for_hire: 'true' } }));
    const toggle = availabilityToggle(false);
    await toggle.save(form);
    assert.equal(toggle.available, false);
    assert.equal(toggle.saving, false);
    assert.equal(toggle.error, 'Availability could not be saved. Please try again.');
});
