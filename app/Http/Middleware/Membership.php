<?php

namespace App\Http\Middleware;
use App;
use Closure;
use Illuminate\Support\Facades\Auth;


class Membership
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
                // return $next($request);

        //Check header request and determine localizaton
        if(auth('branch')->check() && auth('branch')->user()->plan_id <= 1){
            return redirect()->route('branch.getMembership');
        }
        return $next($request);
    }
}
