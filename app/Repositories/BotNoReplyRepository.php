<?php

namespace App\Repositories;

use App\Mongodb\BotNoReply;
use Illuminate\Support\Facades\Log;

class BotNoReplyRepository extends BaseRepository
{

	public function __construct(BotNoReply $botNoReply)
	{
		$this->model = $botNoReply;
	}

    public function getAllUser($connect_page_id){
        $model = new $this->model;
        $model = $model->raw()->aggregate(array(
            [
                '$match' => [
                    'connect_page_id'  => $connect_page_id
                ],
            ],
            array(
                '$group' => array(
                    '_id' => array(
                        'user_id' => '$user_id'
                    )
                )
            )
        ));
        return $model;
    }
}
