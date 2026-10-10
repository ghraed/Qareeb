<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Business Portal')</title>
        <meta name="description" content="@yield('meta_description', 'Trusted help nearby.')">
        <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', 'Business Portal')))">
        <meta property="og:description" content="@yield('og_description', $__env->yieldContent('meta_description', 'Trusted help nearby.'))">
        @hasSection('og_image')
            <meta property="og:image" content="@yield('og_image')">
        @endif
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:400,500,600,700|inter:400,500,600,700|noto-sans-arabic:400,500,700" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen text-[#1f2b23]">
        @php
            $homeRoute = route('business.login');

            if (auth()->check()) {
                $homeRoute = auth()->user()->isAdmin()
                    ? route('admin.dashboard')
                    : (auth()->user()->isSeeker() ? route('seeker.dashboard') : (auth()->user()->hasActiveProviderAccount() ? route('business.dashboard') : route('business.account.status')));
            }
        @endphp
        <div class="relative isolate min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-72 bg-radial-[at_top_left] from-[#e8dcc2]/70 via-transparent to-transparent blur-xl"></div>
            <div class="pointer-events-none absolute right-0 top-24 -z-10 h-72 w-72 rounded-full bg-[#6c8a5b]/12 blur-3xl"></div>

            <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
                <header class="mb-10 flex flex-col items-start justify-between gap-4 sm:flex-row sm:flex-wrap sm:items-center">
                    <a href="{{ $homeRoute }}" class="inline-flex max-w-full items-center gap-3 sm:gap-4">
                        <x-app-logo
                            class="h-20 w-20 shrink-0 rounded-[1.35rem] shadow-[0_20px_40px_-18px_rgba(31,59,46,0.55)] sm:h-20 sm:w-20 sm:rounded-[1.6rem]"
                            alt="Qareeb logo"
                        />
                        <span class="min-w-0">
                            <span class="flex items-center gap-2 text-[1.5rem] leading-none font-semibold tracking-tight text-[#1f3b2e] font-serif sm:gap-3 sm:text-[2rem]">
                                <span>Qareeb&reg;</span>
                                <span class="h-8 w-px shrink-0 bg-[#d8ccb5] sm:h-10"></span>
                                <span class="font-arabic text-[1.6rem] sm:text-[2.1rem]">قريب</span>
                            </span>
                            <span class="mt-1 block text-xs text-[#6b6b6b] sm:text-sm">Trusted help, nearby</span>
                        </span>
                    </a>

                    <nav class="flex w-full items-center gap-3 text-sm text-[#6b6b6b] sm:w-auto">
                        @auth
                            <span class="max-w-full truncate rounded-full border border-[#eadfc7] bg-white/85 px-4 py-2 shadow-sm">{{ auth()->user()->email ?: auth()->user()->username }}</span>
                        @endauth
                    </nav>
                </header>

                <main class="flex-1">
                    @if (session('status'))
                        <x-alert type="success" :message="session('status')" class="mb-6" />
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
