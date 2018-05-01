<?php

namespace App\Http\Controllers;

use App\Mongodb\LogMessage;
use App\Repositories\BotRoleRepository;
use App\Repositories\ConnectPageRepository;
use App\Repositories\ConnectRepository;
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
class BotController extends Controller
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
        ConnectRepository $connect,
        ConnectPageRepository $connectPage
    )
    {
        $this->repConnect = $connect;
        $this->repConnectPage = $connectPage;
    }

    public function index($view_user_id = null)
    {
        $user = Auth::user();

        $pages = array();
        $service_type = config('constants.group_type_service');
        $service_all  = $this->repMaster->getAll2(config('constants.master_group.service'));
        $service_list = $this->simpleData($service_all);
        $user_authority = config('constants.authority');
        $user_list = $this->repUser->getUserList();
        $user_list = $this->convertData($user_list);
        $valid_flg = true;
        if(isset($view_user_id)){
            if($user->authority == $user_authority['client']){
                abort(404);
            }else{
                $view_user = $this->repUser->getById($view_user_id);
                if($view_user && (($user->authority == $user_authority['admin']) || ($user->authority == $user_authority['agency'] && $view_user->created_id == $user->id))){
                    $pages = $this->common->getAllConnectPage($view_user_id);
                    $user = $view_user;
                }else{
                    abort(404);
                }
            }
        }else{
            $share_bot = $this->repBotRole->getListShareByUser($user->id);
            if ($share_bot) {
                foreach ($share_bot as $bot) {
                    $page = $this->repConnectPage->getById($bot->connect_page_id);
                    if($page){
                        $page->share_flg = 1;
                        $page->share_authority = $bot->authority;
                        $pages[] = $page->toArray();
                    }
                }
            }
            $page_by_user = $this->common->getAllConnectPage($user->id);
            $pages = array_merge($pages, $page_by_user);
            foreach($pages as $index => $page){
                if(isset($page["valid_flg"]) && $page["valid_flg"] == 0){
                    $valid_flg = false;
                    break;
                }
                if ($page == null) {
                    unset($pages[$index]);
                }
            }
        }

        //get array page list for transfer bot
        $page_group_list = [];
        $page_efo_list = [];
        //get bot type image
        foreach ($pages as $key => $page){
            $pages[$key]['bot_type_img'] = config('constants.group_type_service_key.'.$page['sns_type']);

            if (isset($page['sns_type'])) {
                $lang_fb = @$service_list[$service_type['facebook']]['lang'];
                $lang_line = @$service_list[$service_type['line']]['lang'];
                $lang_web = @$service_list[$service_type['web_embed']]['lang'];
                switch ($page['sns_type']) {
                    case $service_type['facebook']:
                        $page_group_list[$lang_fb][$page['_id']] = $page['page_name'];
                        break;
                    case $service_type['line']:
                        $page_group_list[$lang_line][$page['_id']] = $page['page_name'];
                        break;
                    case $service_type['web_embed']:
                        $page_group_list[$lang_web][$page['_id']] = $page['page_name'];
                        break;
                    case $service_type['web_embed_efo']:
                        $page_efo_list[$page['_id']] = $page['page_name'];
                        break;
                }
            }
        }
        $sns_type_list = isset($user->sns_type_list) ? $user->sns_type_list : [];
        if($user->authority == $user_authority['client'] && isset($user->created_id)){
            $create_user = $this->repUser->getById($user->created_id);
            if($create_user->authority == $user_authority['agency'] && !empty($create_user->sns_type_list)){
                $tmp_arr = array_intersect ($sns_type_list, $create_user->sns_type_list);
                if(empty($tmp_arr)){
                    $sns_type_list = $create_user->sns_type_list;
                }else{
                    $sns_type_list = $tmp_arr;
                }
            }
        }
        if(count($sns_type_list) > 0){
            foreach ($service_list as $service_code => $service){
                if(($service['active_flg'] && !in_array($service_code, $sns_type_list)) || !$service['active_flg']){
                    unset($service_list[$service_code]);
                }
            }
        }

        return view('bot.index')->with([
            'pages'     => $pages,
            'page_group_list' => $page_group_list,
            'page_efo_list' => $page_efo_list,
            'view_user' => $user,
            'view_user_id' => $view_user_id,
            'service_list' => $service_list,
            'service_type' => $service_type,
            'connect_valid_flg' => $valid_flg,
            'sns_type_list' => $sns_type_list,
            'user_list' => $user_list
        ]);
    }

    public function getListBotTransfer() {
        $user = Auth::user();
        $pages = array();
        $service_type = config('constants.group_type_service');
        $service_all  = $this->repMaster->getAll2(config('constants.master_group.service'));
        $service_list = $this->simpleData($service_all);
        $user_authority = config('constants.authority');
        $user_list = $this->repUser->getUserList();
        $user_list = $this->convertData($user_list);
        $valid_flg = true;
        if(isset($view_user_id)){
            if($user->authority == $user_authority['client']){
                abort(404);
            }else{
                $view_user = $this->repUser->getById($view_user_id);
                if($view_user && (($user->authority == $user_authority['admin']) || ($user->authority == $user_authority['agency'] && $view_user->created_id == $user->id))){
                    $pages = $this->common->getAllConnectPage($view_user_id);
                    $user = $view_user;
                }else{
                    abort(404);
                }
            }
        }else{
            $share_bot = $this->repBotRole->getListShareByUser($user->id);
            if ($share_bot) {
                foreach ($share_bot as $bot) {
                    $page = $this->repConnectPage->getById($bot->connect_page_id);
                    if($page){
                        $page->share_flg = 1;
                        $page->share_authority = $bot->authority;
                        $pages[] = $page->toArray();
                    }
                }
            }
            $page_by_user = $this->common->getAllConnectPage($user->id);
            $pages = array_merge($pages, $page_by_user);
            foreach($pages as $index => $page){
                if(isset($page["valid_flg"]) && $page["valid_flg"] == 0){
                    $valid_flg = false;
                    break;
                }
                if ($page == null) {
                    unset($pages[$index]);
                }
            }
        }

        //get array page list for transfer bot
        $page_group_list = [];
        $page_efo_list = [];
        //get bot type image
        foreach ($pages as $key => $page){
            $pages[$key]['bot_type_img'] = config('constants.group_type_service_key.'.$page['sns_type']);

            if (isset($page['sns_type'])) {
                $lang_fb = @$service_list[$service_type['facebook']]['lang'];
                $lang_line = @$service_list[$service_type['line']]['lang'];
                $lang_web = @$service_list[$service_type['web_embed']]['lang'];
                switch ($page['sns_type']) {
                    case $service_type['facebook']:
                        $page_group_list[$lang_fb][$page['_id']] = $page['page_name'];
                        break;
                    case $service_type['line']:
                        $page_group_list[$lang_line][$page['_id']] = $page['page_name'];
                        break;
                    case $service_type['web_embed']:
                        $page_group_list[$lang_web][$page['_id']] = $page['page_name'];
                        break;
                    case $service_type['web_embed_efo']:
                        $page_efo_list[$page['_id']] = $page['page_name'];
                        break;
                }
            }
        }
        return Response::json(array('success' => true, 'page_efo_list' => $page_efo_list, 'page_group_list' => $page_group_list, 200));
    }

    public function show($view_user_id = null)
    {
    }

    public function create()
    {
        $user = Auth::user();
        $service_list = config('constants.group_type_service');
        $service_code = $service_list['facebook'];
        $add_sns_flg = $this->checkBotType($user, $service_code);
        if($add_sns_flg){
            $data = session('data');
            if(empty($data)){
                return redirect('bot')->with('alert-danger', trans('message.common_error'));
            }
            switch ($data['bot_type']){
                case config('constants.group_type_service.facebook'):
                    if(isset($data['accounts']) && count($data['accounts']) > 0) {
                        $data_account = $data['accounts'];
                        foreach ($data_account as $key => $value) {
                            if ($this->repConnectPage->getOneByField('page_id', $value['id'])) {
                                unset($data_account[$key]);
                            }
                        }
                    }
                    if(!isset($data['accounts']) || count($data['accounts']) == 0){
                        return redirect('bot')->with('alert-danger', trans('message.error_page_not_exist'));
                    }
                    break;
                case config('constants.group_type_service.line'):
                    break;
            }
            return view('bot.create')->with([
                'connects'         => $data['accounts'],
                'sns_id'           => $data['id']
            ]);
        }
        abort(404);
    }

    public function store(Request $request)
    {
        dd('store');
        $user = Auth::user();
        $service_list = config('constants.group_type_service');
        $service_code = $service_list['facebook'];
        $add_sns_flg = $this->checkBotType($user, $service_code);
        if($this->checkBotLimit(false) && $add_sns_flg) {
            $user_id = Auth::user()->id;
            $inputs = $request->all();
            $connect = $this->repConnect->getConnect($user_id, @$inputs['sns_id']);
            $connect_page = $this->repConnectPage->getOneByField('page_id', $inputs['page_id']);
            if ($connect && !$connect_page){
                $page_info = Common::callGetPageAPI($connect->access_token, $inputs['page_id']);
                if($page_info && $page_info['success']){
                    $result = Common::followAPI($page_info['data']['access_token']);
                    if($result['success']){

                        $timezone = $this->getTimezoneDefault();
                        $connect_id = $connect->id;
                        $data = $page_info['data'];
                        $data['page_name'] = $inputs['bot_name'];
                        $data['greeting_message'] = '';
                        $data['my_app_flg'] = 0;
                        $data['app_secret'] = null;
                        $data['timezone_code'] = @$timezone['code'];
                        $data['picture'] = @$data['picture']['url'];
                        $data['timezone_value'] = @$timezone['value'];
                        $check_button = Common::startButtonAPI($data['access_token']);
                        if(!$check_button['success']){
                            return redirect('bot')->with('alert-danger', $check_button['error']);
                        }

                        $connect_page = $this->repConnectPage->storeFacebook($connect_id, $data);
                        if ($connect_page) {
                            $this->common->setMessengerCode($page_info['data']['access_token'], $connect_page);
                        }
                        return redirect('bot')->with('alert-success', trans('message.save_success', ['name' => $inputs['bot_name']]));
                    }
                }
            }
        }
        return redirect('bot')->with('alert-danger', trans('message.common_error'));
    }

    public function update(BotRequest $request, $id){
        $inputs = $request->all();
        $connect_page = $this->repConnectPage->getById($id);
        $inputs = ['page_name' => $inputs['bot_name']];
        $this->repConnectPage->update($connect_page, $inputs);
        return Response::json(array(
            'success'       => true,
        ), 200);

    }

    public function updateStatus(){
        $connects = $this->repConnect->getAllData();
        $service_type = config('constants.group_type_service');
        if($connects){
            foreach ($connects as $connect){
                if($connect->type == 1){
                    $this->repConnect->updateStatusType($connect, $service_type['facebook']);
                    $connect_pages = $this->repConnectPage->getPageByCondition($connect->id, ['sns_type' => null]);
                    if($connect_pages){
                        foreach ($connect_pages as $connect_page){
                            $this->repConnectPage->update($connect_page, ['sns_type' => $service_type['facebook']]);
                        }
                    }
                }
                $connect_page2 = $this->repConnectPage->getPageByCondition($connect->id, ['my_app_flg' => 0, 'sns_type' => $service_type['facebook']]);
                $my_app_flg = 0;
                if($connect_page2){
                    foreach ($connect_page2 as $connect_page){
                        $this->repConnectPage->updateSystemPage($connect_page, $my_app_flg);
                    }
                }
                $users = $this->repUser->getAllByField('bot_template', null);
                foreach ($users as $user){
                    $this->repUser->update($user, ['bot_template' => array()]);
                }
            }
        }
    }

    public function edit($id){
    }

    public function destroy($id){
        $connect_page = $this->repConnectPage->getById($id);
        $service_type = config('constants.group_type_service');
        $user_login = Auth::user();
        $bot_share = $this->repBotRole->getBotShareByUser($id, $user_login->id);
        if(!$bot_share ||($bot_share && $bot_share->authority == config('constants.bot_role_authority.admin'))){
            if($connect_page->sns_type == $service_type['facebook']){
                if(isset($connect_page->my_app_flg) && $connect_page->my_app_flg == 1){
                    $access_token = $connect_page->origin_page_access_token;
                }else{
                    $access_token = $connect_page->page_access_token;
                }
                Common::deletingMenuAPI($access_token);
                Common::unsetGreetingMessageAPI($access_token);
                Common::deleteStartButtonAPI($access_token);
                Common::unfollowAPI($access_token);
            }
            $this->repConnectPage->destroy($id);
            return Response::json(array('success' => true), 200);
        }
        $errors['msg'] = trans("message.common_error");
        return Response::json(array('success' => false, 'errors' => $errors), 400);
    }

    public function serviceAdd(Request $request){
        $inputs = $request->all();
        if($request->has('service_item_key')){
            $service_key = $inputs['service_item_key'];
            $service_type = config('constants.group_type_service');
            $service = $this->repMaster->getServiceActive('service', $service_key);
            if($service ){
                switch ($service_key){
                    case $service_type['facebook'] :
                        return redirect()->action('BotController@handleFacebook', ['return_home_flg' => 0]);
                        break;
                    case $service_type['line'] :
                        return redirect()->route('bot.line');
                        break;
                    case $service_type['web_embed'] :
                        return redirect()->route('bot.WebEmbed');
                        break;
                    case $service_type['web_embed_efo'] :
                        return redirect()->route('bot.WebEmbed', ['web_type' => 'efo']);
                        break;
                    case $service_type['chatwork'] :
                        return redirect()->route('bot.chatwork');
                        break;

                }
            }
        }
        return redirect()->route('bot.index')->with('alert-danger', trans('message.service_not_registered'));
    }

    public function checkBotLimit($is_ajax_flg = true){
        $result = true;
        $user_authority = config('constants.authority');
        $user = Auth::user();
        $limit_bot = 0;

        //get max_bot of Plan if not exist max_bot_number of user
        if(isset($user->max_bot_number) && $user->max_bot_number != '') {
            $limit_bot = $user->max_bot_number;
        } elseif(isset($user->plan) && $user->plan != '') {
            $plan = $this->repPlan->getOneByField('code', $user->plan);
            if($plan) {
                $limit_bot = $plan->max_bot;
            }
        }
        if($user->authority != $user_authority['admin']){
            $current_bot = $this->getCountConnectPage($user->id);
            if($user->authority == $user_authority['agency']){
                $current_bot += $this->getBotNumberAgency($user->id);
            }
            if($limit_bot != config('constants.plan.unlimit') && $current_bot >= $limit_bot){
                $result = false;
            }

            Log::info('Limit Bot: '.$limit_bot.' ---- Number Bot current: '.$current_bot);
        }
        if($is_ajax_flg) {
            return Response::json(array(
                'success'       => $result,
            ), 200);
        }
        return $result;
    }

    public function handleFacebook($return_home_flg = false) {
        if(!session_id()) {
            session_start();
        }
        $fb = new Facebook([
            'app_id' => config('services.facebook.client_id'),
            'app_secret' => config('services.facebook.client_secret'),
            'default_graph_version' => 'v2.6',
            'grant_type' => 'fb_exchange_token',
        ]);

        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email','manage_pages', 'pages_messaging', 'business_management', 'pages_messaging_subscriptions']; // optional
        $urlCallback = config('services.facebook.redirect').'?return_home_flg='.$return_home_flg;
        Log::info('$urlCallback: '.$urlCallback);
        $url = $helper->getLoginUrl($urlCallback, $permissions);
        return redirect($url);
    }

    public function handleFacebookCallback(Request $request) {
        $inputs = $request->all();
        $return_home_flg = isset($inputs['return_home_flg']) ? $inputs['return_home_flg'] : false;
        Log::info('$return_home_flg: '.$return_home_flg);
        if(!session_id()) {
            session_start();
        }
        $fb = new Facebook([
            'app_id' => config('services.facebook.client_id'),
            'app_secret' => config('services.facebook.client_secret'),
            'default_graph_version' => 'v2.6',
            'persistent_data_handler'=>'session',
            'grant_type' => 'fb_exchange_token'
        ]);
        $helper = $fb->getRedirectLoginHelper();
        $_SESSION['FBRLH_state']=$_GET['state'];
        try {
            $user_id        = Auth::user()->id;
            $access_token    = $helper->getAccessToken();
            $response       = $fb->get('/me?fields=id,name,email,picture,accounts{username,cover,picture.type(large),about,location,link,name,id,category,access_token}', $access_token);
            $userFb         = $response->getGraphUser();
            $connect = $this->repConnect->getConnect($user_id, $userFb['id']);
            if(isset($userFb['accounts'])){
                $accounts = $userFb['accounts'];
                foreach ($accounts as $key => $page){
                    $connect_page = $this->repConnectPage->getOneByField('page_id', $page["id"]);
                    if($connect_page && isset($connect_page->valid_flg) && $connect_page->valid_flg == 0){
                        $inputs = [
                            'access_token' => $page["access_token"],
                            'valid_flg' => 1
                        ];
                        $this->repConnectPage->update($connect_page, $inputs);
                        Common::startButtonAPI($page["access_token"]);
                    }
                }
            }

            $userFb['access_token'] = (string)$access_token;
            $userFb['avatar'] = @$userFb['picture']['url'];
            if($connect){
                $this->repConnect->update($connect, $userFb);
            }
            else{
                $userFb['type'] = config('constants.group_type_service.facebook');
                $userFb['sns_id'] = $userFb['id'];
                $userFb['bot_name'] = $userFb['name'];
                $this->repConnect->store($user_id, $userFb);
            }
        } catch(FacebookResponseException $e) {
            Log::info('---------error----------');
            Log::info($e->getMessage());
            if($return_home_flg){
                return redirect()->route('bot.index')->with('alert-danger', trans('message.error_get_data_facebook'));
            }
            return redirect()->route('bot.index')->with('alert-danger', trans('message.error_get_data_facebook'));
        } catch(FacebookSDKException $e) {
            Log::info('----------------error------------');
            Log::info($e->getMessage());
            if($return_home_flg){
                return redirect()->route('bot.index')->with('alert-danger', trans('message.error_get_data_facebook'));
            }
            return redirect()->route('bot.index')->with('alert-danger', trans('message.error_get_data_facebook'));
        }
        if($return_home_flg){
            return redirect()->route('bot.index');
        }
        if (isset($access_token)) {
            // Logged in!
            $_SESSION['facebook_access_token'] = (string) $access_token;
        }
        $userFb['bot_type'] = config('constants.group_type_service.facebook');
        session(['data' => $userFb]);
        return redirect()->route('bot.create');
    }

    public function simpleData($data) {
        $result         = [];
        $lang           = Lang::locale();
        $column_lang    = 'name_'.$lang;
        if($data){
            foreach ($data as $index => $item) {
                $result[$item->code] = array(
                    'lang'       => $item->$column_lang,
                    'active_flg' => $item->active_flg
                );
            }
        }
        return $result;
    }

    public function createLineBot() {
        $user = Auth::user();
        $service_list = config('constants.group_type_service');
        $service_code = $service_list['line'];
        $add_sns_flg = $this->checkBotType($user, $service_code);
        if($add_sns_flg){
            return view('bot.line');
        }
        abort(404);
    }

    public function createLine(BotRequest $request) {
        $user = Auth::user();
        $service_list = config('constants.group_type_service');
        $service_code = $service_list['line'];
        $add_sns_flg = $this->checkBotType($user, $service_code);
        if($this->checkBotLimit(false) && $add_sns_flg) {
            $user_id = Auth::user()->id;
            $inputs  = $request->all();

            $file = $request->file('picture');
            $check = Common::validatingAccessTokens($inputs['channel_access_token']);
            $thumbnail_size = config('constants.thumbnail_size');
            if ($check['success']) {
                $data = $check['body'];
                if ($data && $data['client_id'] == $inputs['channel_id']) {
                    if (!empty($file)) {
                        $extension_file_upload = $inputs['picture']->getClientOriginalExtension();
                        $file_name = uniqid() . '.' . $extension_file_upload;
                        $file_path = config('constants.path_upload') . DIRECTORY_SEPARATOR . 'bot_picture' . DIRECTORY_SEPARATOR . $file_name;
                        $this->resizeImage($this->file_manager, $file, $thumbnail_size);
                        $this->uploadFileAzure($file, $file_path);
                        $inputs['avatar']           = $file_name;
                        $inputs['picture']   = $file_name;
                    }


//                    $profile = Common::getProfile($inputs['channel_access_token']);
//                    $profile = @$profile['body'];
//                    if ($profile) {
                    $inputs['sns_id']           = $data['client_id'];
                    $inputs['access_token']     = $inputs['channel_access_token'];
                    $inputs['type']             = config('constants.group_type_service.line');
                    $inputs['email']            = '';

                    $connect = $this->repConnect->store($user_id, $inputs);
                    if($connect){
                        $timezone = $this->getTimezoneDefault();
                        $inputs['timezone_code'] = @$timezone['code'];
                        $inputs['timezone_value'] = @$timezone['value'];
                        $connect_page = $this->repConnectPage->store_line($connect->id, $inputs);
                        if($connect_page){
                            return redirect()->action('BotSettingController@botSetting', [$connect_page->id])->with('alert-success', trans('message.line_success'));
                        }
                    }
//                    }
                }else{
                    return Redirect::back()->withInput()->withErrors([
                        'channel_id' => trans('message.channel_id_error')
                    ]);
                }
            }else {
                return Redirect::back()->withInput()->withErrors([
                    'channel_access_token' => trans('message.access_token_error')
                ]);
            }
        }
        return redirect('bot')->with('alert-danger', trans('message.common_error'));

    }

    public function createWebEmbedBot($web_type = '') {
        $user = Auth::user();
        $service_list = config('constants.group_type_service');
        $service_code = $service_list['web_embed'];
        if($web_type == 'efo'){
            $service_code = $service_list['web_embed_efo'];
        }
        $add_sns_flg = $this->checkBotType($user, $service_code);
        if($add_sns_flg){
            return view('bot.web_embed')->with([
                'web_type' => $web_type,
            ]);
        }
        abort(404);
    }

    public function createWebEmbed(BotRequest $request, $web_type = '') {
        $user = Auth::user();
        $service_list = config('constants.group_type_service');
        $service_code = $service_list['web_embed'];
        if($web_type == 'efo'){
            $service_code = $service_list['web_embed_efo'];
        }
        $add_sns_flg = $this->checkBotType($user, $service_code);
        if($this->checkBotLimit(false) && $add_sns_flg) {
            try {
                $user_id = Auth::user()->id;
                $inputs = $request->all();
                //create connect page for web_embed if not exist
                $service_code = config('constants.group_type_service.web_embed');
                if($web_type) {
                    $service_code = config('constants.group_type_service.web_embed_efo');
                }
                $thumbnail_mini_size = config('constants.thumbnail_mini_size');
                $connect = $this->repConnect->getFirstByType($user_id, $service_code);
                if(!count($connect)) {
                    $connect_inputs = [
                        'email' => '',
                        'sns_id'    => '',
                        'bot_name'  => $web_type ? 'EFO WebEmbed' : 'WebEmbed',
                        'type'  => $service_code,
                    ];
                    $connect = $this->repConnect->store($user_id, $connect_inputs);
                }
                //create connect page
                $file = $request->file('picture');

                if (!empty($file)) {
                    $extension_file_upload = $inputs['picture']->getClientOriginalExtension();
                    $file_name = uniqid() . '.' . $extension_file_upload;
                    $file_path = config('constants.path_upload') . DIRECTORY_SEPARATOR . 'bot_picture' . DIRECTORY_SEPARATOR . $file_name;
                    $this->resizeImage($this->file_manager, $file, $thumbnail_mini_size);
                    $this->uploadFileAzure($file, $file_path);
//                    $connect_page_inputs['picture'] = $file_name;
                }else {
                    $file_name = 'default_'.config('constants.group_type_service_key')[$service_code].'.png';
                }
                //
//                $destination = config('constants.path_upload') . DIRECTORY_SEPARATOR . 'bot_picture';
//                $picture_url = $this->moveBotImage($destination, $file, 'picture');
                $timezone = $this->getTimezoneDefault();;
                $connect_page_inputs = [
                    'page_name' => $inputs['bot_name'],
                    'sns_type' => $service_code,
                    'picture' => @$file_name,
                    'timezone_code' => @$timezone['code'],
                    'timezone_value' => @$timezone['value'],
                    'setting' => $web_type ? config('constants.efo_web_embed_iframe_setting') : config('constants.web_embed_iframe_setting'),
                ];
                $this->repConnectPage->store($connect->id, $connect_page_inputs);
                return redirect('bot')->with('alert-success', trans('message.save_success', ['name' => $inputs['bot_name']]));

            } catch (\Exception $e) {
                Log::info($e);
            }
        }
        return redirect()->back()->with('alert-danger', isset($common_error) ? $common_error : trans('message.common_error'));
    }

    public function createChatworkBot(){
        $user = Auth::user();
        $service_list = config('constants.group_type_service');
        $service_code = $service_list['chatwork'];
        $add_sns_flg = $this->checkBotType($user, $service_code);
        if($add_sns_flg){
            return view('bot.chatwork');
        }
        abort('404');
    }

    public function createChatwork(BotRequest $request){
        $user = Auth::user();
        $service_list = config('constants.group_type_service');
        $service_code = $service_list['chatwork'];
        $add_sns_flg = $this->checkBotType($user, $service_code);
        if($add_sns_flg){
            try {
                $user_id = Auth::user()->id;
                $inputs = $request->all();
                $api_token = $inputs['api_token'];
                $result_api = Common::clientChatworkRequest('https://api.chatwork.com/v2/me', 'get', [
                    'X-ChatWorkToken' => $api_token
                ]);
                if(isset($result_api['success']) && $result_api['success'] && !empty($result_api['response'])){
                    $response = $result_api['response'];
                    //create connect page for web_embed if not exist
                    $service_code = config('constants.group_type_service.chatwork');
                    $connect = $this->repConnect->getFirstByType($user_id, $service_code);
                    if(empty($connect)) {
                        $connect_inputs = [
                            'email' => '',
                            'sns_id'    => '',
                            'bot_name'  => $inputs['bot_name'],
                            'type'  => $service_code,
                        ];
                        $connect = $this->repConnect->store($user_id, $connect_inputs);
                    }
                    $picture_path_default = 'https://' . config('filesystems.disks.azure.name'). '.blob.core.windows.net/' . config('filesystems.disks.azure.container') . '/' . config('constants.path_upload');
                    $timezone = $this->getTimezoneDefault();
                    $connect_page_inputs = [
                        'page_name' => $inputs['bot_name'],
                        'sns_type' => $service_code,
                        'channel_access_token' => $inputs['api_token'],
                        'picture' => !empty($response['avatar_image_url']) ? $response['avatar_image_url'] : $picture_path_default.'/bot_picture/default_chatwork.png',
                        'timezone_code' => @$timezone['code'],
                        'timezone_value' => @$timezone['value'],
                        'chatwork_account_id' => @$response['account_id'],
                        'chatwork_account_name' => @$response['name'],
                    ];
                    $connect_page = $this->repConnectPage->store_chatwork($connect->id, $connect_page_inputs);
                    return redirect()->action('BotSettingController@botSetting', $connect_page->id)->with('alert-success', trans('message.chatwork_success'));
                }
                $error = trans('message.chatwork_invalid_api_token');
                return redirect()->route('bot.chatwork')->withInput($inputs)->withErrors([
                    'api_token' => $error
                ]);
            } catch (\Exception $e) {
                return redirect('bot')->with('alert-danger', trans('message.common_error'));
            }
        }
        return redirect('bot')->with('alert-danger', trans('message.common_error'));
    }

    public function confirm($confirmation_token = null){
        $botRole = $this->repBotRole->getOneByField('confirmation_token', $confirmation_token);
        $user_invite = Auth::user();
        $lang  = Lang::locale();
        $column_lang    = 'name_'.$lang;
        if($botRole && $botRole->user_id == $user_invite->id){
            $connect_page = $this->repConnectPage->getById($botRole->connect_page_id);
            if($connect_page){
                $connect = $this->repConnect->getById($connect_page->connect_id);
                if($connect){
                    $user = $this->repUser->getById($connect->user_id);
                    $bot_role_authority = $this->repMaster->getServiceActive('bot_role_authority', $botRole->authority);
                    $botRole->authority_name = $bot_role_authority->{$column_lang};
                    return view('bot.confirm')->with([
                        'user' => $user,
                        'connect_page' => $connect_page,
                        'bot_role' => $botRole
                    ]);
                }
            }
        }
        return view('errors.register');
    }

    public function userAccept($confirmation_token){
        $botRole = $this->repBotRole->getOneByField('confirmation_token', $confirmation_token);
        $user_invite = Auth::user();
        if($botRole && $botRole->user_id == $user_invite->id) {
            $connect_page = $this->repConnectPage->getById($botRole->connect_page_id);
            if($connect_page){
                $bot_role_authority = $this->repMaster->getGroupFillSelectBox('bot_role_authority');
                $authority_name = isset($bot_role_authority[$botRole->authority]) ? $bot_role_authority[$botRole->authority] : '';
                $inputs = array(
                    'confirmation_token_reset_flg' => true
                );
                $connect = $this->repConnect->getById($connect_page->connect_id);
                if($connect){
                    $user = $this->repUser->getById($connect->user_id);
                    if($user){
                        $this->repBotRole->update($botRole, $inputs);
                        return redirect('bot')->with('alert-success', trans('message.bot_role_success_user_accept', ['bot_name' => $connect_page->page_name, 'user_name' => $user->name, 'bot_authority' => $authority_name]));
                    }
                }
            }
            return redirect('bot')->with('alert-danger', trans('message.common_error'));
        }
        return view('errors.register');
    }

    public function userIgnore($confirmation_token){
        $botRole = $this->repBotRole->getOneByField('confirmation_token', $confirmation_token);
        $user_invite = Auth::user();
        if($botRole && $botRole->user_id == $user_invite->id) {
            $connect_page = $this->repConnectPage->getById($botRole->connect_page_id);
            $bot_role_authority = $this->repMaster->getGroupFillSelectBox('bot_role_authority');
            $authority_name = isset($bot_role_authority[$botRole->authority]) ? $bot_role_authority[$botRole->authority] : '';
            if($connect_page){
                $connect = $this->repConnect->getById($connect_page->connect_id);
                if($connect) {
                    $user = $this->repUser->getById($connect->user_id);
                    if ($user) {
                        $this->repBotRole->destroy($botRole->id);
                        return redirect('bot')->with('alert-success', trans('message.bot_role_success_user_ignore', ['bot_name' => $connect_page->page_name, 'user_name' => $user->name, 'bot_authority' => $authority_name]));
                    }
                }
            }
            return redirect('bot')->with('alert-danger', trans('message.common_error'));
        }
        return view('errors.register');
    }

    public function getApiGenericLine(){
        return Response::json(array(
            'success' => 200,
            'message' => json_decode('{"type":"template","altText":"gen1","template":{"type":"carousel","columns":[{"text":"t1","thumbnailImageUrl":"https://siviaggia.files.wordpress.com/2015/10/aurora-boreale-tromso-norvegia-o.jpg?w=1200","title":"gen1","actions":[{"type":"uri","label":"b1","uri":"https://siviaggia.files.wordpress.com/2015/10/aurora-boreale-tromso-norvegia-o.jpg?w=1200"}]},{"text":"t2","thumbnailImageUrl":"https://siviaggia.files.wordpress.com/2015/10/aurora-boreale-tromso-norvegia-o.jpg?w=1200","title":"gen2","actions":[{"type":"uri","label":"b2","uri":"https://siviaggia.files.wordpress.com/2015/10/aurora-boreale-tromso-norvegia-o.jpg?w=1200"}]},{"text":"t3","thumbnailImageUrl":"https://siviaggia.files.wordpress.com/2015/10/aurora-boreale-tromso-norvegia-o.jpg?w=1200","title":"gen3","actions":[{"type":"uri","label":"b3","uri":"https://siviaggia.files.wordpress.com/2015/10/aurora-boreale-tromso-norvegia-o.jpg?w=1200"}]},{"text":"t4","thumbnailImageUrl":"https://siviaggia.files.wordpress.com/2015/10/aurora-boreale-tromso-norvegia-o.jpg?w=1200","title":"gen4","actions":[{"type":"uri","label":"b4","uri":"https://siviaggia.files.wordpress.com/2015/10/aurora-boreale-tromso-norvegia-o.jpg?w=1200"}]}]}}')
        ), 200);
    }

    private function getTimezoneDefault(){
        $lang = Lang::locale();
        $data = [];
        $timezone_code = config('constants.timezone_default.'.$lang);
        $timezone = $this->repTimezone->getOneByField('code', $timezone_code);
        if($timezone_code && $timezone){
            $data = [
                'code'   => $timezone_code,
                'value' => $timezone->timezone,
            ];
        }
        return $data;
    }

    public function setKeySend($connect_page_id, BotRequest $request){
        $inputs = $request->all();
        $key_send_flg = (int)$inputs['key_send'];
        $connect_page = $this->repConnectPage->getById($connect_page_id);
        $this->repConnectPage->updateKeySend($connect_page, $key_send_flg);
        return Response::json(array(
            'success'       => true,
        ), 200);
    }

    public function convertData($user_list) {
        $result = [];
        if (count($user_list)) {
            foreach ($user_list as $user) {
                $result[$user->id] = $user->name . '(' . $user->email .')';
            }
        }
        return $result;
    }

    public function copyToUser(BotRequest $request) {
        $inputs = $request->all();
        $service_code = config('constants.group_type_service.web_embed_efo');
        if ($inputs && Auth::user()->authority == config('constants.authority.admin')) {
            try {
                $connect = $this->repConnect->getFirstByType($inputs['to_user'], $service_code);
                if(!count($connect)) {
                    $connect_inputs = [
                        'email' => '',
                        'sns_id'    => '',
                        'bot_name'  => 'EFO WebEmbed',
                        'type'  => $service_code,
                    ];
                    $connect = $this->repConnect->store($inputs['to_user'], $connect_inputs);
                }
                $connect_page = $this->repConnectPage->getById($inputs['transfer_from_bot']);
                if ($connect_page) {
                    $inputs['connect_id'] = $connect->id;
                    $this->repConnectPage->update($connect_page, $inputs);
                }
                return Response::json(array('success' => true , 'msg' =>  trans('message.copy_to_user_success', ['user_name' => $inputs['user_name']])), 200);
            } catch (\Exception $e) {
                Log::info($e);
            }
        }
        return Response::json(array('success' => false, 'msg' => trans('message.common_error')), 400);
    }

    function handleGoogle(Request $request, $library_id){
        session(['data' => [
            'library_id' => $library_id
        ]]);
        $client = Common::getGoogleClient();
        $scopes = config('google_sheet.scopes');
        $client->setScopes($scopes);
        $url = $client->createAuthUrl();

        return redirect($url);
    }

    function handleGoogleCallback(Request $request)
    {
        $data = session('data');
        $inputs = $request->all();
        if (!empty($data['library_id']) && !empty($inputs['code'])) {
            $library_id = $data['library_id'];
            $library = $this->repLibrary->getById($library_id);
            if ($library) {
                $active = config('constants.active');
                $authCode = $inputs['code'];
                $client = Common::getGoogleClient();
                $credentials = $client->fetchAccessTokenWithAuthCode($authCode);
                $refresh_token = $client->getRefreshToken();

                // if access token exprire, get new token via refresh token
                if ($client->isAccessTokenExpired()) {
                    $client->fetchAccessTokenWithRefreshToken($refresh_token);
                    $credentials = $client->getAccessToken();
                    $refresh_token = $client->getRefreshToken();
                }
                $input_update = [
                    'read_sheet_flg' => $active['disable'],
                    'messages' => []
                ];
                if (isset($credentials) && isset($refresh_token)) {
                    $sheet_id = $library->sheet_id;

                    $range_column = [
                        'bot' => $library->column_bot,
                        'user' => $library->column_user
                    ];
                    // check google sheet public by access_token
                    $result = Common::checkGoogleSheetPublic($client, $sheet_id, $range_column, $credentials);
                    if (isset($result) && $result['success']) {
                        $input_update = [
                            'read_sheet_flg' => $active['enable'],
                            'credentials' => $credentials,
                            'refresh_token' => $refresh_token,
                            'messages' => isset($result['messages']) ? $result['messages'] : []
                        ];
                        $this->repLibrary->updateStatusSheet($library, $input_update);
                        return redirect()->route('bot.library.index', [$library->connect_page_id])->with('alert-success', trans('message.oauth_success'));
                    }
                }
                $this->repLibrary->updateStatusSheet($library, $input_update);
                return redirect()->route('bot.library.index', [$library->connect_page_id])->with('alert-danger', trans('message.oauth_error'));
            }
        }
        abort('404');
    }

    public function setOption(BotRequest $request) {
        $inputs = $request->all();
        try {
            $connect_page = $this->repConnectPage->getById($inputs['connect_page_id']);
            if($connect_page) {
                $inputs['list_option'] = [];
                $option_set = [
                    'option' => $inputs['option'],
                    'scenario_connect' => $inputs['scenario_connect']
                ];
                if (isset($connect_page->list_option) && count($connect_page->list_option)) {
                    $list_option = $connect_page->list_option;
                    $list_option_id = [];
                    foreach ($list_option as $index => $option_item) {
                        if ($inputs['option'] == $option_item['option']) {
                            if ($inputs['scenario_connect'] != '' && $option_item['scenario_connect'] != $inputs['scenario_connect']) {
                                $list_option[$index] = $option_set;
                            }elseif ($inputs['scenario_connect'] == '') {
                                unset($list_option[$index]);
                                array_reverse($list_option);
                            }
                        }
                        $list_option_id[] = $option_item['option'];
                    }
                    if (!in_array($inputs['option'], $list_option_id) && $inputs['scenario_connect'] != '') {
                        array_push($list_option, $option_set);
                    }
                    $inputs['list_option'] = $list_option;
                }else {
                    $inputs['list_option'] = [$option_set];
                }
                $this->repConnectPage->update($connect_page, $inputs);
            }
            return Response::json(array('success' => true , 'msg' =>  trans('message.set_option_success')), 200);
        } catch (\Exception $e) {
            Log::info($e);
        }
        return Response::json(array('success' => false, 'msg' => trans('message.common_error')), 400);
    }

    public function listUserBot(Request $request){
        $login_user = Auth::user();
        $authority = config('constants.authority');
        if($login_user->authority == $authority['admin']){
            $group_service = $this->repMaster->getGroupFillSelectBox(config('constants.master_group.service'));
            return view('bot.list_user_bot')->with([
                'group_service' => $group_service
            ]);
        }
        abort(404);
    }

    public function getListUserBot(Request $request){
        $login_user = Auth::user();
        $authority = config('constants.authority');
        $inputs = $request->all();
        if($login_user->authority == $authority['admin']){
            $service_all  = $this->repMaster->getAll2(config('constants.master_group.service'));
            $service_list = $this->simpleData($service_all);
            $sns_type = null;
            if(isset($inputs['sns_type'])){
                $sns_type = $inputs['sns_type'];
            }
            $keyword_search = isset($inputs['keyword']) ? trim($inputs['keyword']) : '';
            $start = (int)$inputs['start'];
            $length = (int)$inputs['length'];

            $users = $this->repUser->getUserByKeywordSearch($keyword_search);
            $user_ids = [];
            if(count($users)){
                $user_ids = $users->toArray();
            }
            $connects = $this->repConnect->getAllByUserIdArr($user_ids);
            $connect_ids = [];
            if(count($connects)){
                $connect_ids = $connects->toArray();
            }
            $rows = $this->repConnectPage->getAll($start, $length,  $keyword_search, $sns_type, $connect_ids);
            $count = $this->repConnectPage->getCount($keyword_search, $sns_type, $connect_ids);
            $data = new Collection();
            $cnt = ($start / $length) * $length + 1;
            foreach ($rows as $row) {
                $data_arr = [
                    'id' => $row['id'],
                    'no' => $cnt++,
                    'sns_type' => $row['sns_type'],
                    'service_list' => $service_list,
                    'page_name' => $row['page_name']
                ];
                $email = $user_name = $company_name = '';
                $connect = $this->repConnect->getById($row->connect_id);
                if($connect){
                    $user = $this->repUser->getById($connect->user_id);
                    if($user){
                        $email = $user->email;
                        $user_name = $user->name;
                        $company_name = $user->company_name;
                    }
                }
                $data_arr['email'] = $email;
                $data_arr['user_name'] = $user_name;
                $data_arr['company_name'] = $company_name;
                $data->push($data_arr);
            }
            $dt = app('datatables');
            $request = $dt->getRequest();
            $request->merge( array( 'start' => 0 ) );
            return $dt->collection($data)
                ->addColumn('sns_type', function ($row) {
                    $service_list = $row['service_list'];
                    $sns_type = $row['sns_type'];
                    $result = '';
                    if(isset($service_list[$sns_type])){
                        $result = $service_list[$sns_type]['lang'];
                    }
                    return $result;
                })
                ->addColumn('page_name', function ($row) {
                    $page_name = $row['page_name'];
                    return '<a class="page-name-link" href="'. route('bot.scenario.index', [ $row['id']]) .'" target="_blank">'. $page_name .'</a>';
                })
                ->setTotalRecords($count)->make(true);
        }
        return null;
    }

}