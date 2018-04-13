<?php

namespace App\Repositories;

use App\Mongodb\LogScenarioTotal;
use App\Mongodb\LogScenarioTotalTmp;
use App\Mongodb\NotificationHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogScenarioTotalTmpRepository extends BaseRepository
{

	public function __construct(LogScenarioTotalTmp $logScenarioTotal)
	{
		$this->model = $logScenarioTotal;
	}

    public function migrateInsert($connect_page_id, $scenario_id, $count, $date){
        $model = new $this->model;
        $model->connect_page_id = $connect_page_id;
        $model->scenario_id = $scenario_id;
        $model->count = $count;
        $model->date = $date;
        $model->save();
    }

    public function getTotalScenarioByDate($connect_page_id, $date = null){
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
                    "total" =>array( '$sum' => '$count' )
                )
            )
        ));
        return $model;
    }

    //シナリオ別実施数
    public function getTotalByScenario($connect_page_id, $condition){
        $start = date('Y-m-d', strtotime($condition['start_date']));
        $end = date('Y-m-d', strtotime($condition['end_date']));
        $model = new $this->model;
        $model = $model->raw()->aggregate(array(
            [
                '$match' => [
                    'connect_page_id'  => $connect_page_id,
                    'date' => ['$gte' => $start, '$lte' => $end]
                ],
            ],
            array(
                '$group' => array(
                    '_id' => array(
                        'scenario_id' => '$scenario_id'
                    ),
                    "total" =>array( '$sum' => '$count' )
                )
            )
        ));
        return $model;

    }

}
