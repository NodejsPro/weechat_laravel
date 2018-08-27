<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TestRequest;
use App\Mongodb\EmbotPlan;
use App\Http\Requests\UserRequest;
use App\Repositories\ConnectPageRepository;
use App\Repositories\ConnectRepository;
use App\Repositories\EmbotPlanRepository;
use App\Repositories\MasterRepository;
use App\Repositories\PlanRepository;
use App\Repositories\UserMongoRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cookie;
use Intervention\Image\ImageManager;
use Jenssegers\Mongodb\Auth\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $repUser;
    protected $repMaster;
	protected $repPlan;
    protected $repConnect;
    protected $repConnectPage;
    protected $file_manager;

    public function __construct(
        UserRepository $user
    ){
        Log::info('api userController');
        $this->repUser = $user;
        $this->file_manager = new ImageManager(array('driver' => 'gd'));
        $this->middleware('authentication.api', ['except' => ['updatePassword', 'checkSmsCode', 'userLogin', 'create', 'checkPhone', 'userTest', 'forgetPassword']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    }

    /**
     * Hàm chức năng check login người dùng
    */
    public function userLogin(Request $request){
        $inputs = $request->all();
        Log::info('api userLogin');
        Log::info($inputs);
        $validator = Validator::make(
            $inputs,
            array(
                'user-name-phone' => 'required',
                'password' => 'required'
            ),[
                'user-name-phone.required' => trans('validation.required', ['attribute' => trans('user.field_user_phone')])
            ]
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $user_name_phone = $inputs['user-name-phone'];
        $password = $inputs['password'];
        $user = $this->repUser->getUserActive($user_name_phone);
        if($user && Hash::check($password, $user->password)){
            $code = $user->code;
            $inputs = [];
            // call api code
            if(config('app.env') == 'local'){
                $code = config('app.code_sms');
            }else{
                $code = $this->getRandomCode(6);
            }
            $inputs['code'] = $code;
            $sms_status = $this->sendSMS($user->phone, $code);
            if($sms_status){
                $validate_token = $this->getValidateToken();
                $inputs['validate_token'] = $validate_token;
                $this->repUser->updateStatus($user, $inputs);
                $data = [
                    'success' => true,
                    'validate_token' => $validate_token
                ];
                return Response::json($data, 200);
            }
            return Response::json(
                array(
                    'success' => false,
                    'msg' => trans('user.send_sms_fail')
                ), 400);
        }
        return Response::json(
            array(
                'success' => false,
                'msg' => trans('user.msg_login_fail')
                ), 400);
    }

    public function userLoginRemember(Request $request){
        $inputs = $request->all();
        Log::info('api userLoginRemember');
        Log::info($inputs);
        $validator = Validator::make(
            $inputs,
            array(
                'phone' => 'required',
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $phone = $inputs['phone'];
        $header = $request->header();
        $validate_token_header = $header['validate-token'][0];
        $user = $this->repUser->getUserByField('phone', $phone);
        if($user){
            if($user->validate_token != $validate_token_header || !$user->remember_flg){
                return Response::json(
                    array(
                        'success' => false,
                        'msg' => trans('user.validate_token_expire'),
                        'log_out_flg' => config('constants.active.enable')
                    ), 400);
            }
            $user_arr = [$user];
            $inputs['login_flg'] = config('constants.active.enable');
            $this->repUser->update($user, $inputs);
            $data = [
                'success' => true,
                'data' => $this->convertUserData($user_arr),
                'validate_token' => $user->validate_token
            ];
            return Response::json($data, 200);
        }
        return Response::json(
            array(
                'success' => false,
                'msg' => trans('user.msg_login_fail')
            ), 400);
    }

    /**
     * Hàm chức năng xác thực người dùng qua sms sau khi login
     *
     * */
    public function authentication(Request $request){
        $header = $request->header();
        $validate_token = $header['validate-token'][0];
        $inputs = $request->all();
        Log::info('api authentication');
        Log::info($inputs);
        $validator = Validator::make(
            $inputs,
            array(
                'code' => 'required'
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $code = $inputs['code'];
        $user = $this->repUser->getUserByField('validate_token', $validate_token);
        if($user && $user->code == $code){
            $inputs = [
                'code' => '',
                'login_flg' => true,
            ];
            $user = $this->repUser->updateStatus($user, $inputs);
            $user_arr = [$user];
            $data = [
                'success' => true,
                'data' => $this->convertUserData($user_arr),
                'validate_token' => $user->validate_token
            ];
            return Response::json($data, 200);
        }
        return Response::json(array(
            'success' => false,
            'msg' => trans('message.common_error')
        ), 400);
    }

    public function checkPhone(Request $request){
        $inputs = $request->all();
        Log::info('api checkPhone');
        Log::info($inputs);
        $validator = Validator::make(
            $inputs,
            array(
                'phone' => 'required',
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $phone = $inputs['phone'];
        $user = $this->repUser->getOneByField('phone', $phone);
        if($user){
            if(isset($user->confirm_flg) && $user->confirm_flg){
                $msg = trans('user.user_exists');
                return response([
                    "success" => true,
                    "user_exists_flg" => true,
                    "data" => [
                        'avatar' => $user->avatar ? asset($user->avatar) : asset('images/profile.png'),
                        'user_name' => $user->user_name,
                    ]
                ], 200);
            }else{
                return response([
                    "success" => true,
                    "user_exists_flg" => false,
                ], 200);
            }
        }
        return response([
            "success" => false,
            'msg' => trans('user.user_not_exists')
        ], 422);
    }

    public function createByUserName(Request $request){
        $inputs = $request->all();
        Log::info('api createByUserName');
        Log::info($inputs);
        $validator = Validator::make(
            $inputs,
            array(
                'phone' => 'required',
                'user-name' => 'required',
                'password' => 'required',
//                'password' => 'required|regex:/^.*(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\`\~\!\@\#\$\%\^\&\*\(\)\_\+\=\-]).*$/'
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
    }

    public function create(Request $request){
        $inputs = $request->all();
        Log::info('api create');
        Log::info($inputs);
        $validator = Validator::make(
            $inputs,
            array(
                'phone' => 'required',
                'user-name' => 'required|unique:users,email,NULL,id,deleted_at,NULL',
                'password' => 'required',
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $phone = $inputs['phone'];
        $user_name = $inputs['user-name'];
        $password = $inputs['password'];
        $active = config('constants.active');
        $user = $this->repUser->getOneByField('phone', $phone);
        if($user){
            if($user->confirm_flg != $active['disable']){
                return Response::json([
                    'success' => true,
                    'msg' => trans('message.user_used')
                ], 400);
            }
            $inputs = [
              'user_name' => $user_name,
              'password' => $password,
              'confirm_flg' => $active['enable']
            ];
            $this->repUser->storeApi($user, $inputs);
            return Response::json([
                'success' => true
            ], 200);
        }else{
            return Response::json([
                'success' => false,
                'msg' => trans('msg.user_not_exists')
            ], 400);
        }
    }

    public function edit(Request $request){
        $inputs = $request->all();
        Log::info('api edit');
        Log::info($inputs);
        $validator = Validator::make(
            $inputs,
            array(
                'avatar' => 'required',
                'password_app' => 'required',
                'password' => 'required|regex:/^.*(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\`\~\!\@\#\$\%\^\&\*\(\)\_\+\=\-]).*$/',
                'phone' => 'required',
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
    }

    public function userTest(){
        Log::info('api userTest');
        $user_admin = $this->repUser->getOneByField("user_name", 'super_admin');
        if(!$user_admin){
            \App\Mongodb\User::create([
                'authority' => '001',
                'name' => 'supper admin',
                'password' => '$2y$10$bJGmXuUkx2hfURc2/fNj9O0ViaKgpUGsqzZNHNL6/QFCx8yEhI/yS',
                'phone' => '01656228578',
                'validate_token' => null,
                'confirm_flg' => 1,
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzaLMnex1QwV83TBQgxLTaoDAQlFswsYy62L3mO4Su-CMkk3jX',
                'user_name' => 'super_admin',
            ]);
        }
        sleep(10);

        $user_check = $this->repUser->getOneByField("user_name", 'admin_lv1');
        if(!$user_check){
            \App\Mongodb\User::create([
                'authority' => '002',
                'name' => 'admin lv1',
                'password' => '$2y$10$bJGmXuUkx2hfURc2/fNj9O0ViaKgpUGsqzZNHNL6/QFCx8yEhI/yS',
                'phone' => '01656228579',
                'validate_token' => null,
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzaLMnex1QwV83TBQgxLTaoDAQlFswsYy62L3mO4Su-CMkk3jX',
                'user_name' => 'admin_lv1',
                'confirm_flg' => 1,
                'code' => null,
                'confirmation_token' => null,
                'created_id' => $user_admin->id,
            ]);

        }

         $user_check = $this->repUser->getOneByField("user_name", 'admin_lv2');
        if(!$user_check){
            \App\Mongodb\User::create([
                'authority' => '003',
                'name' => 'admin lv2',
                'password' => '$2y$10$bJGmXuUkx2hfURc2/fNj9O0ViaKgpUGsqzZNHNL6/QFCx8yEhI/yS',
                'phone' => '01656228580',
                'validate_token' => null,
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzaLMnex1QwV83TBQgxLTaoDAQlFswsYy62L3mO4Su-CMkk3jX',
                'user_name' => 'admin_lv2',
                'confirm_flg' => 1,
                'code' => null,
                'confirmation_token' => null,
                'created_id' => $user_admin->id,
            ]);
        }

        $user_check = $this->repUser->getOneByField("user_name", 'client');
        if(!$user_check){
            \App\Mongodb\User::create([
                'authority' => '004',
                'name' => 'client',
                'password' => '$2y$10$bJGmXuUkx2hfURc2/fNj9O0ViaKgpUGsqzZNHNL6/QFCx8yEhI/yS',
                'phone' => '01656228581',
                'validate_token' => null,
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzaLMnex1QwV83TBQgxLTaoDAQlFswsYy62L3mO4Su-CMkk3jX',
                'user_name' => 'client',
                'code' => null,
                'confirm_flg' => 1,
                'confirmation_token' => null,
                'created_id' => $user_admin->id,
            ]);
        }

        $user_check = $this->repUser->getOneByField("user_name", 'client1');
        if(!$user_check){
            \App\Mongodb\User::create([
                'authority' => '004',
                'name' => 'client1',
                'password' => '$2y$10$bJGmXuUkx2hfURc2/fNj9O0ViaKgpUGsqzZNHNL6/QFCx8yEhI/yS',
                'phone' => '01656228582',
                'validate_token' => null,
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzaLMnex1QwV83TBQgxLTaoDAQlFswsYy62L3mO4Su-CMkk3jX',
                'user_name' => 'client1',
                'code' => null,
                'confirm_flg' => 1,
                'confirmation_token' => null,
                'created_id' => $user_admin->id,
            ]);
        }

        $user_check = $this->repUser->getOneByField("user_name", 'client2');
        if(!$user_check){
            \App\Mongodb\User::create([
                'authority' => '004',
                'name' => 'client2',
                'password' => '$2y$10$bJGmXuUkx2hfURc2/fNj9O0ViaKgpUGsqzZNHNL6/QFCx8yEhI/yS',
                'phone' => '01656228583',
                'validate_token' => null,
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzaLMnex1QwV83TBQgxLTaoDAQlFswsYy62L3mO4Su-CMkk3jX',
                'user_name' => 'client2',
                'code' => null,
                'confirm_flg' => 1,
                'confirmation_token' => null,
                'created_id' => $user_admin->id,
            ]);
        }

        $user_check = $this->repUser->getOneByField("user_name", 'client3');
        if(!$user_check){
            \App\Mongodb\User::create([
                'authority' => '004',
                'name' => 'client3',
                'password' => '$2y$10$bJGmXuUkx2hfURc2/fNj9O0ViaKgpUGsqzZNHNL6/QFCx8yEhI/yS',
                'phone' => '01656228584',
                'validate_token' => null,
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzaLMnex1QwV83TBQgxLTaoDAQlFswsYy62L3mO4Su-CMkk3jX',
                'user_name' => 'client3',
                'code' => null,
                'confirm_flg' => 1,
                'confirmation_token' => null,
                'created_id' => $user_admin->id,
            ]);
        }


        $user_check = $this->repUser->getOneByField("user_name", 'client4');
        if(!$user_check){
            \App\Mongodb\User::create([
                'authority' => '004',
                'name' => 'client4',
                'password' => '$2y$10$bJGmXuUkx2hfURc2/fNj9O0ViaKgpUGsqzZNHNL6/QFCx8yEhI/yS',
                'phone' => '01656228585',
                'validate_token' => null,
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzaLMnex1QwV83TBQgxLTaoDAQlFswsYy62L3mO4Su-CMkk3jX',
                'user_name' => 'client4',
                'code' => null,
                'confirm_flg' => 1,
                'confirmation_token' => null,
                'created_id' => $user_admin->id,
            ]);
        }
    }

    public function show(){

    }

    public function forgetPassword(Request $request){
        $inputs = $request->all();
        Log::info('api forgetPassword');
        Log::info($inputs);
        $validator = Validator::make(
            $inputs,
            array(
                'phone' => 'required'
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $phone = $inputs['phone'];
        $user = $this->repUser->getUserByPhone($phone);
        if($user && $user->confirm_flg){
            $code = $this->getValidateToken();
            $sms_status = $this->sendSMS($phone, $code);
            if($sms_status){
                $this->repUser->forgetPassword($user, $code);
                return response([
                    "success" => true,
                ], 200);
            }
            return Response::json(
                array(
                    'success' => false,
                    'msg' => trans('user.send_sms_fail')
                ), 400);
        }
        return response([
            "success" => false,
            'msg' => 'user.user_not_exists'
        ], 400);
    }

    public function checkSmsCode(Request $request){
        $inputs = $request->all();
        Log::info('api checkSmsCode');
        Log::info($inputs);
        $validator = Validator::make(
            $inputs,
            array(
                'code' => 'required'
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $code = $inputs['code'];
        $user = $this->repUser->getUserByField('code', $code);
        if($user){
            $data = [
                'success' => true,
            ];
            $code = '';
            $this->repUser->updateCode($user, $code, config('constants.active.enable'));
            return Response::json($data, 200);
        }
        return Response::json(array(
            'success' => false,
            'msg' => trans('message.common_error')
        ), 400);
    }

    public function updatePassword(Request $request){
        $inputs = $request->all();
        Log::info('api updatePassword');
        Log::info($inputs);
        $validator = Validator::make(
            $inputs,
            array(
                'phone' => 'required',
                'password' => 'required',
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $password = $inputs['password'];
        $phone = $inputs['phone'];
        $user = $this->repUser->getUserUpdatePassword('phone', $phone);
        if($user){
            $data = [
                'success' => true,
            ];
            $this->repUser->updatePassword($user, $password, config('constants.active.disable'));
            return Response::json($data, 200);
        }
        return Response::json(array(
            'success' => false,
            'msg' => trans('message.user_not_chage_password')
        ), 400);
    }

    protected function fileUpload(Request $request)
    {
        header('Access-Control-Allow-Origin: *');
        $inputs = $request->all();
        Log::info('api fileUpload');
        Log::info($inputs);
        $file_config = config('constants.file_upload');
        $validator = Validator::make(
            $inputs,
            array(
                'file' => 'required',
                'user_id' => 'required',
                'file_type' => 'required',
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $user_id = $inputs['user_id'];
        $user = $this->repUser->getById($user_id);
        $msg = trans("message.common_error");
        if ($user) {
            if(isset($inputs['file']) && $_FILES['file']) {
                try{
                    $upload_storage = $file_config['file_path_base'] . DIRECTORY_SEPARATOR . $file_config['file_path_client'] . DIRECTORY_SEPARATOR. $user_id . DIRECTORY_SEPARATOR;
                    $file = $inputs['file'];
                    $file_name_origin = @$_FILES['file']['name']; //[file_name1.jpg, file_name2.jpg,...]
                    $file_info = pathinfo($file_name_origin);
//                    $file_name = uniqid() . time() . '.'. @$inputs['file_type'];
                    $file_name = uniqid() . time();
                    $this->createFolderLocal([$upload_storage]);
                    $result = $this->moveFile($file, $upload_storage, $file_name);
                    if($result){
                        $data = [
                            'path' => url($upload_storage . $file_name),
                            'name' => $file_name,
                            'name_origin' => $file_info['basename'],
                            'file_type' => @$inputs['file_type'],
                        ];
                        return Response::json(array(
                            'success' => true,
                            'file_upload' => $data
                        ), 200);
                    }
                }catch (\Exception $e){
                    $msg = 'error upload file: '. $e->getMessage();
                }
            }else{
                $msg = trans('user.file_miss');
            }
        }else{
            $msg = trans('user_not_exits');
        }
        return Response::json(array(
            'success' => false,
            'errors' => $msg
        ), 400);
    }

    protected function fileMultiUpload(Request $request)
    {
        header('Access-Control-Allow-Origin: *');
        $inputs = $request->all();
        Log::info('api fileUpload');
        Log::info($inputs);
        $file_config = config('constants.file_upload');
        $validator = Validator::make(
            $inputs,
            array(
                'user_id' => 'required',
                'files.*.file' => 'required',
                'files.*.file_type' => 'required',
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $user_id = $inputs['user_id'];
        $user = $this->repUser->getById($user_id);
        $msg = trans("message.common_error");
        if ($user) {
            if(isset($inputs['files']) && isset($_FILES['files']) && is_array($inputs['files'])) {
                try{
                    $upload_storage = $file_config['file_path_base'] . DIRECTORY_SEPARATOR . $file_config['file_path_client'] . DIRECTORY_SEPARATOR. $user_id . DIRECTORY_SEPARATOR;
                    $data = [];
                    $this->createFolderLocal([$upload_storage]);
                    foreach($inputs['files'] as $index => $file){
                        $file_name_origin = @$_FILES['files']['name'][$index]['file'];
                        $file_info = pathinfo($file_name_origin);
                        $file_name = uniqid() . time();
                        $result = $this->moveFile($file['file'], $upload_storage, $file_name);
                        if($result){
                            $data[] = [
                                'path' => url($upload_storage . $file_name),
                                'name' => $file_name,
                                'name_origin' => $file_info['basename'],
                                'file_type' => @$file['file_type'],
                            ];
                        }
                    }
                    return Response::json(array(
                        'success' => true,
                        'file_upload' => $data
                    ), 200);
                }catch (\Exception $e){
                    $msg = 'error upload file: '. $e->getMessage();
                }
            }else{
                $msg = trans('user.file_miss');
            }
        }else{
            $msg = trans('user_not_exits');
        }
        return Response::json(array(
            'success' => false,
            'errors' => $msg
        ), 400);
    }

    public function checkRequest($request){
        $validator = Validator::make(
            $request->all(),
            array(
                'user-name' => 'required',
                'password' => 'required'
            )
        );
        if ($validator->fails()){
            return response([
                "success" => '13123',
                'msg' => '123'
            ], 422);
        }
    }
}
