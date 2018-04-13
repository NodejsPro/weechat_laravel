//globe variable
var common_data         = [];
var type_text           = 'text';
var type_tel            = 'phone';
var type_tel_no_hyphen  = 'no_hyphen';
var type_password       = 'password';
var type_password_confirm = 'password_confirm';
var type_input          = 'input';
var type_textarea       = 'textarea';
var type_radio          = 'radio';
var type_checkbox       = 'checkbox';
var type_pulldown       = 'pulldown';
var type_postal_code    = 'postal_code';
var type_terms_of_use   = 'terms_of_use';
var type_confirmation   = 'confirmation';
var type_file           = 'file';
var type_calendar       = 'calendar';
var type_calendar_select = 'select';
var type_calendar_embed = 'embed';
var type_calendar_period_of_time = 'period_of_time';
var type_mail           = 'mail';
var type_mail_confirm   = 'email_confirm';
var type_scenario_connect = 'scenario_connect';
var type_carousel = 'carousel';
var type_birthday = 'brithday';
var type_birthday_y_m = 'brithday_y_m';
var type_image_radio = 'image_radio';
var type_date = 'date';
var type_time = 'time';
var type_date_time = 'date_time';
var type_customize = 'customize';
var type_period_of_time = 'period_of_time';
var type_period_of_day = 'period_of_day';
var type_card_payment = 'card_payment';
var type_captcha = 'captcha';
var type_add_to_cart = 'add_to_cart';
var type_request_document = 'request_document';
var type_provinces_of_japan = 'the_provinces_of_japan';
var type_towns_and_villages_of_japan = 'towns_and_villages';
var type_month_date = 'month_date';
var type_captcha_only_numbers = 'only_numbers';
var type_captcha_only_letters = 'only_letters';
var type_detail_content = 'detail_content';
var list_text_button_next = ['Next', '次へ'];
var bot_lang_ja = 'ja';
var text_button_ja = '次へ';
var text_button = 'Next';
var type_image_document = 'image_title_subtitle';
var type_title_document = 'title_only';

var no_label = '001';
var label = '002';
var type_textarea_input = '001';
var min_radio_item      = 1;
var min_checkbox_item   = 1;
var max_generic_item    = 10;
var max_generic_button  = 3;
var icheck_change_event = true;
var is_call_icheck = true;

//message type active
var message_type_bot = false;

$(function () {
    message_area_select(null);

    ///////////////////////START ACTION USER MESAGE
    //get type of bot input textarea
    $('.scenario-body').on('click', '.user_scenario', function (event) {
        message_type_bot = false;
        //hide message bot area
        message_area_select(false);
        var messages_focus      = $('input.messages_focus'),
            user_message_name   = $(this).find('.messages_bot_content').attr('name'),
            filter_messages_focus = $('input.filter_messages_focus'),
            filter_message_name    = $(this).find('.filter_content ').attr('name');
        // if (messages_focus.val() != user_message_name) {
            var messages_bot_type = $(this).find('input.messages_bot_type').val();
            //set active for bot scenario
            $('.scenario-body .bot_scenario, .scenario-body .user_scenario').removeClass('active');
            $(this).addClass('active');
            //get type from bot message and set to sliderbar data
            $('#user_content_type').val(messages_bot_type);
            //set name user message to right slidebar
            messages_focus.val(user_message_name);
            filter_messages_focus.val(filter_message_name);
            //set flag icheck_change_event do not icheck change event handle below action
            icheck_change_event = false;
            checkContentUserMessage(messages_bot_type);
            setUserDataToForm();
            icheck_change_event = true;
        // }
    });

    ///////////////////////START ACTION USER MESAGE
    //TEXT
    $(document).on('input change', '.message_user_area .text_group textarea', function (event) {
        getDataUserFromInput();
    });

    ///////////////////////START ACTION BOT MESAGE
    //get type of bot input textarea
    $('.scenario-body').on('click', '.bot_scenario', function (event) {
        message_type_bot = true;
        var messages_focus      = $('input.messages_focus'),
            filter_messages_focus = $('input.filter_messages_focus'),
            btn_next_messages_focus = $('input.btn_next_messages_focus'),
            input_requiment_messages_focus = $('input.input_requiment_messages_focus'),
            bot_message_name    = $(this).find('.messages_bot_content').attr('name'),
            filter_message_name    = $(this).find('.filter_content ').attr('name'),
            btn_next_message_name    = $(this).find('.btn_next_content ').attr('name'),
            input_requiment_name    = $(this).find('.input_requiment_content').attr('name');

        // if (messages_focus.val() != bot_message_name) {
            $('.message_bot_area  .footer_message_input .carousel_slide').hide();
            //set active for bot scenario
            $('.scenario-body .bot_scenario, .scenario-body .user_scenario').removeClass('active');
            $(this).addClass('active');

            var messages_bot_type = $(this).find('input.messages_bot_type').val();

            //get type from bot message and set to sliderbar data
            $('#bot_content_type').val(messages_bot_type);

            //set name bot message to right slidebar
            messages_focus.val(bot_message_name);
            filter_messages_focus.val(filter_message_name);
            btn_next_messages_focus.val(btn_next_message_name);
            input_requiment_messages_focus.val(input_requiment_name);

            // checkContentBotMessage(messages_bot_type);
            checkContentBotMessage();
            setBotDataToForm();
            //show label empty if not exist any msg
            checkTotalMessageInBlock();

            var variable_input = $('.message_container_input .type_group').find('input.btn_is_variable');
            initIcheck(variable_input);
            // checkbox is required
            var is_required_input = $('.message_container_input .type_group').find('input.btn_is_required');
            initIcheck(is_required_input);
            // checkbox is weekend off in calendar group
            var is_weekend_off = $('.message_container_input .calendar_group').find('input.btn_is_weekend_off');
            initIcheck(is_weekend_off);

        checkUseRequiment();
        setHeightSlidebar();
        // }
    });

    //Change select content type
    $('.message_bot_area .add_msg_type').on('click', function (event) {
        var bot_message_current         = botMessageElement(),
            bot_message_current_type    = bot_message_current.next('input.messages_bot_type'),
            bot_content_type            = $('.message_bot_area #bot_content_type').val();

        //check text type and clear content bot message
        var old_type = bot_message_current_type.val();
        if(old_type != bot_content_type) {
            bot_message_current.val('');
        }
        //change type for bot message
        bot_message_current_type.val(bot_content_type);
        //re-set type follow active group message
        botMessageType(bot_content_type);

        setDataMediaDemo(true);
        checkContentBotInput(bot_content_type);
        checkUseRequiment();
        //show label empty if not exist any msg
        checkTotalMessageInBlock();
    });
    //Change select content type
    $('#user_content_type').on('change', function (event) {
        var user_current        = userMessageElement(),
            user_current_type   = user_current.next('input.messages_bot_type'),
            user_input_text     = userInputText();
        //change type for bot message
        user_current_type.val($(this).val());
        //check text type and clear content user message
        user_current.val('');
        user_input_text.importTags('');
        userMessageType($(this).val());
        setUserDataMediaDemo(true);
        //set flag icheck_change_event do not icheck change event handle below action
        icheck_change_event = false;
        checkContentUserMessage($(this).val());
        getDataUserFromInput();

        icheck_change_event = true;
        // createTag();
    });

    //event add user + bot
    $('.add_pattern_user').on('click', function (event) {
        var message_position = getMessagePosition(this);
        cloneUser(message_position);
    });
    //event add bot
    $('.add_pattern_bot').on('click', function (event) {
        var message_position = getMessagePosition(this);
        cloneBot(message_position);
    });
    //event slide carousel
    $('.carousel_slide .left').click(function() {
        $('.generic_group.active .generic_container').carousel('prev');
        checkCarouselSlide();
    });

    $('.carousel_slide .right').click(function() {
        $('.generic_group.active .generic_container').carousel('next');
        checkCarouselSlide();
    });
    $('.generic_carousel').bind('slid.bs.carousel', function (e) {
        $('.generic_container').carousel('pause');
    });
    //delete group carousel
    $(document).on("click", '.generic_group .group_delete_btn', function (event) {
        $(this).parents('.generic_group').remove();
        $('.message_bot_area .generic_indicators li').remove();
        // $('.message_bot_area .footer_message_input .carousel_slide').hide();
        checkCarouselSlide();
        getDataFromInput();
    });
    //set active for type_group when click
    $(document).on('click', '.message_bot_area .type_group, .message_user_area .type_group', function (event) {
        $('.message_bot_area .type_group, .message_user_area .type_group').removeClass('active');
        $(this).addClass('active');

        //re-set type follow active group message
        botMessageType($(this).data('type'));
        // show/hide button add item
        check_add_item_common();
        if(message_type_bot){
            setValMessageType($(this).data('type'));
            var type  = common_data['bot_content_type'];
            if ($(this).data('type') == type[type_carousel]) {
                checkCarouselSlide();
            }
        }
    });

    // add button item common
   /* $('.message_container_input .footer_message_input .add_item_common').on('click', function (event) {
        var bot_message_type    = botMessageType(),
            type                = common_data['bot_content_type'];

        if(bot_message_type == type[type_radio]) {
            var msg_active = getMessageFormActive(),
                template_radio = common_data['template_radio'];
            if (msg_active.find('.radio_type').val() == template_radio[type_image_radio]) {
                addRadioImage();
            }else {
                addRadioBox();
            }
        } else if(bot_message_type == type[type_checkbox]) {
            addCheckboxBox();
        }else if(bot_message_type == type[type_pulldown]) {
            addItemPulldown();
        }else if(bot_message_type == type[type_terms_of_use]) {
            addItemLink();
        }
    });*/

    //*** AJAX ON ***
    // ajax update scenario
    $('.updateScenario').on('click', function (event) {
        // validateMessages();
        createPreview();
        updateScenario();
    });
    //MOVE ITEM
    $('.fixedsidebar-content').sortable({
        items: ".type_group",
        stop: function(event, ui) {
            getDataFromInput();
        }
    });
    // variable change
    $(document).on("change", '.type_group .button_variable', function (event) {
        getDataFromInput();
    });
    //-----------------start set value from right sliderbar to bot message
    //GROUP TYPE MESSAGE
    $(document).on("click", '.type_group .group_delete_btn', function (event) {
        $(this).parents('.type_group').remove();
        checkUseRequiment();
        getDataFromInput();
        //show label empty if not exist any msg
        checkTotalMessageInBlock();
    });
    //change preview captcha
    $(document).on("change", '.captcha_group .captcha_type, .captcha_group .captcha_color', function (event) {
        var parent = $(this).closest('.captcha_group');
        previewCaptcha(parent);
    });
    $(document).on('input change', '.captcha_group .size', function (event) {
        if(event.type == 'input') {
            var parent = $(this).closest('.captcha_group');
            previewCaptcha(parent);
        }
    });
    //CHECKBOX FOR USE VARIABLE
    $(document).on('ifChanged', '.type_group input.btn_is_variable', function (event) {
        if(icheck_change_event) {
            var group = $(this).closest('.type_group'),
                is_variable = group.find('input.btn_is_variable:checked').val(),
                variable    = group.find('div.variable_group'),
                messages_user_variable = group.find('input.messages_user_variable').val();
            if(is_variable != void 0 && is_variable) {
                variable.removeClass('hidden');
            } else {
                variable.addClass('hidden');
            }
            getDataFromInput();

        }
    });
    //CHECKBOX FOR CARD PAYMENT TYPE
    $(document).on('ifChanged', '.type_group input.btn_is_card_type', function (event) {
        if(icheck_change_event) {
            var group = $(this).closest('.card_type'),
                is_card_type = group.find('input.btn_is_card_type:checked');
            if(is_card_type != void 0 && is_card_type.length){
                getDataFromInput();
            }
        }
    });
    //CHECKBOX INPUT REQUIMENT
    $(document).on('ifChanged', '.checkbox_requirement_one input.btn_is_input_requiment', function (event) {
        if(icheck_change_event) {
            checkActiveRequirementOne();
            getDataFromInput();
        }
    });

    $(document).on('click', '.type_group .img_card_type', function (event) {
        var card_type_item = $(this).parents('.card_type_item');
        if(card_type_item.length){
            icheckAuto('toggle', card_type_item.find('.btn_is_card_type'));
        }
        getDataFromInput();
    });

    //CHECKBOX FOR REQUIRED
    $(document).on('ifChanged', '.type_group input.btn_is_required', function (event) {
        if(icheck_change_event) {
            getDataFromInput();
        }
        if ($(this).closest('.type_group').attr('data-type') == common_data['bot_content_type'][type_carousel]) {
            check_add_item_common();
        }
    });
    //CHECKBOX WEEKEND OFF FOR CALENDAR GROUP
    $(document).on('ifChanged', '.calendar_group input.btn_is_weekend_off', function (event) {
        if(icheck_change_event) {
            getDataFromInput();
        }
        // if ($(this).closest('.type_group').attr('data-type') == common_data['bot_content_type'][type_carousel]) {
        //     check_add_item_common();
        // }
    });
    //SELECT VALIDATION IN TYPE INPUT TEXT
    /*$(document).on('ifChanged', '.type_group input.btn_validation', function (event) {
        if(icheck_change_event) {
            getDataFromInput();
        }
    });*/

    //lABEL IN GROUP MESSAGE
    $(document).on("change", '.type_group .input_label_type', function (event) {
        if($(this).val() ==  no_label) {
            $(this).parents('.type_group').find('.input_label').addClass('hide').val('');
        }else {
            $(this).parents('.type_group').find('.input_label').removeClass('hide');
        }
        getDataFromInput();
    });
    // TEMPLATE TYPE IN TEXTAREA GROUP MESSAGE
    $(document).on("change", '.type_group .textarea_type', function (event) {
        if($(this).val() ==  type_textarea_input) {
            $(this).parents('.type_group').find('.use_variable').removeClass('hide');
            $(this).parents('.type_group').find('.is_required').removeClass('hide');
            $(this).parents('.type_group').find('textarea.input_text_type').removeClass('readonly').addClass('placeholder');
            $(this).parents('.type_group').find('textarea.input_text_type').textcomplete('destroy');
            $(this).parents('.type_group').find('.delete_textarea_box').addClass('hide');
        }else {
            $(this).parents('.type_group').find('.use_variable').addClass('hide');
            $(this).parents('.type_group').find('.is_required').addClass('hide');
            $(this).parents('.type_group').find('textarea.input_text_type').removeClass('placeholder').addClass('readonly');
            $(this).parents('.type_group').find('.delete_textarea_box').removeClass('hide');
            messageTextComplete();
        }
        getDataFromInput();
    });
    // TERM OF USE TYPE IN TERM OF USE GROUP MESSAGE
    $(document).on("change", '.type_group .term_of_use_type', function (event) {
        var template_term_of_use = common_data['template_term_of_use'];
        if($(this).val() ==  template_term_of_use[type_detail_content]) {
            $(this).parents('.type_group').find('.link_input').addClass('hide');
            $(this).parents('.type_group').find('.add_link_input').addClass('hide');
            $(this).parents('.type_group').find('.textarea_input').removeClass('hide');
        }else {
            $(this).parents('.type_group').find('.textarea_input').addClass('hide');
            $(this).parents('.type_group').find('.link_input').removeClass('hide');
            $(this).parents('.type_group').find('.add_link_input').removeClass('hide');
            checkItemLink();
        }
        check_add_item_common();
        getDataFromInput();
    });
    //RADIO, CHECKBOX
    $(document).on('input change', '.message_bot_area .fixedsidebar-content input, .message_bot_area .fixedsidebar-content textarea', function (event) {
        if(event.type == 'input') {
            getDataFromInput();
        }
    });
    $(document).on('change', '.message_bot_area .fixedsidebar-content select', function (event) {
        getDataFromInput();
    });
    $(document).on('input', '.filter_contain input', function (event) {
        if(event.type == 'input') {
            if(message_type_bot) {
                getDataFromInput();
            } else {
                getDataUserFromInput();
            }
        }
    });
    $(document).on('change', '.filter_contain select', function (event) {
        if(message_type_bot) {
            getDataFromInput();
        } else {
            getDataUserFromInput();
        }
    });
    //change text button next
    $(document).on('input change', '.fixedsidebar-content .text_button_next input.content', function (event) {
        if(event.type == 'input') {
            setTimeout(function (e) {
                getDataFromInput();
            }, 100);
        }
    });
    //select file for file type
    $('#scenario-file-list .btn-file-select').on('click', function () {
        var bot_message_type    = botMessageType(),
            type                = common_data['bot_message_type'],
            type_user           = common_data['bot_content_type'],
            file_path_input     = $('#scenario-file-list #scenario_file_selected'),
            file_path           = file_path_input.val();
        //set file url for image input
        if(file_path != '' && file_path != void 0) {
            // $('.file_group .file_url_input').val(file_path);
            // getDataUserFromInput();
            // //clear val file_path_input in popup file list
            // file_path_input.val('');
           if(bot_message_type == type_user[type_carousel]) {
                var genericActive = genericActiveElement();
                genericActive.find('input.image_url').val(file_path);
                getDataFromInput();
           }else if(bot_message_type == type_user[type_radio]) {
               var radio_image_content_active = $('.message_bot_area .radio_group .radio_box .generic_box_content.active');
               radio_image_content_active.find('input.image_url').val(file_path);
               getDataFromInput();
           }else {
               $('.file_group .file_url_input').val(file_path);
               getDataUserFromInput();
           }
            //clear val file_path_input in popup file list
            file_path_input.val('');
        }
    });
    /*add item filter*/
    $(document).on('click', '#btn_filter_add', function(evt) {
        addFilterItem();
        if(message_type_bot) {
            getDataFromInput();
        } else {
            getDataUserFromInput();
        }
    });
    /*delete item filter*/
    $(document).on('click', '.filter_item .filter_btn_delete', function(evt) {
        $(this).parents('.filter_item').remove();
        indexFilterInput();
        var parent = $('.filter_contain .filter_item').first();
        parent.find('.select_option').remove();
        parent.find('.field_filter_option').removeClass('hidden');

        // set height scroll filter containt
        var filter_group;
        if(message_type_bot) {
            getDataFromInput();
            filter_group = $('.message_bot_area .filter_group');
        } else {
            getDataUserFromInput();
            filter_group = $('.message_user_area .filter_group');
        }

        if(filter_group.find('.filter_contain .filter_item').length <= 2) {
            $(filter_group).find('.filter_contain').css('height', 'auto');
            $(filter_group).find('.slimScrollDiv').css('height', 'auto');
        }

        setHeightSlidebar();
    });
    // remove radio item, checkbox item, posstalcode item, pulldown item, terms of use item
    $(document).on("click", '.radio_box .delete_btn_box', function (event) {
        if($('.message_container_input .radio_group.active .radio_box').length > min_radio_item) {
            $(this).parents('.radio_box').remove();
            checkRadioBox();
            getDataFromInput();
        }
    });
    $(document).on("click", '.checkbox_box .delete_btn_box', function (event) {
        if($('.message_container_input .checkbox_group.active .checkbox_box').length > min_checkbox_item) {
            $(this).parents('.checkbox_box').remove();
            checkCheckboxBox();
            getDataFromInput();
        }
    });
    $(document).on("click", '.pulldown_item .item .delete_btn_box', function (event) {
        if($('.message_container_input .pulldown_group.active .pulldown_item .item').length > min_checkbox_item) {
            $(this).parents('.item').remove();
            checkPulldownBox();
            getDataFromInput();
        }
    });
    $(document).on("click", '.link_input .item_link .delete_btn_box', function (event) {
        if($('.message_container_input .terms_of_use_group.active .link_input .item_link').length > min_checkbox_item) {
            $(this).parents('.item_link').remove();
            checkItemLink();
            getDataFromInput();
        }
    });
    $(document).on("click", '.postal_code_box .delete_btn_box', function (event) {
        $(this).parents('.input_item').remove();
        getDataFromInput();
    });
    $(document).on("click", '.terms_of_use_group .delete_btn_box', function (event) {
        $(this).parents('.terms_of_use_group').remove();
        checkUseRequiment();
        getDataFromInput();
    });
    $(document).on("click", '.confirmation_group .delete_btn_box', function (event) {
        $(this).parents('.confirmation_group').remove();
        checkUseRequiment();
        getDataFromInput();
    });
    $(document).on("click", '.upload_file_group .delete_btn_box', function (event) {
        $(this).parents('.upload_file_group').remove();
        getDataFromInput();
    });
    $(document).on("click", '.label_group .delete_btn_box', function (event) {
        $(this).parents('.label_group').remove();
        getDataFromInput();
    });
    $(document).on("click", '.calendar_group .delete_btn_box', function (event) {
        $(this).parents('.calendar_group').remove();
        getDataFromInput();
    });
    // INPUT TYPE
    $(document).on("click", '.input_box .change_input', function (event) {
        inputGroupChangeNumber();
        getDataFromInput();
    });
    //pulldown custom type
    $(document).on("click", '.pulldown_group .change_number_pulldown', function (event) {
        var parent = $(this).closest('.pulldown_group');
        if ($(this).hasClass('one_pulldown')) {
            parent.find('.one_pulldown').addClass('hidden');
            parent.find('.two_pulldown').removeClass('hidden');
        } else {
            parent.find('.one_pulldown').removeClass('hidden');
            parent.find('.two_pulldown').addClass('hidden');
        }
        customPulldownChangeNumber($(this));
        // getDataFromInput();
    });
    //radio image type
    $(document).on("click", '.radio_group .change_number_image', function (event) {
        changeNumberImageRadio($(this));
    });
    //delete user message box
    $(document).on('click', '.user_scenario .delete_user_box', function (event) {
        $(this).parents('.user_scenario').remove();
        //if 2 element class action-group same then remove 1 class action-group
        deleteButtonAction();
        message_area_select(null);
        generateNameMessageBox();
        userInputText('');
    });

    //delete bot message box
    $(document).on('click','.bot_scenario .delete_bot_box',function(e){
        $(this).parents('.bot_scenario').remove();
        //if 2 element class action-group same then remove 1 class action-group
        deleteButtonAction();
        message_area_select(null);
        generateNameMessageBox();
        $('input.messages_focus').val('');
        $('input.filter_messages_focus').val('');
        $('input.btn_next_messages_focus').val('');
        $('input.input_requiment_messages_focus').val('');
    });

    //add a message block if not exist any user message and bot message
    if(!$('.scenario_block .user_scenario').length && !$('.scenario_block .bot_scenario').length) {
        $('.add_pattern_user').click();
    }
    // remove generic block
    $(document).on("click", '.generic_box .delete_generic_box', function (event) {
        if($('.generic_container .generic_box').length > 1) {
            $(this).parents('.generic_box').remove();
            //----Carousel process
            //delete generic indicators item of generic_box
            $('.generic_indicators li.active').remove();
            //re-set index for generic indicators
            $('.generic_indicators li').each(function (index, value) {
                $(this).attr('data-slide-to', index);
            });
            //set active for first indicators and first generic_box
            $('.generic_indicators li:first-child').addClass('active');
            $('.generic_container .generic_box:first-child').addClass('active');
            checkCarouselSlide();
            ////////////////////
            checkGenericBox();
            getDataFromInput();
            check_add_item_common();
        }
    });

    //re-sort position message box
    $('.scenario_block').sortable({
        items: ".message_box",
        // cancel: ".bot_media_demo, .user_media_demo",
        activate: function(event, ui) {

        },
        stop: function(event, ui) {
            //re-generate bot for message library
            generateNameMessageBox();
            //if 2 element class action-group same then remove 1 class action-group
            deleteButtonAction();
            //update name of message box focusing to messages_focus input
            var msg_focusing = getMessageBoxActive(),
                msg_name     = msg_focusing.find('textarea.messages_bot_content').attr('name'),
                filter_name     = msg_focusing.find('textarea.filter_content').attr('name'),
                btn_next_name   = msg_focusing.find('textarea.btn_next_content').attr('name'),
                input_requiment_name   = msg_focusing.find('input.input_requiment_content').attr('name');
            $('input.messages_focus').val(msg_name);
            $('input.filter_messages_focus').val(filter_name);
            $('input.btn_next_messages_focus').val(btn_next_name);
            $('input.input_requiment_messages_focus').val(input_requiment_name);
        }
    });

    //generate name for input message block
    generateNameMessageBox();
    //carousel slide when select generic message
    /*$('.message_bot_area .generic_carousel').bind('slid.bs.carousel', function (e) {
        checkCarouselSlide();
    });*/
    uploadFile();
    setHeightSlidebar();

    //disable submit message form
    $(".scenario-edit form.scenarioForm").submit(function (e) {
        e.preventDefault();
    });
});

