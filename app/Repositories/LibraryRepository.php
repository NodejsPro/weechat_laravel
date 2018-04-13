<?php

namespace App\Repositories;

use App\Mongodb\Library;
use App\Mongodb\ConnectPage;
use Illuminate\Support\Facades\Log;
class LibraryRepository extends BaseRepository
{
    /**
     * Create a new ChannelRepository instance.
     *
     * @param  App\Connect $connect
     * @return void
     */
    public function __construct(Library $library)
    {
        $this->model = $library;
    }

    public function store($inputs, $connect_page_id)
    {
        $library = new $this->model;
        $library->connect_page_id = $connect_page_id;
        return $this->save($library, $inputs);
    }

    private function save($library, $inputs)
    {
        if (isset($inputs['group_name'])) {
            $library->name = $inputs['group_name'];
        }
        if (isset($inputs['all_dialog_flg'])) {
            $library->all_dialog_flg = (int) $inputs['all_dialog_flg'];
        }
        if (isset($inputs['messages'])) {
            $library->messages = $inputs['messages'];
        }
        if (isset($inputs['library_sheet_type'])) {
            $library->library_sheet_type = $inputs['library_sheet_type'];
        }
        if (isset($inputs['sheet_id'])) {
            $library->sheet_id = $inputs['sheet_id'];
        }
        if (isset($inputs['column_bot'])) {
            $library->column_bot = $inputs['column_bot'];
        }
        if (isset($inputs['column_user'])) {
            $library->column_user = $inputs['column_user'];
        }
        if (isset($inputs['read_sheet_flg'])) {
            $library->read_sheet_flg = $inputs['read_sheet_flg'];
        }
        if (isset($inputs['credentials'])) {
            $library->credentials = $inputs['credentials'];
        }
        if (isset($inputs['refresh_token'])) {
            $library->refresh_token = $inputs['refresh_token'];
        }
        if (isset($inputs['scenario_id'])) {
            $library->scenario_id = $inputs['scenario_id'];
        }
        $library->save();
        return $library;
    }

    public function update($connect, $inputs)
    {
        $this->save($connect, $inputs);
        return $connect;
    }

    public function updateStatusSheet($library, $inputs)
    {
        if(isset($inputs['read_sheet_flg'])){
            $library->read_sheet_flg = $inputs['read_sheet_flg'];
        }
        if(isset($inputs['credentials'])){
            $library->credentials = $inputs['credentials'];
        }
        if(isset($inputs['refresh_token'])){
            $library->refresh_token = $inputs['refresh_token'];
        }
        if(isset($inputs['messages'])){
            $library->messages = $inputs['messages'];
        }
        $library->save();
        return $library;
    }

    public function getLibrary($connect_page_id, $library_name){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
                       ->where('name', $library_name);
        return $model->first();
    }

    public function validationEditName($connect_page_id, $library_id, $library_name){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
                       ->where('_id', '<>', $library_id)
                       ->where('name', $library_name);
        return $model->first();
    }

    public function getPageByCondition($connect_page_id, $condition){
        $model = new $this->model;
        $model = $model->where('connect_page_id', $connect_page_id)
            ->where($condition);
        return $model->get();
    }

    public function getLibraryByCondition($condtion){
        $model = new $this->model;
        $model = $model->where($condtion);
        return $model->get();
    }
}
