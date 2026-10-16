const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const script = fs.readFileSync('public/js/accueil.js', 'utf8');

for (const nombre of [0, 1, 12]) {
    const clics = {};
    const commandes = { hidden: true };
    let avancer;
    let reduit = false;
    const carte = {
        getBoundingClientRect: () => ({ width: 260 }),
        cloneNode: () => ({ setAttribute() {}, querySelectorAll: () => [] }),
    };
    const carrousel = {
        children: Array.from({ length: nombre }, () => carte),
        scrollLeft: 0,
        appendChild(copie) { this.children.push(copie); },
        scrollBy(options) { this.scrollLeft += options.left; },
        scrollTo(options) { this.scrollLeft = options.left; },
    };
    const document = {
        hidden: false,
        getElementById: (id) => id === 'carrousel-prestations' ? carrousel : {
            addEventListener: (event, action) => { clics[id] = action; },
        },
        querySelector: () => commandes,
    };
    vm.runInNewContext(script, {
        document,
        window: { matchMedia: () => ({ get matches() { return reduit; } }) },
        requestAnimationFrame: (action) => action(),
        setInterval: (action, delai) => {
            assert.equal(delai, 3000);
            avancer = action;
        },
    });
    assert.equal(commandes.hidden, nombre === 0);
    if (nombre > 0) {
        assert.equal(carrousel.children.length, nombre * 2);
        clics['prestation-suivante']();
        assert.equal(carrousel.scrollLeft, 284);
        clics['prestation-precedente']();
        assert.equal(carrousel.scrollLeft, 0);
        for (let index = 0; index < nombre * 3 + 1; index++) {
            avancer();
        }
        assert.equal(carrousel.scrollLeft, 284);
        document.hidden = true;
        avancer();
        assert.equal(carrousel.scrollLeft, 284);
        document.hidden = false;
        reduit = true;
        avancer();
        assert.equal(carrousel.scrollLeft, 284);
    }
}
console.log('OK : intervalle de 3 secondes, trois tours, flèches, catalogue vide et mouvement réduit.');
