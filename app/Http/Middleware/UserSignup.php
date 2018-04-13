<?php

namespace App\Http\Middleware;

use App\Repositories\PlanRepository;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ConnectPageRepository;
use Illuminate\Support\Facades\Route;

class UserSignup
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
        $result = true;
        // check user create by signup
        if(!empty($user->plan)) {
            $result = false;
            $action_name = $this->getActionName();
            $path_url = $this->getPathUrl($request);
            if(($action_name == 'createWebEmbedBot' || $action_name == 'createWebEmbed') && $path_url != 'efo'){
                $result = true;
            }
        }
        if($result){
            return $next($request);
        }else{
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                abort(404);
            }
        }
    }

    public function getActionName(){
        $action_name = '';
        $route_action = Route::getCurrentRoute()->getActionName();
        if($route_action){
            $route_action = explode('\\', $route_action);
            $route_action = end($route_action);
            $route_action = explode('@', $route_action);
            $action_name = end($route_action);
        }
        return $action_name;
    }

    public function getPathUrl($request){
        $path_name = '';
        $path_full = $request->path();
        if($path_full){
            $path_full = explode('/', $path_full);
            $path_name = end($path_full);
        }
        return $path_name;
    }

}
