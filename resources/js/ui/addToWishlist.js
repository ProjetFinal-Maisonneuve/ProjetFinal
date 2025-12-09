function initWishlistButtons() {
    // Sélectionne tous les boutons permettant d'ajouter une bouteille à la liste d'achat
    const buttons = document.querySelectorAll(".add-to-wishlist");

    buttons.forEach((btn) => {
        // Empêche de lier plusieurs fois le même événement au bouton
        if (btn.dataset.jsBound === "true") return;
        btn.dataset.jsBound = "true";

        // Ajoute l'événement au clic
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;

            // Récupérer l'icône originale du bouton
            const icon = btn.querySelector('svg, [class*="lucide"]');
            if (!icon) return;

            // Sauvegarder le code HTML de l'icône initiale
            const originalIconHTML = icon.outerHTML;

            // Récupérer le template du spinner de chargement
            const spinnerTemplate = document.getElementById(
                "spinner-inline-template"
            );
            if (!spinnerTemplate) return;

            // Cloner le spinner et l'ajouter à la place de l'icône
            const spinner =
                spinnerTemplate.content.cloneNode(true).firstElementChild;
            // Ajuster la taille du spinner pour correspondre à l'icône
            spinner.className = spinner.className.replace("w-6 h-6", "w-5 h-5");
            icon.replaceWith(spinner);

            // Désactiver le bouton pendant l'envoi de la requête
            btn.disabled = true;

            const formData = new FormData();
            formData.append("bouteille_catalogue_id", id);

            // Envoie de la requête POST pour ajouter la bouteille à la liste
            fetch("/liste-achat", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    Accept: "application/json",
                },
                body: formData,
            })
                .then(async (res) => {
                    const data = await res.json();

                    // Restaurer l'icône originale après la réponse du serveur
                    const tempDiv = document.createElement("div");
                    tempDiv.innerHTML = originalIconHTML;
                    const restoredIcon = tempDiv.firstElementChild;
                    spinner.replaceWith(restoredIcon);
                    btn.disabled = false;

                    // Gestion des erreurs serveur
                    if (!res.ok) {
                        showToast("Erreur lors de l'ajout.", "error");
                        return;
                    }

                    // Notification de succès si tout est correct
                    showToast(
                        data.message ||
                            "Bouteille ajoutée à votre liste d'achat.",
                        "success"
                    );
                })
                .catch(() => {
                    // En cas d'erreur réseau, restauration de l'icône et affichage du message
                    const tempDiv = document.createElement("div");
                    tempDiv.innerHTML = originalIconHTML;
                    const restoredIcon = tempDiv.firstElementChild;
                    spinner.replaceWith(restoredIcon);
                    btn.disabled = false;
                    showToast("Erreur réseau", "error");
                });
        });
    });
}

// Initialise les boutons au chargement de la page
document.addEventListener("DOMContentLoaded", initWishlistButtons);

// Réinitialise les boutons après un rechargement dynamique du catalogue
window.addEventListener("catalogueReloaded", initWishlistButtons);
