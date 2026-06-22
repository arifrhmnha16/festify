<a href="{{ auth()->check() ? route('user.concerts.show', $concert) : route('concerts.show', $concert) }}" class="group block">
    <div class="relative aspect-[3/1] overflow-hidden rounded-lg bg-neutral-900 shadow-sm">
        <x-concert-poster :concert="$concert" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" />
        @if($concert->is_promo)
            <span class="absolute left-3 top-3 rounded-md bg-red-600 px-3 py-1 text-xs font-black text-white shadow-sm">Promo</span>
        @endif
    </div>
    <div class="flex min-h-[220px] flex-col pt-4">
        <div class="min-h-[104px]">
            <p class="line-clamp-2 text-sm leading-6 text-neutral-700">{{ $concert->venue }}</p>
            <h3 class="mt-3 line-clamp-2 text-xl font-black leading-snug text-neutral-950 group-hover:text-blue-800">{{ $concert->name }}</h3>
            <p class="mt-2 line-clamp-1 text-sm leading-6 text-neutral-700">Oleh {{ $concert->artist }}</p>
        </div>
        <div class="mt-auto border-t border-dashed border-neutral-200 pt-4">
            <p class="text-sm text-neutral-400">Mulai dari</p>
            <p class="mt-1.5 text-xl font-black text-neutral-950">Rp{{ number_format($concert->price, 0, ',', '.') }}</p>
        </div>
    </div>
</a>
