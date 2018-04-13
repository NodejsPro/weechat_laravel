<?php

namespace App\Repositories;

use App\Mongodb\SessionUserTmp;
use Illuminate\Support\Facades\Log;

class SessionUserTmpRepository extends BaseRepository
{

	public function __construct(SessionUserTmp $sessionUser)
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

}
