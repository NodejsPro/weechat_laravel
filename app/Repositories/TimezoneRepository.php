<?php

namespace App\Repositories;

use App\Mongodb\Timezone;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class TimezoneRepository extends BaseRepository
{
    /**
     * Create a new MasterRepository instance.
     *
     * @param  App\Master $master
     * @return void
     */
    public function __construct(Timezone $timezone)
    {
        $this->model = $timezone;
    }

    public function getTimezone($code = null, $language = null, $active_flg = null){
        $model = new $this->model;
        if(!empty($code)){
            $model = $model->where('code', $code);
        }
        if(empty($language)){
            $model = $model->select('code', 'value');
        }else{
            $model = $model->select('code', 'value', $language);
        }
        if(!empty($active_flg)){
            $model = $model->where('active_flg', $active_flg);
        }else{
            $model = $model->where('active_flg', config('constants.active.enable'));
        }
        $model = $model->orderBy('display_order');
        return $model->get();
    }
}
