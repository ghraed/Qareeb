@extends('layouts.business')
@section('title', 'Private complaint review')
@section('content')
<section class="space-y-5">
    @if ($errors->any())<div class="q-panel p-5" role="alert"><p>Please correct the review fields:</p><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <h1 class="q-heading text-3xl">Private complaint review</h1><a class="q-button-secondary" href="{{ route('admin.dashboard') }}">Admin dashboard</a>
    @forelse ($complaints as $complaint)
        <article class="q-card space-y-4 p-6">
            <h2 class="q-heading text-xl">{{ $complaint->type }} — {{ $complaint->status->label() }}</h2><p class="q-copy">From {{ $complaint->user->name }} · Provider: {{ $complaint->provider?->name ?: 'Not specified' }}</p><p class="q-copy whitespace-pre-line">{{ $complaint->description }}</p>
            <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}" class="space-y-4">
                @csrf @method('PATCH')
                <label class="block font-medium" for="status_{{ $complaint->id }}">Review status</label><select class="q-input" id="status_{{ $complaint->id }}" name="status">@foreach (\App\Enums\ReportStatus::cases() as $status)<option value="{{ $status->value }}" @selected($complaint->status === $status)>{{ $status->label() }}</option>@endforeach</select>
                <label class="block font-medium" for="notes_{{ $complaint->id }}">Internal notes (administrators only)</label><textarea class="q-input" id="notes_{{ $complaint->id }}" name="internal_notes" rows="4" maxlength="5000">{{ $complaint->internal_notes }}</textarea>
                <button class="q-button-primary" type="submit">Save review</button>
            </form>
        </article>
    @empty<p class="q-panel p-6">No complaints to review.</p>@endforelse
    {{ $complaints->links() }}
</section>
@endsection
