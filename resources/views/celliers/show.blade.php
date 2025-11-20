@extends('layouts.app')

@section('title', $cellier->nom)

@section('content')
<div class="min-h-screen bg-gray-50 pt-20">
    <div class="max-w-4xl mx-auto p-4">

        {{-- En-tête du cellier --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 flex items-center justify-between flex-col sm:flex-row gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    {{ $cellier->nom }}
                </h1>
                <p class="text-gray-500">
                    Bouteilles dans ce cellier.
                </p>
            </div>

            <a href="{{ route('bouteilles.manuelles.create', $cellier) }}"
               class="bg-red-800 hover:bg-red-900 text-white font-bold py-3 px-8 rounded-full transition">
                Ajouter une bouteille
            </a>
        </div>

        {{-- Liste des bouteilles --}}
        <div class="space-y-6">
            @if($cellier->bouteilles->isEmpty())
                <div class="bg-white rounded-2xl shadow-lg p-16 text-center text-gray-500">
                    Aucune bouteille pour le moment.
                </div>
            @else
                @foreach($cellier->bouteilles as $bouteille)
                    <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">
                                {{ $bouteille->nom }}
                            </h2>

                            <div class="text-gray-600 space-y-1 mt-2">
                                @if($bouteille->pays)
                                    <p>Pays : {{ $bouteille->pays }}</p>
                                @endif

                                @if($bouteille->format)
                                    <p>Format : {{ $bouteille->format }}</p>
                                @endif

                                @if($bouteille->prix !== null)
                                    <p>Prix : {{ number_format($bouteille->prix, 2, ',', ' ') }} $</p>
                                @endif
                            </div>
                        </div>

                        {{-- Boutons quantité --}}
                        <div class="flex items-center gap-4">
                            {{-- Bouton - --}}
                            <button type="button"
                                    class="qty-btn w-10 h-10 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-2xl font-thin shadow-md transition"
                                    data-url="{{ route('bouteilles.quantite.update', [$cellier, $bouteille]) }}"
                                    data-direction="down"
                                    data-bouteille="{{ $bouteille->id }}">
                                −
                            </button>

                            {{-- Affichage quantité --}}
                            <div class="qty-display bg-red-800 text-white font-bold text-lg px-6 py-2 rounded-full min-w-20 text-center shadow-lg"
                                 data-bouteille="{{ $bouteille->id }}">
                                x {{ $bouteille->quantite ?? 1 }}
                            </div>

                            {{-- Bouton + --}}
                            <button type="button"
                                    class="qty-btn w-10 h-10 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-2xl font-thin shadow-md transition"
                                    data-url="{{ route('bouteilles.quantite.update', [$cellier, $bouteille]) }}"
                                    data-direction="up"
                                    data-bouteille="{{ $bouteille->id }}">
                                +
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
