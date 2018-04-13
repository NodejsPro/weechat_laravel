<?php

namespace App\Repositories;

use App\Mongodb\MessageVariable;
use Illuminate\Support\Facades\Log;

class MessageVariableRepository extends BaseRepository
{
	public function __construct(MessageVariable $msg_variable)
	{
		$this->model = $msg_variable;
	}

    public function getAllByPage($connect_page_id){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
                       ->orderBy('created_at', 'ASC');
        return $model->get();
    }
    public function filterUser($condition, $connect_page_id, $user_id_list = []){
        $model = new $this->model;
        $model = $model->select('_id')
                       ->where('connect_page_id', $connect_page_id);
        if (count($condition) && count($user_id_list)){
            $model = $model->whereIn('user_id', $user_id_list);
            $model = $model->where('variable_id', $condition['condition']);
            $model = $model->where('variable_value', $condition['compare'], $condition['value']);
        }
        return $model->pluck('user_id', '_id');
    }

    function checkExist($variable_id){
        $model = new $this->model;
        $model = $model->where('variable_id', $variable_id);
        $model = $model->get();
        if (count($model)){
            return true;
        }
        return false;
    }
}
