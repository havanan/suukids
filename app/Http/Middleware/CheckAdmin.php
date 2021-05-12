<?php

namespace App\Http\Middleware;

use Closure;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = getCurrentUser();

        if (!$user->isAdmin()) {
            return redirectIfNotHasPermission();
        }

        return $next($request);
    }
}