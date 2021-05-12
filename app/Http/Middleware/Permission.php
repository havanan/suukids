<?php

namespace App\Http\Middleware;

use Closure;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $user = \auth('users')->user();

        if (!$user->hasPermission($permission)) {
            return redirectIfNotHasPermission();
        }

        return $next($request);
    }
}
