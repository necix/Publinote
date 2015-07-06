<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class Tutor
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
     	if(User::isConnected() && User::status() == 'tuteur')
			return $next($request);
		else
			return redirect('/');
    }
}
