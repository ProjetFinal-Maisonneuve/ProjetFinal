// Gestion du toggle actif/inactif pour les utilisateurs admin
document.addEventListener("DOMContentLoaded", function () {
    // Sélectionner tous les formulaires concernés par l'activation/désactivation d'un utilisateur
    document
        .querySelectorAll('form[action*="toggle-active"]')
        .forEach((form) => {
            // Ajout d'un écouteur sur la soumission du formulaire
            form.addEventListener("submit", async (e) => {
                e.preventDefault(); // Empêche le comportement par défaut du formulaire (rechargement immédiat)

                const url = form.action; // URL de l'action Laravel associée
                const csrfToken = document.querySelector(
                    'meta[name="csrf-token"]'
                )?.content; // Token CSRF pour sécuriser la requête
                const submitBtn = form.querySelector('button[type="submit"]'); // Bouton de soumission
                const originalLabel = submitBtn?.textContent.trim(); // Étiquette du bouton avant l'action

                // Si pas de token CSRF, la requête ne peut pas être sécurisée
                if (!csrfToken) {
                    if (window.showToast) {
                        showToast(
                            "Erreur de sécurité. Veuillez recharger la page.",
                            "error"
                        );
                    }
                    return;
                }

                // Désactiver temporairement le bouton pour éviter les doubles clics
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add("opacity-50", "cursor-not-allowed");
                }

                try {
                    // Envoi de la requête POST AJAX
                    const response = await fetch(url, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            Accept: "application/json",
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest", // Indique au backend qu'il s'agit d'une requête AJAX
                        },
                    });

                    const data = await response.json();

                    // Vérification de la réussite côté serveur
                    if (response.ok && data.success) {
                        // Notification visuelle de succès
                        if (window.showToast) {
                            showToast(
                                data.message ||
                                    "Statut de l'usager mis à jour.",
                                "success"
                            );
                        }

                        // Rechargement de la page pour refléter l'état mis à jour
                        // Petit délai pour permettre la lecture du toast
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        // Gestion d'une réponse invalide ou en erreur
                        if (window.showToast) {
                            showToast(
                                data.message ||
                                    "Erreur lors de la mise à jour.",
                                "error"
                            );
                        }

                        // Réactivation du bouton après l'échec
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove(
                                "opacity-50",
                                "cursor-not-allowed"
                            );
                        }
                    }
                } catch (error) {
                    // Gestion des exceptions réseau ou techniques
                    console.error("Erreur:", error);

                    if (window.showToast) {
                        showToast(
                            "Erreur réseau. Veuillez réessayer.",
                            "error"
                        );
                    }

                    // Réactivation du bouton après une erreur
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove(
                            "opacity-50",
                            "cursor-not-allowed"
                        );
                    }
                }
            });
        });
});
