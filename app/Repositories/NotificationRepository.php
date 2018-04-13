<?php

namespace App\Repositories;

use App\Mongodb\Notification;
use Illuminate\Support\Facades\Log;

class NotificationRepository extends BaseRepository
{

	public function __construct(Notification $notify)
	{
		$this->model = $notify;
	}

	public function store($inputs, $connect_page_id)
	{
		$notify = new $this->model;
		$notify->connect_page_id     = $connect_page_id;
        $notify->active_flg          = config('constants.active.enable');
        $this->save($notify, $inputs);
		return $notify;
	}

	public function update($notify, $inputs){
        $this->save($notify, $inputs);
        return $notify;
    }

    public function save($notify, $inputs){
        if (isset($inputs['notification_name'])) {
            $notify->name         = $inputs['notification_name'];
        }
        if (isset($inputs['scenario'])) {
            $notify->scenario_id  = $inputs['scenario'];
        }
        if (isset($inputs['repeat'])) {
            $notify->repeat       = $inputs['repeat'];
        }
        if (isset($inputs['repeat_every'])) {
            $notify->repeat_every = $inputs['repeat_every'];
        }
        if (isset($inputs['repeat_on'])) {
            $notify->repeat_on    = $inputs['repeat_on'];
        }
        if (isset($inputs['time'])) {
            $notify->time         = $inputs['time'];
        }
        if (isset($inputs['date'])) {
            $notify->time1        = $inputs['date'];
        }
        if (isset($inputs['hours'])) {
            $notify->time2        = $inputs['hours'];
        }
        if (isset($inputs['filter'])) {
            $notify->filter       = $inputs['filter'];
        }
        $notify->save();
    }

    public function getAllNotify()
    {
        $model = new $this->model;
        $model = $model->where('active_flg', 1);
        return $model->get();
    }

    public function activeNotification($notify, $active){
        $notify->active_flg = $active;
        $notify->save();
        return $notify;
    }
}
