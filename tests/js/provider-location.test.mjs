import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

test('district removal clears orphan cities and select-all toggles only valid coverage', () => {
    const source = readFileSync('resources/views/business/profile/edit.blade.php', 'utf8');
    const script = source.match(/<script>([\s\S]*?)<\/script>/)[1];
    const context = vm.createContext({});
    vm.runInContext(script, context);
    const form = context.businessProfileEditForm({
        available: false,
        districtCities: { Beirut: ['Achrafieh', 'Ras Beirut'], Keserwan: ['Jounieh'] },
        districts: ['Beirut', 'Keserwan'],
        selectedDistricts: ['Beirut', 'Keserwan'],
        selectedCities: ['Achrafieh', 'Jounieh'],
    });
    form.$refs = {};
    form.$root = { querySelector: () => null };
    form.toggleDistrict('Keserwan');
    assert.deepEqual(Array.from(form.selectedCities), ['Achrafieh']);
    form.toggleAllCities();
    assert.deepEqual(Array.from(form.selectedCities), ['Achrafieh', 'Ras Beirut']);
    assert.equal(form.areAllCitiesSelected(), true);
    form.toggleAllCities();
    assert.equal(form.selectedCities.length, 0);
    form.toggleDistrict('Beirut');
    assert.equal(form.filteredCities().length, 0);
    assert.equal(form.selectedCitiesLabel(), 'Select districts first');
});
