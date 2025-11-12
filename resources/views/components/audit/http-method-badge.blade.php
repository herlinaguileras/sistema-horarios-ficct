@props(['method'])

@php
    $config = [
        'POST' => ['color' => 'green', 'text' => 'POST'],
        'GET' => ['color' => 'blue', 'text' => 'GET'],
        'PUT' => ['color' => 'yellow', 'text' => 'PUT'],
        'PATCH' => ['color' => 'yellow', 'text' => 'PATCH'],
        'DELETE' => ['color' => 'red', 'text' => 'DELETE'],
    ];

    $badge = $config[$method] ?? ['color' => 'gray', 'text' => $method ?? 'N/A'];
@endphp

<span {{ $attributes->merge(['class' => "px-3 py-1 text-xs font-bold rounded bg-{$badge['color']}-100 text-{$badge['color']}-800"]) }}>
    {{ $badge['text'] }}
</span>
