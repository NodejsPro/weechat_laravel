<?php

namespace App\Repositories;

use App\Mongodb\NotificationHistory;
use Illuminate\Support\Facades\Log;

class NotificationHistoryRepository extends BaseRepository
{

	public function __construct(NotificationHistory $notify_history)
	{
		$this->model = $notify_history;
	}

	public function getHistory($connect_page_id, $offset = 0, $limit = 10){
		$model = new $this->model;
		$model = $model->select('send_count', 'read_count', "time_of_message" )->where('connect_page_id', $connect_page_id)
			->skip($offset)
			->take($limit)
			->orderBy('time_of_message', 'DESC');
		return $model->get();
	}

	public function getMigrateData(){
		$model = new $this->model;
		$model = $model->select("connect_page_id", "notification_id", "time_of_message")
			->orderBy('connect_page_id')->orderBy('notification_id')->orderBy('time_of_message');
		return $model->get();
	}

	public function getHistoryNew($connect_page_id, $offset = 0, $limit = 10){
		$model = new $this->model;
		$model = $model->raw()->aggregate(array(
			[
				'$match' => ['connect_page_id' => $connect_page_id],
			],
			array(
				'$group' => array(
					'_id' => array(
						'notification_id' => '$notification_id',
						'push_time' => '$push_time'
					),
					'send_count' => array( '$sum' => '$send_count'),
					'read_count' => array( '$sum' => '$read_count')
				),
			),
			array(
				'$sort' => ['_id.push_time' => -1]
			),
			array(
				'$skip' => $offset
			),
			array(
				'$limit' => $limit
			)
		));
		return $model;
	}

	public function getHistoryCount($connect_page_id){
		$model = new $this->model;
		$model = $model->raw()->aggregate(array(
			[
				'$match' => ['connect_page_id' => $connect_page_id],
			],
			array(
				'$group' => array(
					'_id' => array(
						'notification_id' => '$notification_id',
						'push_time' => '$push_time'
					)
				)
			)
		));
		return $model;
	}
}
