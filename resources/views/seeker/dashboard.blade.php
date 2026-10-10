@extends('layouts.business')
@section('title', 'Seeker dashboard')
@section('content')
<section class="q-card space-y-5 p-6 sm:p-8">
    <p class="q-eyebrow">Seeker dashboard</p><h1 class="q-heading text-3xl">Welcome, {{ $user->name }}.</h1>
    <p class="q-copy">Your Seeker account is ready.</p>
    <a class="q-button-primary" href="{{ route('seeker.profile.edit') }}">Edit private profile</a>
    <x-profile-completion :profile="$user" :seeker="true" />
    <form method="POST" action="{{ route('seeker.logout') }}">@csrf<button class="q-button-secondary" type="submit">Logout</button></form>
</section>
@endsection
