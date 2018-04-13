<?php

namespace App\Repositories;

use App\Mongodb\UserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserNotificationRepository extends BaseRepository
{
    /**
     * Create a new UserRepository instance.
     *
     * @param  App\userNotification $userNotification
     * @return void
     */
    public function __construct(UserNotification $userNotification)
    {
        $this->model = $userNotification;
    }

    /**
     * Create a company.
     *
     * @param  array  $inputs
     * @return $model
     */
    public function store($inputs, $user_id)
    {
        $model = new $this->model;
        $model->user_created_id = $user_id;
        $model = $this->save($model, $inputs);
        return $model;
    }

    /**
     * Update a user notification.
     *
     * @return $model
     */
    public function update($model, $inputs)
    {
        $model = $this->save($model, $inputs);
        return $model;
    }

    private function save($model, $inputs){
        $model->title = $inputs['title'];
        $model->detail = $inputs['detail'];
        $model->start_date = $inputs['start_date'];
        $model = $model->save();
        return $model;
    }

    public function getAll($offset = 0, $limit = 10){
        $model = new $this->model;
        $model = $model->skip($offset)
            ->take($limit)
            ->orderBy('start_date', 'DESC')
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getCount(){
        $model = new $this->model;;
        return $model->count();
    }

    public function getAllBeforeStartDate($start_date){
        $model = new $this->model;
        $model = $model->where('start_date', '<=', $start_date)
            ->orderBy('start_date', 'DESC')
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }
}
