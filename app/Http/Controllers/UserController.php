<?php

namespace App\Http\Controllers;

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
use Jenssegers\Mongodb\Auth\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $repUser;
    protected $repMaster;
	protected $repPlan;
    protected $repConnect;
    protected $repConnectPage;
    protected $repEmbotPlan;

    public function __construct(
        UserRepository $user
//        ,
//        MasterRepository $master,
//        PlanRepository $plan,
//        ConnectRepository $connect,
//        EmbotPlanRepository $embot_plan,
//        ConnectPageRepository $connect_page
    ){
        $this->repUser = $user;
//        $this->repMaster = $master;
//        $this->repPlan = $plan;
//        $this->repConnect = $connect;
//        $this->repConnectPage = $connect_page;
//        $this->repEmbotPlan = $embot_plan;
//        $this->middleware('authority', ['except' => ['accountEdit', 'accountUpdate', 'accountInformation', 'updateAccountInformation', 'unsubscribe', 'UpdateUnsubscribe', 'settingPaymentGateway']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user           = Auth::user();
        return view('user.index')->with([
            'login_user'        => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $user = Auth::user();
        $user_authority = config('constants.authority');
        $templates = [];
        if($user->authority == $user_authority['client']){
            abort('404');
        }
        $sns_type_list = $this->getBotType($user);
        if($user->authority == $user_authority['agency']){
            $client_list = $this->repUser->getAllByField("created_id", $user->id);
            if(count($client_list) >= $user->max_user_number){
                return redirect()->route('user.index')->with('alert-danger', trans('message.user_add_limit'));
            }
            if(!$this->checkEmbotEnv()){
                $current_bot = $user->max_bot_number - $this->getCountConnectPage($user->id) - $this->getBotNumberAgency($user->id);
                if($current_bot <= 0){
                    return redirect()->route('user.index')->with('alert-danger', trans('message.user_add_limit_bot'));
                }
            }
            $group = $this->repMaster->getUserGroup('authority', [$user_authority['admin'], $user_authority['agency']]);
        }else{
            $group = $this->repMaster->getUserGroup('authority');
            $condition = array(
                ['template_flg', config('constants.flag.template')],
                ['public_flg' ,'!=', config('constants.active.enable')]
            );
            $templates = $this->repConnectPage->getKeyValue("page_name", "_id", $condition);
        }
        if($this->checkEmbotEnv()){
            unset($group[$user_authority['admin']]);
        }
        $embot_plan_value = $this->getValueEmbotPlan();
        return view('user.create')->with([
            'users'             => null,
            'group'             => $group,
            'sns_type_list' => $sns_type_list,
            'templates' => $templates,
            'embot_plan_value' => $embot_plan_value,
            'embot_env_flg' => $this->checkEmbotEnv(),
        ]);
    }

    public function userLoginApi(Request $request){
        $inputs = $request->all();
        dd($inputs);
        $user_name = @$inputs['user_name'];
        $password = @$inputs['password'];
        if(!empty($user_name) && !empty($password)){
            $user = $this->repUser->getOneByField('user_name', $user_name);
            if($user && Hash::check($password, $user->password)){
            	$code = $user->code;
                if(empty($code)){
                    // call api code
                    $code = uniqid();
                    $this->repUser->updateCode($user, $code);
                }
                $data = [
                    'success' => true,
                    'code' => $code,
                    'validate_token' => $this->getValidateToken()
                ];
                return Response::json($data, 200);
            }
        }
        return Response::json(array(
                    'success' => false
                ), 400);
    }


    public function createApi(Request $request){
    	// dd(1);
        $inputs = $request->all();
        $authority = @$inputs['authority'];
        $email = @$inputs['email'];
        $name = @$inputs['name'];
        $user_name = @$inputs['user_name'];
        $phone = @$inputs['phone'];
        $avatar = @$inputs['avatar'];
        $created_id = @$inputs['created_id'];
        $password = @$inputs['password'];
        $validate_token = $request->header('validate_token');
        // $confirmation_token = @$inputs['password'];
        if(!empty($validate_token) && !empty($authority) && !empty($email) && !empty($name) && !empty($user_name)
         && !empty($phone) && !empty($avatar) && !empty($created_id) && !empty($password)){
         	$user_created = $this->repUser->getById($created_id);
         	if($user_created){
         		$inputs['confirmation_token'] = $this->getValidateToken();
	            //$user = $this->repUser->store($inputs);
                return Response::json([
                	'success' => true
                ], 200);
            }else{
     		return Response::json([
                	'success' => false,
                	'msg' => 'User quản lý không tồn tại'
                ], 400);
     		}
     	}	
        return Response::json(array(
                'success' => false
            ), 400);
    }

    public function userTest(){
        $user_check = $this->repUser->getOneByField("email", 'supper_admin@weechat.com');
        if(!$user_check){
            \App\Mongodb\User::create([
                'authority' => '001',
                'email' => 'supper_admin@weechat.com',
                'name' => 'supper admin',
                'password' => '$2y$10$bJGmXuUkx2hfURc2/fNj9O0ViaKgpUGsqzZNHNL6/QFCx8yEhI/yS',
                'phone' => '01656228578',
                'validate_token' => null,
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzaLMnex1QwV83TBQgxLTaoDAQlFswsYy62L3mO4Su-CMkk3jX',
                'user_name' => 'super_admin',
            ]);
        }

        $user_check = $this->repUser->getOneByField("email", 'admin_lv1@weechat.com');
        if(!$user_check){
            \App\Mongodb\User::create([
                'authority' => '002',
                'email' => 'admin_lv1@weechat.com',
                'name' => 'admin lv1',
                'password' => '$2y$10$bJGmXuUkx2hfURc2/fNj9O0ViaKgpUGsqzZNHNL6/QFCx8yEhI/yS',
                'phone' => '01656228579',
                'validate_token' => null,
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzaLMnex1QwV83TBQgxLTaoDAQlFswsYy62L3mO4Su-CMkk3jX',
                'user_name' => 'admin_lv1',
                'code' => null,
                'confirmation_token' => null,
            ]);

        }

         $user_check = $this->repUser->getOneByField("email", 'admin_lv2@weechat.com');
        if(!$user_check){
            \App\Mongodb\User::create([
                'authority' => '003',
                'email' => 'admin_lv2@weechat.com',
                'name' => 'admin lv2',
                'password' => '$2y$10$bJGmXuUkx2hfURc2/fNj9O0ViaKgpUGsqzZNHNL6/QFCx8yEhI/yS',
                'phone' => '01656228580',
                'validate_token' => null,
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzaLMnex1QwV83TBQgxLTaoDAQlFswsYy62L3mO4Su-CMkk3jX',
                'user_name' => 'admin_lv2',
                'code' => null,
                'confirmation_token' => null,
            ]);
        }


        $user_check = $this->repUser->getOneByField("email", 'client@weechat.com');
        if(!$user_check){
            \App\Mongodb\User::create([
                'authority' => '004',
                'email' => 'client@weechat.com',
                'name' => 'client',
                'password' => '$2y$10$bJGmXuUkx2hfURc2/fNj9O0ViaKgpUGsqzZNHNL6/QFCx8yEhI/yS',
                'phone' => '01656228581',
                'validate_token' => null,
                'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzaLMnex1QwV83TBQgxLTaoDAQlFswsYy62L3mO4Su-CMkk3jX',
                'user_name' => 'client',
                'code' => null,
                'confirmation_token' => null,
            ]);
        }
    }

    public function show(){

    }

    public function authenticationApi(Request $request){
    	$inputs = $request->all();
	 	$validate_token = $request->header('validate_token');
	 	if(!empty($validate_token) && !empty($inputs['phone']) && !empty($inputs['code'])){
	 		$phone_number = $inputs['phone'];
	 		$code = $inputs['code'];
	 		$user = $this->repUser->getUserCode($inputs['phone'], $inputs['code']);
	 		if($user){
				$data = [
                    'success' => true,
                    'data' => $user,
                    'validate_token' => $this->getValidateToken()
                ];
                return Response::json($data, 200);
	 		}
	 	}
	 	return Response::json(array(
                    'success' => false
                ), 400);
    }
}
