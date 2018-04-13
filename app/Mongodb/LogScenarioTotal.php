<?php
/**
 * Created by PhpStorm.
 * User: le.bach.tung
 * Date: 21-Feb-17
 * Time: 11:07 AM
 */
namespace App\Mongodb;
class LogScenarioTotal extends \Moloquent {
	protected $collection = '_scenario_totals';

	public function setCollection($name){
		$this->collection = $name;
	}
}