<?php
namespace App\Mongodb;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;
class Connect extends \Moloquent {

    protected $connection = 'mongodb';

    protected $fillable = [
        'user_id', 'email', 'sns_id', 'type', 'sns_name', 'access_token'
    ];

}