<?php

namespace App\Http\Controllers;

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

    public function index($view_user_id = null)
    {
        $user = Auth::user();
        $authority = config('constants.authority');
        $date_format = new \DateTime();
        $date_format_js_str = '';
        $date_format_str = '';
        if($user->authority == $authority['super_admin']){

                //get all user
                $user_profiles['all'] = $this->repUser->getAll($user, 0, config('constants.conversation_option.user_load_in_part'));

                //count all user for load ajax users
                $count_user_profiles = $this->repUser->getCount($user);

                $date_format = new \DateTime();

            return view('demo.index')->with([
                'date_format' => $date_format,
                'date_format_str' => $date_format_str,
                'date_format_js_str' => '',
                'user_profiles' => $user_profiles,
                'count_user_profiles' => $count_user_profiles,
            ]);
        }
        abort(404);
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

}