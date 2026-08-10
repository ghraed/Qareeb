@extends('layouts.business')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@section('og_title', $seo['title'])
@section('og_description', $seo['description'])
@if ($seo['image'])
    @section('og_image', $seo['image'])
@endif

@section('content')
    <div class="space-y-6">
        @include('providers.partials.hero')

        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                @include('providers.partials.about')
                @include('providers.partials.location')
                @include('providers.partials.reviews')
            </div>

            <div class="space-y-6 xl:sticky xl:top-6 xl:self-start">
                @include('providers.partials.hiring-card')
                @include('providers.partials.work-samples')
            </div>
        </div>
    </div>
@endsection
