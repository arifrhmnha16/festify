@props(['zones', 'selectedZoneId' => null])

<div class="rounded-lg border border-neutral-200 bg-white p-5">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-orange-700">Map Venue</p>
            <h3 class="text-xl font-black">Pilih Area Tiket</h3>
        </div>
        <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-bold text-neutral-600">Stage View</span>
    </div>

    <div class="rounded-lg border border-neutral-200 bg-stone-50 p-4">
        <div class="mx-auto mb-5 h-10 max-w-md rounded-b-full bg-neutral-950 text-center text-xs font-bold uppercase tracking-widest text-white">
            <span class="inline-block pt-3">Stage</span>
        </div>

        <div class="grid gap-3">
            @foreach($zones as $zone)
                @php($isSelected = (int) $selectedZoneId === $zone->id)
                <div class="rounded-lg border-2 p-4 transition {{ $isSelected ? 'border-neutral-950 bg-white shadow-sm' : 'border-neutral-200 bg-white/70' }}">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="h-5 w-5 rounded-full" style="background: {{ $zone->color }}"></span>
                            <div>
                                <p class="font-black">{{ $zone->name }}</p>
                                <p class="text-sm text-neutral-600">Stok {{ $zone->stock }} tiket</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-neutral-500">Harga</p>
                            <p class="font-black">Rp{{ number_format($zone->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mx-auto mt-5 h-8 max-w-lg rounded-t-full border border-dashed border-neutral-300 text-center text-xs font-bold uppercase tracking-widest text-neutral-500">
            <span class="inline-block pt-2">Gate Masuk</span>
        </div>
    </div>
</div>
