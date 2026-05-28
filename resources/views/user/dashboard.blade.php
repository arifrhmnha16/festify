@extends('layouts.app', ['title' => 'Dashboard User'])
@section('content')
<section class="mx-auto max-w-7xl px-4 py-12">
    <h1 class="text-4xl font-black">Halo, {{ $user->name }}</h1>
    <div class="mt-8 grid gap-4 md:grid-cols-3">
        <div class="rounded-lg border bg-white p-5"><p>Pesanan aktif</p><strong class="text-3xl">{{ $orders->where('order_status','pending')->count() }}</strong></div>
        <div class="rounded-lg border bg-white p-5"><p>E-Ticket aktif</p><strong class="text-3xl">{{ $user->eTickets()->where('ticket_status','belum_ditukar')->count() }}</strong></div>
        <div class="rounded-lg border bg-white p-5"><p>Status pembayaran terbaru</p><strong>{{ $orders->first()?->payment?->payment_status ?? '-' }}</strong></div>
    </div>
    <div class="mt-10 flex flex-wrap gap-3"><a class="rounded-full bg-neutral-950 px-5 py-3 font-bold text-white" href="{{ route('user.concerts') }}">Cari Konser</a><a class="rounded-full bg-orange-700 px-5 py-3 font-bold text-white" href="{{ route('user.tickets') }}">Tiket Saya</a><a class="rounded-full border px-5 py-3 font-bold" href="{{ route('user.orders') }}">Riwayat Pemesanan</a></div>
    <h2 class="mt-12 text-2xl font-black">Konser Rekomendasi</h2>
    <div class="mt-5 grid gap-6 md:grid-cols-3">@foreach($concerts as $concert)<x-concert-card :concert="$concert" />@endforeach</div>
</section>
@endsection
