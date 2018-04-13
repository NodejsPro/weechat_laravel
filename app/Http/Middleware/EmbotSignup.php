<?php

namespace App\Http\Middleware;

use App\Repositories\PlanRepository;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ConnectPageRepository;
use Illuminate\Support\Facades\Route;

class EmbotSignup
{

    public function __construct(
    )
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if((config('app.plan') == 'EMBOT') && !empty($user->plan)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                abort(404);
            }
        }
        return $next($request);
    }
}
