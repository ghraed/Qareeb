<?php

use App\Http\Middleware\EnsureProviderIsPublic;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsSeeker;
use App\Http\Middleware\EnsureUserIsServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'seeker' => EnsureUserIsSeeker::class,
            'public-provider' => EnsureProviderIsPublic::class,
            'service-provider' => EnsureUserIsServiceProvider::class,
        ]);

        $middleware->redirectGuestsTo(fn (Request $request) => $request->routeIs('admin.*')
            ? route('admin.login')
            : route($request->routeIs('seeker.*', 'providers.reviews.*') ? 'seeker.login' : 'business.login'));
        $middleware->redirectUsersTo(fn (Request $request) => route($request->user()->isAdmin()
            ? 'admin.dashboard' : ($request->user()->isSeeker() ? 'seeker.dashboard' : ($request->user()->hasActiveProviderAccount() ? 'business.dashboard' : 'business.account.status'))));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
