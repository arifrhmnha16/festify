@extends('layouts.admin', ['title' => 'Tambah Konser', 'pageTitle' => 'Tambah Konser'])
@section('content')
<section class="mx-auto grid max-w-5xl gap-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-black">Data Konser Baru</h2>
            <p class="text-sm text-neutral-500">Isi informasi konser, poster, harga, dan stok tiket.</p>
        </div>
        <a href="{{ route('admin.concerts') }}" class="fi-btn-muted">Kembali</a>
    </div>

    <form method="post" enctype="multipart/form-data" action="{{ route('admin.concerts.store') }}" class="fi-card p-6">
        @csrf
        <div class="grid gap-5 lg:grid-cols-[1fr_320px]">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-bold">Nama konser</label>
                    <input name="name" value="{{ old('name') }}" class="fi-field mt-2 w-full" required>
                </div>
                <div>
                    <label class="text-sm font-bold">Artis</label>
                    <input name="artist" value="{{ old('artist') }}" class="fi-field mt-2 w-full" required>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-bold">Venue</label>
                    <input name="venue" value="{{ old('venue') }}" class="fi-field mt-2 w-full" required>
                </div>
                <div>
                    <label class="text-sm font-bold">Tanggal</label>
                    <input type="date" name="date" value="{{ old('date') }}" class="fi-field mt-2 w-full" required>
                </div>
                <div>
                    <label class="text-sm font-bold">Jam</label>
                    <input type="time" name="time" value="{{ old('time') }}" class="fi-field mt-2 w-full" required>
                </div>
                <div>
                    <label class="text-sm font-bold">Harga dasar</label>
                    <input type="number" name="price" value="{{ old('price') }}" min="0" class="fi-field mt-2 w-full" required>
                </div>
                <div>
                    <label class="text-sm font-bold">Stok</label>
                    <input type="number" name="stock" value="{{ old('stock') }}" min="0" class="fi-field mt-2 w-full" required>
                </div>
                <div>
                    <label class="text-sm font-bold">Status</label>
                    <select name="status" class="fi-field mt-2 w-full" required>
                        @foreach(['aktif','selesai','dibatalkan'] as $status)
                            <option @selected(old('status', 'aktif') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-bold">Deskripsi</label>
                    <textarea name="description" rows="6" class="fi-field mt-2 w-full">{{ old('description') }}</textarea>
                </div>
            </div>

            <aside class="grid gap-4">
                <div class="rounded-xl border border-dashed border-neutral-300 bg-neutral-50 p-5">
                    <label class="text-sm font-bold">Poster konser</label>
                    <input type="file" name="poster" accept="image/*" class="fi-field mt-3 w-full">
                    <p class="mt-2 text-xs text-neutral-500">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
                </div>
                <div class="rounded-xl border border-orange-200 bg-orange-50 p-4 text-sm text-orange-900">
                    Zona utama otomatis: <strong>Festival Tengah</strong>. Harga zona dibuat otomatis dari harga dasar.
                </div>
            </aside>
        </div>

        <div class="mt-6 border-t border-neutral-100 pt-6">
            <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h3 class="text-lg font-black">Zona Otomatis</h3>
                    <p class="text-sm text-neutral-500">Sistem otomatis membuat harga zona dari harga dasar.</p>
                </div>
                <span class="fi-badge-neutral">Depan +10.000, Tengah +5.000</span>
            </div>
            <div class="overflow-x-auto rounded-xl border border-neutral-200">
                <table class="fi-table min-w-[820px]">
                    <thead>
                        <tr>
                            <th>Zona</th>
                            <th>Rumus harga</th>
                            <th>Pembagian stok</th>
                            <th>Warna</th>
                            <th>Urutan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="font-bold">Festival Depan</td><td>Harga dasar + Rp10.000</td><td>30% stok</td><td><span class="inline-block h-5 w-5 rounded-full bg-red-600"></span></td><td>1</td></tr>
                        <tr><td class="font-bold">Festival Tengah</td><td>Harga dasar + Rp5.000</td><td>45% stok</td><td><span class="inline-block h-5 w-5 rounded-full bg-orange-600"></span></td><td>2</td></tr>
                        <tr><td class="font-bold">Tribune Belakang</td><td>Harga dasar</td><td>25% stok</td><td><span class="inline-block h-5 w-5 rounded-full bg-yellow-600"></span></td><td>3</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2 border-t border-neutral-100 pt-5">
            <a href="{{ route('admin.concerts') }}" class="fi-btn-muted">Batal</a>
            <button class="fi-btn-dark">Simpan Konser</button>
        </div>
    </form>
</section>
@endsection
