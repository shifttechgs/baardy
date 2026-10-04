<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Remembers where a visitor came from, so the lead they may become later
 * says which campaign or site sent them (Lead::capture()).
 *
 * The landing page and referrer are the visit's first touch. UTM tags are
 * taken whenever a link carries them, so a later campaign click wins over an
 * earlier direct visit. Only the public site's page views count: not the
 * admin panel, not form posts.
 */
class RememberLeadSource
{
    public const SESSION_KEY = 'lead_source';

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && ! $request->expectsJson() && ! $request->is('admin', 'admin/*', 'livewire*')) {
            $source = $request->session()->get(self::SESSION_KEY, []);

            if (! isset($source['landing_page'])) {
                $referrer = (string) $request->headers->get('referer');
                $isExternal = $referrer !== '' && parse_url($referrer, PHP_URL_HOST) !== $request->getHost();

                $source['landing_page'] = $request->getRequestUri();
                $source['referrer'] = $isExternal ? $referrer : null;
            }

            if ($request->filled('utm_source') || $request->filled('utm_campaign')) {
                foreach (['utm_source', 'utm_medium', 'utm_campaign'] as $key) {
                    $source[$key] = is_string($request->query($key)) ? $request->query($key) : null;
                }
            }

            $request->session()->put(self::SESSION_KEY, $source);
        }

        return $next($request);
    }
}