function createPreview() {
    $(".scenario-edit .scenario_block .messages_bot_content").each(function(index, element) {
        var value = $(element).val();
        var bot_chat = null;
        if(value != void 0 && value != '' && $(element).parent().hasClass('bot_content_box')){
            var msg_data = JSON.parse(value);
            if(msg_data != void 0 && msg_data.message != void 0 && msg_data.message != ''){
                var msg = msg_data.message;
                bot_chat = $('.template-efo .item').clone();
                bot_chat = createBotPreview(msg, bot_chat);
                $(element).parents('.bot_form_box').find('.bot_media_demo .text_message').html(bot_chat.html());
            } else{
                $(element).parents('.bot_form_box').find('.bot_media_demo .text_message').html('');
            }
        }
    });
    initDatePicker();
    initSlick();
    resetSlickCarousel();
}

function createBotPreview(msg, element) {
    var item = null,
        bot_content_type = common_data['bot_content_type'],
        template_radio = common_data['template_radio'];
    for(var i = 0 ; i < msg.length; i++){
        item = $('.template-efo .item').clone();
        item.addClass('col-xs-12');
        var template_radio = common_data['template_radio'];
        if(msg[i].type != void 0 && (msg[i].type == common_data['bot_content_type']['radio'])){
            if(msg[i].template_type == template_radio[type_image_radio]){
                console.log('type_image_radio');
                item = viewEfoRadioImage(item, msg[i]);
            } else {
                item = viewEfoSelectBox(item, msg[i].type, msg[i]);
            }
        } else if(msg[i].type != void 0 && msg[i].type == bot_content_type['checkbox']){
            item = viewEfoSelectBox(item, msg[i].type, msg[i]);
        } else if(msg[i].type != void 0 && msg[i].type == bot_content_type['input']){
            item = viewInput(item, msg[i]);
        } else if(msg[i].type != void 0 && msg[i].type == bot_content_type['textarea']){
            item = viewTextarea(item, msg[i]);
        } else if(msg[i].type != void 0 && msg[i].type == bot_content_type['postal_code']){
            item = viewPostalCode(item, msg[i]);
        } else if(msg[i].type != void 0 && msg[i].type == bot_content_type['terms_of_use']){
            item = viewTermsUse(item, msg[i]);
        } else if(msg[i].type != void 0 && msg[i].type == bot_content_type['pulldown']){
            item = viewPullDown(item, msg[i]);
        } else if(msg[i].type != void 0 && msg[i].type == bot_content_type['text']){
            item = viewLabel(item, msg[i]);
        } else if(msg[i].type != void 0 && msg[i].type == bot_content_type['card_payment']){
            item = viewCardPayment(item, msg[i]);
        } else if(msg[i].type != void 0 && msg[i].type == bot_content_type['carousel']){
            item = viewCarousel(item, msg[i]);
        } else if(msg[i].type != void 0 && msg[i].type == bot_content_type['file']){
            item = viewFile(item, msg[i]);
        } else if(msg[i].type != void 0 && msg[i].type == bot_content_type['calendar']){
            item = viewCalendar(item, msg[i]);
        } else if(msg[i].type != void 0 && msg[i].type == bot_content_type['captcha']){
            item = viewCaptcha(item, msg[i]);
        }
        $(element).append(item);
    }
    if(showHiddenButtonNext(msg)){
        var button_action = viewButton(element, false, 'btn-action');
        element.append(button_action);
    }
    return element;
}

/**
 * set data for common variable
 */
function setDataCommon(key, value) {
    common_data[key] = value;
}

//add new user box
function cloneUser(add_before_element) {
	var clone = $('.scenario_block_origin .user_scenario').clone();
	clone.find('.user_media_content').addClass('clone_user_media');
	if(add_before_element != void 0 && $(add_before_element).length > 0){
        $(add_before_element).before(clone);
    } else{
        $('.scenario_block').append(clone);
    }
    generateNameMessageBox();
    //focus to new user box
    clone.click();
}

//add new bot box
function cloneBot(add_before_element) {
	var clone = $('.scenario_block_origin .bot_scenario').clone();
    if(add_before_element != void 0 && $(add_before_element).length > 0){
        $(add_before_element).before(clone);
    } else{
        $('.scenario_block').append(clone);
    }
    $('.message_bot_area  .footer_message_input .carousel_slide').hide();
    //reset name for mesage input
    generateNameMessageBox();
    //focus to new bot box
    clone.click();
}


function uploadFile() {
    $(document).on('click', '.upload_image', function () {
        var box_message = $(this).parents('.file_select_box').find('.box_message'),
            allowdtypes = $(this).data('image_type'),
            max_size_image = 40, //Mb
            max_size_video = 40; //Mb

        allowdtypes = (allowdtypes != void 0 && allowdtypes != '') ? allowdtypes : 'jpeg,jpg,png,gif,mp4,pdf';
        allowdtypes = allowdtypes.split(',');
        // image: jpeg,jpg,png,gif
        // video: mp4
        // file: pdf

        $(this).fileupload({
            dropZone: $(this),
            add: function (e, data) {
                setMesssage(null);
                var data_file_upload = data.files[0];
                var error_name = '';
                var fileType = data_file_upload.name.split('.').pop().toLowerCase();
                var max_size_file = (fileType == 'mp4') ? max_size_video : max_size_image;

                if(allowdtypes.indexOf(fileType) < 0) {
                    error_name = $(this).data('error_type');
                    error_name = error_name.replace(':values', allowdtypes.join(', '));

                } else if (data_file_upload.size > max_size_file*1024*1024) {//Byte
                    error_name = $(this).data('error_size');
                    error_name = error_name.replace(':max', max_size_file*1024); //Kb
                }

                if(error_name != '') {
                    setMesssage(error_name, 1, box_message, true);
                }else{
                    data.submit();
                }
            },fail:function(e, data){
                var errors = $.parseJSON(data.jqXHR.responseText);
                if(errors.error != void 0 && errors.error != '') {
                    setMesssage(errors.error , 1, box_message, true);
                }
            },success:function (data) {
                if(data != void 0){
                    if(data.success) {
                        if(data.path_file != void 0){
                            $('#scenario-file-list #scenario_file_selected').val(data.path_file);
                            $('#scenario-file-list .btn-file-select').click();
                            setMesssage(data.success_mess, 2,box_message, true);
                        }
                    } else {
                        var message = '';
                        var errors = data.errors;
                        for(var key in errors){
                            message += errors[key]
                        }
                        setMesssage(message, 1, box_message, true);
                    }
                }
            }
        });
    });
}

/**
 * generate name for user and bot message input
 */
function generateNameMessageBox() {
    var position        = 0,
        message_type    = common_data['message_type'];
    //set index (position) for mesage block
    $('.scenario_block').children().each(function (index, element) {
        if($(this).hasClass('user_scenario')) {
            var prefix_user = 'message[' + message_type['bot'] + '][' + position + ']';
            $(this).find('.messages_bot_content').attr('name', prefix_user + '[content]');
            $(this).find('.filter_content').attr('name', prefix_user + '[filter]');
            $(this).find('.messages_bot_type').attr('name', prefix_user + '[type]');
            position++;
        }
        if($(this).hasClass('bot_scenario')) {
            var prefix_bot = 'message[' + message_type['user'] + '][' + position + ']';
            $(this).find('.messages_bot_content').attr('name', prefix_bot + '[content]');
            $(this).find('.filter_content').attr('name', prefix_bot + '[filter]');
            $(this).find('.btn_next_content').attr('name', prefix_bot + '[btn_next]');
            $(this).find('.input_requiment_content').attr('name', prefix_bot + '[input_requiment]');
            $(this).find('.messages_bot_type').attr('name', prefix_bot + '[type]');
            position++;
        }
    });
}
//hide, show by check number radio box
function checkRadioBox() {
    var group = $('.message_container_input .radio_group.active');
    if(group.length) {
        var radio_box_count = group.find('.radio_box').length,
            delete_button   = group.find('.radio_box .delete_btn_box');
        var template_radio = common_data['template_radio'];
        if(radio_box_count < min_radio_item) {
            if (group.find('.radio_type').val() == template_radio[type_image_radio]) {
                addRadioImage();
            }else {
                addRadioBox();
            }
        } else {
            //show, hide delete button box
            if(radio_box_count <= min_radio_item) {
                delete_button.hide();
            } else {
                delete_button.show();
            }
            check_add_item_common();
        }
    }
}

//hide, show by check number checkbox box
function checkCheckboxBox() {
    var group = $('.message_container_input .checkbox_group.active');
    if(group.length) {
        var checkbox_box_count = group.find('.checkbox_box').length,
            delete_button      = group.find('.checkbox_box .delete_btn_box');

        if(checkbox_box_count < min_checkbox_item) {
            addCheckboxBox();
        } else {
            //show, hide delete item box
            if(checkbox_box_count <= min_checkbox_item) {
                delete_button.hide();
            } else {
                delete_button.show();
            }
            check_add_item_common();
        }
    }
}
//hide, show by link number term of use box
function checkItemLink() {
    var group = $('.message_container_input .terms_of_use_group.active');
    if(group.length) {
        var link_box_count = group.find('.item_link').length,
            delete_button      = group.find('.item_link .delete_btn_box');

        if(link_box_count < min_checkbox_item) {
            addItemLink();
        } else {
            //show, hide delete item box
            if(link_box_count <= min_checkbox_item) {
                delete_button.hide();
            } else {
                delete_button.show();
            }
            // check_add_item_common();
        }
    }
}
//hide, show by check number checkbox box
function checkPulldownBox() {
    var group = $('.message_container_input .pulldown_group.active');
    if(group.length) {
        var pulldown_box_count = group.find('.pulldown_item .item').length,
            delete_button      = group.find('.pulldown_item .item .delete_btn_box');

        if(pulldown_box_count < min_checkbox_item) {
            addItemPulldown();
        } else {
            //show, hide delete item box
            if(pulldown_box_count <= min_checkbox_item) {
                delete_button.hide();
            } else {
                delete_button.show();
            }
        }
    }
}

//show, hide add new button for button type and generic type
function check_add_item_common() {
    var add_item_common     = $('.footer_message_input .add_item_common'),
        text_button_next    = $('.message_bot_area .fixedsidebar-content .text_button_next'),
        bot_message_type    = botMessageType(),
        type                = common_data['bot_content_type'],
        item_btn_count      = 0,
        max_num             = 1;
    add_item_common.hide();
    text_button_next.hide();
    if(bot_message_type == type[type_radio]) {
        item_btn_count         = -1;
    } else if(bot_message_type == type[type_checkbox]) {
        item_btn_count         = -1;
    }else if(bot_message_type == type[type_textarea] || bot_message_type == type[type_input] || bot_message_type == type[type_postal_code]
             || bot_message_type == type[type_confirmation] || bot_message_type == type[type_file]
             || bot_message_type == type[type_calendar] || bot_message_type == type[type_text] || bot_message_type == type[type_carousel]
             || bot_message_type == type[type_card_payment] || bot_message_type == type[type_captcha]){
        item_btn_count = 1;
    }else if ( bot_message_type == type[type_pulldown]) {
        item_btn_count = 1;
        var group = $('.message_container_input .pulldown_group.active');
        if(group.length) {
            var template_pulldown   = common_data['template_pulldown'];
            if (group.find('.pulldown_type').val() == template_pulldown[type_customize]) {
                item_btn_count = -1;
            }
        }
    }else if ( bot_message_type == type[type_terms_of_use]) {
        item_btn_count = 1;
        var group = $('.message_container_input .terms_of_use_group.active');
        if(group.length) {
            var template_term_of_use = common_data['template_term_of_use'];
            if (group.find('.term_of_use_type').val() != template_term_of_use[type_detail_content]) {
                item_btn_count = -1;
            }
        }
    }
    if(item_btn_count >= max_num) {
        add_item_common.hide();
    } else {
        add_item_common.show();
    }
    /*show/hide input text button next*/
    var number_group_message = $('.message_bot_area .fixedsidebar-content .type_group ').length;
    if (number_group_message == 0) {
        text_button_next.hide();
    }else if(number_group_message == 1 && bot_message_type == type[type_radio]) {
        text_button_next.hide();
    }else if (number_group_message == 1 && bot_message_type == type[type_carousel]) {
        var is_required =  $('.message_bot_area .fixedsidebar-content .type_group ').find('input.btn_is_required')[0].checked;
        if (is_required) {
            text_button_next.hide();
        }else {
            text_button_next.show();
        }
    }else {
        text_button_next.show();
    }
}

