@extends('layouts.app', ['title' => 'E-Ticket'])
@section('content')
<section class="mx-auto max-w-7xl px-4 py-12"><h1 class="text-4xl font-black">E-Ticket Saya</h1><div class="mt-6 grid gap-4 md:grid-cols-2">@foreach($tickets as $ticket)<a class="rounded-lg border bg-white p-5" href="{{ route('user.tickets.show',$ticket) }}"><p class="font-mono">{{ $ticket->ticket_code }}</p><h3 class="text-xl font-black">{{ $ticket->concert->name }}</h3><p>{{ $ticket->ticket_status }}</p></a>@endforeach</div><div class="mt-6">{{ $tickets->links() }}</div></section>
@endsection
