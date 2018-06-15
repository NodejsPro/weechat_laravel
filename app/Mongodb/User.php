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

class User extends Moloquent implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
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
        'contact', 'confirm_flg', 'login_flg',
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

    public function getCountConnectPage($id){
        $count = 0;
        $user = User::where('_id', $id)->first();
        if($user){
            $connects = Connect::where('user_id', $id)->get();
            if($connects){
                foreach ($connects as $connect){
                    $connect_pages = ConnectPage::where('connect_id', $connect->id)->get();
                    if($connect_pages){
                        $count += count($connect_pages);
                    }
                }
            }
            if($user->authority != config('constants.authority.client')){
                $client_list = User::where("created_id", $user->id)->get();
                if($client_list){
                    foreach ($client_list as $client){
                        $count += $client->max_bot_number;
                    }
                }
            }
        }
        return $count;
    }

    public function getCountUser($id){
        $user_count = 0;
        $user_list = User::where('created_id', $id)->get();
        if($user_list){
            $user_count = count($user_list);
        }
        return $user_count;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPassword($token));
    }
}
