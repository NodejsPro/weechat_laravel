//globe variable
var common_data         = [];
var type_text           = 'text';
var type_button         = 'button';
var type_generic        = 'generic';
var type_file           = 'file';
var type_library        = 'library';
var type_quick_replies  = 'quick_replies';
var type_api            = 'api';
var type_scenario_connect = 'scenario_connect';
var type_mail           = 'mail';
var type_api_variable_setting = 'api_variable_setting';
var max_button_block    = 3;
var max_generic_item    = 10;
var max_generic_button  = 3;
var max_quick_replies_button  = 11;
var icheck_change_event = true;
var conversation_page = false;

$(function () {
    if($('.scenario-edit.content_message').length) {
        conversation_page = true;
        $('#bot_content_type option').first().remove();
    }
    message_area_select(null);

    ///////////////////////START ACTION USER MESAGE
    //get type of bot input textarea
    $('.scenario-body').on('click', '.user_scenario', function (event) {
        //hide message bot area
        message_area_select(false);
        var messages_focus      = $('input.messages_focus'),
            user_message_name   = $(this).find('.messages_user_content').attr('name');
        if (messages_focus.val() != user_message_name) {
            var messages_user_type = $(this).find('input.messages_user_type').val();
            //set active for bot scenario
            $('.scenario-body .bot_scenario, .scenario-body .user_scenario').removeClass('active');
            $(this).addClass('active');
            //get type from bot message and set to sliderbar data
            $('#user_content_type').val(messages_user_type);
            //set name user message to right slidebar
            messages_focus.val(user_message_name);
            //set flag icheck_change_event do not icheck change event handle below action
            icheck_change_event = false;
            checkContentUserMessage(messages_user_type);
            setUserDataToForm();
            icheck_change_event = true;
        }
    });

    //Change select content type
    $('#user_content_type').on('change', function (event) {
        var user_current        = userMessageElement(),
            user_current_type   = user_current.next('input.messages_user_type'),
            user_input_text     = userInputText();
        //change type for bot message
        user_current_type.val($(this).val());
        //check text type and clear content user message
        user_current.val('');

        user_input_text.importTags('');
        setUserDataMediaDemo(true);
        //set flag icheck_change_event do not icheck change event handle below action
        icheck_change_event = false;
        checkContentUserInput($(this).val());
        icheck_change_event = true;
        createTag();
    });

    //TEXT TYPE
    //init tags input and  change value for user message
    $('.message_user_area .text_group input.user_text_type').tagsInput({
        'onChange' : function () {
            getDataUserText();
        }
    });

    //delete tag and re-set value to user message
    $(document).on('click', '.user_scenario .tagit-close', function (event) {
        var ulParent        = $(this).parent().parent(),
            user_current    = userMessageElement(),
            user_type       = userMessageType(),
            type            = common_data['user_content_type'];
        $(this).parent().remove();
        /*update user input*/
        //case library: remove tag and un-select that library
        if(user_type == type[type_library]) {
            var lib_label_del   = $(this).parent().find(".taget-label").html();
            //LIBRARY[library_name]
            lib_label_del = lib_label_del.substring(8, lib_label_del.length - 1);
            //unselect and re-init tag
            icheckAuto('uncheck', $(".message_user_area .library_group input.library_list[data-name='" + lib_label_del + "']"));
            getDataUserFromInput(type_library);

        } else {
            var arr_text  = new Array(),
                arr_label = $(ulParent).find(".taget-label");
            $(arr_label).each(function (index, value) {
                arr_text.push($(value).text());
            });
            user_current.val(arr_text.join(','));
            //re-fill to input sliderbar
            setUserDataToForm();
        }
    });

    //checkbox for use nlp
    $(document).on('ifChanged', '.message_user_area input.use_nlp', function (event) {
        var user_message_type    = userMessageType(),
            type                 = common_data['user_content_type'];

        if(user_message_type == type[type_text]) {
            var group_message   = $('.message_user_area .text_group');

            if(group_message) {
                var use_nlp = group_message.find('input.use_nlp:checked').val(),
                    select_nlp = group_message.find('select.select_nlp');

                if(use_nlp != void 0 && use_nlp) {
                    select_nlp.removeClass('hidden');
                } else {
                    select_nlp.addClass('hidden');
                }
            }
            if(icheck_change_event) {
                getDataUserFromInput(type_text);
            }
        }
    });

    $('.message_user_area select.select_nlp').on('change', function(event){
        var user_message_type    = userMessageType(),
            type                 = common_data['user_content_type'];

        if(user_message_type == type[type_text]) {
            getDataUserFromInput(type_text);
        }
    });

    //LIBRARY TYPE
    /**
     * select libraries event
     */
    $('.message_user_area .library_group input.library_list').on('ifChanged', function(event){
        var user_message_type    = userMessageType(),
            type                 = common_data['user_content_type'];

        if(user_message_type == type[type_library] && icheck_change_event == true) {
            getDataUserFromInput(type_library);
        }
    });

    //check library when click to label
    $('.message_user_area .library_group .library_label_box').on('click', function(event){
        $(this).prev('.library_input_box').iCheck('toggle');
    });

    //API VARIABLE SETTING TYPE
    $('.message_user_area .api_variable_setting_group select.api_variable').select2({
        minimumResultsForSearch: -1
    });
    $('.message_user_area .api_variable_setting_group select.api_variable, .message_user_area .api_variable_setting_group select.select_variable').on('change', function(event){
        getDataUserFromInput(type_api_variable_setting);
    });

    //COMMON USER TYPE
    $('.message_user_area select.select_variable').on('change', function(event){
        var user_message_type    = userMessageType(),
            type                 = common_data['user_content_type'],
            message_type         = '';

        switch (user_message_type) {
            case type[type_text]:
                message_type    = type_text;
                break;
            case type[type_library]:
                message_type    = type_library;
                break;
            case type[type_api_variable_setting]:
                message_type    = type_api_variable_setting;
                break;
        }
        if(message_type) {
            getDataUserFromInput(message_type);
        }
    });

    //checkbox for use variable
    $(document).on('ifChanged', '.message_user_area input.use_variable', function (event) {
        var user_message_type    = userMessageType(),
            type                 = common_data['user_content_type'],
            group_message        = '',
            message_type         = '';

        switch (user_message_type) {
            case type[type_text]:
                group_message   = $('.message_user_area .text_group');
                message_type    = type_text;
                break;
            case type[type_library]:
                group_message   = $('.message_user_area .library_group');
                message_type    = type_library;
                break;
            case type[type_api_variable_setting]:
                group_message   = $('.message_user_area .api_variable_setting_group');
                message_type    = type_api_variable_setting;
                break;
        }

        if(group_message) {
            var use_variable = group_message.find('input.use_variable:checked').val(),
                select_variable = group_message.find('select.select_variable');

            if(use_variable != void 0 && use_variable) {
                select_variable.removeClass('hidden');
            } else {
                select_variable.addClass('hidden');
            }
        }
        if(message_type && icheck_change_event) {
            getDataUserFromInput(message_type);
        }
    });

    /**
     * focus user library message when click to own answer
     */
    $(document).on('click', '.bot_virtual_box', function (event) {
        $(this).prev('.user_scenario').click();
    });

    ///////////////////////START ACTION BOT MESAGE
    //get type of bot input textarea
    $('.scenario-body').on('click', '.bot_scenario', function (event) {
        var messages_focus      = $('input.messages_focus'),
            bot_message_name    = $(this).find('.messages_bot_content').attr('name');

        if (messages_focus.val() != bot_message_name) {
            //set active for bot scenario
            $('.scenario-body .bot_scenario, .scenario-body .user_scenario').removeClass('active');
            $(this).addClass('active');

            var messages_bot_type = $(this).find('input.messages_bot_type').val();

            //get type from bot message and set to sliderbar data
            $('#bot_content_type').val(messages_bot_type);

            //set name bot message to right slidebar
            messages_focus.val(bot_message_name);

            checkContentBotMessage(messages_bot_type);
            setBotDataToForm();
            setHeightSlidebar();
        }
    });

    //Change select content type
    $('#bot_content_type').on('change', function (event) {
        var bot_message_current         = botMessageElement(),
            bot_message_current_type    = bot_message_current.next('input.messages_bot_type');
        //check text type and clear content bot message
        var old_type = bot_message_current_type.val();
        if(old_type != $(this).val()) {
            bot_message_current.val('');
        }
        //change type for bot message
        bot_message_current_type.val($(this).val());

        setDataMediaDemo(true);
        checkContentBotInput($(this).val());
        setHeightSlidebar();
        if ($('.conversation_index').length){
            $('.fixedsidebar-content').slimscroll({
                height: '250px',
            });
        }
    });

    //event add user + bot
    $('.add_pattern_user').on('click', function (event) {
        if(conversation_page) {
            cloneBot(true);
        } else {
            cloneUser();
            cloneBot();
        }
    });
    //event add bot
    $('.add_pattern_bot').on('click', function (event) {
        cloneBot(true);
    });

    // add button item
    $('.message_bot_area .footer_message_input .add_item_common').on('click', function (event) {
        var bot_message_type    = botMessageType(),
            type                = common_data['bot_content_type'];

        if(bot_message_type == type[type_button]) {
            addButtonBox();
        } else if(bot_message_type == type[type_quick_replies]) {
            addQuickRepliesBox();
        } else if(bot_message_type == type[type_generic]) {
            addGenericButton();
        }
    });

    // add generic item
    $('.message_bot_area .add_group_box .add_item_generic').on('click', function (event) {
        createGenericBox();
    });

    //*** AJAX ON ***
    // ajax update scenario
    $('.updateScenario').on('click', function (event) {
        validateMessages();
        updateScenario();
    });

    //-----------------start set value from right sliderbar to bot message
    //TEXT
    $('.message_bot_area .text_group .input_text_type').on('change', function (event) {
        getDataFromInput(type_text);
    });

    //auto suggest variable by textcomplete
    var variable_list = common_data['variable_list'];
    $('.message_bot_area .text_group .input_text_type').textcomplete([
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
    });

    //select file for file and generic type
    $('#scenario-file-list .btn-file-select').on('click', function () {
        var bot_message_type    = botMessageType(),
            type                = common_data['bot_content_type'],
            file_path_input     = $('#scenario-file-list #scenario_file_selected'),
            file_path           = file_path_input.val();

        //set file url for image input
        if(file_path != '' && file_path != void 0) {
            if(bot_message_type == type[type_file]) {
                $('.file_group .file_url_input').val(file_path);
                getDataFromInput(type_file);
            } else if(bot_message_type == type[type_generic]) {
                var genericActive = genericActiveElement();
                genericActive.find('input.image_url').val(file_path);
                getDataFromInput(type_generic);
            }
            //clear val file_path_input in popup file list
            file_path_input.val('');
        }
    });
    //FILE
    $('.file_group .file_url_input').on('change', function (event) {
        getDataFromInput(type_file);
    });
    $('.file_group .file_url_input').on('keypress', function (event) {
        getDataFromInput(type_file);
    });

    //BUTTON
    $(document).on('keypress', '.button_group input', function (event) {
        getDataFromInput(type_button);
    });
    $(document).on('change', '.button_group input, .button_group select', function (event) {
        getDataFromInput(type_button);
    });
    //checkbox for use variable in button
    $(document).on('ifChanged', '.button_group #btn_is_variable', function (event) {
        var btn_group    = $('.button_group'),
            is_variable = btn_group.find('input#btn_is_variable:checked').val(),
            variable    = btn_group.find('select.button_variable');
        if(is_variable != void 0 && is_variable) {
            variable.removeClass('hidden');
        } else {
            variable.addClass('hidden');
        }
        if(icheck_change_event) {
            getDataFromInput(type_button);
        }
    });

    //GENERIC
    $(document).on('keypress', '.generic_box input', function (event) {
        getDataFromInput(type_generic);
    });
    $(document).on('change', '.generic_box input, .generic_box select', function (event) {
        getDataFromInput(type_generic);
    });
    //--------------------------------end set value from right sliderbar to message box

    //QUICK REPLIES
    //checkbox for use variable in quick reply
    $(document).on('ifChanged', '.quick_replies_group #qr_is_variable', function (event) {
        var qr_group    = $('.quick_replies_group'),
            is_variable = qr_group.find('input#qr_is_variable:checked').val(),
            variable    = qr_group.find('select.quick_replies_variable');
        if(is_variable != void 0 && is_variable) {
            variable.removeClass('hidden');
        } else {
            variable.addClass('hidden');
        }
        checkVariableTextBox();
        if(icheck_change_event) {
            getDataFromInput(type_quick_replies);
        }

        //show, hide button_variable_code_box in quickreply group
        function checkVariableTextBox() {
            var template_quick_replies = common_data['template_quick_replies'];

            qr_group.find('.quick_replies_box').each(function (index, element) {
                var button_sub_type = $(this).find('.button_type_box select.button_sub_type').val(),
                    btn_variable_text_box = $(this).find('.button_variable_code_box');

                if(is_variable != void 0 && is_variable && button_sub_type == template_quick_replies['text']) {
                    btn_variable_text_box.removeClass('hidden');
                } else {
                    btn_variable_text_box.addClass('hidden');
                }
            });
        }
    });
    //checkbox for use scenario in button quick reply
    $(document).on('ifChanged', '.quick_replies_group input.qr_is_scenario', function (event) {
        var qr_box      = $(this).parents('.quick_replies_box'),
            is_scenario = qr_box.find('input.qr_is_scenario:checked').val(),
            scenario    = qr_box.find('select.button_sub_scenario');

        if(is_scenario != void 0 && is_scenario) {
            scenario.removeClass('hidden');
        } else {
            scenario.addClass('hidden');
        }
        if(icheck_change_event) {
            getDataFromInput(type_quick_replies);
        }
    });
    //checkbox for use is_keyword_matching in quick reply
    $(document).on('ifChanged', '.quick_replies_group #user_keyword_matching', function (event) {
        if(icheck_change_event) {
            getDataFromInput(type_quick_replies);
        }
    });
    $(document).on('keypress', '.quick_replies_group input', function (event) {
        getDataFromInput(type_quick_replies);
    });
    $(document).on('change', '.quick_replies_group input, .quick_replies_group select', function (event) {
        getDataFromInput(type_quick_replies);
    });
    $(document).on('change', '.quick_replies_group select.button_sub_type', function (event) {
        var type = common_data['template_quick_replies'],
            box  = $(this).parents('.quick_replies_box');
        if($(this).val() == type['location']) {
            box.find('.button_title_box, .button_scenario_box, .button_image_box').addClass('hidden');
            box.find('input.button_sub_title').removeClass('validate-require');
        } else {
            box.find('.button_title_box, .button_scenario_box, .button_image_box').removeClass('hidden');
            box.find('input.button_sub_title').addClass('validate-require');
        }
        checkButtonVariableTextBox();

        //show, hide button_variable_code_box in button box
        function checkButtonVariableTextBox() {
            var is_variable = $('.quick_replies_group input#qr_is_variable:checked').val(),
                btn_variable_text_box = box.find('.button_variable_code_box'),
                button_sub_type = box.find('.button_type_box select.button_sub_type').val();

            if(is_variable != void 0 && is_variable && button_sub_type == type['text']) {
                btn_variable_text_box.removeClass('hidden');
            } else {
                btn_variable_text_box.addClass('hidden');
            }
        }
    });

    //API
    $('.message_bot_area .api_group select.api_select').select2({
        minimumResultsForSearch: -1
    });
    //select api event
    $('.message_bot_area .api_group select.api_select').on('change', function(event){
        getDataFromInput(type_api);
    });


    //SCENARIO CONNECT
    $('.message_bot_area .scenario_connect_group select.scenario_select').select2({
        minimumResultsForSearch: -1
    });
    //select api event
    $('.message_bot_area .scenario_connect_group select.scenario_select').on('change', function(event){
        getDataFromInput(type_scenario_connect);
    });

    //MAIL
    $('.message_bot_area .mail_group select.mail_select').select2({
        minimumResultsForSearch: -1
    });
    //select api event
    $('.message_bot_area .mail_group select.mail_select').on('change', function(event){
        getDataFromInput(type_mail);
    });

    //delete user message box
    $(document).on('click', '.user_scenario .delete_user_box', function (event) {
        deleteBotVirtual();
        $(this).parent('.user_scenario').remove();
        message_area_select(null);
        generateNameMessageBox();
    });

    //delete bot message box
    $(document).on('click','.bot_scenario .delete_bot_box',function(e){
        $(this).parent('.bot_scenario').remove();
        message_area_select(null);
        generateNameMessageBox();
        $('input.messages_focus').val('');
    });

    // remove button block
    $(document).on("click", '.btn_box .delete_btn_box', function (event) {
        if($('.button_group .button_container .btn_box').length > 1) {
            $(this).parents('.btn_box').remove();
            checkButtonBox();
            getDataFromInput(type_button);
        }
    });

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
            getDataFromInput(type_generic);
            check_add_item_common();
            setHeightSlidebar();
        }
    });

    // remove quick replies block
    $(document).on("click", '.quick_replies_group .delete_btn_box', function (event) {
        if($('.quick_replies_group .quick_replies_box').length > 1) {
            $(this).parents('.quick_replies_box').remove();
            checkQuickReplyButtonBox();
            getDataFromInput(type_quick_replies);
        }
    });

    // remove generic button block
    $(document).on("click", '.button_type_container .delete_btn_box', function (event) {
        var generic_element = $(this).parents('.generic_box');
        if(generic_element.find('.button_type_container').length > 0) {
            $(this).parents('.button_type_container').remove();
            checkGenericButtonBox();
            getDataFromInput(type_generic);
        }
    });

    //add a message block if not exist any user message and bot message
    if(!$('.scenario_block .user_scenario').length && !$('.scenario_block .bot_scenario').length) {
        $('.add_pattern_bot').click();
    }

    //re-sort position message box
    $('.scenario_block').sortable({
        items: ".message_box",
        cancel: ".bot_media_demo, .user_form_box",
        activate: function(event, ui) {
            $('.scenario_block .bot_virtual_box').remove();
        },
        stop: function(event, ui) {
            //re-generate bot for message library
            generateBotVirtual();
            generateNameMessageBox();
            //update name of message box focusing to messages_focus input
            var msg_focusing = getMessageBoxActive(),
                msg_name     = '';
            if(msg_focusing.hasClass('user_scenario')) {
                msg_name = msg_focusing.find('input.messages_user_content').attr('name');
            } else if(msg_focusing.hasClass('bot_scenario')) {
                msg_name = msg_focusing.find('textarea.messages_bot_content').attr('name');
            }
            $('input.messages_focus').val(msg_name);
        }
    });

    //generate virtual bot message for user type
    generateBotVirtual();

    //generate name for input message block
    generateNameMessageBox();

    //carousel slide when select generic message
    $('.generic_carousel').bind('slid.bs.carousel', function (e) {
        checkCarouselSlide();
        checkGenericButtonBox();
    });
    uploadFile();

    //disable submit message form
    $(".scenario-edit form.scenarioForm").submit(function (e) {
        e.preventDefault();
    });
});

