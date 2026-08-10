<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBusinessProfileRequest;
use App\Http\Requests\UploadBusinessPhotoRequest;
use App\Http\Requests\UploadBusinessWorkSampleRequest;
use App\Models\BusinessProfile;
use App\Services\BusinessPhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessProfileController extends Controller
{
    public function show(): RedirectResponse
    {
        return redirect()->route('business.profile.edit');
    }

    public function edit(): View
    {
        $user = request()->user()->loadMissing('businessProfile');

        return view('business.profile.edit', [
            'user' => $user,
            'profile' => $user->businessProfile,
            'profileCompletion' => $user->businessProfile->completionPercentage(),
        ]);
    }

    public function update(UpdateBusinessProfileRequest $request): RedirectResponse
    {
        $profile = $request->user()->businessProfile;
        $districts = collect($request->input('state', []))
            ->filter(fn (mixed $district): bool => is_string($district) && filled(trim($district)))
            ->map(fn (string $district): string => trim($district))
            ->values()
            ->all();
        $availableCities = \App\Models\BusinessProfile::citiesForDistricts($districts);
        $cities = collect($request->input('city', []))
            ->filter(fn (mixed $city): bool => is_string($city) && filled(trim($city)))
            ->map(fn (string $city): string => trim($city))
            ->when(
                fn ($collection) => $collection->contains('__all__'),
                fn ($collection) => collect($availableCities),
                fn ($collection) => $collection->intersect($availableCities)->values()
            )
            ->values()
            ->all();
        $categories = collect($request->input('categories', []))
            ->filter(fn (mixed $category): bool => is_string($category) && filled(trim($category)))
            ->map(fn (string $category): string => trim($category))
            ->unique()
            ->values()
            ->all();

        $profile->update([
            'business_name' => $request->string('business_name')->toString() ?: null,
            'business_email' => $request->user()->email,
            'phone' => $request->string('phone')->toString() ?: null,
            'description' => $request->string('description')->toString() ?: null,
            'categories' => $categories,
            'address' => $request->string('address')->toString() ?: null,
            'city' => $cities,
            'state' => $districts,
            'country' => null,
            'postal_code' => $request->string('postal_code')->toString() ?: null,
            'latitude' => null,
            'longitude' => null,
            'is_available_for_hire' => $request->boolean('is_available_for_hire'),
        ]);

        return redirect()
            ->route('business.profile.edit')
            ->with('status', 'Your business profile has been updated.');
    }

    public function updateHiringAvailability(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'is_available_for_hire' => ['required', 'boolean'],
        ]);

        $isAvailable = (bool) $validated['is_available_for_hire'];

        $request->user()->businessProfile->update([
            'is_available_for_hire' => $isAvailable,
        ]);

        return back()->with(
            'status',
            $isAvailable ? 'You are now marked as available for hire.' : 'You are no longer marked as available for hire.'
        );
    }

    public function updateProfilePhoto(UploadBusinessPhotoRequest $request, BusinessPhotoService $photoService): RedirectResponse
    {
        $photoService->updateProfilePhoto($request->user()->businessProfile, $request->file('photo'));

        return back()->with('status', 'Profile photo updated.');
    }

    public function updateBrandPhoto(UploadBusinessPhotoRequest $request, BusinessPhotoService $photoService): RedirectResponse
    {
        $photoService->updateBrandPhoto($request->user()->businessProfile, $request->file('photo'));

        return back()->with('status', 'Brand photo updated.');
    }

    public function deleteProfilePhoto(BusinessPhotoService $photoService): RedirectResponse
    {
        $photoService->deleteProfilePhoto(request()->user()->businessProfile);

        return back()->with('status', 'Profile photo removed.');
    }

    public function deleteBrandPhoto(BusinessPhotoService $photoService): RedirectResponse
    {
        $photoService->deleteBrandPhoto(request()->user()->businessProfile);

        return back()->with('status', 'Brand photo removed.');
    }

    public function addWorkSample(UploadBusinessWorkSampleRequest $request, BusinessPhotoService $photoService): RedirectResponse
    {
        $photoService->addWorkSamples($request->user()->businessProfile, $request->file('photos', []));

        return back()->with('status', 'Work sample images uploaded.');
    }

    public function deleteWorkSample(Request $request, BusinessPhotoService $photoService, int $index): RedirectResponse
    {
        abort_unless($index >= 0 && $index < BusinessProfile::MAX_WORK_SAMPLES, 404);

        $photoService->deleteWorkSample($request->user()->businessProfile, $index);

        return back()->with('status', 'Work sample image removed.');
    }
}
