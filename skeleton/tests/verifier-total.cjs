const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');

const prestations = [
    { checked: false, dataset: { prix: '10050' }, addEventListener() {} },
    { checked: false, dataset: { prix: '25000' }, addEventListener() {} },
    { checked: false, dataset: { prix: '' }, addEventListener() {} },
];
const total = {};
const contexte = vm.createContext({
    document: {
        querySelectorAll: () => prestations,
        getElementById: () => total,
    },
});
vm.runInContext(fs.readFileSync('public/js/nouvelle-demande.js', 'utf8'), contexte);
assert.equal(total.textContent, 'Choisissez vos prestations');
prestations[0].checked = true;
prestations[1].checked = true;
vm.runInContext('afficherTotal()', contexte);
assert.equal(total.textContent.replace(/\s/g, ''), '350,50€');
prestations[2].checked = true;
vm.runInContext('afficherTotal()', contexte);
assert.equal(total.textContent, 'Sur devis');
prestations.forEach((prestation) => { prestation.checked = false; });
vm.runInContext('afficherTotal()', contexte);
assert.equal(total.textContent, 'Choisissez vos prestations');
console.log('OK : total, centimes, sélection vide et prestation sur devis.');
