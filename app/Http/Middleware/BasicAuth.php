<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;


use Closure;

class BasicAuth extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {

        if (strcmp($request->header('Authorization'), 'c5b90ab4-d970-48cf-93e3-3f14a023b064')) {
            return response()->json('Unauthorized', 401);
        }

        return $next($request);
    }
}