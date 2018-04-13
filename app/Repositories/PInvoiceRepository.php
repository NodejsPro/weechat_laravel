<?php

namespace App\Repositories;

use App\Mongodb\PInvoice;
use Illuminate\Support\Facades\Log;

class PInvoiceRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\PInvoice $invoice
	 * @return void
	 */
	public function __construct(PInvoice $invoice)
	{
		$this->model = $invoice;
	}

	/**
	 * Create a company.
	 *
	 * @param  array  $inputs
	 * @return App\PInvoice $invoice
	 */
	public function store($inputs, $user_id)
	{
		$model = new $this->model;
        $model->user_id = $user_id;
        $model->no = $inputs['no'];
        $model->p_order_id = $inputs['p_order_id'];
        $model->save();
		return $model;
	}

    public function InsertData($inputs, $user_id)
    {
        $model = new $this->model;
        $model->user_id = $user_id;
        $model->no = $inputs['no'];
        $model->p_order_id = $inputs['p_order_id'];
        $model->updated_at = $inputs['updated_at'];
        $model->save();
        return $model;
    }

	public function updateTime($model, $updated_at){
        $model->updated_at = $updated_at;
        $model->save();
        return $model;
    }

}
