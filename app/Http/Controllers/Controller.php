<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getValidateToken(){
    	return bin2hex(openssl_random_pseudo_bytes(24));
    }

    public function convertUserData($user_data){
        $result = [];
        if(!empty($user_data)){
            foreach ($user_data as $user){
                $result[] = [
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'is_login' => isset($user->is_login) && $user->is_login ? $user->is_login : false,
                    'name' => $user->name,
                ];
            }
        }
        return $result;
    }

}
