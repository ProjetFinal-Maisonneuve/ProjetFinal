document.addEventListener("DOMContentLoaded", () => {

    /* ============================================================
       GESTION QUANTITÉ (+ / -)
       ============================================================ */
    document.querySelectorAll(".wishlist-qty-btn").forEach(btn => {

        // Empêche double binding
        if (btn.dataset.jsBound === "true") return;
        btn.dataset.jsBound = "true";

        btn.addEventListener("click", () => {

            const container = btn.parentElement;
            const display = container.querySelector(".wishlist-qty-display");

            if (!display) {
                console.error("wishlist-qty-display introuvable !");
                return;
            }

            let value = parseInt(display.textContent);

            // Up / Down
            if (btn.dataset.direction === "up") {
                value++;
            }
            else if (btn.dataset.direction === "down" && value > 1) {
                value--;
            }

            // Mise à jour visuelle immédiate
            display.textContent = value;

            // Construction FormData pour Laravel
            const formData = new FormData();
            formData.append("_method", "PUT");  
            formData.append("quantite", value);

            // Requête backend
            fetch(btn.dataset.url, {
                method: "POST", 
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json",
                },
                body: formData
            })
            .then(res => {
                if (res.ok) {
                    showToast("Quantité mise à jour", "success");
                } else {
                    showToast("Erreur lors de la mise à jour", "error");
                }
            })
            .catch(() => showToast("Erreur réseau", "error"));
        });
    });


    /* ============================================================
       CHECKBOX : MARQUER COMME ACHETÉ
       ============================================================ */
    document.querySelectorAll(".wishlist-check-achete").forEach(checkbox => {

        if (checkbox.dataset.jsBound === "true") return;
        checkbox.dataset.jsBound = "true";

        checkbox.addEventListener("change", () => {

            const formData = new FormData();
            formData.append("_method", "PUT");  
            formData.append("achete", checkbox.checked ? 1 : 0);

            fetch(checkbox.dataset.url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json"
                },
                body: formData
            })
            .then(res => {

                // Style barré
                const label = checkbox.parentElement.querySelector("span");

                if (checkbox.checked) {
                    label.classList.add("line-through", "text-gray-400");
                } else {
                    label.classList.remove("line-through", "text-gray-400");
                }

                showToast("Statut mis à jour", "success");

            })
            .catch(() => showToast("Erreur réseau", "error"));
        });
    });

});