/**
 * set value user message from input text
 * @param value
 */
function changeDataUserMessage(value) {
    var bot_message = userMessageElement();
    bot_message.val(value);
}

/**
 * set value bot message from input text
 * @param value
 */
function changeDataBotMessage(value) {
    var bot_message = botMessageElement();
    bot_message.val(value);
}
function changeDataFilter(value) {
    var bot_message = filterElement();
    bot_message.val(value);
}
function changeDataBtnNext(value) {
    var bot_message = btnNextElement();
    bot_message.val(value);
}
function changeDataRequirementOne(value) {
    var bot_message = inputRequirementElement();
    bot_message.val(value);
}

/**
 * get message active by fucusing
 * @returns {*|jQuery|HTMLElement}
 */
function getMessageBoxActive() {
    return $('.scenario_block .message_box.active');
}

/**
 * get message form active
 * @returns {*|jQuery|HTMLElement}
 */
function getMessageFormActive() {
    return $('.message_container_input .type_group.active');
}


/**
 * return bot message content focusing
 * @returns {*}
 */
function botMessageContent() {
    var bot_input = botMessageElement();
    return bot_input.val();
}
function getFilterContent() {
    var bot_input = filterElement();
    return bot_input.val();
}
function getBtnNext() {
    var bot_input = btnNextElement();
    return bot_input.val();
}
function getRequirementOne() {
    var bot_input = inputRequirementElement();
    return bot_input.val();
}

/**
 * return bot message type element
 * @returns {*}
 */
function botMessageType(value) {
    var bot_box  = botMessageBoxElement(),
        bot_type = bot_box.find('input.messages_bot_type');
    if(value != void 0) {
        bot_type.val(value);
    }
    return bot_type.val();
}

/**
 * return bot message element
 */
function botMessageElement() {
    return $(".bot_scenario textarea[name='" + $('input.messages_focus').val() + "']");
}

//return filter data
function filterElement() {
    if(message_type_bot) {
        return $(".bot_scenario textarea[name='" + $('input.filter_messages_focus').val() + "']");
    } else {
        return $(".user_scenario textarea[name='" + $('input.filter_messages_focus').val() + "']");
    }
}
//return btn next data
function btnNextElement() {
    if(message_type_bot) {
        return $(".bot_scenario textarea[name='" + $('input.btn_next_messages_focus').val() + "']");
    } else {
        return $(".user_scenario textarea[name='" + $('input.btn_next_messages_focus').val() + "']");
    }
}
//return input requiment flg data
function inputRequirementElement() {
    if(message_type_bot) {
        return $(".bot_scenario input[name='" + $('input.input_requiment_messages_focus').val() + "']");
    }
}

/**
 * return bot message box
 */
function botMessageBoxElement() {
    var bot_input = botMessageElement();
    return bot_input.parents('.bot_form_box');
}

/**
 * change bot text type input
 * @returns {*|jQuery|HTMLElement}
 */
function userInputText(value) {
    var input = $('.message_user_area .text_group .input_text_type');
    if(value != void 0) {
        input.val(value);
    }
    return input;
}

/**
 * return user message content focusing
 * @returns {*}
 */
function userMessageContent() {
    var user_input = userMessageElement();
    return user_input.val();
}

/**
 * return user message type element
 * @returns {*}
 */
function userMessageType(value) {
    var user_message = userMessageBoxElement(),
        user_type    = user_message.find('input.messages_bot_type');

    if(value != void 0) {
        user_type.val(value);
    }
    return user_type.val();
}

/**
 * return user message element
 */
function userMessageElement() {
    return $(".user_scenario textarea[name='" + $('input.messages_focus').val() + "']");
}

/**
 * return user message box
 */
function userMessageBoxElement() {
    var user_message = userMessageElement();
    return user_message.parents('.user_form_box');
}

/**
 * return variable textarea type
 * @returns {*}
 */
function textareaVariableElement() {
    var msg_active = getMessageFormActive();
    return msg_active.find('input.btn_is_variable');
}
function textRequired() {
    var msg_active = getMessageFormActive();
    return msg_active.find('input.btn_is_required');
}
function weekendOff() {
    var msg_active = getMessageFormActive();
    return msg_active.find('input.btn_is_weekend_off');
}

/**
 * check or uncheck library item
 * @param event
 * @param element
 * @returns {*|jQuery|HTMLElement}
 */
function icheckAuto(event, element) {
    if(element != void 0 && element != '') {
        if(event == 'check') {
            element.iCheck('check');
        } else if(event == 'uncheck') {
            element.iCheck('uncheck');
        } else{
            element.iCheck(event);
        }

    }
}

/**
 * add new radio block
 */
function addRadioBox() {
    // var clone = $('.scenario_block_origin .radio_box').clone();
    var clone = $('.scenario_block_origin .radio_text').clone();
    $('.message_container_input .radio_group.active .radio_container').append(clone);
    checkRadioBox();
}
/**
 * add new radio image
 */
function addRadioImage() {
    var clone = $('.scenario_block_origin .radio_image').clone();
    $('.message_container_input .radio_group.active .radio_container').append(clone);
    checkRadioBox();
}
/**
 * add new checkbox block
 */
function addCheckboxBox() {
    var clone = $('.scenario_block_origin .checkbox_box').clone();
    $('.message_container_input .checkbox_group.active .checkbox_container').append(clone);

    checkCheckboxBox();
}
/**
 * add new item pulldown
 */
function addItemPulldown() {
    // var clone = $('.scenario_block_origin .pulldown_box_child .customize_pulldown_item').clone();
    var parent = $('.message_container_input .pulldown_group.active .pulldown_item'),
        clone;
    if (!parent.find('.item').length) {
        clone = $('.scenario_block_origin .pulldown_box_child .customize_one_pulldown_item').clone();
    }else {
        if (parent.find('.item').hasClass('customize_one_pulldown_item')) {
            clone = $('.scenario_block_origin .pulldown_box_child .customize_one_pulldown_item').clone();
        }else {
            clone = $('.scenario_block_origin .pulldown_box_child .customize_two_pulldown_item').clone();
        }
    }
    // $('.message_container_input .pulldown_group.active .pulldown_item').append(clone);
    clone.insertBefore($('.message_bot_area .pulldown_group.active .pulldown_item .add_item_box'));
    checkPulldownBox();
}

function addItemLink() {
    //item_link
    var clone = $('.scenario_block_origin .item_link').clone();
    $('.message_container_input .terms_of_use_group.active .link_input').append(clone);

    checkItemLink();
}

/**
 * show, hide box message and button preview
 */
function checkViewMediaDemo() {
    var bot_box  = botMessageBoxElement();

    //show if is not api or scenario connect type
    var preview = bot_box.find('.preview');
    preview.removeClass('hidden');
}

/**
 * get data from slidebar input for message box by type
 * @param type
 */
function getDataUserFromInput() {
    getDataUserMessage();
    setUserDataMediaDemo();
}

/**
 * get data from slidebar input for message box by type
 * @param type
 */
function getDataFromInput() {
    getDataBotMessage();
    setDataMediaDemo(true);
}

function setActiveLastGroupMsg(is_user) {
    $('.message_bot_area .type_group, .message_user_area .type_group').removeClass('active');
    var bot_message_current         = botMessageElement(),
        bot_message_current_type    = bot_message_current.parents('.message_box').find('input.messages_bot_type'),
        group_last = $('.message_bot_area .type_group').last();

    if(is_user != void 0 && is_user) {
        group_last = $('.message_user_area .type_group').last();
    }
    if(group_last.length) {
        group_last.addClass('active');
        //set msg type group to type in center view
        bot_message_current_type.val(group_last.data('type'));
        setValMessageType(group_last.data('type'));
    }
}

/**
 * get text data to json and fill to message box
 */
function getDataUserMessage() {
    var type  = common_data['bot_message_type'];
    var data = {
        'message': []
    };
    // get filter data
    var filter_data = getFilterData(true);

    //loop radio item
    $('.message_container_input .message_user_area .fixedsidebar-content .type_group').each(function (index, msg_group) {
        var msg_group_type  = $(msg_group).data('type');
        if(msg_group_type != void 0) {
            var msg_group_data = {
                'type': msg_group_type
            };
            switch (msg_group_type) {
                case type[type_text]: {
                    var text_input = userInputText(),
                        content = '';

                    if (text_input != void 0) {
                        content = text_input.val().trim();
                    }
                    msg_group_data['content'] = content;
                }
                    break;
                case type[type_file]: {
                    var file_url    = $(msg_group).find('.file_url_input').val(),
                        file_url    = $.trim(file_url);
                    var file_type   = checkTypeFile(file_url);
                    if(file_type) {
                        msg_group_data['file_type'] =  file_type;
                        msg_group_data['url'] =  file_url;
                    }
                }
                    break;
                case type[type_mail]: {
                    var mail_val = mailElement();
                    msg_group_data['mail'] =  mail_val.val();
                }
                    break;
                case type[type_scenario_connect]: {
                    var scenario_val = scenarioElement();
                        // data_scenario_connect = {
                        //     'scenario' : scenario_val.val()
                        // };
                    msg_group_data['scenario'] = scenario_val.val();
                }
                    break;
            }
            data['message'].push(msg_group_data);
        }
    });

    data = JSON.stringify(data);
    changeDataUserMessage(data);
    if (filter_data != undefined && filter_data.length) {
        filter_data = JSON.stringify(filter_data);
    } else {
        filter_data = '';
    }
    changeDataFilter(filter_data);
}

function mailElement(value) {
    var mail_select = $('.message_user_area .mail_group select.mail_select');
    if(value != void 0 && value != '') {
        return mail_select.val(value).trigger('change.select2');
    }
    return mail_select;
}
function scenarioElement(value) {
    var scenario_select = $('.message_user_area .scenario_connect_group select.scenario_select');
    if(value != void 0 && value != '') {
        return scenario_select.val(value).trigger('change.select2');
    }
    return scenario_select;
}
/**
 * get text data to json and fill to message box
 */
