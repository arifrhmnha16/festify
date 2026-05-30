@extends('layouts.admin', ['title' => 'Edit Konser', 'pageTitle' => 'Edit Konser'])
@section('content')
<section class="mx-auto grid max-w-5xl gap-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-orange-700">#{{ $concert->id }}</p>
            <h2 class="text-xl font-black">{{ $concert->name }}</h2>
        </div>
        <a href="{{ route('admin.concerts') }}" class="fi-btn-muted">Kembali</a>
    </div>

    <form method="post" enctype="multipart/form-data" action="{{ route('admin.concerts.update', $concert) }}" class="fi-card p-6">
        @csrf
        @method('put')
        <div class="grid gap-5 lg:grid-cols-[1fr_320px]">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-bold">Nama konser</label>
                    <input name="name" value="{{ old('name', $concert->name) }}" class="fi-field mt-2 w-full" required>
                </div>
                <div>
                    <label class="text-sm font-bold">Artis</label>
                    <input name="artist" value="{{ old('artist', $concert->artist) }}" class="fi-field mt-2 w-full" required>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-bold">Venue</label>
                    <input name="venue" value="{{ old('venue', $concert->venue) }}" class="fi-field mt-2 w-full" required>
                </div>
                <div>
                    <label class="text-sm font-bold">Tanggal</label>
                    <input type="date" name="date" value="{{ old('date', $concert->date->toDateString()) }}" class="fi-field mt-2 w-full" required>
                </div>
                <div>
                    <label class="text-sm font-bold">Jam</label>
                    <input type="time" name="time" value="{{ old('time', substr($concert->time, 0, 5)) }}" class="fi-field mt-2 w-full" required>
                </div>
                <div>
                    <label class="text-sm font-bold">Harga dasar</label>
                    <input type="number" name="price" value="{{ old('price', $concert->price) }}" min="0" class="fi-field mt-2 w-full" required>
                </div>
                <div>
                    <label class="text-sm font-bold">Stok</label>
                    <input type="number" name="stock" value="{{ old('stock', $concert->stock) }}" min="0" class="fi-field mt-2 w-full" required>
                </div>
                <div>
                    <label class="text-sm font-bold">Status</label>
                    <select name="status" class="fi-field mt-2 w-full" required>
                        @foreach(['aktif','selesai','dibatalkan'] as $status)
                            <option @selected(old('status', $concert->status) === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-bold">Deskripsi</label>
                    <textarea name="description" rows="6" class="fi-field mt-2 w-full">{{ old('description', $concert->description) }}</textarea>
                </div>
            </div>

            <aside class="grid gap-4">
                <div class="overflow-hidden rounded-xl border border-neutral-200 bg-neutral-100">
                    @if($concert->poster)
                        <img src="{{ asset('storage/'.$concert->poster) }}" alt="{{ $concert->name }}" class="aspect-[4/3] w-full object-cover">
                    @else
                        <div class="grid aspect-[4/3] place-items-center text-sm font-bold text-neutral-400">Belum ada poster</div>
                    @endif
                </div>
                <div class="rounded-xl border border-dashed border-neutral-300 bg-neutral-50 p-5">
                    <label class="text-sm font-bold">Ganti poster</label>
                    <input type="file" name="poster" accept="image/*" class="fi-field mt-3 w-full">
                    <p class="mt-2 text-xs text-neutral-500">Kosongkan jika poster tidak ingin diganti.</p>
                </div>
            </aside>
        </div>

        <div class="mt-6 flex justify-between gap-2 border-t border-neutral-100 pt-5">
            <a href="{{ route('concerts.show', $concert) }}" target="_blank" class="fi-btn-muted">Lihat Publik</a>
            <div class="flex gap-2">
                <a href="{{ route('admin.concerts') }}" class="fi-btn-muted">Batal</a>
                <button class="fi-btn-dark">Simpan Perubahan</button>
            </div>
        </div>
    </form>

    <div class="fi-card overflow-hidden">
        <div class="border-b border-neutral-100 px-5 py-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-black">Harga per Zona Otomatis</h3>
                    <p class="text-sm text-neutral-500">Zona diperbarui otomatis saat harga dasar atau stok total disimpan.</p>
                </div>
                <span class="fi-badge-neutral">Zona utama: {{ $concert->seat_zone }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="fi-table min-w-[720px]">
                <thead>
                    <tr>
                        <th>Zona</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Warna</th>
                        <th>Urutan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($concert->ticketZones as $zone)
                        <tr>
                            <td class="font-bold">{{ $zone->name }}</td>
                            <td class="font-black">Rp{{ number_format($zone->price,0,',','.') }}</td>
                            <td>{{ $zone->stock }}</td>
                            <td><span class="inline-block h-5 w-5 rounded-full" style="background: {{ $zone->color }}"></span></td>
                            <td>{{ $zone->position }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-neutral-500">Belum ada zona tiket.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
