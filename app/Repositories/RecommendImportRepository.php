<?php

namespace App\Repositories;

use App\Mongodb\RecommendImport;
use Illuminate\Support\Facades\Log;

class RecommendImportRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\RecommendImport $recommend_import
	 * @return void
	 */
	public function __construct(RecommendImport $recommend_import)
	{
		$this->model = $recommend_import;
	}

	/**
	 * Create a company.
	 *
	 * @param  array  $inputs
	 * @return App\RecommendImport $recommend_import
	 */
	public function store($inputs, $connect_page_id)
	{
		$recommend_import = new $this->model;
        $recommend_import->connect_page_id = $connect_page_id;
		$this->save($recommend_import, $inputs);
		return $recommend_import;
	}

	/**
	 * Save the RecommendImport.
	 *
	 * @param  App\RecommendImport $recommend_import
	 * @param  Array  $inputs
	 * @return void
	 */
	private function save($recommend_import, $inputs)
	{
	    if(isset($inputs['name'])) {
            $recommend_import->name = $inputs['name'];
        }

        if(isset($inputs['upload_file_name'])) {
            $file_name = [];
            if(isset($recommend_import->file_name_import) && $recommend_import->file_name_import) {
                $file_name = $recommend_import->file_name_import;
            }
            $file_name[] = $inputs['upload_file_name'];
            $recommend_import->file_name_import = $file_name;
        }

        if(isset($inputs['real_file_name'])) {
            $file_name = [];
            if(isset($recommend_import->file_name) && $recommend_import->file_name) {
                $file_name = $recommend_import->file_name;
            }
            $file_name[] = $inputs['real_file_name'];
            $recommend_import->file_name = $file_name;
        }
		$recommend_import->save();
	}

	/**
	 * Update a RecommendImport.
	 *
	 * @param  array  $inputs
	 * @param  App\RecommendImport $recommend_import
	 * @return void
	 */
	public function update($recommend_import, $inputs)
	{
		$this->save($recommend_import, $inputs);
	}

    public function getOneById($connect_page_id, $recommend_id){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
                ->where('_id', $recommend_id);
        return $model->first();
    }

    public function getAll2($connect_page_id, $offset = 0, $limit = 10){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id);
        $model = $model->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

}
