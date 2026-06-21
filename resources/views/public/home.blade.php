@extends('layouts.app', ['title' => 'Festify'])
@section('content')
@php
    $bannerConcerts = $concerts->take(4);
    if ($featuredConcert && ! $bannerConcerts->contains('id', $featuredConcert->id)) {
        $bannerConcerts = collect([$featuredConcert])->merge($bannerConcerts)->take(4);
    }
@endphp

<section class="bg-white">
    <div class="mx-auto max-w-6xl px-4 py-6 md:py-8">
        <div class="relative overflow-hidden rounded-lg border border-neutral-200 bg-neutral-950 shadow-sm" data-promo-slider>
            <div class="flex transition-transform duration-500 ease-out" data-promo-track>
                @forelse($bannerConcerts as $concert)
                    <article class="relative min-w-full">
                        <div class="grid min-h-[430px] overflow-hidden md:grid-cols-[1.05fr_.95fr]">
                            <div class="relative flex items-center px-6 py-10 text-white md:px-10 lg:px-12">
                                <div class="absolute inset-0 bg-[linear-gradient(135deg,#111827_0%,#431407_48%,#7c2d12_100%)]"></div>
                                <div class="relative z-10 max-w-xl">
                                    <p class="text-xs font-black uppercase tracking-widest text-orange-200">
                                        @auth
                                            Selamat datang, {{ auth()->user()->name }}
                                        @else
                                            Promo Konser Festify
                                        @endauth
                                    </p>
                                    <h1 class="mt-4 text-[34px] font-black leading-tight md:text-[42px] lg:text-[48px]">{{ $concert->name }}</h1>
                                    <p class="mt-3 text-lg font-bold text-orange-100">{{ $concert->artist }}</p>
                                    <div class="mt-6 grid gap-3 text-sm font-bold text-neutral-100 sm:grid-cols-2">
                                        <p class="rounded-lg border border-white/15 bg-white/10 px-4 py-3">{{ $concert->venue }}</p>
                                        <p class="rounded-lg border border-white/15 bg-white/10 px-4 py-3">{{ $concert->date->format('d M Y') }} - {{ substr($concert->time, 0, 5) }} WIB</p>
                                    </div>
                                    <div class="mt-7 flex flex-wrap items-center gap-3">
                                        <a href="{{ route('concerts.show', $concert) }}" class="rounded-full bg-white px-6 py-3 text-sm font-black text-neutral-950">Amankan Tiket</a>
                                        <a href="{{ route('concerts.index') }}" class="rounded-full border border-white/30 px-6 py-3 text-sm font-black text-white">Lihat Semua</a>
                                    </div>
                                </div>
                            </div>
                            <div class="relative min-h-[260px] bg-neutral-900 md:min-h-full">
                                @if($concert->poster)
                                    <img src="{{ asset('storage/'.$concert->poster) }}" alt="{{ $concert->name }}" class="h-full w-full object-cover">
                                @else
                                    <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-full w-full object-cover opacity-80">
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/50 via-transparent to-transparent md:bg-gradient-to-r md:from-neutral-950/40"></div>
                                <div class="absolute bottom-5 left-5 rounded-lg bg-white/90 px-4 py-3 text-neutral-950 shadow-sm">
                                    <p class="text-xs font-bold text-neutral-500">Mulai dari</p>
                                    <p class="text-xl font-black">Rp{{ number_format($concert->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <article class="relative min-w-full">
                        <div class="grid min-h-[430px] overflow-hidden md:grid-cols-[1.05fr_.95fr]">
                            <div class="relative flex items-center px-6 py-10 text-white md:px-10 lg:px-12">
                                <div class="absolute inset-0 bg-[linear-gradient(135deg,#111827_0%,#431407_48%,#7c2d12_100%)]"></div>
                                <div class="relative z-10 max-w-xl">
                                    <p class="text-xs font-black uppercase tracking-widest text-orange-200">Promo Konser Festify</p>
                                    <h1 class="mt-4 text-[34px] font-black leading-tight md:text-[42px] lg:text-[48px]">Temukan Konser Favoritmu</h1>
                                    <p class="mt-4 text-base leading-7 text-orange-100">Pesan tiket, terima E-Ticket QR, tukar gelang, dan masuk venue lebih cepat.</p>
                                    <a href="{{ route('concerts.index') }}" class="mt-7 inline-flex rounded-full bg-white px-6 py-3 text-sm font-black text-neutral-950">Lihat Konser</a>
                                </div>
                            </div>
                            <div class="min-h-[260px] bg-neutral-900">
                                <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-full w-full object-cover opacity-80">
                            </div>
                        </div>
                    </article>
                @endforelse
            </div>

            @if($bannerConcerts->count() > 1)
                <div class="absolute bottom-5 right-5 flex items-center gap-2">
                    <button type="button" class="grid h-10 w-10 place-items-center rounded-full border border-white/25 bg-white/15 text-white backdrop-blur hover:bg-white/25" data-promo-prev aria-label="Slide sebelumnya">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button type="button" class="grid h-10 w-10 place-items-center rounded-full border border-white/25 bg-white/15 text-white backdrop-blur hover:bg-white/25" data-promo-next aria-label="Slide berikutnya">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>
                <div class="absolute bottom-6 left-1/2 flex -translate-x-1/2 gap-2" data-promo-dots>
                    @foreach($bannerConcerts as $concert)
                        <button type="button" class="h-2.5 w-2.5 rounded-full bg-white/40 transition data-[active=true]:w-8 data-[active=true]:bg-white" data-promo-dot data-active="{{ $loop->first ? 'true' : 'false' }}" aria-label="Slide {{ $loop->iteration }}"></button>
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
