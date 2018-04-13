<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    protected $repUser;

    public function __construct(
        UserRepository $user
    )
    {
        $this->repUser = $user;
    }

    /**
     *
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        $user = Auth::user();
//        $languages = config('constants.language_type');
//
//        if($user && isset($user->locale) && array_key_exists($user->locale, $languages)){
//            $lang = $user->locale;
//
//        } elseif ($request->has('lang') && array_key_exists($request->input('lang'), $languages)) {
//            $lang =  $request->input('lang');
//
//        } elseif (Cookie::has('locale') && array_key_exists(Cookie::get('locale'), $languages)) {
//            $lang = Cookie::get('locale');
//
//        } elseif ($request->server('HTTP_ACCEPT_LANGUAGE') && array_key_exists(substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2), $languages)) {
//            $lang = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
//
//        } else {
//            $lang = 'en';
//        }
//
//        //update locale for user
//        if($user && (!isset($user->locale) || !$user->locale)) {
//            $user_update = $this->repUser->getById($user->id);
//            if($user_update) {
//                $this->repUser->update($user_update, ['locale' => $lang]);
//            }
//        }
//        $is_data_format = false;
//        // set cookie locale
//        if(Cookie::get('locale') != $lang) {
//            Cookie::queue('locale', $lang, time() + 60 * 60 * 24 * 365);
//            $is_data_format = true;
//        } else if(!in_array(Cookie::get('locale'), config('constants.date_format'))){
//            $is_data_format = true;
//        }
//        if($is_data_format){
//            $date_format = config('constants.date_format.'.$lang);
//            Cookie::queue('date_format', $date_format, time() + 60 * 60 * 24 * 365);
//        }
//
//        App::setLocale($lang);
        return $next($request);
    }
}