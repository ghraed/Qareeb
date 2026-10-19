@extends('layouts.business')
@section('title', 'My private complaints')
@section('content')
<section class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="q-eyebrow">Private support</p>
            <h1 class="q-page-title mt-2">My private complaints</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="q-button-primary" href="{{ route('seeker.complaints.create') }}">Submit a complaint</a>
            <a class="q-button-secondary" href="{{ route('seeker.dashboard') }}">Back to dashboard</a>
        </div>
    </div>
    <x-infinite-scroll id="complaints" :records="$complaints" items-class="grid items-start gap-6 md:grid-cols-2">
        @forelse ($complaints as $complaint)
            <article class="q-complaint-paper p-5 sm:p-7">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-qareeb-sand pb-4">
                    <p class="text-xs text-qareeb-stone">Submitted <time datetime="{{ $complaint->created_at->toDateString() }}">{{ $complaint->created_at->format('d M Y') }}</time></p>
                    <x-complaint-status :status="$complaint->status" />
                </div>
                <h2 class="mt-5 font-handwriting text-3xl leading-tight font-semibold break-words text-qareeb-green">
                    <a class="rounded-sm hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-qareeb-green" href="{{ route('seeker.complaints.show', $complaint) }}">{{ $complaint->type }}</a>
                </h2>
                <p class="q-handwritten-note mt-3 break-words">{{ Str::limit($complaint->description, 240) }}</p>
                <a class="mt-5 inline-flex min-h-11 items-center rounded-sm text-sm font-semibold text-qareeb-green underline underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-qareeb-green" href="{{ route('seeker.complaints.show', $complaint) }}" aria-label="Read complaint: {{ $complaint->type }}">Read complaint <span aria-hidden="true" class="ml-2">&rarr;</span></a>
            </article>
        @empty
            <p class="q-panel p-6 md:col-span-2">You have not submitted any complaints.</p>
        @endforelse
    </x-infinite-scroll>
</section>
@endsection
