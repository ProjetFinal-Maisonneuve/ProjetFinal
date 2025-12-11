@extends('layouts.app')

@section('title', 'Signaler une erreur')

@section('content')

<section class="container max-w-2xl mx-auto px-4 py-8">
{{-- En-tête --}}
<x-page-header 
    title="Signaler une erreur"
    :undertitle="'Pour la bouteille : ' . $bouteille->nom"
/>
<div class="mx-auto bg-card p-6 rounded-lg shadow mt-6">

    <form action="{{ route('signalement.store', $bouteille) }}" method="POST" class="space-y-6">
        @csrf

        {{-- Champ : Nom du signalement --}}
        <x-input
            label="Nom du signalement"
            name="nom"
            placeholder="Ex: Mauvaise information"
            required="true"
            class="bg-white"
            size="full"
        />

        {{-- Champ : Description --}}
        <div class="flex flex-col gap-1 w-full">
            <label for="description" class="text-sm font-medium text-text-muted">
                Description
            </label>

            <textarea
                name="description"
                id="description"
                rows="5"
                required
                aria-required="true"
                aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}"
                @if($errors->has('description')) aria-describedby="error-description" @endif
                class="
                    w-full rounded-lg px-3 py-2 bg-card text-text-body border 
                    {{ $errors->has('description') ? 'border-red-500 bg-red-50' : 'border-muted' }}
                    focus:ring-color-focus focus:ring-1 outline-none
                "
            >{{ old('description') }}</textarea>

            @error('description')
                <p id="error-description" role="alert" class="text-red-600 text-sm">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Bouton soumettre --}}
        <button type="submit"
            class="px-6 py-2 rounded-lg bg-button-default border-2 border-primary text-primary font-semibold hover:bg-primary hover:text-white transition">
            Envoyer le signalement
        </button>
    </form>

</div>
</section>

@endsection
