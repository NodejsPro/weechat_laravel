<?php

namespace App\Repositories;

use App\Mongodb\PCard;
use Illuminate\Support\Facades\Log;

class PCardRepository extends BaseRepository
{
	public function __construct(PCard $card)
	{
		$this->model = $card;
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
        if(isset($inputs['card_seq'])) {
            $card->card_seq = $inputs['card_seq'];
        }
        if(isset($inputs['cvc'])) {
            $card->cvc = $inputs['cvc'];
        }
        if(isset($inputs['default_flg'])) {
            $card->default_flg = $inputs['default_flg'];
        }
        $card->save();
    }

    public function update($card, $inputs)
    {
        $this->save($card, $inputs);
    }

    public function getByCardSeq($user_id, $card_seq) {
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
            ->where('card_seq', $card_seq);
        return $model->first();
    }

    public function getDefaultCard($user_id){
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
                       ->where('default_flg',config('constants.active.enable'));
        return $model->first();
    }

    public function countByUser($user_id) {
        $model = new $this->model;
        $model = $model->where('user_id', $user_id);
        return $model->count();
    }

}
