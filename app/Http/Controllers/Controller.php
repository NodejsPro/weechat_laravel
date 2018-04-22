<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getContent($fileName, $data = array()){
        $contents = '';
        try
        {
            $contents = File::get(public_path(). DIRECTORY_SEPARATOR . 'embed' .DIRECTORY_SEPARATOR .$fileName);
            if($contents && count($data) > 0){
                foreach($data as  $attribute => $value) {
                    $contents = str_replace($attribute, $value, $contents);
                }
            }
        }
        catch (FileNotFoundException $exception)
        {
        }
        return $contents;
    }

    public function getValidateToken(){
    	return bin2hex(openssl_random_pseudo_bytes(24));
    }

}
