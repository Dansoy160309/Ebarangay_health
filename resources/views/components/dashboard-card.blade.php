<div class="bg-white p-4 rounded shadow">
    <h2 class="font-semibold text-gray-700">{{ $title }}</h2>

    @if($count !== '')
        <p class="text-3xl font-bold">{{ $count }}</p>
    @endif

    @if($link !== '' && $linkText !== '')
        <p class="mt-2">
            <a href="{{ $link }}" class="text-blue-600 underline">{{ $linkText }}</a>
        </p>
    @endif
</div>
