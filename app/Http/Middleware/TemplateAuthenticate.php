<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ConnectPageRepository;

class TemplateAuthenticate
{
    protected $repConnectPage;

    public function __construct(
        ConnectPageRepository $connectPage
    )
    {
        $this->repConnectPage   = $connectPage;
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
        $connect_page_id    = $request->route('bot');
        $connect_page       = $this->repConnectPage->getById($connect_page_id);
        $user               = Auth::user();
        //If is bot is template and not admin then can not execute function in: scenario, persistent menu, mail, file, library, variable, api, slot, notification
        if(isset($connect_page->template_flg) && $connect_page->template_flg == config('constants.flag.template') && $user->authority != config('constants.authority.admin')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                abort(404);
            }
        }
        return $next($request);
    }
}
