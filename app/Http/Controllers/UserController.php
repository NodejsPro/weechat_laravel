<?php

namespace App\Http\Controllers;

use App\Mongodb\EmbotPlan;
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
//        $this->repUser = $user;
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
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $inputs = $request->all();
        $user = Auth::user();
        $inputs['language'] = config('app.locale');
        $user_authority = config('constants.authority');
        $domain_follow_arr = array();
        if($user->authority == $user_authority['agency'] && isset($user->white_list_domain)){
            $domain_follow_arr = $user->white_list_domain;
        }
        $data = $this->generateDomain($inputs, $user->authority, $domain_follow_arr);
        if (!$data['status']){
            return Redirect::back()->withInput()->withErrors([
                'domain_name_error' => $data['data']
            ]);
        }
        $inputs['white_list_domain'] = array_unique($data['data']);
        try{
            if($user->authority == $user_authority['client']){
                abort('404');
            }elseif($user->authority == $user_authority['agency']){
                if(!$this->checkEmbotEnv()){
                    $max_bot_number = $user->max_bot_number - $this->getCountConnectPage($user->id) - $this->getBotNumberAgency($user->id);
                    if($inputs['max_bot_number'] > $max_bot_number){
                        return Redirect::back()->withInput()->withErrors(
                            ['max_bot_number' => trans('validation.max.numeric', ['attribute' => trans('validation.attributes.max_bot_number'), 'max' => $max_bot_number]) ]
                        );
                    }
                }
            }else{
                if(!empty($inputs['bot_template'])){
                    $inputs['bot_template'] = $this->checkTemplate($inputs['bot_template']);
                }else{
                    $inputs['bot_template'] = array();
                }
            }
            if(empty($inputs['sns_type_list'])){
                $inputs['sns_type_list'] = array();
            }
            if($this->checkEmbotEnv() && $inputs['authority'] == $user_authority['client']){
                $embot_yearly_user = $embot_yearly_user_number = $embot_yearly_fee = null;
                $embot_plan = $inputs['embot_plan'];
                if($inputs['embot_plan'] == config('constants.embot_plan.free')){
                    $embot_yearly_user = config('constants.embot_yearly_user.30');
                }elseif($inputs['embot_plan'] == config('constants.embot_plan.customize')){
                    $embot_yearly_user_number = $inputs['embot_yearly_user_number'];
                    $embot_yearly_fee = $inputs['embot_yearly_fee'];
                }else{
                    $embot_yearly_user = $inputs['embot_yearly_user'];
                }
                $inputs['embot_plan'] = $embot_plan;
                $inputs['embot_yearly_user'] = $embot_yearly_user;
                $inputs['embot_yearly_user_number'] = $embot_yearly_user_number;
                $inputs['embot_yearly_fee'] = $embot_yearly_fee;
            }
            $this->repUser->store($inputs, $user->id);
            return redirect('user')->with('alert-success', trans('message.save_success', ['name' => trans('default.user')]));
        } catch(\Exception $e){
            return redirect()->back()->with('alert-danger', trans('message.save_error', ['name' => trans('default.user')]));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        $user_edit = $this->repUser->getById($id);
        $user_authority = config('constants.authority');
        if($user_edit){
            $templates = [];
            if($user->authority == $user_authority['client'] || ($user->authority == $user_authority['agency'] && $user_edit->created_id != $user->id)) {
                abort('404');
            }elseif($user->authority == $user_authority['admin'] && $user->id == $user_edit->created_id){
                $condition = array(
                    ['template_flg', config('constants.flag.template')],
                    ['public_flg' ,'!=', config('constants.active.enable')]
                );
                $templates = $this->repConnectPage->getKeyValue("page_name", "_id", $condition);
            }
            $except_arr = null;
            $user_list_change = array();
            if($user->authority == $user_authority['agency']){
                $except_arr = [$user_authority['admin'], $user_authority['agency']];
            }else if($user->authority == $user_authority['admin'] && $user_edit->authority == $user_authority['client']){
                $user_id_except_arr = [$user_edit->created_id];
                $user_list_change = $this->repUser->getUserListChange($user_id_except_arr);
            }
            $group = $this->repMaster->getUserGroup('authority', $except_arr);
            $sns_type_list = $this->getBotType($user);
            $embot_plan_value = $this->getValueEmbotPlan();
            if($this->checkEmbotEnv()){
                unset($group[$user_authority['admin']]);
            }
            return view('user.create')->with([
                'user'              => $user_edit,
                'group'             => $group,
                'user_list_change'  => $user_list_change,
                'sns_type_list'  => $sns_type_list,
                'templates' => $templates,
                'embot_plan_value' => $embot_plan_value,
                'embot_env_flg' => $this->checkEmbotEnv(),
            ]);
        }
        abort('404');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $inputs = $request->all();
        $user = Auth::user();
        if(empty($inputs['password'])){
            unset($inputs['password']);
        }
        try{
            $edit_user = $this->repUser->getById($id);
            $user_authority = config('constants.authority');
            if($edit_user){
                // admin update
                if($user->authority == $user_authority['client'] || ($user->authority == $user_authority['agency'] && $edit_user->created_id != $user->id)) {
                    abort('404');
                }
                if($user->authority == $user_authority['admin']){
                    if($user->_id == $edit_user->created_id && isset($inputs['white_list_domain'])){
                        $data = $this->generateDomain($inputs, $user->authority);
                        if (!$data['status']){
                            return Redirect::back()->withInput()->withErrors([
                                'domain_name_error' => $data['data']
                            ]);
                        }
                        $inputs['white_list_domain'] = array_unique($data['data']);
                        $white_domain_client = $inputs['white_list_domain'];
                    }else{
                        $white_domain_client = @$edit_user->white_list_domain;
                    }
                    if($request->has('created_id')){
                        if($edit_user->authority != $user_authority['client'] || $inputs['authority'] != $user_authority['client']){
                            return Redirect::back()->withInput()->withErrors( ['created_id' => trans('message.error_user_change_only_client') ]);
                        }
                        $user_create = $this->repUser->getById($inputs['created_id']);
                        if(empty($user_create)){
                            return Redirect::back()->withInput()->withErrors( ['created_id' => trans('message.error_user_created_not_exist') ]);
                        }
                        if($user_create->authority == $user_authority['client']){
                            return Redirect::back()->withInput()->withErrors( ['created_id' => trans('message.error_user_created_not_client') ]);
                        }elseif($user_create->authority == $user_authority['agency']){
                            $current_bot_number = $this->getCountConnectPage($user_create->_id) + $this->getBotNumberAgency($user->id);
                            if($current_bot_number + $inputs['max_bot_number'] > $user_create->max_bot_number){
                                return Redirect::back()->withInput()->withErrors( ['created_id' => trans('message.error_user_created_add_bot_limit') ]);
                            }
                            $user_create_list = $this->repUser->getAllByField('created_id', $user_create->_id);
                            if($user_create_list){
                                $current_user_number = count($user_create_list);
                                if($current_user_number + 1 > $user_create->max_user_number){
                                    return Redirect::back()->withInput()->withErrors( ['created_id' => trans('message.error_user_created_add_user_limit') ]);
                                }
                            }
                            if(isset($user_create->white_list_domain) && count($user_create->white_list_domain) > 0){
                                if(!empty($white_domain_client) && isset($inputs['change_white_domain']) && $inputs['change_white_domain'] == '1'){
                                    $white_domain_created = array_unique(array_merge($user_create->white_list_domain, $white_domain_client));
                                    $inputs['white_list_domain'] = array_unique($white_domain_client);
                                }elseif(!empty($white_domain_client) && isset($inputs['change_white_domain'])  && $inputs['change_white_domain'] == '0'){
                                    unset($inputs['created_id']);
                                }
                            }
                        }
                    }
                    if($edit_user->authority != $inputs["authority"] && $edit_user->created_id != $user->id){
                        return Redirect::back()->withInput()->withErrors( ['authority' => trans('message.error_client_authority_change') ]);
                    }
                    if($edit_user->authority == $user_authority['agency']){
                        $client_list = $this->repUser->getAllByField("created_id", $edit_user->id);
                        if($client_list && count($client_list) > 0){
                            //change authority
                            if($edit_user->authority != $inputs["authority"]){
                                return Redirect::back()->withInput()->withErrors( ['authority' => trans('message.error_agency_authority_change') ]);
                            }
                            $max_user_number = $inputs['max_user_number'];
                            if($max_user_number < count($client_list)){
                                return Redirect::back()->withInput()->withErrors( ['max_user_number' => trans('message.error_max_user_number_setting', ["count" => count($client_list)]) ]);
                            }
                        }
                    }
                    if($inputs['authority'] == $user_authority['admin']){
                        $inputs['sns_type_list'] = array();
                    } else{
                        $inputs['sns_type_list'] = isset($inputs['sns_type_list']) ? $inputs['sns_type_list'] : array();
                        if($user->id != $edit_user->created_id){
                            unset($inputs['sns_type_list']);
                        }
                    }
                    if($user->id == $edit_user->created_id){
                        $inputs['bot_template'] = isset($inputs['bot_template']) ? $inputs['bot_template'] : array();
                        $inputs['bot_template'] = $this->checkTemplate($inputs['bot_template']);
                    }else{
                        unset($inputs['bot_template']);
                    }
                }else if($user->authority == $user_authority['agency']){
                    //agency update
                    if($request->has('created_id')){
                        unset($inputs['created_id']);
                    }
                    if(!$this->checkEmbotEnv()){
                        $current_bot = $this->getCountConnectPage($edit_user->id);
                        $max_bot_number = $inputs['max_bot_number'];
                        if($max_bot_number < $current_bot){
                            return Redirect::back()->withInput()->withErrors(
                                ['max_bot_number' => trans('message.error_max_bot_number_setting', ["count" => $current_bot]) ]
                            );
                        }
                        $current_max_bot_number = $user->max_bot_number - $this->getCountConnectPage($user->id) - $this->getBotNumberAgency($user->id) + $edit_user->max_bot_number;
                        if($max_bot_number > $current_max_bot_number){
                            return Redirect::back()->withInput()->withErrors(
                                ['max_bot_number' => trans('validation.max.numeric', ['attribute' => trans('validation.attributes.max_bot_number'), 'max' => $current_max_bot_number]) ]
                            );
                        }
                    }
                    $domain_follow_arr = isset($user->white_list_domain) ? $user->white_list_domain : array();
                    $data = $this->generateDomain($inputs, $user->authority, $domain_follow_arr);
                    if (!$data['status']){
                        return Redirect::back()->withInput()->withErrors([
                            'domain_name_error' => $data['data']
                        ]);
                    }
                    if($edit_user->created_id == $user->_id){
                        $inputs['white_list_domain'] = array_unique($data['data']);
                    }
                    if(!isset($inputs['sns_type_list'])){
                        $inputs['sns_type_list'] = array();
                    }
                }
                if($this->checkEmbotEnv() && $inputs['authority'] == $user_authority['client']){
                    $embot_yearly_user = $embot_yearly_user = $embot_yearly_user_number = $embot_yearly_fee = '';
                    $embot_plan = $inputs['embot_plan'];
                    if($inputs['embot_plan'] == config('constants.embot_plan.free')){
                        $embot_yearly_user = config('constants.embot_yearly_user.30');
                    }elseif($inputs['embot_plan'] == config('constants.embot_plan.customize')){
                        $embot_yearly_user_number = $inputs['embot_yearly_user_number'];
                        $embot_yearly_fee = $inputs['embot_yearly_fee'];
                    }else{
                        $embot_yearly_user = $inputs['embot_yearly_user'];
                    }
                    $inputs['embot_plan'] = $embot_plan;
                    $inputs['embot_yearly_user'] = $embot_yearly_user;
                    $inputs['embot_yearly_user_number'] = $embot_yearly_user_number;
                    $inputs['embot_yearly_fee'] = $embot_yearly_fee;
                }
                if(isset($user_create) && isset($white_domain_created)){
                    $this->repUser->updateWhiteDomain($user_create, $white_domain_created);
                }
                $this->repUser->update($edit_user, $inputs);
                return redirect('user')->with('alert-success', trans('message.update_success', ['name' => trans('default.user')]));
            }
            abort('404');
        } catch (\Exception $e){
            return redirect()->back()->with('alert-danger', trans('message.update_error', ['name' => trans('default.user')]));
        }
    }

    public function changeManager( UserRequest $request, $id, $manager_id){
        $inputs = $request->all();
        $user_login = Auth::user();
        $user_authority = config('constants.authority');
        if($user_login->authority == $user_authority['admin']){
            $user_edit = $this->repUser->getById($id);
            $user_manager = $this->repUser->getById($manager_id);
            $user_authority_edit = $inputs['user_authority'];
            if($user_edit && $user_manager && $user_authority_edit == $user_authority['client'] &&
                $user_manager->authority == $user_authority['agency'] && isset($user_manager->white_list_domain) && count($user_manager->white_list_domain) >0
            ){
                if(isset($inputs['white_list_domain']) && $user_login->_id == $user_edit->created_id){
                    $white_list_domain = $inputs['white_list_domain'] ? explode(',', $inputs['white_list_domain']) : array();
                }else{
                    $white_list_domain = $user_edit->white_list_domain;
                }
                if(count($white_list_domain) >0){
                    return Response::json(array(
                        'success' => true
                    ), 200);
                }
            }
        }
        return Response::json(array(
            'success' => false
        ), 200);
    }

    public function accountEdit()
    {
        $user = Auth::user();
        $user_plan = $yearly_user_number = $yearly_fee = $max_bot_number = null;
        if($this->checkEmbotEnv()){
            $plans = [];
            $user_authority = config('constants.authority');
            if($user->authority == $user_authority['client']){
                $plan_code = $user->embot_plan;
                $yearly_user = !empty($user->embot_yearly_user) ? $user->embot_yearly_user : null;
                $yearly_user_number = !empty($user->embot_yearly_user_number) ? $user->embot_yearly_user_number : null;
                $yearly_fee = !empty($user->embot_yearly_fee) ? $user->embot_yearly_fee : null;
                if(!isset($user->embot_plan)){
                    if(!isset($user->created_id)){
                        $plan_code = config('constants.embot_plan.free');
                    }else{
                        $plan_code = config('constants.embot_plan_default.code');
                        $yearly_user_number = config('constants.embot_plan_default.yearly_user');
                        $yearly_fee = config('constants.embot_plan_default.yearly_fee');
                    }
                }
                $user_plan = $this->repEmbotPlan->getByCode($plan_code, $yearly_user);
            }
        }else{
            $user_plan = null;
            $plans = $this->repPlan->getPlanGroup();
        }
        return view('user.my_edit')->with([
            'user' => $user,
            'plans' => $plans,
            'user_plan' => $user_plan,
            'yearly_user_number' => $yearly_user_number,
            'yearly_fee' => $yearly_fee,
        ]);
    }

    public function accountUpdate(UserRequest $request)
    {
        Cookie::queue(Cookie::forget('locale'));
        $user = Auth::user();
        $inputs = $request->all();
        if(empty($inputs['password'])){
            unset($inputs['password']);
        }
        try{
            $this->repUser->updateAccount($user, $inputs);
            App::setLocale($inputs['language']);
            // set cookie locale
            $date_format = config('constants.date_format.'.$inputs['language']);
            Cookie::queue('locale', $inputs['language'], time() + 60 * 60 * 24 * 365);
            Cookie::queue('date_format', $date_format, time() + 60 * 60 * 24 * 365);
            $search_condition = Cookie::get('search_condition');
            if($search_condition){
                $start_date = @$search_condition['start_date'];
                $end_date = @$search_condition['end_date'];
                if(isset($start_date) && isset($end_date)){
                    Cookie::queue(Cookie::forever('search_condition', [
                        'start_date' => date($date_format, strtotime($start_date)),
                        'end_date' => date($date_format, strtotime($end_date))
                    ]));
                }
            }
            return redirect()->back()->with('alert-success', trans('message.update_success', ['name' => trans('default.profile')]));
        } catch (\Exception $e){
            return redirect()->back()->with('alert-danger', trans('message.update_error', ['name' => trans('default.profile')]));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user_authority = config('constants.authority');
        $user = Auth::user();
        $user_destroy = $this->repUser->getById($id);
        if($user_destroy && $user->id != $id && $user->id == $user_destroy->created_id || !isset($user_destroy->created_id)){
            if($user_destroy->authority == $user_authority['agency']){
                $client_list = $this->repUser->getAllByField("created_id", $id);
                if($client_list && count($client_list) > 0){
                    $errors['msg'] = trans("message.error_delete_agency");
                    return Response::json(array(
                        'success' => false,
                        'errors' => $errors
                    ), 400);
                }
            }
            $count_page = $user_destroy->getCountConnectPage($id);
            if($count_page > 0){
                $errors['msg'] = trans("message.error_delete_user");
                return Response::json(array(
                    'success' => false,
                    'errors' => $errors
                ), 400);
            }
            $this->repUser->destroy($id);
            return Response::json(array('success' => true), 200);
        }
        $errors['msg'] = trans("message.common_error");
        return Response::json(array(
            'success' => false,
            'errors' => $errors
        ), 400);
    }

    public function generateDomain($input, $user_login_authority, $domain_follow_arr = array()){
        $input_authority = $input['authority'];
        $input_white_list_domain = @$input['white_list_domain'];
        $user_authority = config('constants.authority');
        $rules = [
            'white_list_domain' => ['required']
        ];
        $error_domain = [];
        if(!empty($input_white_list_domain) && !empty($input_authority)){
            $domain_list = explode(',', $input['white_list_domain']);
            if($user_login_authority == $user_authority['admin'] && $input_authority != $user_authority['admin']){
                $rules['white_list_domain'][] = 'regex:/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/';
            }elseif($user_login_authority == $user_authority['agency'] && !empty($domain_follow_arr)){
                $rules['white_list_domain'][] = Rule::in($domain_follow_arr);
            }elseif($user_login_authority == $user_authority['agency'] && empty($domain_follow_arr)){
                $rules['white_list_domain'][] =  'regex:/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/';
            }
            foreach ($domain_list as $domain){
                $validator = Validator::make(['white_list_domain' => $domain], $rules);
                if ($validator->fails()) {
                    $error_domain[] =  $domain;
                }
            }
            if(count($error_domain) > 0){
                return array(
                    'status' => false,
                    'data' => $error_domain
                );
            }else{
                return array(
                    'status' => true,
                    'data' => $domain_list
                );
            }
        }
        return array(
            'status' => true,
            'data' => []
        );
    }

    public function getListUser(Request $request){
        $login_user = Auth::user();
        $authority = config('constants.authority');
        $keyword_search = $request->input('keyword');
        $embot_env = $this->checkEmbotEnv();
        $embot_plan = config('constants.embot_plan');
        $plan_free = null;
        if($embot_env){
            $plan_free = $this->repEmbotPlan->getByCode($embot_plan['free']);
        }
        if($login_user->authority != $authority['client']){
            $inputs = $request->all();
            $start = (int)$inputs['start'];
            $length = (int)$inputs['length'];
            $group = $this->repMaster->getUserGroup('authority');

            $rows = $this->repUser->getAll($login_user, $start, $length, false, $keyword_search);
            $count = $this->repUser->getAll($login_user, null, null, true, $keyword_search);
            $data = new Collection();
            $cnt = ($start / $length) * $length + 1;
            $sns_list = $this->repMaster->getGroupFillSelectBox('service');
            foreach ($rows as $row) {
                $data_arr = [
                    'no' => $cnt++,
                    'id' => $row->id,
                    'user' => $row,
                    'authority' => @$group[$row->authority],
                    'name' => $row->name,
                    'email' => $row->email,
                    'company_name' => $row->company_name,
                    'login_user' => $login_user,
                    'user_created_id' => $row->created_id,
                    'confirmed_at' => $row->confirmed_at
                ];
                $user_number = $row->getCountUser($row->id);
                $bot_number = $row->getCountConnectPage($row->id);
                if($login_user->authority == $authority['admin'] && $row->authority == $authority['admin']){
                    $data_arr['user_number'] = $user_number.'/-';
                    $data_arr['bot_number'] = $bot_number.'/-';
                }elseif($login_user->authority == $authority['admin'] && $row->authority != $authority['admin']){
                    $data_arr['user_number'] = !empty($row->max_user_number) ? $user_number.'/'.$row->max_user_number : '-';
                    $data_arr['bot_number'] = !empty($row->max_bot_number) ? $bot_number.'/'.$row->max_bot_number : $bot_number;
                }elseif($login_user->authority == $authority['agency']){
                    $data_arr['bot_number'] = $bot_number.'/'.$row->max_bot_number;
                }
                if($embot_env && $row->authority == $authority['client']){
                    if(!isset($row->created_id) && !isset($row->embot_plan) && isset($row->confirmed_at)){
                        $data_arr['bot_number'] = @$plan_free->max_bot_number;
                    }elseif($row->embot_plan == $embot_plan['platinum']){
                        $data_arr['bot_number'] = '-';
                    }
                }
                if($login_user->authority == $authority['admin']){
                    $user_create = $row->getOneUser($row->created_id);
                    $data_arr['user_create'] = isset($row->created_id) ? @$user_create->name : '-';
                }
                $tmp_sns = [];
                if(!empty($row->sns_type_list)){
                    foreach ($row->sns_type_list as $sns_type){
                        $tmp_sns[] = @$sns_list[$sns_type];
                    }
                }
                $data_arr['sns_type_list'] = implode(', ', $tmp_sns);
                if($row->authority == $authority['admin']){
                    $data_arr['white_list_domain'] = '-';
                }else{
                    $data_arr['white_list_domain'] = isset($row->white_list_domain) && count($row->white_list_domain) >0 ? implode(', ', $row->white_list_domain) : '-';
                }
                $data->push($data_arr);
            }
            $dt = app('datatables');
            $request = $dt->getRequest();
            $request->merge( array( 'start' => 0 ) );
            return $dt->collection($data)
                ->addColumn('email', function ($row) {
                    $login_user = $row['login_user'];
                    if(isset($row["confirmed_at"]) || isset($row["user_created_id"])){
                        $bot_list_btn = '<a href="'. action('BotController@index', $row['id']) .'" class="bot-list" target="_blank">'.trans('button.bot_list').'</a>';
                        $edit_btn = '<a href="'. route("user.edit", $row['id']).'" class="btn-edit">'.trans('button.update').'</a>';
                        $remote_btn = '<a class="btn-delete" data-button="'.$row["id"].'" data-from="'. route("user.destroy",":id") .'" href="javascript:void(0)">'.trans('button.delete').'</a>';
                        $email = '<div class="email-detail">'. $row['email'] .'</div>';
                        if($login_user->id == $row['user_created_id'] || !isset($row['user_created_id'])){
                            return '<div class="todo-action-list">' . $email . $bot_list_btn . $edit_btn . $remote_btn.'</div>';
                        }else{
                            return '<div class="todo-action-list">' . $email . $bot_list_btn . $edit_btn .'</div>';
                        }
                    }else{
                        $label_pending = '<div class="name_all_dialog email-detail">' .$row["email"] . '</div><div class="label label-success label_all_dialog">'.trans('auth.pending_approval').'</div>';
                        $remote_btn = '';
                        if($login_user->authority = config('constants.authority.admin')){
                            $remote_btn = '<div class="action"><a class="btn-delete" data-button="'.$row["id"].'" data-from="'. route("user.destroy",":id") .'" href="javascript:void(0)">'.trans('button.delete').'</a></div>';
                        }
                        return '<div class="todo-action-list todo-pending">'.$label_pending . $remote_btn.'</div>';
                    }
                })
                ->setTotalRecords($count)->make(true);
        }
        return null;
    }

    public function checkTemplate($template_arr){
        $template_ids = [];
        $status = config('constants.active.enable');
        if(!empty($template_arr)){
            $templates = $this->repConnectPage->getAllByCondition(['template_flg' => $status], $template_arr);
            if($templates){
                foreach ($templates as $template){
                    $template_ids[] = $template->id;
                }
            }
        }
        return $template_ids;
    }

    public function accountInformation()
    {
        $user = Auth::user();
        $business_segments = $this->repMaster->getGroupFillSelectBox('business_segments');
        return view('accounts.account_information')->with([
            'user' => $user,
            'business_segments' => $business_segments
        ]);
    }

    public function updateAccountInformation(AccountRequest $request)
    {
        $user = Auth::user();
        $inputs = $request->all();
        $this->repUser->updateInformation($user, $inputs);
        if(isset($inputs["order_now"])){
            return redirect()->route('plan.index')->with('alert-success', trans('message.update_success', ['name' => trans('account.account_information')]));
        }
        return redirect()->back()->with('alert-success', trans('message.update_success', ['name' => trans('account.account_information')]));
    }

    public function UpdateUnsubscribe(UnsubscribeRequest $request){
        $user = Auth::user();
        if(!empty($user->plan)){
            $connects = $this->repConnect->getAllByField('user_id', $user->id);
            $sns_type = config('constants.group_type_service');
            foreach ($connects as $connect){
                $connect_pages = $this->repConnectPage->getAllByField('connect_id', $connect->id);
                foreach ($connect_pages as $connect_page){
                    if($connect_page->sns_type == $sns_type['facebook']){
                        if($connect_page->my_app_flg){
                            $page_access_token = $connect_page->origin_page_access_token;
                        } else {
                            $page_access_token = $connect_page->page_access_token;
                        }
                        Common::unfollowAPI($page_access_token);
                    }
                }
                $this->repConnectPage->deleteAllByField('connect_id', $connect->id);
                $this->repConnect->destroy($connect->id);
            }
            $inputs = array(
                'unsubscribed_at' => new \MongoDB\BSON\UTCDateTime(new \DateTime()),
            );
            $this->repUser->update($user, $inputs);
            return view('user.complete_unsubscribe');
        }
        return redirect()->back()->with('alert-danger', trans('message.delete_error', ['name' => $user->name]));
    }

    public function unsubscribe(){
        $user = Auth::user();
        if(!empty($user->plan)){
            return view('user.unsubscribe')->with([
                'user' => $user,
            ]);
        }
        abort(404);
    }

    public function getBotType($user){
        $sns_type_list = $this->repMaster->getGroupFillSelectBox('service');
        $group_type_service = config('constants.group_type_service');
        unset($sns_type_list[$group_type_service['chatwork']]);
        $user_authority = config('constants.authority');
        if($user->authority == $user_authority['agency'] && isset($user->sns_type_list) && count($user->sns_type_list)){
            foreach ($sns_type_list as $service_code => $service_name){
                if(!in_array($service_code, $user->sns_type_list)){
                    unset($sns_type_list[$service_code]);
                }
            }
        }
        return $sns_type_list;
    }

    public function getValueEmbotPlan(){
        $result = [
           'embot_yearly_user_free' => [],
           'embot_yearly_user_not_free' => [],
           'embot_plan_list' => [],
           'embot_plan' => [],
           'embot_yearly_user' => [],
           'embot_max_bot' => [],
        ];
        if($this->checkEmbotEnv()){
            $embot_plan = config('constants.embot_plan');
            $embot_yearly_user = config('constants.embot_yearly_user');
            $embot_yearly_user_free = config('constants.embot_yearly_user_free');
            $plan_tmp = [];
            foreach ($embot_plan as $name => $code){
                $plan_tmp[$code] = trans('embot_plan.plan_' . $name);
            }
            $embot_plan = $plan_tmp;
            $embot_yearly_user_tmp = $embot_yearly_user_free_tmp = [];
            foreach ($embot_yearly_user as $name => $code){
                if(is_numeric(trans('embot_plan.yearly_user_' . $name))){
                    $tmp_name = number_format(trans('embot_plan.yearly_user_' . $name));
                }else{
                    $tmp_name = trans('embot_plan.yearly_user_' . $name);
                }
                $embot_yearly_user_tmp[$code] = $tmp_name;
                if(in_array($code, $embot_yearly_user_free)){
                    $embot_yearly_user_free_tmp[$code] = $tmp_name;
                }
            }
            $embot_yearly_user = $embot_yearly_user_tmp;
            $embot_yearly_user_free = $embot_yearly_user_free_tmp;
            $embot_yearly_user_not_free = array_diff($embot_yearly_user, $embot_yearly_user_free);
            $embot_plan_list = $this->repEmbotPlan->getAll();
            foreach ($embot_plan_list as $plan){
                if(!isset($result['embot_max_bot'][$plan->code])){
                    $result['embot_max_bot'][$plan->code] = $plan->max_bot_number;
                }
            }
            $result['embot_plan'] = $embot_plan;
            $result['embot_yearly_user'] = $embot_yearly_user;
            $result['embot_yearly_user_free'] = $embot_yearly_user_free;
            $result['embot_yearly_user_not_free'] = $embot_yearly_user_not_free;
            $result['embot_plan_list'] = $embot_plan_list;
        }
        return $result;
    }

    public function settingPaymentGateway() {

        return view('accounts.payment_gateway');
    }
}
