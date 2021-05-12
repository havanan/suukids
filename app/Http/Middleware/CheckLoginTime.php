<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class CheckLoginTime
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
        try {
            $user = getCurrentUser();

            if ($user->isAdmin()) {
                return $next($request);
            }

            $now = Carbon::now();

            if (!empty($user->login_time_from)) {
                $fromDate = (new Carbon($user->login_time_from));
                $convertFromDate = new Carbon($fromDate->format('H:i') . ':00 ' . $now->toDateString());

                if ($now < $convertFromDate) {
                    return redirectIfNotHasPermission2();
                }
            }

            if (!empty($user->login_time_to)) {
                $toDate = (new Carbon($user->login_time_to));
                $convertToDate = new Carbon($toDate->format('H:i') . ':00 ' . $now->toDateString());

                if ($now > $convertToDate) {
                    return redirectIfNotHasPermission2();
                }
            }

            return $next($request); 
        } catch(\Exception $e) {
            return $next($request); 
        }
    }
}
