<?php

namespace App\Repositories;

use App\Mongodb\LogEfoCv;
use Illuminate\Support\Facades\Log;

class LogEfoCvRepository extends BaseRepository
{
    private $base_collection = '_efo_cvs';

	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\Cv $cv
	 * @return void
	 */
	public function __construct(LogEfoCv $cv)
	{
		$this->model = $cv;
	}

    public function getUserByDate($connect_page_id, $condition, $flg = null){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $start = date('Y-m-d', strtotime($condition['start_date']));
        $end = date('Y-m-d', strtotime($condition['end_date']));
        $cv_match = [];
        if(isset($flg)){
            $cv_match['cv_flg'] = $flg;
        }

        $model = $model->raw()->aggregate([
            [
                '$match' => array_merge($cv_match,
                    [
                        "date" => ['$gte' => $start, '$lte' => $end],
                        "connect_page_id" => $connect_page_id,
                        "preview_flg" =>  null
                    ]
                )
            ],
            [
                '$group' => [
                    '_id' => [
                        "date" => '$date',
                    ],
                    "count" => ['$sum' => 1]
                ]
            ]
        ]);
        return $model;
    }

    public function getCountUser($connect_page_id, $condition, $flg = null, $groupByDevice = null){
        $start = date('Y-m-d', strtotime($condition['start_date']));
        $end = date('Y-m-d', strtotime($condition['end_date']));
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->where('connect_page_id', $connect_page_id);
        if(isset($flg)){
            $model = $model->where('cv_flg', $flg)->whereNull('preview_flg');
        }
        $model = $model->where('date', ">=", $start);
        $model = $model->where('date', '<=', $end);
        return $model->count();
    }

    public function getCvTime($connect_page_id, $condition){
        $start = date('Y-m-d', strtotime($condition['start_date']));
        $end = date('Y-m-d', strtotime($condition['end_date']));
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->raw()->aggregate([
            [
                '$match' => [
                    "connect_page_id" => $connect_page_id,
                    "date" => ['$gte' => $start, '$lte' => $end],
                    "cv_flg" => config('constants.active.enable'),
                    "preview_flg" =>  null
                ]
            ],
            [
                '$project' => [
                    'cv_11'=> [
                        '$cond' => [[ '$eq' => [ '$cv_minute', 11 ] ], 1, 0]
                    ],
                    'cv_10'=> [
                        '$cond' => [[ '$eq' => [ '$cv_minute', 10 ] ], 1, 0]
                    ],
                    'cv_9'=> [
                        '$cond' => [[ '$eq' => [ '$cv_minute', 9 ] ], 1, 0]
                    ],
                    'cv_8'=> [
                        '$cond' => [[ '$eq' => [ '$cv_minute', 8 ] ], 1, 0]
                    ],
                    'cv_7'=> [
                        '$cond' => [[ '$eq' => [ '$cv_minute', 7 ] ], 1, 0]
                    ],
                    'cv_6'=> [
                        '$cond' => [[ '$eq' => [ '$cv_minute', 6 ] ], 1, 0]
                    ],
                    'cv_5'=> [
                        '$cond' => [[ '$eq' => [ '$cv_minute', 5 ] ], 1, 0]
                    ],
                    'cv_4'=> [
                        '$cond' => [[ '$eq' => [ '$cv_minute', 4 ] ], 1, 0]
                    ],
                    'cv_3'=> [
                        '$cond' => [[ '$eq' => [ '$cv_minute', 3 ] ], 1, 0]
                    ],
                    'cv_2'=> [
                        '$cond' => [[ '$eq' => [ '$cv_minute', 2 ] ], 1, 0]
                    ],
                    'cv_1'=> [
                        '$cond' => [[ '$eq' => [ '$cv_minute', 1 ] ], 1, 0]
                    ],
                    'cv_0'=> [
                        '$cond' => [[ '$eq' => [ '$cv_minute', 0 ] ], 1, 0]
                    ],
                ]
            ],
            [
                '$group' => [
                    '_id' => '$cv_minute',
                    'cv_11' => ['$sum' => '$cv_11'],
                    'cv_10' => ['$sum' => '$cv_10'],
                    'cv_9' => ['$sum' => '$cv_9'],
                    'cv_8' => ['$sum' => '$cv_8'],
                    'cv_7' => ['$sum' => '$cv_7'],
                    'cv_6' => ['$sum' => '$cv_6'],
                    'cv_5' => ['$sum' => '$cv_5'],
                    'cv_4' => ['$sum' => '$cv_4'],
                    'cv_3' => ['$sum' => '$cv_3'],
                    'cv_2' => ['$sum' => '$cv_2'],
                    'cv_1' => ['$sum' => '$cv_1'],
                    'cv_0' => ['$sum' => '$cv_0'],
                ]
            ]
        ]);
        return $model;
    }

