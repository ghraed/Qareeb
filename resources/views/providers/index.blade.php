@extends('layouts.business')
@section('title', 'Find a provider')
@section('content')
<div class="space-y-6">
    <section class="q-card relative z-10 space-y-5 p-6 sm:p-8">
        <div><p class="q-eyebrow">Provider discovery</p><h1 class="q-heading mt-3 text-3xl sm:text-4xl">Find trusted help nearby.</h1><p class="q-copy mt-3">Browse approved providers and contact them directly.</p></div>
        <form method="GET" action="{{ route('providers.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" x-data="{ district: @js(old('district', $filters['district'] ?? '')), city: @js(old('city', $filters['city'] ?? '')) }">
            <div class="min-w-0 flex-1 space-y-2">
                <label id="category-label" for="category" x-bind:for="'category-control'" class="block font-medium">Work category</label>
                <x-provider-filter id="category">
                <select id="category" name="category" class="q-input" x-ref="select" x-show="!enhanced">
                    <option value="">All categories</option>
                    @foreach (\App\Models\BusinessProfile::WORK_CATEGORIES as $category)
                        <option value="{{ $category }}" @selected(old('category', $filters['category'] ?? '') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
                </x-provider-filter>
                @error('category')<p class="text-sm text-rose-700" role="alert">{{ $message }}</p>@enderror
                @error('page')<p class="text-sm text-rose-700" role="alert">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-2">
                <label id="district-label" for="district" x-bind:for="'district-control'" class="block font-medium">Service district</label>
                <x-provider-filter id="district">
                <select id="district" name="district" class="q-input" x-ref="select" x-show="!enhanced" x-model="district" x-on:change="city = ''">
                    <option value="">All districts</option>
                    @foreach (\App\Models\BusinessProfile::LEBANON_DISTRICTS as $district)
                        <option value="{{ $district }}" @selected(old('district', $filters['district'] ?? '') === $district)>{{ $district }}</option>
                    @endforeach
                </select>
                </x-provider-filter>
                @error('district')<p class="text-sm text-rose-700" role="alert">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-2">
                <label id="city-label" for="city" x-bind:for="'city-control'" class="block font-medium">Service city</label>
                <x-provider-filter id="city">
                <select id="city" name="city" class="q-input" x-ref="select" x-show="!enhanced" x-model="city" x-effect="district; $nextTick(() => refreshOptions())">
                    <option value="">All cities in district</option>
                    @foreach (\App\Models\BusinessProfile::LEBANON_DISTRICT_CITIES as $district => $cities)
                        <optgroup label="{{ $district }}" x-bind:disabled="district !== @js($district)">
                            @foreach ($cities as $city)
                                <option value="{{ $city }}" @selected(old('city', $filters['city'] ?? '') === $city)>{{ $city }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                </x-provider-filter>
                <p class="q-copy text-sm">Choose a district before filtering by city.</p>
                @error('city')<p class="text-sm text-rose-700" role="alert">{{ $message }}</p>@enderror
            </div>
            <div class="flex flex-wrap gap-3 md:col-span-2 xl:col-span-3">
            <button class="q-button-primary" type="submit">Search providers</button>
            <a class="q-button-secondary" href="{{ route('providers.index') }}">Clear filters</a>
            </div>
        </form>
    </section>
    <p class="q-copy">{{ $providers->total() }} {{ Str::plural('provider', $providers->total()) }} found</p>
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($providers as $profile)
            @php($provider = $profile->user)
            <article class="q-card min-w-0 space-y-4 p-6">
                <x-provider-avatar :photo-url="$profile->profilePhotoUrl()" :name="$profile->business_name ?: $provider->name" size="md" :hiring="$profile->is_available_for_hire" />
                <h2 class="q-heading break-words text-2xl"><a href="{{ route('providers.show', $provider) }}">{{ $profile->business_name ?: $provider->name }}</a></h2>
                <p class="q-copy text-sm">{{ $profile->formattedCategories() ?: 'No categories listed yet' }}</p>
                <p class="q-copy text-sm">{{ $profile->locationLine() ?: 'Location not provided' }}</p>
                <x-rating-stars :rating="$profile->average_rating" :count="$profile->reviews_count" size="sm" />
                <x-availability-badge :available="$profile->is_available_for_hire" />
                <a class="q-button-secondary" href="{{ route('providers.show', $provider) }}">View provider</a>
            </article>
        @empty
            <div class="q-panel p-6 md:col-span-2 xl:col-span-3"><h2 class="q-heading text-xl">No providers match your filters.</h2><p class="q-copy mt-3">Try another category or service location, or clear your filters.</p></div>
        @endforelse
    </div>
    {{ $providers->links() }}
</div>
@endsection
