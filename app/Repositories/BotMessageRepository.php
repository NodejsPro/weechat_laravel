<?php
/**
 * Created by PhpStorm.
 * User: le.bach.tung
 * Date: 21-Feb-17
 * Time: 10:55 AM
 */
namespace App\Repositories;

use App\Mongodb\BotMessage;

class BotMessageRepository extends BaseRepository
{
	/**
	 * Create a new BotMessageRepository instance.
	 *
	 * @param  App\BotMessage $scenario
	 * @return void
	 */
	public function __construct(BotMessage $scenario)
	{
		$this->model = $scenario;
	}

	/**
	 * Create a Scenario.
	 *
	 * @param  array  $inputs
	 * @return App\BotMessage
	 */
	public function store($inputs)
	{
		$scenario = new $this->model;
		$scenario->scenario_id  = $inputs['scenario_id'];
		$scenario->message_type = $inputs['message_type'];
		$this->save($scenario, $inputs);
		return $scenario;
	}

	/**
	 * Save the Scenario.
	 *
	 * @param  App\BotMessage $inputs
	 * @param  Array  $inputs
	 * @return void
	 */
	private function save($scenario, $inputs)
	{
		$scenario->position = $inputs['position'];
		$scenario->data     = $inputs['data'];
        if (isset($inputs['filter'])){
            $scenario->filter = $inputs['filter'];
        }
        if (isset($inputs['btn_next'])){
            $scenario->btn_next = $inputs['btn_next'];
        }
        if (isset($inputs['input_requiment_flg'])){
            $scenario->input_requiment_flg = (int)$inputs['input_requiment_flg'];
        }
		$scenario->save();
	}

    public function updateData($bot, $inputs) {
        if (isset($inputs['data'])){
            $bot->data = $inputs['data'];
        }
        if (isset($inputs['filter'])){
            $bot->filter = $inputs['filter'];
        }
        $bot->save();
    }
	/**
	 * Update a Scenario.
	 *
	 * @param  array  $inputs
	 * @param  App\Models\Scenario $scenario
	 * @return void
	 */
	public function update($scenario, $inputs)
	{
		$this->save($scenario, $inputs);
		return $scenario;
	}

	public function getMessages($scenario_id, $position)
	{
		$model = new $this->model;
		$model = $model->where('scenario_id', $scenario_id);
        if($position) {
            $model->where('position', $position);
        }
		return $model->first();
	}

    public function getMessagesByPosition($scenario_id, $position)
    {
        $model = new $this->model;
        $model = $model->where('scenario_id', $scenario_id)
                       ->where('position', $position)
                       ->where('message_type', config('constants.message_type.bot'));
        return $model->first();
    }

	public function destroyByScenario($id)
	{
		$model = new $this->model;
		$model = $model->where('scenario_id', $id);
		$model->delete();
	}

	public function destroyByPos($position)
	{
		$model = new $this->model;
		$model = $model->where('position', $position);
		$model->delete();
	}

	public function getListMessages($scenario_id, $message_type = null){
		$model = new $this->model;
        if(isset($message_type)){
            $model = $model->where('message_type',$message_type);
        }
		$model = $model
			->where('scenario_id',(string) $scenario_id)
			->orderBy('position', 'asc');
		return $model->get();
	}
	/*efo convert data pulldown*/
	public function getAllByEFO($efo_scenario_list){
        $model = new $this->model;
        $model = $model->whereIn('scenario_id', $efo_scenario_list)
                       ->where('message_type', config('constants.message_type.user'));
        return $model->get();
    }
    public function updateOldData($obj, $inputs) {
        $obj->data     = $inputs['data'];
        $obj->save();
    }
    /*efo convert data filter*/
	public function getAllMessageEFO($efo_scenario_list){
        $model = new $this->model;
        $model = $model->whereIn('scenario_id', $efo_scenario_list);
        return $model->get();
    }
    public function updateOldFilterData($obj, $data_filter) {
        $obj->filter     = $data_filter;
        $obj->save();
    }
}