<?php

namespace App\Repositories;

use App\Mongodb\POrder;
use Illuminate\Support\Facades\Log;

class POrderRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\POrder $order
	 * @return void
	 */
	public function __construct(POrder $order)
	{
		$this->model = $order;
	}

	/**
	 * Create a company.
	 *
	 * @param  array  $inputs
	 * @return App\POrder $order
	 */
	public function store($inputs, $user_id)
	{
		$order = new $this->model;
        $order->user_id = $user_id;
        $order->order_id = $inputs['order_id'];
        $order->amount = $inputs['amount'];
		$this->save($order, $inputs);
		return $order;
	}

	/**
	 * Save the POrder.
	 *
	 * @param  App\POrder $order
	 * @param  Array  $inputs
	 * @return void
	 */
	private function save($order, $inputs)
	{
	    if(isset($inputs['plan_code'])) {
            $order->plan_code = $inputs['plan_code'];
        }
        if(isset($inputs['amount'])) {
            $order->amount = $inputs['amount'];
        }
        if(isset($inputs['status'])) {
            $order->status = $inputs['status'];
        }
        if(isset($inputs['order_status'])) {
            $order->order_status = $inputs['order_status'];
        }
        if(isset($inputs['start_day'])) {
            $order->start_day = $inputs['start_day'];
        }
        if(isset($inputs['expire_day'])) {
            $order->expire_day = $inputs['expire_day'];
        }
        if(isset($inputs['data_response'])) {
            $order->data_response = $inputs['data_response'];
        }
        if(isset($inputs['payment_failed'])) {
            $order->payment_failed = $inputs['payment_failed'];
        }
        if(isset($inputs['amount'])) {
            $order->amount = $inputs['amount'];
        }
        if(isset($inputs['invoice_id'])) {
            $order->invoice_id = $inputs['invoice_id'];
        }
        if(isset($inputs['payment_day'])) {
            $order->payment_day = $inputs['payment_day'];
        }
		$order->save();
	}

	/**
	 * Update a POrder.
	 *
	 * @param  array  $inputs
	 * @param  App\POrder $order
	 * @return void
	 */
	public function update($order, $inputs)
	{
		$this->save($order, $inputs);
	}

    public function updateOrderStatus($order, $status){
        $order->order_status = $status;
        $order->save();
        return $order;
    }

    public function updatePlanCode($order, $plan){
        $order->plan_code = $plan->code;
        $order->amount = $plan->monthly_fee;
        $order->save();
        return $order;
    }

    public function getAll2($user_id, $offset = 0, $limit = 10, $order_status = null, $order_status_except = null){
        $model = new $this->model;
        $model = $model->where('user_id', $user_id);
        if($order_status) {
            $model = $model->where('order_status', $order_status);
        }
        if($order_status_except) {
            $model = $model->where('order_status', '<>', $order_status_except);
        }
        $model = $model->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getCountByUserId($user_id, $order_status_except = null){
        $model = new $this->model;
        $model = $model->where('user_id', $user_id);
        if($order_status_except) {
            $model = $model->where('order_status', '<>', $order_status_except);
        }
        return $model->count();
    }

    public function getOneByPlan($user_id, $plan_code = null, $order_status = null) {
        $model = new $this->model;
        if($plan_code) {
            $model = $model->where('plan_code', $plan_code);
        }
        if($order_status) {
            $model = $model->where('order_status', $order_status);
        }
        $model = $model->where('user_id', $user_id)
            ->orderBy('created_at', 'DESC');
        return $model->first();
    }

    public function getLastByUser($user_id) {
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
                       ->orderBy('created_at', 'DESC');
        return $model->first();
    }

    public function getLastActiveOrder($user_id) {
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
            ->where('order_status', config('constants.order_status.complete'))
            ->orderBy('created_at', 'DESC');
        return $model->first();
    }

    public function getLastOrder($user_id) {
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
            ->where('order_status', "<>", config('constants.order_status.create_invoice'))
            ->orderBy('created_at', 'DESC');
        return $model->first();
    }

    public function getAll($user_id, $order_status = null){
        $model = new $this->model;
        $model = $model->where('user_id', $user_id);
        if($order_status) {
            $model = $model->where('order_status', $order_status);
        }
        $model = $model->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getLastByExpireDay($user_id, $expire_day){
        $model = new $this->model;
        $model = $model->where('user_id', $user_id)
                        ->where('expire_day', $expire_day)
                        ->orderBy('created_at', 'DESC');
        return $model->first();
    }

}
