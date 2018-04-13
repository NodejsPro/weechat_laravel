<?php

namespace App\Repositories;

use App\Mongodb\NlpImport;

class NlpImportRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\NlpImport $intent
	 * @return void
	 */
	public function __construct(NlpImport $intent)
	{
		$this->model = $intent;
	}

	/**
	 * Create a company.
	 *
	 * @param  array  $inputs
	 * @return App\NlpImport $intent
	 */
	public function store($inputs, $nlp_id)
	{
		$intent = new $this->model;
		$intent->nlp_id = $nlp_id;
		$intent->app_intent_id = @$inputs['app_intent_id'];
		$this->save($intent, $inputs);
		return $intent;
	}

	/**
	 * Save the NlpImport.
	 *
	 * @param  App\NlpImport $intent
	 * @param  Array  $inputs
	 * @return void
	 */
	private function save($intent, $inputs)
	{
		$intent->name = $inputs['name'];
		$intent->save();
	}

	/**
	 * Update a NlpImport.
	 *
	 * @param  array  $inputs
	 * @param  App\NlpImport $intent
	 * @return void
	 */
	public function update($intent, $inputs)
	{
		$this->save($intent, $inputs);
	}

	public function getAll($nlp_id)
	{
		$model = new $this->model;
		$model = $model->where('nlp_id', $nlp_id)
            ->orderBy('id', 'DESC');

		return $model->get();
	}

    public function getAllByNlp($nlp_id, $offset = 0, $limit = 10){
        $model = new $this->model;
        $model = $model->where('nlp_id', $nlp_id)
            ->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getCountByNlp($nlp_id){
        $model = new $this->model;
        $model = $model ->where('nlp_id', $nlp_id);
        return $model->count();
    }

}
