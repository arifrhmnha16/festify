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
                        <div class="relative aspect-[3/1] overflow-hidden bg-neutral-950 md:aspect-[4/1]">
                            @if($concert->poster)
                                <img src="{{ asset('storage/'.$concert->poster) }}" alt="{{ $concert->name }}" class="h-full w-full object-cover">
                            @else
                                <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-full w-full object-cover opacity-80">
                            @endif
                            @if($concert->is_promo)
                                <span class="absolute left-4 top-4 z-20 rounded-md bg-red-600 px-3 py-1 text-xs font-black text-white shadow-sm md:left-6 md:top-6">Promo</span>
                            @endif
                            <a href="{{ route('concerts.show', $concert) }}" class="absolute bottom-3 left-1/2 z-20 flex w-[min(520px,calc(100%-3rem))] -translate-x-1/2 items-center justify-between gap-2 rounded-lg bg-white/80 px-3.5 py-2 text-xs font-black text-neutral-950 shadow-md backdrop-blur md:bottom-4 md:w-[min(500px,calc(100%-2rem))] md:px-3.5 md:py-2.5 md:text-xs">
                                <span class="min-w-0 truncate">{{ $concert->name }} <span class="text-blue-700">Beli tiketnya sekarang!</span></span>
                                <svg class="h-5 w-5 shrink-0 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <article class="relative min-w-full">
                        <div class="relative aspect-[3/1] overflow-hidden bg-neutral-950 md:aspect-[4/1]">
                            <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-full w-full object-cover opacity-80">
                            <a href="{{ route('concerts.index') }}" class="absolute bottom-3 left-1/2 z-20 flex w-[min(520px,calc(100%-3rem))] -translate-x-1/2 items-center justify-between gap-2 rounded-lg bg-white/80 px-3.5 py-2 text-xs font-black text-neutral-950 shadow-md backdrop-blur md:bottom-4 md:w-[min(500px,calc(100%-2rem))] md:px-3.5 md:py-2.5 md:text-xs">
                                <span class="min-w-0 truncate">Festify <span class="text-blue-700">Beli tiketnya sekarang!</span></span>
                                <svg class="h-5 w-5 shrink-0 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            </a>
                        </div>
                    </article>
                @endforelse
            </div>

            @if($bannerConcerts->count() > 1)
                <button type="button" class="absolute left-3 top-1/2 z-30 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-full bg-white text-neutral-900 shadow-md transition hover:scale-105 md:left-5 md:h-9 md:w-9" data-promo-prev aria-label="Slide sebelumnya">
                        <svg class="h-3.5 w-3.5 md:h-4 md:w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button type="button" class="absolute right-3 top-1/2 z-30 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-full bg-white text-neutral-900 shadow-md transition hover:scale-105 md:right-5 md:h-9 md:w-9" data-promo-next aria-label="Slide berikutnya">
                        <svg class="h-3.5 w-3.5 md:h-4 md:w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </button>
                <div class="absolute bottom-5 left-4 z-30 flex gap-1.5 md:bottom-7 md:left-10 md:gap-2" data-promo-dots>
                    @foreach($bannerConcerts as $concert)
                        <button type="button" class="h-1.5 w-1.5 rounded-full bg-white/45 transition data-[active=true]:w-3 data-[active=true]:bg-white" data-promo-dot data-active="{{ $loop->first ? 'true' : 'false' }}" aria-label="Slide {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<section id="konser-pilihan" class="scroll-mt-44 py-14 md:py-16">
    <div class="mx-auto max-w-6xl px-4">
        <div class="mb-7 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="grid h-8 w-8 place-items-center text-blue-950">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m3 11 18-5v12L3 14v-3Z"/><path d="M11.6 17.2 13 21H8.5l-1.2-5.1"/></svg>
                </span>
                <h2 class="text-2xl font-black text-neutral-950 md:text-3xl">Konser Pilihan Untukmu</h2>
            </div>
            <a class="hidden rounded-full border border-neutral-200 px-5 py-2 text-sm font-bold text-neutral-700 hover:border-orange-700 hover:text-orange-700 md:inline-flex" href="{{ route('concerts.index') }}">Lihat semua</a>
        </div>
        <div class="-mx-4 flex snap-x gap-5 overflow-x-auto px-4 pb-4 md:mx-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10 md:overflow-visible md:px-0 md:pb-0 lg:grid-cols-3">
            @foreach($concerts as $concert)
                <div class="w-[82vw] shrink-0 snap-start md:w-auto">
                    <x-concert-card :concert="$concert" />
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="konser-terdekat" class="scroll-mt-44 bg-white py-14 md:py-16">
    <div class="mx-auto max-w-6xl px-4">
        <div class="mb-7 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="grid h-8 w-8 place-items-center text-blue-950">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M8 2v4"/><path d="M16 2v4"/><path d="M3 10h18"/><path d="M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/></svg>
                </span>
                <h2 class="text-2xl font-black text-neutral-950 md:text-3xl">Konser Terdekat</h2>
            </div>
            <a class="hidden rounded-full border border-neutral-200 px-5 py-2 text-sm font-bold text-neutral-700 hover:border-orange-700 hover:text-orange-700 md:inline-flex" href="{{ route('concerts.index') }}">Lihat semua konser</a>
        </div>
        <div class="-mx-4 flex snap-x gap-5 overflow-x-auto px-4 pb-4 md:mx-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10 md:overflow-visible md:px-0 md:pb-0 lg:grid-cols-3">
            @forelse($upcomingConcerts as $concert)
                <div class="w-[82vw] shrink-0 snap-start md:w-auto">
                    <x-concert-card :concert="$concert" />
                </div>
            @empty
                <p class="text-sm font-semibold text-neutral-500">Belum ada konser terdekat.</p>
            @endforelse
        </div>
    </div>
</section>

<section id="cara-kerja" class="scroll-mt-44 bg-white py-14 md:py-16">
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
    let pointerStartX = null;

    const show = (nextIndex) => {
        index = (nextIndex + slides.length) % slides.length;
        track.style.transform = `translateX(-${index * 100}%)`;
        dots.forEach((dot, dotIndex) => dot.dataset.active = dotIndex === index ? 'true' : 'false');
    };

    const start = () => {
        if (slides.length < 2) return;
        timer = window.setInterval(() => show(index + 1), 5000);
    };

    const restart = () => {
        window.clearInterval(timer);
        start();
    };

    prev?.addEventListener('click', () => { show(index - 1); restart(); });
    next?.addEventListener('click', () => { show(index + 1); restart(); });
    dots.forEach((dot, dotIndex) => dot.addEventListener('click', () => { show(dotIndex); restart(); }));
    slider.addEventListener('pointerdown', (event) => {
        pointerStartX = event.clientX;
    });
    slider.addEventListener('pointerup', (event) => {
        if (pointerStartX === null) return;
        const deltaX = event.clientX - pointerStartX;
        pointerStartX = null;
        if (Math.abs(deltaX) < 50) return;
        show(deltaX < 0 ? index + 1 : index - 1);
        restart();
    });
    slider.addEventListener('pointercancel', () => {
        pointerStartX = null;
    });
    start();
})();
</script>
@endsection
