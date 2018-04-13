<?php

namespace App\Repositories;

use App\Mongodb\Api;
use Illuminate\Support\Facades\Log;

class ApiRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\Api $api
	 * @return void
	 */
	public function __construct(Api $api)
	{
		$this->model = $api;
	}

	/**
	 * Create a company.
	 *
	 * @param  array  $inputs
	 * @return App\Api $api
	 */
	public function store($inputs, $connect_page_id)
	{
		$api = new $this->model;
		$api->connect_page_id   = $connect_page_id;
		$this->save($api, $inputs);
		return $api;
	}

	/**
	 * Save the Api.
	 *
	 * @param  App\Api $api
	 * @param  Array  $inputs
	 * @return void
	 */
	private function save($api, $inputs)
	{
		$api->name      = $inputs['name'];
		$api->method    = $inputs['method'];
		$api->url       = $inputs['url'];
		$api->api_type  = $inputs['api_type'];
        $api->request   = @$inputs['request'];
        $api->response  = @$inputs['response'];
		$api->save();
	}

	/**
	 * Update a Api.
	 *
	 * @param  array  $inputs
	 * @param  App\Api $api
	 * @return void
	 */
	public function update($api, $inputs)
	{
		$this->save($api, $inputs);
	}

	public function getAll($connect_page_id)
	{
		$model = new $this->model;
		$model = $model->orderBy('id', 'DESC')
				->where('connect_page_id', '=', $connect_page_id);
		return $model->get();
	}

}
