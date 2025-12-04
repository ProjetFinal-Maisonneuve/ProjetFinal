{{-- Modal pour le partage de bouteille --}}
<div id="shareModal" 
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden"
     role="dialog"
     aria-modal="true"
     aria-labelledby="shareModalTitle"
     aria-hidden="true">

    <div class="bg-card rounded-lg shadow-xl p-4 w-[90%] max-w-md border border-border-base" role="document">
        
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-text-heading" id="shareModalTitle">
                Partager cette bouteille
            </h2>
            <button 
                id="shareModalClose"
                class="text-text-muted hover:text-text-heading transition-colors"
                aria-label="Fermer le modal de partage">
                <x-dynamic-component :component="'lucide-x'" class="w-6 h-6" />
            </button>
        </div>

        <div id="shareModalContent">
            {{-- Contenu chargé dynamiquement --}}
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>
        </div>

    </div>
</div>

