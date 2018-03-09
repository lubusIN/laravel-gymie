<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class Owner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        if (! Auth::User()->can($permissions)) {
            if (Auth::User()->id != $request->route('user_id')) {
                abort(403);
            }
        }

        return $next($request);
    }
}
