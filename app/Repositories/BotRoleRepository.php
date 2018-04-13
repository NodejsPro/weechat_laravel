<?php

namespace App\Repositories;


use App\Mongodb\BotRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BotRoleRepository extends BaseRepository
{
    /**
     * Create a new UserRepository instance.
     *
     * @param  App\User $user
     * @return void
     */
    public function __construct(BotRole $role)
    {
        $this->model = $role;
    }

    /**
     * Create a company.
     *
     * @param  array  $inputs
     * @return App\User
     */
    public function store($connect_page_id, $own_id, $user_id, $inputs)
    {
        $bot_roles = new $this->model;
        $bot_roles->connect_page_id = $connect_page_id;
        $bot_roles->authority = $inputs['bot_role_authority'];
        $bot_roles->own_id = $own_id;
        $bot_roles->user_id = $user_id;
        $bot_roles->confirmation_token = hash_hmac(
            'sha256',
            str_random(60).$inputs['email'],
            config('app.key')
        );
        $bot_roles->confirmation_sent_at = new \MongoDB\BSON\UTCDateTime(new \DateTime());
        $this->save($bot_roles, $inputs);
        return $bot_roles;
    }

    /**
     * Save the User.
     *
     * @param  App\User $user
     * @param  Array  $inputs
     * @return void
     */
    private function save($role, $inputs)
    {
        $role->save();
    }

    /**
     * Update a user.
     *
     * @return void
     */
    public function update($role, $inputs)
    {
        if(isset($inputs['confirmation_token_reset_flg']) && $inputs['confirmation_token_reset_flg']){
            $role->confirmed_at = new \MongoDB\BSON\UTCDateTime(new \DateTime());
            $role->confirmation_token = null;
        }
        if(isset($inputs['bot_role_authority'])){
            $role->authority = $inputs['bot_role_authority'];
        }
        $this->save($role, $inputs);
        return $role;
    }

    public function getListShareByUser($user_id) {
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
                       ->where('confirmation_token', null);
        return $model->get();
    }

    public function getBotShareByUser($connect_page_id, $user_id, $confirmation_token_flg = true) {
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
                       ->where('connect_page_id', $connect_page_id);
        if($confirmation_token_flg){
            $model = $model->where('confirmation_token', null);
        }
        return $model->first();
    }

}
