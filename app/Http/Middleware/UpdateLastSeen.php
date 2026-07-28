<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
     public function handle(Request $request, Closure $next): Response{
        if (auth()->check()) {
            DB::table('laravel.users')
                ->where('id', auth()->id())
                ->where(function ($query) {
                    $query->whereNull('last_seen_at')
                          ->orWhere('last_seen_at', '<', now()->subMinute());
                })
                ->update([
                    'last_seen_at' => now(),
                ]);
        }

        return $next($request);
    }
}
