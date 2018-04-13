<?php

namespace App\Repositories;

use App\Mongodb\ScenarioGroup;
use Illuminate\Support\Facades\Log;

class ScenarioGroupRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\Scenario $scenario
	 * @return void
	 */
	public function __construct(ScenarioGroup $scenarioGroup)
	{
		$this->model = $scenarioGroup;
	}

	/**
	 * Create a Scenario.
	 *
	 * @param  array  $inputs
	 * @return App\Scenario
	 */
	public function store($connect_page_id, $inputs, $page_id)
	{
		$scenario_group = new $this->model;
        $scenario_group->connect_page_id = $connect_page_id;
        $scenario_group->page_id         = $page_id;
		$this->save($scenario_group, $inputs);
		return $scenario_group;
	}

	private function save($scenario_group, $inputs)
	{
	    if (isset($inputs['name'])) {
            $scenario_group->name = $inputs['name'];
        }
        if (isset($inputs['position'])) {
            $scenario_group->position = $inputs['position'];
        }
        $scenario_group->save();
	}

	public function update($scenario_group, $inputs)
	{
		$this->save($scenario_group, $inputs);
	}
    public function getLastGroup($connect_page_id) {
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
                       ->orderBy('created_at', 'DESC');
        return $model->first();
    }
    public function getGroupOrderByPosition($connect_page_id)
    {
        $modal = new $this->model;
        $modal = $modal
            ->where('connect_page_id', $connect_page_id)
            ->orderBy('position', 'ASC');
        return $modal->get();
    }
    public function updatePosition($data, $connect_page_id){
        if (count($data)) {
            foreach ($data as $index => $group_id){
                $scenario_group = $this->getById($group_id);
                if ($scenario_group && $connect_page_id == $scenario_group->connect_page_id) {
                    $scenario_group->position = $index + 1;
                    $scenario_group->save();
                }
            }
        }
    }
}
