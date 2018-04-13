<?php
/**
 * Created by PhpStorm.
 * User: le.bach.tung
 * Date: 21-Feb-17
 * Time: 11:07 AM
 */
namespace App\Mongodb;
class LogUserScenario extends \Moloquent {
	protected $collection = '_user_scenarios';

	public function setCollection($name){
		$this->collection = $name;
	}
}