<?php

namespace App\Repositories;

use App\Mongodb\ConnectPage;
use Illuminate\Support\Facades\Log;

class ConnectPageRepository extends BaseRepository
{
	/**
	 * Create a new ChannelRepository instance.
	 *
   	 * @param  App\ConnectPage $connect
	 * @return void
	 */
	public function __construct(ConnectPage  $connect)
	{
		$this->model = $connect;
	}

    /**
     * Create a company.
     *
     * @param  array  $inputs
     * @return App\ConnectPage
     */

    public function store($connect_id, $inputs)
    {
        $connect_page = new $this->model;
        $connect_page->connect_id = $connect_id;
        $connect_page->sns_type = $inputs["sns_type"];
        $this->save($connect_page, $inputs);
        return $connect_page;
    }

    public function storeFacebook($connect_id = null, $inputs)
    {
        $connect_page                        = new $this->model;
        $connect_page->connect_id            = $connect_id;
        $connect_page->page_id               = $inputs['id'];
        $connect_page->link                  = $inputs['link'];
        $connect_page->category              = $inputs['category'];
        $connect_page->picture               = @$inputs['picture']['url'];
        $connect_page->greeting_message      = $inputs['greeting_message'];
        $connect_page->persistent_menu_flg   = 1;
        $connect_page->my_app_flg =0;
        $connect_page->app_secret            = $inputs['app_secret'];
        $connect_page->sns_type  = config('constants.group_type_service.facebook');
        $connect_page->validate_token        = config('constants.secret_key');
        $connect_page->origin_app_id = null;
        $connect_page->origin_page_access_token = null;
        $connect_page->timezone_code = $inputs['timezone_code'];
        $connect_page->timezone_value = $inputs['timezone_value'];
        $this->save($connect_page, $inputs);
        return $connect_page;
    }

	public function store_line($connect_id, $inputs)
	{
		$connect_page                       = new $this->model;
		$connect_page->connect_id           = $connect_id;
		$connect_page->channel_id           = $inputs['channel_id'];
		$connect_page->picture              = @$inputs['picture'];
		$connect_page->persistent_menu_flg  = 1;
        $connect_page->sns_type  = config('constants.group_type_service.line');
		$connect_page->channel_secret       = $inputs['channel_secret'];
		$connect_page->page_name            = $inputs['bot_name'];
		$connect_page->channel_access_token = $inputs['channel_access_token'];
        $connect_page->timezone_code = $inputs['timezone_code'];
        $connect_page->timezone_value = $inputs['timezone_value'];
        $connect_page->validate_token        = bin2hex(openssl_random_pseudo_bytes(24));
		$connect_page->save();
		return $connect_page;
	}

	public function store_chatwork($connect_id, $inputs){
        $connect_page = new $this->model;
        $connect_page->connect_id = $connect_id;
        $connect_page->chatwork_account_id = $inputs['chatwork_account_id'];
        $connect_page->chatwork_account_name = $inputs['chatwork_account_name'];
        $connect_page->sns_type  = config('constants.group_type_service.chatwork');
        $connect_page->validate_token        = bin2hex(openssl_random_pseudo_bytes(24));
        $this->save($connect_page, $inputs);
        return $connect_page;
    }

    public function store_template($inputs)
    {
        $connect_page = new $this->model;
        $connect_page->user_id       = $inputs['user_id'];
        $connect_page->template_flg  = $inputs['template_flg'];
        $this->save($connect_page, $inputs);
        return $connect_page;
    }