/**
 * get data from slidebar input for message box by type
 * @param type
 */
function getDataUserFromInput(type) {
    switch (type) {
        case type_text: getDataUserText(); break;
        case type_library: getDataUserLibrary(); break;
        case type_api_variable_setting: getDataUserApiVariableSetting(); break;
    }
    setUserVariable(type);
    setUserDataMediaDemo();
}

/**
 * get data text fill to message box
 */
function getDataUserText() {
    var user_message = userMessageElement(),
        user_input_text = userInputText();
    user_message.val(user_input_text.val());
    setUserNlp(type_text);
    createTag();
}

/**
 * get option selected to user input
 */
function getDataUserLibrary() {
    var user_current    = userMessageElement(),
        type = common_data['user_content_type'],
        lib_value       = $.map($('.message_user_area .library_group input.library_list:checked'), function(c){return c.value;});

    if(lib_value.length > 0) {
        lib_value = lib_value.join(',');
    }
    user_current.val(lib_value);
    createTag();
}

/**
 * get data api variable setting fill to message box
 */
function getDataUserApiVariableSetting() {
    var user_message = userMessageElement(),
        api_variable = apiVariableSettingElement();

    user_message.val(api_variable.val());
}

/**
 * create tag by value in active input user message
 */
function createTag() {
    var user_box        = userMessageBoxElement(),
        user_type       = userMessageType(),
        user_value      = userMessageContent(),
        tagit_box       = user_box.find('ul.tagit'),
        type            = common_data['user_content_type'];

    tagit_box.find('li').remove();
    if(user_value) {
        $(user_value.split(',')).each(function (index, value) {
            if(value) {
                value = $.trim(value);
                var cloneChar = $('.scenario_block_origin .tagit-choice-block .tagit-choice').clone();
                //if type is library then get label to show tag
                if(user_type == type[type_library]) {
                    var library_item   = libraryListElement(value),
                        library_label  = library_item.data('name'),
                        label_bot_smg  = $('.scenario_block_origin .bot_virtual_box .bot_library_view').data('label');
                    value = label_bot_smg + '[' + library_label + ']';
                }
                cloneChar.find(".taget-label").text(value);
                tagit_box.append(cloneChar);
            }
        });
    }
}

