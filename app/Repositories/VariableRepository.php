<?php

namespace App\Repositories;

use App\Mongodb\Variable;

class VariableRepository extends BaseRepository
{
	public function __construct(Variable $variable)
	{
		$this->model = $variable;
	}

    public function store($connect_page_id, $inputs)
    {
        $variable = new $this->model;
        $variable->connect_page_id      = $connect_page_id;
        $this->save($variable, $inputs);
        return $variable;
    }

  	private function save($variable, $inputs)
	{
        $variable->variable_name   = $inputs['variable_name'];
        $variable->save();
	}

	public function update($variable, $inputs)
	{
	    $this->save($variable, $inputs);
	    return $variable;
	}

	public function getAllByBot($connect_page_id){
	    $model = $this->model;
	    $model = $model->where('connect_page_id', $connect_page_id);
        $model = $model->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getAll($connect_page_id){
        $model = $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
                       ->pluck('variable_name', '_id');
        return $model;
    }
}
