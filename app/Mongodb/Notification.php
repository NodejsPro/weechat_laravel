<?php
/**
 * Created by PhpStorm.
 * User: le.bach.tung
 * Date: 21-Feb-17
 * Time: 11:07 AM
 */
namespace App\Mongodb;

use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Notification extends \Moloquent {
	protected $connection = 'mongodb';

	use SoftDeletes;
	protected $dates = ['deleted_at'];
}