<?php

namespace App\Mongodb;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Jenssegers\Mongodb\Eloquent\Model as Moloquent;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Notifications\AdminResetPassword;

class Contact extends Moloquent
{
    use Authenticatable, Authorizable, CanResetPassword;
    use Notifiable;
    use SoftDeletes;
    protected $connection = 'mongodb';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
        'confirmation_token', 'confirmation_sent_at'
    ];

       protected $fillable = [
        'name', 'email', 'password',
        'authority',
        'phone' ,
        'validate_token',
        'avatar',
        'user_name',
        'code','confirmation_token' ,'created_id'
    ];

    protected $dates = [
        'deleted_at',
        'confirmed_at',
        'confirmation_sent_at',
    ];

    public function getOneUser($id){
        return User::where('_id', $id)->first();
    }
}
