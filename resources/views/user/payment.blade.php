@extends('layouts.app', ['title' => 'Pembayaran'])
@section('content')
@php
    $payment = $order->payment;
    $statusStyles = [
        'pending' => 'border-amber-200 bg-amber-50 text-amber-800',
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'failed' => 'border-red-200 bg-red-50 text-red-800',
    ];
@endphp
<section class="mx-auto max-w-6xl px-4 py-12">
    <div class="grid gap-6 lg:grid-cols-[1fr_380px]">
        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-orange-700">Midtrans sandbox</p>
            <h1 class="mt-2 text-4xl font-black">Pembayaran Tiket</h1>
            <p class="mt-3 max-w-2xl text-neutral-600">Bayar melalui Midtrans Snap. E-Ticket diterbitkan otomatis setelah status transaksi berhasil.</p>

            <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-sm text-neutral-500">Kode pemesanan</p>
                        <p class="mt-1 font-mono text-lg font-black">{{ $order->order_code }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500">Status pembayaran</p>
                        <span class="mt-2 inline-flex rounded-full border px-3 py-1 text-sm font-black {{ $statusStyles[$payment?->payment_status] ?? 'border-neutral-200 bg-neutral-50 text-neutral-700' }}">{{ $payment?->payment_status ?? 'pending' }}</span>
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500">Konser</p>
                        <p class="mt-1 font-bold">{{ $order->concert->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500">Area venue</p>
                        <p class="mt-1 font-bold">{{ $order->ticketZone?->name ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-6 rounded-lg bg-neutral-950 p-5 text-white">
                    <p class="text-sm text-neutral-300">Total pembayaran</p>
                    <p class="mt-1 text-3xl font-black">Rp{{ number_format($order->total_price,0,',','.') }}</p>
                </div>

                @if(isset($midtransError))
                    <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-800">{{ $midtransError }}</div>
                @endif

                @if($payment?->payment_status === 'success')
                    <div class="mt-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                        Pembayaran berhasil. E-Ticket dapat dilihat pada menu Tiket Saya.
                    </div>
                    <a href="{{ route('user.tickets') }}" class="mt-5 inline-flex rounded-full bg-neutral-950 px-6 py-3 font-bold text-white">Lihat E-Ticket</a>
                @elseif(! $midtransReady)
                    <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        Konfigurasi Midtrans belum lengkap. Isi <strong>MIDTRANS_SERVER_KEY</strong> dan <strong>MIDTRANS_CLIENT_KEY</strong> sandbox di file <strong>.env</strong>.
                    </div>
                @else
                    <div class="mt-6 flex flex-wrap gap-3">
                        @if($payment?->snap_token)
                            <button id="pay-button" type="button" class="rounded-full bg-orange-700 px-6 py-3 font-black text-white hover:bg-orange-800">
                                Bayar dengan Midtrans
                            </button>
                            <a href="{{ $payment->snap_redirect_url }}" class="rounded-full border border-neutral-300 px-6 py-3 font-bold" target="_blank" rel="noopener">
                                Buka Halaman Pembayaran
                            </a>
                        @endif
                        @if($payment?->gateway_order_id)
                            <form method="post" action="{{ route('user.payments.sync', $order) }}">
                                @csrf
                                <button class="rounded-full border border-neutral-300 px-6 py-3 font-bold">Cek Status</button>
                            </form>
                        @else
                            <span class="inline-flex items-center rounded-full border border-neutral-200 px-6 py-3 text-sm font-bold text-neutral-500">
                                Pilih pembayaran di Snap dulu
                            </span>
                        @endif
                    </div>
                    <p class="mt-3 text-sm text-neutral-500">Untuk sandbox, gunakan metode dan kartu test dari dashboard/dokumentasi Midtrans.</p>
                @endif
            </div>
        </div>

        <aside class="h-fit rounded-lg border bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black">Alur Otomatis</h2>
            <div class="mt-5 grid gap-4 text-sm">
                <div class="rounded-lg border border-neutral-200 p-4">
                    <p class="font-black">1. Bayar di Snap</p>
                    <p class="mt-1 text-neutral-600">Pilih VA, QRIS, e-wallet, kartu, atau metode sandbox lain.</p>
                </div>
                <div class="rounded-lg border border-neutral-200 p-4">
                    <p class="font-black">2. Midtrans kirim notifikasi</p>
                    <p class="mt-1 text-neutral-600">Webhook memperbarui status pembayaran di sistem.</p>
                </div>
                <div class="rounded-lg border border-neutral-200 p-4">
                    <p class="font-black">3. E-Ticket terbit</p>
                    <p class="mt-1 text-neutral-600">Setelah status success, sistem membuat E-Ticket otomatis.</p>
                </div>
            </div>
            <div class="mt-5 rounded-lg bg-neutral-100 p-4 text-sm text-neutral-700">
                Notification URL sandbox: <span class="font-mono">{{ route('midtrans.notification') }}</span>
            </div>
        </aside>
    </div>
</section>

@if($midtransReady && $payment?->snap_token && $payment?->payment_status !== 'success')
    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        document.getElementById('pay-button')?.addEventListener('click', function () {
            window.snap.pay(@json($payment->snap_token), {
                onSuccess: function () { window.location.href = @json(route('midtrans.finish', ['order' => $order->order_code])); },
                onPending: function () { window.location.href = @json(route('user.orders.show', $order)); },
                onError: function () { window.location.href = @json(route('user.orders.show', $order)); },
                onClose: function () {}
            });
        });
    </script>
@endif
@endsection
