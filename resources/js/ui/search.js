// Fichier unifié pour la recherche (catalogue, liste d'achat, et cellier)
// Détecte automatiquement le contexte et adapte le comportement

// Champs du DOM pour la recherche et les filtres
const searchInput = document.getElementById("searchInput");
const paysFilter = document.getElementById("paysFilter");
const typeFilter = document.getElementById("typeFilter");
const regionFilter = document.getElementById("regionFilter");
const millesimeFilter = document.getElementById("millesimeFilter");
const priceMinFilter = document.getElementById("priceMin");
const priceMaxFilter = document.getElementById("priceMax");
const sortFilter = document.getElementById("sortFilter");

// Boutons liés à la gestion des filtres
const resetFiltersBtn = document.getElementById("resetFiltersBtn");
const applyFiltersBtn = document.getElementById("applyFiltersBtn");
const closeBtn = document.getElementById("closeFiltersBtn");

// Bottom sheet mobile pour filtres et tri
const sortOptionsBtn = document.getElementById("sortOptionsBtn");
const filtersContainer = document.getElementById("filtersContainer");
const filtersOverlay = document.getElementById("filtersOverlay");
const dragHandle = document.getElementById("dragHandle");

// Détection automatique du contexte d'utilisation du script
// Le cellier possède data-search-url + conteneur spécifique
const cellierRoot = document.querySelector(
    "[data-search-url][data-target-container='cellarBottlesContainer']"
);

// Le catalogue et la liste d'achat partagent data-container
const catalogueRoot = document.querySelector("[data-container]");

let isCellier = false; // Flag permettant de savoir quel mode est actif
let containerId, container, baseUrl, suggestionUrl;

// Configuration du mode cellier
if (cellierRoot) {
    isCellier = true;
    containerId =
        cellierRoot.dataset.targetContainer || "cellarBottlesContainer";
    container = document.getElementById(containerId);
    baseUrl = cellierRoot.dataset.searchUrl;
    suggestionUrl = null; // Les suggestions n'existent pas pour le cellier
}
// Configuration du mode catalogue ou liste d'achat
else if (catalogueRoot) {
    containerId = catalogueRoot.dataset.container || "catalogueContainer";
    container = document.getElementById(containerId);
    baseUrl = catalogueRoot.dataset.url || "/catalogue/search";
    suggestionUrl = catalogueRoot.dataset.suggestionUrl || "/catalogue/suggest";
}

