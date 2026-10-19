@extends('layouts.business')
@section('title', 'Seeker dashboard')
@section('content')
<div class="min-w-0 space-y-6">
    <section class="q-card min-w-0 p-6 sm:p-8">
        <div class="flex min-w-0 flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0"><p class="q-eyebrow">Seeker dashboard</p><h1 class="q-page-title mt-3">Welcome, {{ $user->name }}.</h1><p class="q-copy mt-3">Discover approved providers and contact them directly.</p></div>
        </div>
    </section>
    <section class="q-card space-y-4 p-6 sm:p-8">
        <h2 class="q-heading text-2xl">Your private profile</h2>
        <p class="q-copy">Complete your name, phone, district and city. Your contact and location stay private.</p>
        <x-profile-completion :profile="$user" :seeker="true" />
        <a class="q-button-primary" href="{{ route('seeker.profile.edit') }}">Edit private profile</a>
    </section>
    <div class="grid gap-5 md:grid-cols-2">
        <section class="q-panel space-y-4 p-6"><h2 class="q-heading text-2xl">Find the right provider</h2><p class="q-copy">Search by service category and service area, read public reviews, then contact the provider directly.</p><a class="q-button-primary" href="{{ route('providers.index') }}">Find a provider</a></section>
        <section aria-labelledby="private-concern-heading" class="flex min-w-0 flex-col rounded-[1.75rem] border border-qareeb-green/15 bg-qareeb-cream p-6 sm:p-7">
            <div class="flex items-center gap-3">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-2xl border border-qareeb-olive/20 bg-white text-qareeb-green">
                    <svg aria-hidden="true" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3 4 6v5c0 5 3.5 8.5 8 10 4.5-1.5 8-5 8-10V6l-8-3Z" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                </span>
                <p class="text-xs font-semibold tracking-[0.14em] text-qareeb-green uppercase">Private support</p>
            </div>

            <h2 id="private-concern-heading" class="q-heading mt-5 text-2xl leading-tight">Share a concern privately</h2>
            <p class="q-copy mt-3 text-sm leading-7">Tell us what happened so our administrators can review your concern. You can follow its status from your dashboard.</p>

            <div class="mt-5 flex items-start gap-2.5 rounded-xl bg-white/70 px-4 py-3">
                <svg aria-hidden="true" class="mt-0.5 size-4 shrink-0 text-qareeb-green" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="10" width="14" height="11" rx="2" />
                    <path d="M8 10V7a4 4 0 0 1 8 0v3m-4 5v2" />
                </svg>
                <p class="text-sm leading-6 text-qareeb-green">Your complaint goes to administrators for review and stays separate from public reviews.</p>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-3 border-t border-qareeb-green/10 pt-5">
                <a class="q-button-primary w-full sm:w-auto" href="{{ route('seeker.complaints.create') }}">Submit a private complaint</a>
                <a class="inline-flex min-h-11 items-center gap-2 rounded-md text-sm font-semibold text-qareeb-green underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-qareeb-green" href="{{ route('seeker.complaints.index') }}">
                    My complaints
                    <svg aria-hidden="true" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6" /></svg>
                </a>
            </div>
        </section>
    </div>
</div>
@endsection