function getDataBotMessage() {
    var type  = common_data['bot_content_type'];
    var data = {
        'message': []
    };
    // get filter data
    var filter_data = getFilterData(false);

    //loop radio item
    $('.message_container_input .message_bot_area .fixedsidebar-content .type_group').each(function (index, msg_group) {
        var msg_group_type  = $(msg_group).data('type');
        if(msg_group_type != void 0) {
            var msg_group_data = {
                'type': msg_group_type
            };
            var label_list = [];

            switch (msg_group_type) {
                case type[type_text]: {
                    // var text_input = userInputText(),
                    //     content = '';
                    //
                    // if (text_input != void 0) {
                    //     content = text_input.val();
                    // }
                    var content = $(msg_group).find('.input_text_type').val().trim();
                    msg_group_data['content'] = (content != '') ? content : '';

                }
                break;
                case type[type_radio]: {
                    var template_radio = common_data['template_radio'];
                    var title_flg = $(msg_group).find('.input_label_type').val();
                    var title = "";
                    var template_type = $(msg_group).find('.radio_type').val();
                    if (title_flg != no_label) {
                        title = $(msg_group).find('.title').val().trim();
                    }
                    if (template_type == template_radio[type_image_radio]) {

                        if($(msg_group).find('.radio_box') != void 0 && $(msg_group).find('.radio_box').length) {
                            $(msg_group).find('.radio_box').each(function (i, e) {
                                var image_item = [];
                                if ($(e).find('.generic_box_content') != void 0 && $(e).find('.generic_box_content').length) {
                                    $(e).find('.generic_box_content').each(function (ind, item) {
                                        var image_url = $(item).find('input.image_url').val(),
                                            comment = $(item).find('input.title').val();
                                        image_item.push({
                                            'image_url' : image_url,
                                            'comment' : comment
                                        });
                                    });
                                }
                                //push to radio label list
                                label_list.push(image_item);
                            });
                        }
                    } else {
                        //loop radio item
                        if($(msg_group).find('.radio_box') != void 0 && $(msg_group).find('.radio_box').length) {
                            $(msg_group).find('.radio_box').each(function (i, e) {
                                var label     = $(e).find('input.radio_label').val();
                                //push to radio label list
                                label_list.push(label);
                            });
                        }
                    }

                    msg_group_data['title_flg'] = title_flg;
                    msg_group_data['title'] = title;
                    msg_group_data['template_type'] = template_type;
                    msg_group_data['list'] = label_list;
                }
                break;
                case type[type_checkbox]: {
                    var title_flg = $(msg_group).find('.input_label_type').val();
                    var title = "";
                    if (title_flg != no_label) {
                        title = $(msg_group).find('.title').val().trim();
                    }
                    //loop checkbox item
                    if($(msg_group).find('.checkbox_box') != void 0 && $(msg_group).find('.checkbox_box').length) {
                        $(msg_group).find('.checkbox_box').each(function (i, e) {
                            var label     = $(e).find('input.checkbox_label').val();
                            //push to checkbox label list
                            label_list.push(label);
                        });
                    }

                    msg_group_data['title_flg'] = title_flg;
                    msg_group_data['title'] = title;
                    msg_group_data['list'] = label_list;
                }
                break;
                case type[type_textarea]: {
                    var title_flg = $(msg_group).find('.input_label_type').val();
                    var title = "";
                    if (title_flg != no_label) {
                        title = $(msg_group).find('.title').val().trim();
                    }
                    var template_type = $(msg_group).find('.textarea_type').val();
                    var content = $(msg_group).find('.input_text_type').val() != undefined ? $(msg_group).find('.input_text_type').val().trim() : '';

                    msg_group_data['template_type'] = input_type;
                    msg_group_data['title_flg'] = title_flg;
                    msg_group_data['title'] = title;
                    msg_group_data['list'] = label_list;
                    msg_group_data['title_flg'] = title_flg;
                    msg_group_data['template_type'] = template_type;
                    msg_group_data['title'] = title;
                    msg_group_data['placeholder'] = content;
                }
                break;
                case type[type_input]: {
                    var template_input = common_data['template_input'];
                    var title_flg = $(msg_group).find('.input_label_type').val();
                    var title = "";
                    if (title_flg != no_label) {
                        title = $(msg_group).find('.title').val().trim();
                    }
                    var input_type = $(msg_group).find('.input_type').val();
                    if($(msg_group).find('.field_input').length) {
                        $(msg_group).find('.field_input input.content').each(function (i, e) {
                            var placeholder = $(e).val();
                            //push to list
                            label_list.push({
                                'placeholder' : placeholder
                            });
                        });
                    }
                    /*validation type text*/
                    if (input_type == template_input[type_text]) {
                        var validation_type = $(msg_group).find('select.validation_type').val(),
                            min_length = $(msg_group).find('#min_length').val(),
                            max_length = $(msg_group).find('#max_length').val();
                        if(validation_type != void 0 && validation_type != '') {
                            msg_group_data['validation_type'] = validation_type;
                        }
                        if(min_length != void 0 && min_length != '' || max_length != void 0 && max_length != '') {
                            msg_group_data['min_length'] = min_length;
                            msg_group_data['max_length'] = max_length;
                        }
                    }
                    /*tel input type*/
                    if (input_type == template_input[type_tel]) {
                        var tel_input_type = $(msg_group).find('select.tel_input_type_select').val();
                        if(tel_input_type != void 0 && tel_input_type != '') {
                            msg_group_data['tel_input_type'] = tel_input_type;
                        }
                    }
                    /*min max lengh type password*/
                    if (input_type == template_input[type_password] || input_type == template_input[type_password_confirm]) {
                        var min_length = $(msg_group).find('#min_length').val(),
                            max_length = $(msg_group).find('#max_length').val();
                        if(min_length != void 0 && min_length != '' || max_length != void 0 && max_length != '') {
                            msg_group_data['min_length'] = min_length;
                            msg_group_data['max_length'] = max_length;
                        }
                    }
                    msg_group_data['template_type'] = input_type;
                    msg_group_data['title_flg'] = title_flg;
                    msg_group_data['title'] = title;
                    msg_group_data['list'] = label_list;
                }
                    break;
                case type[type_postal_code]: {
                    if($(msg_group).find('.input_item').length) {
                        $(msg_group).find('.input_item').each(function (i, e) {
                            var type = $(e).find('.title').val(),
                            placeholder = $(e).find('input.content').val();
                            //push to list
                            label_list.push({
                                'type' : type,
                                'placeholder' : placeholder
                            });
                        });
                    }
                    msg_group_data['list'] = label_list;
                }
                    break;
                case type[type_pulldown]: {
                    var template_pulldown   = common_data['template_pulldown'];
                    var title_flg = $(msg_group).find('.input_label_type').val();
                    var title = "";
                    var template_type = $(msg_group).find('.pulldown_type').val();

                    if (title_flg != no_label) {
                        title = $(msg_group).find('.title').val().trim();
                    }
                    switch (template_type){
                        case template_pulldown[type_customize]:
                            //comment pulldown
                            var first_title = $(msg_group).find('.customize .pulldown_first_title').val(),
                                last_title = $(msg_group).find('.customize .pulldown_last_title').val();
                            //loop pulldown item
                            if($(msg_group).find('.pulldown_item .item') != void 0 && $(msg_group).find('.pulldown_item .item').length) {
                                if ($(msg_group).find('.pulldown_item .item').hasClass('customize_one_pulldown_item')) {
                                    var pulldown_list = [];
                                    $(msg_group).find('.pulldown_item .item').each(function (i, e) {
                                        var label     = $(e).find('input.pulldown_label').val();
                                        pulldown_list.push(label);
                                        //push to pulldown label list
                                        // label_list.push(label);
                                    });
                                    label_list.push(pulldown_list);
                                }else {
                                    var first_pulldown_list = [],
                                        last_pulldown_list = [];
                                    $(msg_group).find('.pulldown_item .item').each(function (i, e) {
                                        var label_first_pulldown = $(e).find('.first_pulldown input.pulldown_label').val(),
                                            label_last_pulldown  = $(e).find('.last_pulldown input.pulldown_label').val();
                                        first_pulldown_list.push(label_first_pulldown);
                                        last_pulldown_list.push(label_last_pulldown);
                                        //push to pulldown label list
                                        // label_list.push(label);
                                    });
                                    label_list.push(first_pulldown_list);
                                    label_list.push(last_pulldown_list);
                                }
                                /*$(msg_group).find('.pulldown_item .item').each(function (i, e) {
                                    var label     = $(e).find('input.pulldown_label').val();
                                    //push to pulldown label list
                                    label_list.push(label);
                                });*/
                            }
                            msg_group_data['first_title'] = first_title;
                            msg_group_data['last_title'] = last_title;
                            break;
                        case template_pulldown[type_time]:
                            msg_group_data['spacing_minute'] = $(msg_group).find('.spacing_minute').val();
                            break;
                        case template_pulldown[type_date_time]:
                            msg_group_data['spacing_minute'] = $(msg_group).find('.spacing_minute').val();
                            break;
                        case template_pulldown[type_period_of_time]:
                            msg_group_data['start_spacing_minute'] = $(msg_group).find('.start_time .spacing_minute').val();
                            msg_group_data['end_spacing_minute'] = $(msg_group).find('.end_time .spacing_minute').val();
                            break;
                        case template_pulldown[type_provinces_of_japan]:
                            var label_contain = [];
                            $(msg_group).find('.default_pulldown').each(function (i, e) {
                                var label     = $(e).find('input.pulldown_label').val();
                                //push to pulldown label list
                                label_contain.push(label);
                            });
                            label_list.push(label_contain);
                            break;
                        case template_pulldown[type_towns_and_villages_of_japan]:
                            //comment pulldown
                            var first_title = $(msg_group).find('.towns_and_villages .pulldown_first_title').val(),
                                last_title = $(msg_group).find('.towns_and_villages .pulldown_last_title').val();

                            msg_group_data['first_title'] = first_title;
                            msg_group_data['last_title'] = last_title;
                            break;
                        default:
                            break;
                    }
                    msg_group_data['template_type'] = template_type;
                    msg_group_data['title_flg'] = title_flg;
                    msg_group_data['title'] = title;
                    msg_group_data['comment'] = $(msg_group).find('.comment').val();
                    msg_group_data['list'] = label_list;
                }
                    break;
                case type[type_terms_of_use]: {
                    var template_term_of_use = common_data['template_term_of_use'];
                    var title_flg = $(msg_group).find('.input_label_type').val();
                    var template_type = $(msg_group).find('.term_of_use_type').val();
                    var title = "";
                    if (title_flg != no_label) {
                        title = $(msg_group).find('.title').val().trim();
                    }
                    if (template_type == template_term_of_use[type_detail_content]) {
                        var content = $(msg_group).find('.input_text_type').val().trim();
                        msg_group_data['content'] = content;
                    } else {
                        if($(msg_group).find('.link_input .item_link') != void 0 && $(msg_group).find('.link_input .item_link').length) {
                            $(msg_group).find('.link_input .item_link').each(function (i, e) {
                                var link = $(e).find('.url').val(),
                                    link_first_title = $(e).find('.link_first_title').val(),
                                    link_last_title = $(e).find('.link_last_title').val(),
                                    link_text = $(e).find('.link_text').val();
                                label_list.push({
                                    'link_first_title' : link_first_title,
                                    'link_text' : link_text,
                                    'link' : link,
                                    'link_last_title' : link_last_title
                                });
                            });
                        }
                        msg_group_data['list'] = label_list;
                        // var link = $(msg_group).find('.link_input .url').val().trim(),
                        //     link_first_title = $(msg_group).find('.link_input .link_first_title').val(),
                        //     link_last_title = $(msg_group).find('.link_input .link_last_title').val(),
                        //     link_text = $(msg_group).find('.link_input .link_text').val();
                        // msg_group_data['link_first_title'] = link_first_title;
                        // msg_group_data['link_text'] = link_text;
                        // msg_group_data['link'] = link;
                        // msg_group_data['link_last_title'] = link_last_title;
                    }
                    var text_confirm = $(msg_group).find('.text_confirm_label').val();
                    msg_group_data['title_flg'] = title_flg;
                    msg_group_data['title'] = title;
                    msg_group_data['template_type'] = template_type;
                    msg_group_data['text_confirm'] = text_confirm;
                    msg_group_data['required_flg'] = 1;
                    messageTextComplete();
                }
                    break;
                case type[type_confirmation]: {
                    msg_group_data['required_flg'] = 1;
                }
                case type[type_file]: {
                    var file_type = $(msg_group).find('.file_type').val();
                    msg_group_data['file_type'] = file_type;
                    msg_group_data['required_flg'] = 1;
                }
                    break;
                case type[type_calendar]: {
                    var template_calendar = common_data['template_calendar'];
                    var title_flg = $(msg_group).find('.input_label_type').val();
                    var title = "";
                    if (title_flg != no_label) {
                        title = $(msg_group).find('.title').val().trim();
                    }
                    // is_weekend of flash
                    if ($(msg_group).find('.calendar_type').val() == template_calendar[type_calendar_select] && $(msg_group).find('input.btn_is_weekend_off').length) {
                        var weekend_off_flg = 0,
                            is_weekend_off = $(msg_group).find('input.btn_is_weekend_off')[0].checked;
                        var calendar_weekend_off = $(msg_group).find('.select_calendar input.datetimepicker');
                        if (calendar_weekend_off.data("DateTimePicker")){
                            calendar_weekend_off.data("DateTimePicker").destroy();
                        }
                        if (is_weekend_off) {
                            //disable weekend calendar
                            initCalendarWeekendOff(calendar_weekend_off, $(msg_group).find('.input_start_date').val());
                            weekend_off_flg = 1;
                        }else {
                            initDateTimePickerSelect($(msg_group).find('.input_start_date').val());
                        }
                        msg_group_data['weekend_off_flg'] = weekend_off_flg;
                    }
                    msg_group_data['template_type'] = $(msg_group).find('.calendar_type').val();
                    msg_group_data['start_date'] = $(msg_group).find('.input_start_date').val();
                    msg_group_data['title_flg'] = title_flg;
                    msg_group_data['title'] = title;
                }
                    break;
                case type[type_carousel]: {
                    var title_flg = $(msg_group).find('.input_label_type').val();
                    var title = "";
                    if (title_flg != no_label) {
                        title = $(msg_group).find('.title').val().trim();
                    }
                    //loop carousel item
                    if($(msg_group).find('.generic_box') != void 0 && $(msg_group).find('.generic_box').length) {
                        $(msg_group).find('.generic_box').each(function (i, e) {
                            var title     = $(this).find('input.title').val(),
                                sub_type  = $(this).find('input.sub_title').val(),
                                item_url  = $(this).find('input.item_url').val(),
                                image_url = $(this).find('input.image_url').val(),
                                button_title = $(this).find('input.button_title').val();
                            var carousel_data = {
                                'title'     : title,
                                'subtitle'  : sub_type,
                                'item_url'  : item_url,
                                'image_url' : image_url,
                                'button' : {
                                    'title' : button_title,
                                    'type' : 'select'
                                }
                            };
                            label_list.push(carousel_data);
                        });
                        msg_group_data['title_flg'] = title_flg;
                        msg_group_data['title'] = title;
                        msg_group_data['list'] = label_list;
                    }
                }
                    break;
                case type[type_card_payment]: {
                    var title_flg = $(msg_group).find('.input_label_type').val();
                    var title = "";
                    var card_type = [];

                    if (title_flg != no_label) {
                        title = $(msg_group).find('.title').val().trim();
                    }
                    if($(msg_group).find('.input_item').length) {
                         $(msg_group).find('.input_item').each(function (i, e) {
                             var type = $(e).find('.title').val(),
                             placeholder = $(e).find('input.content').val();
                             //push to list
                             label_list.push({
                                'type' : type,
                                'placeholder' : placeholder
                             });
                         });
                     }
                     if($(msg_group).find('.card_type input.btn_is_card_type').length) {
                         $(msg_group).find('.card_type input.btn_is_card_type').each(function (i, e) {
                             var is_checked = $(e).iCheck('update')[0].checked;
                             if(is_checked) {
                                 card_type.push($(e).val());
                             }
                         });
                     }
                     msg_group_data['list'] = label_list;
                    msg_group_data['title_flg'] = title_flg;
                    msg_group_data['title'] = title;
                    msg_group_data['card_type'] = card_type;
                }
                    break;
                case type[type_captcha]: {
                    var title_flg = $(msg_group).find('.input_label_type').val();
                    var title = "";
                    var card_type = [];

                    if (title_flg != no_label) {
                        title = $(msg_group).find('.title').val().trim();
                    }

                    msg_group_data['title_flg'] = title_flg;
                    msg_group_data['title'] = title;
                    msg_group_data['captcha_type'] = $(msg_group).find('.captcha_type').val();
                    msg_group_data['size'] = $(msg_group).find('.size').val();
                    msg_group_data['color'] = $(msg_group).find('.captcha_color').val();

                }
                    break;
                case type[type_add_to_cart]: {
                    var title_flg = $(msg_group).find('.input_label_type').val();
                    var title = "";
                    if (title_flg != no_label) {
                        title = $(msg_group).find('.title').val().trim();
                    }

                    msg_group_data['title_flg'] = title_flg;
                    msg_group_data['title'] = title;
                }
                    break;
                case type[type_request_document]: {
                    var title_flg = $(msg_group).find('.input_label_type').val();
                    var title = "";
                    if (title_flg != no_label) {
                        title = $(msg_group).find('.title').val().trim();
                    }

                    msg_group_data['title_flg'] = title_flg;
                    msg_group_data['title'] = title;
                    msg_group_data['template_type'] = $(msg_group).find('.document_type').val();
                }
                    break;
            }

            // set variable value
            // if (is_call_icheck) {
            if ($(msg_group).find('input.btn_is_variable').length) {
                var variable_id = '',
                    is_checked = $(msg_group).find('input.btn_is_variable')[0].checked;
                if (is_checked) {
                    variable_id = $(msg_group).find('select.button_variable').val();
                }
                if (msg_group_type != type[type_terms_of_use]) {
                    msg_group_data['variable_id'] = variable_id;
                }
            }
            var is_input_required = $('.message_bot_area .fixedsidebar-content .checkbox_requirement_one').find('input.btn_is_input_requiment')[0].checked;
            if (!is_input_required &&
                   msg_group_type == type[type_input]
                || msg_group_type == type[type_textarea]
                || msg_group_type == type[type_postal_code]
                || msg_group_type == type[type_postal_code]
                || msg_group_type == type[type_radio]
                || msg_group_type == type[type_checkbox]
                || msg_group_type == type[type_pulldown]
                || msg_group_type == type[type_calendar]
                || msg_group_type == type[type_card_payment]
                || msg_group_type == type[type_carousel]
            ) {
                // is_required flash
                if ($(msg_group).find('input.btn_is_required').length) {
                    var required_flg = 0,
                        is_required = $(msg_group).find('input.btn_is_required')[0].checked;
                    if (is_required) {
                        required_flg = 1;
                    }
                    msg_group_data['required_flg'] = required_flg;
                }
            }
            data['message'].push(msg_group_data);
        }
    });
    //text button next
    var group = $('.message_container_input .message_bot_area .fixedsidebar-content .type_group'),
        number_group = $('.message_container_input .message_bot_area .fixedsidebar-content .type_group').length;
    if ( (number_group == 1 && group.data('type') != type[type_radio])
        ||(number_group == 1 && group.data('type') == type[type_carousel] && !$(group).find('input.btn_is_required')[0].checked) || number_group > 1) {
        var btn_next_data = $('.message_container_input .message_bot_area .fixedsidebar-content .text_button_next input.content').val();
        var bot_lang = common_data['bot_lang'];
       /* if (btn_next_data != '' && list_text_button_next.indexOf(btn_next_data) != -1) {
            btn_next_data = '';
        }*/
        if (btn_next_data != '' && (bot_lang == bot_lang_ja && btn_next_data == text_button_ja || bot_lang != bot_lang_ja && btn_next_data == text_button)) {
            btn_next_data = '';
        }
        changeDataBtnNext(btn_next_data);
    }
    // data input message requiment
    if (number_group > 1) {
        var is_input_required = $('.message_bot_area .fixedsidebar-content .checkbox_requirement_one input.btn_is_input_requiment')[0].checked;
        var input_requiment_flg = 0;
        if (is_input_required) {
            input_requiment_flg = 1;
        }
        changeDataRequirementOne(input_requiment_flg);
    }

    data = JSON.stringify(data);
    changeDataBotMessage(data);
    if (filter_data != undefined && filter_data.length) {
        filter_data = JSON.stringify(filter_data);
    } else {
        filter_data = '';
    }
    changeDataFilter(filter_data);
}

/**
 * get title from message_container_input for input User demo
 * @param is_clear
 */
function setUserDataMediaDemo(is_clean) {
    var user_message_box = userMessageBoxElement();
    if(is_clean != void 0 && is_clean) {
        user_message_box.find('.user_media_content').val('');
    } else {
        var user_message_type = userMessageType(),
            type = common_data['bot_content_type'],
            value;

        if(user_message_type == type[type_text]) {
            var typeInput = userInputText();
            value = typeInput.val();
        }
        user_message_box.find('.user_media_content').val(value);
    }
}

/**
 * get title from message_container_input for input Bot demo
 * @param is_clear
 */
function setDataMediaDemo(is_clean) {
    var bot_message_box = botMessageBoxElement();
    if(is_clean != void 0 && is_clean) {
        bot_message_box.find('.bot_media_content').val('');
    } else {
        var bot_message_type    = botMessageType(),
            type                = common_data['bot_content_type'],
            value;

        if(bot_message_type == type[type_input]) {
            var typeInput = userInputText();
            value = typeInput.val();

        } else if(bot_message_type == type[type_textarea]) {
            value = $('.button_group .button_container input.button_title').val();
        } else if(bot_message_type == type[type_file]) {
            value = $('.file_group .file_url_input').val();
        } else if(bot_message_type == type[type_calendar]) {
            value = $('.file_group .file_url_input').val();
        }
        bot_message_box.find('.bot_media_content').val(value);
    }
}

/**
 * Actions when click to user message
 */
function checkContentUserMessage(type_current) {
    $('.message_user_area .fixedsidebar-content .type_group').remove();
    var type = common_data['bot_message_type'],
        user_message = userMessageBoxElement(),
        user_media_demo = $('.scenario_block .user_scenario .user_media_demo'),
        group_message = '';
    //clear data generic
    $('.generic_group .generic_container .generic_box').remove();
    $('.generic_indicators li').remove();
    $('.message_user_area .fixedsidebar-content').html('');
    initFilterGroup();
    switch (type_current){
        case type[type_text]:
            var clone = $('.scenario_message_origin .type_group.text_group').clone();
            $('.message_user_area .fixedsidebar-content').append(clone);
            userInputText('');
            group_message = $('.message_user_area .text_group');
            group_message.find('.input_text_type').focus();
            messageTextComplete();
            changeLabelUserMediaDemo('');
            break;
        case type[type_file]:
            var clone = $('.scenario_message_origin .type_group.file_group').clone();
            $('.message_user_area .fixedsidebar-content').append(clone);
            clone.find('.file_url_input').val('');
            changeLabelUserMediaDemo(type_file);
            break;
        case type[type_mail]:
            var clone = $('.scenario_message_origin .type_group.mail_group').clone();
            $('.message_user_area .fixedsidebar-content').append(clone);
            initSelect2();
            changeMailSelect();
            changeLabelUserMediaDemo(type_mail);
            break;
        case type[type_scenario_connect]:
            var clone = $('.scenario_message_origin .type_group.scenario_connect_group').clone();
            $('.message_user_area .fixedsidebar-content').append(clone);
            initSelect2();
            changeMailSelect();
            changeLabelUserMediaDemo(type_scenario_connect);
            break;
    }
}
//mail and scenario connect change
function changeMailSelect() {
    $('.message_user_area .mail_group select.mail_select, .message_user_area .scenario_connect_group select.scenario_select').on('change', function(event){
        getDataUserFromInput();
    });
}
/**
 * Actions when select type bot
 */
function checkContentBotInput(type_current) {
    var type = common_data['bot_content_type'];
    message_area_select(true);

    //un-active msg type
    $('.message_bot_area .type_group').removeClass('active');

    //clear validation class in select2 select
    $('.message_bot_area .select2 .select2-selection').removeClass('validation-failed');

    var bot_message_input   = botMessageBoxElement();
    //hide validate old message box
    setStyleElm(bot_message_input.find('.bot_media_content'), {'border' : ''});

    //add button demo in bot message
    // checkViewMediaDemo();
    switch (type_current) {
        case type[type_text]:
            initLabelType();
            // changeLabelMediaDemo(type_input);
            break;
        case type[type_input]:
            initInputType();
            // changeLabelMediaDemo(type_input);
            break;
        case type[type_textarea]:
            initTextareaType();
            // changeLabelMediaDemo(type_textarea);
            break;
        case type[type_radio]:
            initRadioType();
            // changeLabelMediaDemo(type_radio);
            break;
        case type[type_checkbox]:
            initCheckboxType();
            // changeLabelMediaDemo(type_checkbox);
            break;
        case type[type_pulldown]:
            initPulldownType();
            // changeLabelMediaDemo(type_pulldown);
            break;
        case type[type_postal_code]:
            initPostalcodeType();
            break;
        case type[type_file]:
            initUploadFileType();
            break;
        case type[type_calendar]:
            initCalendarType();
            break;
        case type[type_terms_of_use]:
            is_call_icheck = false;
            initTermsOfUseType();
            break;
        case type[type_confirmation]:
            is_call_icheck = false;
            initConfirmationType();
            break;
        case type[type_carousel]:
            initCarouselType();
            break;
        case type[type_card_payment]:
            initCardPaymentType();
            break;
        case type[type_captcha]:
            initCaptchaType();
            break;
        case type[type_add_to_cart]:
            initAddToCartType();
            break;
        case type[type_request_document]:
            initDocumentType();
            break;
    }

    checkActiveRequirementOne();
    getDataFromInput();
    check_add_item_common();
    initSelect2();
    btnAddItem();
    var variable_input = textareaVariableElement();
    initIcheck(variable_input);
    // checkbox is required
    var is_required_input = textRequired();
    initIcheck(is_required_input);
}

/**
 * Actions when click to bot message
 */
function checkContentBotMessage() {
    $('.message_bot_area .fixedsidebar-content').html('');
    initFilterGroup();
    message_area_select(true);
}
// init select2
function initSelect2() {
    $('.fixedsidebar-content select').select2({
        minimumResultsForSearch: -1
    });
}
//init input when select that
function initInputType() {
    var group = $('.scenario_block_origin .input_box').clone();
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    // $('.message_bot_area .fixedsidebar-content').append(group);
    inputGroupChangeNumber();
    changeTypeInput();
}
//init textarea when select that
function initTextareaType() {
    var group = $('.scenario_block_origin .textarea_box').clone();
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    // $('.message_bot_area .fixedsidebar-content').append(group);
}
//init radio when select that
function initRadioType() {
    var group = $('.scenario_block_origin .radio_group').clone();
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    // $('.message_bot_area .fixedsidebar-content').append(group);
    addRadioBox();
    changeTypeRadio();
}

