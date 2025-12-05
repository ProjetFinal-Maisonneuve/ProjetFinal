@extends('layouts.app')

@section('title', 'Liste d’achat')

@section('content')

<div class="p-4 lg:p-6 space-y-6">

    {{-- En-tête de page --}}
    <x-page-header
        title="Ma liste d’achat"
        undertitle="Planifiez vos futurs achats de bouteilles en ajoutant des articles à votre liste d’achat." />

    {{-- Barre de recherche + filtres --}}
    <x-search-filter 
        :pays="$pays" 
        :types="$types" 
        :regions="$regions" 
        :millesimes="$millesimes" 
        url="/liste-achat/search" 
        suggestionUrl="/liste-achat/suggest"
        containerID="listeAchatContainer" 
    />

    {{-- État vide --}}
    @if ($items->isEmpty())
        <x-empty-state
            title="Votre liste d’achat est vide"
            subtitle="Ajoutez des bouteilles à votre liste pour planifier vos achats futurs."
            actionLabel="Explorer le catalogue"
            actionUrl="{{ route('bouteille.catalogue') }}" />
    @endif

    {{-- Liste d’achat (conteneur AJAX) --}}
    <section id="listeAchatContainer" class="mt-4">
        @include('liste_achat._liste_achat_list', [
            'items' => $items,
            'count' => $items->total(),
        ])
    </section>

    {{-- Résumé --}}
    @if (!$items->isEmpty())
        <div class="mt-10">
            <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-6 md:p-8">

                <h3 class="text-xl font-semibold text-gray-800 mb-4">
                    Résumé de votre liste
                </h3>

                <div class="grid gap-4 sm:grid-cols-3">

                    {{-- Nombre total --}}
                    <div class="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-4 px-3 border border-gray-100">
                        <span class="text-sm text-gray-500">Nombre de bouteilles</span>
                        <span id="totalItemContainer" class="text-2xl font-bold text-gray-900">
                            {{ $totalItem }}
                        </span>
                    </div>

                    {{-- Prix moyen --}}
                    <div class="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-4 px-3 border border-gray-100">
                        <span class="text-sm text-gray-500 whitespace-nowrap">Prix moyen / bouteille</span>
                        <span id="averagePriceContainer" class="text-2xl font-bold text-gray-900">
                            {{ number_format($avgPrice, 2, ',', ' ') }} $
                        </span>
                    </div>

                    {{-- Total --}}
                    <div class="flex flex-col items-center justify-center bg-gray-50 rounded-xl py-4 px-3 border border-gray-100">
                        <span class="text-sm text-gray-500">Total estimé</span>
                        <span id="totalPriceContainer" class="text-2xl font-bold text-gray-900">
                            {{ number_format($totalPrice, 2, ',', ' ') }} $
                        </span>
                    </div>

                </div>

            </div>
        </div>
    @endif

</div>

@endsection
