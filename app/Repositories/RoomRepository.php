<?php

namespace App\Repositories;


use App\Mongodb\Room;
use App\Mongodb\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RoomRepository extends BaseRepository
{
    /**
     * Create a new UserRepository instance.
     *
     * @param  App\Room $room
     * @return void
     */
    public function __construct(Room $room)
    {
        $this->model = $room;
    }

    /**
     * Create a company.
     *
     * @param  array  $inputs
     * @return App\User
     */
    public function store($inputs, $created_id)
    {
        $model = new $this->model;
        $model->user_id = $created_id;
        $model->room_type = $inputs['room_type'];
        $this->save($model, $inputs);

        return $model;
    }

    /**
     * Save the User.
     *
     * @param  App\User $model
     * @param  Array  $inputs
     * @return $model
     */
    private function save($model, $inputs)
    {
        if(isset($inputs['name'])){
            $model->name = $inputs['name'];
        }
        if(isset($inputs['member'])){
            $model->member = $inputs['member'];
        }
        $model->save();
        return $model;
    }

    /**
     * Update a user.
     *
     * @return $model
     */
    public function update($room, $inputs)
    {
        $model = $this->save($room, $inputs);
        return $model;
    }

    public function getAll($room_arr, $offset = 0, $limit = 10)
    {
        $model = new $this->model;
        if(!empty($room_arr)){
            $model = $model->whereIn('_id', $room_arr);
        }
        $model = $model
            ->where('confirm_flg', config('constants.active.enable'))
            ->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getByUserID($user_id, $room_arr, $room_type, $offset = 0, $limit = 10)
    {
        $model = new $this->model;
        if(!empty($room_arr)){
            $model = $model->whereIn('_id', $room_arr);
        }
        if(isset($room_type)){
            $model = $model->where('room_type', $room_type);
        }
        $model = $model->where(function ($model) use ($user_id) {
            $model->where("user_id", $user_id)
                ->orWhereIn("member", [$user_id]);
        });
        $model = $model
            ->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function checkRoom($user_id, $room_id, $member = []){
        $model = new $this->model;
        $member_arr = [$user_id];
        if(!empty($member)){
            $member_arr = array_merge($member_arr, $member);
        }
        $model = $model->where('_id', $room_id);
        $model = $model->whereIn('member', $member_arr);
    }

    public function getRoomByMember($member, $room_type){
        $model = new $this->model;
        $model = $model->where('member', 'all', $member);
        $model = $model->where('room_type', $room_type);
        if($room_type == config('constants.room_type.one_one')){
            $model = $model->where('member', 'size', 2);
        }else{
            $model = $model->where('member', 'size', count($member));
        }
        return $model->first();
    }

}
