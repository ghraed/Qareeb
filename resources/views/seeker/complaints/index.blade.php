@extends('layouts.business')
@section('title', 'My private complaints')
@section('content')
<section class="space-y-5">
    <h1 class="q-page-title">My private complaints</h1><a class="q-button-primary" href="{{ route('seeker.complaints.create') }}">Submit a complaint</a><a class="q-button-secondary" href="{{ route('seeker.dashboard') }}">Back to dashboard</a>
    <x-infinite-scroll id="complaints" :records="$complaints">
    @forelse ($complaints as $complaint)<article class="q-card p-6"><h2 class="q-heading text-xl"><a href="{{ route('seeker.complaints.show', $complaint) }}">{{ $complaint->type }}</a></h2><p class="q-copy mt-3">Status: {{ $complaint->status->label() }}</p><p class="q-copy mt-2">Submitted {{ $complaint->created_at->format('d M Y') }}</p></article>@empty<p class="q-panel p-6">You have not submitted any complaints.</p>@endforelse
    </x-infinite-scroll>
</section>
@endsection
