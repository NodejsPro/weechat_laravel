<?php

namespace App\Repositories;

use App\Mongodb\Banner;
use Illuminate\Support\Facades\Log;

class BannerRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\Api $banner
	 * @return void
	 */
	public function __construct(Banner $banner)
	{
		$this->model = $banner;
	}

	/**
	 * Create a company.
	 *
	 * @param  array  $inputs
	 * @return App\Banner $banner
	 */
	public function store($banner_url)
	{
		$model = new $this->model;
		$this->save($model, $banner_url);
		return $model;
	}

	/**
	 * Save the Api.
	 *
	 * @param  App\Banner $banner
	 * @param  Array  $inputs
	 * @return void
	 */
	private function save($banner, $banner_url)
	{
        $banner->banner_path = $banner_url;
        $banner->save();
	}

	/**
	 * Update a Api.
	 *
	 * @param  array  $inputs
	 * @param  App\Banner $banner
	 * @return void
	 */
	public function update($banner, $inputs)
	{
		$this->save($banner, $inputs);
	}
}
