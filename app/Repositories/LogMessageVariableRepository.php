<?php

namespace App\Repositories;

use App\Mongodb\LogMessageVariable;
use App\Mongodb\NotificationHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogMessageVariableRepository extends BaseRepository
{

    private $base_collection = '_message_variables';

	public function __construct(LogMessageVariable $logMessageVariable)
	{
		$this->model = $logMessageVariable;
	}

    public function getKeyValue($name, $id, $condition){
        $model = new $this->model;
        if(isset($condition['connect_page_id'])){
            $model->setCollection($condition['connect_page_id'] . $this->base_collection);
        }
        return $model->where($condition)
            ->orderBy('created_at', 'DESC')
            ->pluck($name, $id);
    }
}
