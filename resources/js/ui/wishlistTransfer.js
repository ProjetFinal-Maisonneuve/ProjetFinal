document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".wishlist-transfer-btn").forEach(btn => {

        if (btn.dataset.jsBound === "true") return;
        btn.dataset.jsBound = "true";

        btn.addEventListener("click", async () => {

            // Charger celliers
            const response = await fetch("/api/celliers");
            const celliers = await response.json();

            if (!celliers.length) {
                showToast("Aucun cellier disponible", "error");
                return;
            }

            // Choix du cellier (CORRECTION ICI → ajouter les `backticks`)
            let choix = prompt(
                "Transférer vers quel cellier ?\n" +
                celliers
                    .map(c => `${c.id} — ${c.nom}`)
                    .join("\n")
            );

            if (!choix) return;

            choix = choix.trim();

            if (isNaN(choix)) {
                showToast("ID de cellier invalide", "error");
                return;
            }

            // FORM DATA 
            const formData = new FormData();
            formData.append("cellier_id", choix);

            fetch(btn.dataset.url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json",
                },
                body: formData
            })
            .then(async res => {
                let data = {};

                try { 
                    data = await res.json(); 
                } catch {}

                if (res.ok) {
                    showToast("Transfert réussi", "success");
                    location.reload();
                } else {
                    showToast(data.message || "Erreur lors du transfert", "error");
                }
            })
            .catch(() => showToast("Erreur réseau", "error"));
        });
    });

});
