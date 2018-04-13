<?php

namespace App\Repositories;

use App\Mongodb\SnsRole;

class SnsRoleRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\SnsRole $sns_role
	 * @return void
	 */
	public function __construct(SnsRole $sns_role)
	{
		$this->model = $sns_role;
	}

	/**
	 * Create a company.
	 *
	 * @param  array  $inputs
	 * @return App\SnsRole $sns_role
	 */
	public function store($inputs)
	{
		$sns_role = new $this->model;
		$this->save($sns_role, $inputs);
		return $sns_role;
	}

	/**
	 * Save the SnsRole.
	 *
	 * @param  App\SnsRole $sns_role
	 * @param  Array  $inputs
	 * @return void
	 */
	private function save($sns_role, $inputs)
	{
		$sns_role->sns_type  = $inputs['sns_type'];
		$sns_role->rule_code = $inputs['rule_code'];
		$sns_role->save();
	}

	/**
	 * Update a SnsRole.
	 *
	 * @param  array  $inputs
	 * @param  App\SnsRole $sns_role
	 * @return void
	 */
	public function update($sns_role, $inputs)
	{
		$this->save($sns_role, $inputs);
	}

}
