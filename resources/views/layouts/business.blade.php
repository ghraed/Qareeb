<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Qareeb')</title>
        <meta name="description" content="@yield('meta_description', 'Trusted help nearby.')">
        <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', 'Qareeb')))">
        <meta property="og:description" content="@yield('og_description', $__env->yieldContent('meta_description', 'Trusted help nearby.'))">
        @hasSection('og_image')
            <meta property="og:image" content="@yield('og_image')">
        @endif
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=caveat:400,600|fraunces:400,500,600,700|inter:400,500,600,700|noto-sans-arabic:400,500,700" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-dvh text-[#1f2b23]">
        <div class="relative isolate min-h-dvh overflow-hidden">
            <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-72 bg-radial-[at_top_left] from-[#e8dcc2]/70 via-transparent to-transparent blur-xl"></div>
            <div class="pointer-events-none absolute right-0 top-24 -z-10 h-72 w-72 rounded-full bg-[#6c8a5b]/12 blur-3xl"></div>

            <div class="mx-auto flex min-h-dvh max-w-7xl flex-col px-4 py-3 sm:px-6 sm:py-4 lg:px-8">
                <x-site-header class="mb-6" />

                <main id="main-content" class="min-w-0 flex-1">
                    @yield('content')
                </main>
                <footer class="mt-6 py-2 text-sm"><a class="font-semibold underline" href="{{ route('terms.show') }}">Terms and Conditions</a></footer>
            </div>
        </div>
        <x-toast
            :type="$errors->any() || session('error') ? 'error' : 'success'"
            :message="$errors->first() ?: (session('error') ?: (session('status') ?: ''))"
        />
        @stack('overlays')
    </body>
</html>
