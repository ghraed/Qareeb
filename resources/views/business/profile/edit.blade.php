@extends('layouts.business')

@section('title', 'Edit Business Profile')

@section('content')
    @if ($profile->categories === [])
        <x-alert type="info" message="Choose at least one current work category so Seekers can find your services." class="mb-5" />
    @endif
    @php
        $selectedDistricts = old('state', $profile->state);
        $selectedDistricts = is_array($selectedDistricts) ? array_values($selectedDistricts) : (filled($selectedDistricts) ? [$selectedDistricts] : []);

        $selectedCities = old('city', $profile->city);
        $selectedCities = is_array($selectedCities) ? array_values($selectedCities) : (filled($selectedCities) ? [$selectedCities] : []);

        $selectedWorkCategories = old('categories', $profile->categories);
        $selectedWorkCategories = is_array($selectedWorkCategories) ? array_values($selectedWorkCategories) : (filled($selectedWorkCategories) ? [$selectedWorkCategories] : []);
    @endphp

    <div
        class="min-w-0 space-y-6"
        x-data="businessProfileEditForm(@js([
            'available' => (bool) old('is_available_for_hire', $profile->is_available_for_hire),
            'districtCities' => \App\Models\BusinessProfile::LEBANON_DISTRICT_CITIES,
            'districts' => \App\Models\BusinessProfile::LEBANON_DISTRICTS,
            'selectedDistricts' => $selectedDistricts,
            'selectedCities' => $selectedCities,
        ]))"
        x-init="init()"
    >
        <form id="hiring-availability-toggle-form" method="POST" action="{{ route('business.profile.hiring-availability') }}" class="hidden">
            @csrf
            @method('PATCH')
            <input type="hidden" name="is_available_for_hire" value="{{ $profile->is_available_for_hire ? 0 : 1 }}">
        </form>

        <section class="q-section">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="q-eyebrow">Profile management</p>
                    <h1 class="q-heading mt-3 text-4xl sm:text-5xl">Edit your business profile</h1>
                    <p class="q-copy mt-3 max-w-2xl text-base leading-7">Update your marketplace identity, manual location, and hiring availability with the same Qareeb design language used across the provider portal.</p>
                </div>
                <span class="q-badge">{{ $profileCompletion }}% complete</span>
            </div>
            <x-profile-completion :profile="$profile" />
        </section>

        <div class="grid min-w-0 gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)]">
            <section class="q-section min-w-0">
                <form method="POST" action="{{ route('business.profile.update') }}" class="min-w-0 space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="q-panel p-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="q-heading text-2xl">Business identity</h2>
                                <p class="q-copy mt-1 text-sm">This section controls how your provider card, marketplace profile, and contact block appear.</p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-5">
                            <x-form-input label="Business name" name="business_name" :value="$profile->business_name" placeholder="Qareeb Electric Services" x-on:input="updateAll('[data-business-name-preview]', $event.target.value, @js($user->name))" />
                            <x-form-input label="Business email" name="business_email_display" type="email" :value="$user->email" disabled />
                            <x-form-input label="Phone" name="phone" :value="$profile->phone" placeholder="+961 70 000 000" x-on:input="updateText('phonePreview', $event.target.value, 'Not listed yet')" />

                            <div class="space-y-2">
                                <label for="description" class="block text-sm font-medium text-[#314236]">Description</label>
                                <textarea id="description" name="description" rows="6" x-on:input="updateText('descriptionPreview', $event.target.value, 'Add a short description to show customers what you offer.')" class="q-input border-[#ddd1b8] focus:border-[#6c8a5b] focus:ring-[#e8dcc2]" placeholder="Describe your services, specialties, and business style.">{{ old('description', $profile->description) }}</textarea>
                                @error('description')
                                    <p class="text-sm font-medium text-rose-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-[#314236]">Work categories</label>
                                    <p class="mt-1 text-xs text-[#6b6b6b]">Choose the domains you want customers to associate with your business.</p>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    @foreach (\App\Models\BusinessProfile::WORK_CATEGORIES as $workCategory)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-[1rem] border border-[#ddd1b8] bg-[#fffdf8] px-4 py-3 text-sm text-[#314236] transition hover:border-[#cdbf9f] hover:bg-[#f7f2e8]">
                                            <input
                                                type="checkbox"
                                                name="categories[]"
                                                value="{{ $workCategory }}"
                                                @checked(in_array($workCategory, $selectedWorkCategories, true))
                                                class="h-4 w-4 rounded border-[#cdbf9f] text-[#6c8a5b] focus:ring-[#e8dcc2]"
                                            >
                                            <span>{{ $workCategory }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                @error('categories')
                                    <p class="text-sm font-medium text-rose-700">{{ $message }}</p>
                                @enderror
                                @error('categories.*')
                                    <p class="text-sm font-medium text-rose-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="q-panel p-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="q-heading text-2xl">Location</h2>
                                <p class="q-copy mt-1 text-sm">These fields are displayed publicly as plain location details. No maps integration is required.</p>
                            </div>
                        </div>

                        <div class="mt-6 space-y-5">
                            <x-form-input label="Address" name="address" :value="$profile->address" placeholder="Main street, building 12" x-on:input="updateLocation()" />

                            <div class="grid min-w-0 gap-4 sm:grid-cols-2">
                                <div class="min-w-0 space-y-2">
                                    <label class="block text-sm font-medium text-[#314236]">Districts in Lebanon</label>
                                    <div class="relative min-w-0" x-data="{ open: false }">
                                        <button
                                            type="button"
                                            x-on:click="open = ! open"
                                            class="q-input flex min-h-12 w-full min-w-0 items-center gap-3 border-[#ddd1b8] px-4 text-left focus:border-[#6c8a5b] focus:ring-[#e8dcc2]"
                                        >
                                            <span class="min-w-0 flex-1 truncate" x-text="selectedDistrictsLabel()"></span>
                                            <span class="shrink-0 text-xs text-[#6b6b6b]" x-text="selectedDistricts.length ? `${selectedDistricts.length} selected` : ''"></span>
                                        </button>

                                        <div
                                            x-cloak
                                            x-show="open"
                                            x-on:click.outside="open = false"
                                            class="absolute inset-x-0 z-20 mt-2 max-w-full rounded-[1.15rem] border border-[#ddd1b8] bg-white p-2 shadow-lg"
                                            style="height: 200px; overflow-y: scroll; overflow-x: hidden; z-index: 1;"
                                        >
                                            <div>
                                                <template x-for="district in districts" :key="district">
                                                    <label class="flex min-w-0 cursor-pointer items-center gap-3 rounded-xl px-3 py-2 text-sm text-[#314236] transition hover:bg-[#f7f2e8]">
                                                        <input
                                                            type="checkbox"
                                                            :checked="selectedDistricts.includes(district)"
                                                            x-on:change="toggleDistrict(district)"
                                                            class="h-4 w-4 shrink-0 rounded border-[#cdbf9f] text-[#6c8a5b] focus:ring-[#e8dcc2]"
                                                        >
                                                        <span class="min-w-0 break-words" x-text="district"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <template x-for="district in selectedDistricts" :key="`state-${district}`">
                                        <input type="hidden" name="state[]" :value="district">
                                    </template>

                                    @error('state')
                                        <p class="text-sm font-medium text-rose-700">{{ $message }}</p>
                                    @enderror
                                    @error('state.*')
                                        <p class="text-sm font-medium text-rose-700">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="min-w-0 space-y-2">
                                    <label class="block text-sm font-medium text-[#314236]">Cities</label>
                                    <div class="relative min-w-0" x-data="{ open: false }">
                                        <button
                                            type="button"
                                            x-on:click="if (filteredCities().length) open = ! open"
                                            :disabled="filteredCities().length === 0"
                                            class="q-input flex min-h-12 w-full min-w-0 items-center gap-3 border-[#ddd1b8] px-4 text-left focus:border-[#6c8a5b] focus:ring-[#e8dcc2] disabled:cursor-not-allowed disabled:bg-[#f7f2e8]"
                                        >
                                            <span class="min-w-0 flex-1 truncate" x-text="selectedCitiesLabel()"></span>
                                            <span class="shrink-0 text-xs text-[#6b6b6b]" x-text="selectedCities.length ? `${selectedCities.length} selected` : ''"></span>
                                        </button>

                                        <div
                                            x-cloak
                                            x-show="open && filteredCities().length > 0"
                                            x-on:click.outside="open = false"
                                            class="absolute inset-x-0 z-20 mt-2 max-w-full rounded-[1.15rem] border border-[#ddd1b8] bg-white p-2 shadow-lg"
                                            style="height: 200px; overflow-y: scroll; overflow-x: hidden; z-index: 1;"
                                        >
                                            <div>
                                                <label class="flex min-w-0 cursor-pointer items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-[#314236] transition hover:bg-[#f7f2e8]">
                                                    <input
                                                        type="checkbox"
                                                        :checked="areAllCitiesSelected()"
                                                        x-on:change="toggleAllCities()"
                                                        class="h-4 w-4 shrink-0 rounded border-[#cdbf9f] text-[#6c8a5b] focus:ring-[#e8dcc2]"
                                                    >
                                                    <span class="min-w-0 break-words">All cities in selected districts</span>
                                                </label>

                                                <template x-for="cityOption in filteredCities()" :key="cityOption">
                                                    <label class="flex min-w-0 cursor-pointer items-center gap-3 rounded-xl px-3 py-2 text-sm text-[#314236] transition hover:bg-[#f7f2e8]">
                                                        <input
                                                            type="checkbox"
                                                            :checked="selectedCities.includes(cityOption)"
                                                            x-on:change="toggleCity(cityOption)"
                                                            class="h-4 w-4 shrink-0 rounded border-[#cdbf9f] text-[#6c8a5b] focus:ring-[#e8dcc2]"
                                                        >
                                                        <span class="min-w-0 break-words" x-text="cityOption"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <template x-for="city in selectedCities" :key="`city-${city}`">
                                        <input type="hidden" name="city[]" :value="city">
                                    </template>

                                    <p class="text-xs text-[#6b6b6b]">Choose one or more districts first. The city dropdown is filled immediately from those districts.</p>
                                    @error('city')
                                        <p class="text-sm font-medium text-rose-700">{{ $message }}</p>
                                    @enderror
                                    @error('city.*')
                                        <p class="text-sm font-medium text-rose-700">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <x-form-input label="Postal code" name="postal_code" :value="$profile->postal_code" placeholder="1107" x-on:input="updateLocation()" />
                        </div>
                    </div>

                    <div class="q-panel p-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="q-heading text-2xl">Availability</h2>
                                <p class="q-copy mt-1 text-sm">Control whether customers see you as available for new work.</p>
                            </div>
                            <span class="q-badge" x-text="available ? 'Available for hire' : 'Currently unavailable'"></span>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center gap-3">
                            <button
                                type="submit"
                                form="hiring-availability-toggle-form"
                                class="{{ $profile->is_available_for_hire ? 'inline-flex items-center justify-center rounded-[1.15rem] border border-[#f0c9c9] bg-[#fff5f5] px-5 py-3 text-sm font-semibold text-[#9a3d3d] transition hover:bg-[#ffecec]' : 'q-button-secondary' }}"
                            >
                                {{ $profile->is_available_for_hire ? 'Stop hiring now' : 'Start hiring now' }}
                            </button>
                            <p class="text-sm text-[#6b6b6b]">
                                {{ $profile->is_available_for_hire ? 'This removes the hiring badge from your public profile immediately.' : 'This shows the hiring badge on your public profile immediately.' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="q-button-primary">Save profile</button>
                        <a href="{{ route('business.dashboard') }}" class="q-button-secondary">Back to dashboard</a>
                    </div>
                </form>
            </section>

            <div class="min-w-0 space-y-6 xl:sticky xl:top-6 xl:self-start">
                <section class="q-section overflow-hidden p-0">
                    <div class="relative h-52 bg-[#f1e8d4]">
                        @if ($profile->brandPhotoUrl())
                            <img src="{{ $profile->brandPhotoUrl() }}" alt="Current brand photo" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center bg-linear-to-r from-[#1f3b2e] via-[#6c8a5b] to-[#e8dcc2] px-6 text-center text-white">
                                <div>
                                    <p class="text-sm font-semibold tracking-[0.25em] uppercase text-white/80">Live provider preview</p>
                                    <p class="mt-2 break-words font-serif text-2xl sm:text-3xl" data-business-name-preview>{{ old('business_name', $profile->business_name ?: $user->name) }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 sm:left-6 sm:translate-x-0">
                            <x-profile-avatar :profile="$profile" :name="$profile->business_name ?: $user->name" size="lg" />
                        </div>
                    </div>

                    <div class="px-4 pb-6 pt-16 sm:px-6">
                        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row">
                            <div>
                                <p class="q-eyebrow">Marketplace preview</p>
                                <h3 class="q-heading mt-2 break-words text-2xl sm:text-3xl" data-business-name-preview>{{ old('business_name', $profile->business_name ?: $user->name) }}</h3>
                                <p class="q-copy mt-2 text-sm" x-ref="businessEmailPreview">{{ $user->email ?: ($user->username ?: 'Email not provided') }}</p>
                            </div>
                            <span class="q-badge text-xs" x-text="available ? 'Available for hire' : 'Unavailable'"></span>
                        </div>

                        <div class="mt-4">
                            <x-rating-stars :rating="$profile->average_rating" :count="$profile->reviews_count" />
                        </div>

                        <div class="mt-5 space-y-3">
                            <div class="q-panel p-4">
                                <p class="text-sm font-medium text-[#6b6b6b]">Phone</p>
                                <p class="mt-1 text-sm font-semibold text-[#1f3b2e]" x-ref="phonePreview">{{ old('phone', $profile->phone) ?: 'Not listed yet' }}</p>
                            </div>
                            <div class="q-panel p-4">
                                <p class="text-sm font-medium text-[#6b6b6b]">Description</p>
                                <p class="q-copy mt-1 text-sm leading-6" x-ref="descriptionPreview">{{ old('description', $profile->description) ?: 'Add a short description to show customers what you offer.' }}</p>
                            </div>
                            <div class="q-panel p-4">
                                <p class="text-sm font-medium text-[#6b6b6b]">Work categories</p>
                                <p class="q-copy mt-1 text-sm leading-6">{{ $selectedWorkCategories !== [] ? implode(', ', $selectedWorkCategories) : 'No work categories selected yet.' }}</p>
                            </div>
                            <div class="q-panel p-4">
                                <p class="text-sm font-medium text-[#6b6b6b]">Location preview</p>
                                <div class="q-copy mt-1 space-y-1 text-sm">
                                    <p class="break-words" x-ref="locationAddress" @style(old('address', $profile->address) ? '' : 'display: none;')>{{ old('address', $profile->address) }}</p>
                                    <p class="break-words" x-ref="locationLine" @style(collect([$profile->formattedCities(), $profile->formattedDistricts(), old('postal_code', $profile->postal_code)])->filter()->isNotEmpty() ? '' : 'display: none;')>{{ collect([implode(', ', $selectedCities), implode(', ', $selectedDistricts), old('postal_code', $profile->postal_code)])->filter()->implode(', ') }}</p>
                                    <p x-ref="locationEmpty" @style(old('address', $profile->address) || collect([$profile->formattedCities(), $profile->formattedDistricts(), old('postal_code', $profile->postal_code)])->filter()->isNotEmpty() ? 'display: none;' : '')>No location details added yet.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <x-image-upload
                    label="Profile photo"
                    :current-url="$profile->profilePhotoUrl()"
                    empty-label="Upload a circular identity photo for your provider card."
                    :form-action="route('business.profile.profile-photo.store')"
                    :delete-action="route('business.profile.profile-photo.destroy')"
                />

                <x-image-upload
                    label="Brand cover photo"
                    :current-url="$profile->brandPhotoUrl()"
                    empty-label="Upload a wide brand image for your public provider page."
                    :form-action="route('business.profile.brand-photo.store')"
                    :delete-action="route('business.profile.brand-photo.destroy')"
                />

                <x-work-samples-manager
                    :profile="$profile"
                    :form-action="route('business.profile.work-samples.store')"
                    delete-route-name="business.profile.work-samples.destroy"
                />
            </div>
        </div>
    </div>

    <script>
        function businessProfileEditForm(config) {
            return {
                available: config.available,
                districtCities: config.districtCities,
                districts: config.districts,
                selectedDistricts: config.selectedDistricts ?? [],
                selectedCities: config.selectedCities ?? [],
                init() {
                    this.syncSelectedCities();
                    this.updateLocation();
                },
                updateText(refName, value, fallback = "") {
                    if (! this.$refs[refName]) return;
                    const normalized = typeof value === "string" ? value.trim() : "";
                    this.$refs[refName].textContent = normalized !== "" ? value : fallback;
                },
                updateAll(selector, value, fallback = "") {
                    const normalized = typeof value === "string" ? value.trim() : "";
                    const nextValue = normalized !== "" ? value : fallback;

                    this.$root.querySelectorAll(selector).forEach((element) => {
                        element.textContent = nextValue;
                    });
                },
                filteredCities() {
                    return [...new Set(
                        this.selectedDistricts.flatMap((district) => this.districtCities[district] ?? [])
                    )];
                },
                selectedDistrictsLabel() {
                    return this.selectedDistricts.length ? this.selectedDistricts.join(", ") : "Select districts";
                },
                selectedCitiesLabel() {
                    if (this.filteredCities().length === 0) {
                        return "Select districts first";
                    }

                    return this.selectedCities.length ? this.selectedCities.join(", ") : "Select cities";
                },
                areAllCitiesSelected() {
                    const availableCities = this.filteredCities();

                    return availableCities.length > 0 && availableCities.every((city) => this.selectedCities.includes(city));
                },
                syncSelectedCities() {
                    const availableCities = this.filteredCities();

                    if (availableCities.length === 0) {
                        this.selectedCities = [];
                        return;
                    }

                    this.selectedCities = this.selectedCities.filter((city) => availableCities.includes(city));
                },
                toggleDistrict(district) {
                    if (this.selectedDistricts.includes(district)) {
                        this.selectedDistricts = this.selectedDistricts.filter((item) => item !== district);
                    } else {
                        this.selectedDistricts = [...this.selectedDistricts, district];
                    }

                    this.syncSelectedCities();
                    this.updateLocation();
                },
                toggleCity(city) {
                    if (this.selectedCities.includes(city)) {
                        this.selectedCities = this.selectedCities.filter((item) => item !== city);
                    } else {
                        this.selectedCities = [...this.selectedCities, city];
                    }

                    this.updateLocation();
                },
                toggleAllCities() {
                    this.selectedCities = this.areAllCitiesSelected() ? [] : [...this.filteredCities()];
                    this.updateLocation();
                },
                updateLocation() {
                    const address = this.$root.querySelector("[name=address]")?.value?.trim() ?? "";
                    const postalCode = this.$root.querySelector("[name=postal_code]")?.value?.trim() ?? "";
                    const city = this.selectedCities.join(", ");
                    const state = this.selectedDistricts.join(", ");
                    const locationLine = [city, state, postalCode].filter(Boolean).join(", ");

                    if (this.$refs.locationAddress) {
                        this.$refs.locationAddress.textContent = address;
                        this.$refs.locationAddress.style.display = address ? "" : "none";
                    }

                    if (this.$refs.locationLine) {
                        this.$refs.locationLine.textContent = locationLine;
                        this.$refs.locationLine.style.display = locationLine ? "" : "none";
                    }

                    if (this.$refs.locationEmpty) {
                        this.$refs.locationEmpty.style.display = ! address && ! locationLine ? "" : "none";
                    }
                }
            };
        }
    </script>
@endsection
