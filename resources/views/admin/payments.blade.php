@extends('layouts.admin', ['title' => 'Kelola Pembayaran', 'pageTitle' => 'Kelola Pembayaran'])
@section('content')
@php
    $statusStyles = [
        'pending' => 'border-amber-200 bg-amber-50 text-amber-800',
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'failed' => 'border-red-200 bg-red-50 text-red-800',
    ];
@endphp
<section class="grid gap-6">
    <div class="grid gap-3 md:grid-cols-3">
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-800"><p class="text-sm font-bold">Pending</p><p class="text-3xl font-black">{{ $paymentStats['pending'] ?? 0 }}</p></div>
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800"><p class="text-sm font-bold">Success</p><p class="text-3xl font-black">{{ $paymentStats['success'] ?? 0 }}</p></div>
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800"><p class="text-sm font-bold">Failed</p><p class="text-3xl font-black">{{ $paymentStats['failed'] ?? 0 }}</p></div>
    </div>

    <form method="post" action="{{ route('admin.payments.store') }}" class="rounded-lg border bg-white p-5 shadow-sm">
        @csrf
        <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-lg font-black">Tambah Pembayaran</h2>
            <button class="fi-btn-dark">Tambah</button>
        </div>
        <div class="grid gap-3 md:grid-cols-[1fr_200px_180px]">
            <select name="order_id" class="rounded-md border border-neutral-300 px-3 py-2" required>
                @forelse($orders as $order)
                    <option value="{{ $order->id }}">{{ $order->order_code }} - {{ $order->user->name }} - {{ $order->concert->name }}</option>
                @empty
                    <option disabled>Tidak ada order tanpa pembayaran</option>
                @endforelse
            </select>
            <select name="payment_method" class="rounded-md border border-neutral-300 px-3 py-2" required>
                <option value="transfer_manual">Transfer Manual</option>
                <option value="ewallet">E-Wallet</option>
            </select>
            <select name="payment_status" class="rounded-md border border-neutral-300 px-3 py-2" required>
                <option>pending</option>
                <option>success</option>
                <option>failed</option>
            </select>
        </div>
    </form>

    <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table class="fi-table min-w-[1150px]">
            <thead class="bg-neutral-100 text-xs uppercase tracking-wide text-neutral-600">
                <tr>
                    <th class="p-3">Order</th>
                    <th class="p-3">User</th>
                    <th class="p-3">Konser</th>
                    <th class="p-3">Area</th>
                    <th class="p-3">Nominal</th>
                    <th class="p-3">Metode</th>
                    <th class="p-3">Bukti</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    @php($statusClass = $statusStyles[$payment->payment_status] ?? 'border-neutral-200 bg-neutral-50 text-neutral-700')
                    <tr class="border-t align-top">
                        <td class="p-3 font-mono">{{ $payment->order->order_code }}</td>
                        <td class="p-3">{{ $payment->order->user->name }}</td>
                        <td class="p-3 font-bold">{{ $payment->order->concert->name }}</td>
                        <td class="p-3">{{ $payment->order->ticketZone?->name ?? '-' }}</td>
                        <td class="p-3 font-black">Rp{{ number_format($payment->total_amount,0,',','.') }}</td>
                        <td class="p-3">{{ $payment->payment_method }}</td>
                        <td class="p-3">
                            @if($payment->payment_proof)
                                <a href="{{ asset('storage/'.$payment->payment_proof) }}" target="_blank" class="font-bold text-orange-700">Lihat Bukti</a>
                            @else
                                <span class="text-neutral-400">-</span>
                            @endif
                        </td>
                        <td class="p-3">
                            <select form="payment-update-{{ $payment->id }}" name="payment_status" class="w-36 rounded-md border border-neutral-300 px-3 py-2 {{ $statusClass }}">
                                @foreach(['pending','success','failed'] as $status)
                                    <option @selected($payment->payment_status===$status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-3">
                            <form id="payment-update-{{ $payment->id }}" method="post" action="{{ route('admin.payments.update',$payment) }}" class="hidden">@csrf @method('put')</form>
                            <div class="flex justify-end gap-2">
                                <button form="payment-update-{{ $payment->id }}" class="fi-btn-primary">Simpan</button>
                                <form method="post" action="{{ route('admin.payments.destroy',$payment) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="fi-btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="p-6 text-center text-neutral-500">Belum ada pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $payments->links() }}</div>
</section>
@endsection

