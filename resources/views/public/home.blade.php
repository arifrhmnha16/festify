@extends('layouts.app', ['title' => 'Festify'])
@section('content')
@php
    $bannerConcerts = $concerts->take(4);
    if ($featuredConcert && ! $bannerConcerts->contains('id', $featuredConcert->id)) {
        $bannerConcerts = collect([$featuredConcert])->merge($bannerConcerts)->take(4);
    }
@endphp

<section class="bg-white">
    <div class="px-0 py-1 md:px-1">
        <div class="relative overflow-hidden border-y border-neutral-200 bg-neutral-950 shadow-sm" data-promo-slider>
            <div class="flex transition-transform duration-500 ease-out" data-promo-track>
                @forelse($bannerConcerts as $concert)
                    <article class="relative min-w-full">
                        <div class="relative min-h-[470px] overflow-hidden md:min-h-[520px]">
                            @if($concert->poster)
                                <img src="{{ asset('storage/'.$concert->poster) }}" alt="{{ $concert->name }}" class="absolute inset-0 h-full w-full object-cover">
                            @else
                                <img src="{{ asset('logofest.png') }}" alt="Festify" class="absolute inset-0 h-full w-full object-cover opacity-80">
                            @endif
                            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(17,24,39,.88)_0%,rgba(67,20,7,.62)_38%,rgba(17,24,39,.18)_100%)]"></div>
                            <div class="absolute inset-x-0 top-0 z-10 mx-auto flex max-w-7xl items-start justify-between px-7 py-8 text-white md:px-10">
                                <div>
                                    <p class="text-lg font-black uppercase tracking-wider">Festify</p>
                                    <p class="text-xs font-black uppercase tracking-[0.32em] text-orange-200">Live</p>
                                </div>
                                <p class="hidden rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-widest backdrop-blur md:block">
                                    @auth
                                        Halo, {{ auth()->user()->name }}
                                    @else
                                        Promo Konser
                                    @endauth
                                </p>
                            </div>
                            <div class="relative z-10 mx-auto flex min-h-[470px] max-w-7xl items-center px-7 pb-24 pt-24 text-white md:min-h-[520px] md:px-10">
                                <div class="max-w-3xl">
                                    <h1 class="text-[44px] font-black leading-[.98] tracking-wide md:text-[72px] lg:text-[88px]">{{ $concert->name }}</h1>
                                    <p class="mt-5 text-xl font-bold text-neutral-100 md:text-2xl">with {{ $concert->artist }}</p>
                                    <div class="mt-9 grid max-w-4xl gap-6 text-lg font-bold text-neutral-100 md:grid-cols-3 md:text-2xl">
                                        <p>{{ $concert->date->format('F d') }}</p>
                                        <p>{{ $concert->venue }}</p>
                                        <p>{{ substr($concert->time, 0, 5) }} WIB</p>
                                    </div>
                                    <p class="mt-8 text-sm font-bold uppercase tracking-widest text-neutral-200 md:text-base">Tickets available at Festify</p>
                                </div>
                            </div>
                            <a href="{{ route('concerts.show', $concert) }}" class="absolute bottom-8 left-1/2 z-20 flex w-[min(720px,calc(100%-2rem))] -translate-x-1/2 items-center justify-between gap-4 rounded-xl bg-white/85 px-5 py-4 text-base font-black text-neutral-950 shadow-lg backdrop-blur md:text-xl">
                                <span class="truncate">Nonton {{ $concert->name }}! <span class="text-blue-700">Dapetin tiketnya di sini!</span></span>
                                <svg class="h-7 w-7 shrink-0 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <article class="relative min-w-full">
                        <div class="relative min-h-[470px] overflow-hidden md:min-h-[520px]">
                            <img src="{{ asset('logofest.png') }}" alt="Festify" class="absolute inset-0 h-full w-full object-cover opacity-80">
                            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(17,24,39,.88)_0%,rgba(67,20,7,.62)_48%,rgba(17,24,39,.18)_100%)]"></div>
                            <div class="relative z-10 mx-auto flex min-h-[470px] max-w-7xl items-center px-7 pb-24 pt-24 text-white md:min-h-[520px] md:px-10">
                                <div class="max-w-3xl">
                                    <p class="text-xs font-black uppercase tracking-widest text-orange-200">Festify Live</p>
                                    <h1 class="mt-4 text-[44px] font-black leading-[.98] md:text-[76px]">Temukan Konser Favoritmu</h1>
                                    <p class="mt-5 text-xl font-bold text-neutral-100">Pesan tiket, terima E-Ticket QR, tukar gelang, dan masuk venue lebih cepat.</p>
                                </div>
                            </div>
                            <a href="{{ route('concerts.index') }}" class="absolute bottom-8 left-1/2 z-20 flex w-[min(620px,calc(100%-2rem))] -translate-x-1/2 items-center justify-center rounded-xl bg-white/85 px-5 py-4 text-base font-black text-neutral-950 shadow-lg backdrop-blur md:text-xl">Lihat konser tersedia</a>
                        </div>
                    </article>
                @endforelse
            </div>

            @if($bannerConcerts->count() > 1)
                <button type="button" class="absolute left-5 top-1/2 z-30 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white text-neutral-900 shadow-lg transition hover:scale-105" data-promo-prev aria-label="Slide sebelumnya">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button type="button" class="absolute right-5 top-1/2 z-30 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white text-neutral-900 shadow-lg transition hover:scale-105" data-promo-next aria-label="Slide berikutnya">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </button>
                <div class="absolute bottom-10 left-7 z-30 flex gap-3 md:left-10" data-promo-dots>
                    @foreach($bannerConcerts as $concert)
                        <button type="button" class="h-2 w-2 rounded-full bg-white/40 transition data-[active=true]:h-4 data-[active=true]:w-4 data-[active=true]:bg-white" data-promo-dot data-active="{{ $loop->first ? 'true' : 'false' }}" aria-label="Slide {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
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

<script>
(() => {
    const slider = document.querySelector('[data-promo-slider]');
    if (!slider) return;

    const track = slider.querySelector('[data-promo-track]');
    const slides = Array.from(track.children);
    const prev = slider.querySelector('[data-promo-prev]');
    const next = slider.querySelector('[data-promo-next]');
    const dots = Array.from(slider.querySelectorAll('[data-promo-dot]'));
    let index = 0;
    let timer = null;

    const show = (nextIndex) => {
        index = (nextIndex + slides.length) % slides.length;
        track.style.transform = `translateX(-${index * 100}%)`;
        dots.forEach((dot, dotIndex) => dot.dataset.active = dotIndex === index ? 'true' : 'false');
    };

    const start = () => {
        if (slides.length < 2) return;
        timer = window.setInterval(() => show(index + 1), 6000);
    };

    const restart = () => {
        window.clearInterval(timer);
        start();
    };

    prev?.addEventListener('click', () => { show(index - 1); restart(); });
    next?.addEventListener('click', () => { show(index + 1); restart(); });
    dots.forEach((dot, dotIndex) => dot.addEventListener('click', () => { show(dotIndex); restart(); }));
    start();
})();
</script>
@endsection