//init checkbox when select that
function initCheckboxType() {
    var group = $('.scenario_block_origin .checkbox_group').clone();
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    // $('.message_bot_area .fixedsidebar-content').append(group);
    addCheckboxBox();
}
//init pulldown when select that
function initPulldownType() {
    var group = $('.scenario_block_origin .pulldown_group').clone();
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    // $('.message_bot_area .fixedsidebar-content').append(group);
    initPulldownFirst();
    changeTypePulldown();
}
//init postalcode when select that
function initPostalcodeType() {
    var group = $('.scenario_block_origin .postal_code_box').clone();
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    // $('.message_bot_area .fixedsidebar-content').append(group);
}
//init terms of use when select that
function initTermsOfUseType() {
    var group = $('.scenario_block_origin .terms_of_use_group').clone();
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    // $('.message_bot_area .fixedsidebar-content').append(group);
}
//init label when select that
function initLabelType() {
    var group = $('.scenario_block_origin .label_group').clone();
    // $('.message_bot_area .fixedsidebar-content').append(group);
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
}
//init confirmation when select that
function initConfirmationType() {
    var group = $('.scenario_block_origin .confirmation_group').clone();
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    // $('.message_bot_area .fixedsidebar-content').append(group);
}
//init upload file when select that
function initUploadFileType() {
    var group = $('.scenario_block_origin .upload_file_group').clone();
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    // $('.message_bot_area .fixedsidebar-content').append(group);
}
//init calendar when select that
function initCalendarType(input_start_date) {
    var group = $('.scenario_block_origin .calendar_group').clone();
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    // $('.message_bot_area .fixedsidebar-content').append(group);
    // init datetimepicker
    changeCalendarType();
    initDateTimePickerSelect(input_start_date);
    var is_weekend_off = weekendOff();
    initIcheck(is_weekend_off);

    return group;
}
//init carousel when select that
function initCarouselType() {
    var group = $('.scenario_block_origin .generic_group').clone();
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    // $('.message_bot_area .fixedsidebar-content').append(group);
    generic_element_select();
    createCarouselBox(group);
    addItemCarouselBox();
    initslide();
    slideCarousel();
}
//init carousel when select that
function initCardPaymentType() {
    var group = $('.scenario_block_origin .card_payment_group').clone();
    initIcheck(group.find('.card_type input'));
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    // $('.message_bot_area .fixedsidebar-content').append(group);
}
//init captcha when select that
function initCaptchaType() {
    var group = $('.scenario_block_origin .captcha_group').clone();
    previewCaptcha(group);
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    // $('.message_bot_area .fixedsidebar-content').append(group);
}
//init add to cart when select that
function initAddToCartType() {
    var group = $('.scenario_block_origin .add_to_cart_group').clone();
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));

    return group;
}
//init request document when select that
function initDocumentType() {
    var group = $('.scenario_block_origin .request_document_group').clone();
    group.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
    changeTypeDocument();
    return group;
}
/**
 * add new generic block
 */
function createCarouselBox(group) {
    if($('.message_bot_area  .generic_group .generic_container .generic_box').length < max_generic_item) {
        var clone = $('.scenario_block_origin .generic_box').clone();
        group.find('.generic_container').append(clone);
        checkGenericBox();

        //------Carousel process
        //param false: set active for tem
        addCarouselIndicator('generic_indicators', false);
        checkCarouselSlide();

        // slideCarousel();
    }
}
// add generic item
function addItemCarouselBox() {
    $('.message_bot_area .add_group_box .add_item_generic').on('click', function (event) {
        createCarouselBox($(this).closest('.generic_group'));
    });
}
// slide carousel
function slideCarousel() {
    console.log('change slide');
    // $('.carousel_slide .left').click(function() {
    //     $('.generic_container').carousel('prev');
    //     checkCarouselSlide();
    //  });
    //
    //  $('.carousel_slide .right').click(function() {
    //     $('.generic_container').carousel('next');
    //     checkCarouselSlide();
    //  });
}
function initslide() {
    $('.message_bot_area .generic_carousel').carousel({
        interval: false,
        wrap: false
        // $indicators: $('.message_bot_area .footer_message_input .carousel-indicators')
    });
}

/**
 * action for elements when select generic in bot message
 */
function generic_element_select() {
    $('.message_bot_area  .footer_message_input .carousel_slide').show();
    $('.message_bot_area  .footer_message_input .carousel_indicator_item').hide();
}
//hide, show by check number button box
function checkGenericBox() {
    var generic_box_count   = $('.message_bot_area .generic_container .generic_box').length,
        delete_button       = $('.message_bot_area .generic_box .delete_generic_box');

    //show, hide delete generic box
    if(generic_box_count <= 1) {
        delete_button.hide();
    } else {
        delete_button.show();
    }
    checkAddGroupButtonBox();
}
//add Carousel indicators follow generic item
function addCarouselIndicator(parent_class, first_active) {
    $('.' + parent_class).append('<li data-slide-to="0" data-target="#c-slide"></li>');

    //re-set index for generic indicators
    $('.message_bot_area .generic_indicators li').each(function (index, value) {
        $(this).attr('data-slide-to', index);
    });
    //set active for first indicators and first generic_box
    var indicator = $('.message_bot_area .generic_indicators'),
        container = $('.message_bot_area .generic_container');
    indicator.find('li').removeClass('active');
    container.find('.generic_box').removeClass('active');
    if(first_active) {
        indicator.find('li:first-child').addClass('active');
        container.find('.generic_box:first-child').addClass('active');
    } else {
        indicator.find('li:last-child').addClass('active');
        container.find('.generic_box:last-child').addClass('active');
    }
}
//check active slide to hide, show arrow
function checkCarouselSlide() {
    var arrow_left  = $(".message_bot_area .footer_message_input .carousel_slide [data-slide='prev']"),
        arrow_right = $(".message_bot_area .footer_message_input .carousel_slide [data-slide='next']");

    if ($('.message_bot_area .generic_group.active .generic_container .generic_box').length > 1) {
        if($('.message_bot_area .generic_group.active .generic_container .generic_box:last-child').hasClass('active')) {
            arrow_right.hide();
            arrow_left.show();
        } else if($('.message_bot_area .generic_group.active .generic_container .generic_box:first-child').hasClass('active')) {
            arrow_right.show();
            arrow_left.hide();
        } else {
            arrow_right.show();
            arrow_left.show();
        }
    } else {
        arrow_right.hide();
        arrow_left.hide();
    }
    //re-active Carousel indicator (Because move indicator to outsite Carousel div)
    var indexActive = $('.message_bot_area .generic_container .generic_box.active').index();
    $('.message_bot_area .footer_message_input .generic_indicators li').removeClass('active');
    $('.message_bot_area .footer_message_input .generic_indicators li').eq(indexActive).addClass('active');
}
//hide, show add_group_box button
function checkAddGroupButtonBox() {
    var add_group_btn       = $('.message_bot_area  .add_group_box'),
        bot_message_type    = botMessageType(),
        type                = common_data['bot_content_type'],
        item_btn_count,
        max_num;

    if(bot_message_type == type[type_carousel]) {
        item_btn_count      = $('.message_bot_area .generic_group .generic_container .generic_box').length;
        max_num             = max_generic_item;
    }
    if(item_btn_count >= max_num) {
        add_group_btn.hide();
    } else {
        add_group_btn.show();
    }
}
function genericActiveElement() {
    return $('.message_bot_area .generic_group .generic_box.active');
}
/**.
 * select bot message or user message
 * @param bot: undefined  or null: hide all
 */
function message_area_select(bot) {
    //hide message user area
    var message_user_area = $('.message_container_input .message_user_area'),
        message_bot_area  = $('.message_container_input .message_bot_area');
    message_user_area.hide();
    message_bot_area.hide();

    if(bot != null) {
        if(bot) {
            message_bot_area.show();
        } else {
            message_user_area.show();
        }
    }
}

/**
 * set data user mesage come back to input form
 */
function setUserDataToForm() {
    var user_message_val  = userMessageContent(),
        user_message_type = userMessageType(),
        user_input_text   = userInputText(),
        type              = common_data['bot_message_type'],
        bot_filter_val    = getFilterContent(),
        group_message     = '';
    /*fill data from user message to text input in right slidebar*/
    var jsonData = jsonConverse(user_message_val);
    var filter_data = jsonConverse(bot_filter_val);

    //show save button
    $('.message_container_input .update_scenario').removeClass('hidden');

    //clear old filter and fill new value
    setFilterData(filter_data);

    if(jsonData['message'] != void 0) {
        $(jsonData['message']).each(function (i, msg) {
            var msg_type = msg.type;
            if(msg_type != void 0) {
                switch (msg_type) {
                    case type[type_text]:
                        group_message = $('.message_user_area .text_group');

                        if(msg.content != void 0) {
                            user_input_text.val(msg.content);
                        }
                        break;
                    case type[type_file]:
                        var url     = msg.url ? msg.url : '';
                        //set file url for file input
                        $('.file_group .file_url_input').val(url);
                        break;
                    case type[type_mail]:
                        var mail_id = msg.mail;
                        //set mail id for selectbox
                        mailElement(mail_id);
                        getDataUserFromInput();
                        break;
                    case type[type_scenario_connect]:
                        // jsonData = msg['data']['message'];
                        // var scenario_id = jsonData['scenario'];
                        var scenario_id = msg.scenario;
                        //set scenario id for selectbox
                        scenarioElement(scenario_id);
                        break;
                }
            }
        });
    }

    setActiveLastGroupMsg(true);
}

/**
 * set data bot message come back to input form
 */
