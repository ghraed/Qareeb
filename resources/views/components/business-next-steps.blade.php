@props(['profile', 'local' => false])

@php
    $missingFields = $profile->missingCompletionFields();
    $nextSteps = collect([
        'business_name' => ['title' => 'Add your business name', 'description' => 'Help Seekers identify your business.'],
        'phone' => ['title' => 'Add your phone number', 'description' => 'Give Seekers a way to contact you.'],
        'description' => ['title' => 'Add your bio', 'description' => 'Describe your services and experience.'],
        'categories' => ['title' => 'Choose work categories', 'description' => 'Select the services you offer.'],
        'address' => ['title' => 'Add your address', 'description' => 'Tell Seekers where your business is based.'],
        'state' => ['title' => 'Choose your districts', 'description' => 'Select the districts your business covers.'],
        'city' => ['title' => 'Choose your cities', 'description' => 'Select the cities you serve within your districts.'],
        'profile_photo' => ['title' => 'Upload your profile photo', 'description' => 'Add an identity photo for your provider card.'],
        'brand_photo' => ['title' => 'Upload your brand cover photo', 'description' => 'Add a cover image for your public profile.'],
    ])->filter(fn ($step, $field) => in_array(\App\Models\BusinessProfile::COMPLETION_FIELDS[$field], $missingFields, true));
@endphp
@if ($nextSteps->isNotEmpty())
    <div class="q-card p-6">
        <h2 class="font-serif text-2xl font-semibold text-[#1f3b2e]">Next steps</h2>
        <div class="mt-5 grid gap-3 {{ $local ? 'sm:grid-cols-2 lg:grid-cols-3' : '' }}">
            @foreach ($nextSteps as $field => $step)
                <a href="{{ $local ? '' : route('business.profile.edit') }}#{{ $field }}" @if ($local) @click.prevent="focusRequestedField('{{ $field }}')" @endif class="q-panel block p-4 transition-colors hover:border-qareeb-olive hover:bg-qareeb-cream focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-qareeb-green motion-reduce:transition-none">
                    <p class="font-semibold text-[#1f3b2e]">{{ $step['title'] }}</p>
                    <p class="mt-1 text-sm text-[#6b6b6b]">{{ $step['description'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
@endif
