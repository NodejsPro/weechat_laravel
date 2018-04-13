<?php

namespace App\Repositories;

use App\Mongodb\UserProfile;
use Illuminate\Support\Facades\Log;


class UserProfileRepository extends BaseRepository
{

	public function __construct(UserProfile $userProfile)
	{
		$this->model = $userProfile;
	}

    private function save($user_profile, $inputs)
    {
        if(isset($inputs['last_active_at'])) {
            $user_profile->last_active_at = $inputs['last_active_at'];
        }
        $user_profile->save();
    }

    public function update($user_profile, $inputs)
    {
        $this->save($user_profile, $inputs);
    }

    public function updateUserProfile($user_profile, $inputs)
    {
        if(isset($inputs['profile_pic'])){
            $user_profile->profile_pic = $inputs['profile_pic'];
        }
        if(isset($inputs['first_name'])){
            $user_profile->user_first_name = $inputs['first_name'];
        }
        if(isset($inputs['last_name'])){
            $user_profile->user_last_name = $inputs['last_name'];
        }
        if(isset($inputs['locale'])){
            $user_profile->user_locale = $inputs['locale'];
        }
        if(isset($inputs['timezone'])){
            $user_profile->user_timezone = $inputs['timezone'];
        }
        if(isset($inputs['displayName'])){
            $user_profile->user_display_name = $inputs['displayName'];
        }
        if(isset($inputs['gender'])){
            $user_profile->user_gender = $inputs['gender'];
        }
        if(isset($inputs['full_name'])){
            $user_profile->user_full_name = $inputs['full_name'];
        }
        if(isset($inputs['first_name']) && isset($inputs['last_name'])){
            $user_profile->user_full_name = $inputs['first_name'].' '.$inputs['last_name'];
        }
        $user_profile->save();
    }

    public function getAllUser($connect_page_id, $condition){
        $model = new $this->model;
        $start = date('Y-m-d 00:00:00', strtotime($condition['start_date']));
        $end = date('Y-m-d 23:59:59', strtotime($condition['end_date']));
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $model = $model->where('connect_page_id', $connect_page_id)
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->orderBy('created_at', 'ASC');
        return $model->get();
    }

    public function getAllUserSession($connect_page_id, $start_time, $end_time){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
            ->where('last_active_at', '>=', $start_time * 1000)
            ->where('last_active_at', '<', $end_time * 1000)
            ->whereNull('preview_flg');
        return $model->get();
    }

    public function getNewUserByDate1($connect_page_id, $date = null, $sns_type = null){

        $condition["connect_page_id"] = $connect_page_id;
        $condition["preview_flg"] = ['$ne' => 1];

        if(isset($date)){
            $date = new \DateTime($date);
            $timestamp1 = $date->getTimestamp();
            $date->modify('+1 day');
            $timestamp2 = $date->getTimestamp();
            
            $condition["created_at"] = ['$gte' => new \MongoDB\BSON\UTCDateTime($timestamp1 * 1000), '$lt' => new \MongoDB\BSON\UTCDateTime($timestamp2 * 1000)];
        }

        if(isset($sns_type) && $sns_type == config('constants.group_type_service.web_embed')){
            $condition["start_flg"] = 1;
        }

        $model = new $this->model;
        $model = $model->raw()->aggregate(array(
            [
                '$match' => $condition,
            ],
            array(
                '$group' => array(
                    '_id' => array(
                        "yearMonthDay" => array(
                            '$dateToString' => array(
                                "format" => "%Y-%m-%d",
                                "date" => '$created_at',
                                "timezone" => config('app.timezone')
                            )
                        ),
                        'user_id' => '$user_id'
                    ),
                    'count' => array( '$sum' => 1 )
                )
            ),
            array(
                '$group' => array(
                    '_id' => '$_id.yearMonthDay',
                    'count' => array( '$sum' => 1 )
                )
            ),

        ));
        return $model;
    }

    public function getNewUserByGender($connect_page_id, $condition){
        $model = new $this->model;
        $start = date('Y-m-d 00:00:01', strtotime($condition['start_date']));
        $end = date('Y-m-d 23:59:59', strtotime($condition['end_date']));
        $start = new \DateTime($start);
        $start->setTimeZone( new \DateTimeZone('UTC'));
        $end = new \DateTime($end);
        $end->setTimeZone( new \DateTimeZone('UTC'));
        $model = $model->where('connect_page_id', $connect_page_id)
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->where('user_gender', $condition["gender"]);
        return $model->count();
    }

    public function getNewUserByGender1($connect_page_id, $condition)
    {
        $model = new $this->model;
        $condition1["connect_page_id"] = $connect_page_id;
        $condition1["user_gender"] = $condition["gender"];

        $date = new \DateTime($condition['start_date']);
        $timestamp1 = $date->getTimestamp();

        $date = new \DateTime($condition['end_date']);
        $date->modify('+1 day');
        $timestamp2 =  $date->getTimestamp();

        $condition1["created_at"] = ['$gte' => new \MongoDB\BSON\UTCDateTime($timestamp1 * 1000), '$lt' => new \MongoDB\BSON\UTCDateTime($timestamp2 * 1000)];
        $model = $model->raw()->aggregate(array(
            [
                '$match' => $condition1,
            ],
            array(
                '$group' => array(
                    '_id' => null,
                    'count' => array('$sum' => 1)
                )
            ),

        ));
        return $model;
    }

    public function getAllByCondition($connect_page_id, $user_arr, $sns_type = null, $condition = null){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
                    ->whereIn('user_id', $user_arr);
        return $model->get();
    }

