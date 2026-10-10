<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProviderIsPublic
{
    public function handle(Request $request, Closure $next): Response
    {
        $provider = $request->route('provider');
        abort_unless($provider instanceof User && $provider->hasActiveProviderAccount() && $provider->businessProfile !== null, 404);

        return $next($request);
    }
}
