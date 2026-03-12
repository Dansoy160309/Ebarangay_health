@props(['href', 'color' => 'blue'])
<a href="{{ $href }}" {{ $attributes->merge(['class' => "bg-$color-600 text-white px-4 py-2 rounded-2xl shadow hover:bg-$color-700 flex items-center space-x-2"]) }}>
    {{ $slot }}
</a>
