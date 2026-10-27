<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PauseSeekerAccessDuringPreregistration
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('qareeb.provider_preregistration')
            && $request->is('seeker', 'seeker/*')
            && ! $request->is('seeker/logout')) {
            if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
                return redirect('/');
            }

            abort(404);
        }

        return $next($request);
    }
}
