<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Authority
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
        Log::info('Authority');
        if(Auth::check()){
            $authority = config('constants.authority');
            if(Auth::user()->authority == $authority['client']){
                if ($request->ajax() || $request->wantsJson()) {
                    return response('Not found.', 404);
                }
                abort(404);
            }
        }


        return $next($request);
    }
}