	/**
	 * Save the Channel.
	 *
	 * @param  App\ConnectPage $connect_page
	 * @param  Array  $inputs
	 * @return $connect_page
	 */
    private function save($connect_page, $inputs)
    {
        if (isset($inputs['page_name'])) {
            $connect_page->page_name          = $inputs['page_name'];
        }
        if (isset($inputs['access_token'])) {
            $connect_page->page_access_token  = $inputs['access_token'];
        }
        if (isset($inputs['valid_flg'])) {
            $connect_page->valid_flg  = $inputs['valid_flg'];
        }
        if (isset($inputs['channel_id'])) {
            $connect_page->channel_id = $inputs['channel_id'];
        }
        if (isset($inputs['channel_access_token'])) {
            $connect_page->channel_access_token = $inputs['channel_access_token'];
        }
        if (isset($inputs['channel_secret'])) {
            $connect_page->channel_secret = $inputs['channel_secret'];
        }
        if (isset($inputs['greeting_message'])) {
            $connect_page->greeting_message = $inputs["greeting_message"];
        }
        if (isset($inputs['persistent_menu_flg'])) {
            $connect_page->persistent_menu_flg = $inputs["persistent_menu_flg"];
        }
        if (isset($inputs['picture'])) {
            $connect_page->picture = $inputs["picture"];
        }
        if (isset($inputs['sns_type'])) {
            $connect_page->sns_type = $inputs["sns_type"];
        }
        if (isset($inputs['timezone_value'])) {
            $connect_page->timezone_value = $inputs['timezone_value'];
        }
        if (isset($inputs['timezone_code'])) {
            $connect_page->timezone_code = $inputs['timezone_code'];
        }
        if (isset($inputs['setting'])) {
            $connect_page->setting = $inputs['setting'];
        }
        if (isset($inputs['conversion_setting'])) {
            $connect_page->conversion_setting = $inputs['conversion_setting'];
        }
        if (isset($inputs['public_flg'])) {
            $connect_page->public_flg = (int)$inputs['public_flg'];
        }
        if (isset($inputs['connect_id'])) {
            $connect_page->connect_id = $inputs['connect_id'];
        }
        if (isset($inputs['list_option'])) {
            $connect_page->list_option = $inputs['list_option'];
        }
        if (isset($inputs['webhook_token'])) {
            $connect_page->webhook_token = $inputs['webhook_token'];
        }
        if (isset($inputs['webhook_url'])) {
            $connect_page->webhook_url = $inputs['webhook_url'];
        }
        if (isset($inputs['scenario_type'])) {
            $connect_page->scenario_type = $inputs['scenario_type'];
        }

        $connect_page->save();
        return $connect_page;
    }

	/**
	 * Update a Channel.
	 *
	 * @param  array  $inputs
	 * @param  App\ConnectPage $connect_page
	 * @return $connect_page
	 */
	public function update($connect_page, $input)
	{
        $this->save($connect_page, $input);
        return $connect_page;
	}

	public function updateChatwork($connect_page, $inputs){
        if (isset($inputs['picture'])) {
            $connect_page->picture = $inputs['picture'];
        }
        if (isset($inputs['chatwork_account_name'])) {
            $connect_page->chatwork_account_name = $inputs['chatwork_account_name'];
        }
        $this->save($connect_page, $inputs);
        $connect_page->save();
    }

	public function updateOriginPage($connect_page, $input){
        if($connect_page->origin_app_id != $input['app_id']){
            $connect_page->validate_token = bin2hex(openssl_random_pseudo_bytes(24));
        }
        $connect_page->my_app_flg                   = $input['my_app_flg'];
        $connect_page->app_secret                   = $input['app_secret'];
        $connect_page->origin_app_id                = $input['app_id'];
        $connect_page->origin_page_access_token     = $input['page_access_token'];
        $connect_page->save();
        return $connect_page;
    }

    public function updateSystemPage($connect_page, $my_app_flg){
        $connect_page->my_app_flg   = $my_app_flg;
        $connect_page->origin_app_id   = null;
        $connect_page->origin_page_access_token   = null;
        $connect_page->app_secret   = null;
        $connect_page->validate_token = config('constants.secret_key');
        $connect_page->save();
        return $connect_page;
    }

