@extends('layouts.admin', ['title' => 'Kelola Pemesanan', 'pageTitle' => 'Kelola Pemesanan'])
@section('content')
<section class="grid gap-6">
    <form method="post" action="{{ route('admin.orders.store') }}" class="rounded-lg border bg-white p-5 shadow-sm">
        @csrf
        <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-lg font-black">Tambah Pemesanan</h2>
            <button class="fi-btn-dark">Tambah</button>
        </div>
        <div class="grid gap-3 md:grid-cols-[1fr_1fr_140px_180px]">
            <select name="user_id" class="rounded-md border border-neutral-300 px-3 py-2" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <select name="ticket_zone_id" class="rounded-md border border-neutral-300 px-3 py-2" required>
                @foreach($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->concert->name }} - {{ $zone->name }}</option>
                @endforeach
            </select>
            <input type="number" name="ticket_quantity" value="1" min="1" class="rounded-md border border-neutral-300 px-3 py-2" required>
            <select name="order_status" class="rounded-md border border-neutral-300 px-3 py-2" required>
                <option>pending</option>
                <option>paid</option>
                <option>cancelled</option>
                <option>expired</option>
            </select>
        </div>
    </form>

    <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table class="fi-table min-w-[1000px]">
            <thead class="bg-neutral-100 text-xs uppercase tracking-wide text-neutral-600">
                <tr>
                    <th class="p-3">Kode</th>
                    <th class="p-3">User</th>
                    <th class="p-3">Konser</th>
                    <th class="p-3">Area</th>
                    <th class="p-3">Qty</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="border-t align-top">
                        <td class="p-3 font-mono">{{ $order->order_code }}</td>
                        <td class="p-3">{{ $order->user->name }}</td>
                        <td class="p-3 font-bold">{{ $order->concert->name }}</td>
                        <td class="p-3">{{ $order->ticketZone?->name ?? '-' }}</td>
                        <td class="p-3">{{ $order->ticket_quantity }}</td>
                        <td class="p-3 font-black">Rp{{ number_format($order->total_price,0,',','.') }}</td>
                        <td class="p-3">
                            <select form="order-update-{{ $order->id }}" name="order_status" class="w-36 rounded-md border border-neutral-300 px-3 py-2">
                                @foreach(['pending','paid','cancelled','expired'] as $status)
                                    <option @selected($order->order_status===$status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-3">
                            <form id="order-update-{{ $order->id }}" method="post" action="{{ route('admin.orders.update',$order) }}" class="hidden">@csrf @method('put')</form>
                            <div class="flex justify-end gap-2">
                                <button form="order-update-{{ $order->id }}" class="fi-btn-primary">Simpan</button>
                                <form method="post" action="{{ route('admin.orders.destroy',$order) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="fi-btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-6 text-center text-neutral-500">Belum ada pemesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $orders->links() }}</div>
</section>
@endsection

