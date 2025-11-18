@props(['name'=>'Cellier', 'amount' => '0'])
<a  class="cellar-box p-3 bg-card rounded-lg flex justify-between shadow-md border-border-base border hover:shadow-sm hover:bg-card-hover transition-all duration-300" href="#">
   <div flex flex-col gap-1>
    <h2 class="text-2xl font-semibold">{{ $name }}</h2>
   @if ($amount > 1)
       <p class="text-gray-600">{{ $amount }} Bouteilles</p>
    @else
       <p class="text-gray-600">{{ $amount }} Bouteille</p>
   @endif
   </div>
   <div class="cellar-action-btns flex gap-2 justify-center items-center hidden">
    <x-edit-btn />
    <x-delete-btn />
   </div>
</a>