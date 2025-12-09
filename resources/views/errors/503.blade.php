@extends('layouts.app')

@section('title', 'Maintenance en cours')

@section('content')
<div class="h-screen flex flex-col justify-center items-center text-center px-4">

    <img src="{{ asset('images/503.png') }}"
         alt="Maintenance" class="w-40 opacity-90">

    <h1 class="mt-6 text-4xl font-bold text-gray-800">
        Maintenance en cours
    </h1>

    <p class="mt-2 text-gray-600 max-w-md leading-relaxed">
        Le site est temporairement indisponible. Revenez un peu plus tard.
    </p>

    <x-primary-btn label="Revenir plus tard" type="href" :route="'bouteille.catalogue'" class="mt-6" />
</div>
@endsection
