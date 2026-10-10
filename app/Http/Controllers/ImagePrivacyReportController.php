<?php

namespace App\Http\Controllers;

use App\Models\ImagePrivacyReport;
use App\Models\User;
use App\Services\PublicProviderImage;
use App\Support\AccountIdentity;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ImagePrivacyReportController extends Controller
{
    public function create(User $provider, string $imageType, string $imageRef): View
    {
        $path = PublicProviderImage::resolve($provider, $imageType, $imageRef);

        return view('providers.image-report', compact('provider', 'imageType', 'imageRef', 'path'));
    }

    public function store(Request $request, User $provider, string $imageType, string $imageRef): RedirectResponse
    {
        $path = PublicProviderImage::resolve($provider, $imageType, $imageRef);
        if (is_string($request->input('contact_email'))) {
            $request->merge(['contact_email' => AccountIdentity::normalize($request->input('contact_email'))]);
        }
        $data = $request->validate([
            'reason' => ['required', 'string', Rule::in(ImagePrivacyReport::REASONS)],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
            'contact_email' => ['nullable', 'string', 'email', 'max:255'],
            'submission_token' => ['required', 'uuid'],
        ]);
        $actor = $request->user() ? 'user:'.$request->user()->id : 'session:'.$request->session()->getId();
        $actorKey = hash_hmac('sha256', $actor, config('app.key'));
        $submissionKey = hash('sha256', $actorKey.$data['submission_token']);
        $dedupKey = hash('sha256', json_encode([$actorKey, $provider->id, $imageType, $imageRef, $data['reason'], trim($data['description']), (int) floor(now()->timestamp / 600)]));
        $existing = fn () => ImagePrivacyReport::where('submission_key', $submissionKey)->orWhere('dedup_key', $dedupKey);
        if (! $existing()->exists()) {
            $metadata = ['filename' => basename($path), 'provider_name' => $provider->businessProfile->business_name ?: $provider->name];
            try {
                if (Storage::disk('public')->exists($path)) {
                    $metadata['size_bytes'] = Storage::disk('public')->size($path);
                    $metadata['mime_type'] = Storage::disk('public')->mimeType($path);
                }
            } catch (\Throwable $exception) {
                // A concurrent replacement may remove the file; retain the original reference.
                $metadata['file_state'] = 'Unavailable when report was submitted';
            }
            try {
                ImagePrivacyReport::create([
                    'provider_id' => $provider->id, 'provider_name' => $metadata['provider_name'],
                    'image_type' => $imageType, 'image_ref' => $imageRef, 'image_path' => $path, 'image_metadata' => $metadata,
                    'reason' => $data['reason'], 'description' => $data['description'], 'contact_email' => $data['contact_email'] ?? null,
                    'submission_key' => $submissionKey, 'dedup_key' => $dedupKey,
                ]);
            } catch (UniqueConstraintViolationException $exception) {
                if (! $existing()->exists()) {
                    throw $exception;
                }
            }
        }

        return redirect()->route('providers.show', $provider)->with('status', 'Your image privacy report has been received for private administrator review.');
    }
}
