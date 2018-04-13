<?php
namespace App\Mongodb;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;
class LogMessageVariable extends \Moloquent {

    protected $collection = '_message_variables';

    public function setCollection($name){
        $this->collection = $name;
    }
}