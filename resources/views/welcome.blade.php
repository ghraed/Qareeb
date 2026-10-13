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
    <body class="min-h-dvh text-[#1f2b23]">
        <div class="relative isolate min-h-dvh overflow-hidden">
            <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-72 bg-radial-[at_top_left] from-[#e8dcc2]/70 via-transparent to-transparent blur-xl"></div>
            <div class="pointer-events-none absolute right-0 top-24 -z-10 h-72 w-72 rounded-full bg-[#6c8a5b]/12 blur-3xl"></div>

            <div class="mx-auto flex min-h-dvh max-w-6xl flex-col px-4 py-3 sm:px-6 sm:py-4 lg:px-8">
                <header
                    class="relative z-20 flex shrink-0 items-center justify-between gap-3 border-b border-qareeb-sand pb-3 lg:gap-6"
                    x-data="{ menuOpen: false, isDesktop: window.matchMedia('(min-width: 64rem)').matches }"
                    x-effect="document.body.style.overflow = menuOpen && !isDesktop ? 'hidden' : ''"
                    x-on:resize.window="isDesktop = window.matchMedia('(min-width: 64rem)').matches; if (isDesktop) menuOpen = false"
                    x-on:click.outside="menuOpen = false"
                    x-on:keydown.escape.stop="if (menuOpen) { menuOpen = false; $refs.menuToggle.focus() }"
                    x-on:focusout="if (!$el.contains($event.relatedTarget)) menuOpen = false"
                >
                    <a href="{{ url('/') }}" class="inline-flex shrink-0 items-center gap-3 rounded-xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-qareeb-green">
                        <x-app-logo class="h-14 w-14 rounded-[1.2rem] shadow-[0_20px_45px_-20px_rgba(31,59,46,0.55)] lg:h-20 lg:w-20 lg:rounded-[1.5rem]" alt="Qareeb logo" />
                        <span class="min-w-0">
                            <span class="flex items-center gap-2 text-[1.35rem] leading-none font-semibold tracking-tight text-[#1f3b2e] font-serif sm:text-[1.7rem]">
                                <span>Qareeb</span>
                                <span class="font-arabic text-[1.45rem] sm:text-[1.9rem]">قريب</span>
                            </span>
                            <span class="mt-1 block text-xs text-[#6b6b6b] sm:text-sm">Trusted help, nearby</span>
                        </span>
                    </a>

                    <button
                        type="button"
                        class="q-menu-toggle lg:hidden"
                        x-cloak
                        x-ref="menuToggle"
                        aria-controls="home-navigation"
                        aria-expanded="false"
                        aria-label="Open navigation"
                        x-bind:aria-expanded="menuOpen"
                        x-on:click="menuOpen = true; $nextTick(() => requestAnimationFrame(() => $refs.menuClose.focus({ preventScroll: true })))"
                    >
                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                            <path class="q-menu-line q-menu-line-top" d="M6 8H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path class="q-menu-line q-menu-line-middle" d="M6 14H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path class="q-menu-line q-menu-line-bottom" d="M6 20H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <circle class="q-menu-dot" cx="22" cy="14" r="1.5" fill="var(--color-qareeb-brown)" />
                        </svg>
                    </button>

                    <div
                        id="home-navigation"
                        aria-label="Main navigation"
                        class="q-home-navigation"
                        x-bind:class="{ 'is-open': menuOpen }"
                        x-bind:inert="!isDesktop && !menuOpen"
                        x-bind:role="isDesktop ? null : 'dialog'"
                        x-bind:aria-modal="!isDesktop && menuOpen ? 'true' : null"
                        x-on:keydown.tab="if (!isDesktop && menuOpen) {
                            const controls = $el.querySelectorAll('button, a[href]');
                            const first = controls[0], last = controls[controls.length - 1];
                            if ($event.shiftKey && document.activeElement === first) { $event.preventDefault(); last.focus() }
                            else if (!$event.shiftKey && document.activeElement === last) { $event.preventDefault(); first.focus() }
                        }"
                    >
                        <div class="q-home-navigation-inner">
                            <div class="q-home-navigation-heading">
                                <div class="flex items-center gap-3">
                                    <x-app-logo class="h-12 w-12 rounded-xl" alt="Qareeb logo" />
                                    <span class="q-heading text-2xl">Qareeb <span class="font-arabic">قريب</span></span>
                                </div>
                                <button type="button" class="q-menu-toggle" x-ref="menuClose" aria-label="Close navigation" x-on:click="menuOpen = false; $refs.menuToggle.focus()">
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                                        <path d="M7 7L21 21M21 7L7 21" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
                                    </svg>
                                </button>
                            </div>
                            <nav aria-label="Main navigation" class="q-home-navigation-links">
                                <a href="{{ route('providers.index') }}" class="q-nav-link" x-on:click="menuOpen = false">Find a provider</a>
                                <a href="{{ route('seeker.login') }}" class="q-nav-link" x-on:click="menuOpen = false">Seeker sign in</a>
                                <a href="{{ route('seeker.register') }}" class="q-nav-link" x-on:click="menuOpen = false">Join as a Seeker</a>
                                <a href="{{ route('business.login') }}" class="q-nav-link" x-on:click="menuOpen = false">Business login</a>
                            </nav>
                            <p class="q-home-navigation-footer">Trusted help, nearby</p>
                        </div>
                    </div>
                </header>

                <main class="flex flex-1 items-center py-3">
                    <div class="grid w-full gap-4 lg:grid-cols-[1fr_0.9fr] lg:items-center lg:gap-8">
                        <section class="space-y-3">
                            <span class="inline-flex rounded-full border border-[#d7ccb6] bg-[#f6f2e6] px-4 py-1.5 text-sm font-semibold text-[#1f3b2e]">Service provider portal</span>
                            <div class="space-y-3">
                                <h1 class="max-w-3xl font-serif text-2xl leading-tight font-semibold tracking-tight text-[#1f3b2e] sm:text-3xl lg:text-[2rem]">Qareeb keeps the same identity across the full provider and admin experience.</h1>
                                <p class="max-w-2xl text-sm leading-6 text-[#5f625d] sm:text-base sm:leading-7">Use the business workspace to manage your provider profile, or sign in as an administrator to review and activate accounts.</p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('seeker.login') }}" class="q-button-primary">Find a provider</a>
                            </div>
                        </section>

                        <section class="q-card flex items-center justify-center gap-4 p-4 text-center lg:flex-col lg:gap-6 lg:p-8">
                            <x-app-logo class="h-12 w-12 shrink-0 rounded-xl shadow-[0_25px_60px_-28px_rgba(31,59,46,0.55)] lg:h-20 lg:w-20 lg:rounded-[1.5rem]" alt="Qareeb logo" />
                            <div class="space-y-1 lg:space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#6c8a5b] lg:text-sm">Trusted help, nearby</p>
                                <h2 class="font-serif text-xl font-semibold tracking-tight text-[#1f3b2e] lg:text-3xl">Local services, closer to you</h2>
                                <p class="text-sm leading-6 text-[#5f625d]">Discover providers in your area and contact them directly.</p>
                            </div>
                        </section>
                    </div>
                </main>
                <footer class="shrink-0 py-1 text-sm"><a class="font-semibold underline" href="{{ route('terms.show') }}">Terms and Conditions</a></footer>
            </div>
        </div>
    </body>
</html>
