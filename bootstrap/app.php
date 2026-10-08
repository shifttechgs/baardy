<?php

use App\Http\Middleware\RedirectToCanonicalUrl;
use App\Http\Middleware\RememberLeadSource;
use App\Http\Middleware\SetSecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Behind a load balancer or Cloudflare, set TRUSTED_PROXIES (comma
        // separated addresses, or * for all) so the app sees the visitor's
        // real scheme and host.
        $trusted = env('TRUSTED_PROXIES');

        if ($trusted) {
            $middleware->trustProxies(at: $trusted === '*' ? '*' : explode(',', $trusted));
        }

        $middleware->prepend(RedirectToCanonicalUrl::class);

        $middleware->web(append: [
            RememberLeadSource::class,
            SetSecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
