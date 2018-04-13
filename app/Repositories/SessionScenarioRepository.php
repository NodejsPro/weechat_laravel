<?php

namespace App\Repositories;

use App\Mongodb\SessionScenario;
use Illuminate\Support\Facades\Log;

class SessionScenarioRepository extends BaseRepository
{

	public function __construct(SessionScenario $sessionScenario)
	{
		$this->model = $sessionScenario;
    }

    public function store($connect_page_id, $inputs)
    {
        $model = new $this->model;
        $model->connect_page_id = $connect_page_id;
        $model->scenario_id = $inputs["scenario_id"];
        $model->session_no = $inputs['session_no'];
        $model->date = $inputs['date'];
        $model->save();
        return $model;
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
                        'scenario_id' => '$scenario_id'
                    ),
                    'count' => array( '$sum' => '$session_no' )
                )
            )
        ));
        return $postedJobs;
    }

    public function updateSessionCount($connect_page_id, $inputs)
    {
        $model = new $this->model;
        $model->raw(function($collection) use ($connect_page_id, $inputs){
            return $collection->findOneAndUpdate([
                'connect_page_id' => $connect_page_id,
                'scenario_id' => $inputs["scenario_id"],
                'date' => $inputs["date"],
            ], [ '$inc' => [ 'session_no' => $inputs["session_no"] ] ], ['upsert' => true] );

        });
    }

}
