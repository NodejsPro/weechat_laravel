<?php

namespace App\Repositories;


use App\Mongodb\PulldownMaster;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class PulldownMasterRepository extends BaseRepository
{
    /**
     * Create a new MasterRepository instance.
     *
     * @param  App\Master $master
     * @return void
     */
    public function __construct(PulldownMaster $pulldownMaster)
    {
        $this->model = $pulldownMaster;
    }

    public function getAll(){
        $model = new $this->model;
        $model = $model->where('active_flg',config('constants.active.enable'))
                       ->orderBy('display_order');
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
}
