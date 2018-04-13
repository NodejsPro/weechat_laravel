<?php

namespace App\Repositories;

use App\Mongodb\PGateway;
use Illuminate\Support\Facades\Log;

class PGatewayRepository extends BaseRepository
{
	public function __construct(PGateway $gateway)
	{
		$this->model = $gateway;
	}

	public function store($inputs, $user_id)
	{
		$card = new $this->model;
        $card->user_id = $user_id;
        $this->save($card, $inputs);
        return $card;
	}

    public function save($card, $inputs)
    {
        if(isset($inputs['provider'])) {
            $card->provider = $inputs['provider'];
        }
        if(isset($inputs['gateway_name'])) {
            $card->gateway_name = $inputs['gateway_name'];
        }
        if(isset($inputs['pgcard_shop_id'])) {
            $card->pgcard_shop_id = $inputs['pgcard_shop_id'];
        }
        if(isset($inputs['pgcard_shop_pass'])) {
            $card->pgcard_shop_pass = $inputs['pgcard_shop_pass'];
        }
        if(isset($inputs['pgcard_site_id'])) {
            $card->pgcard_site_id = $inputs['pgcard_site_id'];
        }
        if(isset($inputs['pgcard_site_pass'])) {
            $card->pgcard_site_pass = $inputs['pgcard_site_pass'];
        }
        if(isset($inputs['api_url'])) {
            $card->api_url = $inputs['api_url'];
        }
        if(isset($inputs['default_flg'])) {
            $card->default_flg = $inputs['default_flg'];
        }
        $card->save();
    }

    public function update($gateway, $inputs)
    {
        $this->save($gateway, $inputs);
    }

    public function getAllByUser($user_id, $offset = 0, $limit = 10){
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
            ->skip($offset)
            ->take($limit)
//            ->orderBy('created_at', 'DESC');
            ->orderBy('default_flg', 'DESC');
        return $model->get();
    }

    public function getCountByUser($user_id){
        $model = new $this->model;
        $model = $model->where('user_id', $user_id);
        return $model->count();
    }

    public function getDefaultGateway($user_id) {
        $model = new $this->model;
        $model = $model->where('user_id', $user_id);
        $model = $model->where('default_flg', config('constants.active.enable'));
        return $model->first();
    }

    public function getFirst($user_id){
        $model = new $this->model;
        $model = $model->where('user_id', $user_id);
        $model = $model->where('default_flg', config('constants.active.disable'));
        $model = $model->orderBy('created_at', 'ASC');
        return $model->first();
    }
}
