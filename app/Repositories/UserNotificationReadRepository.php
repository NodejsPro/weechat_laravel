<?php

namespace App\Repositories;

use App\Mongodb\UserNotificationRead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserNotificationReadRepository extends BaseRepository
{

    public function __construct(UserNotificationRead $user_notification_read)
    {
        $this->model = $user_notification_read;
    }

    public function store($user_id, $user_notification_id)
    {
        $model = new $this->model;
        $model->user_id = $user_id;
        $model->user_notification_id = $user_notification_id;
        $model = $model->save();
        return $model;
    }

    public function getByUser($user_id, $user_notification_id){
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
                        ->where('user_notification_id', $user_notification_id);
        return $model->first();
    }


    public function getByUser2($user_id, $user_notification_id){
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
            ->where('user_notification_id', $user_notification_id);
        return $model->first();
    }

}
