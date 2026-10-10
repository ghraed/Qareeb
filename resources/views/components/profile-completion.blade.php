@props(['profile', 'seeker' => false])
@php
    $percentage = $seeker ? $profile->seekerCompletionPercentage() : $profile->completionPercentage();
    $missing = $seeker ? $profile->missingSeekerCompletionFields() : $profile->missingCompletionFields();
@endphp
<div class="mt-4 space-y-3">
    <p class="q-copy text-sm">{{ $percentage }}% complete</p>
    <progress class="h-3 w-full accent-[#1f3b2e]" value="{{ $percentage }}" max="100" aria-label="Profile completion">{{ $percentage }}%</progress>
    @if ($missing)
        <p class="q-copy text-sm">Missing: {{ implode(', ', $missing) }}.</p>
    @else
        <p class="q-copy text-sm">Your profile is complete.</p>
    @endif
</div>
