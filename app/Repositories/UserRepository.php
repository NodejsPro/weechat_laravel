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
        $user = new $this->model;
        $user->password         = bcrypt($inputs['password']);
        $user->email            = $inputs['email'];
        $user->authority        = $inputs['authority'];
        $user->created_id       = $created_id;
        $this->save($user, $inputs);

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
        if(isset($inputs['name'])){
            $user->name             = $inputs['name'];
        }
        if(isset($inputs['user_name'])){
            $user->user_name             = $inputs['user_name'];
        }
        if(isset($inputs['phone'])){
            $user->phone             = $inputs['phone'];
        }
        if(isset($inputs['avatar']) && !isset($inputs['avatar'])){
            $user->avatar             = $inputs['avatar'];
        }
        if(isset($inputs['confirmation_token'])){
            $user->confirmation_token = $inputs['confirmation_token'];
        }
        
        $user->save();
        return $user;
    }

    /**
     * Update a user.
     *
     * @return void
     */
    public function update($user, $inputs)
    {
        if(isset($inputs['password'])){
            $user->password     = bcrypt($inputs['password']);
        }
        if(isset($inputs['email'])){
            $user->email     = $inputs['email'];
        }
        if (isset($inputs['authority'])) {
            $user->authority  = $inputs['authority'];
        }
        if (isset($inputs['plan'])) {
            $user->plan  = $inputs['plan'];
        }
        $user_authority = config('constants.authority');
        if(isset($inputs['created_id']) && Auth::user()->authority == $user_authority['admin'] && $user->authority == $user_authority['client']){
            $user->created_id = $inputs['created_id'];
        }
        $this->save($user, $inputs);
    }

    public function updateAccount($user, $inputs)
    {
        $user->name = $inputs['name'];
        $user->locale = $inputs['language'];
        if(isset($inputs['password'])){
            $user->password = bcrypt($inputs['password']);
        }
        $user->save();
    }

    public function updateWhiteDomain($user, $white_domain){
        $user->white_list_domain = $white_domain;
        $user->save();
    }

    public function getAll($login_user, $offset = 0, $limit = 10, $is_count = false, $keyword_search = '')
    {
        $model = new $this->model;
        if($login_user->authority == config('constants.authority.agency')){
            $model = $model->where('created_id', $login_user->id);
        }else if($login_user->authority == config('constants.authority.admin')){
            $model = $model->where('_id', "<>", $login_user->id);
        }
        if ($keyword_search != '') {
            $model = $model->where(function ($model) use ($keyword_search) {
                        $model->where("name", "LIKE","%$keyword_search%")
                            ->orWhere("email", "LIKE", "%$keyword_search%")
                            ->orWhere("company_name", "LIKE", "%$keyword_search%");
                    });
        }
        if($is_count){
            return $model->count();
        }
        $model = $model->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
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

    public function updateInformation($user, $inputs)
    {
        $user->business_segments = $inputs['business_segments'];
        $user->company_name = $inputs['company_name'];
        $user->person_in_charge = $inputs['person_in_charge'];
        $user->zip_code = @$inputs['zip_code'];
        $user->address = $inputs['address'];
        $user->tel = $inputs['tel'];
        $user->url = @$inputs['url'];
        $user->confirmation_flg = 1;
        $user->save();
    }

    public function checkEmailReset($email){
        $model = new $this->model;
        $model = $model->where('email', $email)
                       ->where('deleted_at', null)
                       ->where('confirmation_token', null);
        return $model->first();
    }

    public function getUserPayment() {
        $model = new $this->model;
        $model = $model->where('plan', '<>', null)
                       ->where('plan', '<>', config('constants.user_plan.free')) ;
        return $model->get();
    }

    public function changePlan($user, $inputs) {
        if(isset($inputs['plan'])){
            $user->plan = $inputs['plan'];
        }
        if(isset($inputs['next_month_plan'])){
            $user->next_month_plan = $inputs['next_month_plan'];
        }
        $user->save();
    }

    public function nextMonthPlan($user, $plan_code){
        $user->next_month_plan = $plan_code;
        $user->save();
    }

    public function getUserPlan(){
        $model = new $this->model;
        $model = $model->select("id", "email", "plan")->whereNotNull('plan')
            ->whereNotNull('confirmed_at')
            ->whereNull('deleted_at');
        return $model->get();
    }

    public function getUserList(){
        $model = new $this->model;
        $model = $model->select('_id', 'name', 'email');
        $model = $model->whereNotNull('name');
        return $model->get();
    }

    public function getUserLogin($user_name, $password){
        $model = new $this->model;
        dd(bcrypt($password) .'---'. bcrypt('123456'));
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
                        ->where('code', $code);
        $model = $model->first();
        return $model;
    }

    public function updateCode($user, $code){
        $user->code = $code;
        $user->save();
        return $user;
    }

    public function updateToken($user, $token){
        $user->validate_token = $token;
        $user->save();
        return $user;
    }
}
