<?php

namespace App\Repositories;


use App\Mongodb\Nlp;
use App\Mongodb\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NlpRepository extends BaseRepository
{
    /**
     * Create a new UserRepository instance.
     *
     * @param  App\Nlp $nlp
     * @return void
     */
    public function __construct(Nlp $nlp)
    {
        $this->model = $nlp;
    }

    /**
     * Create a company.
     *
     * @param  array  $inputs
     * @return App\Nlp
     */
    public function store($inputs, $connect_page_id)
    {
        $nlp = new $this->model;
        $nlp->connect_page_id = $connect_page_id;
        $nlp->culture = $inputs['culture'];
        $this->save($nlp, $inputs);

        return $nlp;
    }

    /**
     * Save the User.
     *
     * @param  App\User $nlp
     * @param  Array  $inputs
     * @return void
     */
    private function save($nlp, $inputs)
    {
        if(isset($inputs['name'])){
            $nlp->name = $inputs['name'];
        }
        if(isset($inputs['app_name'])){
            $nlp->app_name = $inputs['app_name'];
        }
        if(isset($inputs['description'])){
            $nlp->description = $inputs['description'];
        }
        if(isset($inputs['app_id'])){
            $nlp->app_id = $inputs['app_id'];
        }
        if(isset($inputs['train_status'])){
            $nlp->train_status = $inputs['train_status'];
        }
        if(isset($inputs['upload_file_name']) && $inputs['upload_file_name'] && isset($inputs['real_file_name']) && $inputs['real_file_name']) {
            $history = [];
            if(isset($nlp->import_history) && $nlp->import_history) {
                $history = $nlp->import_history;
            }
            $history[] = [
                'real_name' => $inputs['real_file_name'],
                'import_name' => $inputs['upload_file_name'],
                'date' => date('Y-m-d H:i'),
            ];
            $nlp->import_history = $history;
        }
        $nlp->save();
    }

    /**
     * Update a user.
     *
     * @return void
     */
    public function update($nlp, $inputs)
    {
        $this->save($nlp, $inputs);
    }

    public function updateStatus($nlp, $train_status)
    {
        $nlp->train_status = $train_status;
        $nlp->save();
    }

    public function updatePublish($nlp, $publish_content)
    {
//        $nlp->publish_flg = $inputs['publish_flg'];
        $nlp->publish_content = $publish_content;
        $nlp->save();
    }
}
