<?php

namespace App\Repositories;

use App\Mongodb\LogMessage;
use App\Mongodb\LogUserProfile;
use App\Mongodb\NotificationHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogUserProfileRepository extends BaseRepository
{

    private $base_collection = '_user_profiles';

	public function __construct(LogUserProfile $logUserProfile)
	{
		$this->model = $logUserProfile;
	}

    public function getAll($connect_page_id){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->where('connect_page_id', $connect_page_id)->whereNull('preview_flg')
            ->orderBy('created_at', 'ASC');
        return $model->get();
    }

    public function getOne($connect_page_id, $user_id){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->where('connect_page_id', $connect_page_id)
//            ->whereNull('preview_flg')
            ->where('user_id', $user_id);
        return $model->first();
    }

    public function getNotIn($connect_page_id, $user_except){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->where('connect_page_id', $connect_page_id)
            ->whereNotIn('user_id', $user_except);
        return $model->get();
    }

    public function getUserLimit($connect_page_id, $condition, $offset = 0, $limit = 10, $order_by = 'DESC'){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $group_type = config('constants.group_type_service');
        $model = $model->where('connect_page_id', $connect_page_id)
            ->skip($offset)
            ->take($limit)
            ->orderBy('updated_at', $order_by);
        $sns_type = $condition["sns_type"];
        if($sns_type == $group_type['web_embed'] || $sns_type == $group_type['web_embed_efo']){
            $model = $model->where('start_flg', config('constants.active.enable'))->whereNull('preview_flg');
        }

        if(@$condition["cv_flg"] == 1){
            $model = $model->where('cv_flg', 1);
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
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->where('connect_page_id', $connect_page_id);
        $group_type = config('constants.group_type_service');

        $sns_type = $condition["sns_type"];
        if(isset($sns_type) && ($sns_type == $group_type['web_embed'] || $sns_type == $group_type['web_embed_efo'])){
            $model = $model->where('start_flg', config('constants.active.enable'))->whereNull('preview_flg');
        }
        if(@$condition["bookmark_flg"] == 1){
            $model = $model->where('bookmark_flg', config('constants.active.enable'));
        }

        if(@$condition["cv_flg"] == 1){
            $model = $model->where('cv_flg', 1);
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

    public function filterUser($condition, $connect_page_id){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->where('connect_page_id', $connect_page_id);
        if (count($condition) > 0){
            foreach ($condition as $con){
                if ($con['condition'] == 'user_timezone'){
                    $con['value'] = (int)($con['value']);
                }
                $model = $model->where($con['condition'], $con['compare'], $con['value']);
            }
        }

        return $model->get();
    }

    public function clearCookieUser($connect_page_id) {
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->where('connect_page_id', $connect_page_id)
            ->update(['new_flg' => 1]);
        return $model;
    }

    public function updateAllRead($connect_page_id){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model->timestamps = false;
        $model->where('connect_page_id', $connect_page_id)
            ->whereNull('preview_flg')
            ->update([
                'unread_cnt' => 0
            ]);
    }

    public function updateOneRead($connect_page_id, $user_id){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
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
        $model->setCollection($connect_page_id . $this->base_collection);
        $model->timestamps = false;
        $model->where('connect_page_id', $connect_page_id)
            ->where('user_id', $user_id)
            ->whereNull('preview_flg')
            ->update([
                'bookmark_flg' => $flg
            ]);
    }

    public function getCountByConnectPage($connect_page_id, $sns_type = null){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->where('start_flg', config('constants.active.enable'));
        $model = $model->whereNull('preview_flg');
        return $model->count();
    }


    public function getCountUserForPlan($connect_page_id){
        $model = new $this->model;
        $model->setCollection($connect_page_id . $this->base_collection);
        $model = $model->where('connect_page_id', $connect_page_id)->whereNull('preview_flg')
            ->where('start_flg', config('constants.active.enable'));
        return $model->count();
    }
}
