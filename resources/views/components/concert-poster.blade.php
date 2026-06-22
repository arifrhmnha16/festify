@props([
    'concert',
    'class' => 'h-full w-full object-cover',
    'fallbackClass' => 'flex h-full items-end bg-[linear-gradient(135deg,#101322,#2c1f4f_48%,#da2b0d)] p-4 text-white',
])

@if($concert->poster_url)
    <img src="{{ $concert->poster_url }}" {{ $attributes->merge(['class' => $class, 'alt' => $concert->name]) }}>
@else
    <div class="{{ $fallbackClass }}">
        <div class="min-w-0">
            <p class="truncate text-xs font-bold uppercase tracking-widest text-orange-100">{{ $concert->artist }}</p>
            <h3 class="mt-1 line-clamp-2 text-lg font-black leading-tight">{{ $concert->name }}</h3>
        </div>
    </div>
@endif
