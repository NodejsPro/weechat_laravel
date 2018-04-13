//globe variable
var common_data         = [];
var type_text           = 'text';
var type_button         = 'button';
var type_carousel       = 'carousel';
var type_file           = 'file';
var type_library        = 'library';
var type_confirm        = 'confirm';
var type_location       = 'location';
var type_sticker        = 'sticker';
var type_imagemap       = 'imagemap';
var type_api            = 'api';
var type_scenario_connect = 'scenario_connect';
var type_mail           = 'mail';
var type_api_variable_setting = 'api_variable_setting';
var max_button_block    = 4;
var max_carousel_item   = 5;
var max_carousel_button = 3;
var max_confirm_button  = 2;
var imagemap_base_size_w = 1040;
var imagemap_base_size_h = 1040;
var imagemap_btn_label = ['A', 'B', 'C', 'D', 'E', 'F'];
var imagemap_template_path = '/images/line_imagemap_template/';
var icheck_change_event = true;
var conversation_page = false;

$(function () {
    message_area_select(null);
    if($('.scenario-edit.content_message').length) {
        conversation_page = true;
        $('#bot_content_type option').first().remove();
    }
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

        if(bot_message_type == type[type_button] || bot_message_type == type[type_confirm]) {
            addButtonBox();
        } else if(bot_message_type == type[type_carousel]) {
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
                $('.file_group .input_url_box.focus textarea').val(file_path);
                getDataFromInput(type_file);
            } else if(bot_message_type == type[type_button]) {
                $('.button_group .button_container input.image_url').val(file_path);
                getDataFromInput(type_button);
            } else if(bot_message_type == type[type_carousel]) {
                var genericActive = genericActiveElement();
                genericActive.find('input.image_url').val(file_path);
                getDataFromInput(type_carousel);
            } else if(bot_message_type == type[type_imagemap]) {
                $('.imagemap_group textarea.imagemap_url').val(file_path);
                getDataFromInput(type_imagemap);
            }
            //clear val file_path_input in popup file list
            file_path_input.val('');
        }
    });
    //FILE
    $('.file_group textarea').on('change', function (event) {
        getDataFromInput(type_file);
    });
    $('.file_group textarea').on('keypress', function (event) {
        getDataFromInput(type_file);
    });
    //set focus for fill data from file select popup
    $('.file_group .file_select_box').on('click', function (event) {
        $('.file_group .input_url_box').removeClass('focus');
        $(this).prev('.input_url_box').addClass('focus');
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

    //CAROUSEL
    $(document).on('keypress', '.generic_box input', function (event) {
        getDataFromInput(type_carousel);
    });
    $(document).on('change', '.generic_box input, .generic_box select', function (event) {
        getDataFromInput(type_carousel);
    });

    //CONFIRM
    $(document).on('keypress', '.confirm_group input', function (event) {
        getDataFromInput(type_confirm);
    });
    $(document).on('change', '.confirm_group input, .confirm_group select', function (event) {
        getDataFromInput(type_confirm);
    });
    //checkbox for use variable in confirm
    $(document).on('ifChanged', '.confirm_group #confirm_is_variable', function (event) {
        var confirm_group    = $('.confirm_group'),
            is_variable = confirm_group.find('input#confirm_is_variable:checked').val(),
            variable    = confirm_group.find('select.confirm_variable');
        if(is_variable != void 0 && is_variable) {
            variable.removeClass('hidden');
        } else {
            variable.addClass('hidden');
        }
        if(icheck_change_event) {
            getDataFromInput(type_confirm);
        }
    });

    //LOCATION
    $(document).on('keypress', '.location_group input', function (event) {
        getDataFromInput(type_location);
    });
    $(document).on('change', '.location_group input', function (event) {
        getDataFromInput(type_location);
    });

    //STICKER
    //select sticker from popup
    $('#line_sticker_select_modal .btn_sticker_select').on('click', function () {
        var bot_message_type    = botMessageType(),
            type                = common_data['bot_content_type'],
            sticker_input       = $('#line_sticker_select_modal #scenario_sticker_selected').val();

        //set file url for image input
        if(sticker_input != '' && sticker_input != void 0) {
            if(bot_message_type == type[type_sticker]) {
                sticker_input = sticker_input.split('_');
                if(sticker_input.length >= 2) {
                    var sticker_group = $('.sticker_group'),
                        package_id     = sticker_group.find('input.package_id').val(sticker_input[0]),
                        sticker_id     = sticker_group.find('input.sticker_id').val(sticker_input[1]);

                    //show to preview in input area
                    var sticker_path    = '/images/line_sticker/package_' + sticker_input[0] + '/' + sticker_input[1] + '.png';
                    showStickerInputArea(sticker_path, true);
                    //remove error notice
                    sticker_group.find('label.error').remove();
                }
                getDataFromInput(type_sticker);
            }
        }
    });

    //IMAGE MAP
    $(document).on('change', '.imagemap_group select, .imagemap_group textarea, .imagemap_group input', function (event) {
        getDataFromInput(type_imagemap);
    });
    $(document).on('keypress', '.imagemap_group textarea', function (event) {
        getDataFromInput(type_imagemap);
    });
    $('#line_imagemap_template_modal .btn-modal-select').on('click', function(event) {
        var modal_imagemap_template = $('#line_imagemap_template_modal .imagemap_template_content'),
            imagemap_area_num = modal_imagemap_template.find('.imagemap_template_box.active').data('template_area');

        imagemap_area_num = parseInt(imagemap_area_num);
        imagemap_area_num = (imagemap_area_num <=6) ? imagemap_area_num : 6;
        if(imagemap_area_num) {
            var imagemap_group = $('.message_bot_area .imagemap_group'),
                imagemap_template_type = modal_imagemap_template.find('.imagemap_template_box.active').data('template_type');

            imagemap_group.find('input.template_type').val(imagemap_template_type);
            imagemap_group.find('.imagemap_template_selected').attr('src', imagemap_template_path + 'type_' + imagemap_template_type + '_guide_s.png');
            addImageMapButton(imagemap_area_num);

            //get data if select template by user, not js
            if(event.originalEvent != void 0) {
                getDataFromInput(type_imagemap);
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

    // remove button block and button in confirm block
    $(document).on("click", '.btn_box .delete_btn_box', function (event) {
        var bot_message_type    = botMessageType(),
            type                = common_data['bot_content_type'];

        switch (bot_message_type) {
            case type[type_button]: {
                if($('.button_group .button_container .btn_box').length > 1) {
                    $(this).parents('.btn_box').remove();
                    checkButtonBox();
                    getDataFromInput(type_button);
                }
                break;
            }
            case type[type_confirm]: {
                if($('.confirm_group .confirm_container .btn_box').length > 1) {
                    $(this).parents('.btn_box').remove();
                    checkButtonBox();
                    getDataFromInput(type_confirm);
                }
                break;
            }
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
            getDataFromInput(type_carousel);
            check_add_item_common();
            setHeightSlidebar();
        }
    });

    // remove generic button block
    $(document).on("click", '.button_type_container .delete_btn_box', function (event) {
        var generic_element = $(this).parents('.generic_box');
        if(generic_element.find('.button_type_container').length > 1) {
            $(this).parents('.button_type_container').remove();
            checkGenericButtonBox();
            getDataFromInput(type_carousel);
        }
    });

    //add a message block if not exist any user message and bot message
    if(!$('.scenario_block .user_scenario').length && !$('.scenario_block .bot_scenario').length) {
        $('.add_pattern_bot').click();
    }

    //re-sort position message box
    $('.scenario_block').sortable({
        items: ".message_box",
        cancel: ".bot_preview_demo, .bot_media_demo, .user_form_box",
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
    var bot_message_type    = botMessageType(),
        type                = common_data['bot_content_type'],
        container,
        button_max          = null;

    switch (bot_message_type) {
        case type[type_button]: {
            container  = $('.button_group .button_container');
            button_max = max_button_block;
            break;
        }
        case type[type_confirm]: {
            container  = $('.confirm_group .confirm_container');
            button_max = max_confirm_button;
            break;
        }
    }
    var button_box_count = container.find('.btn_box').length;

    if(button_max != null && container && button_box_count < max_button_block) {
        var clone = $('.scenario_block_origin .btn_box').clone();
        //if is confirm then remove delete button box
        if(bot_message_type == type[type_confirm]) {
            clone.find('.button_delete_box').remove();
        }
        container.append(clone);
        checkButtonBox();
    }
}

/**
 * add new generic child button
 */
function addGenericButton() {
    var generic_active = genericActiveElement();
    if(generic_active.find('.button_type_container').length < max_carousel_button) {
        var clone_button = $('.scenario_block_origin .generic_button_template .button_type_container').clone();
        generic_active.find('.generic_button_container').append(clone_button);
        checkGenericButtonBox();
    }
}

/**
 * add new image map child button
 */
function addImageMapButton(imagemap_area_num) {
    if(imagemap_area_num > 0) {
        var imagemap_group = $('.message_bot_area .imagemap_group .imagemap_container'),
            button_box_count = imagemap_group.find('.btn_box').length;

        if(button_box_count < imagemap_area_num) {
            //add button
            for(var i=button_box_count; i<imagemap_area_num; i++) {
                var clone = $('.scenario_block_origin .btn_box').clone(),
                    template_button = common_data['template_button'];
                clone.prepend('<label class="col-md-12">' + imagemap_btn_label[i] + '</label>');
                clone.find('.button_delete_box, .button_title_box').remove();
                //remove postback option
                clone.find('select.button_sub_type option[value="' + template_button['postback'] + '"]').remove();
                imagemap_group.append(clone);
            }
        } else if(button_box_count > imagemap_area_num) {
            //remove button
            for(var i=imagemap_area_num; i<button_box_count; i++) {
                var btn_box_last = imagemap_group.find('.btn_box').last();
                btn_box_last.remove();
            }
        }
    }
}

/**
 * add new generic block
 */
function createGenericBox() {
    if($('.generic_group .generic_container .generic_box').length < max_carousel_item) {
        var clone = $('.scenario_block_origin .generic_box').clone();
        $('.generic_container').append(clone);
        checkGenericBox();

        //------Carousel process
        //param false: set active for tem
        addCarouselIndicator('generic_indicators', false);
        checkCarouselSlide();

        //add a button for new Generic
        addGenericButton();
    }
}

function uploadFile() {
    $(document).on('click', '.upload_image', function () {
        var box_message = $(this).parents('.file_select_box').find('.box_message'),
            allowdtypes = $(this).data('image_type'),
            max_size_image = 40, //Mb
            max_size_video = 40; //Mb

        allowdtypes = (allowdtypes != void 0 && allowdtypes != '') ? allowdtypes : 'jpeg,jpg,png,mp4';
        allowdtypes = allowdtypes.split(',');
        // image: jpeg, jpg
        // video: mp4, jpeg, jpg
        // imagemap, button, generic: jpeg, jpg, png.
        //Image: max 1mb, video: max 10mb

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

                //check dimension image
                var checked_dimension_flg = true;
                var bot_message_type = botMessageType(),
                    type = common_data['bot_content_type'];
                if(error_name == '' && fileType != 'mp4' && bot_message_type == type[type_imagemap]) {
                    checked_dimension_flg = false;
                    error_name = $(this).data('error_dimension').replace(':width', imagemap_base_size_w).replace(':height', imagemap_base_size_h);
                    //get dimension image
                    var image = new Image();
                    image.onload = function () {
                        if (imagemap_base_size_w == this.naturalWidth && imagemap_base_size_h == this.naturalHeight) {
                            error_name = '';
                        }
                        checked_dimension_flg = true;
                    };
                    image.src = URL.createObjectURL(data_file_upload);
                }
                //wait because image.onload run after
                waitForCheckDimension();
                var time_wait_check = 0;
                function waitForCheckDimension(){
                    //if check dimension is not finish or check is less than 5s
                    if (checked_dimension_flg || time_wait_check >= 50) {
                        if(error_name != '') {
                            setMesssage(error_name, 1, box_message, true);
                        }else{
                            data.submit();
                        }
                    } else {
                        time_wait_check++;
                        setTimeout(function(){waitForCheckDimension()}, 100);
                    }
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

    if(bot_message_type == type[type_carousel]) {
        item_btn_count      = $('.generic_group .generic_container .generic_box').length;
        max_num             = max_carousel_item;
    }
    if(item_btn_count >= max_num) {
        add_group_btn.hide();
    } else {
        add_group_btn.show();
    }
}

//hide, show by check number button box and button in confirm box
function checkButtonBox() {
    var bot_message_type    = botMessageType(),
        type                = common_data['bot_content_type'],
        delete_button       = $('.message_container_input .btn_box .delete_btn_box'),
        button_box_count    = null;

    switch (bot_message_type) {
        case type[type_button]: {
            button_box_count = $('.button_group .button_container .btn_box').length;
            break;
        }
    }

    if(button_box_count != null) {
        //show, hide delete button box
        if(button_box_count <= 1) {
            delete_button.hide();
        } else {
            delete_button.show();
        }
        check_add_item_common();
    }
}

//hide, show by check number generic button box
function checkGenericButtonBox() {
    var button_box          = genericActiveElement(),
        button_box_count    = $(button_box).find('.generic_button_container .button_type_container').length,
        delete_button       = $(button_box).find('.delete_btn_box');
    //show, hide delete button
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
    } else if(bot_message_type == type[type_carousel]) {
        var genericActive   = genericActiveElement();
        item_btn_count      = genericActive.find('.generic_button_container .button_type_container').length;
        max_num             = max_carousel_button;
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
 * show preview sticker
 * @param img_path
 */
function showStickerInputArea(img_path, show_message_demo) {
    if(img_path != void 0 && img_path != '') {
        //show to preview in message preview box
        if(show_message_demo != void 0 && show_message_demo) {
            var preview_url = img_path.replace('.png', '_key.png');
            if(!imageExist(preview_url)) {
                preview_url = img_path;
            }
            var bot_message_input   = botMessageBoxElement();
            bot_message_input.find('.bot_preview_demo img.preview_img').attr('src', preview_url);
        }

        //show to preview in input
        //if not exist large image then get small image
        if(!imageExist(img_path)) {
            img_path = img_path.replace('.png', '_key.png');
        }
        $('.sticker_group .sticker_preview img').attr('src', img_path);
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
        case type_carousel:         getDataGeneric(); break;
        case type_file:             getDataFile(); break;
        case type_confirm:          getDataConfirm(); break;
        case type_location:         getDataLocation(); break;
        case type_sticker:          getDataSticker(); break;
        case type_imagemap:         getDataImagemap(); break;
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
            'type' : 'text',
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
        button_text             = button_box.find('input.button_text').val(),
        image_url               = button_box.find('input.image_url').val(),
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
            'type'      : 'template',
            'altText'   : button_text,
            'template'  : {
                "type"              : "buttons",
                "text"              : button_text
            }
        }
    };
    if(image_url != void 0 && image_url != '') {
        data['message']['template']['thumbnailImageUrl'] = image_url;
    }
    if(button_title != void 0 && button_title != '') {
        data['message']['template']['title'] = button_title;
    }
    data['message']['template']['actions'] = [];

    $('.button_group .button_container .btn_box').each(function (index, element) {
        var button_sub_title     = $(this).find('input.button_sub_title').val(),
            button_sub_type      = $(this).find('select.button_sub_type').val(),
            button_sub_data      = $(this).find('input.button_sub_data').val(),
            button_sub_scenario  = $(this).find('select.button_sub_scenario').val();
        var button_data = {
            'type'  : template_button_flip[button_sub_type],
            'label' : button_sub_title,
        };
        if(button_sub_type == template_button['uri']) {
            button_data['uri']  = button_sub_data;
        } else {
            if(button_sub_type == template_button['message'] && button_sub_data != void 0 && button_sub_data != '') {
                button_data['text'] = button_sub_data;
            }
            if(button_sub_type == template_button['postback']) {
                if(variable_id == void 0 || variable_id == '') {
                    variable_id = '-1';
                }
                var button_sub_title_base64 = base64Encode(button_sub_title, '-1');
                button_data['data']  = 'SCENARIO_' + button_sub_scenario + '_' + variable_id + '_' + button_sub_title_base64;
            }
        }
        data['message']['template']['actions'].push(button_data);
    });
    data = JSON.stringify(data);
    changeDataBotMessage(data);
}

/**
 * get first file
 */
function getDataFile() {
    var original_url    = $('.file_group .file_url_input').val(),
        preview_url     = $('.file_group .preview_url_input').val();
    original_url        = $.trim(original_url);
    preview_url         = $.trim(preview_url);
    var file_type       = checkTypeFile(original_url);

    if(file_type) {
        var data = {
            'message' : {
                'type'                  : file_type,
                'originalContentUrl'    : original_url,
                'previewImageUrl'       : preview_url
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
        template_button_flip    = common_data['template_button_flip'];
    var data = {
        'message' : {
            'type'      : 'template',
            'altText'   : '',
            'template'  : {
                'type'      : 'carousel',
                'columns'   : []
            }
        }
    };
    $('.generic_container .generic_box').each(function (index, element) {
        var title     = $(this).find('input.title').val(),
            text      = $(this).find('input.text').val(),
            image_url = $(this).find('input.image_url').val();
        var generic_data = {
            'text'              : text
        };
        //set first title for altText
        if(data['message']['altText'] == '') {
            data['message']['altText'] = title;
        }

        if(image_url != void 0 && image_url != '') {
            generic_data['thumbnailImageUrl'] = image_url;
        }
        if(title != void 0 && title != '') {
            generic_data['title'] = title;
        }
        generic_data['actions'] = [];

        //loop button in generic box
        $(this).find('.generic_button_container .button_type_container').each(function (index2, element2) {
            var button_title        = $(this).find('input.button_title').val(),
                button_sub_type     = $(this).find('select.button_sub_type').val(),
                button_sub_data     = $(this).find('input.button_sub_data').val(),
                button_sub_scenario = $(this).find('select.button_sub_scenario').val();

            var button_data = {
                'type'      : template_button_flip[button_sub_type],
                'label'     : button_title,
            };

            if(button_sub_type == template_button['uri']) {
                button_data['uri']  = button_sub_data;
            } else {
                if(button_sub_type == template_button['message'] && button_sub_data != void 0 && button_sub_data != '') {
                    button_data['text'] = button_sub_data;
                }
                if(button_sub_type == template_button['postback']) {
                    button_data['data']  = 'SCENARIO_' + button_sub_scenario + '_' + button_title.replace(/[_]/g, '');
                }
            }
            //push to buttons in carousel data
            generic_data['actions'].push(button_data);
        });
        data['message']['template']['columns'].push(generic_data);
    });
    data = JSON.stringify(data);
    changeDataBotMessage(data);
}


/**
 * get button data to json and fill to message box
 */
function getDataConfirm() {
    var confirm_group           = $('.confirm_group .confirm_container'),
        confirm_text            = confirm_group.find('input.confirm_text').val(),
        template_button         = common_data['template_button'],
        template_button_flip    = common_data['template_button_flip'],
        variable_id             = '';

    //set variable if use select box
    if(confirm_group.find('input.confirm_is_variable:checked').val()) {
        variable_id             = confirm_group.find('select.confirm_variable').val();
    }

    var data = {
        'variable' : variable_id,
        'message' : {
            'type'      : 'template',
            'altText'   : confirm_text,
            'template'  : {
                'type'      : 'confirm',
                'text'      : confirm_text,
                'actions'   : []
            }
        }
    };

    $('.confirm_group .confirm_container .btn_box').each(function (index, element) {
        var button_sub_title     = $(this).find('input.button_sub_title').val(),
            button_sub_type      = $(this).find('select.button_sub_type').val(),
            button_sub_data      = $(this).find('input.button_sub_data').val(),
            button_sub_scenario  = $(this).find('select.button_sub_scenario').val();
        var button_data = {
            'type'  : template_button_flip[button_sub_type],
            'label' : button_sub_title
        };
        if(button_sub_type == template_button['uri']) {
            button_data['uri']  = button_sub_data;
        } else {
            if(button_sub_type == template_button['message'] && button_sub_data != void 0 && button_sub_data != '') {
                button_data['text'] = button_sub_data;
            }
            if(button_sub_type == template_button['postback']) {
                if(variable_id == void 0 || variable_id == '') {
                    variable_id = '-1';
                }
                var button_sub_title_base64 = base64Encode(button_sub_title, '-1');
                button_data['data']  = 'SCENARIO_' + button_sub_scenario + '_' + variable_id + '_' + button_sub_title_base64;
            }
        }
        data['message']['template']['actions'].push(button_data);
    });

    data = JSON.stringify(data);
    changeDataBotMessage(data);
}

/**
 * get location data to json and fill to message box
 */
function getDataLocation() {
    var location_group = $('.location_group'),
        title          = location_group.find('input.title').val(),
        address        = location_group.find('input.address').val(),
        latitude       = location_group.find('input.latitude').val(),
        longitude      = location_group.find('input.longitude').val(),
        data    = {
            'message' : {
                'type'      : 'location',
                'title'     : title,
                'address'   : address,
                'latitude'  : latitude,
                'longitude' : longitude
            }
        };

    data = JSON.stringify(data);
    changeDataBotMessage(data);
}

/**
 * get sticker data to json and fill to message box
 */
function getDataSticker() {
    var location_group = $('.sticker_group'),
        package_id     = location_group.find('input.package_id').val(),
        sticker_id     = location_group.find('input.sticker_id').val(),
        data           = {
            'message' : {
                'type'      : 'sticker',
                'packageId' : package_id,
                'stickerId' : sticker_id
            }
        };

    data = JSON.stringify(data);
    changeDataBotMessage(data);
}

/**
 * get imagemap data to json and fill to message box
 */
function getDataImagemap() {
    var imagemap_group          = $('.imagemap_group'),
        template_button         = common_data['template_button'],
        template_button_flip    = common_data['template_button_flip'],
        imagemap_template_type  = imagemap_group.find('input.template_type').val(),
        image_url               = imagemap_group.find('textarea.imagemap_url').val(),
        alt_text                = imagemap_group.find('input.alt_text').val(),
        data = {
            'template_type' : imagemap_template_type,
            'message' : {
                'type'      : 'imagemap',
                'baseUrl'   : image_url,
                'altText'   : alt_text,
                'baseSize'  : {
                    'height'    : imagemap_base_size_h,
                    'width'     : imagemap_base_size_w
                },
                'actions'   : []
            }
        };

    imagemap_group.find('.imagemap_container .btn_box').each(function (index, element) {
        var button_sub_type      = $(this).find('select.button_sub_type').val(),
            button_sub_data      = $(this).find('input.button_sub_data').val();

        var button_data = {
            'type'  : template_button_flip[button_sub_type]
        };
        if(button_sub_type == template_button['uri']) {
            button_data['linkUri']  = button_sub_data;

        } else if(button_sub_type == template_button['message']) {
            button_data['text'] = button_sub_data;
        }
        //area for image map
        var imagemap_area_index = index+1;
        button_data['area'] = getAreaImagemap(imagemap_template_type, imagemap_area_index);

        data['message']['actions'].push(button_data);
    });

    data = JSON.stringify(data);
    changeDataBotMessage(data);

    function getAreaImagemap(template_type, area_index) {
        var result = {
            "x": 0,
            "y": 0,
            "width": 0,
            "height": 0
        };

        if(template_type != void 0 && template_type > 0 && template_type <= 8) {
            var w_half = Math.floor(imagemap_base_size_w / 2);
            var h_half = Math.floor(imagemap_base_size_h / 2);
            var w_third = Math.floor(imagemap_base_size_w / 3);
            var h_third = Math.floor(imagemap_base_size_h / 3);
            var w_four = Math.floor(imagemap_base_size_w / 4);
            var h_four = Math.floor(imagemap_base_size_h / 4);

            //imagemap_area_index is number from 1->6 (area 1->6)
            template_type = parseInt(template_type);
            area_index = parseInt(area_index);
            switch (template_type) {
                case 1:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                     A
                     */
                    result['width'] = imagemap_base_size_w;
                    result['height'] = imagemap_base_size_h;
                    break;
                case 2:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                         |
                     A   |   B
                         |
                     */
                    result['width'] = w_half;
                    result['height'] = imagemap_base_size_h;
                    if (area_index == 2) {
                        result['x'] = w_half;
                    }
                    break;
                case 3:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                        A
                     -------
                        B
                     */
                    result['width'] = imagemap_base_size_w;
                    result['height'] = h_half;
                    if (area_index == 2) {
                        result['y'] = h_half;
                    }
                    break;
                case 4:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                     A           A
                     -------------
                     B           B
                     -------------
                     C           C
                     */
                    result['width'] = imagemap_base_size_w;
                    result['height'] = h_third;
                    if (area_index == 2) {
                        result['y'] = h_half;

                    } else if (area_index == 3) {
                        result['y'] = h_third*2;
                    }
                    break;
                case 5:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                     A   |  B
                     ----|----
                     C   |  D
                     */
                    result['width'] = w_half;
                    result['height'] = h_half;
                    if (area_index == 2) {
                        result['x'] = w_half;

                    } else if (area_index == 3) {
                        result['y'] = h_half;

                    } else if (area_index == 4) {
                        result['x'] = w_half;
                        result['y'] = h_half;
                    }
                    break;
                case 6:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                        A
                    ---------
                     B  |  C
                     */
                    result['height'] = h_half;
                    if (area_index == 1) {
                        result['width'] = imagemap_base_size_w;

                    } else if (area_index == 2) {
                        result['width'] = w_half;
                        result['y'] = h_half;

                    } else if (area_index == 3) {
                        result['width'] = w_half;
                        result['x'] = w_half;
                        result['y'] = h_half;
                    }
                    break;
                case 7:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                         A (W: 50%)
                         A
                     ---------
                         B (W: 25%)
                     ---------
                         c (W: 25%)
                     */
                    result['width'] = imagemap_base_size_w;
                    if (area_index == 1) {
                        result['height'] = h_half;

                    } else if (area_index == 2) {
                        result['height'] = h_four;
                        result['y'] = h_four*2;

                    } else if (area_index == 3) {
                        result['height'] = h_four;
                        result['y'] = h_four*3;
                    }
                    break;
                case 8:
                    /** Image preview (1: A, 2: B, 3: C, 4: D, 5: E, 6: F)
                     A   |  B  |  C
                     ----|-----|----
                     D   |  E  |  F
                     */
                    result['width'] = w_third;
                    result['height'] = h_half;
                    if (area_index == 2) {
                        result['x'] = w_third;
                    } else if (area_index == 3) {
                        result['x'] = w_third*2;

                    } else if (area_index == 4) {
                        result['y'] = h_half;

                    } else if (area_index == 5) {
                        result['x'] = w_third;
                        result['y'] = h_half;

                    } else if (area_index == 6) {
                        result['x'] = w_third*2;
                        result['y'] = h_half;
                    }
                    break;
            }
        }
        return result;
    }
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

        } else if(bot_message_type == type[type_carousel]) {
            value = '';
        } else if(bot_message_type == type[type_confirm]) {
            value = $('.confirm_group input.confirm_text').val();
        } else if(bot_message_type == type[type_file]) {
            value = $('.file_group .file_url_input').val();
        } else if(bot_message_type == type[type_location]) {
            value = $('.location_group input.title').val();
        } else if(bot_message_type == type[type_sticker]) {
            value = '';
        } else if(bot_message_type == type[type_imagemap]) {
            value = $('.imagemap_group .imagemap_url').val();
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

            $('.message_user_area .api_variable_setting_group').show();
            user_message.find('.user_media_content ').removeClass('hidden');
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
    button_element_select();
    //clear data generic
    $('.generic_group .generic_container .generic_box').remove();
    $('.generic_indicators li').remove();
    //clear data confirm
    $('.confirm_group input.confirm_text').val('');
    $('.confirm_group .btn_box').remove();
    //clear data location
    $('.location_group input').val('');
    //clear validation class in select2 select
    $('.message_bot_area .select2 .select2-selection').removeClass('validation-failed');

    //sticker show hide element
    var bot_message_input   = botMessageBoxElement();
    //hide sticker preview demo and show media demo in message chat box
    bot_message_input.find('.bot_preview_demo').addClass('hidden');
    bot_message_input.find('.bot_preview_demo img.preview_img').attr('src', '/images/no_sticker_available.png');
    bot_message_input.find('.bot_media_demo').removeClass('hidden');
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
            $('.file_group').find('.file_url_input, .preview_url_input').val('');
            changeLabelMediaDemo(type_file);
            break;
        case type[type_carousel]:
            generic_element_select();
            //add first generic box
            createGenericBox();
            changeLabelMediaDemo(type_carousel);
            break;
        case type[type_confirm]:
            $('.message_bot_area .confirm_group').show();
            //set un-checked input variable
            icheckAuto('uncheck', $('.message_bot_area .confirm_group #confirm_is_variable'));
            //add 2 button box
            for(var i=0; i<=1; i++) {
                addButtonBox();
            }
            changeLabelMediaDemo(type_confirm);
            break;
        case type[type_location]:
            $('.message_bot_area .location_group').show();
            changeLabelMediaDemo(type_location);
            break;
        case type[type_sticker]:
            //show sticker select
            sticker_element_select();
            changeLabelMediaDemo('');
            //show sticker message demo in message box
            bot_message_input.find('.bot_preview_demo').removeClass('hidden');
            bot_message_input.find('.bot_media_demo').addClass('hidden');
            break;
        case type[type_imagemap]:
            imagemap_element_select();
            changeLabelMediaDemo(type_imagemap);
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
            $('.file_group').find('.file_url_input, .preview_url_input').val('');
            break;
        case type[type_carousel]:
            generic_element_select();
            break;
        case type[type_confirm]:
            $('.message_bot_area .confirm_group').show();
            break;
        case type[type_location]:
            $('.location_group input').val('');
            $('.message_bot_area .location_group').show();
            break;
        case type[type_sticker]:
            sticker_element_select();
            break;
        case type[type_imagemap]:
            imagemap_element_select();
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
    //clear common error
    $('.message_bot_area .common_error').addClass('hidden').find('.error_container').html('');

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
function button_element_select() {
    var button_box = $('.button_group .button_container');
    button_box.find('input.button_title').val('');
    button_box.find('input.button_text').val('');
    button_box.find('input.image_url').val('');
    button_box.find('.btn_box').remove();
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

/**
 * action for elements when select sticker in bot message
 */
function sticker_element_select() {
    var sticker_group = $('.message_bot_area .sticker_group');
    sticker_group.show();
    sticker_group.find('.sticker_preview img').attr('src', '');
    sticker_group.find('input.package_id').val('');
    sticker_group.find('input.sticker_id').val('');
}

/**
 * action for elements when select imagemap in bot message
 */
function imagemap_element_select() {
    var imagemap_group = $('.message_bot_area .imagemap_group');
    imagemap_group.find('input.template_type, input.imagemap_url, input.alt_text').val('');
    imagemap_group.find('.btn_box').remove();
    //select first imagemap template
    setFirstTemplateImagemap();
    imagemap_group.show();
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
        user_message_nlp  = userMessageNlp(),
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

        if(bot_message_type == type[type_button] || bot_message_type == type[type_confirm]) {
            //remove old data
            if(bot_message_type == type[type_button]) {
                var button_group = $('.button_group');
                var variable_checkbox = button_group.find('#btn_is_variable'),
                    variable_select = button_group.find('select.button_variable');

                //clear data button
                button_element_select();
            } else if(bot_message_type == type[type_confirm]) {
                var confirm_group = $('.confirm_group');
                var variable_checkbox = confirm_group.find('#confirm_is_variable'),
                    variable_select = confirm_group.find('select.confirm_variable');

                //clear data confirm
                $('.confirm_group input.confirm_text').val('');
                $('.confirm_group .btn_box').remove();
            }

            var button_number = 0;

            if(jsonData != void 0 && jsonData) {
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

                if(jsonData['message'] && jsonData['message']['template']) {
                    jsonData            = jsonData['message'];
                    var data_template   = jsonData['template'],
                        button_box      = null;

                    if(bot_message_type == type[type_button]) {
                        button_box      = $('.button_group .button_container');
                        button_box.find('input.button_title').val(data_template['title']);
                        button_box.find('input.button_text').val(data_template['text']);
                        button_box.find('input.image_url').val(data_template['thumbnailImageUrl']);

                    } else if(bot_message_type == type[type_confirm]) {
                        button_box      = $('.confirm_group .confirm_container');
                        button_box.find('input.confirm_text').val(data_template['text']);
                    }

                    //create button detail
                    var buttons = data_template['actions'];
                    button_number = buttons.length;
                    for (var i=0; i<buttons.length; i++) {
                        var clone       = $('.scenario_block_origin .btn_box').clone(),
                            button_data = buttons[i],
                            button_sub_data     = clone.find('input.button_sub_data'),
                            placeholder_text    = button_sub_data.data('label_text');

                        //remove delete button box if is confirm type
                        if(bot_message_type == type[type_confirm]) {
                            clone.find('.button_delete_box').remove();
                        }
                        clone.find('input.button_sub_title').val(button_data['label']);
                        clone.find('select.button_sub_type').val(template_button[button_data['type']]);

                        button_sub_data.removeClass('validate-require validate-url validate-white-list maximum-length-1000 maximum-length-300');
                        button_sub_data.val(button_data['text']);

                        if(button_data['type'] == 'postback') {
                            var custom_data = button_data['data'].split('_');
                            custom_data = custom_data[1];
                            clone.find('select.button_sub_scenario').val(custom_data);
                            clone.find('.button_scenario_box').show();
                            button_sub_data.addClass('maximum-length-300 hidden').attr('placeholder', 'placeholder_text');

                        } else if(button_data['type'] == 'uri') {
                            placeholder_text = button_sub_data.data('label_url');
                            button_sub_data.addClass('validate-require validate-url validate-white-list maximum-length-1000').removeClass('hidden');
                            button_sub_data.val(button_data['uri']);

                        } else if(button_data['type'] == 'message') {
                            button_sub_data.addClass('validate-require maximum-length-300').removeClass('hidden');
                        }
                        button_sub_data.attr('placeholder', placeholder_text);

                        if(button_box != null) {
                            button_box.append(clone);
                        }
                        checkButtonBox();
                    }
                }
            }
            //add button box for button and confirm type
            if(button_number <= 0) {
                var button_init = 1;
                if(bot_message_type == type[type_confirm]) {
                    button_init = max_confirm_button;
                }
                for(var i=1; i<=button_init; i++) {
                    addButtonBox();
                }
            }
            //re-set default value
            icheck_change_event = true;

        } else if(bot_message_type == type[type_carousel]) {
            //remove all generic before action
            $('.generic_group .generic_container .generic_box').remove();
            $('.generic_indicators li').remove();

            if(jsonData && jsonData['message']) {
                jsonData = jsonData['message']['template']['columns'];
                for (var i=0; i<jsonData.length; i++) {
                    var clone           = $('.scenario_block_origin .generic_box').clone(),
                        generic_data    = jsonData[i];

                    clone.find('input.title').val(generic_data['title']);
                    clone.find('input.text').val(generic_data['text']);
                    clone.find('input.image_url').val(generic_data['thumbnailImageUrl']);
                    //add a button for new Generic
                    //------Carousel process
                    //add class active for first item to show Carousel
                    for (var j=0; j<generic_data['actions'].length; j++) {
                        var button_data     = generic_data['actions'][j],
                            button_clone    = $('.scenario_block_origin .generic_button_template .button_type_container').clone(),
                            button_sub_data = button_clone.find('input.button_sub_data'),
                            placeholder_text = button_sub_data.data('label_text');

                        button_clone.find('select.button_sub_type').val(template_button[button_data['type']]);
                        button_clone.find('input.button_title').val(button_data['label']);

                        button_sub_data.removeClass('validate-require validate-url validate-white-list maximum-length-1000 maximum-length-300');
                        button_sub_data.val(button_data['text']);

                        if(button_data['type'] == 'postback') {
                            var custom_data = button_data['data'].split('_');
                            custom_data = custom_data[1];
                            button_clone.find('select.button_sub_scenario').val(custom_data);
                            button_clone.find('.button_scenario_box').show();
                            button_sub_data.addClass('maximum-length-300 hidden');
                        } else if(button_data['type'] == 'uri') {
                            placeholder_text = button_sub_data.data('label_url');
                            button_sub_data.addClass('validate-require validate-url validate-white-list maximum-length-1000');
                            button_sub_data.val(button_data['uri']);
                        } else if(button_data['type'] == 'message') {
                            button_sub_data.addClass('validate-require maximum-length-300');
                        }
                        button_sub_data.attr('placeholder', placeholder_text);

                        clone.find('.generic_button_container').append(button_clone);
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
        } else if(bot_message_type == type[type_file]) {
            if(jsonData && jsonData['message']) {
                jsonData            = jsonData['message'];
                var original_url    = jsonData['originalContentUrl'] ? jsonData['originalContentUrl'] : '',
                    preview_url     = jsonData['previewImageUrl'] ? jsonData['previewImageUrl'] : '';
                //set file url for file input
                $('.file_group .file_url_input').val(original_url);
                $('.file_group .preview_url_input').val(preview_url);
            }
        } else if(bot_message_type == type[type_location]) {
            if(jsonData && jsonData['message']) {
                jsonData            = jsonData['message'];
                var title           = jsonData['title'] ? jsonData['title'] : '',
                    address         = jsonData['address'] ? jsonData['address'] : '',
                    latitude        = jsonData['latitude'] ? jsonData['latitude'] : '',
                    longitude       = jsonData['longitude'] ? jsonData['longitude'] : '',
                    location_group  = $('.location_group');

                location_group.find('input.title').val(title);
                location_group.find('input.address').val(address);
                location_group.find('input.latitude').val(latitude);
                location_group.find('input.longitude').val(longitude);
            }
        } else if(bot_message_type == type[type_sticker]) {
            if(jsonData && jsonData['message']) {
                jsonData            = jsonData['message'];
                var package_id      = jsonData['packageId'] ? jsonData['packageId'] : '',
                    sticker_id      = jsonData['stickerId'] ? jsonData['stickerId'] : '',
                    sticker_group   = $('.sticker_group');

                sticker_group.find('input.package_id').val(package_id);
                sticker_group.find('input.sticker_id').val(sticker_id);

                //show to preview in input area
                var sticker_path    = '/images/line_sticker/package_' + package_id + '/' + sticker_id + '.png';
                showStickerInputArea(sticker_path);
            }
        } else if(bot_message_type == type[type_imagemap]) {
            var button_number = 0;
            if(jsonData != void 0 && jsonData && jsonData['message']) {
                var imagemap_group = $('.imagemap_group .imagemap_container');
                imagemap_group.find('input.template_type').val(jsonData['template_type']);
                imagemap_group.find('.imagemap_template_selected').attr('src', imagemap_template_path + 'type_' + jsonData['template_type'] + '_guide_s.png');

                jsonData = jsonData['message'];
                var buttons = jsonData['actions'];

                imagemap_group.find('.imagemap_url').val(jsonData['baseUrl']);
                imagemap_group.find('input.alt_text').val(jsonData['altText']);
                //create button detail
                button_number = buttons.length;
                if(button_number) {
                    //remove old button
                    imagemap_group.find('.btn_box').remove();
                    for (var i=0; i<buttons.length; i++) {
                        var clone       = $('.scenario_block_origin .btn_box').clone(),
                            button_data = buttons[i],
                            button_sub_data  = clone.find('input.button_sub_data'),
                            placeholder_text = button_sub_data.data('label_text');

                        clone.prepend('<label class="col-md-12">' + imagemap_btn_label[i] + '</label>');
                        clone.find('.button_delete_box, .button_title_box').remove();
                        //remove postback option
                        clone.find('select.button_sub_type option[value="' + template_button['postback'] + '"]').remove();

                        clone.find('select.button_sub_type').val(template_button[button_data['type']]);
                        button_sub_data.removeClass('validate-require validate-url validate-white-list maximum-length-1000 maximum-length-300');

                        if(button_data['type'] == 'uri') {
                            button_sub_data.val(button_data['linkUri']);
                            placeholder_text = button_sub_data.data('label_url');
                            button_sub_data.addClass('validate-require validate-url validate-white-list maximum-length-1000').removeClass('hidden');

                        } else if(button_data['type'] == 'message') {
                            button_sub_data.val(button_data['text']);
                            button_sub_data.addClass('validate-require maximum-length-400').removeClass('hidden');
                        }
                        button_sub_data.attr('placeholder', placeholder_text);

                        if(imagemap_group != null) {
                            imagemap_group.append(clone);
                        }
                    }
                }
            }
            //select first template if not exist any button
            if(button_number <= 0) {
                setFirstTemplateImagemap();
            }
        }  else if(bot_message_type == type[type_api]) {
            if(jsonData && jsonData['message'] && jsonData['message']['api'] && jsonData['message'] != '') {
                var api = jsonData['message']['api'];
                //select option
                apiElement(api);
            } else {
                //re get api if exist any api item
                getDataFromInput(type_api);
            }
        } else if(bot_message_type == type[type_scenario_connect] && jsonData['message'] != '') {
            if(jsonData && jsonData['message']  && jsonData['message']['scenario']) {
                var scenario = jsonData['message']['scenario'];
                //select option
                scenarioConnectElement(scenario);
            } else {
                //re get scenario if exist any api item
                getDataFromInput(type_scenario_connect);
            }
        } else if(bot_message_type == type[type_mail] && jsonData['message'] != '') {
            if(jsonData && jsonData['message']) {
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
        common_error_box        = $('.message_bot_area .common_error'),
        group_box               = $('.message_bot_area .add_group_box'),
        group_box_header        = 0;
    if(group_box.css('display') != 'none') {
        group_box_header += group_box.innerHeight() + parseInt(group_box.css('margin-bottom'));
    }
    group_box_header += common_error_box.innerHeight() + parseInt(common_error_box.css('margin-bottom'));

    var height_box = fixedsidebar - space - group_box_header;
    $('.fixedsidebar .message_bot_area .slimScrollDiv').css('height', height_box + 'px');
    $('.message_bot_area .fixedsidebar-content').css('height', height_box + 'px');
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
        case type[type_carousel]:
            form_box = $('.message_bot_area .generic_group');
            break;
        case type[type_file]:
            form_box = $('.message_bot_area .file_group');
            break;
        case type[type_confirm]:
            form_box = $('.message_bot_area .confirm_group');
            break;
        case type[type_location]:
            form_box = $('.message_bot_area .location_group');
            break;
        case type[type_sticker]:
            form_box = $('.message_bot_area .sticker_group');
            break;
        case type[type_imagemap]:
            form_box = $('.message_bot_area .imagemap_group');
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
        bot_media_content = bot_box.find('.bot_media_content'),
        validate          = true;

    if(form_box != void 0 && form_box != '' && form_box.length) {
        validate = validation_scenario('line', form_box, bot_message_type);
        //if not demo bot input (bot_media_content) then get input messages_bot_content (only text type)
        if(!bot_media_content.length) {
            bot_media_content = bot_box.find('.messages_bot_content');
        }
    }
    //validate button number of carousel
    var validate_carousel = validateCarouselButton();

    if(!validate || !validate_carousel) {
        result = false;
        form_style        = {'border' : '1px solid #ff5f5f'};
    }
    setStyleElm(bot_media_content, form_style);

    return result;
}

/**
 * Validate button number must equal in per carousels
 * @returns {boolean}
 */
function validateCarouselButton() {
    var bot_message_type    = botMessageType(),
        type                = common_data['bot_content_type'];

    //clear common error
    $('.message_bot_area .common_error').addClass('hidden').find('.error_container').html('');

    if(bot_message_type == type[type_carousel]) {
        var bot_message_val = botMessageContent(),
        jsonData            = jsonConverse(bot_message_val);

        if(jsonData && jsonData['message']) {
            jsonData = jsonData['message']['template']['columns'];
            var count = null;
            for (var i=0; i<jsonData.length; i++) {
                var carousel_item = jsonData[i];
                if(carousel_item['actions'] != void 0 && carousel_item['actions'] != '') {
                    var count_action = carousel_item['actions'].length;
                    if(count == null) {
                        //set first button number to check in next carousel
                        count = count_action;
                    } else if (count != count_action) {
                        var validation_msg_list = common_data['validation_msg_list'];
                        if(validation_msg_list != void 0 && validation_msg_list['validate_carousel_buton_number'] != void 0) {
                            var notice = '<label class="error">' + validation_msg_list['validate_carousel_buton_number'] + '</label>';
                            $('.message_bot_area .common_error').removeClass('hidden').find('.error_container').append(notice);
                        }
                        return false;
                    }
                }
            }
        }
    }
    return true;
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
 *
 * @param img_path
 * @returns {boolean}
 */
function imageExist(img_path)
{
    var http = new XMLHttpRequest();
    http.open('HEAD', img_path, false);
    http.send();
    return http.status != 404;
}

/**
 * set select first option for select input
 * @param elm
 */
function selectFirstOption(elm) {
    elm.val(elm.find('option').first().val());
}

/**
 * select first teplate for imagemap type
 */
function setFirstTemplateImagemap() {
    var modal_imagemap_template = $('#line_imagemap_template_modal');
    modal_imagemap_template.find('.imagemap_template_content .imagemap_template_box').first().click();
    modal_imagemap_template.find('.btn-modal-select').click();
}
