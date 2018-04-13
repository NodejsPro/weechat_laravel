<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ConnectPageRepository;
use App\Repositories\SnsRoleRepository;
use App\Repositories\RuleRepository;
use App\Repositories\BotRoleRepository;
use Illuminate\Support\Facades\Route;

class SnsRule
{
    protected $repConnectPage;
    protected $repSnsRole;
    protected $repRule;
    protected $repBotRole;

    public function __construct(
        ConnectPageRepository $connectPage,
        SnsRoleRepository $sns_role,
        RuleRepository $rule,
        BotRoleRepository $bot_role
    )
    {
        $this->repConnectPage   = $connectPage;
        $this->repSnsRole = $sns_role;
        $this->repRule = $rule;
        $this->repBotRole = $bot_role;
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
        $function_rule = config('constants.function_rule');
        $connect_page_id = $request->route('bot');
        $controller_name = $this->getControlleName();

        if($controller_name && $connect_page_id) {
            $connect_page = $this->repConnectPage->getById($connect_page_id);
            if($connect_page && $connect_page->sns_type) {
                if($connect_page->template_flg != config('constants.flag.template')) {
                    $sns_role = $this->repSnsRole->getOneByField('sns_type', $connect_page->sns_type);
                    $sns_role = $sns_role->rule_code;
                } else {
                    $sns_role = config('constants.template_rule');
                }

                $function_rule_code = $function_rule[$controller_name];
                if($function_rule_code && is_array($sns_role) && in_array($function_rule_code, $sns_role)) {
                    $rule_access_flg = true;
                }
            }
        }

        if(!$rule_access_flg) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                abort(404);
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