function setBotDataToForm() {
    var bot_message_input   = botMessageElement(),
        bot_message_val     = botMessageContent(),
        bot_filter_val      = getFilterContent(),
        bot_btn_next_val    = getBtnNext(),
        input_requiment_flg_val = getRequirementOne(),
        type                = common_data['bot_content_type'],
        group_message     = '';

    //fill data from bot message to text input in right slidebar
    var jsonData = jsonConverse(bot_message_val);
    var filter_data = jsonConverse(bot_filter_val);
    //set text btn next
    if (bot_btn_next_val != '') {
        $('.message_container_input .message_bot_area .fixedsidebar-content .text_button_next input.content').val(bot_btn_next_val);
    }
    //clear old input before add
    $('.message_bot_area .fixedsidebar-content .type_group').remove();

    //clear old filter and fill new value
    setFilterData(filter_data);

    //remove all generic before action
    $('.message_bot_area .generic_group .generic_container .generic_box').remove();
    $('.message_bot_area .generic_indicators li').remove();

    if(jsonData['message'] != void 0) {
        $(jsonData['message']).each(function (index, msg) {
            //un-active msg type
            $('.message_bot_area .type_group').removeClass('active');

            var msg_type = msg.type;
            if(msg_type != void 0) {
                switch (msg_type) {
                    case type[type_radio]: {
                        var template_radio = common_data['template_radio'];
                        group_message = $('.scenario_block_origin .radio_group').clone();
                        if(msg.template_type != void 0 && msg.template_type != '') {
                            group_message.find('.radio_type').val(msg.template_type).trigger('change.select2');
                        }
                        if(msg.list != void 0 && msg.list.length) {
                            var clone;
                            $(msg.list).each(function (i, label) {
                                if (msg.template_type == template_radio[type_image_radio]) {
                                    if (label.length == 1) {
                                        clone = $('.scenario_block_origin .radio_image').clone();
                                        clone.find('input.image_url').val(label[0].image_url);
                                        clone.find('input.title ').val(label[0].comment);
                                    }else if (label.length == 2) {
                                        clone = $('.scenario_block_origin .radio_two_image').clone();
                                        clone.find('.generic_box_content').each(function (index, item) {
                                            console.log(label[index].comment);
                                            $(item).find('input.image_url').val(label[index].image_url);
                                            $(item).find('input.title ').val(label[index].comment);
                                        });
                                    }else if (label.length == 3) {
                                        clone = $('.scenario_block_origin .radio_three_image').clone();
                                        clone.find('.generic_box_content').each(function (index, item) {
                                            $(item).find('input.image_url').val(label[index].image_url);
                                            $(item).find('input.title ').val(label[index].comment);
                                        });
                                    }

                                }else {
                                    clone = $('.scenario_block_origin .radio_text').clone();
                                    clone.find('input.radio_label').val(label);
                                }
                                group_message.find('.radio_container').append(clone);
                            });
                        }
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));

                        // $('.message_bot_area .fixedsidebar-content').append(group_message);
                        //create radio if not exist and show, hide delete radio item label
                        checkRadioBox();
                        changeTypeRadio();
                        btnAddItem();
                    }
                        break;
                    case type[type_checkbox]: {
                        group_message = $('.scenario_block_origin .checkbox_group').clone();
                        if(msg.list != void 0 && msg.list.length) {
                            $(msg.list).each(function (i, label) {
                                var clone = $('.scenario_block_origin .checkbox_box').clone();
                                clone.find('input.checkbox_label').val(label);
                                group_message.find('.checkbox_container').append(clone);
                            });
                        }
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                        // $('.message_bot_area .fixedsidebar-content').append(group_message);
                        //create checkbox if not exist and show, hide delete radio item label
                        checkCheckboxBox();
                        btnAddItem();
                    }
                        break;
                    case type[type_textarea]: {
                        group_message = $('.scenario_block_origin .textarea_box').clone();
                        if(msg.template_type != void 0 && msg.template_type != '') {
                            if (msg.template_type != type_textarea_input){
                                group_message.find('.input_text_type').removeClass('placeholder').addClass('readonly');
                            }
                            group_message.find('.textarea_type').val(msg.template_type).trigger('change.select2');
                        }
                        if(msg.placeholder != void 0 && msg.placeholder != '') {
                            group_message.find('.input_text_type').val(msg.placeholder);
                        }
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                        // $('.message_bot_area .fixedsidebar-content').append(group_message);
                        messageTextComplete();
                    }
                        break;
                    case type[type_input]: {
                        group_message = $('.scenario_block_origin .input_box').clone();
                        var template_input = common_data['template_input'];
                        if(msg.template_type != void 0 && msg.template_type != '') {
                            group_message.find('.input_type ').val(msg.template_type).trigger('change.select2');
                        }

                        /*show type validation input text*/
                        if (group_message.find('.input_type').val() == template_input[type_text]) {
                            group_message.find('.validation_text').removeClass('hidden');
                            group_message.find('.lengh_password').removeClass('hidden');
                            if(msg.min_length != '' || msg.max_length != '') {
                                group_message.find('#min_length').val(msg.min_length);
                                group_message.find('#max_length').val(msg.max_length);
                                passwordLength();
                            }
                        }else {
                            group_message.find('.validation_text').addClass('hidden');
                            group_message.find('.lengh_password').addClass('hidden');
                        }
                        if(msg.validation_type != void 0 && msg.validation_type != '') {
                            group_message.find('.validation_type').val(msg.validation_type).trigger('change.select2');
                        }
                        /*show tel input type*/
                        if (group_message.find('.input_type').val() == template_input[type_tel]) {
                            group_message.find('.tel_input_type').removeClass('hidden');
                            if(msg.tel_input_type != void 0 && msg.tel_input_type != '') {
                                group_message.find('.tel_input_type_select').val(msg.tel_input_type).trigger('change.select2');
                                changeTypeTelInput();
                            }
                        }
                        /*show min max lengh input password*/
                        if( (msg.min_length != '' || msg.max_length != '') && (msg.template_type == template_input[type_password] || msg.template_type == template_input[type_password_confirm])) {
                            group_message.find('#min_length').val(msg.min_length);
                            group_message.find('#max_length').val(msg.max_length);
                            group_message.find('.lengh_password').removeClass('hidden');
                            passwordLength();
                        }
                        if(msg.list != void 0 && msg.list.length) {
                            var clone;
                            if (msg.list.length == 1){
                                clone = $('.scenario_block_origin .input_box_child .one_input').clone();
                                var template_input = common_data['template_input'];
                                if (msg.template_type != template_input[type_text]){
                                    clone.find('.change_input').addClass('hide');
                                }
                            }else if (msg.list.length == 2 && msg.template_type == template_input[type_text]){
                                clone = $('.scenario_block_origin .input_box_child .two_input').clone();
                            }else if (msg.list.length == 2 && (msg.template_type == template_input[type_password_confirm] || msg.template_type == template_input[type_mail_confirm])){
                                clone = $('.scenario_block_origin .input_box_child .confirm_input').clone();
                            }else if (msg.list.length == 3 && (msg.template_type == template_input[type_tel])){
                                clone = $('.scenario_block_origin .input_box_child .tel_input_hyphen').clone();
                            }
                            clone.find('input.content').each(function (ind, item) {
                                $(item).val(msg.list[ind]['placeholder']);
                            });
                            group_message.append(clone);
                        }
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                        // $('.message_bot_area .fixedsidebar-content').append(group_message);
                        changeTypeInput();
                    }
                        break;
                    case type[type_postal_code]: {
                        group_message = $('.scenario_block_origin .postal_code_box').clone();
                        if(msg.list != void 0 && msg.list.length) {
                            var item_input = [];
                            var placeholder = [];
                            $(msg.list).each(function (i, e) {
                                item_input.push(e.type);
                                placeholder.push(e.placeholder);
                            });
                            group_message.find('input.content').each(function (ind, item) {
                                if (item_input) {
                                    if (item_input.indexOf($(item).attr('id')) == -1) {
                                        $(item).closest('.input_item').remove();
                                    }else {

                                    }
                                }
                            });
                            group_message.find('input.content').each(function (ind, item) {
                                if (msg.list[ind] != undefined) {
                                    $(item).val(msg.list[ind]['placeholder']);
                                }
                            });
                        }else {
                            group_message.find('.input_item').remove();
                        }
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                        // $('.message_bot_area .fixedsidebar-content').append(group_message);
                    }
                        break;
                    case type[type_pulldown]: {
                        var template_pulldown   = common_data['template_pulldown'],
                            birthday = $('.scenario_block_origin .pulldown_box_child .birthday').clone(),
                            birthday_y_m = $('.scenario_block_origin .pulldown_box_child .birthday_y_m').clone(),
                            date = $('.scenario_block_origin .pulldown_box_child .date').clone(),
                            month_date = $('.scenario_block_origin .pulldown_box_child .month_date').clone(),
                            time = $('.scenario_block_origin .pulldown_box_child .time').clone(),
                            towns_and_villages = $('.scenario_block_origin .pulldown_box_child .towns_and_villages').clone(),
                            date_time = $('.scenario_block_origin .pulldown_box_child .date_time').clone(),
                            period_of_time = $('.scenario_block_origin .pulldown_box_child .period_of_time').clone(),
                            period_of_day = $('.scenario_block_origin .pulldown_box_child .period_of_day').clone();

                        group_message = $('.scenario_block_origin .pulldown_group').clone();
                        if(msg.template_type != void 0 && msg.template_type != '') {
                            group_message.find('.pulldown_type').val(msg.template_type).trigger('change.select2');
                        }
                        if(msg.list != void 0) {
                            // group_message.find('.content').remove();
                            if (msg.template_type == template_pulldown[type_birthday]) {
                                group_message.append(birthday);
                            }else if(msg.template_type == template_pulldown[type_birthday_y_m]){
                                group_message.append(birthday_y_m);
                            }else if (msg.template_type == template_pulldown[type_date]) {
                                group_message.append(date);
                            }else if (msg.template_type == template_pulldown[type_month_date]) {
                                group_message.append(month_date);
                            }else if (msg.template_type == template_pulldown[type_time]) {
                                group_message.append(time);
                                if (msg.spacing_minute != void 0 && msg.spacing_minute != '') {
                                    group_message.find('.spacing_minute').val(msg.spacing_minute).trigger('change.select2');
                                }
                                changeOptionSelectMinute(group_message.find('.minute'), group_message.find('.spacing_minute').val());
                            }else if (msg.template_type == template_pulldown[type_date_time]) {
                                group_message.append(date_time);
                                if (msg.spacing_minute != void 0 && msg.spacing_minute != '') {
                                    group_message.find('.spacing_minute').val(msg.spacing_minute).trigger('change.select2');
                                }
                                changeOptionSelectMinute(group_message.find('.minute'), group_message.find('.spacing_minute').val());
                            }else if (msg.template_type == template_pulldown[type_period_of_time]) {
                                group_message.append(period_of_time);
                                if (msg.start_spacing_minute != void 0 && msg.start_spacing_minute != '') {
                                    group_message.find('.start_time .spacing_minute').val(msg.start_spacing_minute).trigger('change.select2');
                                    changeOptionSelectMinute(group_message.find('.start_time .minute'), group_message.find('.start_time .spacing_minute').val());
                                }
                                if (msg.end_spacing_minute != void 0 && msg.end_spacing_minute != '') {
                                    group_message.find('.end_time .spacing_minute').val(msg.end_spacing_minute).trigger('change.select2');
                                    changeOptionSelectMinute(group_message.find('.end_time .minute'), group_message.find('.end_time .spacing_minute').val());
                                }
                            }else if (msg.template_type == template_pulldown[type_period_of_day]) {
                                group_message.append(period_of_day);
                            }else if (msg.list.length && msg.template_type == template_pulldown[type_customize]) {
                                var clone = $('.scenario_block_origin .customize').clone();
                                if (msg.first_title != '' || msg.last_title != '') {
                                    clone.find('.pulldown_first_title').val(msg.first_title);
                                    clone.find('.pulldown_last_title').val(msg.last_title);
                                }
                                if (msg.list.length == 1) {
                                    $(msg.list[0]).each(function (i, label) {
                                        var item_clone = $('.scenario_block_origin .pulldown_box_child .customize_one_pulldown_item').clone();
                                        item_clone.find('input.pulldown_label').val(label);
                                        // clone.find('.pulldown_item').append(item_clone);
                                        item_clone.insertBefore(clone.find('.pulldown_item .add_item_box'));
                                        group_message.find('.pulldown_container').append(clone);
                                    });
                                }else {
                                    $(msg.list[0]).each(function (i, label) {
                                        var item_clone = $('.scenario_block_origin .pulldown_box_child .customize_two_pulldown_item').clone();
                                        item_clone.find('.first_pulldown input.pulldown_label').val(label);
                                        item_clone.find('.last_pulldown input.pulldown_label').val(msg.list[1][i]);
                                        clone.find('.pulldown_item').append(item_clone);
                                        group_message.find('.pulldown_container').append(clone);
                                    });
                                    group_message.find('.one_pulldown').addClass('hidden');
                                    group_message.find('.two_pulldown').removeClass('hidden');
                                }
                                checkPulldownBox();
                            }else if (msg.template_type == template_pulldown[type_provinces_of_japan]) {
                                fillDataPulldownProvinces(group_message);
                            }else if (msg.template_type == template_pulldown[type_towns_and_villages_of_japan]) {
                                group_message.append(towns_and_villages);
                                if (msg.first_title != '' || msg.last_title != '') {
                                    group_message.find('.pulldown_first_title').val(msg.first_title);
                                    group_message.find('.pulldown_last_title').val(msg.last_title);
                                }
                            }
                        }
                        if (msg.comment != void 0 && msg.comment != '') {
                            group_message.find('.comment').val(msg.comment)
                        }
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                        // $('.message_bot_area .fixedsidebar-content').append(group_message);
                        changeTypePulldown();
                        btnAddItem();
                        //create checkbox if not exist and show, hide delete radio item label
                    }
                        break;
                    case type[type_terms_of_use]: {
                        group_message = $('.scenario_block_origin .terms_of_use_group').clone();
                        var template_term_of_use = common_data['template_term_of_use'];
                        if(msg.template_type != void 0 && msg.template_type != '') {
                            group_message.find('.term_of_use_type').val(msg.template_type).trigger('change.select2');
                        }
                        if(group_message.find('.term_of_use_type').val() ==  template_term_of_use[type_detail_content]) {
                            group_message.find('.link_input').addClass('hide');
                            group_message.find('.add_link_input').addClass('hide');
                            group_message.find('.textarea_input').removeClass('hide');
                            if(msg.content != void 0 && msg.content != '') {
                                group_message.find('.input_text_type').val(msg.content);
                            }
                        }else {
                            group_message.find('.textarea_input').addClass('hide');
                            group_message.find('.link_input').removeClass('hide');
                            group_message.find('.add_link_input').removeClass('hide');

                            if(msg.list != void 0 && msg.list.length) {
                                $(msg.list).each(function (i, link_item) {
                                    var clone = $('.scenario_block_origin .item_link').clone();
                                    if(link_item.link != void 0 && link_item.link != '') {
                                        clone.find('.url').val(link_item.link);
                                    }
                                    if(link_item.link_text != void 0 && link_item.link_text != '') {
                                        clone.find('.link_text').val(link_item.link_text);
                                    }
                                    if(link_item.link_first_title != void 0 && link_item.link_first_title != '') {
                                        clone.find('.link_first_title').val(link_item.link_first_title);
                                    }
                                    if(link_item.link_last_title != void 0 && link_item.link_last_title != '') {
                                        clone.find('.link_last_title').val(link_item.link_last_title);
                                    }
                                    group_message.find('.link_input').append(clone);
                                });
                            }
                            checkItemLink();
                            // if(msg.link != void 0 && msg.link != '') {
                            //     group_message.find('.link_input .url').val(msg.link);
                            // }
                            // if(msg.link_text != void 0 && msg.link_text != '') {
                            //     group_message.find('.link_input .link_text').val(msg.link_text);
                            // }
                            // if(msg.link_first_title != void 0 && msg.link_first_title != '') {
                            //     group_message.find('.link_input .link_first_title').val(msg.link_first_title);
                            // }
                            // if(msg.link_last_title != void 0 && msg.link_last_title != '') {
                            //     group_message.find('.link_input .link_last_title').val(msg.link_last_title);
                            // }
                        }
                        if(msg.text_confirm != void 0 && msg.text_confirm != '') {
                            group_message.find('.text_confirm_label').val(msg.text_confirm);
                        }
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                        // $('.message_bot_area .fixedsidebar-content').append(group_message);
                        messageTextComplete();
                        btnAddItem();
                    }
                        break;
                    case type[type_confirmation]: {
                        group_message = $('.scenario_block_origin .confirmation_group').clone();

                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                        // $('.message_bot_area .fixedsidebar-content').append(group_message);
                    }
                        break;
                    case type[type_file]: {
                        group_message = $('.scenario_block_origin .upload_file_group').clone();
                        if (msg.file_type != void 0 && msg.file_type.length) {
                            group_message.find('.file_type').val(msg.file_type);
                        }
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                        // $('.message_bot_area .fixedsidebar-content').append(group_message);
                    }
                        break;
                    case type[type_calendar]: {
                        var template_calendar = common_data['template_calendar'];
                        group_message = $('.scenario_block_origin .calendar_group').clone();
                        if(msg.template_type != void 0 && msg.template_type != '') {
                            group_message.find('.calendar_type').val(msg.template_type).trigger('change.select2');
                        }
                        var input_start_date;
                        if(msg.start_date != void 0 && msg.start_date != '') {
                            var start_date_input = group_message.find('.input_start_date');
                            start_date_input.val(msg.start_date);
                            start_date_input.datetimepicker({
                                sideBySide: true,
                                format: 'Y/MM/DD',
                                ignoreReadonly: true
                            }).on('dp.change', function () {
                                getDataFromInput();
                            });
                            input_start_date = msg.start_date;
                        }
                        // }else {
                        //     input_start_date = getDateTime();
                        // }
                        if (group_message.find('.calendar_type').val() == template_calendar[type_calendar_embed] && group_message.find('.embed_calendar').hasClass('hidden')) {
                            group_message.find('.select_calendar').addClass('hidden');
                            group_message.find('.embed_calendar').removeClass('hidden');
                            group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                            // $('.message_bot_area .fixedsidebar-content').append(group_message);
                            group_message.find('.embeb_datepicker').empty().datetimepicker({
                                inline: true,
                                format: 'Y/MM/DD',
                                minDate : (input_start_date != '') ? input_start_date : false
                            });
                        }else if(group_message.find('.calendar_type').val() == template_calendar[type_calendar_period_of_time] && group_message.find('.calendar_period_of_time').hasClass('hidden')){
                            group_message.find('.select_calendar').addClass('hidden');
                            group_message.find('.calendar_period_of_time').removeClass('hidden');
                            group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                            // $('.message_bot_area .fixedsidebar-content').append(group_message);
                            initDateTimePickerPeriodOfTime(input_start_date);
                        } else {
                            var group = initCalendarType(input_start_date);
                            //
                            if(msg.title != void 0 && msg.title != '') {
                                changeStatusLabel(group, msg.title, msg.title_flg);
                            }
                            if(msg.start_date != void 0 && msg.start_date != '') {
                                var start_date_input = group.find('.input_start_date');
                                start_date_input.val(msg.start_date);
                                start_date_input.datetimepicker({
                                    sideBySide: true,
                                    format: 'Y/MM/DD',
                                    ignoreReadonly: true
                                }).on('dp.change', function () {
                                    getDataFromInput();
                                });
                            }
                            icheck_change_event = false;
                            if(msg.variable_id != void 0 && msg.variable_id != '') {
                                group.find('input.btn_is_variable').iCheck('check');
                                group.find('.variable_group').removeClass('hidden');
                                group.find('select.button_variable').val(msg.variable_id);
                            }
                            //change value is weekend off
                            if(msg.weekend_off_flg != void 0 && msg.weekend_off_flg != 0 && group_message.find('input.btn_is_weekend_off').length) {
                                group.find('input.btn_is_weekend_off').iCheck('check');
                                var calendar_weekend_off = group.find('.select_calendar input.datetimepicker');
                                calendar_weekend_off.data("DateTimePicker").destroy();
                                initCalendarWeekendOff(calendar_weekend_off, input_start_date);
                            }
                            if(msg.required_flg != void 0 && msg.required_flg != 0 && group.find('input.btn_is_required').length) {
                                group.find('input.btn_is_required').iCheck('check');
                            }
                            icheck_change_event = true;
                        }
                        // $('.message_bot_area .fixedsidebar-content').append(group_message);
                        changeStatusWeekendOff(group_message);
                        changeCalendarType();
                    }
                        break;
                    case type[type_text]: {
                        group_message = $('.scenario_block_origin .label_group').clone();
                        if(msg.content != void 0 && msg.content != '') {
                            group_message.find('.input_text_type').val(msg.content);
                        }
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                        // $('.message_bot_area .fixedsidebar-content').append(group_message);
                    }
                        break;
                    case type[type_carousel]: {
                        // //remove all generic before action
                        // $('.message_bot_area .generic_group .generic_container .generic_box').remove();
                        // $('.message_bot_area .generic_indicators li').remove();

                        group_message = $('.scenario_block_origin .generic_group').clone();
                        //remove all generic before action
                        group_message.find('.generic_container .generic_box').remove();
                        $('.message_bot_area .generic_indicators li').remove();

                        if(msg.list != void 0 && msg.list.length) {
                            $(msg.list).each(function (i, element) {
                                var clone = $('.scenario_block_origin .generic_box').clone();
                                clone.find('input.title').val(element.title);
                                clone.find('input.sub_title').val(element.subtitle);
                                clone.find('input.item_url').val(element.item_url);
                                clone.find('input.image_url').val(element.image_url);
                                clone.find('input.button_title').val(element.button.title);

                                group_message.find('.generic_container').append(clone);
                                group_message.find('.generic_box:first-child').addClass('active');
                                //------Carousel process
                                //set active for first item
                                addCarouselIndicator('generic_indicators', true);
                                // checkCarouselSlide();

                                //check to show, hide button delete box
                                checkGenericBox();
                            });
                        }else {
                            //add first generic box
                            createCarouselBox(group_message);
                        }
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                        // $('.message_bot_area .fixedsidebar-content').append(group_message);
                        group_message.closest('.message_bot_area').find('.generic_indicators li').removeClass('active');
                        group_message.closest('.message_bot_area').find('.generic_indicators li:first-child').addClass('active');
                        group_message.closest('.message_bot_area').find('.footer_message_input .carousel_slide').show();
                        checkCarouselSlide();
                        addItemCarouselBox();
                        initslide();
                        slideCarousel();
                    }
                        break;
                    case type[type_card_payment]: {
                        group_message = $('.scenario_block_origin .card_payment_group').clone();

                        if(msg.list != void 0 && msg.list.length) {
                            var item_input = [];
                            var placeholder = [];
                            $(msg.list).each(function (i, e) {
                                group_message.find('input#' + e.type).val(e.placeholder);
                            });
                        }
                        if(msg.card_type != void 0 && msg.card_type.length) {
                            $(msg.card_type).each(function (i, e) {
                                var card_check = group_message.find('input.btn_is_card_type[value="'+ e + '"]');
                                if(card_check.length){
                                    icheckAuto('check', card_check);
                                }
                            });
                        }
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                        // $('.message_bot_area .fixedsidebar-content').append(group_message);
                        initIcheck(group_message.find('.card_type input'));
                    }
                        break;
                    case type[type_captcha]: {
                        group_message = $('.scenario_block_origin .captcha_group').clone();
                        if(msg.captcha_type != void 0 && msg.captcha_type!= '' || msg.size != void 0 && msg.size!= '' || msg.color != void 0 && msg.color!= '') {
                            group_message.find('.captcha_type').val(msg.captcha_type).trigger('change.select2');
                            group_message.find('.size').val(msg.size);
                            group_message.find('.captcha_color').val(msg.color).trigger('change.select2');
                        }
                        previewCaptcha(group_message);
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                    }
                        break;
                    case type[type_add_to_cart]: {
                        group_message = initAddToCartType();
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                    }
                        break;
                    case type[type_request_document]: {
                        group_message = initDocumentType();
                        group_message.insertBefore($('.message_bot_area .fixedsidebar-content .text_button_next'));
                        if(msg.template_type != void 0 && msg.template_type != '') {
                            group_message.find('.document_type').val(msg.template_type).trigger('change.select2');
                        }
                        demoTemplateDocument(group_message);
                        changeTypeDocument();
                    }
                        break;
                }
                // change label status
                if(msg.title != void 0 && msg.title != '') {
                    changeStatusLabel(group_message, msg.title, msg.title_flg);
                }
                //change value variable
                icheck_change_event = false;
                if(msg.variable_id != void 0 && msg.variable_id != '') {
                    group_message.find('input.btn_is_variable').iCheck('check');
                    group_message.find('.variable_group').removeClass('hidden');
                    group_message.find('select.button_variable').val(msg.variable_id);
                }
                //change value is required
                if(msg.required_flg != void 0 && msg.required_flg != 0 && group_message.find('input.btn_is_required').length) {
                    group_message.find('input.btn_is_required').iCheck('check');
                }
                //change value is input requiment one
                if (input_requiment_flg_val != '' && input_requiment_flg_val == 1) {
                    $('.message_bot_area .fixedsidebar-content .checkbox_requirement_one input.btn_is_input_requiment').iCheck('check');
                    group_message.find('.is_required').hide();
                    icheckAuto('uncheck', group_message.find('.is_required'));
                }
                icheck_change_event = true;
            }
        });
    }
    //fill text button next
    if(jsonData['text_button_next'] != void 0 && jsonData['text_button_next'] != '') {
        $('.footer_message_input .text_button_next input.content').val(jsonData['text_button_next']);
    }
    setActiveLastGroupMsg();
    //check error input in message box and push notice
    checkErrorBoxMessage();
    check_add_item_common();
    initSelect2();
    // btnAddItem();
}
function changeStatusLabel(group_message, title, title_flg) {
    group_message.find('.input_label').removeClass('hide');
    group_message.find('.input_label .title').val(title);
    group_message.find('.input_label_type').val(title_flg).trigger('change.select2');
}
/**
 * check if string can converse to json
 * @param value
 * @returns {boolean}
 */
function jsonConverse(value){
    try{
        var result = JSON.parse(value);
        return result;
    } catch (error){
        return false;
    }
}

/**
 * return file type from url
 * @param url
 * @returns {boolean}
 */
