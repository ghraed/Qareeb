import test from 'node:test';
import assert from 'node:assert/strict';
import providerFilter from '../../resources/js/provider-filter.js';

function setup() {
    const changes = [];
    const filter = providerFilter();
    filter.$refs = {
        select: {
            options: [{ value: '', text: 'All categories' }, { value: 'Photography', text: 'Photography' }, { value: 'Electrician', text: 'Electrician' }],
            value: '',
            dispatchEvent(event) { changes.push({ type: event.type, bubbles: event.bubbles, value: this.value }); },
        },
        trigger: { focus() {} },
        list: { querySelectorAll() { return []; } },
    };
    filter.$nextTick = callback => callback();
    filter.init();
    return { filter, changes };
}

test('opening and dismissing the filter leaves its submitted value unchanged', () => {
    const { filter, changes } = setup();
    filter.toggle();
    assert.equal(filter.open, true);
    assert.equal(filter.$refs.select.value, '');
    assert.deepEqual(changes, []);
    filter.onKey({ key: 'Escape' });
    assert.equal(filter.open, false);
    assert.equal(filter.value, '');
});

test('choosing an option updates the submitted value and notifies district/city bindings', () => {
    const { filter, changes } = setup();
    filter.toggle();
    filter.choose(1);
    assert.equal(filter.label, 'Photography');
    assert.equal(filter.$refs.select.value, 'Photography');
    assert.equal(filter.open, false);
    assert.deepEqual(changes, [{ type: 'change', bubbles: true, value: 'Photography' }]);
});

test('keyboard navigation waits for explicit selection and supports typeahead', () => {
    const { filter, changes } = setup();
    const key = key => filter.onKey({ key, preventDefault() {} });
    key('ArrowDown');
    key('ArrowDown');
    assert.equal(filter.active, 1);
    assert.deepEqual(changes, []);
    key('Enter');
    assert.equal(filter.value, 'Photography');
    key('e');
    assert.equal(filter.active, 2);
    key('Enter');
    assert.equal(filter.value, 'Electrician');
});

test('city options follow the enabled district and refresh after a district reset', () => {
    const { filter } = setup();
    const beirut = { disabled: false };
    const keserwan = { disabled: true };
    filter.$refs.select.options = [
        { value: '', text: 'All cities in district' },
        { value: 'Achrafieh', text: 'Achrafieh', parentElement: beirut },
        { value: 'Jounieh', text: 'Jounieh', parentElement: keserwan },
    ];
    filter.refreshOptions();
    assert.deepEqual(filter.options.map(option => option.value), ['', 'Achrafieh']);
    filter.choose(1);
    beirut.disabled = true;
    keserwan.disabled = false;
    filter.$refs.select.value = '';
    filter.refreshOptions();
    assert.equal(filter.label, 'All cities in district');
    assert.deepEqual(filter.options.map(option => option.value), ['', 'Jounieh']);
    keserwan.disabled = true;
    filter.refreshOptions();
    assert.deepEqual(filter.options.map(option => option.value), ['']);
});
