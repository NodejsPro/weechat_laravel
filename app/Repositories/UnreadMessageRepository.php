<?php

namespace App\Repositories;

use App\Mongodb\Rule;
use App\Mongodb\UnreadMessage;

class UnreadMessageRepository extends BaseRepository
{
	/**
	 * Create a new UserRepository instance.
	 *
	 * @param  App\UnreadMessage $unread_message
	 * @return void
	 */
	public function __construct(UnreadMessage $model)
	{
		$this->model = $model;
	}
}
