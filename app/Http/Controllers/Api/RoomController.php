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
use App\Repositories\LastMessageRepository;
use App\Repositories\MasterRepository;
use App\Repositories\PlanRepository;
use App\Repositories\RoomRepository;
use App\Repositories\UnreadMessageRepository;
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
    protected $repLastMessage;
    protected $repUnreadMessage;

    public function __construct(
        UserRepository $user,
        LastMessageRepository $last_message,
        UnreadMessageRepository $unread_message,
        RoomRepository $room
    ){
        $this->repUser = $user;
        $this->repRoom = $room;
        $this->repLastMessage = $last_message;
        $this->repUnreadMessage = $unread_message;
        Log::info('api RoomController');
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
        Log::info('api getList');
        Log::info($inputs);

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
            $user_room = $this->repRoom->getByUserID($user->id, [], null, $start, $length);
            $data_room = $this->convertRoomData($user_room);
            foreach ($data_room as $index => $room){
                $unread = $this->repUnreadMessage->getAllByField('room_id', $room['id']);
                Log::info($room['id']);
                Log::info($unread);
                $data_unread_message = $data_last_message = [];
                if($unread){
                    foreach ($unread as $item){
                        $data_unread_message[] = [
                            'user_id' => $item->user_id,
                            'count' => $item->count,
                        ];
                    }
                }
                $last_message = $this->repLastMessage->getOneByField('room_id', $room['id']);
                if($last_message){
                    $data_last_message = [
                        'user_id' => $last_message->user_id,
                        'message' => $last_message->message,
                        'message_type' => $last_message->message_type,
                    ];
                }
                Log::info($last_message);
                $data_room[$index]['unread_message'] = $data_unread_message;
                $data_room[$index]['last_message'] = $data_last_message;
            }
            return Response::json([
                'success' => true,
                'data' => $data_room
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
        Log::info('api create');
        Log::info($inputs);
        $validator = Validator::make(
            $inputs,
            array(
                'user_id' => 'required',
                'member' => 'required|array',
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $user_id = $inputs['user_id'];
        $member = $inputs['member'];
        $msg = trans('message.phone_not_in_member');
        if(in_array($user_id, $member)){
            $user = $this->repUser->getUserById($user_id);
            if($user && !empty($user->contact) && !empty(array_intersect($member, $user->contact))){
                $member_fix = array_intersect($member, $user->contact);
                $inputs['member'] = array_merge($member_fix, [$user_id]);
                $member_name = $this->repUser->getList($inputs['member'], 0, config('constants.per_page.5'));
                $name = [];
                foreach ($member_name as $item_name){
                    $name[] = $item_name->user_name;
                }
                $inputs['name'] = implode(',', $name);
                $inputs['room_type'] = config('constants.room_type.one_many');
                $room = $this->repRoom->store($inputs, $user->id);
                return Response::json(array(
                    'success' => true,
                    'room' => $this->convertRoomData([$room])
                ), 200);
            }
            $msg = trans('message.some_phone_not_exists_or_not_contact');
        }
        return Response::json(array(
                'success' => false,
                'msg' => $msg
            ), 400);
    }

    public function show(){

    }

    public function update(Request $request){
        $inputs = $request->all();
        Log::info('api update');
        Log::info($inputs);
        $validator = Validator::make(
            $inputs,
            array(
                'user_id' => 'required',
                'room_id' => 'required',
                'member' => 'array',
//                'room_name' => 'array',
            )
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $user_id = $inputs['user_id'];
        $room_id = $inputs['room_id'];
        $member = @$inputs['member'];
        $msg = trans('message.phone_not_in_member');
        if(empty($member) || (!empty($member) && in_array($user_id, $member))){
            $room = $this->repRoom->getById($room_id);
            if($room && $this->validateMember($member, $room)){
                $user_room = $this->repUser->getById($room->user_id);
                if($user_room){
                    $member_fix = array_intersect($member, $user_room->contact);
                    $inputs['member'] = array_merge($member_fix, [$user_id]);
                    $user = $this->repUser->getUserById($user_id);
                    if($user && ($user_room->id == $user->id || $user_room->created_id == $user->id || $user->authority == config('constants.authority.super_admin'))){
                        $inputs['name'] = $inputs['room_name'];
                        $room = $this->repRoom->update($room, $inputs);
                        return Response::json(array(
                            'success' => true,
                            'room' => $this->convertRoomData([$room])
                        ), 200);
                    }
                }
            }
            $msg = trans('message.room_not_in_member');
        }
        return Response::json(array(
            'success' => false,
            'msg' => $msg
        ), 400);
    }

    public function validateMember($member, $room){
        if(empty($member)){
            return true;
        }
        $result = false;
        $room_type_constants = config('constants.room_type');
        if($room->room_type == $room_type_constants['one_many']){
            $result = true;
        }
        return $result;
    }
}
