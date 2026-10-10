@props(['profile'])
@php($percentage = $profile->completionPercentage())
<div class="mt-4 space-y-3">
    <p class="q-copy text-sm">{{ $percentage }}% complete</p>
    <progress class="h-3 w-full accent-[#1f3b2e]" value="{{ $percentage }}" max="100" aria-label="Profile completion">{{ $percentage }}%</progress>
    @if ($profile->missingCompletionFields())
        <p class="q-copy text-sm">Missing: {{ implode(', ', $profile->missingCompletionFields()) }}.</p>
    @else
        <p class="q-copy text-sm">Your profile is complete.</p>
    @endif
</div>
