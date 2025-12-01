@props([
    'route'
])

<form action="{{ $route }}" method="POST" class="d-inline-block">
    @csrf
    @method('delete')
    <x-button type="submit" {{ $attributes->class(['btn btn-danger btn-sm']) }} onmouseover="this.style.opacity='0.8';" onmouseout="this.style.opacity='1';">
        <x-icon.trash class="text-white"/>
    </x-button>
</form>