    public function updateMessengerCode($connect_page, $messenger_code){
        $connect_page->messenger_code  = @$messenger_code;
        $connect_page->save();
    }

    public function getAllPage($connect_id){
	    $model = new $this->model;
	    $model = $model->where('connect_id', $connect_id)
                       ->orderBy('created_at', 'DESC');
	    return $model->get();
    }

    public function getConnect($connect_id){
        $model = new $this->model;
        $model = $model->where('connect_id', $connect_id);
        return $model->first();
    }
    
    public function getPageByCondition($connect_id, $condition)
    {
        $model = new $this->model;
        $model = $model->where('connect_id', $connect_id);
        $model = $model->where($condition);
        return $model->get();
    }

    function getAllByCondition($condition, $id_list = null, $public_flg = '-1', $order_by = 'DESC', $order_column = 'created_at') {
        $model = $this->model;
        if(is_array($id_list)){
            $model = $model->whereIn('_id', $id_list);
        }
        if($public_flg != '-1'){
            $model = $model->orWhere('public_flg', $public_flg);
        }
        $model = $model->where($condition)
            ->orderBy($order_column, $order_by);
        return $model->get();
    }

    public function getAll($offset = 0, $limit = 10, $keyword_search = '', $sns_type = null, $connect_ids = [])
    {
        $model = new $this->model;
        $model = $model->where("template_flg", "<>", config('constants.flag.template'));
        if(count($connect_ids) && $keyword_search){
            $model = $model->where(function ($model) use ($connect_ids, $keyword_search) {
                $model->whereIn('connect_id', $connect_ids)
                    ->orWhere("page_name", "LIKE", "%$keyword_search%");
            });
        }elseif(count($connect_ids) == 0 && $keyword_search){
            $model = $model->where("page_name", "LIKE","%$keyword_search%");
        }
        if($sns_type){
            $model = $model->where("sns_type", $sns_type);
        }
        $model = $model->skip($offset)
            ->take($limit)
            ->orderBy('created_at', 'DESC');
        return $model->get();
    }

    public function getCount($keyword_search = '', $sns_type = null, $connect_ids = [])
    {
        $model = new $this->model;
        $model = $model->where("template_flg", "<>", config('constants.flag.template'));
        if(count($connect_ids) && $keyword_search){
            $model = $model->where(function ($model) use ($connect_ids, $keyword_search) {
                $model->whereIn('connect_id', $connect_ids)
                    ->orWhere("page_name", "LIKE", "%$keyword_search%");
            });
        }elseif(count($connect_ids) == 0 && $keyword_search){
            $model = $model->where("page_name", "LIKE","%$keyword_search%");
        }
        if($sns_type){
            $model = $model->where("sns_type", $sns_type);
        }
        return $model->count();
    }

    public function updateStatus($page, $valid_flg)
    {
        $page->valid_flg = $valid_flg;
        $page->save();
    }

    public function updateKeySend($page, $flg)
    {
        $page->key_send_flg = $flg;
        $page->save();
    }

    public function updateWebhookToken($page, $webhook_token)
    {
        $page->webhook_token = $webhook_token;
        $page->save();
    }
    /*use command convert data*/

    public function getAllBotEFO(){
        $model = new $this->model;
        $model = $model->where('sns_type', config('constants.group_type_service.web_embed_efo'));
        $model = $model->pluck('_id');
        return $model;
    }

    public function getAllBotExcludeEFO(){
        $model = new $this->model;
        $model = $model->where('sns_type', '<>',  config('constants.group_type_service.web_embed_efo'));
        $model = $model->pluck('_id');
        return $model;
    }

    public function getBotTypeCart($connect_id){
        $model = new $this->model;
        $model = $model->whereIn('connect_id', $connect_id);
        $model = $model->whereNotNull('scenario_type');
        $model = $model->where('scenario_type', config('constants.efo_type_scenario.add_to_cart'));
        return $model->first();
    }
}
