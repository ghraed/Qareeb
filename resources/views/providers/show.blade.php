@extends('layouts.business')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@section('og_title', $seo['title'])
@section('og_description', $seo['description'])
@if ($seo['image'])
    @section('og_image', $seo['image'])
@endif

@section('content')
    <div class="mx-auto w-full min-w-0 space-y-6">
        @include('providers.partials.hero')

        <div class="grid min-w-0 grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
            <div class="min-w-0 space-y-6">
                @include('providers.partials.about')
                @include('providers.partials.location')
                @include('providers.partials.reviews')
            </div>

            <div class="min-w-0 space-y-6 xl:sticky xl:top-6 xl:self-start">
                @include('providers.partials.hiring-card')
                @include('providers.partials.work-samples')
            </div>
        </div>
    </div>
@endsection
