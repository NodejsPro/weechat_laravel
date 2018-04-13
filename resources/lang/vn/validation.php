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

    'accepted'   => 'Trường :attribute phải được chấp nhận.',
    'active_url' => 'Trường :attribute không phải là một URL hợp lệ.',
    'after'      => 'Trường :attribute phải là một ngày sau ngày :date.',
    'alpha'      => 'Trường :attribute chỉ có thể chứa các chữ cái.',
    'alpha_dash' => "Trường :attribute chỉ có thể chứa chữ cái, số và dấu gạch ngang.",
    'alpha_num'  => "Trường :attribute chỉ có thể chứa chữ cái và số.",
    'array'      => 'Kiểu dữ liệu của trường :attribute phải là dạng mảng.',
    'before'     => 'Trường :attribute phải là một ngày trước ngày :date.',
    'between'    => [
        'numeric' => 'Trường :attribute phải nằm trong khoảng :min - :max.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải từ :min - :max kB.',
        'string'  => 'Trường :attribute phải từ :min - :max ký tự.',
        'array'   => 'Trường :attribute phải có từ :min - :max phần tử.',
    ],
    'boolean'              => "Trường :attribute phải là true hoặc false.",
    'confirmed'            => 'Giá trị xác nhận trong trường :attribute không khớp.',
    'date'                 => 'Trường :attribute không phải là định dạng của ngày-tháng.',
    'date_format'          => "Trường :attribute không giống với định dạng :format.",
    'different'            => 'Trường :attribute và :other phải khác nhau.',
    'digits'               => 'Độ dài của trường :attribute phải gồm :digits chữ số.',
    'digits_between'       => 'Độ dài của trường :attribute phải nằm trong khoảng :min and :max chữ số.',
    'dimensions'           => 'Trường :attribute có kích thước không hợp lệ.',
    'distinct'             => 'Trường :attribute có giá trị trùng lặp.',
    'email'                => 'Trường :attribute phải là một địa chỉ email hợp lệ.',
    'exists'               => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'file'                 => 'Trường :attribute phải là một tệp tin.',
    'filled'               => 'Trường :attribute không được bỏ trống.',
    'image'                => 'Trường :attribute phải là định dạng hình ảnh.',
    'in'                   => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'in_array'             => 'Trường :attribute phải thuộc tập cho phép: :other.',
    'integer'              => 'Trường :attribute phải là một số nguyên.',
    'ip'                   => 'Trường :attribute phải là một địa chỉ IP.',
    'json'                 => 'Trường :attribute phải là một chuỗi JSON.',
    'max'                  => [
        'numeric' => 'Trường :attribute không được lớn hơn :max.',
        'file'    => 'Dung lượng tập tin trong trường :attribute không được lớn hơn :max kB.',
        'string'  => 'Trường :attribute không được lớn hơn :max ký tự.',
        'array'   => 'Trường :attribute không được lớn hơn :max phần tử.',
    ],
    'mimes'                => 'Trường :attribute phải là một tập tin có định dạng: :values.',
    'mimetypes'            => 'Trường :attribute phải là một tập tin có định dạng: :values.',
    'min'                  => [
        'numeric' => 'Trường :attribute phải tối thiểu là :min.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải tối thiểu :min kB.',
        'string'  => 'Trường :attribute phải có tối thiểu :min ký tự.',
        'array'   => 'Trường :attribute phải có tối thiểu :min phần tử.',
    ],
    'not_in'               => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'numeric'              => 'Trường :attribute phải là một số.',
    'present'              => 'Trường :attribute phải được cung cấp.',
    'regex'                => 'Định dạng trường :attribute không hợp lệ.',
    'required'             => 'Trường :attribute không được bỏ trống.',
    'required_if'          => 'Trường :attribute không được bỏ trống khi trường :other là :value.',
    'required_unless'      => 'Trường :attribute không được bỏ trống trừ khi :other là :values.',
    'required_with'        => 'Trường :attribute không được bỏ trống khi một trong :values có giá trị.',
    'required_with_all'    => 'Trường :attribute không được bỏ trống khi tất cả :values có giá trị.',
    'required_without'     => 'Trường :attribute không được bỏ trống khi một trong :values không có giá trị.',
    'required_without_all' => 'Trường :attribute không được bỏ trống khi tất cả :values không có giá trị.',
    'same'                 => 'Trường :attribute và :other phải giống nhau.',
    'size'                 => [
        'numeric' => 'Trường :attribute phải bằng :size.',
        'file'    => 'Dung lượng tập tin trong trường :attribute phải bằng :size kB.',
        'string'  => 'Trường :attribute phải chứa :size ký tự.',
        'array'   => 'Trường :attribute phải chứa :size phần tử.',
    ],
    'string'   => 'Trường :attribute phải là một chuỗi ký tự.',
    'timezone' => 'Trường :attribute phải là một múi giờ hợp lệ.',
    'unique'   => 'Trường :attribute đã có trong cơ sở dữ liệu.',
    'uploaded' => 'Trường :attribute tải lên thất bại.',
    'url'      => 'Trường :attribute không giống với định dạng một URL.',

    'validate_white_list'   => 'URL không có trong white list.',
    'validate_require'      => 'Trường này không được bỏ trống.',
    'validate_url'          => 'Định dạng URL không hợp lệ.',
    'validate_url_secure'   => 'Phương thức URL phải là HTTPS.',
    'validate_max'          => 'Trường này không được lớn hơn :max ký tự.',
    'validate_min'          => 'Trường này phải có tối thiểu :min ký tự.',
    'validate_phone'        => 'Số điện thoại không hợp lệ.',
    'validate_number'       => 'Trường này phải là kiểu số.',
    'validate_carousel_buton_number' => 'Số lượng Button trong mỗi Carousel phải bằng nhau.',
    'validate_incomplete_field_carousel' => 'Một trong 3 trường Image URL, subtitle, buttons phải nhập giá trị.',

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
    'imagemap_dimension_type' => 'Kích thước ảnh phải là :width pixel x :height pixel ở định dạng :type.',
