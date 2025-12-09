@extends('layouts.app')

@section('title', 'Erreur interne')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center text-center px-4">

    <img src="{{ asset('images/500.png') }}"
         alt="Erreur interne" class="w-40 opacity-90">

    <h1 class="mt-6 text-4xl font-bold text-gray-800">
        Erreur interne du serveur
    </h1>

    <p class="mt-2 text-gray-600 max-w-md leading-relaxed">
        Une erreur inattendue s'est produite. Nos équipes ont été notifiées.
    </p>

    <x-primary-btn label="Retour à l'accueil" type="href" :route="'bouteille.catalogue'" class="mt-6" />
</div>
@endsection
