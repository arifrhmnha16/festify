@extends('layouts.admin', ['title' => 'Laporan', 'pageTitle' => 'Laporan'])
@section('content')
<section class="grid gap-6">
    <div class="fi-card overflow-hidden">
        <div class="border-b border-neutral-100 px-5 py-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-black">Riwayat Scan</h2>
                    <p class="text-sm text-neutral-500">Log validasi E-Ticket dan gelang dari petugas loket/gate.</p>
                </div>
                <a href="{{ route('admin.reports.export') }}" class="fi-btn-dark">Export CSV</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="fi-table min-w-[920px]">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Petugas</th>
                        <th>Tipe</th>
                        <th>Hasil</th>
                        <th>Pesan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $history)
                        <tr>
                            <td>{{ $history->scanned_at->format('d M Y H:i') }}</td>
                            <td class="font-bold">{{ $history->officer->name }}</td>
                            <td>{{ $history->scan_type }}</td>
                            <td><span class="{{ $history->scan_result === 'berhasil' ? 'fi-badge-success' : 'fi-badge-danger' }}">{{ $history->scan_result }}</span></td>
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
                        <tr><td colspan="6" class="text-center text-neutral-500">Belum ada riwayat scan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div>{{ $histories->links() }}</div>
</section>
@endsection

