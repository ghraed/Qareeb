<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBusinessProfileRequest;
use App\Http\Requests\UploadBusinessPhotoRequest;
use App\Http\Requests\UploadBusinessWorkSampleRequest;
use App\Models\BusinessProfile;
use App\Services\BusinessPhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BusinessProfileController extends Controller
{
    public function show(): RedirectResponse
    {
        return redirect()->route('business.profile.edit');
    }

    public function edit(): View
    {
        $user = request()->user()->load('businessProfile');
        if (! $user->businessProfile) {
            $user->setRelation('businessProfile', $user->businessProfile()->firstOrCreate([], ['business_email' => $user->email, 'is_available_for_hire' => false]));
        }

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
        $availableCities = BusinessProfile::citiesForDistricts($districts);
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
            ...($request->has('is_available_for_hire') ? ['is_available_for_hire' => $request->boolean('is_available_for_hire')] : []),
        ]);

        return redirect()
            ->route('business.profile.edit')
            ->with('status', 'Your business profile has been updated.');
    }

    public function updateHiringAvailability(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'is_available_for_hire' => ['required', 'boolean'],
        ]);

        $isAvailable = (bool) $validated['is_available_for_hire'];

        try {
            if (! $request->user()->businessProfile->update(['is_available_for_hire' => $isAvailable])) {
                throw new \RuntimeException('Availability save failed.');
            }
        } catch (\Throwable $exception) {
            report($exception);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Availability could not be saved. Please try again.'], 500);
            }

            return back()->withErrors(['is_available_for_hire' => 'Availability could not be saved. Please try again.']);
        }

        if ($request->expectsJson()) {
            return response()->json(['is_available_for_hire' => $isAvailable]);
        }

        return back()->with(
            'status',
            $isAvailable ? 'You are now marked as available for hire.' : 'You are no longer marked as available for hire.'
        );
    }

    public function updateProfilePhoto(UploadBusinessPhotoRequest $request, BusinessPhotoService $photoService): RedirectResponse
    {
        return $this->saveImages(fn () => $photoService->updateProfilePhoto($request->user()->businessProfile, $request->file('photo')), 'Profile photo updated.', 'photo');
    }

    public function updateBrandPhoto(UploadBusinessPhotoRequest $request, BusinessPhotoService $photoService): RedirectResponse
    {
        return $this->saveImages(fn () => $photoService->updateBrandPhoto($request->user()->businessProfile, $request->file('photo')), 'Brand photo updated.', 'photo');
    }

    public function deleteProfilePhoto(BusinessPhotoService $photoService): RedirectResponse
    {
        return $this->saveImages(fn () => $photoService->deleteProfilePhoto(request()->user()->businessProfile), 'Profile photo removed.', 'photo');
    }

    public function deleteBrandPhoto(BusinessPhotoService $photoService): RedirectResponse
    {
        return $this->saveImages(fn () => $photoService->deleteBrandPhoto(request()->user()->businessProfile), 'Brand photo removed.', 'photo');
    }

    public function addWorkSample(UploadBusinessWorkSampleRequest $request, BusinessPhotoService $photoService): RedirectResponse
    {
        return $this->saveImages(fn () => $photoService->addWorkSamples($request->user()->businessProfile, $request->file('photos', [])), 'Work sample images uploaded.', 'photos');
    }

    public function deleteWorkSample(Request $request, BusinessPhotoService $photoService, int $index): RedirectResponse
    {
        abort_unless($index >= 0 && $index < BusinessProfile::MAX_WORK_SAMPLES, 404);

        $request->validate(['image_ref' => ['nullable', 'string', 'size:64', 'regex:/\A[a-f0-9]+\z/']]);

        return $this->saveImages(fn () => $photoService->deleteWorkSample($request->user()->businessProfile, $index, $request->input('image_ref')), 'Work sample image removed.', 'photos');
    }

    private function saveImages(callable $operation, string $message, string $field): RedirectResponse
    {
        try {
            $operation();
        } catch (ValidationException|HttpException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors([$field => 'The image change could not be saved. Please try again.']);
        }

        return back()->with('status', $message);
    }
}
