@extends('layouts.app', ['title' => 'Scan Gelang'])
@section('content')
<section class="mx-auto max-w-2xl px-4 py-12">
    <h1 class="text-4xl font-black">Scan Gelang</h1>
    <x-qr-scanner-form
        :action="route('gate.scan.submit')"
        field="wristband_code"
        label="Kode Gelang"
        placeholder="GLG-..."
        button="Validasi Masuk"
    />
</section>
@endsection
