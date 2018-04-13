<?php

namespace App\Repositories;

use App\Mongodb\Connect;
use App\Mongodb\ConnectPage;
use Illuminate\Support\Facades\Log;
//use Jenssegers\Mongodb\Eloquent\Model as Moloquent;
class ConnectRepository extends BaseRepository
{
	/**
	 * Create a new ChannelRepository instance.
	 *
   	 * @param  App\Connect $connect
	 * @return void
	 */
	public function __construct(Connect $connect)
	{
		$this->model = $connect;
	}

    /**
     * Create a company.
     *
     * @param  array  $inputs
     * @return App\Connect
     */
    public function store($user_id, $inputs)
    {
        $connect = new $this->model;
        $connect->user_id       = $user_id;
        $connect->email         = @$inputs['email'];
        $connect->sns_id        = @$inputs['sns_id'];
        $connect->sns_name      = @$inputs['bot_name'];
	    $connect->type          = @$inputs['type'];
        $this->save($connect, $inputs);
        return $connect;
    }

	/**
	 * Save the Channel.
	 *
	 * @param  App\Connect $connect
	 * @param  Array  $inputs
	 * @return void
	 */
  	private function save($connect, $inputs)
	{
	    if(isset($inputs['access_token'])) {
            $connect->access_token  = $inputs['access_token'];
        }
        if(isset($inputs['avatar'])) {
            $connect->avatar        = $inputs['avatar'];
        }
        $connect->valid_flg = 1;
        $connect->save();
	}

	/**
	 * Update a Channel.
	 *
	 * @param  array  $inputs
	 * @param  App\Models\Connect $connect
	 * @return void
	 */
	public function update($connect, $inputs)
	{
		$this->save($connect, $inputs);
		return $connect;
	}

    public function updateStatus($connect, $valid_flg)
    {
        $connect->valid_flg = $valid_flg;
        $connect->save();
    }

    public function getConnect($user_id, $sns_id)
    {
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
                       ->where('sns_id', $sns_id);
        return $model->first();
    }

    public function getAllConnect($user_id)
    {
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
                       ->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getAllByUserIdArr($user_ids = []){
        if(count($user_ids)){
            $model = new $this->model;
            $model = $model->whereIn('user_id', $user_ids);
            $model = $model->pluck('_id');
            return $model;
        }
        return [];
    }

    public function updateStatusType($connect, $input_type){
        $connect->type = $input_type;
        return $connect->save();
    }

    //get for web_embed type
    public function getFirstByType($user_id, $sns_type)
    {
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
            ->where('type', $sns_type);
        return $model->first();
    }

    //get all for efo type
    public function getAllEfoType($user_id)
    {
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
            ->where('type', config('constants.group_type_service.web_embed_efo'))
            ->pluck('_id');
        return $model;
    }

}
