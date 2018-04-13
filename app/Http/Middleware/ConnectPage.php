<?php

namespace App\Http\Middleware;

use Closure;
use App\Repositories\ConnectPageRepository;
use App\Repositories\ConnectRepository;
use App\Repositories\ScenarioRepository;
use App\Repositories\BotRoleRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConnectPage
{
    protected $repConnectPage;
    protected $repConnect;
    protected $repScenario;
    protected $repBotRole;
    public function __construct(
        ConnectPageRepository $connectPage,
        ConnectRepository $connect,
        ScenarioRepository $scenario,
        BotRoleRepository $botRole
    )
    {
        $this->repConnectPage   = $connectPage;
        $this->repConnect       = $connect;
        $this->repScenario      = $scenario;
        $this->repBotRole       = $botRole;
    }

    /**
     * Handle an incoming request. Check connect page id to connect id by user ID create
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $connect_page_id    = $request->route('bot');
        $connect_page       = $this->repConnectPage->getById($connect_page_id);
        $user               = Auth::user();
        $result = false;
        if($connect_page && $user) {
            //Bot Template
            if($connect_page->template_flg == config('constants.flag.template')) {
                $bot_template = $user->bot_template ? $user->bot_template : [];
                //If is admin or bot_template of user include template ID
                if($user->authority == config('constants.authority.admin') || in_array($connect_page->id, $bot_template) || (isset($connect_page->public_flg) && $connect_page->public_flg)) {
                    $result = true;
                }
            } else {
                //Bot
                $connect = $this->repConnect->getById($connect_page->connect_id);
                $bot_share = $this->repBotRole->getBotShareByUser($connect_page_id, $user->_id);
                //check own Bot

                if($connect && ($user->authority == config('constants.authority.admin') || $connect->user_id == $user->_id || $bot_share)) {
                    $result = true;
                }
            }
            if ($result) {
                return $next($request);
            }
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response('Not found.', 404);
        }
        abort(404);
    }
}
