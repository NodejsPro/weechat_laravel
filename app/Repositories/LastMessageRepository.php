<?php

namespace App\Repositories;

use App\Mongodb\LastMessage;
use App\Mongodb\Rule;
use App\Mongodb\UnreadMessage;

class LastMessageRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\LastMessage $last_message
	 * @return void
	 */
	public function __construct(LastMessage $model)
	{
		$this->model = $model;
	}
}
