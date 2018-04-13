<?php

namespace App\Repositories;


use App\Mongodb\Master;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class MasterRepository extends BaseRepository
{
    /**
     * Create a new MasterRepository instance.
     *
     * @param  App\Master $master
     * @return void
     */
    public function __construct(Master $master)
    {
        $this->model = $master;
    }

    public function getGroupAvailable($group){
        $model = new $this->model;
        $model = $model->where('group', $group)
                       ->where('active_flg',config('constants.active.enable'))
//                       ->orderBy('code');
                       ->orderBy('display_order');
        return $model->get();
    }

    public function getVariable($group, $sns_type = null, $code = null, $active_flg = null){
        $model = new $this->model;
        $model = $model->where('group', $group);
        if(!empty($code)){
            $model = $model->where('code', $code);
        }
        if(!empty($sns_type)){
            $model = $model->where('sns_type', $sns_type);
        }
        if(empty($active_flg)){
            $active_flg = config('constants.active.enable');
        }
        $model = $model->where('active_flg', $active_flg);
        $model = $model->orderBy('display_order');
        return $model->get();
    }
    public function getDefaultVariable($group, $sns_type){
        $model = new $this->model;
        $model = $model->where('group', $group);
        $model = $model->where('sns_type', $sns_type);
        $model = $model->orderBy('display_order');
        $model = $model->pluck('code', '_id');;
        return $model;
    }

    public function getAll2($group, $sns_type = null, $code = null){
        $model = new $this->model;
        $model = $model->where('group', $group);
        if(!empty($code)){
            $model = $model->where('code', $code);
        }
        if(!empty($sns_type)){
            $model = $model->where('sns_type', $sns_type);
        }
        $model = $model->orderBy('display_order');
        return $model->get();
    }

    public function getGroupFillSelectBox($group){
        $lang  = Lang::locale();
        $column_lang    = 'name_'.$lang;
        $model = new $this->model;
        $model = $model->where('group', $group)
                       ->where('active_flg',config('constants.active.enable'))
                       ->orderBy('display_order')
                       ->pluck($column_lang, 'code');
        return $model;
    }

    public function getVariableGreeting($group, $sns_type){
        $model = new $this->model;
        $model = $model->where('group', $group)
                       ->where('greeting_mess_flg', 1)
                       ->where('sns_type', $sns_type);
        return $model->get();
    }

    public function validationVariable ($variable_name){
        $model = new $this->model;
        $model = $model->where('code', $variable_name)
                       ->where('group', config('constants.master_group.default_variable'));
        return $model->first();
    }

	public function getOperators($group){
		$model = new $this->model;
		$model = $model->select('atributes')
                       ->where('group', $group)
                       ->where('active_flg',config('constants.active.enable'));
		return $model->first();
	}

    public function getServiceActive($group, $code){
        $model = new $this->model;
        $model = $model->where('group', $group)
                        ->where('code', $code)
                        ->where('active_flg', config('constants.active.enable'));
        return $model->first();
    }

    public function getUserGroup($group,  $expect = array()){
        $lang  = Lang::locale();
        $column_lang    = 'name_'.$lang;
        $model = new $this->model;
        $model = $model->where('group', $group)
            ->where('active_flg', config('constants.active.enable'));
        if(!empty($expect)){
            $model = $model->whereNotIn('code', $expect);
        }
        $model =  $model->orderBy('display_order');
        $model = $model->pluck($column_lang, 'code');
        return $model;
    }

    public function getListByGroup($group, $column = null){
        if(!$column) {
            $lang  = Lang::locale();
            $column    = 'name_'.$lang;
        }

        $model = new $this->model;
        $model = $model->where('group', $group)
            ->where('active_flg',config('constants.active.enable'))
            ->orderBy('display_order')
            ->pluck($column, 'code');
        return $model;
    }
}
