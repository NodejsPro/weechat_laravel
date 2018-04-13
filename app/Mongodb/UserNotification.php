<?php
namespace App\Mongodb;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;

class UserNotification extends \Moloquent {

    protected $connection = 'mongodb';

}