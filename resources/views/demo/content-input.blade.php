<div class="row send_wrapper">
    <div class="col-md-12">
        <div class="chat_send_area">
            <div class="chat_toolbar" >
                <div class="chat_input_submit_container">
                    <div class="chat_input_action_left pull-left">
                        <div class="input-actions">
                            <div class="scenario inline-block">
                                <div tabindex="0" title="Scenario" class="action-container">
                                    <i class="fa fa-sitemap"></i>
                                    <span class="badge up badge-info" style="display: none;">✓</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chat_input_action_right pull-right">
                        <span class="switch-key-send">
                        <span class=" icheck minimal">
                            <span class="checkbox single-row check_key_send">
                                <input type="checkbox" style="width: 20px" class="checkbox form-control icheckbox_minimal" id="key_send" {{isset($connect_page->key_send_flg) && $connect_page->key_send_flg ? 'checked' : ''}} name="terms_of_use" value="{{config('constants.active.enable')}}" />
                            </span>
                        </span>
                        <label class="control-label label-key">{{trans('message.key_enter_to_send')}}</label>
                        </span>
                        <span class="chat-send">
                            <button type="submit" class="btn btn-info btn-send-chat">{{trans('button.send')}}</button>
                        </span>
                    </div>
                </div>
            </div>
            <textarea id="chat-input" class="form-control chat-input" tabindex="1" style="height: 75px;" placeholder="{{trans('message.send_text')}}"></textarea>
        </div>
    </div>
</div>