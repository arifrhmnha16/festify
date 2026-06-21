<a href="{{ auth()->check() ? route('user.concerts.show', $concert) : route('concerts.show', $concert) }}" class="group block">
    <div class="relative aspect-[4/1] overflow-hidden rounded-lg bg-neutral-900 shadow-sm">
        @if($concert->poster)
            <img src="{{ asset('storage/'.$concert->poster) }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" alt="{{ $concert->name }}">
        @else
            <div class="flex h-full items-end bg-[linear-gradient(135deg,#111,#3f2a20)] p-4 text-white">
                <div>
                    <p class="text-xs uppercase tracking-widest text-orange-200">{{ $concert->artist }}</p>
                    <h3 class="text-lg font-black">{{ $concert->name }}</h3>
                </div>
            </div>
        @endif
    </div>
    <div class="pt-5">
        <p class="text-lg text-neutral-700">{{ $concert->venue }}</p>
        <h3 class="mt-4 line-clamp-1 text-2xl font-black leading-tight text-neutral-950 group-hover:text-blue-800">{{ $concert->name }}</h3>
        <p class="mt-3 line-clamp-1 text-lg text-neutral-700">Oleh {{ $concert->artist }}</p>
        <div class="mt-5 border-t border-dashed border-neutral-200 pt-5">
            <p class="text-base text-neutral-400">Mulai dari</p>
            <p class="mt-2 text-2xl font-black text-neutral-950">Rp{{ number_format($concert->price, 0, ',', '.') }}</p>
        </div>
    </div>
</a>
