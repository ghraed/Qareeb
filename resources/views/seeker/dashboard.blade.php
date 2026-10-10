@extends('layouts.business')
@section('title', 'Seeker dashboard')
@section('content')
<div class="min-w-0 space-y-6">
    <section class="q-card min-w-0 p-6 sm:p-8">
        <div class="flex min-w-0 flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0"><p class="q-eyebrow">Seeker dashboard</p><h1 class="q-heading mt-3 break-words text-3xl sm:text-4xl">Welcome, {{ $user->name }}.</h1><p class="q-copy mt-3">Discover approved providers and contact them directly.</p></div>
            <form method="POST" action="{{ route('seeker.logout') }}">@csrf<button class="q-button-secondary" type="submit">Logout</button></form>
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
        <section class="q-panel space-y-4 p-6"><h2 class="q-heading text-2xl">Share a concern privately</h2><p class="q-copy">Submit a structured complaint for administrator review and follow its status.</p><div class="flex flex-wrap gap-3"><a class="q-button-secondary" href="{{ route('seeker.complaints.create') }}">Submit a private complaint</a><a class="q-button-secondary" href="{{ route('seeker.complaints.index') }}">My complaints</a></div></section>
    </div>
</div>
@endsection
