<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Authentication
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
        $headers = $request->header();
        $result = false;
        if(isset($headers['validate-token']) && !empty($headers['validate-token'])){
            $validate_token = $headers['validate-token'];
            $result = true;
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
