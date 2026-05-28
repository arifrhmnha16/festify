@extends('layouts.app', ['title' => 'Scan E-Ticket'])
@section('content')
<section class="mx-auto max-w-2xl px-4 py-12">
    <h1 class="text-4xl font-black">Scan E-Ticket</h1>
    <x-qr-scanner-form
        :action="route('loket.scan.submit')"
        field="ticket_code"
        label="Kode E-Ticket"
        placeholder="TIX-..."
        button="Validasi & Tukar Gelang"
    />
</section>
@endsection
