@props([
    'route' => null,     // name de route ou URL complète
    'label' => 'Retour',
    'icon'  => true,     // Affiche l’icône flèche ou non
])

@php
    // Génération de l'URL
    if ($route === null) {
        // Pas de route fournie → on revient à la page précédente
        $href = url()->previous();
    }
    // Si tu passes une URL complète ou un chemin (/admin/users)
    elseif (str_contains($route, 'http') || str_starts_with($route, '/')) {
        $href = $route;
    }
    // Sinon, on considère que c'est un name de route Laravel
    else {
        $href = route($route);
    }
@endphp

<a href="{{ $href }}"
   {{ $attributes->class(
        'inline-flex items-center gap-2
         text-button-default font-medium
         hover:text-button-hover
         transition-colors duration-200'
    ) }}>
    @if ($icon)
        {{-- Icône décorative ignorée par les lecteurs d'écran --}}
        <x-dynamic-component
            :component="'lucide-arrow-left'"
            class="w-4 h-4"
            aria-hidden="true"
        />
    @endif

    <span>{{ $label }}</span>
</a>
