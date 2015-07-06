<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class Student
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
		if(User::isConnected() && User::status() == 'student')
			return $next($request);
		else
			return redirect('/');
    }
}
