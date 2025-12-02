@extends('layouts.app')

@section('title', 'Liste d’achat')

@section('content')
<div class="p-4">

    <h1 class="text-2xl font-bold text-center mb-6">Ma liste d’achat</h1>

    @if ($items->isEmpty())
        <p class="text-center text-muted mt-20">
            Votre liste d’achat est vide.<br>
            Ajoutez des bouteilles depuis le catalogue ou vos celliers.
        </p>
    @endif

    @foreach ($items as $item)
        <div class="p-4 bg-card shadow rounded mb-3 flex justify-between items-center">
            
            <div>
                <p class="font-semibold">{{ $item->bouteilleCatalogue->nom }}</p>
                <p class="text-sm text-muted">Quantité : {{ $item->quantite }}</p>
            </div>

            <div class="flex items-center gap-3">
                
                {{-- Supprimer --}}
                <form method="POST" action="{{ route('listeAchat.destroy', $item) }}">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 font-bold">Supprimer</button>
                </form>
            </div>

        </div>
    @endforeach

</div>
@endsection
