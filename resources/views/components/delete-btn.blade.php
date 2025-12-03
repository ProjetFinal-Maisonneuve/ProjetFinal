@props([
    'route'   => null,
    'id'      => null,
    'label'   => null,          // texte optionnel (ex. "Supprimer")
    'variant' => 'icon',        // "icon" (ancien comportement) ou "menu"
])

@php
    // Détermination du href / action
    $action = $route;

    // Classes du bouton selon la variante
    $base = "inline-flex items-center justify-center transition-all duration-300";

    if ($variant === 'menu') {
        // Style petite pastille blanche comme ton exemple "Supprimer"
        $classes = $base .
            " w-full px-4 py-2 rounded-xl bg-white text-danger text-sm " .
            "shadow-md border border-border-base hover:bg-red-50 hover:text-red-700";
    } else {
        // Ancien style : petit bouton rond avec icône poubelle
        $classes = $base .
            " p-2 bg-card hover:bg-card-hover rounded-lg border-border-base border " .
            "shadow-md hover:shadow-sm";
    }
@endphp

<form
    action="{{ $action }}"
    method="POST"
    @if($id) id="{{ $id }}" @endif
    class="inline"
>
    @csrf
    @method('DELETE')

    <button
        type="button"
        class="use-confirm {{ $classes }}"
        data-action="{{ $action }}"
        aria-label="{{ $label ?? 'Supprimer' }}"
    >
        {{-- Icône poubelle --}}
        <x-dynamic-component
            :component="'lucide-trash-2'"
            class="w-5 h-5 stroke-text-heading"
            aria-hidden="true"
        />

        {{-- Texte optionnel --}}
        @if($label)
            <span class="ml-2">{{ $label }}</span>
        @endif
    </button>
</form>