function checkTypeFile(url) {
    var extension       = url.substring(url.lastIndexOf('.') + 1).toLowerCase(),
        image_file_type = ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'bmp'],
        video_file_type = ['3gp', '3g2', '3gp', 'asf', 'dat', 'divx', 'dv', 'f4v', 'm2ts', 'm4v', 'mkv', 'mod', 'mov', 'mp4', 'mpe', 'mpeg', 'mpeg4', 'mpg', 'mts', 'nsv', 'ogm', 'ogv', 'qt', 'tod', 'ts', 'vob', 'wmv'],
        other_file_type = ['pdf'],
        result          = false;

    if (image_file_type.includes(extension)) {
        result = 'image';
    } else if(video_file_type.includes(extension)) {
        result = 'video';
    } else if(other_file_type.includes(extension)) {
        result = 'file';
    }
    return result;
}

function changeLabelMediaDemo(value) {
    var content             = '',
        bot_message_input   = botMessageBoxElement(),
        bot_type            = bot_message_input.find('.bot_media_demo .bot_type');

    if(value != void 0 && value != '') {
        content = bot_type.data(value.toLowerCase());
        // console.log('key:' + value);
        // console.log('-> ' + content);
        bot_type.removeClass('hidden');
    } else {
        bot_type.addClass('hidden');
    }
    bot_type.html(content);
}
function changeLabelUserMediaDemo(value) {
    var content             = '',
        user_message_input   = userMessageBoxElement(),
        user_type            = user_message_input.find('.user_media_demo .user_type');

    if(value != void 0 && value != '') {
        content = user_type.data(value.toLowerCase());
        user_type.removeClass('hidden');
    } else {
        user_type.addClass('hidden');
    }
    user_type.html(content);
}

/**
 * re-set hieght for right sldiebar
 */
function setHeightSlidebar() {
    var windown_w       = $( window ).width(),
        windown_h       = $( window ).height(),
        fixedsidebar    = $('.fixedsidebar'),
        fixedsidebar_content = fixedsidebar.find('.fixedsidebar-content');

    //show save button
    $('.message_container_input .update_scenario').removeClass('hidden');

    //set height, slidebar height = screen height - elements
    var nav_top             = $('.top-nav').height(),
        space               = 40,
        height_main         = windown_h - nav_top,
        common_error_box    = $('.message_bot_area .common_error'),
        filler_box          = fixedsidebar.find('.filter_group'),
        footer_box          = fixedsidebar.find('.footer_message_input'),
        update_scenario_box = fixedsidebar.find('.update_scenario'),
        group_box_header    = 0;

    if(common_error_box.length) {
        group_box_header += common_error_box.innerHeight() + parseInt(common_error_box.css('margin-bottom'));
    }

    var height_box = height_main  - space - group_box_header - update_scenario_box.innerHeight() - filler_box.innerHeight() - footer_box.innerHeight();
    fixedsidebar_content.slimscroll({
        height: height_box + 'px'
    });
}

function initIcheck(element) {
    if(element != void 0 && element) {
        element.iCheck({
            checkboxClass: 'icheckbox_minimal',
            radioClass: 'iradio_minimal'
        });
    }
}

/**
 * click to per messages to fill data and validate
 */
function validateMessages() {
    var msg_box_active = $('.scenario_block .message_box.active');
    //re-fill data of current bot message for validate
    setBotDataToForm();
    $('.scenario_block .message_box.bot_scenario').each(function (i, e) {
        $(this).click();
    });
    //re-active to active
    if(msg_box_active.length) {
        msg_box_active.click();
    }
}

/**
 * check Error and set border color for Box Message
 */
function checkErrorBoxMessage() {
    var result = true;
    var form_box            = '',
        bot_message_type    = botMessageType(),
        type                = common_data['bot_content_type'];

    switch (bot_message_type) {
        /*case type[type_text]:
            form_box = $('.message_bot_area .text_group');
            break;
        case type[type_button]:
            form_box = $('.message_bot_area .button_group');
            break;
        case type[type_generic]:
            form_box = $('.message_bot_area .generic_group');
            break;
        case type[type_file]:
            form_box = $('.message_bot_area .file_group');
            break;
        case type[type_quick_replies]:
            form_box = $('.message_bot_area .quick_replies_group');
            break;
        case type[type_api]:
            form_box = $('.message_bot_area .api_group');
            break;
        case type[type_scenario_connect]:
            form_box = $('.message_bot_area .scenario_connect_group');
            break;
        case type[type_mail]:
            form_box = $('.message_bot_area .mail_group');
            break;*/
        case type[type_card_payment]:
            form_box = $('.message_bot_area .card_payment_group');
            break;
    }

    var form_style        = {'border' : ''},
        bot_box           = botMessageBoxElement(),
        bot_media_content = bot_box.find('.bot_media_content');
    if(form_box != void 0 && form_box != '' && form_box.length) {
        var validate      = validation_scenario('efo', form_box, bot_message_type);
        //if not demo bot input (bot_media_content) then get messages_bot_content (only text type)
        if(!bot_media_content.length) {
            bot_media_content = bot_box.find('.messages_bot_content');
        }

        if(!validate) {
            result = false;
            form_style        = {'border' : '1px solid #ff5f5f'};
        }
    }
    setStyleElm(bot_media_content, form_style);
    return result;
}

/**
 * set style for element
 */
function setStyleElm(elm, style) {
    if(elm != void 0 && elm != '' && elm.length && style != void 0 && style != '') {
        elm.css(style);
    }
}

/**
 * set select first option for select input
 * @param elm
 */
function selectFirstOption(elm) {
    elm.val(elm.find('option').first().val());
}
/*change input number in input type*/
function inputGroupChangeNumber() {
    setTimeout(function(){
        var bot_message_type    = botMessageType(),
            type                = common_data['bot_content_type'],
            input_group         = $('.message_container_input .input_box.active'),
            one_input = $('.scenario_block_origin .input_box_child .one_input').clone(),
            two_input = $('.scenario_block_origin .input_box_child .two_input').clone();
        if(input_group.length && bot_message_type == type[type_input]) {
            if (input_group.find('.input_item').length) {
                if(input_group.find('.input_item').hasClass('one_input')) {
                    input_group.find('.one_input').remove();
                    input_group.append(two_input);
                } else {
                    input_group.find('.two_input').remove();
                    input_group.append(one_input);
                }
            } else {
                input_group.append(one_input);
            }
            getDataFromInput();
        }
    }, 100);

}
//auto suggest variable by textcomplete
function messageTextComplete() {
    var variable_list = common_data['variable_list'];
    $('.text_group .input_text_type, .textarea_box .readonly, textarea.terms_of_user_content').textcomplete([
        {
            match: /@(\w*)$/,
            search: function (term, callback) {
                callback($.map(variable_list, function (element) {
                    return element.indexOf(term) === 0 ? element : null;
                }));
            },
            index: 1,
            replace: function (element) {
                return ['\{\{' + element + '}}', ''];
            }
        }
    ], {
        maxCount: 1000
    }).on({
        'textComplete:hide': function (e) {
            // getDataFromInput();
            if(message_type_bot) {
                getDataFromInput();
            } else {
                getDataUserFromInput();
            }
        }
    });
}
/*event change input type*/
function changeTypeInput() {
    var template_input = common_data['template_input'];
    $(document).on("change", '.input_box .input_type', function (event) {
        var input_group = $('.message_container_input .input_box.active');
        input_group.find('.lengh_password').addClass('hidden');
        input_group.find('.tel_input_type').addClass('hidden');
        if ($(this).val() == template_input[type_text]) {
            if (input_group.find('.input_item').hasClass('confirm_input')) {
                input_group.find('.confirm_input').remove();
            }
            inputGroupChangeNumber();
            input_group.find('.validation_text').removeClass('hidden');
            input_group.find('.lengh_password').removeClass('hidden');
        }else {
            if ($(this).val() == template_input[type_password] || $(this).val() == template_input[type_password_confirm]) {
                input_group.find('.lengh_password').removeClass('hidden');
                passwordLength();
            }
            input_group.find('.validation_text').addClass('hidden');

            var one_input = $('.scenario_block_origin .input_box_child .one_input').clone(),
                confirm_input = $('.scenario_block_origin .input_box_child .confirm_input').clone();
            if ($(this).val() == template_input[type_mail_confirm] || $(this).val() == template_input[type_password_confirm]) {
                if(input_group.find('.input_item').hasClass('one_input')) {
                    input_group.find('.one_input').remove();
                }else if (input_group.find('.input_item').hasClass('two_input')) {
                    input_group.find('.two_input').remove();
                }
                else if (input_group.find('.confirm_input').hasClass('confirm_input')) {
                    input_group.find('.confirm_input').remove();
                }
                input_group.append(confirm_input);
            }else {
                if(input_group.find('.input_item').hasClass('two_input')) {
                    input_group.find('.two_input').remove();
                    input_group.append(one_input);
                }else if (input_group.find('.input_item').hasClass('confirm_input')) {
                    input_group.find('.confirm_input').remove();
                    input_group.append(one_input);
                }
                input_group.find('.change_input').addClass('hide');
            }
            if ($(this).val() == template_input[type_tel]) {
                input_group.find('.validation_text').addClass('hidden');
                input_group.find('.tel_input_type').removeClass('hidden');
                changeTypeTelInput();
            }
        }
        getDataFromInput();
    });
}
/*first init pulldown customize type*/
function initPulldownFirst() {
    var bot_message_type    = botMessageType(),
        type                = common_data['bot_content_type'],
        pulldown_group_active = $('.message_container_input .pulldown_group.active'),
        customize = $('.scenario_block_origin .pulldown_box_child .customize').clone();
    if(pulldown_group_active.length && bot_message_type == type[type_pulldown]) {
        if (!pulldown_group_active.find('.content').length) {
            pulldown_group_active.find('.pulldown_container').append(customize);
            checkPulldownBox();
        }
    }
}
/*change pulldown number in pulldown type*/
function customPulldownChangeNumber(btn_change) {
    setTimeout(function(){
        var item_pulldown = btn_change.closest('.pulldown_group.active').find('.pulldown_item .item'),
            list_item_pulldown = [];
        item_pulldown.each(function (ind, item) {
            if (btn_change.hasClass('one_pulldown')){
                // get data in one pulldown
                var label = $(item).find('input.pulldown_label').val();
                list_item_pulldown.push(label);
            }else {
                //get first data in pulldown
                var label = $(item).find('.first_pulldown input.pulldown_label').val();
                list_item_pulldown.push(label);
            }
        });
        btn_change.closest('.pulldown_group.active').find('.pulldown_item .item').remove();
        if(list_item_pulldown != void 0 && list_item_pulldown.length) {
            $(list_item_pulldown).each(function (ind, item) {
                if (btn_change.hasClass('one_pulldown')){
                    // clone two pulldown
                    var clone = $('.scenario_block_origin .pulldown_box_child .customize_two_pulldown_item').clone();
                    clone.find('.first_pulldown input.pulldown_label').val(item);
                    // $('.message_container_input .pulldown_group.active .pulldown_item').append(clone);
                    clone.insertBefore($('.message_container_input .pulldown_group.active .pulldown_item .add_item_box'));
                }else {
                    //clone one pulldown
                    var clone = $('.scenario_block_origin .pulldown_box_child .customize_one_pulldown_item').clone();
                    clone.find('input.pulldown_label').val(item);
                    // $('.message_container_input .pulldown_group.active .pulldown_item').append(clone);
                    clone.insertBefore($('.message_container_input .pulldown_group.active .pulldown_item .add_item_box'));
                }
            });
        }
       /* if (btn_change.hasClass('one_pulldown')){
            // clone two pulldown
            var clone = $('.scenario_block_origin .pulldown_box_child .customize_two_pulldown_item').clone();
            $('.message_container_input .pulldown_group.active .pulldown_item').append(clone);
        }else {
            //clone one pulldown
            var clone = $('.scenario_block_origin .pulldown_box_child .customize_one_pulldown_item').clone();
            $('.message_container_input .pulldown_group.active .pulldown_item').append(clone);
        }*/
        checkPulldownBox();
        getDataFromInput();
    }, 100);

}
/*event change type in pulldown group*/
function changeTypePulldown() {
    var template_pulldown   = common_data['template_pulldown'],
        birthday = $('.scenario_block_origin .pulldown_box_child .birthday').clone(),
        birthday_y_m = $('.scenario_block_origin .pulldown_box_child .birthday_y_m').clone(),
        date = $('.scenario_block_origin .pulldown_box_child .date').clone(),
        month_date = $('.scenario_block_origin .pulldown_box_child .month_date').clone(),
        time = $('.scenario_block_origin .pulldown_box_child .time').clone(),
        towns_and_villages = $('.scenario_block_origin .pulldown_box_child .towns_and_villages').clone(),
        date_time = $('.scenario_block_origin .pulldown_box_child .date_time').clone(),
        customize = $('.scenario_block_origin .pulldown_box_child .customize').clone(),
        period_of_time = $('.scenario_block_origin .pulldown_box_child .period_of_time').clone(),
        period_of_day = $('.scenario_block_origin .pulldown_box_child .period_of_day').clone();
        var is_call_select2 = true;
        $('.pulldown_group.active .pulldown_type').on('change', function (e) {
            var parent = $(this).closest('.pulldown_group.active');
            parent.find('.content').remove();
            if ($(this).val() == template_pulldown[type_birthday]) {
                parent.append(birthday);
            }else if($(this).val() == template_pulldown[type_birthday_y_m]){
                parent.append(birthday_y_m);
            }else if ($(this).val() == template_pulldown[type_date]) {
                parent.append(date);
            }else if ($(this).val() == template_pulldown[type_month_date]) {
                parent.append(month_date);
            }else if ($(this).val() == template_pulldown[type_time]) {
                parent.append(time);
                changeOptionSelectMinute();
            }else if ($(this).val() == template_pulldown[type_date_time]) {
                parent.append(date_time);
                changeOptionSelectMinute();
            }else if ($(this).val() == template_pulldown[type_period_of_time]) {
                parent.append(period_of_time);
                changeOptionSelectMinute();
            }else if ($(this).val() == template_pulldown[type_period_of_day]) {
                parent.append(period_of_day);
            }else if ($(this).val() == template_pulldown[type_customize]) {
                parent.find('.pulldown_container').append(customize);
                checkPulldownBox();
                btnAddItem();
            }else if ($(this).val() == template_pulldown[type_provinces_of_japan]) {
                fillDataPulldownProvinces(parent);
            }else if ($(this).val() == template_pulldown[type_towns_and_villages_of_japan]) {
                parent.append(towns_and_villages);
            }

            if (is_call_select2) {
                // initSelect2();
            }
            check_add_item_common();
            getDataFromInput();
            is_call_select2 = false;
        });
        /*$(document).on("change", '.pulldown_group.active .pulldown_type', function (event) {
            var parent = $(this).closest('.pulldown_group.active');
            parent.find('.content').remove();
            if ($(this).val() == template_pulldown[type_birthday]) {
                parent.append(birthday);
            }else if ($(this).val() == template_pulldown[type_date]) {
                parent.append(date);
            }else if ($(this).val() == template_pulldown[type_month_date]) {
                parent.append(month_date);
            }else if ($(this).val() == template_pulldown[type_time]) {
                parent.append(time);
                changeOptionSelectMinute();
            }else if ($(this).val() == template_pulldown[type_date_time]) {
                parent.append(date_time);
                changeOptionSelectMinute();
            }else if ($(this).val() == template_pulldown[type_period_of_time]) {
                parent.append(period_of_time);
                changeOptionSelectMinute();
            }else if ($(this).val() == template_pulldown[type_period_of_day]) {
                parent.append(period_of_day);
            }else if ($(this).val() == template_pulldown[type_customize]) {
                parent.find('.pulldown_container').append(customize);
                checkPulldownBox();
            }else if ($(this).val() == template_pulldown[type_provinces_of_japan]) {
                fillDataPulldownProvinces(parent);
            }else if ($(this).val() == template_pulldown[type_towns_and_villages_of_japan]) {
                parent.append(towns_and_villages);
            }

            if (is_call_select2) {
                initSelect2();
            }
            check_add_item_common();
            getDataFromInput();
            is_call_select2 = false;
        });*/
}
/*event change calendar type*/
function changeCalendarType(){
    var template_calendar = common_data['template_calendar'];
    $(document).on("change", '.calendar_group .calendar_type', function (event) {
        var parent = $(this).closest('.calendar_group');
        var input_start_date = parent.find('.input_start_date').val();
        parent.find('div.is_weekend_off').addClass('hidden');
        if ($(this).val() == template_calendar[type_calendar_select]) {
            parent.find('.embed_calendar').addClass('hidden');
            parent.find('.calendar_period_of_time').addClass('hidden');
            parent.find('.select_calendar').removeClass('hidden');
            parent.find('div.is_weekend_off').removeClass('hidden');
            initDateTimePickerSelect(input_start_date);
        }else if ($(this).val() == template_calendar[type_calendar_embed]) {
            parent.find('.select_calendar').addClass('hidden');
            parent.find('.calendar_period_of_time').addClass('hidden');
            parent.find('.embed_calendar').removeClass('hidden');
            parent.find('.embeb_datepicker').empty().datetimepicker({
                inline: true,
                format: 'Y/MM/DD',
                minDate: (input_start_date != '') ? input_start_date : false
            });
        }else{
            parent.find('.select_calendar').addClass('hidden');
            parent.find('.embed_calendar').addClass('hidden');
            parent.find('.calendar_period_of_time').removeClass('hidden');
            initDateTimePickerPeriodOfTime(input_start_date);
        }
    });
}
/*show/hide checkbox weekend off*/
function changeStatusWeekendOff(group) {
    var template_calendar = common_data['template_calendar'];
    if (group.find('.calendar_type').val() != template_calendar[type_calendar_select]) {
        group.find('div.is_weekend_off').addClass('hidden');
    }
}
function getDateTime(){
    var fullDate = new Date();
    var month = fullDate.getMonth()+1;
    month = (month<10 ? '0' : '') + month;
    var input_start_date = fullDate.getFullYear() + "/" + month + "/" + fullDate.getDate();
    return input_start_date;
}
function initStartDateCalendar() {
    $('.input_start_date').datetimepicker({
        sideBySide: true,
        format: 'Y/MM/DD',
        ignoreReadonly: true,
        // defaultDate: moment()
    }).on('dp.change', function () {
        getDataFromInput();
    });
}
function initDateTimePickerSelect(input_start_date){
    initStartDateCalendar();
    $('.datetimepicker').datetimepicker({
        sideBySide: true,
        format: 'Y/MM/DD',
        ignoreReadonly: true,
        minDate: (input_start_date != '') ? input_start_date : false,
    });
    $('.message_bot_area .select_calendar .input-group-btn').on('click', function (event) {
        $(this).parent('.select_calendar').find('.datetimepicker').focus();
    });
}
function initCalendarWeekendOff(calendar_weekend_off , input_start_date){
    calendar_weekend_off.datetimepicker({
        sideBySide: true,
        format: 'Y/MM/DD',
        ignoreReadonly: true,
        minDate: (input_start_date != '') ? input_start_date : false,
        daysOfWeekDisabled: [0,6]
    });
}
function initDateTimePickerPeriodOfTime(input_start_date){
    initStartDateCalendar();
    $('.startDate').datetimepicker({
        format: 'Y/MM/DD',
        allowInputToggle : true,
        ignoreReadonly: true,
        minDate: (input_start_date != '') ? input_start_date : false
    });
    $('.endDate').datetimepicker({
        useCurrent: false,
        format: 'Y/MM/DD',
        allowInputToggle : true,
        ignoreReadonly: true
    });
    $(".startDate").on("dp.change", function (e) {
        $('.endDate').data("DateTimePicker").minDate(e.date);
    });
    $(".endDate").on("dp.change", function (e) {
        $('.startDate').data("DateTimePicker").maxDate(e.date);
    });
    //
    $('.message_bot_area .calendar_period_of_time .start').on('click', function (event) {
        $(this).closest('.calendar_period_of_time').find('.startDate').focus();
    });
    $('.message_bot_area .calendar_period_of_time .end').on('click', function (event) {
        $(this).closest('.calendar_period_of_time').find('.endDate').focus();
    });
}
/*event change value pasword min max length*/
function passwordLength(){
    $(document).on('input change', '.input_box.active .lengh_password #min_length', function (event) {
        var parent = $(this).closest('.lengh_password');
        parent.find('#max_length').attr('min', $(this).val());
    });
    $(document).on('input change', '.input_box.active .lengh_password #max_length', function (event) {
        var parent = $(this).closest('.lengh_password');
        parent.find('#min_length').attr('max', $(this).val());
    });
}

