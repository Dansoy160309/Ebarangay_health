@props(['type' => 'info', 'message'])

@php
    $styles = [
        'success' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-200',
            'text' => 'text-green-800',
            'icon' => 'bi-check-circle-fill',
            'icon_color' => 'text-green-500'
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'text' => 'text-red-800',
            'icon' => 'bi-x-circle-fill',
            'icon_color' => 'text-red-500'
        ],
        'warning' => [
            'bg' => 'bg-yellow-50',
            'border' => 'border-yellow-200',
            'text' => 'text-yellow-800',
            'icon' => 'bi-exclamation-triangle-fill',
            'icon_color' => 'text-yellow-500'
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'text' => 'text-blue-800',
            'icon' => 'bi-info-circle-fill',
            'icon_color' => 'text-blue-500'
        ]
    ];
    
    $style = $styles[$type] ?? $styles['info'];
@endphp

<div x-data="{ show: true }" 
     x-show="show" 
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-90"
     class="{{ $style['bg'] }} border-l-4 {{ $style['border'] }} p-4 rounded-r-lg shadow-sm mb-6 flex items-start gap-3" 
     role="alert">
    
    <div class="flex-shrink-0">
        <i class="bi {{ $style['icon'] }} text-xl {{ $style['icon_color'] }}"></i>
    </div>
    
    <div class="flex-1">
        <p class="font-bold text-lg {{ $style['text'] }} capitalize">{{ $type }}</p>
        <p class="text-base {{ $style['text'] }} mt-1">
            {{ $message }}
        </p>
    </div>

    <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition focus:outline-none">
        <i class="bi bi-x-lg text-lg"></i>
    </button>
</div>
