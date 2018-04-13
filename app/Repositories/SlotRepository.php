<?php

namespace App\Repositories;

use App\Mongodb\Slot;

class SlotRepository extends BaseRepository
{
	/**
	 * Create a new Slot instance.
	 *
	 * @param  App\Slot $sticker
	 * @return void
	 */
	public function __construct(Slot $slot)
	{
		$this->model = $slot;
	}

	/**
	 * Create a Slot.
	 *
	 * @param  array  $inputs
	 * @return App\Slot
	 */
	public function store($inputs, $connect_page_id)
	{
        $slot = new $this->model;
        $slot->connect_page_id = $connect_page_id;
		$this->save($slot, $inputs);
		return $slot;
	}

	private function save($slot, $inputs)
	{
        $slot->name     = $inputs['name'];
        $slot->action   = $inputs['action'];
        $slot->action_data = $inputs['action_data'];
        $slot->item     = $inputs['item'];
        $slot->save();
	}

	public function update($slot, $inputs)
	{
		$this->save($slot, $inputs);
	}

    public function getAll($connect_page_id)
    {
        $model = new $this->model;
        $model = $model
            ->where('connect_page_id', $connect_page_id)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

}
