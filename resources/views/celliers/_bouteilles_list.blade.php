{{-- Liste des bouteilles d'un cellier – utilisée par la page et par l'AJAX --}}

@php
    // On accepte soit $bouteilles passé par le contrôleur (search),
    // soit la relation $cellier->bouteilles (show classique)
    $bottles = isset($bouteilles) ? $bouteilles : ($cellier->bouteilles ?? collect());
@endphp

<div class="bg-card border border-border-base rounded-xl shadow-md p-6 mt-4">
    @if ($bottles->isEmpty())
        <p class="text-text-muted">
            Ce cellier est encore vide. Utilisez le bouton « Ajouter une bouteille » pour commencer.
        </p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($bottles as $bouteille)
                <div
                    class="border border-border-base rounded-lg p-4 flex flex-col gap-3"
                    data-bottle-id="{{ $bouteille->id }}"
                >
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="font-semibold text-text-title">
                            {{ $bouteille->nom }}
                        </h2>

                        {{-- Contrôles quantité + badge --}}
                        <div class="flex items-center gap-2">
                            {{-- Bouton - --}}
                            <button
                                type="button"
                                class="qty-btn bottle-qty-minus inline-flex items-center justify-center w-7 h-7 rounded-full border border-border-base text-primary hover:bg-primary/10"
                                data-url="{{ route('bouteilles.quantite.update', [$cellier, $bouteille]) }}"
                                data-direction="down"
                                data-bouteille="{{ $bouteille->id }}"
                                data-qty-btn
                                data-cellier-id="{{ $cellier->id }}"
                                data-bottle-id="{{ $bouteille->id }}"
                            >
                                –
                            </button>

                            {{-- Badge quantité --}}
                            <div
                                class="qty-display bottle-qty-value inline-flex items-center justify-center rounded-full bg-primary text-white text-xs px-2 py-0.5 min-w-16 text-center"
                                data-bouteille="{{ $bouteille->id }}"
                                data-qty-value="{{ $bouteille->id }}"
                            >
                                x {{ $bouteille->quantite ?? 1 }}
                            </div>

                            {{-- Bouton + --}}
                            <button
                                type="button"
                                class="qty-btn bottle-qty-plus inline-flex items-center justify-center w-7 h-7 rounded-full border border-border-base text-primary hover:bg-primary/10"
                                data-url="{{ route('bouteilles.quantite.update', [$cellier, $bouteille]) }}"
                                data-direction="up"
                                data-bouteille="{{ $bouteille->id }}"
                                data-qty-btn
                                data-cellier-id="{{ $cellier->id }}"
                                data-bottle-id="{{ $bouteille->id }}"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    {{-- Informations --}}
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

                    {{-- Actions --}}
                    <div class="flex gap-2 mt-auto">
                        <x-delete-btn 
                            :route="route('bouteilles.delete', [
                                'cellier'   => $cellier->id,
                                'bouteille' => $bouteille->id,
                            ])"
                        />

                        @if ($bouteille->code_saq === null)
                            <x-edit-btn
                                :route="route('bouteilles.edit', [$cellier->id, $bouteille->id])"
                                label="Modifier"
                            />
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
