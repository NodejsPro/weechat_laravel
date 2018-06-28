<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mongodb\LogMessage;
use App\Repositories\BotRoleRepository;
use App\Repositories\ConnectPageRepository;
use App\Repositories\ConnectRepository;
use App\Repositories\LogMessageRepository;
use App\Repositories\RoomRepository;
use App\Repositories\UnreadMessageRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
class DemoController extends Controller
{
    protected $repConnect;
    protected $repConnectPage;
    protected $repUser;
    protected $repScenario;
    protected $repRoom;
    protected $repLogMessage;
    protected $repScenarioGroup;

    protected $repTest;
    protected $repMenu;
    protected $repMaster;
    protected $repPlan;
    protected $common;
    protected $repTimezone;
    protected $repBotRole;
    protected $file_manager;

    protected $repUnreadMessage;

    protected $repLibrary;
    protected $repGoogleSheetUser;

    public function __construct(
        UserRepository $user,
        RoomRepository $room,
        UnreadMessageRepository $unread_message,
        LogMessageRepository $logMessage
    )
    {
        $this->repUser = $user;
        $this->repRoom = $room;
        $this->repLogMessage = $logMessage;
        $this->repUnreadMessage = $unread_message;
        Log::info('api DemoController');
    }

    public function index($view_user_id = null){
    }

    public function show(){
    }

    public function create(){
    }

    public function store(Request $request){
    }

    public function update(Request $request, $id){
    }

    public function updateStatus(){
    }

    public function edit($id){
    }

    public function destroy($id){
    }

    public function serviceAdd(Request $request){
    }

    public function getConversation(Request $request){
        $inputs = $request->all();
        Log::info('api getConversation');
        Log::info($inputs);
        $user_id = @$inputs['user_id'];
        $room_id = @$inputs['room_id'];
        $room_type = @$inputs['room_type'];
        $member = $request->get('member', []);
        $valid_arr = array(
            'user_id' => 'required',
            'member' => 'required|Array',
            'room_type' => 'required|in:' . implode(',' , config('constants.room_type')),
            'room_id' => 'required',
        );
        if(isset($room_id)){
            unset($valid_arr['member']);
        }else{
            unset($valid_arr['room_id']);
        }
        $validator = Validator::make(
            $inputs,
          $valid_arr
        );
        if ($validator->fails()){
            return response([
                "success" => false,
                'msg' => $validator->errors()->getMessages()
            ], 422);
        }
        $user = $this->repUser->getById($user_id);
        if(!$user){
            return response([
                "success" => false,
                'msg' => 'User valid'
            ], 422);
        }
        $flg = false;
        $member_fix = [];
        if(isset($room_id)){
            $room = $this->repRoom->getOneByField('_id', $room_id);
            if($room){
                $member_fix = $room->member;
                $flg = true;
            }
        }else{
            if(in_array($user_id, $member)){
                $room = $this->repRoom->getRoomByMember($member, $room_type);
                if($room){
                    $room_id = $room->id;
                    if(empty($member)){
                        $member = $room->member;
                        $member_fix = $room->member;
                    }
                    if($room){
                        $flg = true;
                    }
                }
            }
        }
        if($flg){
            $unread = $this->repUnreadMessage->getAllByField('room_id', $room_id);
            $unread_user = [];
            if($unread && count($unread)){
                foreach ($unread as $item){
                    if(isset($item->count) && $item->count > 0){
                        $unread_user[] = $item->user_id;
                    }
                }
            }
            $user_read = array_diff($member_fix, $unread_user);
            $log = $this->repLogMessage->getMessage($room_id, config('constants.log_message_limit'));
            $user_member = $this->repUser->getList($member_fix, 0, config('constants.per_page.5'));
            $member_name = $this->convertUserData($user_member);
            return Response::json([
                'success' => true,
                'log_messages' => $log,
                'member_name' => $member_name,
                'user_read' => $user_read,
                'room_id' => $room_id
            ], 200);
        }
        return response([
            "success" => false,
            'msg' => 'room valid'
        ], 422);
    }

}