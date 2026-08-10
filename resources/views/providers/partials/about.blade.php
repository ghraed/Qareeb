<section class="q-section">
    <h2 class="q-heading text-2xl">About the business</h2>
    <p class="q-copy mt-4 text-sm leading-7">{{ $profile->description ?: 'This provider has not added a public description yet.' }}</p>

    <div class="mt-6 grid gap-4 sm:grid-cols-2">
        <div class="q-panel p-5">
            <p class="text-sm font-medium text-[#6b6b6b]">Business email</p>
            <a href="https://mail.google.com/mail/?view=cm&fs=1&to={{ rawurlencode($provider->email) }}" target="_blank" rel="noopener noreferrer" class="mt-2 block text-lg font-semibold text-[#1f3b2e] hover:text-[#6c8a5b]">{{ $provider->email }}</a>
        </div>
        <div class="q-panel p-5">
            <p class="text-sm font-medium text-[#6b6b6b]">Phone</p>
            @if ($profile->phone)
                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $profile->phone) }}" target="_blank" rel="noopener noreferrer" class="mt-2 block text-lg font-semibold text-[#1f3b2e] hover:text-[#6c8a5b]">{{ $profile->phone }}</a>
            @else
                <p class="mt-2 text-lg font-semibold text-[#1f3b2e]">Not publicly listed</p>
            @endif
        </div>
        <div class="q-panel p-5 sm:col-span-2">
            <p class="text-sm font-medium text-[#6b6b6b]">Work categories</p>
            <p class="mt-2 text-lg font-semibold text-[#1f3b2e]">{{ $profile->formattedCategories() ?: 'No categories listed yet' }}</p>
        </div>
    </div>
</section>
