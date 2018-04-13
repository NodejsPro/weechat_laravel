<?php
namespace App\Mongodb;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;
class LogEfoCv extends \Moloquent {

    protected $collection = '_efo_cvs';

    public function setCollection($name){
        $this->collection = $name;
    }
}