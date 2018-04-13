<?php

namespace App\Repositories;

use App\Mongodb\GoogleSheetUser;
use Illuminate\Support\Facades\Log;
class GoogleSheetUserRepository extends BaseRepository
{
    /**
     * Create a new ChannelRepository instance.
     *
     * @param  App\Connect $connect
     * @return void
     */
    public function __construct(GoogleSheetUser $google_sheet_user)
    {
        $this->model = $google_sheet_user;
    }

    public function store($inputs, $user_id)
    {
        Log::info('$model->save();');
        $model = new $this->model;
        $model->user_id = $user_id;
        $model->email = $inputs['email'];
        $model->access_token = $inputs['access_token'];
        $model->save();
    }

    public function update($connect, $inputs)
    {
        $this->save($connect, $inputs);
        return $connect;
    }

    public function getByCondition($email, $user_id){
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
                        ->where('email', $email);
        return $model->first();
    }

    public function updateAccessToken($model, $access_token){
        $model->access_token = $access_token;
        $model->save();
    }
}
