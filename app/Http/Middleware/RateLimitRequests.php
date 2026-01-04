<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RateLimitRequests
{
    /**
     * Create a new middleware instance.
     */
    public function __construct(protected RateLimiter $limiter)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $limit = '60,1'): Response
    {
        // Parse limit from format "60,1" = 60 requests per 1 minute
        [$requests, $minutes] = explode(',', $limit);

        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, (int) $requests)) {
            return $this->buildResponse($request, (int) $minutes);
        }

        $this->limiter->hit($key, (int) $minutes * 60);

        return $next($request)
            ->header('X-RateLimit-Limit', $requests)
            ->header('X-RateLimit-Remaining', max(0, $requests - $this->limiter->attempts($key)))
            ->header('X-RateLimit-Reset', $this->limiter->resetAfter($key));
    }

    /**
     * Resolve request signature for rate limiting key
     */
    protected function resolveRequestSignature(Request $request): string
    {
        return sha1(implode('|', [
            $request->method(),
            $request->getHost(),
            $request->path(),
            $request->ip(),
        ]));
    }

    /**
     * Create a 'too many requests' response.
     */
    protected function buildResponse(Request $request, int $retryAfter): Response
    {
        $headers = [
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => '0',
        ];

        if ($request->expectsJson()) {
            return response()->json(
                [
                    'message' => 'Terlalu banyak requests. Silakan coba lagi dalam '.$retryAfter.' menit.',
                    'retry_after' => $retryAfter,
                ],
                429,
                $headers,
            );
        }

        return response('Terlalu banyak requests. Silakan coba lagi dalam '.$retryAfter.' menit.', 429, $headers);
    }
}
