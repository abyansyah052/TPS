<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (config('security.headers.enabled', true)) {
            // X-Frame-Options
            $response->headers->set('X-Frame-Options', config('security.headers.x-frame-options', 'DENY'));
            
            // X-Content-Type-Options
            $response->headers->set('X-Content-Type-Options', config('security.headers.x-content-type-options', 'nosniff'));
            
            // X-XSS-Protection
            $response->headers->set('X-XSS-Protection', config('security.headers.x-xss-protection', '1; mode=block'));
            
            // Referrer Policy
            $response->headers->set('Referrer-Policy', config('security.headers.referrer-policy', 'strict-origin-when-cross-origin'));
            
            // Permissions Policy
            $response->headers->set('Permissions-Policy', config('security.headers.permissions-policy', 'geolocation=(), microphone=(), camera=()'));
            
            // Strict Transport Security (only if HTTPS)
            if ($request->isSecure()) {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            }
            
            // Content Security Policy
            if (config('security.csp.enabled', true)) {
                $cspPolicy = collect(config('security.csp.policy', []))
                    ->map(function ($value, $key) {
                        return "$key $value";
                    })
                    ->implode('; ');
                
                $response->headers->set('Content-Security-Policy', $cspPolicy);
            }
        }

        return $response;
    }
}
