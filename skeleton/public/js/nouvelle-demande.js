const prestations = document.querySelectorAll('.prestations-choix input');
const affichageTotal = document.getElementById('total-prestations');

function afficherTotal() {
    let total = 0;
    let nombre = 0;
    let surDevis = false;

    prestations.forEach(function (prestation) {
        if (prestation.checked) {
            nombre++;
            if (prestation.dataset.prix === '') {
                surDevis = true;
            } else {
                total += Number(prestation.dataset.prix);
            }
        }
    });

    if (nombre === 0) {
        affichageTotal.textContent = 'Choisissez vos prestations';
    } else if (surDevis) {
        affichageTotal.textContent = 'Sur devis';
    } else {
        affichageTotal.textContent = (total / 100).toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' });
    }
}

prestations.forEach(function (prestation) {
    prestation.addEventListener('change', afficherTotal);
});

afficherTotal();
