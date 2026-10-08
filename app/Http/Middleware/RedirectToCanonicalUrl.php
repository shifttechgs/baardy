<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * In production, sends every other address of the site (no www, plain http)
 * to the one in APP_URL with a permanent redirect.
 *
 * Every generated asset URL already points at APP_URL, so a page opened on
 * another address loads its stylesheet and fonts cross-origin and the browser
 * blocks the fonts: the site and the admin panel come up unstyled.
 *
 * The scheme is only corrected when no proxy header is present, so a proxy
 * that ends TLS without being trusted cannot cause a redirect loop.
 */
class RedirectToCanonicalUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $canonical = (string) config('app.url');

        if (! app()->isProduction() || ! str_starts_with($canonical, 'https://') || $request->is('up')) {
            return $next($request);
        }

        $wrongHost = strcasecmp($request->getHost(), (string) parse_url($canonical, PHP_URL_HOST)) !== 0;
        $plainHttp = ! $request->isSecure() && ! $request->headers->has('X-Forwarded-Proto');

        if ($wrongHost || $plainHttp) {
            return redirect()->away(rtrim($canonical, '/').$request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
