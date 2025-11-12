@props(['items'])

<nav {{ $attributes->merge(['class' => 'text-sm text-gray-500 mb-6']) }} aria-label="Breadcrumb">
    @foreach($items as $index => $item)
        @if($loop->last)
            <span class="text-gray-900 font-semibold">{{ $item['label'] }}</span>
        @else
            <a href="{{ $item['url'] }}" class="hover:text-gray-700 transition">
                {{ $item['label'] }}
            </a>
            <span class="mx-2">/</span>
        @endif
    @endforeach
</nav>
