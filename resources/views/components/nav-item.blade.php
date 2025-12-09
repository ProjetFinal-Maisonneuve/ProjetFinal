@props(['active' => false])

{{-- Élément de navigation --}}
<a 
    href="{{ $url }}" 
    class="group py-3 px-3 sm:px-6 transition-colors duration-400 hover:bg-neutral-300 flex-1 flex justify-center active:bg-neutral-400 min-w-0 min-h-[68px]"
    aria-current="{{ $active ? 'page' : 'false' }}"
>
    <div class="flex flex-col items-center min-w-0 h-full justify-center">

        <x-dynamic-component :component="'lucide-' . $icon" class="w-7 sm:w-8 stroke-icon transition-colors duration-400 delay-75 group-hover:stroke-icon-hover {{ $active ? 'stroke-primary' : '' }} group-active:stroke-neutral-600 flex-shrink-0"/>
    
        <span class="text-xs sm:text-sm text-icon transition-colors duration-400 group-hover:text-icon-hover group-active:text-neutral-600 font-heading {{ $active ? 'text-primary' : '' }} truncate w-full text-center leading-tight">{{ $label }}</span>
    </div> 
</a>