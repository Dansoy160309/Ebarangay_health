@props(['title','value','color'=>'blue','icon'])

<div class="bg-{{ $color }}-100 p-6 rounded-2xl shadow flex items-center space-x-4">
    {!! $icon !!}
    <div>
        <h2 class="font-semibold text-gray-700">{{ $title }}</h2>
        <p class="text-3xl font-bold">{{ $value }}</p>
    </div>
</div>
