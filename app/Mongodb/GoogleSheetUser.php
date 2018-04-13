<?php
namespace App\Mongodb;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;
class GoogleSheetUser extends \Moloquent {

    protected $connection = 'mongodb';

}