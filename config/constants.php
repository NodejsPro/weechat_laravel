<?php

return [
    'per_page'  => [5, 10, 15, 20, 50],
    'authority' => [
        'super_admin' => '001',
        'admin_lv1' => '002',
        'admin_lv2' => '003',
        'client' => '004',
    ],
    'authority_lang' => [
        '001' => 'Supper admin',
        '002' => 'Admin lv1',
        '003' => 'Admin lv2',
        '004' => 'Client',
    ],
    'active'    => [
        'enable'                => 1,
        'disable'               => 0,
    ],
    'size_image' => [
      'width' => 150,
      'height' => 150
    ],
    'room_type' => [
      'one_one' => '001',
      'one_many' => '002',
    ],
    'log_message_limit' => 100,
];
