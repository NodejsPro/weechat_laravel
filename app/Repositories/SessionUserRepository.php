<?php

namespace App\Repositories;

use App\Mongodb\SessionUser;
use Illuminate\Support\Facades\Log;

class SessionUserRepository extends BaseRepository
{

	public function __construct(SessionUser $sessionUser)
	{
		$this->model = $sessionUser;
	}
    public function getByDate($connect_page_id, $condition){
        $model = new $this->model;
        $start = date('Y-m-d', strtotime($condition['start_date']));
        $end = date('Y-m-d', strtotime($condition['end_date']));

        $postedJobs = $model->raw()->aggregate(array(
            [
                '$match' => [
                    'connect_page_id'  => $connect_page_id,
                    'date' => ['$gte' => $start, '$lte' => $end]
                ],
            ],
            array(
                '$group' => array(
                    '_id' => array(
                        'user_id' => '$user_id'
                    ),
                    'count' => array( '$sum' => '$session_no' )
                )
            )
        ));
        return $postedJobs;
    }

    public function store($connect_page_id, $inputs)
    {
        $model = new $this->model;
        $model->connect_page_id = $connect_page_id;
        $model->user_id =  $inputs['user_id'];;
        $model->session_no = $inputs['session_no'];
        $model->date = $inputs['date'];
        $model->save();
        return $model;
    }

    public function updateSessionCount($connect_page_id, $user_id, $date)
    {
        $model = new $this->model;
        $model->raw(function($collection) use ($connect_page_id, $user_id, $date){
            return $collection->findOneAndUpdate([
                'connect_page_id' => $connect_page_id,
                'user_id' => $user_id,
                'date' => $date,
            ], [ '$inc' => [ 'session_no' => 1 ] ], ['upsert' => true] );

        });
    }

}
