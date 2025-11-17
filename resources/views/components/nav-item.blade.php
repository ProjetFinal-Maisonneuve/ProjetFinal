@props(['active' => false])

<a href="{{ $url }}" class="group py-3 px-5 transition-colors duration-400 hover:bg-[var(--color-neutral-300)] flex-1 flex justify-center active:bg-[var(--color-neutral-500)]">
    <div class="flex flex-col items-center">

        <x-dynamic-component :component="'lucide-' . $icon" class="w-12 stroke-[var(--icon-color)] transition-colors duration-400 delay-75 group-hover:stroke-[var(--icon-hover)] group-active:stroke-[var(--color-neutral-600)]"/>
    
        <span class="text-sm stroke-[var(--icon-color)] transition-colors duration-400 group-hover:text-[var(--icon-hover)] group-active:text-[var(--color-neutral-600)] font-heading {{ $active ? 'text-[var(--color-primary)]' : '' }}">{{ $label }}</span>
    </div> 
</a>