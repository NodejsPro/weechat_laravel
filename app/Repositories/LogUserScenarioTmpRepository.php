<?php

namespace App\Repositories;

use App\Mongodb\LogScenarioTotal;
use App\Mongodb\LogUserScenario;
use App\Mongodb\LogUserScenarioTmp;
use App\Mongodb\NotificationHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogUserScenarioTmpRepository extends BaseRepository
{
	public function __construct(LogUserScenarioTmp $logUserScenario)
	{
		$this->model = $logUserScenario;
	}

    public function migrateInsert($connect_page_id, $user_id, $count, $date){
        $model = new $this->model;
        $model->connect_page_id = $connect_page_id;
        $model->user_id = $user_id;
        $model->count = $count;
        $model->date = $date;
        $model->save();
    }

    public function getUserActiveByDate($connect_page_id, $date = null){
        $condition["connect_page_id"] = $connect_page_id;
        if(isset($date)){
            $condition["date"] = $date;
        }
        $model = new $this->model;
        $model = $model->raw()->aggregate(array(
            [
                '$match' => $condition,
            ],
            array(
                '$group' => array(
                    '_id' => array(
                        'date' => '$date',
                    ),
                    'count' => array( '$sum' => 1 )
                )
            )
        ));
        return $model;
    }

    public function getScenarioUserDate($connect_page_id, $date = null)
    {
        $condition["connect_page_id"] = $connect_page_id;
        if(isset($date)){
            $condition["date"] = $date;
        }
        $model = new $this->model;
        $model = $model->raw()->aggregate(array(
            [
                '$match' => $condition,
            ],
            array(
                '$group' => array(
                    '_id' => array(
                        'date' => '$date',
                        'count' => '$count'
                    ),
                    'user_count' => array( '$sum' => 1 )
                )
            )
        ));
        return $model;
    }

}
