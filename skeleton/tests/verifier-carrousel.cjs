const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const script = fs.readFileSync('public/js/accueil.js', 'utf8');

for (const [nombre, largeur] of [[0, 1112], [1, 1112], [2, 1112], [3, 1112], [4, 1112], [12, 1112], [2, 260]]) {
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
        clientWidth: largeur,
        scrollWidth: Math.max(largeur, nombre * 284 - 24),
        appendChild(copie) {
            this.children.push(copie);
            this.scrollWidth = this.children.length * 284 - 24;
        },
        scrollBy(options) {
            this.scrollLeft = Math.max(0, Math.min(this.scrollLeft + options.left, this.scrollWidth - this.clientWidth));
        },
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
    const deborde = nombre * 284 - 24 > largeur + 2;
    assert.equal(commandes.hidden, !deborde);
    if (deborde) {
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
    } else {
        assert.equal(carrousel.children.length, nombre);
        assert.equal(avancer, undefined);
    }
}
console.log('OK : trois tours, flèches, catalogue court sans blocage, mobile et mouvement réduit.');
