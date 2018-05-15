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
        $this->repUser = $user;
        $this->file_manager = new ImageManager(array('driver' => 'gd'));
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
        $contacts = $this->repUser->getFull(0, config('constants.per_page')[4]);
        return view('user.create')->with([
            'users'             => null,
            'user_login'             => $user,
            'contacts'             => $contacts,
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
                ->addColumn('contact', function ($row) {
                    $contacts = $row['contact'];
                    $result = [];
                    if(!empty($contacts)){
                        foreach ($contacts as $key => $contact){
                            $user = $this->repUser->getById($contact);
                            if($user){
                                $result[] = $user->user_name;
                            }
                        }
                    }
                    return implode(', ', $result);
                })
                ->setTotalRecords($count)->make(true);
        }
        return null;
    }

    public function store(UserRequest $request)
    {
        $inputs = $request->all();
        $inputs['contact'] = $this->checkContact(@$inputs['contact']);
        $user = Auth::user();
        $avatar = $request->file('avatar');
        $inputs['created_id'] = Auth::user()->id;
        if(!empty($inputs['user_name']) && !empty($inputs['password'])){
            $inputs['confirm_flg'] = config('constants.active.enable');
        }else{
            $inputs['confirm_flg'] = config('constants.active.disable');
        }
        if(empty($avatar)){
            $inputs['avatar'] = '/images/profile.png';
        }else{
            $extension_file_upload = $avatar->getClientOriginalExtension();
            $path = 'uploads/' . uniqid().'.'.$extension_file_upload;
            $this->resizeImage($this->file_manager, $avatar, config('constants.size_image'), public_path($path));
            $inputs['avatar'] = $path;
        }
        try{
            $this->repUser->store($inputs, $user->id);
            return redirect('user')->with('alert-success', trans('message.save_success', ['name' => trans('default.user')]));
        } catch(\Exception $e){
            return redirect()->back()->with('alert-danger', trans('message.save_error', ['name' => trans('default.user')]));
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $user_edit = $this->repUser->getById($id);
        $user_authority = config('constants.authority');
        if($user_edit && ($user->authority == $user_authority['super_admin'] || $user->id == $user_edit->created_id)){
            $user = Auth::user();
            $contacts = $this->repUser->getFull(0, config('constants.per_page')[4]);
            dd($contacts);
            return view('user.create')->with([
                'user'              => $user_edit,
                'users'             => null,
                'user_login'             => $user,
                'contacts'             => $contacts,
                'group' => $this->getAuthorityForUser($user),
            ]);
        }
        abort('404');
    }

    public function update(UserRequest $request, $id)
    {
        $inputs = $request->all();
        $user = Auth::user();
        try{
            $edit_user = $this->repUser->getById($id);
            $user_authority = config('constants.authority');
            if($edit_user && ($user->authority == $user_authority['super_admin'] || $user->id == $edit_user->created_id)){
                if(!empty($inputs['contact'])){
                    $inputs['contact'] = $this->checkContact(@$inputs['contact']);
                }
                $avatar = $request->file('avatar');
                if(!empty($avatar)){
                    $extension_file_upload = $avatar->getClientOriginalExtension();
                    $path = 'uploads/' . uniqid() . '.' . $extension_file_upload;
                    $this->resizeImage($this->file_manager, $avatar, config('constants.size_image'), public_path($path));
                    $inputs['avatar'] = $path;
                }
                $this->repUser->update($edit_user, $inputs);
                return redirect('user')->with('alert-success', trans('message.update_success', ['name' => trans('default.user')]));
            }
            abort('404');
        } catch (\Exception $e){
            return redirect()->back()->with('alert-danger', trans('message.update_error', ['name' => trans('default.user')]));
        }
    }

    public function destroy(Request $request, $id)
    {
        Log::info('user destroy');
        $user_authority = config('constants.authority');
        $user = Auth::user();
        $user_destroy = $this->repUser->getById($id);
        if($user_destroy && $user->id != $id && ($user->authority == $user_authority['super_admin'] ||  $user->id == $user_destroy->created_id)){
            $this->repUser->destroy($id);
            return Response::json(array('success' => true), 200);
        }
        $errors['msg'] = trans("message.common_error");
        return Response::json(array(
            'success' => false,
            'errors' => $errors
        ), 400);
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

    public function checkContact($data){
        $result = [];
        try{
            $contact = json_decode($data, true);
            $contact = array_values($contact);
            foreach ($contact as $item){
                $user = $this->repUser->getById($item);
                if($user){
                    $result[] = $item;
                }
            }
        }catch(\Exception $e){
            Log::info(trans('message.user_contact_error'));
        }
        return $result;
    }

    public function accountEdit()
    {
        $user = Auth::user();
        $current_lang = Lang::locale();
        return view('user.my_edit')->with([
            'user' => $user,
        ]);
    }
}
