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
            <p class="text-sm font-bold uppercase tracking-widest text-orange-700">Verifikasi pembayaran</p>
            <h1 class="mt-2 text-4xl font-black">Kirim Bukti Pembayaran</h1>
            <p class="mt-3 max-w-2xl text-neutral-600">E-Ticket diterbitkan setelah admin memverifikasi pembayaran. Pastikan nominal dan kode pemesanan sesuai.</p>

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
                    <p class="text-sm text-neutral-300">Total yang harus dibayar</p>
                    <p class="mt-1 text-3xl font-black">Rp{{ number_format($order->total_price,0,',','.') }}</p>
                </div>

                @if($payment?->payment_status === 'success')
                    <div class="mt-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                        Pembayaran sudah diverifikasi. E-Ticket dapat dilihat pada menu Tiket Saya.
                    </div>
                    <a href="{{ route('user.tickets') }}" class="mt-5 inline-flex rounded-full bg-neutral-950 px-6 py-3 font-bold text-white">Lihat E-Ticket</a>
                @else
                    <form method="post" action="{{ route('user.payments.submit', $order) }}" enctype="multipart/form-data" class="mt-6 grid gap-5">
                        @csrf
                        <div>
                            <label class="text-sm font-bold">Metode pembayaran</label>
                            <select name="payment_method" class="mt-2 w-full rounded-md border border-neutral-300 px-4 py-3">
                                <option value="transfer_manual" @selected($payment?->payment_method === 'transfer_manual')>Transfer Manual</option>
                                <option value="ewallet" @selected($payment?->payment_method === 'ewallet')>E-Wallet</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-bold">Upload bukti pembayaran</label>
                            <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" class="mt-2 w-full rounded-md border border-neutral-300 px-4 py-3 file:mr-4 file:rounded-full file:border-0 file:bg-neutral-950 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white" required>
                            <p class="mt-2 text-sm text-neutral-500">Format JPG, PNG, atau PDF. Maksimal 2 MB.</p>
                        </div>
                        @if($payment?->payment_proof)
                            <a href="{{ asset('storage/'.$payment->payment_proof) }}" target="_blank" class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-800">Bukti sebelumnya sudah dikirim. Klik untuk melihat file.</a>
                        @endif
                        <button class="rounded-full bg-orange-700 px-6 py-3 font-black text-white hover:bg-orange-800">Kirim Bukti untuk Verifikasi</button>
                    </form>
                @endif
            </div>
        </div>

        <aside class="h-fit rounded-lg border bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black">Instruksi Transfer</h2>
            <div class="mt-5 grid gap-4 text-sm">
                <div class="rounded-lg border border-neutral-200 p-4">
                    <p class="text-neutral-500">Bank</p>
                    <p class="mt-1 font-black">BCA Festify</p>
                </div>
                <div class="rounded-lg border border-neutral-200 p-4">
                    <p class="text-neutral-500">Nomor rekening</p>
                    <p class="mt-1 font-mono text-lg font-black">1234567890</p>
                </div>
                <div class="rounded-lg border border-neutral-200 p-4">
                    <p class="text-neutral-500">Berita transfer</p>
                    <p class="mt-1 font-mono font-black">{{ $order->order_code }}</p>
                </div>
            </div>
            <div class="mt-5 rounded-lg bg-neutral-100 p-4 text-sm text-neutral-700">
                Setelah bukti dikirim, status tetap pending sampai admin menyetujui pembayaran.
            </div>
        </aside>
    </div>
</section>
@endsection
