@extends('layouts.admin', ['title' => 'Banner Utama', 'pageTitle' => 'Banner Utama'])
@section('content')
<form method="post" action="{{ route('admin.banner.update') }}" enctype="multipart/form-data" class="grid gap-6">
    @csrf
    @method('put')

    <section class="fi-card p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-black">Atur 3 Slide Banner Beranda</h2>
                <p class="mt-1 text-sm text-neutral-500">Setiap slide bisa otomatis, upload gambar sendiri, atau mengambil banner dari konser aktif.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('home') }}" class="fi-btn-muted">Lihat Beranda</a>
                <button class="fi-btn-dark">Simpan Banner</button>
            </div>
        </div>
    </section>

    <section class="grid gap-5">
        @foreach($settings['slots'] as $position => $slot)
            @php($selectedConcert = $slot['concert_id'] ? $concerts->firstWhere('id', $slot['concert_id']) : null)
            <div class="fi-card overflow-hidden">
                <div class="border-b border-neutral-200 bg-neutral-50 px-5 py-4">
                    <h3 class="font-black">Slide {{ $position }}</h3>
                </div>

                <div class="grid gap-5 p-5 lg:grid-cols-[1fr_18rem]">
                    <div class="grid gap-5">
                        <div class="grid gap-3 md:grid-cols-3">
                            @foreach([
                                'auto' => ['title' => 'Otomatis', 'desc' => 'Ambil konser aktif berikutnya.'],
                                'custom' => ['title' => 'Upload Sendiri', 'desc' => 'Gunakan gambar banner khusus.'],
                                'concert' => ['title' => 'Dari Konser', 'desc' => 'Pilih satu konser aktif.'],
                            ] as $value => $option)
                                <label class="cursor-pointer rounded-xl border p-4 transition has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                                    <input type="radio" name="slots[{{ $position }}][source]" value="{{ $value }}" class="sr-only" {{ old("slots.{$position}.source", $slot['source']) === $value ? 'checked' : '' }}>
                                    <span class="block text-sm font-black text-neutral-950">{{ $option['title'] }}</span>
                                    <span class="mt-1 block text-xs leading-5 text-neutral-500">{{ $option['desc'] }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="grid gap-2">
                                <span class="text-sm font-black">Gambar Banner</span>
                                <input type="file" name="slots[{{ $position }}][banner]" accept="image/*" class="fi-field">
                                <span class="text-xs font-semibold text-neutral-500">Rasio wajib 4:1, maksimal 10 MB.</span>
                            </label>

                            <label class="grid gap-2">
                                <span class="text-sm font-black">Pilih Konser</span>
                                <select name="slots[{{ $position }}][concert_id]" class="fi-field">
                                    <option value="">Pilih konser aktif</option>
                                    @foreach($concerts as $concert)
                                        <option value="{{ $concert->id }}" {{ (string) old("slots.{$position}.concert_id", $slot['concert_id']) === (string) $concert->id ? 'selected' : '' }}>
                                            {{ $concert->name }} - {{ $concert->artist }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="grid gap-2">
                                <span class="text-sm font-black">Judul Custom</span>
                                <input name="slots[{{ $position }}][title]" value="{{ old("slots.{$position}.title", $slot['title']) }}" class="fi-field" maxlength="150" placeholder="Festify">
                            </label>
                            <label class="grid gap-2">
                                <span class="text-sm font-black">Link Custom</span>
                                <input name="slots[{{ $position }}][link]" value="{{ old("slots.{$position}.link", $slot['link']) }}" class="fi-field" maxlength="2048" placeholder="{{ route('concerts.index') }}">
                            </label>
                        </div>
                    </div>

                    <aside>
                        <p class="text-sm font-black">Preview</p>
                        <div class="mt-2 aspect-[4/1] overflow-hidden rounded-lg bg-neutral-950">
                            @if($slot['source'] === 'custom' && $slot['image_url'])
                                <img src="{{ $slot['image_url'] }}" alt="{{ $slot['title'] ?: 'Banner utama' }}" class="h-full w-full object-cover">
                            @elseif($slot['source'] === 'concert' && $selectedConcert)
                                <x-concert-poster :concert="$selectedConcert" class="h-full w-full object-cover" />
                            @else
                                <div class="grid h-full place-items-center bg-[linear-gradient(135deg,#101322,#2c1f4f_48%,#da2b0d)] px-4 text-center text-xs font-black text-white">
                                    Otomatis
                                </div>
                            @endif
                        </div>
                        <p class="mt-3 text-xs leading-5 text-neutral-500">Slide ini akan tetap muncul sebagai salah satu dari 3 banner di halaman utama.</p>
                    </aside>
                </div>
            </div>
        @endforeach
    </section>
</form>
@endsection