    public function getByCondition($connect_page_id, $colums_name, $condition, $cv_flg = null){
        $start = date('Y-m-d', strtotime($condition['start_date']));
        $end = date('Y-m-d', strtotime($condition['end_date']));
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $cv_match = [];
        if(isset($cv_flg)){
            $cv_match['cv_flg'] = $cv_flg;
        }
        $model = $model->raw()->aggregate([
            [
                '$match' => array_merge([
                    "date" => ['$gte' => $start, '$lte' => $end],
                    "connect_page_id" => $connect_page_id,
                    "preview_flg" =>  null
                ], $cv_match)
            ],
            [
                '$group' => [
                    '_id' => [
                        "$colums_name" => '$'.$colums_name,
                    ],
                    "count" => ['$sum' => 1]
                ]
            ]
        ]);
        return $model;
    }

    public function getListAnswerByDate($connect_page_id, $condition){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $start = date('Y-m-d', strtotime($condition['start_date']));
        $end = date('Y-m-d', strtotime($condition['end_date']));

        $model = $model->raw()->aggregate([
            [
                '$match' => [
                    "date" => ['$gte' => $start, '$lte' => $end],
                    "connect_page_id" => $connect_page_id,
                    "preview_flg" =>  null
                ]
            ],
            [
                '$group' => [
                    '_id' => [
                        "scenario_id" => '$scenario_id',
                        "position" => '$position'
                    ],
                    "count" => ['$sum' => 1]
                ]
            ]
        ]);
        return $model;
    }

    public function getScenarioByDate($connect_page_id, $condition){
        $start = date('Y-m-d', strtotime($condition['start_date']));
        $end = date('Y-m-d', strtotime($condition['end_date']));
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->raw()->aggregate(array(
            [
                '$match' => [
                    'connect_page_id'  => $connect_page_id,
                    'date' => ['$gte' => $start, '$lte' => $end],
                    "preview_flg" =>  null
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        "scenario_id" => '$scenario_id',
                        "position" => '$position',
                    ],
                    "count" => ['$sum' => 1]
                ]
            ]

        ));
        return $model;
    }

    public function  getUserPosition($connect_page_id, $user_id){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->select('position')->where('connect_page_id', $connect_page_id)
            ->where('user_id', $user_id);
        return $model->first();
    }

    public function  getAnalyticUserPosition($connect_page_id, $condition){
        $start = date('Y-m-d', strtotime($condition['start_date']));
        $end = date('Y-m-d', strtotime($condition['end_date']));
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->raw()->aggregate(array(
            [
                '$match' => [
                    'connect_page_id'  => $connect_page_id,
                    'date' => ['$gte' => $start, '$lte' => $end],
                    "preview_flg" =>  null
//                    'cv_flg'  => 0
                ],
            ],
            [
                '$group' => [
                    '_id' => [
                        "answer_count" => '$answer_count',
                        "cv_flg" => '$cv_flg',
                    ],
                    "count" => ['$sum' => 1]
                ]
            ]

        ));
        return $model;
    }

}
