<?php
return [
    'sms' => '',
    'host' => env('SMS_HOST', 'http://101.99.6.26:8080'),
    'key' => env('SMS_KEY', 'A0Zr98j/3yX+R~XHH!jmN]LWX/,?RT'),
    'request' => [
        'send_sms' => ':host/sms?content=:content&phone=:phone&key=:key'
    ],
];