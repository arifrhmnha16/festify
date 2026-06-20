@extends('layouts.app', ['title' => 'Festify'])
@section('content')
<section class="bg-white">
    <div class="mx-auto grid max-w-6xl items-center gap-8 px-4 py-8 md:min-h-[520px] md:grid-cols-[.95fr_.85fr] md:gap-10 md:py-10">
        <div>
            <p class="mb-3 text-xs font-black uppercase tracking-widest text-orange-700">
                @auth
                    Selamat datang, {{ auth()->user()->name }}
                @else
                    Selamat datang di Festify
                @endauth
            </p>
            <h1 class="max-w-xl text-[36px] font-black leading-[1.08] md:text-[40px] lg:text-[44px]">Temukan Konser Favoritmu dan Masuk Pakai E-Ticket</h1>
            <p class="mt-4 max-w-lg text-base leading-7 text-neutral-600">Festify memudahkan pembelian tiket konser online, penerbitan E-Ticket, penukaran gelang, dan validasi masuk venue.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('concerts.index') }}" class="rounded-full bg-neutral-950 px-6 py-3 font-bold text-white">Lihat Konser</a>
                <a href="#cara-kerja" class="rounded-full border border-neutral-300 px-6 py-3 font-bold">Cara Kerja</a>
            </div>
        </div>
        @php($heroConcert = $featuredConcert ?? $concerts->first())
        <div class="w-full max-w-xl justify-self-end">
            <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-neutral-100 shadow-sm">
                <div class="aspect-[16/10]">
                    @if($heroConcert?->poster)
                        <img src="{{ asset('storage/'.$heroConcert->poster) }}" alt="{{ $heroConcert->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="h-full w-full bg-[linear-gradient(135deg,#111,#4a281d)]"></div>
                    @endif
                </div>
            </div>

            <div class="mt-4 rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm">
                <div class="mb-3 flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-orange-100 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-orange-800">Konser unggulan</span>
                    <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-bold text-neutral-600">{{ $heroConcert?->date?->format('d M Y') ?? now()->addWeek()->format('d M Y') }}</span>
                </div>

                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <h2 class="text-2xl font-black leading-tight">{{ $heroConcert?->name ?? 'Festify Live Night' }}</h2>
                        <p class="mt-1 text-sm text-neutral-600">{{ $heroConcert?->artist ?? 'Lineup pilihan minggu ini' }}</p>
                        <p class="mt-3 text-sm font-bold text-neutral-900">{{ $heroConcert?->venue ?? 'Venue partner Festify' }} - {{ $heroConcert ? substr($heroConcert->time, 0, 5) : '19:00' }} WIB</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-xs text-neutral-500">Mulai</p>
                        <p class="font-black">Rp{{ number_format($heroConcert?->price ?? 250000,0,',','.') }}</p>
                    </div>
                </div>

                <a href="{{ $heroConcert ? route('concerts.show', $heroConcert) : route('concerts.index') }}" class="mt-4 inline-flex w-full justify-center rounded-full bg-neutral-950 px-5 py-3 text-sm font-black text-white">Amankan Tiket</a>
            </div>
        </div>
    </div>
</section>
<section class="mx-auto max-w-6xl px-4 py-7">
    <form action="{{ route('concerts.index') }}" class="grid gap-3 rounded-lg border border-neutral-200 bg-white p-4 md:grid-cols-[1fr_1fr_180px_auto]">
        <input name="q" class="rounded-md border border-neutral-300 px-4 py-3" placeholder="Cari konser atau artis">
        <input name="venue" class="rounded-md border border-neutral-300 px-4 py-3" placeholder="Lokasi">
        <input type="date" name="date" class="rounded-md border border-neutral-300 px-4 py-3">
        <button class="rounded-md bg-orange-700 px-5 py-3 font-bold text-white">Cari</button>
    </form>
</section>
<section class="mx-auto max-w-6xl px-4 py-8">
    <div class="mb-6 flex items-end justify-between"><div><p class="text-xs font-black uppercase tracking-widest text-orange-700">Featured Concerts</p><h2 class="text-3xl font-black">Konser Pilihan</h2></div><a class="font-bold" href="{{ route('concerts.index') }}">Lihat semua</a></div>
    <div class="grid gap-6 md:grid-cols-3">@foreach($concerts as $concert)<x-concert-card :concert="$concert" />@endforeach</div>
</section>
<section id="cara-kerja" class="bg-white py-10">
    <div class="mx-auto max-w-6xl px-4">
        <h2 class="text-3xl font-black">Cara Kerja</h2>
        <div class="mt-6 grid gap-3 md:grid-cols-7">@foreach(['Login / Register','Pilih Konser','Pesan Tiket','Bayar','Terima E-Ticket','Tukar Gelang','Scan Gate'] as $step)<div class="rounded-lg border border-neutral-200 p-3 text-sm font-bold">{{ $loop->iteration }}. {{ $step }}</div>@endforeach</div>
    </div>
</section>
@endsection
