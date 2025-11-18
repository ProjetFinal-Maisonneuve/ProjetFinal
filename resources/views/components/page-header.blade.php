@props(['title'=>'titre', 'actionBtn' => false])
<header class="mt-header flex flex-wrap justify-between">
    <h1 class="text-3xl font-bold font-heading text-heading">{{ $title }}</h1>
    @if ($actionBtn === true)
        <x-setting-btn id="setting-btn"/>
    @endif
</header>