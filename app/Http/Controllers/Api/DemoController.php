<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mongodb\LogMessage;
use App\Repositories\BotRoleRepository;
use App\Repositories\ConnectPageRepository;
use App\Repositories\ConnectRepository;
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
        UserRepository $user
    )
    {
        $this->repUser = $user;
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
        return Response::json([
            'success' => true,
            'log_messages' => []
        ], 200);
    }

}