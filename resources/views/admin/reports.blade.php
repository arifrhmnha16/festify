@extends('layouts.admin', ['title' => 'Laporan', 'pageTitle' => 'Laporan'])
@section('content')
<section class="grid gap-6">
    <div class="grid gap-3 md:grid-cols-3">
        <div class="rounded-lg border bg-white p-4 shadow-sm"><p class="text-sm font-bold text-neutral-500">Total scan</p><p class="text-3xl font-black">{{ $summary['total'] }}</p></div>
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800"><p class="text-sm font-bold">Berhasil</p><p class="text-3xl font-black">{{ $summary['berhasil'] }}</p></div>
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800"><p class="text-sm font-bold">Gagal</p><p class="text-3xl font-black">{{ $summary['gagal'] }}</p></div>
    </div>

    <form method="get" action="{{ route('admin.reports') }}" class="fi-card p-5">
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
            <div>
                <label class="text-xs font-bold uppercase text-neutral-500">Dari</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="fi-field mt-1 w-full">
            </div>
            <div>
                <label class="text-xs font-bold uppercase text-neutral-500">Sampai</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="fi-field mt-1 w-full">
            </div>
            <div>
                <label class="text-xs font-bold uppercase text-neutral-500">Tipe</label>
                <select name="scan_type" class="fi-field mt-1 w-full">
                    <option value="">Semua</option>
                    <option value="scan_eticket" @selected(($filters['scan_type'] ?? '') === 'scan_eticket')>E-Ticket</option>
                    <option value="scan_gelang" @selected(($filters['scan_type'] ?? '') === 'scan_gelang')>Gelang</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-bold uppercase text-neutral-500">Hasil</label>
                <select name="scan_result" class="fi-field mt-1 w-full">
                    <option value="">Semua</option>
                    <option value="berhasil" @selected(($filters['scan_result'] ?? '') === 'berhasil')>Berhasil</option>
                    <option value="gagal" @selected(($filters['scan_result'] ?? '') === 'gagal')>Gagal</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-bold uppercase text-neutral-500">Petugas</label>
                <select name="officer_id" class="fi-field mt-1 w-full">
                    <option value="">Semua</option>
                    @foreach($officers as $officer)
                        <option value="{{ $officer->id }}" @selected((string) ($filters['officer_id'] ?? '') === (string) $officer->id)>{{ $officer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="fi-btn-dark flex-1">Filter</button>
                <a href="{{ route('admin.reports') }}" class="fi-btn-muted">Reset</a>
            </div>
        </div>
    </form>

    <div class="fi-card overflow-hidden">
        <div class="border-b border-neutral-100 px-5 py-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-black">Riwayat Scan</h2>
                    <p class="text-sm text-neutral-500">Log validasi E-Ticket dan gelang dari petugas loket/gate.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.reports.export', request()->query() + ['format' => 'csv']) }}" class="fi-btn-muted">CSV</a>
                    <a href="{{ route('admin.reports.export', request()->query() + ['format' => 'xls']) }}" class="fi-btn-muted">Excel</a>
                    <a href="{{ route('admin.reports.export', request()->query() + ['format' => 'pdf']) }}" class="fi-btn-dark">PDF</a>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="fi-table min-w-[1100px]">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Petugas</th>
                        <th>Tipe</th>
                        <th>Hasil</th>
                        <th>Kode</th>
                        <th>User</th>
                        <th>Pesan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $history)
                        <tr>
                            <td>{{ $history->scanned_at->format('d M Y H:i') }}</td>
                            <td class="font-bold">{{ $history->officer?->name ?? '-' }}</td>
                            <td>{{ $history->scan_type }}</td>
                            <td><span class="{{ $history->scan_result === 'berhasil' ? 'fi-badge-success' : 'fi-badge-danger' }}">{{ $history->scan_result }}</span></td>
                            <td class="font-mono text-xs">{{ $history->eTicket?->ticket_code ?? $history->wristband?->wristband_code ?? '-' }}</td>
                            <td>{{ $history->eTicket?->user?->name ?? $history->wristband?->eTicket?->user?->name ?? '-' }}</td>
                            <td>{{ $history->message }}</td>
                            <td>
                                <form method="post" action="{{ route('admin.reports.destroy',$history) }}" class="flex justify-end">
                                    @csrf
                                    @method('delete')
                                    <button class="fi-btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-neutral-500">Belum ada riwayat scan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div>{{ $histories->links() }}</div>
</section>
@endsection