//    'custom' => [
//        'attribute-name' => [
//            'rule-name' => 'custom-message',
//        ],
//    ],

    'custom' => [
        'channel_type' => [
            'required'  => 'Trường này không được bỏ trống.',
            'numeric'   => 'Trường yêu cầu dạng số',
        ],
        'fb_page_id' => [
            'required'  => 'Trường này không được bỏ trống.',
        ],
        'fb_app_id' => [
            'required'  => 'Trường này không được bỏ trống.',
        ],
        'fb_app_secret' => [
            'required'  => 'Trường này không được bỏ trống.',
        ],
        'fb_page_access_token' => [
            'required'  => 'Trường này không được bỏ trống.',
        ],
        'custom_email' => [
            'required' => 'Vui lòng nhập địa chỉ email.',
        ],
        'custom_password' => [
            'required' => 'Vui lòng nhập mật khẩu.',
        ],
        'terms_of_use' => [
            'required' => 'Bạn phải chấp nhận các điều khoản thỏa thuận sử dụng.',
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
        'bot_name' => 'Tên Bot',
        'email' => 'Email',
        'name' => 'Tên',
        'variable_name' => 'Tên biến số',
        'group_name' => 'Tên nhóm',
        'company_name' => 'Tên công ty',
        'password' => 'Mật khẩu',
        'password_confirmation' => 'Xác nhận Mật khẩu',
        'scenario_name' => 'Tên kịch bản',
        'scenario' => 'Kịch bản',
        'title' => 'Tiêu đề',
        'type' => 'Kiểu',
        'url' => 'URL',
        'greeting_message' => 'Greeting Message',
        'upload_file' => 'Upload file',
        'time' => 'Thời gian bắt đầu',
        'notification_name' => 'Tên thông báo',
        'api_name' => 'Tên API',
	    'param.key.*' => 'Key',
	    'max_bot_number' => 'Số Bot tối đa',
	    'max_user_number' => 'Số người dùng tối đa',
        'white_list_domain' => 'White list domain',
        'file' => 'File',
	    'channel_id' => 'Channel ID',
        'app_id' => 'App ID',
	    'channel_secret' => 'Channel secret',
	    'channel_access_token' => 'Channel access token',
        'page_access_token' => 'Page access token',
        'timezone' => 'Timezone',
        'language' => 'Ngôn ngữ',
        'to'       => 'Tới',
        'content'  => 'Nội dung',
        'subject'  => 'Chủ đề',
        'transfer_from_bot_name' => 'Tên Bot nguồn',
        'transfer_to_bot' => 'Chuyển tới Bot',
        'api_type' => 'Kiểu API',
        'action'  => 'Thao tác',
        'email_name' => 'Tên email',
	    'template_name' => 'Tên Template',
        'domain' => 'Tên miền',
        'address' => 'Địa chỉ',
        'tel' => 'Điện thoại',
        'person_in_charge' => 'Người phụ trách',
        'business_segments' => 'Loại hình kinh doanh',
        'zip_code' => 'Mã bưu điện',
        'card_number'   => 'Số thẻ',
        'month_select' => 'Tháng',
        'year_select' => 'Năm',
        'cvc' => 'CVC',
        'terms_of_use' => 'Thỏa thuận sử dụng',
        'agree' => 'Đồng ý',
        'nlp_app_id' => 'App Id',
        'culture' => 'Culture',
        'from_name' => 'Tên người gửi',
        'from_email' => 'Từ',
        'option' => 'Option',
        'scenario_connect' => 'Scenario connect',
        'webhook_token' => 'Webhook token',
        'api_token' => 'Api token',
        'sheet_id' => ' Spreadsheet Id',
        'detail' => 'Chi tiết',
        'start_date' => 'Ngày bắt đầu',
        'yahoo_url' => 'Trang cảm ơn',
        'gateway_name' => 'Tên cổng thanh toán',
        'pgcard_shop_id' => 'Shop ID',
        'pgcard_shop_pass' => 'Shop pass',
        'pgcard_site_id' => 'Site ID',
        'pgcard_site_pass' => 'Site pass',
        'api_url' => 'API Url',
    ],

];
