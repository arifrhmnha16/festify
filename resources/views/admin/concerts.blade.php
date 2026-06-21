@extends('layouts.admin', ['title' => 'Kelola Konser', 'pageTitle' => 'Kelola Konser'])
@section('content')
<section class="grid gap-6">
    <div class="fi-card p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-black">Data Konser</h2>
                <p class="text-sm text-neutral-500">Kelola konser dari halaman create dan edit terpisah.</p>
            </div>
            <a href="{{ route('admin.concerts.create') }}" class="fi-btn-dark">Tambah Konser</a>
        </div>
    </div>

    <div class="fi-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="fi-table min-w-[1100px]">
                <thead>
                    <tr>
                        <th>Poster</th>
                        <th>Konser</th>
                        <th>Artis</th>
                        <th>Venue</th>
                        <th>Jadwal</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Unggulan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($concerts as $concert)
                        <tr>
                            <td>
                                <div class="aspect-[4/1] w-32 overflow-hidden rounded-lg bg-neutral-100">
                                    @if($concert->poster)
                                        <img src="{{ asset('storage/'.$concert->poster) }}" alt="{{ $concert->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="grid h-full place-items-center text-xs font-bold text-neutral-400">No Poster</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <p class="font-black">{{ $concert->name }}</p>
                                <p class="text-xs text-neutral-500">{{ $concert->seat_zone ?: 'Zona belum diisi' }}</p>
                            </td>
                            <td>{{ $concert->artist }}</td>
                            <td>{{ $concert->venue }}</td>
                            <td>
                                <p class="font-bold">{{ $concert->date->format('d M Y') }}</p>
                                <p class="text-xs text-neutral-500">{{ substr($concert->time, 0, 5) }} WIB</p>
                            </td>
                            <td class="font-black">Rp{{ number_format($concert->price,0,',','.') }}</td>
                            <td>{{ $concert->stock }}</td>
                            <td>
                                <span class="{{ $concert->status === 'aktif' ? 'fi-badge-success' : ($concert->status === 'dibatalkan' ? 'fi-badge-danger' : 'fi-badge-neutral') }}">{{ $concert->status }}</span>
                            </td>
                            <td>
                                @if($concert->is_featured)
                                    <span class="fi-badge-warning">Unggulan</span>
                                @else
                                    <form method="post" action="{{ route('admin.concerts.featured',$concert) }}">
                                        @csrf
                                        @method('patch')
                                        <button class="fi-btn-muted">Jadikan</button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.concerts.edit', $concert) }}" class="fi-btn-primary">Edit</a>
                                    <form method="post" action="{{ route('admin.concerts.destroy',$concert) }}">
                                        @csrf
                                        @method('delete')
                                        <button class="fi-btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-neutral-500">Belum ada konser.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div>{{ $concerts->links() }}</div>
</section>
@endsection
