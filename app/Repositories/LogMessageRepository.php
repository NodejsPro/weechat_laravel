<?php

namespace App\Repositories;

use App\Mongodb\LogMessage;
use App\Mongodb\NotificationHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogMessageRepository extends BaseRepository
{

    private $base_collection = '_logs';

    public function __construct(LogMessage $logMessage)
	{
		$this->model = $logMessage;
	}

    public function getMessage($room_id, $created_at = null, $limit = null, $last_time_of_message = null) {
        $model = new $this->model;
        $model->setCollection($room_id . $this->base_collection);
        if(empty($limit)){
            $limit = config('constants.log_message_limit');
        }
        $model = $model->where('room_id', $room_id)
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

    public function getLog($connect_page_id, $start_date, $end_date, $params){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
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
        $model->setCollection($connect_page_id . $this->base_collection);
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
        $model->setCollection($connect_page_id . $this->base_collection);
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
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->where('connect_page_id', $connect_page_id)
            ->whereNull('error_flg')
            ->whereNull('notification_id')
            ->orderBy('user_id', 'ASC')
            ->orderBy('created_at', 'ASC');
        return $model->get();
    }

    public function getAllSection2($connect_page_id, $user_id){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->where('connect_page_id', $connect_page_id)
            ->where('user_id', $user_id)
            ->whereNull('error_flg')
            ->whereNull('notification_id')
            ->orderBy('user_id', 'ASC')
            ->orderBy('created_at', 'ASC');
        return $model->get();
    }

    public function updateGmo($connect_page_id, $page_id)
    {
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model->where('page_id', $page_id)->update(['connect_page_id' => $connect_page_id]);
    }

    public function getAllUserByUserSaid($connect_page_id, $user_said){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
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
