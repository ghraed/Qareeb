@extends('layouts.business')
@section('title', 'Seeker sign in')
@section('content')
<section class="q-card mx-auto max-w-xl space-y-5 p-5 sm:p-6">
    <div class="space-y-3"><p class="q-eyebrow">Seeker sign in</p><h1 class="q-page-title">Welcome back.</h1><p class="q-copy text-sm leading-6">Sign in to find trusted providers and keep your details in one place.</p></div>
    <form method="POST" action="{{ route('seeker.login.store') }}" class="space-y-4">
        @csrf
        <x-form-input label="Username or email" name="email" autocomplete="username" required />
        <x-form-input label="Password" name="password" type="password" autocomplete="current-password" required />
        <label class="flex min-h-11 items-center gap-3"><input type="checkbox" name="remember" value="1" @checked(old('remember'))>Remember me</label>
        <button class="q-button-primary w-full" type="submit">Sign in</button>
    </form>
    <div class="space-y-3 border-t border-qareeb-sand pt-4 text-center text-sm">
        <p class="q-copy">New to Qareeb? <a class="font-semibold text-qareeb-green underline underline-offset-4" href="{{ route('seeker.register') }}">Create a Seeker account</a></p>
        <a class="inline-flex min-h-11 items-center font-semibold text-qareeb-green underline underline-offset-4" href="{{ route('business.login') }}">Provider sign in</a>
    </div>
</section>
@endsection
