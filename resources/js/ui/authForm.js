// Récupération des éléments nécessaires à la gestion des formulaires
const btnLogin = document.getElementById("loginBtn");
const btnRegister = document.getElementById("registerBtn");

const formLogin = document.getElementById("loginForm");
const formRegister = document.getElementById("registerForm");

// Vérifier que les éléments existent avant d'exécuter la logique
if (btnLogin && btnRegister && formLogin && formRegister) {
    // Affiche le formulaire de connexion et met à jour le style du bouton actif
    function showLogin() {
        formLogin.classList.remove("hidden"); // Afficher login
        formRegister.classList.add("hidden"); // Masquer register

        // Styles pour bouton actif (login)
        btnLogin.classList.add("text-primary", "border-primary");
        btnLogin.classList.remove("text-neutral-700", "border-neutral-600");

        // Styles pour bouton inactif (register)
        btnRegister.classList.add("text-neutral-700", "border-neutral-600");
        btnRegister.classList.remove("text-primary", "border-primary");

        // Mise à jour de l'URL sans rechargement de page
        window.history.pushState({}, "", "/login");
    }

    // Affiche le formulaire d'inscription et met à jour le style du bouton actif
    function showRegister() {
        formRegister.classList.remove("hidden"); // Afficher register
        formLogin.classList.add("hidden"); // Masquer login

        // Styles pour bouton actif (register)
        btnRegister.classList.add("text-primary", "border-primary");
        btnRegister.classList.remove("text-neutral-700", "border-neutral-600");

        // Styles pour bouton inactif (login)
        btnLogin.classList.add("text-neutral-700", "border-neutral-600");
        btnLogin.classList.remove("text-primary", "border-primary");

        // Mise à jour de l'URL sans rechargement
        window.history.pushState({}, "", "/register");
    }

    // Réinitialise les champs, les erreurs visuelles et les messages d'erreurs d'un formulaire donné
    function clearForm(form) {
        form.querySelectorAll("input").forEach((input) => {
            // Conserver le token CSRF obligatoire pour Laravel
            if (input.name === "_token") return;
            input.value = "";
        });

        // Supprimer les messages d'erreurs existants
        form.querySelectorAll(".text-red-600").forEach((error) =>
            error.remove()
        );

        // Retirer les classes d'erreurs visuelles sur les champs
        form.querySelectorAll(".border-red-500, .bg-red-50").forEach(
            (input) => {
                input.classList.remove("border-red-500", "bg-red-50");
            }
        );
    }

    // Charger automatiquement le bon formulaire selon le mode défini dans Blade
    const mode = document.body.dataset.mode;
    mode === "register" ? showRegister() : showLogin();

    // Gestion du clic sur le bouton de connexion
    btnLogin.addEventListener("click", () => {
        formLogin.reset(); // Réinitialiser les champs
        clearForm(formLogin); // Nettoyer erreurs précédentes
        showLogin(); // Afficher le formulaire de connexion
    });

    // Gestion du clic sur le bouton d'inscription
    btnRegister.addEventListener("click", () => {
        formRegister.reset();
        clearForm(formRegister);
        showRegister();
    });
}
