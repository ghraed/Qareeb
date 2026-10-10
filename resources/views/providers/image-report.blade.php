@extends('layouts.business')
@section('title', 'Report image privacy concern')
@section('content')
<section class="q-card mx-auto max-w-2xl space-y-5 p-6 sm:p-8">
    <h1 class="q-heading text-3xl">Report an image privacy concern</h1><p class="q-copy">Your report goes privately to administrators. Submitting a report does not automatically remove an image.</p>
    <img src="{{ '/storage/'.$path }}" alt="Reported {{ $imageType }} image" class="max-h-72 w-full rounded-2xl object-contain">
    <form method="POST" action="{{ route('providers.images.report.store', ['provider' => $provider, 'imageType' => $imageType, 'imageRef' => $imageRef]) }}" class="space-y-5">
        @csrf
        <input type="hidden" name="submission_token" value="{{ old('submission_token', (string) Str::uuid()) }}">
        <div class="space-y-2"><label for="reason" class="block font-medium">Privacy concern</label><select class="q-input" id="reason" name="reason" required><option value="">Choose a reason</option>@foreach (\App\Models\ImagePrivacyReport::REASONS as $reason)<option value="{{ $reason }}" @selected(old('reason') === $reason)>{{ $reason }}</option>@endforeach</select>@error('reason')<p class="text-sm text-rose-700" role="alert">{{ $message }}</p>@enderror</div>
        <div class="space-y-2"><label for="description" class="block font-medium">Describe the concern</label><textarea class="q-input" id="description" name="description" rows="5" minlength="10" maxlength="3000" required>{{ old('description') }}</textarea>@error('description')<p class="text-sm text-rose-700" role="alert">{{ $message }}</p>@enderror</div>
        <x-form-input label="Contact email (optional, private)" name="contact_email" type="email" autocomplete="email" />
        @error('submission_token')<p class="text-sm text-rose-700" role="alert">Please refresh this form and try again.</p>@enderror
        <button class="q-button-primary" type="submit">Send private report</button><a class="q-button-secondary" href="{{ route('providers.show', $provider) }}">Back to provider</a>
    </form>
</section>
@endsection
