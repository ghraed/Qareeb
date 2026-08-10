@extends('layouts.business')

@section('title', 'Admin Dashboard')

@php
    use App\Enums\AccountStatus;
@endphp

@section('content')
    <div class="space-y-8">
        <section class="q-card p-6 sm:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="q-eyebrow">Admin dashboard</p>
                    <h1 class="mt-3 font-serif text-4xl font-semibold tracking-tight text-[#1f3b2e] sm:text-5xl">Provider account approvals and activation</h1>
                    <p class="mt-3 max-w-3xl text-base leading-7 text-[#6b6b6b]">Review newly registered service providers, approve trusted businesses, reject incomplete applications, or suspend live accounts when access must be restricted.</p>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center rounded-[1.15rem] border border-[#eadfc7] bg-[#fff7ef] px-5 py-3 text-sm font-semibold text-[#8f5b28] transition hover:bg-[#f7ecdb]">Logout</button>
                </form>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="q-panel p-5">
                <p class="text-sm font-medium text-[#6b6b6b]">Pending</p>
                <p class="mt-2 text-3xl font-semibold text-[#1f3b2e]">{{ $stats['pending'] }}</p>
            </div>
            <div class="q-panel p-5">
                <p class="text-sm font-medium text-[#6b6b6b]">Active</p>
                <p class="mt-2 text-3xl font-semibold text-[#1f3b2e]">{{ $stats['active'] }}</p>
            </div>
            <div class="q-panel p-5">
                <p class="text-sm font-medium text-[#6b6b6b]">Rejected</p>
                <p class="mt-2 text-3xl font-semibold text-[#1f3b2e]">{{ $stats['rejected'] }}</p>
            </div>
            <div class="q-panel p-5">
                <p class="text-sm font-medium text-[#6b6b6b]">Suspended</p>
                <p class="mt-2 text-3xl font-semibold text-[#1f3b2e]">{{ $stats['suspended'] }}</p>
            </div>
        </section>

        <section class="space-y-5">
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="q-eyebrow">Priority queue</p>
                    <h2 class="mt-2 font-serif text-3xl font-semibold text-[#1f3b2e]">Pending provider reviews</h2>
                </div>
                <span class="q-badge">{{ $pendingProviders->count() }} awaiting review</span>
            </div>

            @forelse ($pendingProviders as $provider)
                <article class="q-card p-6">
                    <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                        <div class="max-w-3xl">
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="font-serif text-3xl font-semibold text-[#1f3b2e]">{{ $provider->businessProfile?->business_name ?: $provider->name }}</h3>
                                <span class="rounded-full bg-[#ece4d0] px-4 py-2 text-sm font-semibold text-[#1f3b2e]">{{ $provider->account_status->label() }}</span>
                            </div>
                            <p class="mt-2 text-sm text-[#6b6b6b]">{{ $provider->email }} · Registered {{ $provider->created_at->diffForHumans() }}</p>
                            <p class="mt-4 text-sm leading-7 text-[#5f625d]">{{ $provider->businessProfile?->description ?: 'No business description has been added yet.' }}</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2 xl:w-[28rem]">
                            <form method="POST" action="{{ route('admin.providers.approve', $provider) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="q-button-primary w-full">Approve account</button>
                            </form>

                            <form method="POST" action="{{ route('admin.providers.reject', $provider) }}" class="space-y-3 rounded-[1.5rem] border border-rose-100 bg-rose-50/70 p-4">
                                @csrf
                                @method('PATCH')
                                <label for="rejection_reason_{{ $provider->id }}" class="block text-sm font-semibold text-rose-700">Reject with reason</label>
                                <textarea id="rejection_reason_{{ $provider->id }}" name="rejection_reason" rows="3" class="q-input border-rose-200 bg-white focus:border-rose-300 focus:ring-rose-100" placeholder="Explain why this provider account is being rejected."></textarea>
                                <button type="submit" class="w-full rounded-[1.15rem] bg-rose-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-700">Reject account</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="q-panel-soft p-8 text-center">
                    <h3 class="font-serif text-2xl font-semibold text-[#1f3b2e]">No pending approvals right now.</h3>
                    <p class="mt-2 text-sm leading-7 text-[#6b6b6b]">Newly registered providers will appear here with approval and rejection controls.</p>
                </div>
            @endforelse
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="space-y-5">
                <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="q-eyebrow">Live accounts</p>
                        <h2 class="mt-2 font-serif text-3xl font-semibold text-[#1f3b2e]">Active providers</h2>
                    </div>
                    <span class="q-badge">{{ $activeProviders->count() }} active</span>
                </div>

                @forelse ($activeProviders as $provider)
                    <article class="q-card p-6">
                        <div class="flex flex-col gap-5">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="font-serif text-2xl font-semibold text-[#1f3b2e]">{{ $provider->businessProfile?->business_name ?: $provider->name }}</h3>
                                    <span class="rounded-full bg-[#e1efe3] px-4 py-2 text-sm font-semibold text-[#2e583b]">{{ $provider->account_status->label() }}</span>
                                </div>
                                <p class="mt-2 text-sm text-[#6b6b6b]">{{ $provider->email }}</p>
                                <p class="mt-3 text-sm text-[#5f625d]">Approved {{ optional($provider->approved_at)->diffForHumans() ?: 'just now' }}</p>
                            </div>

                            <form method="POST" action="{{ route('admin.providers.suspend', $provider) }}" class="space-y-3 rounded-[1.5rem] border border-amber-200 bg-amber-50/75 p-4">
                                @csrf
                                @method('PATCH')
                                <label for="suspension_reason_{{ $provider->id }}" class="block text-sm font-semibold text-amber-800">Suspend with reason</label>
                                <textarea id="suspension_reason_{{ $provider->id }}" name="suspension_reason" rows="3" class="q-input border-amber-200 bg-white focus:border-amber-300 focus:ring-amber-100" placeholder="Explain why provider access is being suspended."></textarea>
                                <button type="submit" class="w-full rounded-[1.15rem] px-5 py-3 text-sm font-semibold text-white transition" style="background-color: #c6863a;" onmouseover="this.style.backgroundColor='#ae7431'" onmouseout="this.style.backgroundColor='#c6863a'">Suspend account</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="q-panel-soft p-8 text-center">
                        <h3 class="font-serif text-2xl font-semibold text-[#1f3b2e]">No active providers yet.</h3>
                        <p class="mt-2 text-sm leading-7 text-[#6b6b6b]">Approved business accounts will move into this section automatically.</p>
                    </div>
                @endforelse
            </div>

            <div class="space-y-5">
                <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="q-eyebrow">Restricted accounts</p>
                        <h2 class="mt-2 font-serif text-3xl font-semibold text-[#1f3b2e]">Rejected and suspended providers</h2>
                    </div>
                    <span class="q-badge">{{ $restrictedProviders->count() }} restricted</span>
                </div>

                @forelse ($restrictedProviders as $provider)
                    <article class="q-card p-6">
                        <div class="flex flex-col gap-5">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="font-serif text-2xl font-semibold text-[#1f3b2e]">{{ $provider->businessProfile?->business_name ?: $provider->name }}</h3>
                                    <span @class([
                                        'rounded-full px-4 py-2 text-sm font-semibold',
                                        'bg-rose-100 text-rose-700' => $provider->account_status === AccountStatus::Rejected,
                                        'bg-amber-100 text-amber-800' => $provider->account_status === AccountStatus::Suspended,
                                    ])>{{ $provider->account_status->label() }}</span>
                                </div>
                                <p class="mt-2 text-sm text-[#6b6b6b]">{{ $provider->email }}</p>
                                @if ($provider->account_status === AccountStatus::Rejected && $provider->rejection_reason)
                                    <p class="mt-3 text-sm leading-7 text-rose-700">{{ $provider->rejection_reason }}</p>
                                @endif
                                @if ($provider->account_status === AccountStatus::Suspended && $provider->suspension_reason)
                                    <p class="mt-3 text-sm leading-7 text-amber-800">{{ $provider->suspension_reason }}</p>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('admin.providers.activate', $provider) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="q-button-secondary w-full">Activate account</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="q-panel-soft p-8 text-center">
                        <h3 class="font-serif text-2xl font-semibold text-[#1f3b2e]">No restricted providers.</h3>
                        <p class="mt-2 text-sm leading-7 text-[#6b6b6b]">Rejected and suspended business accounts will be listed here with a one-click activation action.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
