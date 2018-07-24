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
        $user_contact = $this->repUser->getAllByField('created_id', $user->id);
        $user_id_arr = [];
        foreach($user_contact as $item){
            $user_id_arr[] = $item->id;
        }
        $user_edit_contact = !empty($user->contact) ? $user->contact : [];
        $user_create = $this->repUser->getAllByField('created_id', $user->id);
        foreach ($user_create as $item){
            $user_id_arr[] = $item->id;
        }
        $user_id_arr = array_merge($user_id_arr, $user_edit_contact);
        $user_id_arr = array_unique($user_id_arr);
        $contacts = $this->repUser->getContact($user_id_arr, 0, config('constants.per_page.5'));
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
            $keyword = isset($inputs['keyword']) ? trim($inputs['keyword']) : '';
            $group = config('constants.authority_lang');

            $rows = $this->repUser->getAll($keyword, $login_user, $start, $length);
            $count = $this->repUser->getCount($keyword, $login_user);
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
                        $bot_list_btn = '<a href="'. action('RoomController@index', $row['id']) .'" class="room-list" target="_blank">'.trans('button.room_list').'</a>';
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
        $contact_list = $this->checkContact(@$inputs['contact']);
        $user = Auth::user();
        $avatar = $request->file('avatar');
        $inputs['created_id'] = Auth::user()->id;
        $active = config('constants.active');
        $inputs['remember_flg'] = isset($inputs['remember_flg']) ? $inputs['remember_flg'] : $active['enable'];
        if(!empty($inputs['user_name']) && !empty($inputs['password'])){
            $inputs['confirm_flg'] = config('constants.active.enable');
        }else{
            $inputs['confirm_flg'] = config('constants.active.disable');
        }
        if(empty($avatar)){
            $inputs['avatar'] = '/images/profile.png';
        }else{
            $extension_file_upload = $avatar->getClientOriginalExtension();
            $file_config = config('constants.file_upload');
            $path = $file_config['file_path_base'] . DIRECTORY_SEPARATOR . $file_config['file_path_profile'] . DIRECTORY_SEPARATOR . uniqid().'.'.$extension_file_upload;
            $this->resizeImage($this->file_manager, $avatar, config('constants.size_image'), public_path($path));
            $inputs['avatar'] = $path;
        }
        try{
            $user = $this->repUser->store($inputs, $user->id);
            $this->updateContact($contact_list, $user);
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
            $user_contact = $this->repUser->getAllByField('created_id', $user_edit->id);
            $user_id_arr = [];
            foreach($user_contact as $item){
                if($item->id != $id){
                    $user_id_arr[] = $item->id;
                }
            }
            $user_edit_contact = !empty($user_edit->contact) ? $user_edit->contact : [];
            $user_create = $this->repUser->getAllByField('created_id', $user->id);
            foreach ($user_create as $item){
                if($item->id != $id){
                    $user_id_arr[] = $item->id;
                }
            }
            $user_id_arr = array_merge($user_id_arr, $user_edit_contact);
            $user_id_arr = array_unique($user_id_arr);
            $contacts = $this->repUser->getContact($user_id_arr, 0, config('constants.per_page.5'));
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
            $contact_list = [];
            if($edit_user && ($user->authority == $user_authority['super_admin'] || $user->id == $edit_user->created_id)){
                if(!empty($inputs['contact'])){
                    $contact_list = $this->checkContact(@$inputs['contact']);
                }
                $avatar = $request->file('avatar');
                if(!empty($avatar)){
                    $extension_file_upload = $avatar->getClientOriginalExtension();
                    $path = 'uploads/' . uniqid() . '.' . $extension_file_upload;
                    $this->resizeImage($this->file_manager, $avatar, config('constants.size_image'), public_path($path));
                    $inputs['avatar'] = $path;
                }
                $edit_user = $this->repUser->update($edit_user, $inputs);
                $this->updateContact($contact_list, $edit_user);
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
            //check all contact
            if(!empty($contact)){
                $result = $this->repUser->getList($contact, 0, config('constants.per_page.5'));
            }
//            $result = $this->repUser->getList($contact, 0, config('constants.per_page.5'));
        }catch(\Exception $e){
            Log::info(trans('message.user_contact_error'));
        }
        return $result;
    }

    public function updateContact($user_contact_list, $user_edit){
        // Neu user edit co 1 contact trong user user_contact_list
        // thì user đó cũng phải có contact của user edit
        $user_edit_id = $user_edit->id;
        $contact_arr = [];
//        $this->repUser->getContact();
        foreach($user_contact_list as $user){
            $contact = !empty($user->contact) ? $user->contact : [];
            if(!in_array($user_edit_id, $contact)){
                $contact[] = $user_edit_id;
                $this->repUser->updateContact($user, $contact);
            }
            $contact_arr[] = $user->id;
        }
        $this->repUser->updateContact($user_edit, $contact_arr);
    }

    public function accountEdit()
    {
        $user = Auth::user();
        $contacts = [];
        if(!empty($user->contact)){
            $contacts = $this->repUser->getContact($user->contact, 0, config('constants.per_page.5'));
        }
        $contact_name = [];
        foreach($contacts as $contact){
            $contact_name[] = $contact->user_name;
        }
        return view('user.my_edit')->with([
            'user' => $user,
            'contact_name' => empty($contact_name) ? ' ' : implode(', ', $contact_name),
        ]);
    }

    public function accountUpdate(UserRequest $request)
    {
        $user = Auth::user();
        $inputs = $request->all();
        if(empty($inputs['password'])){
            unset($inputs['password']);
        }
        try{
            $this->repUser->updateAccount($user, $inputs);
            return redirect()->back()->with('alert-success', trans('message.update_success', ['name' => trans('default.profile')]));
        } catch (\Exception $e){
            return redirect()->back()->with('alert-danger', trans('message.update_error', ['name' => trans('default.profile')]));
        }
    }
}
