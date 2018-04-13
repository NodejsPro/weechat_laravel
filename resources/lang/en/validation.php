<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted'             => 'The :attribute must be accepted.',
    'active_url'           => 'The :attribute is not a valid URL.',
    'after'                => 'The :attribute must be a date after :date.',
    'alpha'                => 'The :attribute may only contain letters.',
    'alpha_dash'           => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num'            => 'The :attribute may only contain letters and numbers.',
    'array'                => 'The :attribute must be an array.',
    'before'               => 'The :attribute must be a date before :date.',
    'between'              => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'string'  => 'The :attribute must be between :min and :max characters.',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],
    'boolean'              => 'The :attribute field must be true or false.',
    'confirmed'            => 'The :attribute confirmation does not match.',
    'date'                 => 'The :attribute is not a valid date.',
    'date_format'          => 'The :attribute does not match the format :format.',
    'different'            => 'The :attribute and :other must be different.',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'distinct'             => 'The :attribute field has a duplicate value.',
    'email'                => 'The :attribute must be a valid email address.',
    'exists'               => 'The selected :attribute is invalid.',
    'filled'               => 'The :attribute field is required.',
    'image'                => 'The :attribute must be an image.',
    'in'                   => 'The selected :attribute is invalid.',
    'in_array'             => 'The :attribute field does not exist in :other.',
    'integer'              => 'The :attribute must be an integer.',
    'ip'                   => 'The :attribute must be a valid IP address.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'max'                  => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
   ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'mimetypes'            => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => 'The :attribute field is required.',
    'required_if'          => 'The :attribute field is required.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'unique'               => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'url'                  => 'The :attribute format is invalid.',

    'validate_white_list'   => 'You can not select anything other than the agent\'s white domain',
    'validate_require'      => 'Validation is required',
    'validate_url'          => 'A valid URL is required',
    'validate_url_secure'   => 'Please enter the URL of HTTPS',
    'validate_max'          => 'Must be at least max characters',
    'validate_min'          => 'Must be at least min characters.',
    'validate_phone'        => 'Phone number can not be verified',
    'validate_number'       => 'Enter number only',
    'validate_carousel_buton_number' => 'Please unify the number of buttons in each of carousel',
    'validate_incomplete_field_carousel' => 'Incomplete element data: title and at least one other field (image url, subtitle or buttons) is required with non-empty value',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    'imagemap_dimension_type' => 'Image dimension must be :width pixel x :height pixel in either :type format.',
//    'custom' => [
//        'attribute-name' => [
//            'rule-name' => 'custom-message',
//        ],
//    ],

    'custom' => [
        'channel_type' => [
            'required'  => 'The attribute is required.',
            'numeric'   => 'Enter numbers only in this field',
        ],
        'fb_page_id' => [
            'required'  => 'The attribute is required.',
        ],
        'fb_app_id' => [
            'required'  => 'The attribute is required.',
        ],
        'fb_app_secret' => [
            'required'  => 'The attribute is required.',
        ],
        'fb_page_access_token' => [
            'required'  => 'The attribute is required.',
        ],
        'custom_email' => [
            'required' => 'Please enter your email address',
        ],
        'custom_password' => [
            'required' => 'Please enter your password',
        ],
        'terms_of_use' => [
            'required' => 'You must accept our Terms of Service',
        ]
    ],
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'page_id' => 'Page',
        'bot_name' => 'Bot name',
        'email' => 'Mail address',
        'name' => 'Name',
        'variable_name' => 'Variable name',
        'group_name' => 'Group name',
        'company_name' => 'Company name',
        'password' => 'Password',
        'password_confirmation' => 'Password confirmation',
        'scenario_name' => 'Scenario name',
        'scenario' => 'Scenario',
        'title' => 'Title',
        'type' => 'Type',
        'url' => 'URL',
        'greeting_message' => 'Greeting Message',
        'upload_file' => 'File upload',
        'time' => 'Start time',
        'notification_name' => 'Schedule messages name',
        'api_name' => 'API name',
	    'param.key.*' => 'Key',
	    'max_bot_number' => 'Max Bot number',
	    'max_user_number' => 'Max user number',
        'white_list_domain' => 'White list domain',
        'file' => 'File',
	    'channel_id' => 'Channel ID',
        'app_id' => 'Application ID',
	    'channel_secret' => 'Channel secret',
	    'channel_access_token' => 'Channel access token',
        'page_access_token' => 'Page access token',
        'timezone' => 'Timezone',
        'language' => 'Language',
        'to'       => 'To',
        'content'  => 'Content',
        'subject'  => 'Subject',
        'transfer_from_bot_name' => 'Copy from Bot name',
        'transfer_to_bot' => 'Transfer to Bot',
        'api_type' => 'API type',
        'action'   => 'Activity',
        'email_name' => 'Email name',
        'template_name' => 'Template name',
        'domain' => 'Domain',
        'address' => 'Address',
        'tel' => 'Tel',
        'person_in_charge' => 'Person in charge',
        'business_segments' => 'Business segments',
        'zip_code' => 'Zip code',
        'card_number'   => 'Number Card',
        'month_select' => 'Month',
        'year_select' => 'Year',
        'cvc' => 'CVC',
        'terms_of_use' => 'Terms of use',
        'agree' => 'Agree',
        'nlp_app_id' => 'App Id',
        'culture' => 'Culture',
        'from_name' => 'From name',
        'from_email' => 'From',
        'option' => 'Option',
        'scenario_connect' => 'Scenario connect',
        'webhook_token' => 'Webhook token',
        'api_token' => 'Api token',
        'sheet_id' => ' Spreadsheet Id',
        'detail' => 'Detail',
        'start_date' => 'Start Date',
        'yahoo_url' => 'Thank You Page\'s URL',
        'gateway_name' => 'Payment gateway name',
        'pgcard_shop_id' => 'Shop ID',
        'pgcard_shop_pass' => 'Shop pass',
        'pgcard_site_id' => 'Site ID',
        'pgcard_site_pass' => 'Site pass',
        'api_url' => 'API Url',
    ],

];
