<?php

namespace App\Repositories;

use App\Mongodb\RoomMessageVariable;
use Illuminate\Support\Facades\Log;

class RoomMessageVariableRepository extends BaseRepository
{
	public function __construct(RoomMessageVariable $room_variable)
	{
		$this->model = $room_variable;
	}

    public function getAllByPage($connect_page_id){
        $model = new $this->model;
        $model = $model->raw()->aggregate(array(
            [
                '$match' => [
                    'connect_page_id'  => $connect_page_id,
                ],
            ],
            array(
                '$group' => array(
                    '_id' => array(
                        'variable_id' => '$variable_id',
                        'variable_value' => '$variable_value',
                    ),
                    'count' => ['$sum' => 1]
                )
            ),
            [ '$sort' => [ '_id.variable_id' => 1 ]]
        ));
        return $model;
    }
}
