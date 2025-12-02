@props([
    'action',               
    'name' => 'q',         
    'label' => 'Rechercher',
    'value' => '',
    'placeholder' => 'Rechercher…',
])

<form method="GET" action="{{ $action }}" {{ $attributes->merge(['class' => 'max-w-md']) }}>
    <div class="flex flex-col sm:flex-row gap-3 items-end">
        <x-input
            :label="$label"
            :name="$name"
            :value="$value"
            :placeholder="$placeholder"
            size="full"
        />

        <x-primary-btn
            type="submit"
            label="Rechercher"
            class="sm:w-auto w-full"
        />
    </div>
</form>
