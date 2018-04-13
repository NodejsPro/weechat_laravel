<?php

namespace App\Repositories;

use App\Mongodb\Connect;
use App\Mongodb\ConnectPage;
use App\Mongodb\EmbotPlan;
use Illuminate\Support\Facades\Log;
//use Jenssegers\Mongodb\Eloquent\Model as Moloquent;
class EmbotPlanRepository extends BaseRepository
{

	public function __construct(EmbotPlan $model)
	{
		$this->model = $model;
	}

	public function getAll(){
	    $model = new $this->model;
//	    $model = $model->orderBy('code', 'asc')
//            ->orderBy('addtion_user', 'asc');
	    $model = $model->get();
	    return $model;
    }

    public function getByCode($plan_code, $yearly_user = null){
        $model = new $this->model;
	    $model = $model->where('code', $plan_code);
	    if(isset($yearly_user)){
            $model = $model->where('yearly_user', $yearly_user);
        }
        $model = $model->first();
        return $model;
    }
}
