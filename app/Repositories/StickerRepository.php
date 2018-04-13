<?php

namespace App\Repositories;

use App\Mongodb\Sticker;

class StickerRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\Sticker $sticker
	 * @return void
	 */
	public function __construct(Sticker $sticker)
	{
		$this->model = $sticker;
	}

	/**
	 * Create a Sticker.
	 *
	 * @param  array  $inputs
	 * @return App\Sticker
	 */
	public function store($inputs)
	{
		$sticker = new $this->model;
		$this->save($sticker, $inputs);
		return $sticker;
	}

	private function save($sticker, $inputs)
	{
        $sticker->package_id = $inputs['package_id'];
        $sticker->sticker_id = $inputs['sticker_id'];
        $sticker->save();
	}

	public function update($sticker, $inputs)
	{
		$this->save($sticker, $inputs);
	}

	public function getGroupByPackage()
	{
        $model = new $this->model;
        $model = $model
            ->orderBy('package_id', 'ASC')
            ->orderBy('sticker_id', 'ASC');
        return $model->get()->groupBy('package_id');
	}
}
