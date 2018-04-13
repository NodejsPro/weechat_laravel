<?php

namespace App\Repositories;

use App\Mongodb\LogChatMessage;
use App\Mongodb\NotificationHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogChatMessageRepository extends BaseRepository
{

	public function __construct(LogChatMessage $logChatMessage)
	{
		$this->model = $logChatMessage;
	}

//    public function getScenarioByDate($connect_page_id, $condition){
//        $model = new $this->model;
//        $start = date('Y-m-d 00:00:01', strtotime($condition['start_date']));
//        $end = date('Y-m-d 23:59:59', strtotime($condition['end_date']));
////        $start = new \DateTime($start);
////        $end = new \DateTime($end);
//        $postedJobs = $model->raw()->aggregate(array(
//            [
//                '$match' => [
//                    'connect_page_id'  => $connect_page_id,
//                    'type'  => 1,
//                    'error_flg'  => null,
//                    'notification_id'  => null,
//                    'scenario_id'  =>['$ne' => null],
//                    'created_at' => ['$gte' => new \MongoDB\BSON\UTCDateTime(strtotime($start) * 1000), '$lte' => new \MongoDB\BSON\UTCDateTime(strtotime($end) * 1000)]
//                ],
//            ],
//            array(
//                '$group' => array(
//                    '_id' => array(
//                        "yearMonthDay" => array(
//                            '$dateToString' => array(
//                                "format" => "%Y-%m-%d",
//                                "date" => '$created_at'
//                            )
//                        )
//                    ),
//                    'count' => array( '$sum' => 1 )
//                )
//            )
//        ));
//        return $postedJobs;
//    }

//    public function getActiveUserByDate($connect_page_id, $condition){
//        $model = new $this->model;
//        $start = date('Y-m-d 00:00:01', strtotime($condition['start_date']));
//        $end = date('Y-m-d 23:59:59', strtotime($condition['end_date']));
////        $start = new \DateTime($start);
////        $end = new \DateTime($end);
//        $postedJobs = $model->raw()->aggregate(array(
//            [
//                '$match' => [
//                    'connect_page_id'  => $connect_page_id,
//                    'type'  => 1,
//                    'error_flg'  => null,
//                    'notification_id'  => null,
//                    'user_id'  =>['$ne' => null],
//                    'created_at' => ['$gte' => new \MongoDB\BSON\UTCDateTime(strtotime($start) * 1000), '$lte' => new \MongoDB\BSON\UTCDateTime(strtotime($end) * 1000)]
//                ],
//            ],
//            array(
//                '$group' => array(
//                    '_id' => array(
//                        "yearMonthDay" => array(
//                            '$dateToString' => array(
//                                "format" => "%Y-%m-%d",
//                                "date" => '$created_at'
//                            )
//                        ),
//                        'user_id' => '$user_id'
//                    )
//                )
//            ),
//            array(
//                '$group' => array(
//                    '_id' => '$_id.yearMonthDay',
//                    'count' => array( '$sum' => 1 )
//                )
//            ),
//
//        ));
//        return $postedJobs;
//    }

//    public function getScenarioByUser($connect_page_id, $condition){
//        $model = new $this->model;
//        $start = date('Y-m-d 00:00:01', strtotime($condition['start_date']));
//        $end = date('Y-m-d 23:59:59', strtotime($condition['end_date']));
////        $start = new \DateTime($start);
////        $end = new \DateTime($end);
//        $postedJobs = $model->raw()->aggregate(array(
//            [
//                '$match' => [
//                    'connect_page_id'  => $connect_page_id,
//                    'user_id'  =>['$ne' => null],
//                    'created_at' => ['$gte' => new \MongoDB\BSON\UTCDateTime(strtotime($start) * 1000), '$lte' => new \MongoDB\BSON\UTCDateTime(strtotime($end) * 1000)],
//                    'type'  => 1,
//                    'error_flg'  => null,
//                    'notification_id'  => null,
//                    'scenario_id'  =>['$ne' => null]
//                ],
//            ],
//            array(
//                '$group' => array(
//                    '_id' => array(
//                        'user_id' => '$user_id',
//                        'scenario_id' => '$scenario_id'
//                    )
//                )
//            ),
//            array(
//                '$group' => array(
//                    '_id' => '$_id.user_id',
//                    'count' => array( '$sum' => 1 )
//                )
//            ),
//
//        ));
//        return $postedJobs;
//    }

    public function getLog($connect_page_id, $start_date, $end_date, $params){
        $model = new $this->model;
        $start = date('Y-m-d 00:00:01', strtotime($start_date));
        $end = date('Y-m-d 23:59:59', strtotime($end_date));
        $start = new \DateTime($start);
        $end = new \DateTime($end);

        $model = $model->where('connect_page_id', $connect_page_id)
                        ->where('created_at', '>=', $start)
                        ->where('created_at', '<=', $end)
                        ->skip($params["start"])
                        ->take($params["length"])
                        ->orderBy($params["sort_column"], $params["dir"]);
        return $model->get();
    }

    public function countLog($connect_page_id, $start_date, $end_date){
        $model = new $this->model;
        $start = date('Y-m-d 00:00:01', strtotime($start_date));
        $end = date('Y-m-d 23:59:59', strtotime($end_date));
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $model = $model->where('connect_page_id', $connect_page_id)
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end);

        return $model->count();
    }

    public function getAllLog($connect_page_id, $condition){
        $model = new $this->model;
        $start = date('Y-m-d 00:00:01', strtotime($condition['start_date']));
        $end = date('Y-m-d 23:59:59', strtotime($condition['end_date']));
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $model = $model->where('connect_page_id', $connect_page_id)
                       ->where('created_at', '>=', $start)
                       ->where('created_at', '<=', $end)
                       ->where('type', 1)
                       ->whereNull('error_flg')
                       ->whereNull('notification_id')
                       ->orderBy('created_at', 'ASC');
        return $model->get();
    }

    public function getAllSection($connect_page_id, $condition){
        $model = new $this->model;
        $start = date('Y-m-d 00:00:01', strtotime($condition['start_date']));
        $end = date('Y-m-d 23:59:59', strtotime($condition['end_date']));
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $model = $model->where('connect_page_id', $connect_page_id)
                        ->where('created_at', '>=', $start)
                        ->where('created_at', '<=', $end)
//            ->where("scenario_id", "59fae272059408ac28460cd4")
                        ->whereNull('error_flg')
                        ->whereNull('notification_id')
                        ->orderBy('user_id', 'ASC')
                        ->orderBy('created_at', 'ASC');
        return $model->get();
    }

    public function getAllSection1($connect_page_id){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
            ->whereNull('error_flg')
            ->whereNull('notification_id')
            ->orderBy('user_id', 'ASC')
            ->orderBy('created_at', 'ASC');
        return $model->get();
    }

    public function getAllSection2($connect_page_id, $user_id){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
            ->where('user_id', $user_id)
            ->whereNull('error_flg')
            ->whereNull('notification_id')
//            ->orderBy('user_id', 'ASC')
            ->orderBy('created_at', 'ASC');
        return $model->get();
    }

    public function updateGmo($connect_page_id, $page_id)
    {
        $model = new $this->model;
        $model->where('page_id', $page_id)->update(['connect_page_id' => $connect_page_id]);
    }

    public function getMessage($connect_page_id, $user_id, $created_at = null, $limit = null, $last_time_of_message = null) {
        $model = new $this->model;
        if(empty($limit)){
            $limit = config('constants.log_message_limit');
        }
        $model = $model->where('connect_page_id', $connect_page_id)
                        ->where('user_id', $user_id)
                        ->whereNull('background_flg')
                        ->whereNull('error_flg')
                        ->take($limit);
        if($last_time_of_message) {
            $model = $model->where('created_at', '>', $last_time_of_message);
        }
        if(!empty($created_at)){
            $created_at = new \MongoDB\BSON\UTCDateTime($created_at * 1000);
            $model = $model->where('created_at' , '<', $created_at);
        }
        $model = $model->orderBy('created_at', 'DESC');
        return $model->get();
    }


    public function getAllUserByUserSaid($connect_page_id, $user_said){
        $model = new $this->model;
        $model = $model->raw()->aggregate(array(
            [
                '$match' => [
                    'connect_page_id'  => $connect_page_id,
                    'user_said' => array( '$regex' => $user_said )
                ],
            ],
            array(
                '$group' => array(
                    '_id' => array(
                        'user_id' => '$user_id'
                    )
                )
            )
        ));
        return $model;
    }
}
