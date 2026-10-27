@php($providerPreregistration = config('qareeb.provider_preregistration'))
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Qareeb</title>
        <link rel="icon" type="image/png" href="{{ asset('images/qareeb-logo.png') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:400,500,600,700|inter:400,500,600,700|noto-sans-arabic:400,500,700" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-dvh text-[#1f2b23]">
        <div class="relative isolate min-h-dvh overflow-hidden">
            <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-72 bg-radial-[at_top_left] from-[#e8dcc2]/70 via-transparent to-transparent blur-xl"></div>
            <div class="pointer-events-none absolute right-0 top-24 -z-10 h-72 w-72 rounded-full bg-[#6c8a5b]/12 blur-3xl"></div>

            <div class="mx-auto flex min-h-dvh max-w-6xl flex-col px-4 py-3 sm:px-6 sm:py-4 lg:px-8">
                <x-site-header />

                <main class="flex flex-1 items-center py-3">
                    <div class="grid w-full gap-4 lg:grid-cols-[1fr_0.9fr] lg:items-center lg:gap-8">
                        <section class="space-y-3">
                            <span class="inline-flex rounded-full border border-[#d7ccb6] bg-[#f6f2e6] px-4 py-1.5 text-sm font-semibold text-[#1f3b2e]">{{ $providerPreregistration ? 'Provider preregistration' : 'Local services' }}</span>
                            <div class="space-y-3">
                                <h1 class="q-page-title max-w-3xl">{{ $providerPreregistration ? 'Grow your business with Qareeb.' : 'Find trusted help, close to home.' }}</h1>
                                <p class="max-w-2xl text-sm leading-6 text-[#5f625d] sm:text-base sm:leading-7">{{ $providerPreregistration ? 'We are welcoming service providers ahead of our public launch. Register your business for review and prepare your profile after approval.' : 'Explore local services, discover providers in your area, and contact the right person directly.' }}</p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                @if ($providerPreregistration)
                                    <a href="{{ route('business.register') }}" class="q-button-primary">Register as a provider</a>
                                @else
                                    <a href="{{ route('seeker.login') }}" class="q-button-primary">Find a provider</a>
                                @endif
                            </div>
                        </section>

                        <section class="q-card flex items-center justify-center gap-4 p-4 text-center lg:flex-col lg:gap-6 lg:p-8">
                            <x-app-logo class="h-12 w-12 shrink-0 rounded-xl shadow-[0_25px_60px_-28px_rgba(31,59,46,0.55)] lg:h-20 lg:w-20 lg:rounded-[1.5rem]" alt="Qareeb logo" />
                            <div class="space-y-1 lg:space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#6c8a5b] lg:text-sm">Trusted help, nearby</p>
                                <h2 class="font-serif text-xl font-semibold tracking-tight text-[#1f3b2e] lg:text-3xl">{{ $providerPreregistration ? 'Be part of our first providers' : 'Local services, closer to you' }}</h2>
                                <p class="text-sm leading-6 text-[#5f625d]">{{ $providerPreregistration ? 'Join early and get your business ready for launch.' : 'Discover providers in your area and contact them directly.' }}</p>
                            </div>
                        </section>
                    </div>
                </main>
                <footer class="shrink-0 py-1 text-sm"><a class="font-semibold underline" href="{{ route('terms.show') }}">Terms and Conditions</a></footer>
            </div>
        </div>
    </body>
</html>
