<?php

namespace App\Repositories;

use App\Mongodb\LogReportTotalDateTmp;

class LogReportTotalDateTmpRepository extends BaseRepository
{
	public function __construct(LogReportTotalDateTmp $logReportTotalDate)
	{
		$this->model = $logReportTotalDate;
	}

    public function migrateInsert($connect_page_id, $new_user_cnt, $repeat_user_cnt, $scenario_cnt, $date){
        $model = new $this->model;
        $model->connect_page_id = $connect_page_id;
        $model->date = $date;
        $model->new_user = $new_user_cnt;
        $model->repeat_user = $repeat_user_cnt;
        $model->scenario_total = $scenario_cnt;
        $model->save();
    }

    public function update($model, $connect_page_id, $new_user_cnt, $repeat_user_cnt, $scenario_cnt, $date){
        if(!isset($model)){
            $model = new $this->model;
        }
        $model->connect_page_id = $connect_page_id;
        $model->date = $date;
        $model->new_user = $new_user_cnt;
        $model->repeat_user = $repeat_user_cnt;
        $model->scenario_total = $scenario_cnt;
        $model->save();
    }

    public function getByDate($connect_page_id, $date){
        $model = new $this->model;
        return $model->where("connect_page_id", $connect_page_id)
                     ->where("date", $date)->first();
    }

    public function getTotalByDate($connect_page_id, $condition)
    {
        $model = new $this->model;
        $start = date('Y-m-d', strtotime($condition['start_date']));
        $end = date('Y-m-d', strtotime($condition['end_date']));
        $model = $model->select("date","new_user", "repeat_user", "scenario_total")
            ->where('connect_page_id', $connect_page_id)
            ->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->orderBy('date', 'ASC');
        return $model->get();


    }

    public function getCountUserForPlan($connect_page_id)
    {
        $model = new $this->model;
        $model = $model->raw()->aggregate(array(
            [
                '$match' => [
                    'connect_page_id'  => $connect_page_id,
                    'preview_flg'  => ['$ne' => 1]
                ],
            ],
            array(
                '$group' => array(
                    '_id' => array(
                        'connect_page_id' => '$connect_page_id'
                    ),
                    "total" =>array( '$sum' => '$new_user' )
                )
            )
        ));
        return $model;
    }
}