// init button filter
function initFilterGroup() {
    var filter_group = $('.scenario_block_origin .filter_group').clone();
    if(message_type_bot) {
        $('.message_bot_area').find('.filter_group').remove();
        $('.message_bot_area').prepend(filter_group);
        // checkbox input requiment
        var checkbox_requirement_one = $('.scenario_block_origin .checkbox_requirement_one').clone();
        $('.message_bot_area .fixedsidebar-content').append(checkbox_requirement_one);
        var input_requiment = $('.message_container_input .checkbox_requirement_one').find('input.btn_is_input_requiment');
        initIcheck(input_requiment);
        //button next
        var btn_next_group = $('.scenario_block_origin .text_button_next').clone();
        $('.message_bot_area .fixedsidebar-content').append(btn_next_group);
    } else {
        $('.message_user_area').find('.filter_group').remove();
        $('.message_user_area').prepend(filter_group);
    }
}
/*event add item filter*/
function addFilterItem() {
    var filter_group;
    if(message_type_bot) {
        filter_group = $('.message_bot_area .filter_group');
    } else {
        filter_group = $('.message_user_area .filter_group');
    }

    if(filter_group.length) {
        var filter_clone = $('.scenario_block_origin .filter_item').clone();
        filter_group.find('.filter_contain').append(filter_clone);
        var filter_index = filter_group.find('.filter_contain .filter_item').last().index();
        if(filter_index >= 1 && ($('.template_filter .select_option').length)) {
            var option_clone = $('.template_filter .select_option').clone();
            var parent = filter_group.find('.filter_contain .filter_item').last().prepend(option_clone);
            parent.find('.field_filter_option').addClass('hidden').attr('disabled', 'disabled');
            parent.find('.filter_scenario_value .filter_scenario').addClass('hidden').attr('disabled', 'disabled');
        }
        indexFilterInput();

        //set scroll
        if(filter_group.find('.filter_contain .filter_item').length > 2) {
            $(filter_group).find('.filter_contain').slimscroll({
                height: '140px',
                scrollTo: ($(filter_group).find('.filter_contain .filter_item').length * $(filter_group).find('.filter_contain .filter_item').outerHeight()) + 'px',
            });
        }
        setHeightSlidebar();
    }
}
//reset name for filter input
function indexFilterInput() {
    $('.message_form_area .filter_contain').each(function (i, filter_contain) {
        if($(filter_contain).find('.filter_item').length) {
            $(this).find('.filter_item').each(function (i, filter_item) {
                var prefix_name = 'filter['+ i +']';
                $(filter_item).find('select.filter_variable').attr('name', prefix_name +'[condition]');
                $(filter_item).find('select.filter_operator').attr('name', prefix_name+ '[compare]');
                $(filter_item).find('input.filter_value_text').attr('name', prefix_name+ '[value]');
            });
        }
    });
}
//fill data to right slidebar
function setFilterData(data) {
    $('.filter_contain .filter_item').remove();

    if(data != void 0 && data.length) {
        $(data).each(function (i, e) {
            var filter_item_last;
           /* addFilterItem();
            if(message_type_bot) {
                filter_item_last = $('.message_bot_area .filter_contain .filter_item').last();
            } else {
                filter_item_last = $('.message_user_area .filter_contain .filter_item').last();
            }
            if(filter_item_last != void 0) {
                filter_item_last.find('select.filter_variable').val(e.condition);
                filter_item_last.find('select.filter_operator').val(e.compare);
                filter_item_last.find('input.filter_value_text').val(e.value);
            }*/
           if (e != void 0 && e.length) {
               $(e).each(function (ind, item) {
                    addFilterItem();
                    if(message_type_bot) {
                        filter_item_last = $('.message_bot_area .filter_contain .filter_item').last();
                    } else {
                        filter_item_last = $('.message_user_area .filter_contain .filter_item').last();
                    }
                    if(filter_item_last != void 0) {
                        filter_item_last.find('select.filter_option').val(item.option);
                        filter_item_last.find('select.filter_variable').val(item.condition);
                        filter_item_last.find('select.filter_operator').val(item.compare);
                        filter_item_last.find('input.filter_value_text').val(item.value);
                    }
               })
           }
        });
    }
}
//get filter data
function getFilterData() {
    var index = 0;
    var result = [];
    var message_area = $('.message_user_area');
    if(message_type_bot) {
        message_area = $('.message_bot_area');
    }
    if (message_area.find('.filter_contain .filter_item').length) {
        result[index] = [];
        message_area.find('.filter_contain .filter_item').each(function (i, e) {
            var option_filter = $(this).find('select.filter_option').val(),
                condition = $(this).find('select.filter_variable').val(),
                compare = $(this).find('select.filter_operator').val(),
                value = $(this).find('input.filter_value_text').val();

            if (option_filter != undefined && option_filter == 'or') {
                index++;
                result[index] = [];
            }
            result[index].push({
                'option' : option_filter,
                'condition' : condition,
                'compare' : compare,
                'value' : value
            });
            /*result.push({
                'condition' : condition,
                'compare' : compare,
                'value' : value
            });*/
        });
    }
    return result;
}
function changeOptionSelectMinute(parent, spacing_minute) {
    if (spacing_minute && parent.length) {
        parent.find('option').not(':first').remove();
        setOptionSelectMinute(parent, spacing_minute);
    }

    $(document).on("change", '.time .spacing_minute, .date_time .spacing_minute', function (event) {
       var select_minute = $(this).closest('.pulldown_group.active').find('.minute');
        setOptionSelectMinute(select_minute, $(this).val());
    });
    // period of time change spacing
    $(document).on("change", '.start_time .spacing_minute', function (event) {
        var select_minute = $(this).closest('.pulldown_group.active .start_time').find('.minute');
        setOptionSelectMinute(select_minute, $(this).val());
    });
    $(document).on("change", '.end_time .spacing_minute', function (event) {
        var select_minute = $(this).closest('.pulldown_group.active .end_time').find('.minute');
        setOptionSelectMinute(select_minute, $(this).val());
    });
}

function setOptionSelectMinute(parent, spacing_value) {
    parent.find('option').not(':first').remove();
    for (var i = 1; i < 60; i++) {
        if (spacing_value == '5') {
            if (i % 5 == 0) {
                parent.append('<option value="'+i+'">' + i + '</option>');
            }
        } else if (spacing_value == '10') {
            if (i % 10 == 0) {
                parent.append('<option value="'+i+'">' + i + '</option>');
            }
        }else if (spacing_value == '15') {
            if (i % 15 == 0) {
                parent.append('<option value="'+i+'">' + i + '</option>');
            }
        }else if (spacing_value == '30') {
            if (i % 30 == 0) {
                parent.append('<option value="'+i+'">' + i + '</option>');
            }
        }else {
            parent.append('<option value="'+i+'">' + i + '</option>');
        }
    }

}

function fillDataPulldownProvinces(parent) {
    var pulldown_provinces = common_data['pulldown_provinces'];
    Object.keys(pulldown_provinces).forEach(function (key) {
        var clone = $('.scenario_block_origin .pulldown_box_child .default_pulldown').clone();
        clone.find('.pulldown_label').val(pulldown_provinces[key]);
        parent.find('.pulldown_container').append(clone);
    });
}

function changeTypeTelInput() {
    var tel_input_type = common_data['tel_input_type'],
        one_input = $('.scenario_block_origin .input_box_child .one_input').clone(),
        tel_input_hyphen = $('.scenario_block_origin .input_box_child .tel_input_hyphen').clone(),
        input_clone;

    $(document).on("change", '.input_box .tel_input_type_select', function (event) {
        var parent = $(this).closest('.input_box');
        parent.find('.input_item').remove();
        if ($(this).val() == tel_input_type[type_tel_no_hyphen]) {
            console.log('khong gach');
            input_clone = one_input;
        }else{
            input_clone = tel_input_hyphen;
        }
        parent.append(input_clone);
        parent.find('.change_input').addClass('hide');
        getDataFromInput();
    });
}

function getMessagePosition(element) {
    var action_group = $(element).parents('.action-group');
    if(action_group.length > 0 && action_group.attr('data-append') == '1'){
        return action_group;
    }
    return null;
}

function changeTypeRadio() {
    var template_radio = common_data['template_radio'];
    $(document).on("change", '.radio_group .radio_type', function (event) {
        var parent = $(this).closest('.radio_group');
        parent.find('.radio_box').remove();
        if ($(this).val() == template_radio[type_image_radio]) {
            addRadioImage();
        }else{
            addRadioBox();
        }
    });
}

function changeNumberImageRadio(btn_change) {
    setTimeout(function(){
        var clone,
            list_item_radio = [];
        btn_change.closest('.radio_box').find('.generic_box_content').each(function (index, item) {
            var image_url = $(item).find('input.image_url').val(),
                comment = $(item).find('input.title').val();
            list_item_radio.push({
                'image_url' : image_url,
                'comment' : comment
            });
        });
        var after_item = btn_change.closest('.radio_box').next(),
            number_item = btn_change.closest('.radio_container').find('.radio_box').length;

        btn_change.closest('.radio_box').remove();
        if (btn_change.hasClass('increment_to_two') || btn_change.hasClass('decrement_to_two')){
            clone = $('.scenario_block_origin .radio_two_image').clone();
            clone.find('.generic_box_content').each(function (index, item) {
                if (list_item_radio[index] != undefined) {
                    $(item).find('input.image_url').val(list_item_radio[index].image_url);
                    $(item).find('input.title ').val(list_item_radio[index].comment);
                }
            });
        }else if (btn_change.hasClass('decrement_to_one')) {
            clone = $('.scenario_block_origin .radio_image').clone();
            if (list_item_radio[0] != undefined) {
                clone.find('input.image_url').val(list_item_radio[0].image_url);
                clone.find('input.title ').val(list_item_radio[0].comment);
            }
        }else if (btn_change.hasClass('increment_to_three')) {
            clone = $('.scenario_block_origin .radio_three_image').clone();
            clone.find('.generic_box_content').each(function (index, item) {
                if (list_item_radio[index] != undefined) {
                    $(item).find('input.image_url').val(list_item_radio[index].image_url);
                    $(item).find('input.title ').val(list_item_radio[index].comment);
                }
            });
        }

        if (number_item == 1 || !after_item.hasClass('radio_box')) {
            $('.message_container_input .radio_group.active .radio_container').append(clone);
        }else {
            clone.insertBefore(after_item);
        }
        checkRadioBox();
        getDataFromInput();
    }, 100);
}

function deleteButtonAction() {
    $('.scenario_block .action-group').each(function (i, e) {
        var button_action_next = $(this).next();
        if(button_action_next.hasClass('action-group') || button_action_next.length == 0){
            $(this).remove();
        }
    });
}

function previewCaptcha(parent) {
    var src = '',
        captcha_type = parent.find('.captcha_type').val(),
        size = parent.find('.size').val(),
        captcha_color = parent.find('.captcha_color').val(),
        template_captcha = common_data['template_captcha'];
    parent.find('.preview_captcha').attr('src', '');
    if (captcha_color == 1) {
        captcha_color = true;
    }
    if (captcha_type == template_captcha[type_captcha_only_numbers]) {
        if (captcha_color == true) {
            src = 'http://app.botchan.chat/captchapreview?size='+ size +'&color='+ captcha_color +'&charPreset=1234567890';
        }else {
            src = 'http://app.botchan.chat/captchapreview?size='+ size +'&charPreset=1234567890';
        }
    }else if (captcha_type == template_captcha[type_captcha_only_letters]) {
        if (captcha_color == true) {
            src = 'http://app.botchan.chat/captchapreview?size='+ size +'&color='+ captcha_color +'&charPreset=ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }else {
            src = 'http://app.botchan.chat/captchapreview?size='+ size +'&charPreset=ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
    }else {
        if (captcha_color == true) {
            src = 'http://app.botchan.chat/captchapreview?size='+ size +'&color='+ captcha_color +'&charPreset=ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        }else {
            src = 'http://app.botchan.chat/captchapreview?size='+ size +'&charPreset=ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        }
    }
    parent.find('.preview_captcha').attr('src', src);
}

function checkUseRequiment() {
    var msg_group_content = $('.message_container_input .message_bot_area .fixedsidebar-content');
    var checkbox_requirement_input = $('.message_bot_area .fixedsidebar-content .checkbox_requirement_one');
    // var requirement_one_active = true;
    // var requirement_one_active = false;
    var requirement_one_active = true;

    //not show if in block message exist any type message below
    if (msg_group_content.find('.type_group').length < 2
     || (msg_group_content.find('.label_group').length && (msg_group_content.find('.input_box').length < 2))
     || msg_group_content.find('.captcha_group').length
     || msg_group_content.find('.terms_of_use_group').length
     || msg_group_content.find('.textarea_box').length
     || msg_group_content.find('.radio_group').length
     || msg_group_content.find('.checkbox_group').length
     || msg_group_content.find('.pulldown_group').length
     || msg_group_content.find('.postal_code_box').length
     || msg_group_content.find('.upload_file_group').length
     || msg_group_content.find('.calendar_group').length
     || msg_group_content.find('.confirmation_group').length
     || msg_group_content.find('.generic_group').length
     || msg_group_content.find('.card_payment_group').length
     || msg_group_content.find('.add_to_cart_group').length
     || msg_group_content.find('.request_document_group').length
     ) {
         requirement_one_active = false;
     }

    icheck_change_event = false;
    if (requirement_one_active) {
        checkbox_requirement_input.removeClass('hidden');
        if (checkbox_requirement_input[0].checked) {
            //un-check require message checkbox
            icheckAuto('uncheck', msg_group_content.find('.type_group .is_required'));
        }
    } else {
        icheckAuto('uncheck', msg_group_content.find('input.btn_is_input_requiment'));
        checkbox_requirement_input.addClass('hidden');
        msg_group_content.find('.type_group .is_required').show();
    }
    icheck_change_event = true;
}

//check active requirement input then uncheck and hide input require message
function checkActiveRequirementOne() {
    var msg_group_content = $('.message_container_input .message_bot_area .fixedsidebar-content'),
        is_input_requiment = msg_group_content.find('input.btn_is_input_requiment')[0].checked,
        input_required_list = msg_group_content.find('.type_group .is_required');

    if(is_input_requiment != void 0 && is_input_requiment){
        input_required_list.hide();
        icheckAuto('uncheck', input_required_list);
    }else {
        input_required_list.show();
    }
}
//change template document
function changeTypeDocument(){
    $('.request_document_group.active .document_type').on('change', function (e) {
        var parent = $(this).closest('.request_document_group.active');
        demoTemplateDocument(parent);
    });
}
//show/hide for document type
function demoTemplateDocument(parent) {
    var template_document = common_data['template_document'];
    parent.find('.image_document').addClass('hide');
    parent.find('.sub_title_document').addClass('hide');
    if (parent.find('.document_type').val() == template_document[type_image_document]) {
        //image, title & subtitle
        parent.find('.image_document').removeClass('hide');
        parent.find('.sub_title_document').removeClass('hide');
    }else if (parent.find('.document_type').val() == template_document[type_title_document]) {
        //title only
        parent.find('.image_document').addClass('hide');
        parent.find('.sub_title_document').addClass('hide');
    }else {
        //title & subtitle
        parent.find('.sub_title_document').removeClass('hide');
    }
}

function btnAddItem() {
    $('.message_bot_area .type_group.active .add_item_common').on('click', function (event) {
        setTimeout(function(){
            var bot_message_type    = botMessageType(),
                type                = common_data['bot_content_type'];
            if(bot_message_type == type[type_radio]) {
                var msg_active = getMessageFormActive(),
                    template_radio = common_data['template_radio'];
                if (msg_active.find('.radio_type').val() == template_radio[type_image_radio]) {
                    addRadioImage();
                }else {
                    addRadioBox();
                }
            } else if(bot_message_type == type[type_checkbox]) {
                addCheckboxBox();
            }else if(bot_message_type == type[type_pulldown]) {
                addItemPulldown();
            }else if(bot_message_type == type[type_terms_of_use]) {
                addItemLink();
            }
        }, 100);
    });
}

function setValMessageType(value) {
    $('.footer_message_input #bot_content_type').val(value);
}

//check number message in block message
function checkTotalMessageInBlock() {
    var message_bot_content = $('.message_bot_area .fixedsidebar-content'),
        msg_in_block_num = message_bot_content.find('.type_group').length;

    if(!msg_in_block_num) {
        var clone = $('.scenario_block_origin .block_message_empty').clone();
        message_bot_content.append(clone);
    } else {
        message_bot_content.find('.block_message_empty').remove();
    }
    return msg_in_block_num;
}

$( window ).resize(function() {
    setHeightSlidebar();
});
