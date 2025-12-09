// Afficher ou masquer les boutons d'action des caves au clic du bouton de réglage

// Bouton permettant d'activer/désactiver le mode gestion
const cellarToggleActionBtn = document.getElementById("setting-btn");

// Toutes les boîtes représentant un cellier
const cellarBoxes = document.querySelectorAll(".cellar-box");

// Exécuter seulement si le bouton est présent sur la page
if (cellarToggleActionBtn) {
    // État du bouton (false = mode normal / true = mode gestion)
    let clicked = false;

    // Clic sur le bouton de réglage
    cellarToggleActionBtn.addEventListener("click", () => {
        clicked = !clicked; // Inverse l'état

        if (clicked) {
            // Mode activé → visuel actif sur le bouton
            cellarToggleActionBtn.classList.add(
                "bg-blue-600",
                "border-blue-600",
                "border"
            );

            // Affiche les actions sur chaque cellier
            cellarBoxes.forEach((box) => {
                box.classList.add("animate-shake"); // Animation visuelle
                const actionBtns = box.querySelector(".cellar-action-btns");

                // Vérifier que l'élément existe pour éviter une erreur JS
                if (actionBtns) {
                    actionBtns.classList.remove("hidden");
                }
            });
        } else {
            // Mode désactivé → retour à l'état normal du bouton
            cellarToggleActionBtn.classList.remove(
                "bg-blue-600",
                "border-blue-600",
                "border"
            );

            // Masque les actions sur chaque cellier
            cellarBoxes.forEach((box) => {
                box.classList.remove("animate-shake");
                const actionBtns = box.querySelector(".cellar-action-btns");

                if (actionBtns) {
                    actionBtns.classList.add("hidden");
                }
            });
        }
    });
}
