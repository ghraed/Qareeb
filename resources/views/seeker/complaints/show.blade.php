@extends('layouts.business')
@section('title', 'Private complaint status')
@section('content')
<section class="q-complaint-paper mx-auto max-w-2xl space-y-5 p-6 sm:p-8">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-qareeb-sand pb-4">
        <p class="text-xs text-qareeb-stone">Submitted <time datetime="{{ $complaint->created_at->toDateString() }}">{{ $complaint->created_at->format('d M Y') }}</time></p>
        <x-complaint-status :status="$complaint->status" />
    </div>
    <h1 class="font-handwriting text-4xl leading-tight font-semibold break-words text-qareeb-green">{{ $complaint->type }}</h1>
    <p class="q-handwritten-note whitespace-pre-line">{{ $complaint->description }}</p><p class="q-copy text-sm">Your complaint is private. Administrator notes are not shared.</p>
    <a class="q-button-secondary" href="{{ route('seeker.complaints.index') }}">My complaints</a>
</section>
@endsection
