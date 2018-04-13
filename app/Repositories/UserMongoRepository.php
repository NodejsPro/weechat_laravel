<?php
namespace App\Repositories;

use App\User;

class UserMongoRepository extends BaseMongoRepository
{
    protected $collection = "user";

    public function __construct(User $user)
    {
        parent::__construct();
        $this->model = $user;
    }

    /**
     * Create a User.
     *
     * @param  array  $inputs
     * @param  int    $confirmation_code
     * @return App\User
     */
    public function store($input){
        $user = [];
        $user['_id']        = $input['id'];
        $user['email']      = $input['email'];
        $user['authority']  = $input['authority'];
        $user['name']       = $input['name'];
        $user['url']        = $input['url'];
        $user['comment']    = $input['comment'];
        $this->getCollection()->insert($user);
    }

    public function updateMongo($input){
        $user = [];
        $user['email']      = $input['email'];
        $user['authority']  = $input['authority'];
        $user['name']       = $input['name'];
        $user['url']        = $input['url'];
        $user['comment']    = $input['comment'];
        $this->getCollection()->where('_id', $input['id'])->update($user);
    }
}
