@props(['status'])

@php
    $colors = match ($status) {
        \App\Enums\ReportStatus::Open => 'border-sky-200 bg-sky-50 text-sky-800',
        \App\Enums\ReportStatus::InReview => 'border-amber-200 bg-amber-50 text-amber-800',
        \App\Enums\ReportStatus::Resolved => 'border-emerald-200 bg-emerald-50 text-emerald-800',
    };
@endphp

<span {{ $attributes->class("inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold {$colors}") }}>Status: {{ $status->label() }}</span>
