<?php

namespace App\Repositories;

use App\Mongodb\LogReportUserScenario;
use Illuminate\Support\Facades\Log;
class LogReportUserScenarioRepository extends BaseRepository
{

    private $base_collection = '_report_user_scenarios';

	public function __construct(LogReportUserScenario $logReportUserScenario)
	{
		$this->model = $logReportUserScenario;
	}

    public function migrateInsert($connect_page_id, $scenario_total, $user_total, $date){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model->connect_page_id = $connect_page_id;
        $model->user_total = $user_total;
        $model->scenario_total = $scenario_total;
        $model->date = $date;
        $model->save();
    }

    public function getScenarioUserTotal($connect_page_id, $condition)
    {
        $start = date('Y-m-d', strtotime($condition['start_date']));
        $end = date('Y-m-d', strtotime($condition['end_date']));

        $query_condition["connect_page_id"] = $connect_page_id;
        $query_condition["date"] = ['$gte' => $start, '$lte' => $end];

        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->raw()->aggregate(array(
            [
                '$match' => $query_condition,
            ],
            array(
                '$group' => array(
                    '_id' => array(
                        'scenario_total' => '$scenario_total',
                    ),
                    "total" =>array( '$sum' => '$user_total' )
                )
            )
        ));
        return $model;
    }

    public function removeAllRecord($connect_page_id){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model->truncate();
    }

    public function getByDate($connect_page_id, $date){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        return $model->where("connect_page_id", $connect_page_id)
            ->where("date", $date)->first();
    }

    public function update($model, $connect_page_id, $scenario_total, $user_total, $date)
    {
        if(!isset($model)){
            $model = new $this->model;
        }
        $model->setCollection($connect_page_id . $this->base_collection);
        $model->connect_page_id = $connect_page_id;
        $model->user_total = $user_total;
        $model->scenario_total = $scenario_total;
        $model->date = $date;
        $model->save();
    }

}
