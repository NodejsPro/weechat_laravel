<?php

namespace App\Http\Requests;
use App\Mongodb\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $input = $this->all();
        //update
        if(Request::isMethod('put') && Request::is('user/*') ){
            $userId = Request::route()->parameter('user');
            $validation = [
//                'user_name'                 => 'required|max:50',
                'email' => "required|email|max:50|min:6|unique:users,email,$userId,_id,deleted_at,NULL",
                'phone'         => 'required|max:255',
                'contact'                  => 'url|max:255',
//                'password'             => 'max:25|min:6|confirmed',
//                'password_confirmation'=> 'required_if:password,!=null',
            ];
            if(Auth::user()->id != $userId){
                $validation = array_merge($validation, $this->validateAction($input, $userId));
            }
            return $validation;
        }else {
            //create  new user
            $validation = [
                'name'                  => 'required|max:50',
                'email'                 => 'required|email|max:50|min:6|unique:users,email,NULL,id,deleted_at,NULL',
                'user_name' => 'required|max:50',
                'authority' => 'required',
                'phone' => 'required|max:50',
                'avatar' => 'required|max:50',
                'created_id' => 'required|max:50',
                'password'              => 'required|max:25|min:6',
            ];
            //$validation = array_merge($validation, $this->validateAction($input));
            Log::info($validation);
            return $validation;
        }
    }

    public function validateAction($input, $user_id = null){
        if ($user_id != null) {
            $user = User::where('_id', $user_id)->first();
        }
        $user_authority = config('constants.authority');
        $authority = Auth::user()->authority;
        $validation = array();
        $validation['authority'] = 'required';
        $validation['user_name'] = 'required';
        $validation['password'] = 'required|min:6|regex:/^.*(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\`\~\!\@\#\$\%\^\&\*\(\)\_\+\=\-]).*$/';
        $validation['password_confirmation'] = 'required_if:password,!=null';
        $input_authority = @$input['authority'];
        $input_user_name = @$input['user_name'];
        $input_password = @$input['password'];

        if(!isset($input_password) && !isset($input_user_name)){
            unset($validation['user_name']);
            unset($validation['password']);
            unset($validation['password_confirmation']);
        }
        $authority_arr = config('constants.authority');
        if($authority == $user_authority['super_admin']){
            unset($authority_arr['super_admin']);
        }elseif($authority == $user_authority['admin_lv1']){
            unset($authority_arr['super_admin']);
            unset($authority_arr['admin_lv1']);
        }elseif($authority == $user_authority['admin_lv2']){
            unset($authority_arr['super_admin']);
            unset($authority_arr['admin_lv1']);
            unset($authority_arr['admin_lv2']);
        }
        $validation['authority'] = 'required|in:'.implode(',', $authority_arr);
        return $validation;
    }
}
