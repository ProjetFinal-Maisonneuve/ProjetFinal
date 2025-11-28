@extends('layouts.app')

@section('title', 'Mon cellier – ' . $cellier->nom)

@section('add-wine-btn', '')

@section('content')
<section class="p-4 pt-2">
    <x-page-header
        :title="$cellier->nom"
        :undertitle="$undertitle"
    />

    {{-- Composant de recherche / filtres / tri (mode cellier) --}}
    <x-search-filter
    :pays="$pays"
    :types="$types"
    :millesimes="$millesimes"
    mode="cellier" 
    data-search-url="{{ route('celliers.search', $cellier) }}"
    data-target-container="cellarBottlesContainer"
    class="mt-3 mb-4"
/>


    {{-- Conteneur mis à jour par AJAX --}}
    <div id="cellarBottlesContainer">
        @include('celliers._bouteilles_list', ['cellier' => $cellier])
    </div>
</section>

{{-- Fenêtre flottante "Ajouter un vin" --}}
<div
    id="addWineBtnContainer"
    class="fixed z-50 bottom-0 left-0 w-full p-4 pt-10 bg-card border border-border-base shadow-lg rounded-t-lg transform translate-y-full transition-transform duration-300"
>
    <span class="flex items-center justify-between mb-4">
        <h1 class="text-3xl text-heading font-heading">Ajouter un vin</h1>
        <x-dynamic-component :component="'lucide-x'" id="closeAddWine" class="w-6 h-6" />
    </span>

    <div class="flex flex-col gap-4">
        <x-icon-text-btn
            :href="route('bouteille.catalogue')"
            icon="wine"
            title="Explorer le catalogue SAQ"
            subtitle="Recherchez des vins répertoriés à la SAQ."
        />
        <x-icon-text-btn
            :href="route('bouteilles.manuelles.create', $cellier->id)"
            icon="notebook-pen"
            title="Ajouter manuellement"
            subtitle="Pour les vins non répertoriés à la SAQ."
        />
    </div>
</div>
@endsection
