// Sélection des éléments du DOM nécessaires au fonctionnement du panneau
const panel = document.getElementById("addWineBtnContainer");
const openBtn = document.getElementById("addWineToCellar");
const closeBtn = document.getElementById("closeAddWine");
const optionButtons = document.querySelectorAll(".addWineOptionBtn");

// Ajout d'un gestionnaire d'événements à chaque bouton d'option pour fermer le panneau lors du clic
optionButtons.forEach((button) => {
    button.addEventListener("click", () => {
        panel.classList.add("translate-y-full");
        panel.classList.remove("translate-y-0");
    });
});

// Vérification de l'existence des éléments pour éviter des erreurs si l'un d'eux est absent
if (panel && openBtn && closeBtn) {
    // Ouverture du panneau : suppression de la classe cachant l'élément et ajout de la classe l'affichant
    openBtn.addEventListener("click", () => {
        panel.classList.remove("translate-y-full");
        panel.classList.add("translate-y-0");
    });

    // Fermeture du panneau : inverse des actions d'ouverture
    closeBtn.addEventListener("click", () => {
        panel.classList.add("translate-y-full");
        panel.classList.remove("translate-y-0");
    });
}
