@extends('layouts.app', ['title' => 'Dashboard User'])
@section('content')
<section class="mx-auto grid max-w-7xl gap-10 px-4 py-8 sm:py-12">
    <div class="grid gap-5 lg:grid-cols-[1fr_360px]">
        <div class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm sm:p-7">
            <p class="text-sm font-bold uppercase text-orange-700">Dashboard pelanggan</p>
            <h1 class="mt-2 text-3xl font-black sm:text-4xl">Halo, {{ $user->name }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-neutral-600">Pantau pesanan, pembayaran, dan E-Ticket sebelum ditukar menjadi gelang fisik di loket venue.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a class="rounded-full bg-neutral-950 px-5 py-3 text-sm font-bold text-white" href="{{ route('user.concerts') }}">Cari Konser</a>
                <a class="rounded-full bg-orange-700 px-5 py-3 text-sm font-bold text-white" href="{{ route('user.tickets') }}">Tiket Saya</a>
                <a class="rounded-full border border-neutral-300 px-5 py-3 text-sm font-bold" href="{{ route('user.orders') }}">Riwayat Pemesanan</a>
            </div>
        </div>
        <div class="rounded-lg border border-orange-200 bg-orange-50 p-5 text-orange-950">
            <p class="text-sm font-black uppercase">Langkah berikutnya</p>
            <ol class="mt-3 grid gap-2 text-sm font-semibold">
                <li>1. Selesaikan pembayaran pesanan pending.</li>
                <li>2. Tunggu admin verifikasi agar E-Ticket terbit.</li>
                <li>3. Tukarkan E-Ticket di loket venue untuk mendapatkan gelang.</li>
            </ol>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($stats as $label => $value)
            <div class="rounded-lg border bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-neutral-500">{{ $label }}</p>
                <strong class="mt-2 block text-3xl font-black">{{ $value }}</strong>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_420px]">
        <div>
            <h2 class="text-2xl font-black">Konser Rekomendasi</h2>
            <div class="mt-5 grid gap-6 md:grid-cols-3">
                @forelse($concerts as $concert)
                    <x-concert-card :concert="$concert" />
                @empty
                    <div class="rounded-lg border bg-white p-6 text-neutral-500 md:col-span-3">Belum ada konser aktif.</div>
                @endforelse
            </div>
        </div>
        <div class="rounded-lg border bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-black">Pesanan Terakhir</h2>
                <a href="{{ route('user.orders') }}" class="text-sm font-bold text-orange-700">Lihat Semua</a>
            </div>
            <div class="mt-4 grid gap-3">
                @forelse($orders as $order)
                    <a href="{{ route('user.orders.show', $order) }}" class="rounded-lg border border-neutral-200 p-4 text-sm hover:border-orange-300 hover:bg-orange-50">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-mono text-xs text-neutral-500">{{ $order->order_code }}</p>
                                <p class="mt-1 font-black">{{ $order->concert->name }}</p>
                                <p class="text-neutral-500">{{ $order->ticketZone?->name ?? '-' }}</p>
                            </div>
                            <span class="{{ $order->order_status === 'paid' ? 'fi-badge-success' : 'fi-badge-warning' }}">{{ $order->order_status }}</span>
                        </div>
                    </a>
                @empty
                    <p class="rounded-lg bg-neutral-50 p-4 text-sm text-neutral-500">Belum ada pesanan.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
