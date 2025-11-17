@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
dsada
        
        {{-- Section d'affichage des bouteilles pour les tests --}}
        @if(isset($bouteilles) && $bouteilles->count() > 0)
            <section class="w-full mt-12 mb-8">
                <h2 class="text-xl font-bold text-text-body mb-6 text-center">Bouteilles du catalogue (test)</h2>
                <div class="grid grid-cols-1 gap-4 max-h-[600px] overflow-y-auto">
                    @foreach($bouteilles as $bouteille)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex gap-4">
                                {{-- Image de la bouteille --}}
                                @if($bouteille->url_image)
                                    <div class="flex-shrink-0 w-24 h-24 flex items-center justify-center bg-gray-50 rounded">
                                        @php
                                            // Normaliser le chemin pour s'assurer qu'il commence par / pour les chemins de stockage local
                                            $imageUrl = $bouteille->url_image;
                                            if (strpos($imageUrl, 'storage/') === 0 && strpos($imageUrl, '/storage/') !== 0) {
                                                $imageUrl = '/' . $imageUrl;
                                            }
                                        @endphp
                                        <img src="{{ asset($imageUrl) }}" 
                                             alt="{{ $bouteille->nom }}" 
                                             class="max-w-full max-h-full object-contain"
                                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/%3E%3Ctext fill=%22%23999%22 font-family=%22Arial%22 font-size=%2212%22 dy=%2210.5%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22%3EAucune image%3C/text%3E%3C/svg%3E'">
                                    </div>
                                @else
                                    <div class="flex-shrink-0 w-24 h-24 flex items-center justify-center bg-gray-50 rounded text-gray-400 text-xs text-center p-2">
                                        Aucune image
                                    </div>
                                @endif

                                {{-- Informations de la bouteille --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-base text-text-body mb-2 line-clamp-2">{{ $bouteille->nom }}</h3>
                                    <div class="space-y-1 text-sm text-gray-600">
                                        @if($bouteille->typeVin)
                                            <div><span class="font-medium">Type:</span> {{ $bouteille->typeVin->nom }}</div>
                                        @endif
                                        @if($bouteille->pays)
                                            <div><span class="font-medium">Pays:</span> {{ $bouteille->pays->nom }}</div>
                                        @endif
                                        @if($bouteille->region)
                                            <div><span class="font-medium">Région:</span> {{ $bouteille->region }}</div>
                                        @endif
                                        @if($bouteille->millesime)
                                            <div><span class="font-medium">Millésime:</span> {{ $bouteille->millesime }}</div>
                                        @endif
                                        @if($bouteille->volume)
                                            <div><span class="font-medium">Volume:</span> {{ $bouteille->volume }}</div>
                                        @endif
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-lg font-bold text-primary">{{ number_format($bouteille->prix, 2) }} $</span>
                                        @if($bouteille->code_saQ)
                                            <span class="text-xs text-gray-500">Code SAQ: {{ $bouteille->code_saQ }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif  
@endsection