    public function getAll($connect_page_id){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
                       ->orderBy('created_at', 'ASC');
        return $model->get();
    }

    public function getFacebookUserForUpdate($connect_page_id, $time_update){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id);
        $model = $model->where('updated_at', '<', $time_update);
        return $model->get();
    }

    public function getLineUserForUpdate($connect_page_id){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id);
        $model = $model->whereNull('unfollow_at');
        return $model->get();
    }

    public function getOne($connect_page_id, $user_id){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
//            ->whereNull('preview_flg')
            ->where('user_id', $user_id);
        return $model->first();
    }

    public function getNotIn($connect_page_id, $user_except){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
            ->whereNotIn('user_id', $user_except);
        return $model->get();
    }

    public function getUserLimit($connect_page_id, $condition, $offset = 0, $limit = 10, $order_by = 'DESC'){
        $model = new $this->model;
        $group_type = config('constants.group_type_service');
        $model = $model->where('connect_page_id', $connect_page_id)
            ->skip($offset)
            ->take($limit)
            ->orderBy('updated_at', $order_by);
        $sns_type = $condition["sns_type"];
        if($sns_type == $group_type['web_embed'] || $sns_type == $group_type['web_embed_efo']){
            $model = $model->where('start_flg', config('constants.active.enable'));
            $model = $model->whereNull('preview_flg');
        }

        if(@$condition["bookmark_flg"] == 1){
            $model = $model->where('bookmark_flg', config('constants.active.enable'));
        }

        if(isset($condition["user_arr"]) == 1){
            $model = $model->where('bookmark_flg', config('constants.active.enable'));
        }


        if(!empty($condition["username"]) == 1){
            $group_type = config('constants.group_type_service');
            if($sns_type == $group_type['facebook']){
                $model = $model->where('user_full_name', 'like', '%'.$condition["username"].'%');
            } elseif($sns_type == $group_type['line']){
                $model = $model->where('user_display_name', 'like', '%'.$condition["username"].'%');
            }
        }

        return $model->get();
    }

    public function getCountAll($connect_page_id, $condition = []){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id);
        $group_type = config('constants.group_type_service');

        $sns_type = $condition["sns_type"];
        if(isset($sns_type) && ($sns_type == $group_type['web_embed'] || $sns_type == $group_type['web_embed_efo'])){
            $model = $model->where('start_flg', config('constants.active.enable'));
            $model = $model->whereNull('preview_flg');
        }
        if(@$condition["bookmark_flg"] == 1){
            $model = $model->where('bookmark_flg', config('constants.active.enable'));
        }

        if(!empty($condition["username"]) == 1){
            $group_type = config('constants.group_type_service');
            if($sns_type == $group_type['facebook']){
                $model = $model->where('user_full_name', 'like', '%'.$condition["username"].'%');
            } elseif($sns_type == $group_type['line']){
                $model = $model->where('user_display_name', 'like', '%'.$condition["username"].'%');
            }
        }

        return $model->count();
    }


    public function getUserActive($connect_page_id, $sns_type, $date_from) {
        $model = new $this->model;
        $group_type = config('constants.group_type_service');
        $model = $model->where('connect_page_id', $connect_page_id)
            ->where('last_active_at', '>=', $date_from)
            ->orderBy('last_active_at', 'DESC');
        if($sns_type == $group_type['web_embed'] || $sns_type == $group_type['web_embed_efo']){
            $model = $model->where('start_flg', config('constants.active.enable'));
        }
        return $model->get();
    }

    public function filterUser($condition, $connect_page_id, $sns_type, $variable_custom_condition = false){
        $group_type = config('constants.group_type_service');
        $model = new $this->model;
        $model = $model->select('user_id')
                       ->where('connect_page_id', $connect_page_id);
        if($sns_type == $group_type['line']){
            $model = $model->whereNull('unfollow_at');
        }
        if (count($condition) > 0){
            foreach ($condition as $con){
                if ($con['condition'] == 'user_timezone'){
                    $con['value'] = (int)($con['value']);
                }
                $model = $model->where($con['condition'], $con['compare'], $con['value']);
            }
        }
        if ($variable_custom_condition) {
            return $model->get();
        }else{
            return $model->count();
        }
    }
    public function countUserPushNotification($connect_page_id, $sns_type) {
        $group_type = config('constants.group_type_service');
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id);
        if($sns_type == $group_type['line']){
            $model = $model->whereNull('unfollow_at');
        }
        return $model->count();
    }

    public function clearCookieUser($connect_page_id) {
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
                       ->update(['new_flg' => 1]);
        return $model;
    }

    public function updateAllRead($connect_page_id){
        $model = new $this->model;
        $model->timestamps = false;
        $model->where('connect_page_id', $connect_page_id)
            ->whereNull('preview_flg')
            ->update([
                'unread_cnt' => 0
            ]);
    }

    public function updateOneRead($connect_page_id, $user_id){
        $model = new $this->model;
        $model->timestamps = false;
        $model->where('connect_page_id', $connect_page_id)
            ->where('user_id', $user_id)
            ->whereNull('preview_flg')
            ->update([
                'unread_cnt' => 0
            ]);
    }

    public function updateBookmark($connect_page_id, $user_id, $flg){
        $model = new $this->model;
        $model->timestamps = false;
        $model->where('connect_page_id', $connect_page_id)
            ->where('user_id', $user_id)
            ->whereNull('preview_flg')
            ->update([
                'bookmark_flg' => $flg
            ]);
    }

}
