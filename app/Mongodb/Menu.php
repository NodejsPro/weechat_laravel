<?php

namespace App\Mongodb;

class Menu extends \Moloquent {

    protected $connection = 'mongodb';

    protected $fillable = [
        'connect_page_id', 'title', 'type', 'url', 'scenario'
    ];

}
