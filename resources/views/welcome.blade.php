<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Qareeb</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:400,500,600,700|inter:400,500,600,700|noto-sans-arabic:400,500,700" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen text-[#1f2b23]">
        <div class="relative isolate min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-72 bg-radial-[at_top_left] from-[#e8dcc2]/70 via-transparent to-transparent blur-xl"></div>
            <div class="pointer-events-none absolute right-0 top-24 -z-10 h-72 w-72 rounded-full bg-[#6c8a5b]/12 blur-3xl"></div>

            <div class="mx-auto flex min-h-screen max-w-6xl flex-col px-4 py-6 sm:px-6 lg:px-8">
                <header class="flex items-center justify-between gap-4">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <x-app-logo class="h-20 w-20 rounded-[1.2rem] shadow-[0_20px_45px_-20px_rgba(31,59,46,0.55)] sm:h-20 sm:w-20 sm:rounded-[1.5rem]" alt="Qareeb logo" />
                        <span class="min-w-0">
                            <span class="flex items-center gap-2 text-[1.35rem] leading-none font-semibold tracking-tight text-[#1f3b2e] font-serif sm:text-[1.7rem]">
                                <span>Qareeb</span>
                                <span class="font-arabic text-[1.45rem] sm:text-[1.9rem]">قريب</span>
                            </span>
                            <span class="mt-1 block text-xs text-[#6b6b6b] sm:text-sm">Trusted help, nearby</span>
                        </span>
                    </a>

                    <nav class="flex flex-wrap items-center justify-end gap-3">
                        <a href="{{ route('providers.index') }}" class="q-button-secondary">Find a provider</a>
                        <a href="{{ route('seeker.login') }}" class="q-button-secondary">Seeker sign in</a>
                        <a href="{{ route('seeker.register') }}" class="q-button-primary">Join as a Seeker</a>
                        <a href="{{ route('business.login') }}" class="q-button-secondary">Business login</a>
                        <a href="{{ route('business.register') }}" class="q-button-primary">Register</a>
                        <a href="{{ route('admin.login') }}" class="q-button-secondary">Admin</a>
                    </nav>
                </header>

                <main class="flex flex-1 items-center py-10 sm:py-16">
                    <div class="grid w-full gap-8 lg:grid-cols-[1fr_0.9fr] lg:items-center">
                        <section class="space-y-6">
                            <span class="inline-flex rounded-full border border-[#d7ccb6] bg-[#f6f2e6] px-4 py-2 text-sm font-semibold text-[#1f3b2e]">Service provider portal</span>
                            <div class="space-y-4">
                                <h1 class="max-w-3xl font-serif text-4xl font-semibold tracking-tight text-[#1f3b2e] sm:text-6xl">Qareeb keeps the same identity across the full provider and admin experience.</h1>
                                <p class="max-w-2xl text-base leading-7 text-[#5f625d] sm:text-lg sm:leading-8">Use the business workspace to manage your provider profile, or sign in as an administrator to review and activate accounts.</p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('business.login') }}" class="q-button-primary">Open provider workspace</a>
                                <a href="{{ route('admin.login') }}" class="q-button-secondary">Open admin portal</a>
                            </div>
                        </section>

                        <section class="q-card flex flex-col items-center justify-center gap-6 p-8 text-center sm:p-10">
                            <x-app-logo class="h-20 w-20 rounded-[1.2rem] shadow-[0_25px_60px_-28px_rgba(31,59,46,0.55)] sm:h-20 sm:w-20 sm:rounded-[1.5rem]" alt="Qareeb logo" />
                            <div class="space-y-2">
                                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-[#6c8a5b]">Brand mark</p>
                                <h2 class="font-serif text-3xl font-semibold tracking-tight text-[#1f3b2e]">Unified on every page</h2>
                            </div>
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
