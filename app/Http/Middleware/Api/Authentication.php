<?php

namespace App\Http\Middleware\Api;

use App\Repositories\UserRepository;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Authentication
{
    protected $repUser;
    public function __construct(
        UserRepository $user
    )
    {
        $this->repUser   = $user;
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
        Log::info('Middleware Authentication');
        $headers = $request->header();
        $inputs = $request->all();
        $result = false;
        if(isset($headers['validate-token']) && !empty($headers['validate-token'])){
            $validate_token = $headers['validate-token'];
            Log::info('validate-token');
            Log::info($validate_token);
            $user = $this->repUser->getOneByField('validate_token', @$validate_token['0']);
            if($user && (!isset($inputs['phone']) || $inputs['phone'] == $user->phone)){
                $result = true;
            }
        }
        if($result){
            return $next($request);
        }else{
            return response([
                'success' => false,
                'msg' => trans('message.authentication_error')
            ], 401);
        }
    }
}
