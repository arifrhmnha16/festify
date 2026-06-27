@extends('layouts.admin', ['title' => 'Banner Utama', 'pageTitle' => 'Banner Utama'])
@section('content')
<section class="grid gap-6 lg:grid-cols-[1fr_22rem]">
    <form method="post" action="{{ route('admin.banner.update') }}" enctype="multipart/form-data" class="fi-card p-5">
        @csrf
        @method('put')

        <div>
            <h2 class="text-lg font-black">Atur Banner Beranda</h2>
            <p class="mt-1 text-sm text-neutral-500">Pilih banner utama dari upload admin, poster konser, atau mode otomatis.</p>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-3">
            @foreach([
                'auto' => ['title' => 'Otomatis', 'desc' => 'Mengikuti konser unggulan dan konser aktif.'],
                'custom' => ['title' => 'Upload Sendiri', 'desc' => 'Gunakan gambar banner khusus.'],
                'concert' => ['title' => 'Dari Konser', 'desc' => 'Ambil banner dari poster konser.'],
            ] as $value => $option)
                <label class="cursor-pointer rounded-xl border p-4 transition has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                    <input type="radio" name="source" value="{{ $value }}" class="sr-only" {{ old('source', $settings['source']) === $value ? 'checked' : '' }}>
                    <span class="block text-sm font-black text-neutral-950">{{ $option['title'] }}</span>
                    <span class="mt-1 block text-xs leading-5 text-neutral-500">{{ $option['desc'] }}</span>
                </label>
            @endforeach
        </div>

        <div class="mt-6 grid gap-5">
            <label class="grid gap-2">
                <span class="text-sm font-black">Gambar Banner</span>
                <input type="file" name="banner" accept="image/*" class="fi-field">
                <span class="text-xs font-semibold text-neutral-500">Rasio wajib 4:1, maksimal 10 MB. Contoh: 1920 x 480 px.</span>
            </label>

            <label class="grid gap-2">
                <span class="text-sm font-black">Pilih Konser</span>
                <select name="concert_id" class="fi-field">
                    <option value="">Pilih konser aktif</option>
                    @foreach($concerts as $concert)
                        <option value="{{ $concert->id }}" {{ (string) old('concert_id', $settings['concert_id']) === (string) $concert->id ? 'selected' : '' }}>
                            {{ $concert->name }} - {{ $concert->artist }}
                        </option>
                    @endforeach
                </select>
            </label>

            <div class="grid gap-4 md:grid-cols-2">
                <label class="grid gap-2">
                    <span class="text-sm font-black">Judul Custom</span>
                    <input name="title" value="{{ old('title', $settings['title']) }}" class="fi-field" maxlength="150" placeholder="Festify">
                </label>
                <label class="grid gap-2">
                    <span class="text-sm font-black">Link Custom</span>
                    <input name="link" value="{{ old('link', $settings['link']) }}" class="fi-field" maxlength="2048" placeholder="{{ route('concerts.index') }}">
                </label>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <button class="fi-btn-dark">Simpan Banner</button>
            <a href="{{ route('home') }}" class="fi-btn-muted">Lihat Beranda</a>
        </div>
    </form>

    <aside class="fi-card p-5">
        <h2 class="text-lg font-black">Preview Saat Ini</h2>
        <p class="mt-1 text-sm text-neutral-500">Mode aktif: <span class="font-black text-neutral-950">{{ ucfirst($settings['source']) }}</span></p>

        <div class="mt-5 aspect-[4/1] overflow-hidden rounded-lg bg-neutral-950">
            @if($settings['source'] === 'custom' && $settings['image_url'])
                <img src="{{ $settings['image_url'] }}" alt="{{ $settings['title'] ?: 'Banner utama' }}" class="h-full w-full object-cover">
            @elseif($settings['source'] === 'concert' && $settings['concert_id'] && ($selectedConcert = $concerts->firstWhere('id', $settings['concert_id'])))
                <x-concert-poster :concert="$selectedConcert" class="h-full w-full object-cover" />
            @else
                <div class="grid h-full place-items-center bg-[linear-gradient(135deg,#101322,#2c1f4f_48%,#da2b0d)] px-4 text-center text-sm font-black text-white">
                    Banner otomatis konser
                </div>
            @endif
        </div>

        <div class="mt-5 rounded-xl border border-neutral-200 bg-neutral-50 p-4 text-sm text-neutral-600">
            <p class="font-bold text-neutral-950">Catatan</p>
            <p class="mt-1 leading-6">Mode upload memakai gambar custom sebagai banner tunggal. Mode konser memakai poster konser yang dipilih. Mode otomatis kembali ke slider konser seperti sebelumnya.</p>
        </div>
    </aside>
</section>
@endsection
