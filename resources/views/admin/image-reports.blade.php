@extends('layouts.business')
@section('title', 'Private image privacy reports')
@section('content')
<section class="space-y-5">
    <h1 class="q-page-title">Private image privacy reports</h1><a class="q-button-secondary" href="{{ route('admin.dashboard') }}">Admin dashboard</a>
    @if ($errors->any())<div class="q-panel p-5" role="alert"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @forelse ($reports as $report)
        <article class="q-card space-y-4 p-6">
            <h2 class="q-heading text-xl">{{ $report->provider_name }} — {{ $report->image_type }} image</h2><p class="q-badge">{{ $report->status->label() }}</p>
            <p class="q-copy">{{ $report->reason }}</p><p class="q-copy whitespace-pre-line">{{ $report->description }}</p><p class="q-copy text-sm">Private contact: {{ $report->contact_email ?: 'Not provided' }}</p>
            <p class="q-copy break-all text-sm">Original image: {{ $report->image_metadata['filename'] }} · Reference: {{ $report->image_ref }}</p>
            @php
                $profile = $report->provider?->businessProfile;
                $currentPaths = $profile ? match ($report->image_type) { 'profile' => [$profile->profile_photo], 'brand' => [$profile->brand_photo], default => $profile->work_samples } : [];
            @endphp
            @if ($profile && in_array($report->image_path, $currentPaths, true) && \App\Services\PublicProviderImage::isOwnedPath($profile, $report->image_type, $report->image_path))
                <img src="{{ '/storage/'.$report->image_path }}" alt="Reported image" class="max-h-64 w-full rounded-2xl object-contain">
            @else
                <p class="q-copy">The original image has changed or is no longer listed. The original report reference and metadata are retained.</p>
            @endif
            <form method="POST" action="{{ route('admin.image-reports.update', $report) }}" class="space-y-4">
                @csrf @method('PATCH')
                <label class="block font-medium" for="status_{{ $report->id }}">Review status</label><select class="q-input" id="status_{{ $report->id }}" name="status">@foreach (\App\Enums\ReportStatus::cases() as $status)<option value="{{ $status->value }}" @selected($report->status === $status)>{{ $status->label() }}</option>@endforeach</select>
                <label class="block font-medium" for="notes_{{ $report->id }}">Internal notes</label><textarea class="q-input" id="notes_{{ $report->id }}" name="internal_notes" rows="3">{{ $report->internal_notes }}</textarea><button class="q-button-primary" type="submit">Save review</button>
            </form>
            <form method="POST" action="{{ route('admin.image-reports.remove', $report) }}" class="q-panel space-y-4 p-5">
                @csrf @method('PATCH')
                <label class="block font-medium" for="reason_{{ $report->id }}">Reason for removing the reported image</label><textarea class="q-input" id="reason_{{ $report->id }}" name="removal_reason" minlength="10" maxlength="2000" rows="3" required>{{ $report->removal_reason }}</textarea>
                <p class="q-copy text-sm">This action affects the specific reported image. Replacement images and unrelated files are preserved.</p><button class="q-button-secondary" type="submit">Remove reported image</button>
                @if ($report->removal_outcome)<p class="q-copy">{{ $report->removal_outcome }}</p>@endif
            </form>
        </article>
    @empty<p class="q-panel p-6">No image reports to review.</p>@endforelse
    {{ $reports->links() }}
</section>
@endsection
