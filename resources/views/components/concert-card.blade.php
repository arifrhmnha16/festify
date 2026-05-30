<div class="overflow-hidden rounded-lg border border-neutral-200 bg-white shadow-sm">
    <div class="aspect-[16/10] bg-neutral-900">
        @if($concert->poster)
            <img src="{{ asset('storage/'.$concert->poster) }}" class="h-full w-full object-cover" alt="{{ $concert->name }}">
        @else
            <div class="flex h-full items-end bg-[linear-gradient(135deg,#111,#3f2a20)] p-5 text-white">
                <div><p class="text-xs uppercase tracking-widest text-orange-200">{{ $concert->artist }}</p><h3 class="text-xl font-black">{{ $concert->name }}</h3></div>
            </div>
        @endif
    </div>
    <div class="space-y-3 p-4">
        <div>
            <h3 class="text-lg font-black">{{ $concert->name }}</h3>
            <p class="text-sm text-neutral-600">{{ $concert->artist }}</p>
        </div>
        <div class="grid grid-cols-2 gap-2 text-sm text-neutral-600">
            <span>{{ $concert->date->format('d M Y') }}</span>
            <span>{{ substr($concert->time, 0, 5) }} WIB</span>
            <span class="col-span-2">{{ $concert->venue }}</span>
        </div>
        <div class="flex items-center justify-between">
            <div><p class="text-xs text-neutral-500">Mulai</p><p class="font-black">Rp{{ number_format($concert->price, 0, ',', '.') }}</p></div>
            <div class="text-right"><p class="text-xs text-neutral-500">Stok</p><p class="font-semibold">{{ $concert->stock }}</p></div>
        </div>
        <a href="{{ auth()->check() ? route('user.concerts.show', $concert) : route('concerts.show', $concert) }}" class="block rounded-full bg-neutral-950 px-4 py-2.5 text-center text-sm font-bold text-white">Detail / Beli Tiket</a>
    </div>
</div>