/**
 * set select variable in user message
 * @param user_message_type
 */
function setUserVariable(user_message_type) {
    var group_message   = '',
        select_variable = '';

    switch (user_message_type) {
        case type_text:
            group_message   = $('.message_user_area .text_group');
            break;
        case type_library:
            group_message   = $('.message_user_area .library_group');
            break;
        case type_api_variable_setting:
            group_message   = $('.message_user_area .api_variable_setting_group');
            break;
    }

    //get variable to imput message
    var use_variable = group_message.find('input.use_variable:checked').val();
    if(use_variable != void 0 && use_variable && group_message.find('select.select_variable').val()) {
        select_variable = group_message.find('select.select_variable').val();
    }
    userMessageVariable(select_variable);
}

/**
 * set select nlp in user message
 * @param user_message_type
 */
function setUserNlp(user_message_type) {
    var group_message   = '',
        select_nlp = '';

    switch (user_message_type) {
        case type_text:
            group_message   = $('.message_user_area .text_group');
            break;
        case type_library:
            group_message   = $('.message_user_area .library_group');
            break;
        case type_api_variable_setting:
            group_message   = $('.message_user_area .api_variable_setting_group');
            break;
    }

    //get variable to imput message
    var use_nlp = group_message.find('input.use_nlp:checked').val();
    if(use_nlp != void 0 && use_nlp && group_message.find('select.select_nlp').val()) {
        select_nlp = group_message.find('select.select_nlp').val();
    }
    userMessageNlp(select_nlp);
}

/**
 * set data for common variable
 */
function setDataCommon(key, value) {
    common_data[key] = value;
}

//add new user box
function cloneUser() {
	var clone = $('.scenario_block_origin .user_scenario').clone();
	$('.scenario_block').append(clone);
}

//add new bot box
function cloneBot(focus) {
	var clone = $('.scenario_block_origin .bot_scenario').clone();
    $('.scenario_block').append(clone);
    //reset name for mesage input
    generateNameMessageBox();
    //focus to new bot box
    if(focus != void 0 && focus) {
        clone.click();
    } else {
        $('.scenario_block .message_box.user_scenario').last().click();
    }
}

//add new bot library box after user library mesage
function cloneBotVirtual(after_elm, type_message) {
    if(after_elm == void 0 || after_elm == '') {
        var user_message = userMessageBoxElement();
        after_elm = user_message.parents('.user_scenario');
    }
    if(!after_elm.next().hasClass('bot_virtual_box')) {
        var clone_bot = $('.scenario_block_origin .bot_virtual_box').clone();
        after_elm.after(clone_bot);
    }

    //re show, hide for each type user message
    var type = common_data['user_content_type'],
        bot_library_view = after_elm.next().find('.bot_library_view'),
        bot_api_view = after_elm.next().find('.bot_api_variable_setting_view');
    switch (type_message) {
        case type[type_library]:
            bot_library_view.removeClass('hidden');
            bot_api_view.addClass('hidden');
            break;
        case type[type_api_variable_setting]:
            bot_library_view.addClass('hidden');
            bot_api_view.removeClass('hidden');
            break;
    }
}

//Generate for all bot library
function generateBotVirtual() {
    var type = common_data['user_content_type'];
    $('.scenario_block .user_scenario').each(function (i, e) {
        var bot_type = $(this).find('input.messages_user_type').val();
        if(bot_type && (bot_type == type[type_library] || bot_type == type[type_api_variable_setting])) {
            cloneBotVirtual($(this), bot_type);
        }
    });
}

//add new bot virtual box after user library mesage
function deleteBotVirtual() {
    var user_message    = userMessageBoxElement();
    user_message.parents('.user_scenario').next('.bot_virtual_box').remove();
}

/**
 * add new button block
 */
function addButtonBox() {
    if($('.button_group .button_container .btn_box').length < max_button_block) {
        var clone = $('.scenario_block_origin .btn_box').clone();
        $('.button_group .button_container').append(clone);
        checkButtonBox();
    }
}

/**
 * add new generic child button
 */
function addGenericButton() {
    var generic_active = genericActiveElement();
    if(generic_active.find('.button_type_container').length < max_generic_button) {
        var clone_button = $('.scenario_block_origin .generic_button_template .button_type_container').clone();
        generic_active.find('.generic_button_container').append(clone_button);
        checkGenericButtonBox();
    }
}


/**
 * add new quick reply block
 */
function addQuickRepliesBox() {
    if($('.quick_replies_group .quick_replies_box').length < max_quick_replies_button) {
        var is_variable = $('.quick_replies_group input#qr_is_variable:checked').val(),
            clone = $('.scenario_block_origin .quick_replies_box').clone();

        if(is_variable != void 0 && is_variable) {
            clone.find('.button_variable_code_box').removeClass('hidden');
        }
        $('.quick_replies_group .quick_replies_container').append(clone);
        //re-init icheck
        initIcheck($('.quick_replies_group input.qr_is_scenario'));

        checkQuickReplyButtonBox();
    }
}

/**
 * add new generic block
 */
