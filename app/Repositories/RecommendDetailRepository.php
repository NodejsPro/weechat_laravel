<?php

namespace App\Repositories;

use App\Mongodb\RecommendDetail;
use Illuminate\Support\Facades\Log;

class RecommendDetailRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\RecommendDetail $recommend_detail
	 * @return void
	 */
	public function __construct(RecommendDetail $recommend_detail)
	{
		$this->model = $recommend_detail;
	}

	/**
	 * Create a company.
	 *
	 * @param  array  $inputs
	 * @return App\RecommendDetail $recommend_detail
	 */
	public function store($inputs, $recommend_import_id)
	{
		$recommend_detail = new $this->model;
        $recommend_detail->recommend_import_id = $recommend_import_id;
		$this->save($recommend_detail, $inputs);
		return $recommend_detail;
	}

	/**
	 * Save the RecommendDetail.
	 *
	 * @param  App\RecommendDetail $recommend_detail
	 * @param  Array  $inputs
	 * @return void
	 */
	private function save($recommend_detail, $inputs)
	{
        if(isset($inputs['content'])) {
            $recommend_detail->content = $inputs['content'];
        }
        if(isset($inputs['import_status'])) {
            $recommend_detail->import_status = $inputs['import_status'];
        }
		$recommend_detail->save();
	}

	/**
	 * Update a RecommendDetail.
	 *
	 * @param  array  $inputs
	 * @param  App\RecommendDetail $recommend_detail
	 * @return void
	 */
	public function update($recommend_detail, $inputs)
	{
		$this->save($recommend_detail, $inputs);
	}
}
