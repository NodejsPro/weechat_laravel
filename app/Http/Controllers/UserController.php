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
    ){
        $this->repUser = $user;
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
        return view('user.create')->with([
            'users'             => null,
            'user_login'             => $user,
            'contact'             => $this->getContactForUser($user),
            'group' => $this->getAuthorityForUser($user)
        ]);
    }

    public function show(){

    }

    public function getListUser(Request $request){
        $login_user = Auth::user();
        $authority = config('constants.authority');
        if($login_user->authority != $authority['client']){
            $inputs = $request->all();
            $start = isset($inputs['start']) ? (int)$inputs['start'] : 0;
            $length = isset($inputs['length']) ? (int)$inputs['length'] : config('constants.per_page')[3];
            $group = config('constants.authority_lang');

            $rows = $this->repUser->getAll($login_user, $start, $length);
            $count = $this->repUser->getCount($login_user);
            $data = new Collection();
            $cnt = ($start / $length) * $length + 1;
            foreach ($rows as $row) {
                $data_arr = [
                    'no' => $cnt++,
                    'id' => $row->id,
                    'user' => $row,
                    'authority' => @$group[$row->authority],
                    'user_name' => $row->user_name,
                    'email' => $row->email,
                    'phone' => $row->phone,
                    'login_user' => $login_user,
                    'user_created_id' => $row->created_id,
                    'confirm_flg' => $row->confirm_flg,
                    'contact' => $row->contact,
                ];
                    $user_create = $this->repUser->getById($row->created_id);
                    $data_arr['user_create'] = @$user_create->user_name;
                $data->push($data_arr);
            }
            $dt = app('datatables');
            $request = $dt->getRequest();
            $request->merge( array( 'start' => 0 ) );
            return $dt->collection($data)
                ->addColumn('action', function ($row) {
                    $login_user = $row['login_user'];
                    if(isset($row["confirm_flg"]) && !$row["confirm_flg"]){
                        $label_pending = '<div class="label label-success label_all_dialog">'.trans('auth.pending_approval').'</div>';
                        $remote_btn = '';
                        if($login_user->authority = config('constants.authority.super_admin')){
                            $remote_btn = '<div class="action"><a class="btn-delete" data-button="'.$row["id"].'" data-from="'. route("user.destroy",":id") .'" href="javascript:void(0)">'.trans('button.delete').'</a></div>';
                        }
                        return '<div class="todo-action-list todo-pending">'.$label_pending . $remote_btn.'</div>';
                    }else{
                        $bot_list_btn = '<a href="'. action('BotController@index', $row['id']) .'" class="bot-list" target="_blank">'.trans('button.bot_list').'</a>';
                        $edit_btn = '<a href="'. route("user.edit", $row['id']).'" class="btn-edit">'.trans('button.update').'</a>';
                        $remote_btn = '<a class="btn-delete" data-button="'.$row["id"].'" data-from="'. route("user.destroy",":id") .'" href="javascript:void(0)">'.trans('button.delete').'</a>';
                        if($login_user->id == $row['user_created_id'] || !isset($row['user_created_id'])){
                            return '<div class="todo-action-list">' . $bot_list_btn . $edit_btn . $remote_btn.'</div>';
                        }else{
                            return '<div class="todo-action-list">' . $bot_list_btn . $edit_btn .'</div>';
                        }
                    }
                })
                ->setTotalRecords($count)->make(true);
        }
        return null;
    }

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

    public function getAuthorityForUser($user){
        $authority_arr = config('constants.authority');
        $authority = $user->authority;
        $user_authority = config('constants.authority');
        $user_authority_lang = config('constants.authority_lang');
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
        foreach ($authority_arr as $item => $key){
            $authority_arr[$key] = $user_authority_lang[$key];
            unset($authority_arr[$item]);
        }
        return $authority_arr;
    }

    public function getContactForUser($user){
        $user_authority = config('constants.authority');
        $contact = [];
        // get contact super admin
        $user_super_admin = $this->repUser->getKeyValue('user_name', '_id',[
            'authority' => $user_authority['super_admin']
        ]);
        $contact2 = [$user->_id => $user->name];
        return array_merge($user_super_admin->toArray(), $contact2);
    }
}
