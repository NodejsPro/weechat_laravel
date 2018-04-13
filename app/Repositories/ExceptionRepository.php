<?php

namespace App\Repositories;

use App\Mongodb\Exception;

class ExceptionRepository extends BaseRepository
{
	/**
	 * Create a new ExceptionRepository instance.
	 *
	 * @param  App\Exception $exception
	 * @return void
	 */
	public function __construct(Exception $exception)
	{
		$this->model = $exception;
	}

	/**
	 * Create a company.
	 *
	 * @param  array  $inputs
	 * @return App\Exception $exception
	 */
	public function store($inputs)
	{
        $exception = new $this->model;
		$this->save($exception, $inputs);
		return $exception;
	}

	/**
	 * Save the Exception.
	 *
	 * @param  App\Exception $exception
	 * @param  Array  $inputs
	 * @return void
	 */
	private function save($exception, $inputs)
	{
		$exception->err = $inputs['err'];
		$exception->save();
	}
}
