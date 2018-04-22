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
                'name'                 => 'required|max:50',
                'email' => "required|email|max:50|min:6|unique:users,email,$userId,_id,deleted_at,NULL",
                'password'             => 'max:25|min:6|confirmed',
                'password_confirmation'=> 'required_if:password,!=null',
                'company_name'         => 'required|max:255',
                'url'                  => 'url|max:255',
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
        $embot_plan = config('constants.embot_plan');
        $authority = Auth::user()->authority;
        $validation = array();
        $validation['authority'] = 'required';
        $input_authority = @$input['authority'];

        if (!isset($user) || (isset($user) && $user->created_id != null)) {
            $validation['max_bot_number'] = 'required|numeric|min:1';
        }
        // admin create
        if($authority == $user_authority['admin']){
            $validation['authority'] = 'required|in:'.implode(',',$user_authority);
            if($input_authority == $user_authority['agency']){
                $validation['max_user_number'] = 'required|numeric|min:1';
            }else if($input_authority == $user_authority['admin']){
                unset($validation['max_bot_number']);
            }
        }
        // agency create
        else if($authority == $user_authority['agency']){
            $validation['authority'] = 'required|in:'.$user_authority['client'];
        }
        if(config('app.plan') == 'EMBOT'){
            $validation['embot_plan'] = 'required|in:'.implode(',',$embot_plan);
            $input_plan = @$input['embot_plan'];
            $embot_yearly_user = config('constants.embot_yearly_user');
            $embot_yearly_user_free = config('constants.embot_yearly_user_free');
            $embot_plan_not_free = array_diff($embot_yearly_user, $embot_yearly_user_free);
            if($input_plan == $embot_plan['free']){
                $validation['embot_yearly_user'] = 'required|in:'.implode(',',$embot_yearly_user_free);
            }elseif($input_plan == $embot_plan['customize']){
                $validation['embot_yearly_user_number'] = 'required|numeric';
                $validation['embot_yearly_fee'] = 'required|numeric';
            }elseif($input_plan == $embot_plan['platinum']){
                unset($validation['max_bot_number']);
                $validation['embot_yearly_user'] = 'required|in:'.implode(',',$embot_plan_not_free);
            }else{
                $validation['embot_yearly_user'] = 'required|in:'.implode(',',$embot_plan_not_free);
            }
        }

        return $validation;
    }
}
