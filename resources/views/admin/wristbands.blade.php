@extends('layouts.admin', ['title' => 'Kelola Gelang', 'pageTitle' => 'Kelola Gelang'])
@section('content')
<section class="grid gap-6">
    <form method="post" action="{{ route('admin.wristbands.store') }}" class="rounded-lg border bg-white p-5 shadow-sm">
        @csrf
        <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-lg font-black">Tambah Gelang</h2>
            <button class="fi-btn-dark">Tambah</button>
        </div>
        <div class="grid gap-3 md:grid-cols-[1fr_220px]">
            <select name="e_ticket_id" class="rounded-md border border-neutral-300 px-3 py-2" required>
                @foreach($tickets as $ticket)
                    <option value="{{ $ticket->id }}">{{ $ticket->ticket_code }} - {{ $ticket->user->name }} - {{ $ticket->concert->name }}</option>
                @endforeach
            </select>
            <select name="wristband_status" class="rounded-md border border-neutral-300 px-3 py-2" required>
                <option>aktif</option>
                <option>sudah_masuk</option>
                <option>invalid</option>
            </select>
        </div>
    </form>

    <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table class="fi-table min-w-[920px]">
            <thead class="bg-neutral-100 text-xs uppercase tracking-wide text-neutral-600">
                <tr>
                    <th class="p-3">Kode Gelang</th>
                    <th class="p-3">E-Ticket</th>
                    <th class="p-3">User</th>
                    <th class="p-3">Konser</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wristbands as $wristband)
                    <tr class="border-t align-top">
                        <td class="p-3 font-mono">{{ $wristband->wristband_code }}</td>
                        <td class="p-3 font-mono text-neutral-500">{{ $wristband->eTicket->ticket_code }}</td>
                        <td class="p-3">{{ $wristband->eTicket->user->name }}</td>
                        <td class="p-3 font-bold">{{ $wristband->concert->name }}</td>
                        <td class="p-3">
                            <select form="wristband-update-{{ $wristband->id }}" name="wristband_status" class="w-full rounded-md border border-neutral-300 px-3 py-2">
                                @foreach(['aktif','sudah_masuk','invalid'] as $status)
                                    <option @selected($wristband->wristband_status===$status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-3">
                            <form id="wristband-update-{{ $wristband->id }}" method="post" action="{{ route('admin.wristbands.update',$wristband) }}" class="hidden">@csrf @method('put')</form>
                            <div class="flex justify-end gap-2">
                                <button form="wristband-update-{{ $wristband->id }}" class="fi-btn-primary">Simpan</button>
                                <form method="post" action="{{ route('admin.wristbands.destroy',$wristband) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="fi-btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-neutral-500">Belum ada gelang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $wristbands->links() }}</div>
</section>
@endsection

