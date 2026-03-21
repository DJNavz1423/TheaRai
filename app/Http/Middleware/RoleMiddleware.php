<?php

/* The RoleMiddleware class in PHP checks if the authenticated user has a specific role before allowing
access to a route. */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {   
        if(!auth()->check() || !in_array(auth()->user()->role, explode('|', $role))){
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}
