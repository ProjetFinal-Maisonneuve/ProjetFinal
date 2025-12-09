// Cache/affiche le header en fonction du défilement
// Cache le header quand on défile vers le bas et l'affiche quand on remonte

let lastScroll = 0; // Mémorise la dernière position du scroll
const header = document.getElementById("mainHeader"); // Élément du header cible

// Vérifie que le header existe avant d'appliquer la logique
if (header) {
    window.addEventListener("scroll", () => {
        // Position actuelle du défilement depuis le haut
        const currentScroll = window.pageYOffset;

        if (currentScroll <= 0) {
            // Si on est en haut de la page, le header doit toujours être visible
            header.classList.remove("-translate-y-full");
            return;
        }

        if (currentScroll > lastScroll) {
            // Défilement vers le bas → cacher le header
            header.classList.add("-translate-y-full");
        } else {
            // Défilement vers le haut → réafficher le header
            header.classList.remove("-translate-y-full");
        }

        // Met à jour la position du scroll pour la prochaine comparaison
        lastScroll = currentScroll;
    });
}
