<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TestRequest;
use App\Mongodb\EmbotPlan;
use App\Http\Requests\UserRequest;
use App\Repositories\ConnectPageRepository;
use App\Repositories\ConnectRepository;
use App\Repositories\ContactRepository;
use App\Repositories\EmbotPlanRepository;
use App\Repositories\MasterRepository;
use App\Repositories\PlanRepository;
use App\Repositories\RoomRepository;
use App\Repositories\UserMongoRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cookie;
use Jenssegers\Mongodb\Auth\User;
use Illuminate\Support\Facades\Hash;

class RoomController extends Controller
{
    protected $repUser;
    protected $repRoom;

    public function __construct(
        UserRepository $user,
        RoomRepository $room
    ){
        $this->repUser = $user;
        $this->repRoom = $room;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    }

    public function getList(Request $request){
        $inputs = $request->all();
        $validator = Validator::make(
            $inputs,
            array(
                'phone' => 'required'
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $phone = $inputs['phone'];
        $user = $this->repUser->getOneByField('phone', $phone);
        $start = isset($inputs['start']) ? (int)$inputs['start'] : 0;
        $length = isset($inputs['length']) ? (int)$inputs['length'] : config('constants.per_page')[3];
        if($user){
            $user_room = $this->repRoom->getByUserID($user->id, [], config('constants.room_type.one_many'), $start, $length);
            return Response::json([
                'success' => true,
                'data' => $this->convertRoomData($user_room)
            ], 200);
        }
        return Response::json([
            'success' => true,
            'msg' => trans('message.user_not_exists')
        ], 400);
    }

    public function create(Request $request){
    	// dd(1);
        $inputs = $request->all();
        $validator = Validator::make(
            $inputs,
            array(
                'phone' => 'required',
                'member' => 'required|array',
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $phone = $inputs['phone'];
        $member = $inputs['member'];
        if(in_array($phone, $member)){
            $user = $this->repUser->getUserByPhone($phone);
            dd($user->contact);
            dd(array_intersect($member, $user->contact));
            if($user && !empty($user->contact) && !empty(array_intersect($member, $user->contact))){
                $member_fix = array_intersect($member, $user->contact);
                dd($member_fix);
                $this->repRoom->store();
            }
        }
        return Response::json(array(
                'success' => false
            ), 400);
    }

    public function show(){

    }
}