function createGenericBox() {
    if($('.generic_group .generic_container .generic_box').length < max_generic_item) {
        var clone = $('.scenario_block_origin .generic_box').clone();
        $('.generic_container').append(clone);
        checkGenericBox();

        //------Carousel process
        //param false: set active for tem
        addCarouselIndicator('generic_indicators', false);
        checkCarouselSlide();

        //check hide, show button add new button for generic
        checkGenericButtonBox();
    }
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

// set file size
function formatFileSize(bytes) {
    if (typeof bytes !== 'number') {
        return '';
    }
    if (bytes >= 1000000000) {
        return (bytes / 1000000000).toFixed(2) + ' GB';
    }
    if (bytes >= 1000000) {
        return (bytes / 1000000).toFixed(2) + ' MB';
    }
    return (bytes / 1000).toFixed(2) + ' KB';
}

//add Carousel indicators follow generic item
function addCarouselIndicator(parent_class, first_active) {
    $('.' + parent_class).append('<li data-slide-to="0" data-target="#c-slide"></li>');

    //re-set index for generic indicators
    $('.generic_indicators li').each(function (index, value) {
        $(this).attr('data-slide-to', index);
    });
    //set active for first indicators and first generic_box
    var indicator = $('.generic_indicators'),
        container = $('.generic_container');

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

/**
 * generate name for user and bot message input
 */
function generateNameMessageBox() {
    var position        = 0,
        message_type    = common_data['message_type'];
    //set index (position) for mesage block
    $('.scenario_block').children().each(function (index, element) {
        if($(this).hasClass('user_scenario')) {
            var prefix_user = 'message[' + message_type['user'] + '][' + position + ']';
            $(this).find('.messages_user_content').attr('name', prefix_user + '[content]');
            $(this).find('.messages_user_type').attr('name', prefix_user + '[type]');
            $(this).find('.messages_user_variable').attr('name', prefix_user + '[variable]');
            $(this).find('.messages_user_nlp').attr('name', prefix_user + '[nlp]');
            position++;
        }
        if($(this).hasClass('bot_scenario')) {
            var prefix_bot = 'message[' + message_type['bot'] + '][' + position + ']';
            $(this).find('.messages_bot_content').attr('name', prefix_bot + '[content]');
            $(this).find('.messages_bot_type').attr('name', prefix_bot + '[type]');
            position++;
        }
    });
}

//hide, show add_group_box button
function checkAddGroupButtonBox() {
    var add_group_btn       = $('.message_bot_area .add_group_box'),
        bot_message_type    = botMessageType(),
        type                = common_data['bot_content_type'],
        item_btn_count,
        max_num;

    if(bot_message_type == type[type_generic]) {
        item_btn_count      = $('.generic_group .generic_container .generic_box').length;
        max_num             = max_generic_item;
    }
    if(item_btn_count >= max_num) {
        add_group_btn.hide();
    } else {
        add_group_btn.show();
    }
}

//hide, show by check number button box
function checkButtonBox() {
    var button_box_count    = $('.button_group .button_container .btn_box').length,
        delete_button       = $('.message_container_input .btn_box .delete_btn_box');
    //show, hide delete button box
    if(button_box_count <= 1) {
        delete_button.hide();
    } else {
        delete_button.show();
    }
    check_add_item_common();
}

//hide, show by check number generic button box
function checkGenericButtonBox() {
    var button_box          = genericActiveElement(),
        button_box_count    = $(button_box).find('.generic_button_container .button_type_container').length,
        delete_button       = $(button_box).find('.delete_btn_box');
    //show, hide delete button
    if(button_box_count <= 0) {
        delete_button.hide();
    } else {
        delete_button.show();
    }
    check_add_item_common();
}

//hide, show by check number quick replies button box
function checkQuickReplyButtonBox() {
    var button_box_count    = $('.quick_replies_group .quick_replies_box').length,
        delete_button       = $('.quick_replies_group .delete_btn_box');
    //show, hide delete button box
    if(button_box_count <= 1) {
        delete_button.hide();
    } else {
        delete_button.show();
    }
    check_add_item_common();
}

//show, hide add new button for button type and generic type
function check_add_item_common() {
    var add_item_common     = $('.footer_message_input .add_item_common'),
        bot_message_type    = botMessageType(),
        type                = common_data['bot_content_type'],
        item_btn_count,
        max_num;

    if(bot_message_type == type[type_button]) {
        item_btn_count  = $('.button_group .button_container .btn_box').length;
        max_num         = max_button_block;
    } else if(bot_message_type == type[type_quick_replies]) {
        item_btn_count  = $('.quick_replies_group .quick_replies_box').length;
        max_num         = max_quick_replies_button;
    } else if(bot_message_type == type[type_generic]) {
        var genericActive   = genericActiveElement();
        item_btn_count      = genericActive.find('.generic_button_container .button_type_container').length;
        max_num             = max_generic_button;
    }
    if(item_btn_count >= max_num) {
        add_item_common.hide();
    } else {
        add_item_common.show();
    }
}

//hide, show by check number button box
function checkGenericBox() {
    var generic_box_count   = $('.generic_container .generic_box').length,
        delete_button       = $('.generic_box .delete_generic_box');

    //show, hide delete generic box
    if(generic_box_count <= 1) {
        delete_button.hide();
    } else {
        delete_button.show();
    }
    checkAddGroupButtonBox();
}

/**
 * change bot text type input
 * @returns {*|jQuery|HTMLElement}
 */
function botTextTypeInput(value) {
    var input = $('.message_bot_area .text_group .input_text_type');
    if(value != void 0) {
        input.val(value);
    }
    return input;
}

/**
 * set value bot message from input text
 * @param value
 */
function changeDataBotMessage(value) {
    var bot_message = botMessageElement();
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
 * return bot message content focusing
 * @returns {*}
 */
function botMessageContent() {
    var bot_input = botMessageElement();
    return bot_input.val();
}

/**
 * return bot message type element
 * @returns {*}
 */
function botMessageType() {
    var bot_box    = botMessageBoxElement(),
        bot_type   = bot_box.find('input.messages_bot_type').val();
    return bot_type;
}

/**
 * return bot message element
 */
function botMessageElement() {
    return $(".bot_scenario textarea[name='" + $('input.messages_focus').val() + "']");
}

/**
 * return bot message box
 */
function botMessageBoxElement() {
    var bot_input = botMessageElement();
    return bot_input.parents('.bot_form_box');
}

/**
 * return current active generic
 */
function genericActiveElement() {
    return $('.generic_group .generic_box.active');
}

/**
 * return text input of user message
 * @returns {*|jQuery|HTMLElement}
 */
function userInputText() {
    return $('.message_user_area .text_group input.user_text_type');
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
function userMessageType() {
    var user_message = userMessageBoxElement(),
        user_type    = user_message.find('input.messages_user_type').val();
    return user_type;
}

/**
 * return user message element
 */
function userMessageElement() {
    return $(".user_scenario input[name='" + $('input.messages_focus').val() + "']");
}

/**
 * return user message box
 */
function userMessageBoxElement() {
    var user_message = userMessageElement();
    return user_message.parents('.user_form_box');
}


/**
 * return user message variable focusing
 * @returns {*}
 */
function userMessageVariable(value) {
    var user_message = userMessageBoxElement(),
        variable_elm = user_message.find('input.messages_user_variable');
    if(value != void 0) {
        return variable_elm.val(value);
    }
    return variable_elm.val();
}

/**
 * return user message nlp focusing
 * @returns {*}
 */
function userMessageNlp(value) {
    var user_message = userMessageBoxElement(),
        nlp_elm = user_message.find('input.messages_user_nlp');
    if(value != void 0) {
        return nlp_elm.val(value);
    }
    return nlp_elm.val();
}

/**
 * return list library checkbox element
 * @returns {*|jQuery|HTMLElement}
 */
function libraryListElement(value) {
    if(value != void 0 && value != '') {
        return $(".message_user_area .library_group input.library_list[value='" + value + "']");
    }
    return $('.message_user_area .library_group input.library_list');
}

/**
 * return api variable select element
 * @returns {*|jQuery|HTMLElement}
 */
function apiVariableSettingElement(value) {
    var api_variable = $('.message_user_area .api_variable_setting_group select.api_variable');
    if(value != void 0 && value != '') {
        return api_variable.val(value).trigger('change.select2');
    }
    return api_variable;
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
        }

    }
}

/**
 * @param value
 */
function apiElement(value) {
    var api_select = $(".message_bot_area .api_group select.api_select");
    if(value != void 0 && value != '') {
        return api_select.val(value).trigger('change.select2');
    }
    return api_select;
}

/**
 * @param value
 */
function scenarioConnectElement(value) {
    var scenario_select = $('.message_bot_area .scenario_connect_group select.scenario_select');
    if(value != void 0 && value != '') {
        return scenario_select.val(value).trigger('change.select2');
    }
    return scenario_select;
}

/**
 * @param value
 */
function mailElement(value) {
    var mail_select = $('.message_bot_area .mail_group select.mail_select');
    if(value != void 0 && value != '') {
        return mail_select.val(value).trigger('change.select2');
    }
    return mail_select;
}

/**
 * show, hide box message and button preview
 */
function checkViewMediaDemo() {
    var bot_box  = botMessageBoxElement(),
        bot_type = botMessageType(),
        type     = common_data['bot_content_type'];

    //show if is not api or scenario connect type
    var preview = bot_box.find('.preview');
    if (bot_type == type[type_text] || bot_type == type[type_api] || bot_type == type[type_scenario_connect] || bot_type == type[type_mail]) {
        preview.addClass('hidden');
    } else {
        preview.removeClass('hidden');
    }
}

/**
 * get data from slidebar input for message box by type
 * @param type
 */
function getDataFromInput(type) {
    switch (type) {
        case type_text:             getDataText(); break;
        case type_button:           getDataButton(); break;
        case type_generic:          getDataGeneric(); break;
        case type_file:             getDataFile(); break;
        case type_quick_replies:    getDataQuickReply(); break;
        case type_api:              getDataApi(); break;
        case type_scenario_connect: getDataScenarioConnect(); break;
        case type_mail:             getDataMail(); break;
    }
    //set title for button demo in message content box
    if(type != type_file) {
        setDataMediaDemo();
    }
}

/**
 * get text data to json and fill to message box
 */
function getDataText() {
    var text_input = botTextTypeInput(),
        text_input = text_input.val();
    var data = {
        'message' : {
            'text' : text_input
        }
    };
    data = JSON.stringify(data);
    changeDataBotMessage(data);
}

/**
 * get button data to json and fill to message box
 */
function getDataButton() {
    var button_box              = $('.button_group .button_container'),
        button_title            = button_box.find('input.button_title').val(),
        template_button         = common_data['template_button'],
        template_button_flip    = common_data['template_button_flip'],
        variable_id             = '';

    //set variable if use select box
    if(button_box.find('input.btn_is_variable:checked').val()) {
        variable_id             = button_box.find('select.button_variable').val();
    }
    var data = {
        'variable' : variable_id,
        'message' : {
            'attachment' : {
                'type' : 'template',
                'payload' : {
                    'template_type' : type_button,
                    'text' : button_title,
                    'buttons' : []
                }
            }
        }
    };
    $('.button_group .button_container .btn_box').each(function (index, element) {
        var button_sub_title     = $(this).find('input.button_sub_title').val(),
            button_sub_type      = $(this).find('select.button_sub_type').val(),
            button_sub_data      = $(this).find('input.button_sub_data').val(),
            button_sub_scenario  = $(this).find('select.button_sub_scenario').val();
        var button_data = {
            'type' : template_button_flip[button_sub_type],
            'title' : button_sub_title
        };
        if(button_sub_type == template_button['web_url']) {
            button_data['url']          = button_sub_data;

        } else if(button_sub_type == template_button['postback']) {
            if(variable_id == void 0 || variable_id == '') {
                variable_id = '-1';
            }
            var button_sub_title_base64 = base64Encode(button_sub_title, '-1');
            button_data['payload']      = 'SCENARIO_' + button_sub_scenario  + '_' + variable_id + '_' + button_sub_title_base64;

        } else {
            button_data['payload']      = button_sub_data;
        }
        data['message']['attachment']['payload']['buttons'].push(button_data);
    });
    data = JSON.stringify(data);
    changeDataBotMessage(data);
}

/**
 * get first file
 */
function getDataFile() {
    var file_url    = $('.file_group .file_url_input').val(),
        type        = common_data['bot_content_type'];
    file_url        = $.trim(file_url);
    var file_type   = checkTypeFile(file_url);

    if(file_type) {
        var data = {
            'message' : {
                'attachment' : {
                    'type'      : file_type,
                    'payload'   : {
                        'url'   : file_url
                    }
                }
            }
        };
        data = JSON.stringify(data);
        changeDataBotMessage(data);
        //set title for button demo in message content box
        setDataMediaDemo();
        changeLabelMediaDemo(file_type);
    } else {
        console.log('File type not valid type');
        //clear bot message content
        changeDataBotMessage('');
        setDataMediaDemo(true);
    }
}

/**
 * get button data to json and fill to message box
 */
function getDataGeneric() {
    var template_button         = common_data['template_button'],
        template_button_flip    = common_data['template_button_flip'],
        type                    = common_data['bot_content_type'];
    var data = {
        'message' : {
            'attachment' : {
                'type'      : 'template',
                'payload'   : {
                    'template_type' : type_generic,
                    'elements'      : []
                }
            }
        }
    };
    $('.generic_container .generic_box').each(function (index, element) {
        var title     = $(this).find('input.title').val(),
            sub_type  = $(this).find('input.sub_title').val(),
            item_url  = $(this).find('input.item_url').val(),
            image_url = $(this).find('input.image_url').val();
        var generic_data = {
            'title'     : title,
            'subtitle'  : sub_type,
            'item_url'  : item_url,
            'image_url' : image_url
        };

        //loop button in generic box
        $(this).find('.generic_button_container .button_type_container').each(function (index2, element2) {
            //create button array if not exist
            if(generic_data['buttons'] == void 0) {
                generic_data['buttons'] = [];
            }
            var button_title        = $(this).find('input.button_title').val(),
                button_sub_type     = $(this).find('select.button_sub_type').val(),
                button_sub_data     = $(this).find('input.button_sub_data').val(),
                button_sub_scenario = $(this).find('select.button_sub_scenario').val();

            var button_data = {
                'title'     : button_title,
                'type'      : template_button_flip[button_sub_type]
            };

            if(button_sub_type == template_button['web_url']) {
                button_data['url']          = button_sub_data;
            } else if(button_sub_type == template_button['postback']) {
                button_data['payload']      = 'SCENARIO_' + button_sub_scenario + '_' + button_title.replace(/[_]/g, '');
            } else if(button_sub_type == template_button['element_share']) {
                button_data = {'type' : 'element_share'};
            } else {
                button_data['payload']      = button_sub_data;
            }
            //push to buttons in generic_data
            generic_data['buttons'].push(button_data);
        });
        data['message']['attachment']['payload']['elements'].push(generic_data);
    });
    data = JSON.stringify(data);
    changeDataBotMessage(data);
}


/**
 * get button data to json and fill to message box
 */
function getDataQuickReply() {
    var qr_group                        = $('.quick_replies_group'),
        qr_title                        = qr_group.find('input.quick_replies_title').val(),
        variable_id                     = '',
        template_quick_replies          = common_data['template_quick_replies'],
        template_quick_replies_flip     = common_data['template_quick_replies_flip'],
        user_keyword_matching           = qr_group.find('#user_keyword_matching:checked').val();

    //set variable if use select box
    if(qr_group.find('input.qr_is_variable:checked').val()) {
        variable_id                     = qr_group.find('select.quick_replies_variable').val();
    }
    //set user_keyword_matching if checked
    if(user_keyword_matching == void 0 || user_keyword_matching == '') {
        user_keyword_matching = 0;
    }
    user_keyword_matching = parseInt(user_keyword_matching);

    var data = {
        'variable' : variable_id,
        'keyword_matching_flg' : user_keyword_matching,
        'message' : {
            'text' : qr_title,
            'quick_replies' : []
        }
    };
    qr_group.find('.quick_replies_box').each(function (index, element) {
        var type                = $(this).find('select.button_sub_type').val(),
            title               = $(this).find('input.button_sub_title').val(),
            variable_code       = '';

        var button_data = {
            'content_type' : template_quick_replies_flip[type]
        };
        if(type == template_quick_replies['text']) {
            var scenario_id      = '-1';
            var btn_variable_id  = variable_id;
            //set scenario if use scenario connect
            if($(this).find('input.qr_is_scenario:checked').val()) {
                scenario_id = $(this).find('select.button_sub_scenario').val();
            }
            if(btn_variable_id == void 0 || btn_variable_id == '') {
                btn_variable_id = '-1';
            } else {
                variable_code = $(this).find('input.button_variable_code').val();
                variable_code = base64Encode(variable_code);
            }
            button_data['title']     = title;
            button_data['payload']   = 'QUICK_REPLIES_' + scenario_id + '_' + btn_variable_id + '_' + variable_code + '_' + user_keyword_matching;
        }
        data['message']['quick_replies'].push(button_data);
    });
    data = JSON.stringify(data);
    changeDataBotMessage(data);
}


/**
 * get api data to json and fill to message box
 */
function getDataApi() {
    var api_val = apiElement(),
        data    = {
            'api' : api_val.val()
        };
    data = {'message' : data};
    data = JSON.stringify(data);
    changeDataBotMessage(data);
}

/**
 * get api data to json and fill to message box
 */
function getDataScenarioConnect() {
    var scenario_val = scenarioConnectElement(),
        data         = {
            'scenario' : scenario_val.val()
        };
    data = {'message' : data};
    data = JSON.stringify(data);
    changeDataBotMessage(data);
}

/**
 * get mail data to json and fill to message box
 */
function getDataMail() {
    var scenario_val = mailElement(),
        data         = {
            'mail' : scenario_val.val()
        };
    data = {'message' : data};
    data = JSON.stringify(data);
    changeDataBotMessage(data);
}

/**
 * get title from message_container_input for input User demo
 * @param is_clear
 */
function setUserDataMediaDemo(is_clean) {
    var user_message_box    = userMessageBoxElement();
    if(is_clean != void 0 && is_clean) {
        user_message_box.find('.user_media_content').val('');
    } else {
        var user_message_type = userMessageType(),
            type = common_data['user_content_type'],
            value;

        if(user_message_type == type[type_api_variable_setting]) {
            var element = apiVariableSettingElement(),
                label_bot_smg  = $('.scenario_block_origin .bot_virtual_box .bot_api_variable_setting_view').data('label');
            value = element.find("option[value='" + element.val() + "']").text();
            value = label_bot_smg + '[' + value + ']';
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

        if(bot_message_type == type[type_text]) {
            var typeInput = botTextTypeInput();
            value = typeInput.val();

        } else if(bot_message_type == type[type_button]) {
            value = $('.button_group .button_container input.button_title').val();

        } else if(bot_message_type == type[type_generic]) {
            value = '';
        } else if(bot_message_type == type[type_quick_replies]) {
            value = $('.quick_replies_group .quick_replies_title').val();
        } else if(bot_message_type == type[type_file]) {
            value = $('.file_group .file_url_input').val();

        } else if(bot_message_type == type[type_api] || bot_message_type == type[type_scenario_connect] || bot_message_type == type[type_mail]) {
            var element;
            switch (bot_message_type) {
                case type[type_api]:
                    element = apiElement(); break;
                case type[type_scenario_connect]:
                    element = scenarioConnectElement(); break;
                case type[type_mail]:
                    element = mailElement(); break;
            }
            value = element.find("option[value='" + element.val() + "']").text()

        }
        bot_message_box.find('.bot_media_content').val(value);
    }
}

/**
 * Actions when select type user
 */
function checkContentUserInput(type_current) {
    var type = common_data['user_content_type'],
        user_message = userMessageBoxElement(),
        user_media_demo = $('.scenario_block .user_scenario .user_media_demo'),
        group_message = '';
    $('.message_user_area .type_group').hide();

    //case type
    switch (type_current){
        case type[type_text]:
            group_message = $('.message_user_area .text_group');
            group_message.find('.tagsinput input').first().focus();

            user_message.find('.user_media_content').addClass('hidden');
            user_message.find('ul.tagit').removeClass('hidden');

            //delete virtual bot message box
            deleteBotVirtual();
            break;
        case type[type_library]:
            group_message = $('.message_user_area .library_group');
            //un-select all option
            var lib_element = libraryListElement();
            icheckAuto('uncheck', lib_element);

            user_message.find('.user_media_content').addClass('hidden');
            user_message.find('ul.tagit').removeClass('hidden');
            cloneBotVirtual(null, type[type_library]);
            break;
        case type[type_api_variable_setting]:
            //show button box
            group_message = $('.message_user_area .api_variable_setting_group');

            user_message.find('.user_media_content').removeClass('hidden');
            user_message.find('ul.tagit').addClass('hidden');
            cloneBotVirtual(null, type[type_api_variable_setting]);

            //select and get first option for message
            var api = apiVariableSettingElement();
            apiVariableSettingElement(api.find('option').first().val());

            getDataUserFromInput(type_api_variable_setting);
            break;
    }
    if(group_message) {
        group_message.show();
        //set un-checked input and select first option variable, nlp
        icheckAuto('uncheck', group_message.find('input.use_variable'));
        selectFirstOption(group_message.find('select.select_variable'));
        icheckAuto('uncheck', group_message.find('input.use_nlp'));
        selectFirstOption(group_message.find('select.select_nlp'));
    }
}

/**
 * Actions when click to user message
 */
function checkContentUserMessage(type_current) {
    var type = common_data['user_content_type'],
        user_message = userMessageBoxElement(),
        user_media_demo = $('.scenario_block .user_scenario .user_media_demo'),
        group_message = '';

    $('.message_user_area .type_group').hide();

    //case type
    switch (type_current){
        case type[type_text]:
            group_message = $('.message_user_area .text_group');
            group_message.find('.tagsinput input').first().focus();

            user_message.find('.user_media_content ').addClass('hidden');
            user_message.find('ul.tagit').removeClass('hidden');

            //delete virtual bot message box
            deleteBotVirtual();
            break;
        case type[type_library]:
            group_message = $('.message_user_area .library_group');
            //un-select all option
            var lib_element = libraryListElement();
            icheckAuto('uncheck', lib_element);

            user_message.find('.user_media_content').addClass('hidden');
            user_message.find('ul.tagit').removeClass('hidden');
            cloneBotVirtual(null, type[type_library]);
            break;
        case type[type_api_variable_setting]:
            //show button box
            group_message = $('.message_user_area .api_variable_setting_group');
            user_message.find('.user_media_content ').removeClass('hidden');
            user_message.find('ul.tagit').addClass('hidden');
            cloneBotVirtual(null, type[type_api_variable_setting]);
            //select and get first option for message
            var api = apiVariableSettingElement();
            apiVariableSettingElement(api.find('option').first().val());

            break;
    }
    if(group_message) {
        group_message.show();
        //set un-checked input and select first option variable, nlp
        icheckAuto('uncheck', group_message.find('input.use_variable'));
        selectFirstOption(group_message.find('select.select_variable'));
        icheckAuto('uncheck', group_message.find('input.use_nlp'));
        selectFirstOption(group_message.find('select.select_nlp'));
    }
}

/**
 * Actions when select type bot
 */
function checkContentBotInput(type_current) {
    var type = common_data['bot_content_type'];
    bot_change_element();
    //clear data text
    botTextTypeInput('');
    //clear data button
    $('.button_group .button_container input.button_title').val('');
    $('.button_group .button_container .btn_box').remove();
    //clear data generic
    $('.generic_group .generic_container .generic_box').remove();
    $('.generic_indicators li').remove();
    //clear data quick replies
    $('.quick_replies_group input.quick_replies_title').val('');
    $('.quick_replies_group .quick_replies_box').remove();
    //clear validation class in select2 select
    $('.message_bot_area .select2 .select2-selection').removeClass('validation-failed');

    var bot_message_input   = botMessageBoxElement();
    //hide validate old message box
    setStyleElm(bot_message_input.find('.bot_media_content'), {'border' : ''});

    //add button demo in bot message
    checkViewMediaDemo();
    switch (type_current) {
        case type[type_text]:
            $('.message_bot_area .text_group').show();
            changeLabelMediaDemo('');
            break;
        case type[type_button]:
            //show button box
            $('.message_bot_area .button_group').show();
            //set un-checked input variable
            icheckAuto('uncheck', $('.message_bot_area .button_group #btn_is_variable'));
            //add first button box
            addButtonBox();
            changeLabelMediaDemo(type_button);
            break;
        case type[type_file]:
            $('.message_bot_area .file_group').show();
            $('.file_group .file_url_input').val('');
            changeLabelMediaDemo(type_file);
            break;
        case type[type_generic]:
            generic_element_select();
            //add first generic box
            createGenericBox();
            changeLabelMediaDemo(type_generic);
            break;
        case type[type_quick_replies]:
            var qr_group = $('.message_bot_area .quick_replies_group');
            qr_group.show();
            //set un-checked input variable and user_keyword_matching
            icheckAuto('uncheck', qr_group.find('#qr_is_variable'));
            icheckAuto('uncheck', qr_group.find('#user_keyword_matching'));

            //add first quick reply box
            addQuickRepliesBox();
            changeLabelMediaDemo(type_quick_replies);
            break;
        case type[type_api]:
            $('.message_bot_area .api_group').show();
            //select first option
            var api_select = apiElement();
            apiElement(api_select.find('option').first().val());
            getDataFromInput(type_api);

            changeLabelMediaDemo(type_api);
            break;
        case type[type_scenario_connect]:
            $('.message_bot_area .scenario_connect_group').show();
            //select first option
            var scenario = scenarioConnectElement();
            scenarioConnectElement(scenario.find('option').first().val());
            //set first option for message
            getDataFromInput(type_scenario_connect);

            changeLabelMediaDemo(type_scenario_connect);
            break;
        case type[type_mail]:
            $('.message_bot_area .mail_group').show();
            //select first option
            var mail_select = mailElement();
            mailElement(mail_select.find('option').first().val());
            //set first option for message
            getDataFromInput(type_mail);

            changeLabelMediaDemo(type_mail);
            break;
    }
}

/**
 * Actions when click to bot message
 */
function checkContentBotMessage(type_current) {
    var type = common_data['bot_content_type'];
    bot_change_element();
    switch (type_current){
        case type[type_text]:
            $('.message_bot_area .text_group').show();
            break;
        case type[type_button]:
            //show button box
            $('.message_bot_area .button_group').show();
            break;
        case type[type_file]:
            $('.message_bot_area .file_group').show();
            $('.file_group .file_url_input').val('');
            break;
        case type[type_generic]:
            generic_element_select();
            break;
        case type[type_quick_replies]:
            $('.message_bot_area .quick_replies_group').show();
            break;
        case type[type_api]:
            $('.message_bot_area .api_group').show();
            //select first option
            var api_select = apiElement();
            apiElement(api_select.find('option').first().val());
            break;
        case type[type_scenario_connect]:
            $('.message_bot_area .scenario_connect_group').show();
            //select first option
            var scenario = scenarioConnectElement();
            scenarioConnectElement(scenario.find('option').first().val());
            break;
        case type[type_mail]:
            $('.message_bot_area .mail_group').show();
            //select first option
            var mail = mailElement();
            mailElement(mail.find('option').first().val());
            break;
    }
}

/**
 * action for elements when change bot message
 */
function bot_change_element() {
    $('.message_bot_area .type_group').hide();
    //clear old error
    $('.message_bot_area .type_group label.error').remove();
    $('.message_bot_area .type_group input.validation-failed, .message_bot_area .type_group textarea.validation-failed').removeClass('validation-failed');
    //clear generic error
    $('.message_bot_area .generic_error').addClass('hidden').find('.error_container').html('');

    $('.message_bot_area .add_group_box').hide();
    $('.message_bot_area .footer_message_input .carousel_slide').hide();
    $('.message_bot_area .footer_message_input .carousel_indicator_item').hide();
    $('.message_bot_area .footer_message_input .add_item_common').hide();
    //clear data input
    botTextTypeInput('');
    message_area_select(true);
}

/**
 * action for elements when select generic in bot message
 */
function generic_element_select() {
    $('.message_bot_area .footer_message_input .carousel_slide').show();
    $('.message_bot_area .footer_message_input .carousel_indicator_item').show();
    $('.message_bot_area .generic_group').show();
    $('.message_bot_area .add_group_box').show();
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
        user_message_variable = userMessageVariable(),
        user_message_nlp = userMessageNlp(),
        type              = common_data['user_content_type'],
        user_input_text   = userInputText(),
        group_message     = '';

    /*fill data from user message to text input in right slidebar*/
    switch (user_message_type) {
        case type[type_text]:
            group_message = $('.message_user_area .text_group');
            user_input_text.importTags(user_message_val);
            break;
        case type[type_library]:
            group_message = $('.message_user_area .library_group');
            //re-check item
            $(user_message_val.split(',')).each(function (index, value) {
                if(value) {
                    var library_item = libraryListElement(value);
                    icheckAuto('check', library_item);
                }
            });
            break;
        case type[type_api_variable_setting]:
            group_message = $('.message_user_area .api_variable_setting_group');
            if(user_message_val) {
                apiVariableSettingElement(user_message_val);
            }
            break;
    }

    if(group_message) {
        if(user_message_variable) {
            //set checked input variable
            icheck_change_event = false;
            icheckAuto('check', group_message.find('input.use_variable'));
            icheck_change_event = true;
            //set value for select variable
            group_message.find('select.select_variable').val(user_message_variable);
        }

        if(user_message_nlp) {
            //set checked input nlp
            icheck_change_event = false;
            icheckAuto('check', group_message.find('input.use_nlp'));
            icheck_change_event = true;
            //set value for select nlp
            group_message.find('select.select_nlp').val(user_message_nlp);
        }
    }

    userMessageVariable(user_message_variable);
    userMessageNlp(user_message_nlp);
}

/**
 * set data bot message come back to input form
 */
function setBotDataToForm() {
    var bot_message_input   = botMessageElement(),
        bot_message_val     = botMessageContent(),
        bot_message_type    = botMessageType(),
        type                = common_data['bot_content_type'];

    //fill data from bot message to text input in right slidebar
    var jsonData = jsonConverse(bot_message_val);

    //if text simple case
    if(bot_message_type == type[type_text]) {
        var text = '';
        if(jsonData && jsonData['message']) {
            jsonData = jsonData['message'];
            if(jsonData['text']) {
                text = jsonData['text'];
            }
        }
        botTextTypeInput(text);
    } else {
        var template_button = common_data['template_button'],
            file_type       = common_data['file_type'];

        if(bot_message_type == type[type_button]) {
            var button_group = $('.button_group .button_container');
            var variable_checkbox = button_group.find('#btn_is_variable'),
                variable_select = button_group.find('select.button_variable');

            //remove all button before action
            button_group.find('.btn_box').remove();
            button_group.find('input.button_title').val('');

            //disable 'ifChanged' event of iCheck in this process, it call getDataButton() function make error data
            icheck_change_event = false;
            //set un-checked input is variable
            icheckAuto('uncheck', variable_checkbox);
            //checked first option variable
            variable_select.val(variable_select.find('option').first().val());
            //set value for variable
            if(jsonData['variable'] != void 0 && jsonData['variable'] != '') {
                //set checked input is variable
                icheckAuto('check', variable_checkbox);
                //set value for select variable
                variable_select.val(jsonData['variable']);
            }

            var button_number = 0;
            if(jsonData && jsonData['message']) {
                jsonData            = jsonData['message']['attachment']['payload'];
                button_group.find('input.button_title').val(jsonData['text']);
                //create button detail
                var buttons = jsonData['buttons'];
                button_number = buttons.length;
                for (var i=0; i<buttons.length; i++) {
                    var clone       = $('.scenario_block_origin .btn_box').clone(),
                        button_data = buttons[i],
                        custom_data = button_data['payload'] ? button_data['payload'] : (button_data['url'] ? button_data['url'] : ''),
                        placeholder = clone.find('input.button_sub_data').attr('data-label-url');

                    clone.find('input.button_sub_title').val(button_data['title']);
                    clone.find('select.button_sub_type').val(template_button[button_data['type']]);

                    if(button_data['type'] == 'postback') {
                        custom_data = custom_data.split('_');
                        custom_data = custom_data[1];
                        placeholder = clone.find('input.button_sub_data').attr('data-label-postback');

                        clone.find('select.button_sub_scenario').val(custom_data);
                        clone.find('.button_scenario_box').show();
                        clone.find('.button_data_box').hide();
                        clone.find('input.button_sub_data').removeClass('validate-require validate-url validate-white-list');

                    } else if(button_data['type'] == 'phone_number') {
                        placeholder = clone.find('input.button_sub_data').attr('data-label-phone');

                        clone.find('input.button_sub_data').val(custom_data);
                        clone.find('input.button_sub_data').removeClass('validate-url validate-white-list').addClass('validate-phone');

                    } else {
                        clone.find('input.button_sub_data').val(custom_data);
                    }
                    clone.find('input.button_sub_data').attr('placeholder', placeholder);
                    button_group.append(clone);
                    checkButtonBox();
                }
            }
            //add button box for button and confirm type
            if(button_number <= 0) {
                addButtonBox();
            }
            //re-set default value
            icheck_change_event = true;

        } else if(bot_message_type == type[type_generic]) {
            //remove all generic before action
            $('.generic_group .generic_container .generic_box').remove();
            $('.generic_indicators li').remove();

            if(jsonData && jsonData['message']) {
                jsonData = jsonData['message']['attachment']['payload']['elements'];
                for (var i=0; i<jsonData.length; i++) {
                    var clone           = $('.scenario_block_origin .generic_box').clone(),
                        generic_data    = jsonData[i];

                    clone.find('input.title').val(generic_data['title']);
                    clone.find('input.sub_title').val(generic_data['subtitle']);
                    clone.find('input.item_url').val(generic_data['item_url']);
                    clone.find('input.image_url').val(generic_data['image_url']);
                    //add a button for new Generic
                    //------Carousel process
                    //add class active for first item to show Carousel
                    if(generic_data['buttons'] != void 0 && generic_data['buttons'] != '') {
                        for (var j=0; j<generic_data['buttons'].length; j++) {
                            var button_data     = generic_data['buttons'][j],
                                button_clone    = $('.scenario_block_origin .generic_button_template .button_type_container').clone(),
                                custom_data     = button_data['payload'] ? button_data['payload'] : (button_data['url'] ? button_data['url'] : ''),
                                placeholder     = button_clone.find('input.button_sub_data').attr('data-label-url');

                            button_clone.find('select.button_sub_type').val(template_button[button_data['type']]);
                            button_clone.find('input.button_title').val(button_data['title']);

                            if(button_data['type'] == 'postback') {
                                custom_data = custom_data.split('_');
                                custom_data = custom_data[1];
                                placeholder = button_clone.find('input.button_sub_data').attr('data-label-postback');

                                button_clone.find('select.button_sub_scenario').val(custom_data);
                                button_clone.find('.button_scenario_box').show();
                                button_clone.find('.button_data_box').hide();
                                button_clone.find('input.button_sub_data').removeClass('validate-require validate-url validate-white-list');

                            } else if(button_data['type'] == 'element_share') {
                                button_clone.find('.button_title_box').hide();
                                button_clone.find('input.button_title').removeClass('validate-require');
                                button_clone.find('.button_data_box').hide();
                                button_clone.find('input.button_sub_data').removeClass('validate-require validate-url validate-white-list');

                            } else if(button_data['type'] == 'phone_number') {
                                placeholder = button_clone.find('input.button_sub_data').attr('data-label-phone');

                                button_clone.find('input.button_sub_data').val(custom_data);
                                button_clone.find('input.button_sub_data').removeClass('validate-url validate-white-list').addClass('validate-phone');

                            } else {
                                button_clone.find('input.button_sub_data').val(custom_data);
                            }
                            button_clone.find('input.button_sub_data').attr('placeholder', placeholder);
                            clone.find('.generic_button_container').append(button_clone);
                        }
                    }
                    $('.generic_container').append(clone);

                    //------Carousel process
                    //set active for first item
                    addCarouselIndicator('generic_indicators', true);
                    checkCarouselSlide();

                    //check to show, hide button delete box
                    checkGenericBox();
                    checkGenericButtonBox();
                }
            } else {
                //add first generic box
                createGenericBox();
            }
        } else if(bot_message_type == type[type_quick_replies]) {
            var template_quick_replies  = common_data['template_quick_replies'],
                qr_group                = $('.quick_replies_group');
            //disable 'ifChanged' event of iCheck in this process, it call getDataQuickReply() function make error data
            icheck_change_event = false;
            //remove all button before action
            qr_group.find('.quick_replies_box').remove();
            qr_group.find('input.quick_replies_title').val('');
            //set un-checked input is variable and user_keyword_matching
            icheckAuto('uncheck', qr_group.find('#qr_is_variable'));
            icheckAuto('uncheck', qr_group.find('#user_keyword_matching'));
            //checked first option variable
            qr_group.find('select.quick_replies_variable').val(qr_group.find('select.quick_replies_variable option').first().val());

            if(jsonData) {
                var is_variable = false;
                //set variable if available
                if(jsonData['variable'] != void 0 && jsonData['variable'] != '') {
                    is_variable = true;
                    //set checked input is variable
                    icheckAuto('check', qr_group.find('#qr_is_variable'));
                    //set value for select variable
                    qr_group.find('select.quick_replies_variable').val(jsonData['variable']);
                }

                //set user_keyword_matching if available
                if(jsonData['keyword_matching_flg'] != void 0 && jsonData['keyword_matching_flg']) {
                    icheckAuto('check', qr_group.find('#user_keyword_matching'));
                }

                if(jsonData['message']) {
                    jsonData = jsonData['message'];
                    qr_group.find('input.quick_replies_title').val(jsonData['text']);
                    //create quick replies detail
                    var quick_replies = jsonData['quick_replies'];
                    for (var i=0; i<quick_replies.length; i++) {
                        var clone       = $('.scenario_block_origin .quick_replies_box').clone(),
                            qr_data     = quick_replies[i];

                        clone.find('select.button_sub_type').val(template_quick_replies[qr_data['content_type']]);
                        if(qr_data['content_type'] == 'text') {
                            clone.find('input.button_sub_title').val(qr_data['title']);
                            if(qr_data['payload']) {
                                var payload = qr_data['payload'];
                                payload     = payload.split('QUICK_REPLIES_').pop();
                                payload     = payload.split('_');

                                var scenario_id = payload[0],
                                    variable_code = (payload[2] != void 0) ? payload[2] : '';

                                //if exist option selected then select this option
                                if( clone.find("select.button_sub_scenario option[value='" + scenario_id + "']").length) {
                                    //set checked input is scenario
                                    icheckAuto('check', clone.find('input.qr_is_scenario'));
                                    //show set value for select scenario
                                    var scenario = clone.find('select.button_sub_scenario');
                                    scenario.removeClass('hidden');
                                    scenario.val(scenario_id);
                                }
                                //show button_variable_code_box if variable is checked
                                if(is_variable) {
                                    //decode base 64
                                    if(variable_code != void 0 && variable_code != '') {
                                        try{
                                            variable_code = base64Decode(variable_code);
                                        } catch (e) {
                                            variable_code = '';
                                        }
                                    }
                                    clone.find('.button_variable_code_box').removeClass('hidden');
                                    clone.find('input.button_variable_code').val(variable_code);
                                }
                            }
                            clone.find('input.button_sub_title').addClass('validate-require');
                        } else {
                            clone.find('.button_title_box, .button_scenario_box, .button_variable_code_box').addClass('hidden');
                            clone.find('input.button_sub_title').removeClass('validate-require');
                        }
                        qr_group.find('.quick_replies_container').append(clone);
                        checkQuickReplyButtonBox();
                    }
                    //re-init icheck
                    initIcheck($('.quick_replies_group input.qr_is_scenario'));
                }
            }
            //add first quick replies box if is not exist any quick reply box
            if($('.quick_replies_group .quick_replies_box').length <= 0) {
                addQuickRepliesBox();
            }

            //re-set default value
            icheck_change_event = true;

        } else if(bot_message_type == type[type_file]) {
            if(jsonData && jsonData['message']) {
                if(jsonData['message']) {
                    jsonData    = jsonData['message']['attachment'];
                    var url     = jsonData['payload']['url'] ? jsonData['payload']['url'] : '';
                    //set file url for file input
                    $('.file_group .file_url_input').val(url);
                }
            }
        } else if(bot_message_type == type[type_api]) {
            if(jsonData && jsonData['message'] && jsonData['message'] != '') {
                var api = jsonData['message']['api'];
                //select option
                apiElement(api);
            } else {
                //re get api if exist any api item
                getDataFromInput(type_api);
            }
        } else if(bot_message_type == type[type_scenario_connect]) {
            if(jsonData && jsonData['message'] && jsonData['message'] != '') {
                var scenario = jsonData['message']['scenario'];
                //select option
                scenarioConnectElement(scenario);
            } else {
                //re get scenario if exist any api item
                getDataFromInput(type_scenario_connect);
            }
        } else if(bot_message_type == type[type_mail]) {
            if(jsonData && jsonData['message'] && jsonData['message'] != '') {
                var mail = jsonData['message']['mail'];
                //select option
                mailElement(mail);
            } else {
                //re get mail if exist any api item
                getDataFromInput(type_mail);
            }
        }
    }

    //check error input in message box and push notice
    checkErrorBoxMessage();
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

//check active slide to hide, show arrow
function checkCarouselSlide() {
    var arrow_left  = $(".footer_message_input .carousel_slide [data-slide='prev']"),
        arrow_right = $(".footer_message_input .carousel_slide [data-slide='next']");

    if ($('.generic_container .generic_box').length > 1) {
        if($('.generic_container .generic_box:last-child').hasClass('active')) {
            arrow_right.hide();
            arrow_left.show();
        } else if($('.generic_container .generic_box:first-child').hasClass('active')) {
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
    var indexActive = $('.generic_container .generic_box.active').index();
    $('.footer_message_input .generic_indicators li').removeClass('active');
    $('.footer_message_input .generic_indicators li').eq(indexActive).addClass('active');
}

/**
 * re-set hieght for right sldiebar
 */
function setHeightSlidebar() {
    var fixedsidebar            = $('.fixedsidebar').innerHeight(),
        space                   = 208,
        group_box               = $('.message_bot_area .add_group_box'),
        group_box_header        = 0;
    if(group_box.css('display') != 'none') {
        group_box_header = group_box.innerHeight() + parseInt(group_box.css('margin-bottom'));
    }
    var height_box = fixedsidebar - space - group_box_header;
    $('.fixedsidebar .message_bot_area .slimScrollDiv').css('height', height_box + 'px');
    $('.message_bot_area .fixedsidebar-content').css('height', height_box + 'px');
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
        case type[type_text]:
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
            break;
    }

    var form_style        = {'border' : ''},
        bot_box           = botMessageBoxElement(),
        bot_media_content = bot_box.find('.bot_media_content');
    if(form_box != void 0 && form_box != '' && form_box.length) {
        var validate      = validation_scenario('facebook', form_box, bot_message_type);
        //if not demo bot input (bot_media_content) then get input messages_bot_content (only text type)
        if(!bot_media_content.length) {
            bot_media_content = bot_box.find('.messages_bot_content');
        }
        //validate button number of carousel
        var validate_carousel = validateCarouselField();

        if(!validate || !validate_carousel) {
            result = false;
            form_style        = {'border' : '1px solid #ff5f5f'};
        }
    }
    setStyleElm(bot_media_content, form_style);
    return result;
}

/**
 * Validate 1 in 3 field not empty (sub title, url image, button number)
 * @returns {boolean}
 */
function validateCarouselField() {
    var bot_message_type    = botMessageType(),
        type                = common_data['bot_content_type'],
        result              = true;

    //clear common error
    $('.message_bot_area .common_error').addClass('hidden').find('.error_container').html('');

    if(bot_message_type == type[type_generic]) {
        var bot_message_val = botMessageContent(),
            jsonData        = jsonConverse(bot_message_val);

        if(jsonData && jsonData['message'] != void 0 && jsonData['message']['attachment']['payload']['elements'] != void 0) {
            var element = jsonData['message']['attachment']['payload']['elements'];
            $(element).each(function (i, e) {
                if((e['subtitle'] == void 0 || e['subtitle'] == '') && (e['image_url'] == void 0 || e['image_url'] == '') && (e['buttons'] == void 0 || !e['buttons'].length)) {
                    var validation_msg_list = common_data['validation_msg_list'];
                    //show error message in top carousel element
                    if(validation_msg_list != void 0 && validation_msg_list['validate_incomplete_field_carousel'] != void 0) {
                        var notice = '<label class="error">' + validation_msg_list['validate_incomplete_field_carousel'] + '</label>';
                        var generic_error_elm = $('.message_bot_area .generic_group .generic_box').eq(i).find('.generic_error');

                        generic_error_elm.removeClass('hidden').find('.error_container').append(notice);
                        //show error in Carousel slide
                        showErrorCarouselItem('facebook', generic_error_elm, bot_message_type);
                    }
                    result = false;
                }
            });
        }
    }
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