// Model pour la confirmation de suppression

// Sélection des éléments du DOM utilisés par la modale
const modal = document.getElementById("confirmModal");
const form = document.getElementById("confirmForm");
const cancel = document.getElementById("confirmCancel");
const confirmMessage = document.getElementById("confirmMessage");

// Vérifier l'existence des éléments avant d'activer la logique
if (modal && form && cancel) {
    let currentButton = null; // Bouton d'origine ayant déclenché l'ouverture de la modale

    // Ajouter un écouteur sur les boutons nécessitant une confirmation
    document.querySelectorAll(".use-confirm").forEach((btn) => {
        btn.addEventListener("click", () => {
            currentButton = btn; // Stocker le bouton pressé pour récupérer ses données

            // Définir l'action du formulaire selon le bouton cliqué
            form.action = btn.dataset.action;

            // Personnaliser le message de confirmation si fourni
            if (confirmMessage && btn.dataset.message) {
                confirmMessage.textContent = btn.dataset.message;
            } else if (confirmMessage) {
                confirmMessage.textContent =
                    "Êtes-vous sûr ? Veuillez confirmer la suppression.";
            }

            // Afficher la modale
            modal.classList.remove("hidden");
            modal.setAttribute("aria-hidden", "false");
        });
    });

    // Gestion de la soumission du formulaire
    form.addEventListener("submit", async (e) => {
        // Si le bouton spécifie une suppression via AJAX, intercepter la soumission
        if (currentButton && currentButton.dataset.ajax === "true") {
            e.preventDefault(); // Empêche l'envoi standard du formulaire

            const url = form.action; // URL de suppression
            const csrfToken = document.querySelector(
                'meta[name="csrf-token"]'
            )?.content; // Token CSRF Laravel

            // Vérifier la présence du token CSRF
            if (!csrfToken) {
                showToast(
                    "Erreur de sécurité. Veuillez recharger la page.",
                    "error"
                );
                modal.classList.add("hidden");
                modal.setAttribute("aria-hidden", "true");
                return;
            }

            // Désactiver le bouton de confirmation pour éviter des clics multiples
            const confirmBtn = form.querySelector('button[type="submit"]');
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.classList.add("opacity-50", "cursor-not-allowed");
            }

            try {
                // Envoi de la requête DELETE vers le serveur
                const response = await fetch(url, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        Accept: "application/json",
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                // Vérification et parsing sécurisé du JSON
                let data;
                try {
                    data = await response.json();
                } catch (jsonError) {
                    // Si la réponse n'est pas JSON, on force une erreur manuelle
                    throw new Error(
                        `Erreur HTTP ${response.status}: ${response.statusText}`
                    );
                }

                // Gestion d'une suppression réussie
                if (response.ok && data.success) {
                    showToast(
                        data.message || "Suppression réussie.",
                        "success"
                    );

                    // Fermer la modale
                    modal.classList.add("hidden");
                    modal.setAttribute("aria-hidden", "true");

                    // Attendre le toast avant de recharger
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Erreur signalée par le backend
                    showToast(
                        data.message || "Erreur lors de la suppression.",
                        "error"
                    );

                    // Réactiver le bouton
                    if (confirmBtn) {
                        confirmBtn.disabled = false;
                        confirmBtn.classList.remove(
                            "opacity-50",
                            "cursor-not-allowed"
                        );
                    }
                }
            } catch (error) {
                // Gestion des erreurs réseau ou exceptions non prévues
                console.error("Erreur:", error);
                console.error("URL tentée:", url);
                showToast("Erreur réseau. Veuillez réessayer.", "error");

                // Réactivation du bouton en cas d'erreur
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.classList.remove(
                        "opacity-50",
                        "cursor-not-allowed"
                    );
                }
            }
        }
        // Sinon, le formulaire se soumet normalement (comportement classique de Laravel)
    });

    // Bouton d'annulation : fermeture de la modale
    cancel.addEventListener("click", () => {
        modal.classList.add("hidden");
        modal.setAttribute("aria-hidden", "true");
        currentButton = null; // Réinitialiser le bouton actif
    });

    // Fermeture au clic sur l'arrière-plan de la modale
    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            // Vérifie que l'utilisateur clique en dehors du contenu
            modal.classList.add("hidden");
            modal.setAttribute("aria-hidden", "true");
            currentButton = null;
        }
    });
}
