<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mongodb\LogMessage;
use App\Repositories\BotRoleRepository;
use App\Repositories\ConnectPageRepository;
use App\Repositories\ConnectRepository;
use App\Repositories\LogMessageRepository;
use App\Repositories\RoomRepository;
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

    protected $repLibrary;
    protected $repGoogleSheetUser;

    public function __construct(
        UserRepository $user,
        RoomRepository $room,
        LogMessageRepository $logMessage
    )
    {
        $this->repUser = $user;
        $this->repRoom = $room;
        $this->repLogMessage = $logMessage;
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
        $user_id = @$inputs['user_id'];
        $room_id = @$inputs['room_id'];
        $member = $request->get('member', []);
        $valid_arr = array(
            'user_id' => 'required',
            'member' => 'required|Array',
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
        if(!isset($room_id)){
            if(!in_array($user_id, $member)){
                return response([
                    "success" => false,
                    'msg' => 'Room valid'
                ], 422);
            }
            $room = $this->repRoom->getRoomByMember($member);
            if($room){
                $room_id = $room->id;
            }
        }
        $log = [];
        if($room_id){
            $log = $this->repLogMessage->getMessage($room_id, null, config('constants.log_message_limit'), $user->created_at);
        }
        return Response::json([
            'success' => true,
            'log_messages' => $log
        ], 200);
    }

}