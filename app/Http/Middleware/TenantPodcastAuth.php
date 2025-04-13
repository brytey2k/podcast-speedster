<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantPodcastAuth
{
    public function __construct(protected AuthenticateWithBasicAuth $basicAuthMiddleware)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();
        if ($tenant->requiresAuth()) {
            return $this->basicAuthMiddleware->handle($request, $next);
        }

        // If the tenant does not require authentication, just proceed to the next middleware
        return $next($request);
    }
}
