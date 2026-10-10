@extends('layouts.business')
@section('title', 'Create a Seeker account')
@section('content')
<section class="q-card mx-auto max-w-xl space-y-6 p-6 sm:p-8">
    <div><p class="q-eyebrow">Seeker registration</p><h1 class="q-heading mt-3 text-3xl">Find trusted help nearby.</h1><p class="q-copy mt-3">Create an account to discover providers and contact them directly.</p></div>
    <form method="POST" action="{{ route('seeker.register.store') }}" class="space-y-5">
        @csrf
        <x-form-input label="Full name" name="name" autocomplete="name" required />
        <x-form-input label="Username (required without email)" name="username" autocomplete="username" />
        <p class="q-copy text-sm">Use letters, numbers, dots, underscores or hyphens. You can sign in with your username or email.</p>
        <x-form-input label="Email (optional)" name="email" type="email" autocomplete="email" />
        <x-form-input label="Password" name="password" type="password" autocomplete="new-password" required />
        <x-form-input label="Confirm password" name="password_confirmation" type="password" autocomplete="new-password" required />
        <p class="q-copy text-sm">Password recovery is not currently available.</p>
        <x-terms-acceptance />
        <button class="q-button-primary w-full" type="submit">Create Seeker account</button>
    </form>
    <p class="q-copy">Already registered? <a class="font-semibold underline" href="{{ route('seeker.login') }}">Seeker sign in</a>.</p>
    <a class="q-button-secondary" href="{{ route('business.register') }}">Register as a provider</a>
</section>
@endsection
