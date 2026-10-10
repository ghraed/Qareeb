<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Models\ImagePrivacyReport;
use App\Services\BusinessPhotoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminImagePrivacyReportController extends Controller
{
    public function index(): View
    {
        return view('admin.image-reports', ['reports' => ImagePrivacyReport::with('provider.businessProfile')->latest()->paginate(20)]);
    }

    public function update(Request $request, ImagePrivacyReport $report): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::enum(ReportStatus::class)], 'internal_notes' => ['nullable', 'string', 'max:5000']]);
        $report->update([...$data, 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);

        return back()->with('status', 'Image report review saved.');
    }

    public function removeImage(Request $request, ImagePrivacyReport $report, BusinessPhotoService $photos): RedirectResponse
    {
        $data = $request->validate(['removal_reason' => ['required', 'string', 'min:10', 'max:2000']]);
        // Persist the authorization/reason before making any destructive image change.
        if (! $report->update([...$data, 'status' => ReportStatus::InReview, 'removal_requested_by' => $request->user()->id, 'removal_requested_at' => now()])) {
            return back()->withErrors(['removal_reason' => 'Removal authorization could not be recorded.']);
        }
        $profile = $report->provider?->businessProfile;
        try {
            if (! hash_equals($report->image_ref, hash('sha256', $report->image_path))) {
                throw ValidationException::withMessages(['removal_reason' => 'The original image reference does not match.']);
            }
            $removedCurrent = $profile ? $photos->removeReportedImage($profile, $report->image_type, $report->image_path) : false;
            $report->update([
                'status' => ReportStatus::Resolved, 'reviewed_by' => $request->user()->id, 'reviewed_at' => now(),
                'removal_outcome' => $removedCurrent ? 'Reported image removed' : 'Original image already changed or no longer listed',
            ]);
        } catch (\Throwable $exception) {
            report($exception);
            $report->update(['removal_outcome' => 'Removal failed; administrator follow-up required']);

            return back()->withErrors(['removal_reason' => 'The reported image could not be removed safely. The report remains in review.']);
        }

        return back()->with('status', $report->removal_outcome);
    }
}