// Si aucun contexte valide n'est trouvé, le script ne s'exécute pas
if (!cellierRoot && !catalogueRoot) {
    // Page non concernée (admin ou autres), ne rien faire
} else {
    // Initialisation du comportement dynamique

    // Boîte d'affichage des suggestions à la saisie (uniquement catalogue/liste)
    const suggestionsBox = document.getElementById("suggestionsBox");
    let suggestionTimeout = null; // Timeout pour la saisie utilisateur

    // Valeur par défaut du tri selon le contexte
    let sortFilterDefault = "date_import-desc";
    if (containerId === "listeAchatContainer") {
        sortFilterDefault = "date_ajout-desc";
    }

    // Remise à zéro des filtres et relancement de la recherche
    function resetFilters() {
        if (searchInput) searchInput.value = "";
        if (paysFilter) paysFilter.value = "";
        if (typeFilter) typeFilter.value = "";
        if (regionFilter) regionFilter.value = "";
        if (millesimeFilter) millesimeFilter.value = "";
        if (priceMinFilter) priceMinFilter.value = "";
        if (priceMaxFilter) priceMaxFilter.value = "";

        if (sortFilter) {
            sortFilter.value = sortFilterDefault;
        }

        // Lancer la recherche en fonction du mode
        if (isCellier) {
            fetchCellier();
        } else {
            fetchCatalogue();
        }
    }

    // Ouvre ou ferme le panneau mobile (tri et filtres)
    function toggleSortOptions() {
        if (!filtersContainer || !filtersOverlay) return;
        const isHidden = filtersContainer.classList.contains("hidden");

        if (isHidden) {
            filtersOverlay.classList.remove("hidden");
            filtersContainer.classList.remove("hidden");
            setTimeout(() => {
                filtersOverlay.classList.add("opacity-50");
                filtersContainer.classList.remove("translate-y-[100%]");
            }, 10);
        } else {
            filtersContainer.classList.add("translate-y-[100%]");
            filtersOverlay.classList.remove("opacity-50");
            setTimeout(() => {
                filtersOverlay.classList.add("hidden");
                filtersContainer.classList.add("hidden");
            }, 500);
        }
    }

    // Attache d'événements au panneau mobile
    if (sortOptionsBtn)
        sortOptionsBtn.addEventListener("click", toggleSortOptions);
    if (filtersOverlay)
        filtersOverlay.addEventListener("click", toggleSortOptions);
    if (dragHandle) dragHandle.addEventListener("click", toggleSortOptions);
    if (closeBtn) closeBtn.addEventListener("click", toggleSortOptions);

    // Fonction utilitaire debounce pour éviter les requêtes trop fréquentes
    function debounce(fn, delay = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => fn(...args), delay);
        };
    }

    // Conversion du tri textuel pour l'API du cellier
    function buildSortForCellier() {
        if (!sortFilter || !sortFilter.value) {
            return { sort: "nom", direction: "asc" };
        }

        const [sortBy, sortDir] = sortFilter.value.split("-");
        let sort = "nom";

        switch (sortBy) {
            case "prix":
                sort = "prix";
                break;
            case "millesime":
                sort = "millesime";
                break;
            case "date_import":
            case "date_ajout":
                sort = "date_ajout";
                break;
            default:
                sort = "nom";
        }

        const direction = sortDir === "desc" ? "desc" : "asc";
        return { sort, direction };
    }

    // Charge les bouteilles du cellier en AJAX selon les filtres actifs
    function fetchCellier(url) {
        if (!container) return;

        const { sort, direction } = buildSortForCellier();

        const params = new URLSearchParams({
            nom: searchInput?.value || "",
            pays: paysFilter?.value || "",
            type: typeFilter?.value || "",
            region: regionFilter?.value || "",
            millesime: millesimeFilter?.value || "",
            sort,
            direction,
        });

        if (priceMinFilter?.value)
            params.append("prix_min", priceMinFilter.value);
        if (priceMaxFilter?.value)
            params.append("prix_max", priceMaxFilter.value);

        const baseUrlFinal = url || baseUrl;
        const finalUrl = baseUrlFinal.includes("?")
            ? `${baseUrlFinal}&${params.toString()}`
            : `${baseUrlFinal}?${params.toString()}`;

        fetch(finalUrl)
            .then((res) => res.json())
            .then((data) => {
                container.innerHTML = data.html;
                bindPaginationLinks();
                window.dispatchEvent(new CustomEvent("cellierReloaded"));
            })
            .catch((err) => console.error("Erreur fetch cellier :", err));
    }

    // Charge le catalogue ou la liste d'achat via AJAX
    function fetchCatalogue(customUrl = baseUrl) {
        if (!container) return;

        let sortBy = "";
        let sortDirection = "";

        if (sortFilter && sortFilter.value) {
            const [field, dir] = sortFilter.value.split("-");
            sortBy = field || "";
            sortDirection = dir || "";
        }

        const params = new URLSearchParams({
            search: searchInput?.value || "",
            pays: paysFilter?.value || "",
            type: typeFilter?.value || "",
            region: regionFilter?.value || "",
            millesime: millesimeFilter?.value || "",
            prix_min: priceMinFilter?.value || "",
            prix_max: priceMaxFilter?.value || "",
            sort_by: sortBy,
            sort_direction: sortDirection,
        });

        const finalUrl = customUrl.includes("?")
            ? `${customUrl}&${params.toString()}`
            : `${customUrl}?${params.toString()}`;

        fetch(finalUrl)
            .then((res) => res.json())
            .then((data) => {
                container.innerHTML = data.html;
                window.dispatchEvent(new CustomEvent("catalogueReloaded"));
                bindPaginationLinks();

                // Cache l'overlay de chargement si utilisé
                const overlay = document.getElementById("page-loading-overlay");
                if (overlay) {
                    overlay.classList.add("hidden");
                    overlay.setAttribute("aria-hidden", "true");
                    overlay.innerHTML = "";
                }
            })
            .catch((err) => {
                console.error("Erreur lors du fetch catalogue :", err);

                const overlay = document.getElementById("page-loading-overlay");
                if (overlay) {
                    overlay.classList.add("hidden");
                    overlay.setAttribute("aria-hidden", "true");
                    overlay.innerHTML = "";
                }
            });
    }

    // Suggestion intelligente lors de la saisie (uniquement catalogue/liste d'achat)
    function renderSuggestions(items) {
        if (!suggestionsBox) return;

        if (!items.length) {
            suggestionsBox.classList.add("hidden");
            return;
        }

        suggestionsBox.innerHTML = items
            .map(
                (item) => `
        <div class="px-3 py-2 cursor-pointer hover:bg-gray-100 suggestion-item"
             data-value="${item.nom}">
            ${item.nom}
        </div>`
            )
            .join("");

        suggestionsBox.classList.remove("hidden");

        document.querySelectorAll(".suggestion-item").forEach((el) => {
            el.addEventListener("click", () => {
                searchInput.value = el.dataset.value;
                suggestionsBox.classList.add("hidden");
                isCellier ? debouncedFetchCellier() : debouncedFetchCatalogue();
            });
        });
    }

    // Debounced fetchers
    const debouncedFetchCatalogue = debounce(() => fetchCatalogue(), 300);
    const debouncedFetchCellier = debounce(() => fetchCellier(), 300);

    // Recherche live selon le contexte
    if (searchInput) {
        if (isCellier) {
            searchInput.addEventListener("input", debouncedFetchCellier);
        } else {
            searchInput.addEventListener("input", debouncedFetchCatalogue);

            // Système de suggestions
            searchInput.addEventListener("input", (e) => {
                const query = e.target.value.trim();
                if (query.length < 1) {
                    suggestionsBox?.classList.add("hidden");
                    return;
                }

                clearTimeout(suggestionTimeout);
                suggestionTimeout = setTimeout(() => {
                    fetch(
                        `${suggestionUrl}?search=${encodeURIComponent(query)}`
                    )
                        .then((res) => res.json())
                        .then((items) => renderSuggestions(items));
                }, 150);
            });
        }
    }

    // Appliquer les filtres
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener("click", () => {
            isCellier ? fetchCellier() : fetchCatalogue();
            toggleSortOptions(); // Ferme la bottom sheet mobile
        });
    }

    // Réinitialiser les filtres
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener("click", resetFilters);
    }

    // Fermeture automatique des suggestions au clic extérieur
    if (searchInput && suggestionsBox && !isCellier) {
        document.addEventListener("click", (e) => {
            if (
                !searchInput.contains(e.target) &&
                !suggestionsBox.contains(e.target)
            ) {
                suggestionsBox.classList.add("hidden");
            }
        });
    }

    // Rebind des liens de pagination en AJAX
    function bindPaginationLinks() {
        if (!container) return;

        const links = container.querySelectorAll("a[href*='page=']");
        links.forEach((link) => {
            link.addEventListener("click", (e) => {
                e.preventDefault();
                isCellier ? fetchCellier(link.href) : fetchCatalogue(link.href);
            });
        });
    }

    // Lier la pagination dès la première exécution
    if (container) {
        bindPaginationLinks();
    }
} // Fin bloc d'initialisation conditionnelle
