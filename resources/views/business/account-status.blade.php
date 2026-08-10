@extends('layouts.business')

@section('title', 'Provider Account Status')

@section('content')
    <div class="mx-auto max-w-5xl space-y-8">
        <section class="q-card p-6 sm:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="q-eyebrow">{{ $content['eyebrow'] }}</p>
                    <h1 class="mt-3 font-serif text-4xl font-semibold tracking-tight text-[#1f3b2e] sm:text-5xl">{{ $content['title'] }}</h1>
                    <p class="mt-4 max-w-3xl text-base leading-7 text-[#6b6b6b]">{{ $content['description'] }}</p>
                </div>

                <form method="POST" action="{{ route('business.logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-[1.15rem] border border-[#eadfc7] bg-[#fff7ef] px-5 py-3 text-sm font-semibold text-[#8f5b28] transition hover:bg-[#f7ecdb]">Logout</button>
                </form>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="q-card p-6 sm:p-8">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="q-badge">{{ $user->name }}</span>
                    <span class="rounded-full px-4 py-2 text-sm font-semibold {{ $content['badgeClass'] }}">{{ $user->account_status->label() }}</span>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="q-panel p-5">
                        <p class="text-sm font-medium text-[#6b6b6b]">Account email</p>
                        <p class="mt-2 text-xl font-semibold text-[#1f3b2e]">{{ $user->email }}</p>
                    </div>
                    <div class="rounded-[1.75rem] border p-5 {{ $content['panelClass'] }}">
                        <p class="text-sm font-medium text-[#6b6b6b]">Current access</p>
                        <p class="mt-2 text-xl font-semibold text-[#1f3b2e]">Dashboard and profile tools are unavailable until the account becomes active.</p>
                    </div>
                    @if ($content['reasonLabel'] && $content['reason'])
                        <div class="q-panel p-5">
                            <p class="text-sm font-medium text-[#6b6b6b]">{{ $content['reasonLabel'] }}</p>
                            <p class="mt-2 text-base leading-7 text-[#1f3b2e]">{{ $content['reason'] }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="overflow-hidden rounded-[2rem] bg-[#1f3b2e] p-8 text-white shadow-[0_35px_80px_-30px_rgba(31,59,46,0.95)]">
                    <p class="text-sm font-semibold tracking-[0.25em] text-[#d9e4d0] uppercase">What happens next</p>
                    <h2 class="mt-4 font-serif text-3xl font-semibold tracking-tight">The provider portal unlocks automatically once the business account is active.</h2>
                    <p class="mt-4 text-base leading-7 text-[#ecf1e7]">After approval, the same login session can continue into the business dashboard without creating a new account.</p>
                </div>

                <div class="q-panel p-6">
                    <h2 class="font-serif text-2xl font-semibold text-[#1f3b2e]">Access policy</h2>
                    <div class="mt-4 space-y-3">
                        <div class="rounded-[1.25rem] bg-white/75 p-4">
                            <p class="font-semibold text-[#1f3b2e]">Pending</p>
                            <p class="mt-1 text-sm text-[#6b6b6b]">Waiting for administrator review.</p>
                        </div>
                        <div class="rounded-[1.25rem] bg-white/75 p-4">
                            <p class="font-semibold text-[#1f3b2e]">Active</p>
                            <p class="mt-1 text-sm text-[#6b6b6b]">Full provider dashboard and profile access.</p>
                        </div>
                        <div class="rounded-[1.25rem] bg-white/75 p-4">
                            <p class="font-semibold text-[#1f3b2e]">Rejected or suspended</p>
                            <p class="mt-1 text-sm text-[#6b6b6b]">Restricted until an administrator reactivates the account.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
