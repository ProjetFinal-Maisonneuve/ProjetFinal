@props([
    'pays' => [],
    'types' => [],
])

{{-- Composant de recherche et filtres --}}
<div {{ $attributes->merge(['class' => '']) }}>
    <x-input 
        id="searchInput" 
        type="text" 
        placeholder="Rechercher..." 
    />
    <div class="flex gap-3 mb-4">
        <select id="paysFilter" class="border px-3 py-2 rounded">
            <option value="">Pays</option>
            @foreach($pays as $p)
                <option value="{{ $p->id }}">{{ $p->nom }}</option>
            @endforeach
        </select>

        <select id="typeFilter" class="border px-3 py-2 rounded">
            <option value="">Type</option>
            @foreach($types as $t)
                <option value="{{ $t->id }}">{{ $t->nom }}</option>
            @endforeach
        </select>
    </div>
</div>