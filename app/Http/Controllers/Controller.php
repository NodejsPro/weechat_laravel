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
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'is_login' => isset($user->is_login) && $user->is_login ? $user->is_login : false,
                    'user_name' => $user->user_name,
                ];
            }
        }
        return $result;
    }

    public function convertRoomData($room_data){
        $result = [];
        if(!empty($room_data)){
            foreach ($room_data as $item){
                $result[] = [
                    'name' => $item->name,
                    'id' => $item->_id
                ];
            }
        }
        return $result;
    }

    public function resizeImage($file_manage, $file, $size, $path, $is_aspectRatio = false){
        if(!empty($file)){
            $file_save = $file_manage->make($file);
            if($is_aspectRatio){
                $file_save = $file_save->resize($size['width'], $size['height'], function ($c){
                    $c->aspectRatio();
                    $c->upsize();
                });
            } else{
                $file_save = $file_save->resize($size['width'], $size['height']);
            }
            $file_save->orientate()->save($path);
        }
    }
}
