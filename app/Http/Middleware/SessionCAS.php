<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class SessionCAS
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
		if(!User::isConnected())
			return redirect('/connecter');
        return $next($request);
    }
}
