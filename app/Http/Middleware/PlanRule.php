<?php

namespace App\Http\Middleware;

use App\Repositories\PlanRepository;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ConnectPageRepository;
use Illuminate\Support\Facades\Route;

class PlanRule
{
    protected $repConnectPage;
    protected $repPlan;

    public function __construct(
        ConnectPageRepository $connectPage,
        PlanRepository $plan
    )
    {
        $this->repConnectPage = $connectPage;
        $this->repPlan = $plan;
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
        $rule_access_flg = false;
        $user = Auth::user();

        if($user) {
            if($user->authority == config('constants.authority.client') && $user->plan != null && $user->plan != '') {
                $plan_function_all = config('constants.plan_function');
                $plan_user = $this->repPlan->getOneByField('code', $user->plan);
                $controller_name = $this->getControlleName();

                if($controller_name && $plan_user && isset($plan_user->function_option) && is_array($plan_function_all) && count($plan_function_all)) {
                    $plan_role = $plan_user->function_option;
                    //$role_except is function code can not accept
                    $role_except = array_diff($plan_function_all, $plan_role);
                    $function_rule_code = config('constants.function_rule.'.$controller_name);
                    if($function_rule_code && !in_array($function_rule_code, $role_except)) {
                        $rule_access_flg = true;
                    }
                }
            } else {
                //admin, agency
                $rule_access_flg = true;
            }
        }

        if(!$rule_access_flg) {
            $function_title_list = [
                'notification' => trans('title.notification'),
                'report' => trans('title.report'),
            ];
            $function_title = isset($controller_name) ? @$function_title_list[$controller_name] : '';

            if ($request->ajax() || $request->wantsJson()) {
                return response(trans('message.change_plan_to_using_function'), 200);
            } else {
                return response()->view('errors.plan_limit', ['title' => $function_title], 200);
            }
        }
        return $next($request);
    }

    public function getControlleName() {
        $controller_name = '';
        $route_action = Route::getCurrentRoute()->getActionName();
        if($route_action) {
            $route_action = explode('\\', $route_action);
            $route_action = end($route_action);
            $route_action = explode('Controller', $route_action);
            $controller_name = strtolower(reset($route_action));
        }
        return $controller_name;
    }
}
