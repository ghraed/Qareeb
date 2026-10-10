@props(['provider', 'type', 'path'])
@if (is_string($path) && \App\Services\PublicProviderImage::isOwnedPath($provider->businessProfile, $type, $path))
    <a {{ $attributes->class('q-button-secondary text-sm') }} href="{{ route('providers.images.report', ['provider' => $provider, 'imageType' => $type, 'imageRef' => hash('sha256', $path)]) }}">Report {{ match ($type) { 'profile' => 'profile', 'brand' => 'cover', default => 'gallery' } }} image</a>
@endif
