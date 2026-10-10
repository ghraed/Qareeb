@extends('layouts.business')
@section('title', 'Seeker sign in')
@section('content')
<section class="q-card mx-auto max-w-xl space-y-6 p-6 sm:p-8">
    <h1 class="q-heading text-3xl">Seeker sign in</h1>
    <form method="POST" action="{{ route('seeker.login.store') }}" class="space-y-5">
        @csrf
        <x-form-input label="Username or email" name="email" autocomplete="username" required />
        <x-form-input label="Password" name="password" type="password" autocomplete="current-password" required />
        <label class="flex min-h-11 items-center gap-3"><input type="checkbox" name="remember" value="1" @checked(old('remember'))>Remember me</label>
        <button class="q-button-primary w-full" type="submit">Sign in</button>
    </form>
    <a class="q-button-secondary" href="{{ route('seeker.register') }}">Create a Seeker account</a>
    <a class="font-semibold underline" href="{{ route('business.login') }}">Provider sign in</a>
</section>
@endsection
