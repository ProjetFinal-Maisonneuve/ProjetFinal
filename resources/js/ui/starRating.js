/**
 * Gestion du système de notation par étoiles (0-5)
 * Permet d'afficher et de modifier interactivement la note d'une bouteille
 */

// Sélectionner tous les conteneurs de notation par étoiles présents dans la page
const ratingContainers = document.querySelectorAll(".star-rating-container");

ratingContainers.forEach((container) => {
    // Définit si la note peut être modifiée ou non
    const isEditable = container.dataset.editable === "true";

    // Récupère toutes les étoiles cliquables
    const stars = container.querySelectorAll(".star-btn");

    // Input caché pour l'envoi de la note au serveur (formulaire)
    const hiddenInput = container.querySelector('input[type="hidden"]');

    // Note maximale autorisée (par défaut 5)
    const maxRating = parseInt(container.dataset.maxRating) || 5;

    // Note actuelle affichée (définie côté back)
    let currentRating = parseInt(container.dataset.rating) || 0;

    // Si la notation n'est pas modifiable, on ne fait rien d'autre
    if (!isEditable) {
        return;
    }

    // Si une note existe déjà, afficher le bouton pour la supprimer
    if (currentRating > 0) {
        const clearBtn = container.querySelector(".clear-rating-btn");
        if (clearBtn) {
            clearBtn.style.display = "block";
        }
    }

    /**
     * Colorie les étoiles de 1 à N selon la valeur donnée
     */
    function highlightStars(starValue) {
        stars.forEach((s, i) => {
            if (i < starValue) {
                s.classList.remove("text-gray-300");
                s.classList.add("text-primary"); // étoile active
            } else {
                s.classList.remove("text-primary");
                s.classList.add("text-gray-300"); // étoile inactive
            }
        });
    }

    // Gestion des interactions pour chaque étoile
    stars.forEach((star, index) => {
        const starValue = index + 1; // 1 à 5

        // Survol de souris → prévisualise la note (desktop uniquement)
        star.addEventListener("mouseenter", function () {
            highlightStars(starValue);
        });

        // Sortie de la souris → revient à la note actuelle (desktop uniquement)
        star.addEventListener("mouseleave", function () {
            updateStarDisplay(stars, currentRating);
        });

        // Interaction tactile pour mobile → prévisualisation immédiate
        star.addEventListener("touchstart", function (e) {
            e.preventDefault();
            highlightStars(starValue);
        });

        // Fin du toucher → attribue la note sélectionnée
        star.addEventListener("touchend", function (e) {
            e.preventDefault();
            e.stopPropagation();

            currentRating = starValue;
            container.dataset.rating = currentRating;

            updateStarDisplay(stars, currentRating);

            if (hiddenInput) {
                hiddenInput.value = currentRating;
            }

            const ratingText = container.querySelector("span[aria-live]");
            if (ratingText) {
                ratingText.textContent = currentRating + "/5";
            }
        });

        // Clic souris → attribue définitivement la note
        star.addEventListener("click", function (e) {
            e.stopPropagation();
            e.preventDefault();

            currentRating = starValue;
            container.dataset.rating = currentRating;

            updateStarDisplay(stars, currentRating);

            if (hiddenInput) {
                hiddenInput.value = currentRating;
            }

            const ratingText = container.querySelector("span[aria-live]");
            if (ratingText) {
                ratingText.textContent = currentRating + "/5";
            }
        });
    });

    // Bouton permettant d'effacer la note (retour à 0)
    const clearBtn = container.querySelector(".clear-rating-btn");
    if (clearBtn) {
        clearBtn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            currentRating = 0;
            container.dataset.rating = 0;

            updateStarDisplay(stars, 0);

            if (hiddenInput) {
                hiddenInput.value = 0;
            }

            const ratingText = container.querySelector("span[aria-live]");
            if (ratingText) {
                ratingText.textContent = "Non noté";
            }

            clearBtn.style.display = "none";
        });
    }
});

/**
 * Met à jour l'affichage visuel des étoiles en fonction de la note donnée
 * @param {NodeList} stars - Liste des boutons étoiles
 * @param {number} rating - Note actuelle (0-5)
 */
function updateStarDisplay(stars, rating) {
    stars.forEach((star, index) => {
        const starValue = index + 1;
        if (starValue <= rating) {
            // Étoiles actives
            star.classList.remove("text-gray-300");
            star.classList.add("text-primary");
        } else {
            // Étoiles inactives
            star.classList.remove("text-primary");
            star.classList.add("text-gray-300");
        }
    });

    // Gestion du bouton X (effacer note)
    const container = stars[0]?.closest(".star-rating-container");
    if (container) {
        const clearBtn = container.querySelector(".clear-rating-btn");
        if (clearBtn) {
            clearBtn.style.display = rating > 0 ? "block" : "none";
        }
    }
}
