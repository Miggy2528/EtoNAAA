@props([
    'route'
])

<x-button {{ $attributes->class(['btn btn-primary btn-sm']) }} route="{{ $route }}" onmouseover="this.style.opacity='0.8';" onmouseout="this.style.opacity='1';">
    <x-icon.eye class="text-white"/>
    <span class="text-white">{{ $slot }}</span>
</x-button>