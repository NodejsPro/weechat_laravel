<?php

namespace App\Mongodb;
class LogUnreadMessage extends \Moloquent {
	protected $collection = '_unread_messages';

	public function setCollection($name){
		$this->collection = $name;
	}
}