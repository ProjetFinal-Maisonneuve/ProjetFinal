@props([
    'route'   => null,
    'label'   => null,
    'variant' => 'icon', // "icon" ou "menu"
])

@php
    // --- 1. Base commune ---
    // "use-confirm" est essentiel pour ton script JS
    $base = "use-confirm inline-flex items-center justify-center transition-all duration-300 cursor-pointer";

    // --- 2. Logique des variantes ---
    if ($variant === 'menu') {
        // === VARIANT MENU (Ton nouveau design) ===
        // Fond rouge (bg-danger), texte blanc, pleine largeur
        $classes = $base .
            " w-full px-4 py-3 rounded-lg bg-danger text-white text-sm " .
            "shadow-md border border-border-base hover:bg-red-500";
            
        // L'icône doit être blanche sur fond rouge -> 'stroke-current' prend la couleur du texte
        $iconClass = "w-5 h-5 stroke-current mr-2"; 
        
    } else {
        // === VARIANT ICON (Ancien design conservé) ===
        // Petit bouton discret
        $classes = $base .
            " p-2 bg-card hover:bg-card-hover rounded-lg border-border-base border " .
            "shadow-md hover:shadow-sm";
            
        // L'icône garde sa couleur par défaut
        $iconClass = "w-5 h-5 stroke-text-heading";
    }
@endphp

<button
    type="button"
    class="{{ $classes }}"
    data-action="{{ $route }}"
    aria-label="{{ $label ?? 'Supprimer' }}"
>
    {{-- Icône --}}
    <x-dynamic-component
        :component="'lucide-trash-2'"
        class="{{ $iconClass }}"
        aria-hidden="true"
    />

    {{-- Texte (Uniquement affiché si un label existe ET qu'on est en mode menu) --}}
    @if($label && $variant === 'menu')
        <span>{{ $label }}</span>
    @endif
</button>