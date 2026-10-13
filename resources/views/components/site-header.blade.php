@php
    $navigationUser = auth()->user();
    $navigationHome = url('/');
    $logoutRoute = null;
    $navigationLinks = [
        ['label' => 'Find a provider', 'route' => 'providers.index', 'active' => 'providers.*'],
        ['label' => 'Seeker sign in', 'route' => 'seeker.login', 'active' => 'seeker.login'],
        ['label' => 'Join as a Seeker', 'route' => 'seeker.register', 'active' => 'seeker.register'],
        ['label' => 'Business login', 'route' => 'business.login', 'active' => 'business.*'],
    ];

    if ($navigationUser?->isAdmin()) {
        $navigationHome = route('admin.dashboard');
        $logoutRoute = 'admin.logout';
        $navigationLinks = [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
            ['label' => 'Complaints', 'route' => 'admin.complaints.index', 'active' => 'admin.complaints.*'],
            ['label' => 'Image reports', 'route' => 'admin.image-reports.index', 'active' => 'admin.image-reports.*'],
        ];
    } elseif ($navigationUser?->isSeeker()) {
        $navigationHome = route('seeker.dashboard');
        $logoutRoute = 'seeker.logout';
        $navigationLinks = [
            ['label' => 'Find a provider', 'route' => 'providers.index', 'active' => 'providers.*'],
            ['label' => 'Dashboard', 'route' => 'seeker.dashboard', 'active' => 'seeker.dashboard'],
            ['label' => 'My profile', 'route' => 'seeker.profile.edit', 'active' => 'seeker.profile.*'],
            ['label' => 'My complaints', 'route' => 'seeker.complaints.index', 'active' => 'seeker.complaints.*'],
        ];
    } elseif ($navigationUser?->isServiceProvider()) {
        $logoutRoute = 'business.logout';
        $navigationHome = route($navigationUser->hasActiveProviderAccount() ? 'business.dashboard' : 'business.account.status');
        $navigationLinks = $navigationUser->hasActiveProviderAccount()
            ? [
                ['label' => 'Dashboard', 'route' => 'business.dashboard', 'active' => 'business.dashboard'],
                ['label' => 'My profile', 'route' => 'business.profile.show', 'active' => 'business.profile.*'],
                ['label' => 'Public profile', 'route' => 'providers.show', 'parameters' => $navigationUser, 'active' => 'providers.*'],
            ]
            : [
                ['label' => 'Account status', 'route' => 'business.account.status', 'active' => 'business.account.status'],
            ];
    }
@endphp

<header
    {{ $attributes->class('relative z-20 flex shrink-0 items-center justify-between gap-3 border-b border-qareeb-sand pb-3 lg:gap-6') }}
    x-data="siteNavigation"
    x-on:resize.window="syncViewport()"
    x-on:click.outside="closeMenu(false)"
    x-on:keydown.escape.stop="if (menuOpen) closeMenu()"
    x-on:focusout="if (!$el.contains($event.relatedTarget)) closeMenu(false)"
>
    <a href="{{ $navigationHome }}" class="inline-flex shrink-0 items-center gap-3 rounded-xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-qareeb-green">
        <x-app-logo class="h-14 w-14 rounded-[1.2rem] shadow-[0_20px_45px_-20px_rgba(31,59,46,0.55)] lg:h-20 lg:w-20 lg:rounded-[1.5rem]" />
        <span class="min-w-0">
            <span class="flex items-center gap-2 font-serif text-[1.35rem] leading-none font-semibold tracking-tight text-qareeb-green sm:text-[1.7rem]">
                <span>Qareeb</span>
                <span class="font-arabic text-[1.45rem] sm:text-[1.9rem]">قريب</span>
            </span>
            <span class="mt-1 block text-xs text-qareeb-stone sm:text-sm">Trusted help, nearby</span>
        </span>
    </a>

    <button type="button" class="q-menu-toggle lg:hidden" x-cloak x-ref="menuToggle" aria-controls="site-navigation" aria-expanded="false" aria-label="Open navigation" x-bind:aria-expanded="menuOpen" x-on:click="openMenu()">
        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
            <path d="M6 8H22M6 14H17M6 20H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            <circle cx="22" cy="14" r="1.5" fill="var(--color-qareeb-brown)" />
        </svg>
    </button>

    <div id="site-navigation" aria-label="Main navigation" class="q-home-navigation" x-ref="menuPanel" x-bind:class="{ 'is-open': menuOpen }" x-bind:inert="!isDesktop && !menuOpen" x-bind:role="isDesktop ? null : 'dialog'" x-bind:aria-modal="!isDesktop && menuOpen ? 'true' : null" x-on:keydown.tab="trapFocus($event)">
        <div class="q-home-navigation-inner">
            <div class="q-home-navigation-heading">
                <div class="flex items-center gap-3">
                    <x-app-logo class="h-12 w-12 rounded-xl" />
                    <span class="q-heading text-2xl">Qareeb <span class="font-arabic">قريب</span></span>
                </div>
                <button type="button" class="q-menu-toggle" x-ref="menuClose" aria-label="Close navigation" x-on:click="closeMenu()">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                        <path d="M7 7L21 21M21 7L7 21" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
            <nav aria-label="Main navigation" class="q-home-navigation-links">
                @foreach ($navigationLinks as $navigationLink)
                    @php
                        $navigationHref = route($navigationLink['route'], $navigationLink['parameters'] ?? []);
                        $navigationIsCurrent = isset($navigationLink['parameters'])
                            ? url()->current() === $navigationHref
                            : request()->routeIs($navigationLink['active']);
                    @endphp
                    <a href="{{ $navigationHref }}" @class(['q-nav-link', 'is-current' => $navigationIsCurrent]) @if ($navigationIsCurrent) aria-current="page" @endif x-on:click="closeMenu(false)">{{ $navigationLink['label'] }}</a>
                @endforeach
                @if ($logoutRoute)
                    <form method="POST" action="{{ route($logoutRoute) }}" class="q-navigation-logout">
                        @csrf
                        <button type="submit" class="q-nav-link w-full" x-on:click="closeMenu(false)">Logout</button>
                    </form>
                @endif
            </nav>
            <p class="q-home-navigation-footer break-words">{{ $navigationUser ? ($navigationUser->email ?: $navigationUser->username ?: $navigationUser->name) : 'Trusted help, nearby' }}</p>
        </div>
    </div>
</header>
