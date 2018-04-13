<?php

namespace App\Repositories;

use App\Mongodb\TotalUserChat;

class TotalUserChatRepository extends BaseRepository
{

	public function __construct(TotalUserChat $totalUserChat)
	{
		$this->model = $totalUserChat;
	}

    public function save($model, $user, $total_user_chat, $collect_page_arr)
    {
        if(!isset($model)){
            $model = new $this->model();
        }
        $model->user_id = $user->id;
        $model->email = $user->email;
        $model->plan = $user->plan;
        $model->total_user_chat = $total_user_chat;
        $model->cpid = $collect_page_arr;
        $model->save();
    }

}
