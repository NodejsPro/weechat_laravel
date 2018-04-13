<?php

namespace App\Repositories;

use App\Mongodb\UserScenarioTmp;
use Illuminate\Support\Facades\Log;

class UserScenarioTmpRepository extends BaseRepository
{

	public function __construct(UserScenarioTmp $userScenario)
	{
		$this->model = $userScenario;
    }
    
    public function store($connect_page_id, $inputs)
    {
        $model = new $this->model;
        $model->connect_page_id = $connect_page_id;
        $model->user_id = $inputs['user_id'];
        $model->count = $inputs['count'];
        $model->scenario_id = $inputs["scenario_id"];
        $model->date = $inputs['date'];
        $model->save();
        return $model;
    }

    public function getScenarioByDate($connect_page_id, $condition){
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
                        'date' => '$date'
                    ),
                    "total" =>array( '$sum' => '$count' )
                )
            )
        ));
        return $model;
    }

    public function getScenarioByUser($connect_page_id, $condition){
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
                        'user_id' => '$user_id'
                    ),
                    "total" =>array( '$sum' => '$count' )
                )
            )
        ));
        return $model;
    }

    public function getListByCondition($connect_page_id, $scenario_arr){
        $model = new $this->model;
        $model = $model->raw()->aggregate(array(
            [
                '$match' => [
                    'connect_page_id' => $connect_page_id,
                    'scenario_id' => ['$in' => $scenario_arr],
                ],
            ],
            array(
                '$group' => array(
                    '_id' => array(
                        'user_id' => '$user_id',
                        'scenario_id' => '$scenario_id'
                    )
                )
            )
        ));
        return $model;
    }


    public function getMigrationScenarioByUser($connect_page_id, $date){
        $model = new $this->model;
        $model = $model->raw()->aggregate(array(
            [
                '$match' => [
                    'connect_page_id'  => $connect_page_id,
                    'date' => $date
                ],
            ],
            array(
                '$group' => array(
                    '_id' => array(
                        'user_id' => '$user_id',
                    ),
                    "total" =>array( '$sum' => '$count' )
                )
            )
        ));
        return $model;
    }

    public function getMigrationSumByScenario($connect_page_id, $date){
        $model = new $this->model;
        $model = $model->raw()->aggregate(array(
            [
                '$match' => [
                    'connect_page_id'  => $connect_page_id,
                    'date' => $date
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
