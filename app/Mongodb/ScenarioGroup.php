<?php

namespace App\Mongodb;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class ScenarioGroup extends \Moloquent {

    protected $connection = 'mongodb';

    use SoftDeletes;
    protected $dates = ['deleted_at'];
}
