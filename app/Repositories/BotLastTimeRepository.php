<?php

namespace App\Repositories;

use App\Mongodb\BotLastTime;
use Illuminate\Support\Facades\Log;

class BotLastTimeRepository extends BaseRepository
{

	public function __construct(BotLastTime $botLastTime)
	{
		$this->model = $botLastTime;
	}

	public function store($inputs)
	{
        $bot_last_time = new $this->model;
        $bot_last_time->connect_page_id = $inputs['connect_page_id'];
        $bot_last_time->user_id = $inputs['user_id'];

		$this->save($bot_last_time, $inputs);
		return $bot_last_time;
	}

	private function save($bot_last_time, $inputs)
	{
	    if(isset($inputs['last_time'])) {
            $bot_last_time->last_time = $inputs['last_time'];
        }
        $bot_last_time->save();
	}

	public function update($bot_last_time, $inputs)
	{
		$this->save($bot_last_time, $inputs);
	}

	public function getOne($connect_page_id, $user_id)
	{
		$model = new $this->model;
		$model = $model->where('connect_page_id', '=', $connect_page_id)
				       ->where('user_id', '=', $user_id);
		return $model->first();
	}

}
