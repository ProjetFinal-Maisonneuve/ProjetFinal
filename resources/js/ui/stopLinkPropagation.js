/**
 * Empêche la propagation des événements de clic sur les éléments interactifs
 * placés à l'intérieur des cartes cliquables (liens englobants).
 *
 * Sans ce script, cliquer sur un bouton à l'intérieur d'une carte déclencherait
 * aussi le clic sur la carte elle-même, ce qui redirigerait l'utilisateur
 * de manière non souhaitée.
 *
 * Exemple d'utilisation :
 * <a href="/bouteille/45" class="card">
 *     <button class="stop-link-propagation">Ajouter</button>
 * </a>
 * → Le bouton fonctionnera sans ouvrir la page de la carte.
 */

// Sélectionne tous les éléments qui doivent bloquer le clic du parent
const stopPropagationElements = document.querySelectorAll(
    ".stop-link-propagation"
);

stopPropagationElements.forEach((element) => {
    // Empêche le clic de remonter jusqu'au lien parent
    element.addEventListener("click", function (event) {
        event.stopPropagation(); // bloque la propagation vers le parent
        event.preventDefault(); // empêche l'action par défaut du lien parent si présent
    });
});
