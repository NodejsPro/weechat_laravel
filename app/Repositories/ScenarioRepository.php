<?php

namespace App\Repositories;

use App\Mongodb\Scenario;
use Illuminate\Support\Facades\Log;
class ScenarioRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\Scenario $scenario
	 * @return void
	 */
	public function __construct(Scenario $scenario)
	{
		$this->model = $scenario;
	}

	/**
	 * Create a Scenario.
	 *
	 * @param  array  $inputs
	 * @return App\Scenario
	 */
	public function store($connect_page_id, $inputs, $page_id)
	{
		$scenario = new $this->model;
		$scenario->connect_page_id = $connect_page_id;
		$scenario->page_id         = $page_id;
		$this->save($scenario, $inputs);
		return $scenario;
	}

	private function save($scenario, $inputs)
	{
        if (isset($inputs['scenario_group_id'])) {
            $scenario->group_id = $inputs['scenario_group_id'];
        }
        if (isset($inputs['position'])) {
            $scenario->position = $inputs['position'];
        }
	    if (isset($inputs['name'])) {
            $scenario->name     = $inputs['name'];
        }
		if (isset($inputs['start_flg'])) {
			$scenario->start_flg = $inputs['start_flg'];
		}
		if (isset($inputs['data_filter'])) {
			$scenario->filter   = $inputs['data_filter'];
		}
		if (isset($inputs['library'])) {
			$scenario->library  = $inputs['library'];
		}
		if (isset($inputs['parent'])) {
			$scenario->parent   = $inputs['parent'];
		}
		if (isset($inputs['attach_variable'])) {
			$scenario->attach_variable = $inputs['attach_variable'];
		}
        $scenario->save();
	}

	public function update($scenario, $inputs)
	{
		$this->save($scenario, $inputs);
	}

	public function getListByPage($connect_page_id)
	{
		$model = new $this->model;
		$model = $model->where('connect_page_id', $connect_page_id);
		return $model->get();
	}
	// set other scenarios start_flg = null
	public function inActiveOther($connect_page_id, $scenario_except_id = null)
	{
		$scenarios = new $this->model;
        $scenarios = $scenarios->where('connect_page_id', $connect_page_id);
        if($scenario_except_id) {
            $scenarios = $scenarios->where('_id', '<>' ,$scenario_except_id);
        }
        $scenarios = $scenarios->update(['start_flg' => config('constants.active.disable')]);
		return $scenarios;
	}

	public function getScenariosOrderByStart($connect_page_id)
	{
		$modal = new $this->model;
		$modal = $modal
			->select('_id', 'group_id', 'name', 'start_flg', 'position', 'parent')
			->where('connect_page_id', $connect_page_id)
//			->orderBy('start_flg', 'DESC')
			->orderBy('position', 'ASC');
//			->orderBy('created_at', 'ASC');
		return $modal->get();
	}

    public function getScenariosOrderByPosition($connect_page_id)
    {
        $modal = new $this->model;
        $modal = $modal
            ->select('_id', 'name', 'start_flg', 'position', 'parent')
            ->where('connect_page_id', $connect_page_id)
            ->orderBy('position', 'ASC')
            ->groupBy('group_id');
        return $modal->get();
    }

	public function getScenariosById($id)
	{
		$modal = new $this->model;
		$modal = $modal
			->select('_id', 'name', 'start_flg', 'parent')
			->where('_id', $id);
		return $modal->first();
	}

    public function getScenariosStarting($connect_page_id)
    {
        $modal = new $this->model;
        $modal = $modal
            ->where('connect_page_id', $connect_page_id)
            ->where('start_flg', config('constants.active.enable'));
        return $modal->get();
    }

    function resetParentField($scenario){
        $scenario->parent = [];
        $scenario->save();
    }

    public function destroyByScenarioGroup($id)
    {
        $model = new $this->model;
        $model = $model->where('group_id', $id);
        $model->delete();
    }
    public function getLastByGroup($connect_page_id, $group_id) {
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
                       ->where('group_id', $group_id)
//                       ->max('position');
                       ->orderBy('position', 'DESC');
        return $model->first();
    }

    public function getByPosition($connect_page_id, $group_id, $position) {
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
                        ->where('group_id', $group_id)
                        ->where('position', '>=', $position)
                        ->increment('position', 1);
        return $model;
    }

    public function getPosition($connect_page_id, $group_id) {
        $model = new $this->model;
        if ($group_id == "") {
            $model = $model->whereNull('group_id')->orWhere('group_id', $group_id);
        }else{
            $model = $model->where('group_id', $group_id);
        }
        $model = $model->where('connect_page_id', $connect_page_id)
                       ->pluck('position', '_id');
        return $model;
    }

    public function updatePosition($scenario, $inputs){
        if (isset($inputs['group_id'])) {
            $scenario->group_id = $inputs['group_id'];
        }
        if (isset($inputs['position'])) {
            $scenario->position = $inputs['position'];
        }
        $scenario->save();
        return $scenario;
    }

    /*handle old data*/
    public function getOldData() {
        $model = new $this->model;
        $model = $model->orderBy('start_flg', 'DESC')
                        ->orderBy('created_at', 'ASC');
        return $model->get();
    }
    //scenario of bot EFO
    public function getAllByBotEFO($connect_page_list){
        $model = new $this->model;
        $model = $model->whereIn('connect_page_id', $connect_page_list)
                       ->pluck('_id');
        return $model;
    }
}
