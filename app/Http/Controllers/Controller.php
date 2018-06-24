<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
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

    public function convertUserData($user_data, $return_room_flg = false){
        $result = [];
        if(!empty($user_data)){
            foreach ($user_data as $user){
                $user_arr = [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar ? asset($user->avatar) : asset('images/profile.png'),
                    'login_flg' => isset($user->login_flg) && $user->login_flg ? $user->login_flg : false,
                    'user_name' => $user->user_name,
                ];
                if($return_room_flg){
                    $room = $this->repRoom->getByUserID($user->id);
                }
                $result[] = $user_arr;
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
                    'id' => $item->_id,
                    'room_type' => $item->room_type,
                    'member' => $item->member
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

    public function uploadFile($file_manage, $file, $path){
        if(!empty($file)){
            $file_save = $file_manage->make($file);
            $file_save->save($path);
            return true;
        }
        return false;
    }

    public function createFolderLocal($folder_name_arr){
        foreach ($folder_name_arr as $folder){
            if(!file_exists(public_path($folder))){
                mkdir(public_path($folder));
            }
        }
    }

    public function sendRequest($url, $method, $header = [], $body = [], $param=[]){
        $result = [
          'success' => false,
        ];
        try{
            $client = new Client();
            $params = [];
            if(!empty($header) && is_array($header)){
                $params['headers'] = $header;
            }
            if(!empty($body) && is_array($body)){
                $params['form_params'] = $body;
            }
            $params = array_merge($params, $param);
            $res = $client->$method($url, $params);
            $result['data'] = $res->getBody();
            $result['response'] = json_decode($res->getBody(), true);
            if($res->getStatusCode() == 200) {
                Log::info('success');
                $result['success'] = true;
            } else {
                Log::info('false');
                $result['code'] = $res->getStatusCode();
            }
        }catch (\Exception $e) {
            $result['code'] = $e->getCode();
            $body = json_decode($e->getResponse()->getBody(true));
            $message = isset($body->message) ? $body->message : ((isset($body->error) && isset($body->error->message)) ? $body->error->message : trans('message.common_error'));
            $result['error'] = $message;
            Log::info(print_r($body, true));
        }
        Log::info($result);
        return $result;
    }

    public function sendSMS($phone, $code){
        Log::info('send sms demo via code ' . $code);
//        return true;
        $url = config('sms.request.send_sms');
        $data_replace = [
            ':host' => config('sms.host'),
            ':content' => trans('sms.send_message', ['code' => $code]),
            ':phone' => $phone,
            ':key' => config('sms.key'),
        ];
        foreach ($data_replace as $key => $value){
            $url = str_replace($key, $value, $url);
        }
        Log::info('$phone, $code: ' . $phone . '----'. $code);
        $random_time = $this->getRandomCode(5);
        usleep($random_time);
        Log::info('url: ' . $url . 'send in ' . $random_time);
        $result = $this->sendRequest($url, 'get');
        return $result;
    }

    public function getRandomCode($number = 6){
        if($number == 6){
            return '123546';
        }
        $min = pow(10, $number);
        $max = pow(10, $number + 1) - 1;
        $code = random_int($min, $max);
        return $code;
    }
}
