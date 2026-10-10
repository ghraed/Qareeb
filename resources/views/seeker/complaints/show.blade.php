@extends('layouts.business')
@section('title', 'Private complaint status')
@section('content')
<section class="q-card mx-auto max-w-2xl space-y-5 p-6 sm:p-8">
    <h1 class="q-heading text-3xl">{{ $complaint->type }}</h1><p class="q-badge">Status: {{ $complaint->status->label() }}</p>
    <p class="q-copy whitespace-pre-line">{{ $complaint->description }}</p><p class="q-copy text-sm">Your complaint is private. Administrator notes are not shared.</p>
    <a class="q-button-secondary" href="{{ route('seeker.complaints.index') }}">My complaints</a>
</section>
@endsection
