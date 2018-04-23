<?php

namespace App\Repositories;


use App\Mongodb\Contact;
use App\Mongodb\Room;
use App\Mongodb\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ContactRepository extends BaseRepository
{
    /**
     * Create a new UserRepository instance.
     *
     * @param  App\Contact $contact
     * @return void
     */
    public function __construct(Contact $contact)
    {
        $this->model = $contact;
    }

    /**
     * Create a company.
     *
     * @param  array  $inputs
     * @return App\User
     */
    public function store($inputs, $created_id)
    {
        $user = new $this->model;
        $user->password         = bcrypt($inputs['password']);
        $user->email            = $inputs['email'];
        $user->authority        = $inputs['authority'];
        $user->created_id       = $created_id;
        $this->save($user, $inputs);

        return $user;
    }

    /**
     * Save the User.
     *
     * @param  App\User $user
     * @param  Array  $inputs
     * @return void
     */
    private function save($user, $inputs)
    {
        if(isset($inputs['name'])){
            $user->name             = $inputs['name'];
        }
        if(isset($inputs['user_name'])){
            $user->user_name             = $inputs['user_name'];
        }
        if(isset($inputs['phone'])){
            $user->phone             = $inputs['phone'];
        }
        if(isset($inputs['avatar']) && !isset($inputs['avatar'])){
            $user->avatar             = $inputs['avatar'];
        }
        if(isset($inputs['confirmation_token'])){
            $user->confirmation_token = $inputs['confirmation_token'];
        }
        
        $user->save();
        return $user;
    }

    /**
     * Update a user.
     *
     * @return void
     */
    public function update($user, $inputs)
    {
        if(isset($inputs['password'])){
            $user->password     = bcrypt($inputs['password']);
        }
        if(isset($inputs['email'])){
            $user->email     = $inputs['email'];
        }
        if (isset($inputs['authority'])) {
            $user->authority  = $inputs['authority'];
        }
        if (isset($inputs['plan'])) {
            $user->plan  = $inputs['plan'];
        }
        $user_authority = config('constants.authority');
        if(isset($inputs['created_id']) && Auth::user()->authority == $user_authority['admin'] && $user->authority == $user_authority['client']){
            $user->created_id = $inputs['created_id'];
        }
        $this->save($user, $inputs);
    }

    public function getAll($login_user, $offset = 0, $limit = 10, $is_count = false, $keyword_search = '')
    {
        $model = new $this->model;
        if($login_user->authority == config('constants.authority.agency')){
            $model = $model->where('created_id', $login_user->id);
        }else if($login_user->authority == config('constants.authority.admin')){
            $model = $model->where('_id', "<>", $login_user->id);
        }
        if ($keyword_search != '') {
            $model = $model->where(function ($model) use ($keyword_search) {
                        $model->where("name", "LIKE","%$keyword_search%")
                            ->orWhere("email", "LIKE", "%$keyword_search%")
                            ->orWhere("company_name", "LIKE", "%$keyword_search%");
                    });
        }
        if($is_count){
            return $model->count();
        }
        $model = $model->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

}
