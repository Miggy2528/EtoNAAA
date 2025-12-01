@props([
    'route'
])

<x-button {{ $attributes->class(['btn btn-warning btn-sm']) }} route="{{ $route }}" onmouseover="this.style.opacity='0.8';" onmouseout="this.style.opacity='1';">
    <x-icon.pencil class="text-white"/>
    <span class="text-white">{{ $slot }}</span>
</x-button>