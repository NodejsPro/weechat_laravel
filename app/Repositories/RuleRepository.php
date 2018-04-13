<?php

namespace App\Repositories;

use App\Mongodb\Rule;

class RuleRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\Rule $rule
	 * @return void
	 */
	public function __construct(Rule $rule)
	{
		$this->model = $rule;
	}

	/**
	 * Create a company.
	 *
	 * @param  array  $inputs
	 * @return App\Rule $rule
	 */
	public function store($inputs)
	{
		$rule = new $this->model;
		$this->save($rule, $inputs);
		return $rule;
	}

	/**
	 * Save the Rule.
	 *
	 * @param  App\Rule $rule
	 * @param  Array  $inputs
	 * @return void
	 */
	private function save($rule, $inputs)
	{
		$rule->code  = $inputs['code'];
		$rule->display_order  = $inputs['display_order'];
		$rule->save();
	}

	/**
	 * Update a Rule.
	 *
	 * @param  array  $inputs
	 * @param  App\Rule $rule
	 * @return void
	 */
	public function update($rule, $inputs)
	{
		$this->save($rule, $inputs);
	}

    public function getRuleList($role_list){
        return $this->model
            ->whereIn('code', $role_list)
            ->where('active_flg', config('constants.active.enable'))
            ->orderBy('display_order', 'ASC')
            ->get();
    }
}
