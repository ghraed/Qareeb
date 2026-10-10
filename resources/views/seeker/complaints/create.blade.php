@extends('layouts.business')
@section('title', 'Submit a private complaint')
@section('content')
<section class="q-card mx-auto max-w-2xl space-y-5 p-6 sm:p-8">
    <h1 class="q-heading text-3xl">Submit a private complaint</h1><p class="q-copy">This survey goes to administrators for review. It is separate from public reviews.</p>
    <form method="POST" action="{{ route('seeker.complaints.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="submission_token" value="{{ old('submission_token', (string) Str::uuid()) }}">
        <div class="space-y-2"><label for="type" class="block font-medium">Complaint type</label><select class="q-input" id="type" name="type" required><option value="">Choose a type</option>@foreach (\App\Models\Complaint::TYPES as $type)<option value="{{ $type }}" @selected(old('type') === $type)>{{ $type }}</option>@endforeach</select>@error('type')<p class="text-sm text-rose-700" role="alert">{{ $message }}</p>@enderror</div>
        <div class="space-y-2"><label for="provider_id" class="block font-medium">Provider (optional)</label><select class="q-input" id="provider_id" name="provider_id"><option value="">No specific provider</option>@foreach ($providers as $provider)<option value="{{ $provider->id }}" @selected((string) old('provider_id') === (string) $provider->id)>{{ $provider->businessProfile->business_name ?: $provider->name }}</option>@endforeach</select>@error('provider_id')<p class="text-sm text-rose-700" role="alert">{{ $message }}</p>@enderror</div>
        <div class="space-y-2"><label for="description" class="block font-medium">What happened?</label><textarea class="q-input" id="description" name="description" rows="6" minlength="10" maxlength="3000" required>{{ old('description') }}</textarea>@error('description')<p class="text-sm text-rose-700" role="alert">{{ $message }}</p>@enderror</div>
        @error('submission_token')<p class="text-sm text-rose-700" role="alert">Please refresh the form and try again.</p>@enderror
        <button class="q-button-primary" type="submit">Submit privately</button><a class="q-button-secondary" href="{{ route('seeker.complaints.index') }}">My complaints</a>
    </form>
</section>
@endsection
