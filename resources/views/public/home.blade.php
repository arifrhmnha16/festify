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
                        <a href="{{ route('concerts.show', $concert) }}" class="block aspect-[4/1] overflow-hidden bg-neutral-950">
                            @if($concert->poster)
                                <img src="{{ asset('storage/'.$concert->poster) }}" alt="{{ $concert->name }}" class="h-full w-full object-cover">
                            @else
                                <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-full w-full object-cover opacity-80">
                            @endif
                        </a>
                    </article>
                @empty
                    <article class="relative min-w-full">
                        <a href="{{ route('concerts.index') }}" class="block aspect-[4/1] overflow-hidden bg-neutral-950">
                            <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-full w-full object-cover opacity-80">
                        </a>
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
                <div class="absolute bottom-6 left-7 z-30 flex gap-3 md:left-10" data-promo-dots>
                    @foreach($bannerConcerts as $concert)
                        <button type="button" class="h-2 w-2 rounded-full bg-white/40 transition data-[active=true]:h-4 data-[active=true]:w-4 data-[active=true]:bg-white" data-promo-dot data-active="{{ $loop->first ? 'true' : 'false' }}" aria-label="Slide {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-10">
    <div class="mb-7 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <span class="grid h-9 w-9 place-items-center text-blue-950">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m3 11 18-5v12L3 14v-3Z"/><path d="M11.6 17.2 13 21H8.5l-1.2-5.1"/></svg>
            </span>
            <h2 class="text-3xl font-black text-neutral-950 md:text-4xl">Konser Pilihan Untukmu</h2>
        </div>
        <a class="hidden rounded-full border border-neutral-200 px-5 py-2 text-sm font-bold text-neutral-700 hover:border-orange-700 hover:text-orange-700 md:inline-flex" href="{{ route('concerts.index') }}">Lihat semua</a>
    </div>
    <div class="grid gap-x-6 gap-y-10 md:grid-cols-2 lg:grid-cols-4">
        @foreach($concerts as $concert)
            <x-concert-card :concert="$concert" />
        @endforeach
    </div>
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
