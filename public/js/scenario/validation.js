/**
 * Created by nguyen.khac.tung on 4/19/2017.
 */
var error_class   = 'validation-failed',
    success_class = 'validation-pass',
    bot_content_type = null;

function validation_scenario (service_name, form, bot_message_type) {
    var validation_result   = true,
        validation_msg_list = common_data['validation_msg_list'];
    bot_content_type    = common_data['bot_content_type'];

    var validate_types = {
        validate_require    : "validate-require",
        validate_url        : "validate-url",
        validate_url_secure : "validate-url-secure",
        validate_max        : "validate-max",
        validate_min        : "validate-min",
        validate_phone      : "validate-phone",
        validate_white_list : "validate-white-list",
        validate_number     : "validate-number"
    };
    //converse to array
    var validate_value = Object.keys(validate_types).map(function(key) {
        return validate_types[key];
    });

    clearCrouselError();

    //check one checkbox is checked then not check other checkbox same name
    var checkbox_name_list = {};

    $(form.find('input:not(.hidden), textarea:not(.hidden), select:not(.hidden)')).each(function (index, elm) {
        var elm_class = $(elm).attr('class');

        if(!isEmpty(elm_class)) {
            //clear old validate class
            $(elm).removeClass(error_class);
            $(elm).removeClass(success_class);
            //if is select and using select2
            if(isSelect2(elm)) {
                $(elm).next('.select2').find('.select2-selection').removeClass(error_class).removeClass(success_class);
            }

            check(elm);
        }
    });
    return validation_result;

    function check(elm) {
        var result_check    = true;
        var elm_class       = $(elm).attr('class');
        var elm_class_arr   = $(elm).attr('class').split(' ');
        var value           = '';


        if($(elm).attr('type') == 'checkbox' || $(elm).attr('type') == 'radio') {
            if(!isEmpty($(elm).attr('name'))) {
                var input_name = $(elm).attr('name');

                //not check if any checkbox is checked
                if(!isEmpty(checkbox_name_list[input_name]) && checkbox_name_list[input_name]) {
                    return result_check;
                }
                //add input name to checkbox_name_list
                if(isEmpty(checkbox_name_list[input_name])) {
                    checkbox_name_list[input_name] = 0;
                }
                var input_list  = $(elm).parents('.type_group').find('input[name="' + input_name + '"]:checked');
                value           = $.map(input_list, function(c){return c.value;});

                //set flg 1 for input name. Not check after checkbox checked
                if(!isEmpty(value)) {
                    checkbox_name_list[input_name] = 1;
                }
            } else {
                value       = $(elm)[0].checked;
            }
        } else {
            value           = $(elm).val();
        }

        //each class check if is validate class and call to check process
        $(elm_class_arr).each(function (i, class_name) {
            var error_code      = '';
            var error_param_msg = {};
            if(result_check == true && validate_value.indexOf(class_name) != -1) {
                if(class_name == validate_types.validate_require && isEmpty(value)) {
                    result_check = !(elm_class_arr.indexOf(validate_types.validate_require) != -1);

                } else if(class_name != validate_types.validate_require && !isEmpty(value)) {
                    switch (class_name) {
                        //'Please enter a valid URL. Protocol is required (http://, https:// or ftp://)'
                        case validate_types.validate_url :
                            var value_replace = value.replace(/[{}]/g, '');
                            result_check = /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value_replace);
                            break;
                        //'Please enter a valid URL. Protocol is required (http://, https:// or ftp://)'
                        case validate_types.validate_url_secure :
                            var value_replace = value.replace(/[{}]/g, '');
                            result_check = /^(https):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value_replace);
                            break;
                        //'Please enter a valid phone number. For example (123) 456-7890 or 123-456-7890.'
                        //Origin backup: /^((\d[-. ]?)?((\(\d{3}\))|\d{3}))?[-. ]?\d{3}[-. ]?\d{4}$/.test(value);
                        case validate_types.validate_phone : result_check = /^\+?((\d{1,3}[-. ]?)?((\(\d{1,3}\))|\d{1,3}))?[-. ]?\d{1,3}[-. ]?\d{1,4}$/.test(value);
                            break;
                        //'Text length does not satisfy specified text range.'
                        case validate_types.validate_max :
                            var hasMax = /maximum-length-+[0-9]*/.test(elm_class);
                            if (hasMax) {
                                var reMax       = elm_class.match(/maximum-length-+[0-9]*/).toString(),
                                    length      = reMax.split('-')[2];
                                if(value.length > length) {
                                    result_check = false;
                                    error_param_msg['max'] = length;
                                }
                            }
                            break;
                        case validate_types.validate_min :
                            var hasMin = /minimum-length-+[0-9]*/.test(elm_class);
                            if (hasMin) {
                                var reMin       = elm_class.match(/minimum-length-+[0-9]*/).toString(),
                                    length      = reMin.split('-')[2];
                                if(value.length < length) {
                                    result_check = false;
                                    error_param_msg['min'] = length;
                                }
                            }
                            break;
                        case validate_types.validate_white_list :
                            var white_list_domain   = common_data['white_list_domain'];

                            if(white_list_domain != void 0 && white_list_domain.length) {
                                var result_input    = false;
                                $(white_list_domain).each(function (i, domain) {
                                    if(domain != '' && domain != void 0 && domain != null) {
                                        var reg                = /[a-z0-9]+\.*[a-z0-9]+\.[a-z]+/,
                                            white_domain_clear = domain.match(reg),
                                            value_clear        = value.match(reg);
                                        if(value_clear.toString() == white_domain_clear.toString()) {
                                            result_input = true;
                                        }
                                    }
                                });
                                if(!result_input) {
                                    result_check = false;
                                }
                            }
                            break;
                        case validate_types.validate_number : result_check = !isNaN(value) && /^\s*-?\d*(\.\d*)?\s*$/.test(value);
                            break;
                    }
                }
                //remove old error
                clearMsg(elm);
                if(!result_check) {
                    error_code = class_name;
                    validate_view(elm, result_check, error_code, error_param_msg);

                    //show error in Carousel slide
                    showErrorCarouselItem(service_name, elm, bot_message_type);
                }
            }
        });
        return result_check;
    }

    function isEmpty(v) {
        return (v == '' || (v == null) || (v.length == 0) || /^\s+$/.test(v));
    }

    /**
     * Actions with validate input
     * @param elm
     * @param result_check
     * @param error_code
     * @param error_param_msg
     */
    function validate_view(elm, result_check, error_code, error_param_msg) {
        if($(elm).length) {
            //add message if is error or clear error class if not error
            if (!result_check) {
                validation_result = false;
                //set error class for that elm
                $(elm).addClass(error_class);
                //add message
                if(!isEmpty(error_code)) {
                    error_code = error_code.replace(/-/g, '_');
                    var msg = error_code;
                    if(!isEmpty(validation_msg_list) && !isEmpty(validation_msg_list[error_code])) {
                        //replace attribute message by value
                        msg = setParamMessage(error_param_msg, validation_msg_list[error_code]);
                    }
                    var error_element = "<label class='error'>" + msg + "</label>";

                    //if is select using select2
                    if(isSelect2(elm)) {
                        //set border for select2
                        $(elm).next('.select2').find('.select2-selection').addClass(error_class);
                        //set message after select2
                        $(elm).next('.select2').after(error_element);
                    } else {
                        if($(elm).attr('type') == 'checkbox' || $(elm).attr('type') == 'radio') {
                            var input_type = $(elm).attr('type');

                            $(elm).parents('.' + input_type + '_box').append(error_element);
                        } else {
                            $(elm).after(error_element);
                        }
                    }
                }
            } else {
                $(elm).addClass(success_class);
            }
        }
    }

    /**
     * Clear message
     * @param elm
     */
    function clearMsg(elm) {
        $(elm).parent().find('label.error').remove();

        if($(elm).attr('type') == 'checkbox' || $(elm).attr('type') == 'radio') {
            var input_type = $(elm).attr('type');
            $(elm).parents('.' + input_type + '_box').find('label.error').remove();
        }
    }

    /**
     * Clear error carousel slide
     */
    function clearCrouselError() {
        $('.scenario-edit .message_bot_area .footer_message_input .carousel_indicator_item li').removeClass(error_class);
    }

    /**
     * set value for attribute code in string
     * @param param
     * @param str
     * @returns {*}
     */
    function setParamMessage(param, str) {
        if(!isEmpty(param) && typeof param == 'object') {
            $.each(param, function(code, value) {
                if(!isEmpty(value)) {
                    code = ':' + code;
                    var re = new RegExp(code, "g");
                    str = str.replace(re, value);
                }
            });
        }
        return str;
    }
}

/**
 * Show error in Carousel slide
 */
function showErrorCarouselItem(service_name, elm, bot_message_type) {
    if(bot_content_type != null) {
        var carousel_type = '';
        switch (service_name) {
            case 'facebook' : carousel_type = bot_content_type[type_generic]; break;
            case 'line' : carousel_type = bot_content_type[type_carousel]; break;
        }

        if(bot_message_type == carousel_type) {
            var elm_index = $(elm).parents('.generic_box.item').index(),
                elm_dot   = $('.scenario-edit .message_bot_area .footer_message_input .carousel_indicator_item li').eq(elm_index);
            if(!elm_dot.hasClass(error_class)) {
                elm_dot.addClass(error_class);
            }
        }
    }
}

function isSelect2(elm) {
    if($(elm).hasClass('select2-hidden-accessible') && $(elm).next('.select2').length) {
        return true;
    }
    return false;
}