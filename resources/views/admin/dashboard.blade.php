@extends('layouts.admin', ['title' => 'Admin', 'pageTitle' => 'Dashboard Admin'])
@section('content')
<section class="grid gap-6">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach($stats as $label=>$value)
            <div class="fi-card p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-bold text-neutral-500">{{ $label }}</p>
                        <p class="mt-2 text-3xl font-black">{{ $value }}</p>
                    </div>
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-orange-100 text-sm font-black text-orange-700">{{ substr($label, 0, 1) }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <div class="fi-card overflow-hidden">
            <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4">
                <div>
                    <h2 class="text-lg font-black">Pemesanan Terbaru</h2>
                    <p class="text-sm text-neutral-500">Ringkasan transaksi terakhir.</p>
                </div>
                <a class="fi-btn-muted" href="{{ route('admin.orders') }}">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="fi-table min-w-[760px]">
                    <thead>
                        <tr><th>Kode</th><th>User</th><th>Konser</th><th>Area</th><th>Status</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="font-mono">{{ $order->order_code }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td class="font-bold">{{ $order->concert->name }}</td>
                                <td>{{ $order->ticketZone?->name ?? '-' }}</td>
                                <td><span class="{{ $order->order_status === 'paid' ? 'fi-badge-success' : 'fi-badge-warning' }}">{{ $order->order_status }}</span></td>
                                <td class="font-black">Rp{{ number_format($order->total_price,0,',','.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-neutral-500">Belum ada pemesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-6">
            <div class="fi-card p-5">
                <h2 class="text-lg font-black">Insight Operasional</h2>
                <div class="mt-4 grid gap-3">
                    @foreach($insights as $label => $value)
                        <div class="rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3">
                            <p class="text-xs font-bold uppercase text-neutral-500">{{ $label }}</p>
                            <p class="mt-1 text-xl font-black">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="fi-card p-5">
                <h2 class="text-lg font-black">Akses Cepat</h2>
                <div class="mt-4 grid gap-2">
                    @foreach(['concerts'=>'Konser','users'=>'User','officers'=>'Petugas','orders'=>'Pemesanan','payments'=>'Pembayaran','tickets'=>'E-Ticket','wristbands'=>'Gelang','reports'=>'Laporan'] as $route=>$label)
                        <a class="flex items-center justify-between rounded-xl border border-neutral-200 px-4 py-3 text-sm font-bold transition hover:border-orange-300 hover:bg-orange-50" href="{{ route('admin.'.$route) }}">
                            <span>{{ $label }}</span>
                            <span class="text-neutral-400">Open</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

