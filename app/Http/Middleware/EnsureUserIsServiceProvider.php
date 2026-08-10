<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsServiceProvider
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $mode = 'active'): Response|RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! $user->isServiceProvider()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if ($mode !== 'any' && ! $user->hasActiveProviderAccount()) {
            return redirect()->route('business.account.status');
        }

        return $next($request);
    }
}
