<?php
namespace App\Mongodb;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class File extends \Moloquent {
    use SoftDeletes;
    protected $connection = 'mongodb';
    protected $dates = ['deleted_at'];

}