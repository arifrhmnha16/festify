@extends('layouts.admin', ['title' => 'Kelola E-Ticket', 'pageTitle' => 'Kelola E-Ticket'])
@section('content')
<section class="grid gap-6">
    <form method="post" action="{{ route('admin.tickets.store') }}" class="rounded-lg border bg-white p-5 shadow-sm">
        @csrf
        <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-lg font-black">Tambah E-Ticket</h2>
            <button class="fi-btn-dark">Tambah</button>
        </div>
        <div class="grid gap-3 md:grid-cols-[1fr_220px]">
            <select name="order_id" class="rounded-md border border-neutral-300 px-3 py-2" required>
                @foreach($orders as $order)
                    <option value="{{ $order->id }}">{{ $order->order_code }} - {{ $order->user->name }} - {{ $order->concert->name }}</option>
                @endforeach
            </select>
            <select name="ticket_status" class="rounded-md border border-neutral-300 px-3 py-2" required>
                <option>belum_ditukar</option>
                <option>sudah_ditukar</option>
                <option>invalid</option>
            </select>
        </div>
    </form>

    <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table class="fi-table min-w-[920px]">
            <thead class="bg-neutral-100 text-xs uppercase tracking-wide text-neutral-600">
                <tr>
                    <th class="p-3">Kode</th>
                    <th class="p-3">User</th>
                    <th class="p-3">Konser</th>
                    <th class="p-3">Order</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr class="border-t align-top">
                        <td class="p-3 font-mono">{{ $ticket->ticket_code }}</td>
                        <td class="p-3">{{ $ticket->user->name }}</td>
                        <td class="p-3 font-bold">{{ $ticket->concert->name }}</td>
                        <td class="p-3 font-mono text-neutral-500">{{ $ticket->order->order_code }}</td>
                        <td class="p-3">
                            <select form="ticket-update-{{ $ticket->id }}" name="ticket_status" class="w-full rounded-md border border-neutral-300 px-3 py-2">
                                @foreach(['belum_ditukar','sudah_ditukar','invalid'] as $status)
                                    <option @selected($ticket->ticket_status===$status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-3">
                            <form id="ticket-update-{{ $ticket->id }}" method="post" action="{{ route('admin.tickets.update',$ticket) }}" class="hidden">@csrf @method('put')</form>
                            <div class="flex justify-end gap-2">
                                <button form="ticket-update-{{ $ticket->id }}" class="fi-btn-primary">Simpan</button>
                                <form method="post" action="{{ route('admin.tickets.destroy',$ticket) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="fi-btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-neutral-500">Belum ada E-Ticket.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $tickets->links() }}</div>
</section>
@endsection

