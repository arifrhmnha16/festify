@extends('layouts.app', ['title' => 'Checkout'])
@section('content')
<section class="mx-auto grid max-w-7xl gap-8 px-4 py-12 md:grid-cols-[1fr_420px]">
    <div>
        <h1 class="text-4xl font-black">Checkout</h1>
        <p class="mt-3 text-neutral-600">{{ $concert->name }} - {{ $concert->artist }}</p>
        <div class="mt-6"><x-venue-map :zones="$concert->ticketZones" /></div>
    </div>
    <form method="post" action="{{ route('user.orders.store', $concert) }}" class="rounded-lg border bg-white p-6">@csrf
        <p class="text-sm font-bold uppercase tracking-widest text-orange-700">Pilih area venue</p>
        <div class="mt-4 space-y-3">
            @foreach($concert->ticketZones as $zone)
                <label class="block cursor-pointer rounded-lg border border-neutral-200 p-4 has-[:checked]:border-neutral-950 has-[:checked]:bg-stone-50">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="ticket_zone_id" value="{{ $zone->id }}" class="h-4 w-4" @checked($loop->first) required>
                            <div>
                                <p class="font-black">{{ $zone->name }}</p>
                                <p class="text-sm text-neutral-600">Stok {{ $zone->stock }}</p>
                            </div>
                        </div>
                        <p class="font-black">Rp{{ number_format($zone->price,0,',','.') }}</p>
                    </div>
                </label>
            @endforeach
        </div>
        <label class="mt-5 block text-sm font-bold">Jumlah tiket</label><input type="number" name="ticket_quantity" value="1" min="1" max="5" class="mt-2 w-full rounded-md border px-4 py-3">
        <button class="mt-6 w-full rounded-full bg-neutral-950 px-5 py-3 font-bold text-white">Lanjut Pembayaran</button>
    </form>
</section>
@endsection
