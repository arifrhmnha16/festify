@extends('layouts.app', ['title' => 'Festify'])
@section('content')
<section class="bg-white">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 md:grid-cols-[1.1fr_.9fr] md:py-24">
        <div>
            <p class="mb-4 text-sm font-bold uppercase tracking-widest text-orange-700">E-ticket konser premium</p>
            <h1 class="max-w-4xl text-5xl font-black leading-tight md:text-7xl">Temukan Konser Favoritmu dan Masuk Pakai E-Ticket</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-neutral-600">Festify memudahkan pembelian tiket konser online, penerbitan E-Ticket, penukaran gelang, dan validasi masuk venue.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('concerts.index') }}" class="rounded-full bg-neutral-950 px-6 py-3 font-bold text-white">Lihat Konser</a>
                <a href="#cara-kerja" class="rounded-full border border-neutral-300 px-6 py-3 font-bold">Cara Kerja</a>
            </div>
        </div>
        @php($heroConcert = $featuredConcert ?? $concerts->first())
        <div class="overflow-hidden rounded-lg border border-neutral-200 bg-neutral-950 text-white shadow-sm">
            <div class="relative aspect-[16/10] bg-neutral-900">
                @if($heroConcert?->poster)
                    <img src="{{ asset('storage/'.$heroConcert->poster) }}" alt="{{ $heroConcert->name }}" class="h-full w-full object-cover opacity-80">
                @else
                    <div class="h-full w-full bg-[linear-gradient(135deg,#111,#4a281d)]"></div>
                @endif
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-neutral-950 to-transparent p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-orange-200">Konser unggulan</p>
                    <h2 class="mt-2 text-4xl font-black">{{ $heroConcert?->name ?? 'Festify Live Night' }}</h2>
                    <p class="mt-2 text-sm text-neutral-200">{{ $heroConcert?->artist ?? 'Lineup pilihan minggu ini' }}</p>
                </div>
            </div>
            <div class="grid gap-4 p-6">
                <div class="grid grid-cols-3 gap-3 text-sm">
                    <div class="rounded-lg bg-white/10 p-3">
                        <p class="text-neutral-300">Mulai</p>
                        <p class="mt-1 font-black">Rp{{ number_format($heroConcert?->price ?? 250000,0,',','.') }}</p>
                    </div>
                    <div class="rounded-lg bg-white/10 p-3">
                        <p class="text-neutral-300">Stok</p>
                        <p class="mt-1 font-black">{{ $heroConcert?->stock ?? 100 }}</p>
                    </div>
                    <div class="rounded-lg bg-white/10 p-3">
                        <p class="text-neutral-300">Masuk</p>
                        <p class="mt-1 font-black">QR Gate</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-white/10 pt-4">
                    <div>
                        <p class="text-sm text-neutral-300">{{ $heroConcert?->venue ?? 'Venue partner Festify' }}</p>
                        <p class="font-bold">{{ $heroConcert?->date?->format('d M Y') ?? now()->addWeek()->format('d M Y') }} - {{ $heroConcert ? substr($heroConcert->time, 0, 5) : '19:00' }} WIB</p>
                    </div>
                    <a href="{{ $heroConcert ? route('concerts.show', $heroConcert) : route('concerts.index') }}" class="rounded-full bg-orange-700 px-5 py-3 text-sm font-black text-white">Amankan Tiket</a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="mx-auto max-w-7xl px-4 py-10">
    <form action="{{ route('concerts.index') }}" class="grid gap-3 rounded-lg border border-neutral-200 bg-white p-4 md:grid-cols-[1fr_1fr_180px_auto]">
        <input name="q" class="rounded-md border border-neutral-300 px-4 py-3" placeholder="Cari konser atau artis">
        <input name="venue" class="rounded-md border border-neutral-300 px-4 py-3" placeholder="Lokasi">
        <input type="date" name="date" class="rounded-md border border-neutral-300 px-4 py-3">
        <button class="rounded-md bg-orange-700 px-5 py-3 font-bold text-white">Cari</button>
    </form>
</section>
<section class="mx-auto max-w-7xl px-4 py-10">
    <div class="mb-8 flex items-end justify-between"><div><p class="text-sm font-bold uppercase text-orange-700">Featured Concerts</p><h2 class="text-4xl font-black">Konser Pilihan</h2></div><a class="font-bold" href="{{ route('concerts.index') }}">Lihat semua</a></div>
    <div class="grid gap-6 md:grid-cols-3">@foreach($concerts as $concert)<x-concert-card :concert="$concert" />@endforeach</div>
</section>
<section id="cara-kerja" class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4">
        <h2 class="text-4xl font-black">Cara Kerja</h2>
        <div class="mt-8 grid gap-4 md:grid-cols-7">@foreach(['Login / Register','Pilih Konser','Pesan Tiket','Bayar','Terima E-Ticket','Tukar Gelang','Scan Gate'] as $step)<div class="rounded-lg border border-neutral-200 p-4 text-sm font-bold">{{ $loop->iteration }}. {{ $step }}</div>@endforeach</div>
    </div>
</section>
@endsection
