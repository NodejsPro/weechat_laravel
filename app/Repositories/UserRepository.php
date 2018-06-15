<?php

namespace App\Repositories;


use App\Mongodb\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UserRepository extends BaseRepository
{
    /**
     * Create a new UserRepository instance.
     *
     * @param  App\User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * Create a company.
     *
     * @param  array  $inputs
     * @return App\User
     */
    public function store($inputs, $created_id)
    {
        Log::info('store');
        $user = new $this->model;
        $user->phone            = $inputs['phone'];
        $user->authority        = $inputs['authority'];
        $user->created_id       = $created_id;
        $user->remember_flg       = $inputs['remember_flg'];
        $user = $this->save($user, $inputs);

        return $user;
    }

    public function storeApi($user, $inputs){
        $user->user_name = $inputs['user_name'];
        $user->password = $inputs['password'];
        $user->confirm_flg = $inputs['confirm_flg'];
        $user->remember_flg = $inputs['remember_flg'];
        $user->save();
        return $user;
    }

    /**
     * Save the User.
     *
     * @param  App\User $user
     * @param  Array  $inputs
     * @return void
     */
    private function save($user, $inputs)
    {
        if(isset($inputs['user_name'])){
            $user->user_name = $inputs['user_name'];
        }
        if(isset($inputs['login_flg'])){
            $user->login_flg = $inputs['login_flg'];
        }
        if(isset($inputs['confirm_flg'])){
            $user->confirm_flg = $inputs['confirm_flg'];
        }
        if(isset($inputs['avatar'])){
            $user->avatar = $inputs['avatar'];
        }
        if(isset($inputs['password'])){
            $user->password = bcrypt($inputs['password']);
        }
        if(isset($inputs['contact'])){
            $user->contact = $inputs['contact'];
        }
        if(isset($inputs['phone'])){
            $user->phone = $inputs['phone'];
        }
        $user->save();
        return $user;
    }

    /**
     * Update a user.
     *
     * @return $user
     */
    public function update($user, $inputs)
    {
        if(isset($inputs['password'])){
            $user->password     = bcrypt($inputs['password']);
        }
        if (isset($inputs['authority'])) {
            $user->authority  = $inputs['authority'];
        }
        $this->save($user, $inputs);
        return $user;
    }

    public function updateAccount($user, $inputs)
    {
        $user->user_name = $inputs['user_name'];
        $user->phone = $inputs['phone'];
        if(isset($inputs['password'])){
            $user->password = bcrypt($inputs['password']);
        }
        $user->save();
    }

    public function updateContact($user, $contact){
        $user->contact = $contact;
        $user->save();
    }

    public function updateWhiteDomain($user, $white_domain){
        $user->white_list_domain = $white_domain;
        $user->save();
    }

    public function getList($user_ids, $offset = 0, $limit = 10)
    {
        $model = new $this->model;
        if(!empty($user_ids)){
            $model = $model->whereIn('_id', $user_ids);
        }
        $model = $model->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getListByPhone($phones)
    {
        $model = new $this->model;
        if(!empty($phones)){
            $model = $model->whereIn('phone', $phones);
        }
        return $model->get();
    }

    public function getAll($keyword_search, $user_login, $offset = 0, $limit = 10)
    {
        $model = new $this->model;
        if(!empty($keyword_search)){
            $model = $model->where(function ($model) use ($keyword_search) {
                $model->where("phone", "LIKE","%$keyword_search%")
                    ->orWhere("user_name", "LIKE", "%$keyword_search%");
            });
        }
        if($user_login->authority == config('constants.authority.super_admin')){
            $model = $model->where('_id', "<>", $user_login->id);
        }else if($user_login->authority != config('constants.authority.super_admin')){
            $model = $model->where('created_id', $user_login->id);
        }
        $model = $model->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getAllByNotCreate($user_login, $offset = 0, $limit = 10)
    {
        $model = new $this->model;
        if($user_login->authority == config('constants.authority.super_admin')){
            $model = $model->where(function ($model){
                $model->whereNull("created_id")
                    ->orWhere("created_id", "");
            });
//            $model = $model->where('_id', "<>", $user_login->id);
        }else if($user_login->authority != config('constants.authority.super_admin')){
            $model = $model->where('created_id', $user_login->id);
        }
        $model = $model->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getFull($id_except_arr, $offset = 0, $limit = 10){
        $model = new $this->model;
        $model = $model->where('confirm_flg', '<>', config('constants.active.disable'));
        if(!empty($id_except_arr)){
            $model = $model->whereNotIn('_id', $id_except_arr);
        }
        $model = $model->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getContact($contact, $offset = 0, $limit = 10, $confirm_flg = null){
        $model = new $this->model;
        if(isset($confirm_flg)){
            $model = $model->where('confirm_flg', '<>', config('constants.active.disable'));
        }
        $model = $model->whereIn('_id', $contact);
        $model = $model->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getCount($keyword_search, $user_login){
        $model = new $this->model;
        if(!empty($keyword_search)){
            $model = $model->where(function ($model) use ($keyword_search) {
                $model->where("phone", "LIKE","%$keyword_search%")
                    ->orWhere("user_name", "LIKE", "%$keyword_search%");
            });
        }
        if($user_login->authority == config('constants.authority.super_admin')){
            $model = $model->where('_id', "<>", $user_login->id);
        }else if($user_login->authority != config('constants.authority.super_admin')){
            $model = $model->where('created_id', $user_login->id);
        }
        return $model->count();
    }

    public function getCountByNotCreate($user_login){
        $model = new $this->model;
        if($user_login->authority == config('constants.authority.super_admin')){
            $model = $model->where('_id', "<>", $user_login->id);
        }else if($user_login->authority != config('constants.authority.super_admin')){
            $model = $model->where('created_id', $user_login->id);
        }
        return $model->count();
    }

    public function getUserByKeywordSearch($keyword_search){
        if($keyword_search){
            $model = new $this->model;
            $model = $model->where(function ($model) use ($keyword_search) {
                $model->where("name", "LIKE","%$keyword_search%")
                    ->orWhere("email", "LIKE", "%$keyword_search%")
                    ->orWhere("company_name", "LIKE", "%$keyword_search%");
            });
            $model = $model->pluck('_id');
            return $model;
        }
        return [];
    }

    public function checkValidationEmail($email, $_id = null){
        $model = new $this->model;
        $model = $model->where('email', $email);
        if( !empty($_id)){
            $model = $model->where('_id', '!=', $_id);
        }
        return $model->first();
    }

    public function getUserListChange($except_arr = null){
        $model = new $this->model;
        $model = $model->where('authority', '<>', config('constants.authority.client'));
        if(!empty($except_arr)){
            $model = $model->whereNotIn('_id', $except_arr);
        }
        $model = $model->pluck('name', '_id');
        return $model;
    }

    public function checkExistEmail($email){
        $model = new $this->model;
        $model = $model->where('email', $email)
                       ->where('deleted_at', null);
        return $model->first();
    }

    public function makeConfirmationToken($email, $key) {
        $model = new $this->model;
        $model->email = $email;
        $model->authority = config('constants.authority.client');
        $model->confirmation_token = hash_hmac(
            'sha256',
            str_random(60).$email,
            $key
        );
        $model->save();
        return $model->confirmation_token;
    }

    public function isSended($user) {
        $user->confirmation_sent_at = Carbon::now();
        $user->save();
    }

    public function confirm($user, $inputs) {
        $user->name = $inputs['name'];
        $user->password = bcrypt($inputs['password']);
        $user->plan = config('constants.user_plan.free');
        $user->confirmed_at = Carbon::now();
        $user->confirmation_token = null;
        $user->save();
    }

    public function getUserActive($value){
        $model = new $this->model;
        $model =  $model->where(function ($model) use ($value) {
            $model->where("user_name", $value)
                ->orWhere("phone", $value);
        });
        $model = $model->where('confirm_flg', config('constants.active.enable'));
        return $model->first();
    }

    public function getUserConditionActive($field, $value){
        $model = new $this->model;
        $model = $model->where($field, $value)
                        ->where('confirm_flg', config('constants.active.enable'));
        return $model->first();
    }

    public function getUserLogin($user_name, $password){
        $model = new $this->model;
        $model = $model->where('user_name', $user_name)
                        // ->where('password', bcrypt($password));
                       ->where('confirmation_token', null);
                // $model = $model->where('user_name', 'super_admin');;
        $model = $model->first();
        return $model;
    }

    public function getUserCode($phone, $code){
        $model = new $this->model;
        $model = $model->where('phone', $phone)
                        ->where('code', $code)
                        ->where('confirm_flg', config('constants.active.enable'));
        $model = $model->first();
        return $model;
    }

    public function getUserByField($field_name, $field_code){
        $model = new $this->model;
        $model = $model->where($field_name, $field_code)
            ->where('confirm_flg', config('constants.active.enable'));
        $model = $model->first();
        return $model;
    }

    public function updateCode($user, $code){
        $user->code = $code;
        $user->save();
        return $user;
    }

    public function updateStatus($user, $inputs){
        if(isset($inputs['code'])){
            $user->code = $inputs['code'];
        }
        if(isset($inputs['validate_token'])){
            $user->validate_token = $inputs['validate_token'];
        }
        if(isset($inputs['login_flg'])){
            $user->login_flg = $inputs['login_flg'];
        }
        $user->save();
        return $user;
    }

    public function updateToken($user, $token){
        $user->validate_token = $token;
        $user->save();
        return $user;
    }

    public function getUserByPhone($phone){
        $model = new $this->model;
        $model = $model->where('phone', $phone)
            ->where('confirm_flg', config('constants.active.enable'));
        $model = $model->first();
        return $model;
    }

    public function getUserById($user_id){
        $model = new $this->model;
        $model = $model->where('_id', $user_id)
            ->where('confirm_flg', config('constants.active.enable'));
        $model = $model->first();
        return $model;
    }

    public function getDataUpdateByDate($date_time){
        $model = new $this->model;
        $model = $model->where('updated_at', '<=', $date_time)
            ->where('remember_flg', '!=', config('constants.active.disable'))
            ->where('confirm_flg', config('constants.active.enable'));
        $model = $model->get();
        return $model;
    }
}
