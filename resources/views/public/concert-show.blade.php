@extends('layouts.app', ['title' => $concert->name])
@section('content')
<section class="mx-auto grid max-w-7xl gap-8 px-4 py-12 md:grid-cols-[.9fr_1.1fr]">
    <div class="aspect-[4/3] overflow-hidden rounded-lg bg-neutral-900">
        @if($concert->poster)
            <img src="{{ asset('storage/'.$concert->poster) }}" class="h-full w-full object-cover" alt="{{ $concert->name }}">
        @else
            <div class="flex h-full items-end bg-[linear-gradient(135deg,#111,#3f2a20)] p-6 text-white">
                <div>
                    <p class="text-sm uppercase tracking-widest text-orange-200">{{ $concert->artist }}</p>
                    <h2 class="mt-2 text-4xl font-black">{{ $concert->name }}</h2>
                </div>
            </div>
        @endif
    </div>
    <div>
        <p class="text-sm font-bold uppercase text-orange-700">{{ $concert->artist }}</p>
        <h1 class="mt-2 text-5xl font-black">{{ $concert->name }}</h1>
        <p class="mt-5 text-neutral-600">{{ $concert->description }}</p>
        <div class="mt-6 grid gap-3 text-sm md:grid-cols-2">
            <div class="rounded-lg border border-neutral-200 bg-white p-4">Tanggal<br><strong>{{ $concert->date->format('d M Y') }}</strong></div>
            <div class="rounded-lg border border-neutral-200 bg-white p-4">Jam<br><strong>{{ substr($concert->time, 0, 5) }} WIB</strong></div>
            <div class="rounded-lg border border-neutral-200 bg-white p-4">Venue<br><strong>{{ $concert->venue }}</strong></div>
            <div class="rounded-lg border border-neutral-200 bg-white p-4">Zone<br><strong>{{ $concert->seat_zone ?: 'Festival' }}</strong></div>
        </div>
        <div class="mt-8 flex items-center justify-between rounded-lg border border-neutral-200 bg-white p-5">
            <div><p class="text-sm text-neutral-500">Harga</p><p class="text-3xl font-black">Rp{{ number_format($concert->price,0,',','.') }}</p></div>
            <a href="{{ route('login') }}" class="rounded-full bg-neutral-950 px-6 py-3 font-bold text-white">Login untuk beli</a>
        </div>
    </div>
</section>
<section class="mx-auto max-w-7xl px-4 pb-12">
    <x-venue-map :zones="$concert->ticketZones" />
</section>
@endsection
