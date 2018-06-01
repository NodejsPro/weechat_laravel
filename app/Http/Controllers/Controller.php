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

    public function convertUserData($user_data){
        $result = [];
        if(!empty($user_data)){
            foreach ($user_data as $user){
                $result[] = [
                    'id' => $user->id,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar ? asset($user->avatar) : asset('images/profile.png'),
                    'login_flg' => isset($user->login_flg) && $user->login_flg ? $user->login_flg : false,
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
                    'id' => $item->_id,
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
        Log::info('url: ' . $url);
        $result = $this->sendRequest($url, 'get');
        return $result;
    }
}
