<?php
namespace App\Mongodb;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;
class LogMessage extends \Moloquent {

    protected $collection = '_logs';

    public function setCollection($name){
        $this->collection = $name;
    }
}