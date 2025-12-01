@props([
    'route',
    'text' => 'Back'
])

<x-button {{ $attributes->class(['btn btn-icon']) }} route="{{ $route }}" style="background-color: #6c757d; border-color: #6c757d;" onmouseover="this.style.backgroundColor='#5a6268'; this.style.borderColor='#545b62';" onmouseout="this.style.backgroundColor='#6c757d'; this.style.borderColor='#6c757d';">
    <x-icon.arrow class="text-white"/>
    <span class="text-white">{{ $text }}</span>
</x-button>