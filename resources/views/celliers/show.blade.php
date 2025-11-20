@extends('layouts.app')

@section('title', 'Vue du cellier')

@section('content')
<div class="min-h-screen bg-background pt-24">
    <div class="max-w-5xl mx-auto space-y-6">

        {{-- En-tête du cellier --}}
        <div class="bg-card border border-border-base rounded-xl shadow-md p-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-text-title">
                    {{ $cellier->nom }}
                </h1>
                <p class="text-sm text-text-muted">
                    Vue principale du cellier – liste des bouteilles.
                </p>
            </div>

            <a
                href="{{ route('bouteilles.manuelles.create', $cellier) }}"
                class="bg-primary text-white font-bold py-2 px-4 rounded-lg hover:bg-primary-hover transition-colors duration-300 text-sm"
            >
                Ajouter une bouteille
            </a>
        </div>

        {{-- Liste des bouteilles --}}
        <div class="bg-card border border-border-base rounded-xl shadow-md p-6">
            @if ($cellier->bouteilles->isEmpty())
                <p class="text-text-muted">
                    Ce cellier est encore vide. Utilisez le bouton « Ajouter une bouteille » pour commencer.
                </p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($cellier->bouteilles as $bouteille)
                        <div
                            class="border border-border-base rounded-lg p-4 flex flex-col gap-3"
                            data-bottle-id="{{ $bouteille->id }}"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <h2 class="font-semibold text-text-title">
                                    {{ $bouteille->nom }}
                                </h2>

                                {{-- Badge quantité avec boutons +/- (logique JS/API à faire dans PV-9 / PV-17) --}}
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        class="bottle-qty-minus inline-flex items-center justify-center w-7 h-7 rounded-full border border-border-base text-primary hover:bg-primary/10"
                                    >
                                        –
                                    </button>

                                    <span
                                        class="bottle-qty-value inline-flex items-center justify-center rounded-full bg-primary text-white text-xs px-2 py-0.5"
                                    >
                                        x {{ $bouteille->quantite }}
                                    </span>

                                    <button
                                        type="button"
                                        class="bottle-qty-plus inline-flex items-center justify-center w-7 h-7 rounded-full border border-border-base text-primary hover:bg-primary/10"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>

                            <div class="text-sm text-text-muted space-y-1">
                                @if ($bouteille->pays)
                                    <p>
                                        <span class="font-medium text-text-body">Pays :</span>
                                        {{ $bouteille->pays }}
                                    </p>
                                @endif

                                @if ($bouteille->format)
                                    <p>
                                        <span class="font-medium text-text-body">Format :</span>
                                        {{ $bouteille->format }}
                                    </p>
                                @endif

                                @if (!is_null($bouteille->prix))
                                    <p>
                                        <span class="font-medium text-text-body">Prix :</span>
                                        {{ number_format($bouteille->prix, 2, ',', ' ') }} $
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
