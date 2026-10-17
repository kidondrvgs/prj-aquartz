const carrousel = document.getElementById('carrousel-prestations');
const precedent = document.getElementById('prestation-precedente');
const suivant = document.getElementById('prestation-suivante');
const mouvementReduit = window.matchMedia('(prefers-reduced-motion: reduce)');

// On garde les cartes fixes si elles tiennent toutes à l'écran
if (carrousel.children.length > 0 && carrousel.scrollWidth > carrousel.clientWidth + 2) {
    document.querySelector('.carrousel-commandes').hidden = false;
    const cartes = Array.from(carrousel.children);

    // On copie les cartes à la suite pour continuer le tour
    cartes.forEach(function (carte) {
        const copie = carte.cloneNode(true);
        copie.setAttribute('aria-hidden', 'true');
        copie.querySelectorAll('a').forEach(function (lien) {
            lien.tabIndex = -1;
        });
        carrousel.appendChild(copie);
    });

    function defiler(sens) {
        const pas = cartes[0].getBoundingClientRect().width + 24;
        const tour = pas * cartes.length;

        // Les copies permettent de revenir au début sans saut visible
        if (sens > 0 && carrousel.scrollLeft >= tour - 2) {
            carrousel.scrollTo({ left: carrousel.scrollLeft - tour, behavior: 'instant' });
        } else if (sens < 0 && carrousel.scrollLeft <= 0) {
            carrousel.scrollTo({ left: tour, behavior: 'instant' });
        }

        requestAnimationFrame(function () {
            carrousel.scrollBy({
                left: pas * sens,
                behavior: mouvementReduit.matches ? 'instant' : 'smooth',
            });
        });
    }

    suivant.addEventListener('click', function () {
        defiler(1);
    });

    precedent.addEventListener('click', function () {
        defiler(-1);
    });

    // On avance d'une carte toutes les trois secondes
    setInterval(function () {
        if (!document.hidden && !mouvementReduit.matches) {
            defiler(1);
        }
    }, 3000);
}
