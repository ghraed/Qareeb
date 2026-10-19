<section class="q-section">
    <h2 class="q-heading text-2xl">About the business</h2>
    <p class="q-copy mt-4 text-sm leading-7">{{ $profile->description ?: 'This provider has not added a public description yet.' }}</p>

    <div class="mt-6 grid gap-4 sm:grid-cols-2">
        <div class="q-panel p-5">
            <p class="text-sm font-medium text-[#6b6b6b]">Business email</p>
            @if ($provider->email)
            <a href="https://mail.google.com/mail/?view=cm&fs=1&to={{ rawurlencode($provider->email) }}" target="_blank" rel="noopener noreferrer" class="mt-2 block [overflow-wrap:anywhere] text-lg font-semibold text-[#1f3b2e] hover:text-[#6c8a5b]">{{ $provider->email }}</a>
            @else
                <p class="mt-2 text-lg font-semibold text-[#1f3b2e]">Not publicly listed</p>
            @endif
        </div>
        <div class="q-panel p-5">
            <p class="text-sm font-medium text-[#6b6b6b]">Phone/WhatsApp</p>
            @if ($profile->phone)
                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $profile->phone) }}" target="_blank" rel="noopener noreferrer" aria-label="Contact this provider on WhatsApp at {{ $profile->phone }} (opens in a new tab)" title="Open WhatsApp" class="mt-2 inline-flex min-h-11 items-center gap-3 rounded-md text-lg font-semibold text-[#1f3b2e] hover:text-[#6c8a5b] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#1f3b2e]">
                    <span>{{ $profile->phone }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="h-6 w-6 shrink-0 text-[#128c7e] motion-safe:animate-bounce">
                        <path d="M20.52 3.48A11.87 11.87 0 0 0 12.05 0C5.46 0 .1 5.36.1 11.95c0 2.1.55 4.16 1.6 5.97L0 24l6.25-1.64a11.9 11.9 0 0 0 5.8 1.48h.01C18.65 23.84 24 18.48 24 11.9c0-3.19-1.24-6.18-3.48-8.42ZM12.06 21.83a9.9 9.9 0 0 1-5.04-1.38l-.36-.21-3.71.97.99-3.62-.24-.37a9.88 9.88 0 0 1-1.52-5.27c0-5.48 4.46-9.94 9.94-9.94a9.87 9.87 0 0 1 7.03 2.91 9.87 9.87 0 0 1 2.91 7.03c0 5.48-4.46 9.88-10 9.88Zm5.45-7.44c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.49s1.07 2.89 1.22 3.09c.15.2 2.1 3.2 5.09 4.49.71.31 1.27.49 1.7.63.71.23 1.36.2 1.87.12.57-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35Z" />
                    </svg>
                </a>
            @else
                <p class="mt-2 text-lg font-semibold text-[#1f3b2e]">Not publicly listed</p>
            @endif
        </div>
        <div class="q-panel p-5 sm:col-span-2">
            <h3 id="work-categories-heading" class="text-base font-semibold text-qareeb-green">Work categories</h3>
            @if ($profile->categories)
                <ul aria-labelledby="work-categories-heading" role="list" class="mt-4 flex flex-wrap gap-2">
                    @foreach ($profile->categories as $category)
                        <li class="max-w-full rounded-xl border border-qareeb-olive/25 bg-qareeb-olive/10 px-3 py-2 text-sm leading-6 font-medium break-words text-qareeb-green">{{ $category }}</li>
                    @endforeach
                </ul>
            @else
                <p class="q-copy mt-3 text-sm">No categories listed yet</p>
            @endif
        </div>
    </div>
</section>
