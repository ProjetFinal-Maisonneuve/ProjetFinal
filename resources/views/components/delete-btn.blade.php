@props(["id" => null])
<button class="p-2 bg-card hover:bg-card-hover rounded-lg border-border-base border shadow-md hover:shadow-sm transition-all duration-300">
    <x-dynamic-component :component="'lucide-trash-2'" class="w-6 stroke-text-heading "/>
</button>