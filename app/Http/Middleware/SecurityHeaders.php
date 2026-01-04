<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Add security headers to prevent common attacks
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking attacks (frame embedding)
        $response->header('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->header('X-Content-Type-Options', 'nosniff');

        // Enable XSS protection in older browsers
        $response->header('X-XSS-Protection', '1; mode=block');

        // Referrer Policy - prevent referrer leakage
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy - restrict browser features
        $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // HSTS - Force HTTPS (only in production)
        if (config('app.env') === 'production') {
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // CSP disabled for now - too strict for development
        // Will enable after proper testing
        // $csp = $this->getCSPHeader();
        // $response->header('Content-Security-Policy', $csp);

        return $response;
    }

    /**
     * Get Content Security Policy header value
     * Restrictive but allows necessary resources
     */
    private function getCSPHeader(): string
    {
        $isDev = config('app.env') !== 'production';
        $policies = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://unpkg.com https://cdnjs.cloudflare.com",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: https:",
            "media-src 'self' data: https:",
            "connect-src 'self' https: ws: wss:",
            "frame-src 'none'",
            "object-src 'none'",
        ];

        // Allow Vite dev server in development
        if ($isDev) {
            $policies[] = "script-src-elem 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com http://localhost:5173";
            $policies[] = "style-src-elem 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://unpkg.com https://cdnjs.cloudflare.com http://localhost:5173";
        } else {
            // Production - stricter
            $policies[] = "upgrade-insecure-requests";
        }

        return implode('; ', $policies);
    }
}
