document.addEventListener("DOMContentLoaded", () => {
    // Sélectionne tous les boutons permettant d'ajouter une bouteille du cellier à la liste d'achat
    const buttons = document.querySelectorAll(".add-to-wishlist-cellar");

    buttons.forEach((btn) => {
        // Empêche d'attacher plusieurs fois le même événement au même bouton
        if (btn.dataset.bound === "true") return;
        btn.dataset.bound = "true";

        // Gestion du clic sur le bouton
        btn.addEventListener("click", async () => {
            // Récupération des informations nécessaires à la requête
            const codeSaq = btn.dataset.codeSaq; // Code SAQ de la bouteille, si disponible
            const nom = btn.dataset.nom; // Nom de la bouteille fallback si aucun code SAQ
            const quantite = btn.dataset.quantite ?? 1; // Quantité par défaut à 1 si non définie

            // Récupérer l'icône originale affichée dans le bouton
            const icon = btn.querySelector('svg, [class*="lucide"]');
            if (!icon) return;

            // Sauvegarder l'HTML de l'icône avant de la remplacer
            const originalIconHTML = icon.outerHTML;

            // Récupérer le template du spinner de chargement
            const spinnerTemplate = document.getElementById(
                "spinner-inline-template"
            );
            if (!spinnerTemplate) return;

            // Cloner le spinner et l'insérer à la place de l'icône
            const spinner =
                spinnerTemplate.content.cloneNode(true).firstElementChild;
            spinner.className = spinner.className.replace("w-6 h-6", "w-5 h-5"); // Adapter la taille
            icon.replaceWith(spinner);

            // Désactiver le bouton durant la requête pour éviter les clics multiples
            btn.disabled = true;

            // Fonction utilitaire permettant de restaurer l'icône initiale
            const restoreIcon = () => {
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = originalIconHTML;
                const restoredIcon = tempDiv.firstElementChild;
                spinner.replaceWith(restoredIcon);
                btn.disabled = false;
            };

            try {
                let catalogueData = null;

                // Premier essai : trouver une bouteille via son code SAQ
                if (codeSaq && codeSaq !== "") {
                    const catalogueResponse = await fetch(
                        `/api/catalogue/by-code-saq/${encodeURIComponent(
                            codeSaq
                        )}`
                    );
                    if (catalogueResponse.ok) {
                        catalogueData = await catalogueResponse.json();
                    }
                }

                // Deuxième essai : si aucune correspondance via code SAQ, on tente une recherche par nom
                if (!catalogueData && nom) {
                    const catalogueResponse = await fetch(
                        `/api/catalogue/by-name/${encodeURIComponent(nom)}`
                    );
                    if (catalogueResponse.ok) {
                        catalogueData = await catalogueResponse.json();
                    }
                }

                // Si aucune donnée trouvée, il s'agit probablement d'une bouteille manuelle non liée au catalogue
                if (!catalogueData || !catalogueData.id) {
                    restoreIcon();
                    showToast(
                        "Cette bouteille n'est pas dans le catalogue SAQ et ne peut pas être ajoutée à la liste d'achat.",
                        "error"
                    );
                    return;
                }

                // Préparation des données pour l'ajout à la liste d'achat
                const formData = new FormData();
                formData.append("bouteille_catalogue_id", catalogueData.id);
                formData.append("quantite", quantite);

                // Envoi de la requête POST vers Laravel
                const response = await fetch("/liste-achat", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        Accept: "application/json",
                    },
                    body: formData,
                });

                const data = await response.json();

                // Toujours restaurer l'icône après la réponse du serveur
                restoreIcon();

                // Vérifie le statut HTTP de la requête
                if (!response.ok) {
                    showToast(
                        data.message || "Erreur lors de l'ajout.",
                        "error"
                    );
                    return;
                }

                // Notification de succès
                showToast(
                    data.message ||
                        "Cette bouteille a été ajoutée à votre liste d'achat.",
                    "success"
                );
            } catch (error) {
                // Gestion des erreurs réseau ou exceptions JavaScript
                console.error("Erreur:", error);
                restoreIcon();
                showToast("Erreur réseau", "error");
            }
        });
    });
});
