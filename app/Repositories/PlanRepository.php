<?php

namespace App\Repositories;

use App\Mongodb\Plan;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class PlanRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\Plan $plan
	 * @return void
	 */
	public function __construct(Plan $plan)
	{
		$this->model = $plan;
	}

	public function getAll()
	{
		$model = new $this->model;
		$model = $model->orderBy('display_order', 'ASC');
		return $model->get();
	}


	public function getByCode($code){
		$model = new $this->model;
		$model = $model->where('code', '=', $code);
		return $model->first();
	}

    public function getPlanGroup($user_lang = null){
        if ($user_lang){
            $lang = $user_lang;
        }else {
            $lang = Lang::locale();
        }
        $column_lang = 'name_'.$lang;
        $model = new $this->model;
        $model = $model->pluck($column_lang, 'code');
        return $model;
    }


}
