/**
 * Created by nguyen.khac.tung on 7/4/2017.
 */

var line_carousel_template ={
    "message":{
        "type":"template",
        "altText":"Product 1",
        "template":
        {
            "type":"carousel",
                "columns":[
                {
                    "text":"title product 1",
                    "thumbnailImageUrl":"http://embot.local.vn/uploads/593e58dd3763e7612191788a/241b5e7373aa9a17e20d.jpg",
                    "title":"Product 1",
                    "actions":[
                        {
                            "type":"uri",
                            "label":"Contact",
                            "uri":"https://hachidori.io/abstracts/19742"
                        },
                        {
                            "type":"postback",
                            "label":"Buy",
                            "data":"SCENARIO_5955b3569a892034620eebf8"
                        }
                    ]
                },
                {
                    "text":"title product 2",
                    "thumbnailImageUrl":"http://embot.local.vn/uploads/593e58dd3763e7612191788a/2dcdc2ad85f671edfbdf.jpg",
                    "title":"Product 2",
                    "actions":[
                        {
                            "type":"uri",
                            "label":"Contact",
                            "uri":"https://www.coinbase.com/charts"
                        },
                        {
                            "type":"postback",
                            "label":"Buy",
                            "data":"SCENARIO_5955b3569a892034620eebf8"
                        }
                    ]
                }
            ]
        }
    }
};
var fb_carousel_template = {
    "message":{
        "attachment":{
            "type":"template",
            "payload":{
                "template_type":"generic",
                "elements":[
                    {
                        "title":"Product 1",
                        "subtitle":"title product 1",
                        "item_url":"https://devdocs.line.me/en/?php#imagemap-message",
                        "image_url":"http://embot.local.vn/uploads/593e58dd3763e7612191788a/241b5e7373aa9a17e20d.jpg",
                        "buttons":[
                            {
                                "title":"Contact",
                                "type":"web_url",
                                "url":"https://hachidori.io/abstracts/19742"
                            },
                            {
                                "title":"Buy",
                                "type":"postback",
                                "payload":"SCENARIO_5940bcdc9a8920674a634de4"
                            }
                        ]
                    },
                    {
                        "title":"Product 2",
                        "subtitle":"title product 2",
                        "item_url":"https://btc-e.com/exchange/eth_usd",
                        "image_url":"http://embot.local.vn/uploads/593e58dd3763e7612191788a/2dcdc2ad85f671edfbdf.jpg",
                        "buttons":[
                            {
                                "title":"Contact",
                                "type":"web_url",
                                "url":"https://www.coinbase.com/charts"
                            },
                            {
                                "title":"Buy",
                                "type":"postback",
                                "payload":"SCENARIO_594cd77f9a892051d5208390"
                            }
                        ]
                    }
                ]
            }
        }
    }
};
facebookToLine(fb_carousel_template);

function facebookToLine(message) {
    var result = {
        "message" : {
            "type": 'template',
            "altText": '',
            "template": {
                "type": 'carousel',
                "columns": []
            }
        }
    };
    if (message['message'] != void 0
        && message['message']['attachment'] != void 0
        && message['message']['attachment']['payload'] != void 0
        && message['message']['attachment']['payload']['elements'] != void 0
        && message['message']['attachment']['payload']['elements'].length
    ) {
        var elements = message['message']['attachment']['payload']['elements'];
        $(elements).each(function (index, elm) {
            //set altText
            if(result['message']['altText'] == '') {
                result['message']['altText'] = getString(elm['title']);
            }

            //carousel item
            var carousel_item = {
                "text" : getString(elm['title']),
                "thumbnailImageUrl" : getString(elm['image_url']),
                "title" : getString(elm['subtitle']),
                "actions" : []
            };
            //button
            if(elm['buttons'] != void 0 && elm['buttons'].length) {
                $(elm['buttons']).each(function (i, e) {
                    var button_item = {
                        'label' : getString(e['title'])
                    };
                    if(e['type'] == 'web_url') {
                        button_item['type'] = 'uri';
                        button_item['uri'] = getString(e['url']);

                    } else if(e['type'] == 'postback') {
                        button_item['type'] = 'postback';
                        button_item['data'] = getString(e['payload']);
                    }
                    carousel_item['actions'].push(button_item);
                });
            }
            result['message']['template']['columns'].push(carousel_item);
        });
    }
    console.log(JSON.stringify(result['message']));
    return JSON.stringify(result['message']);
}

function getString(string) {
    return (string != void 0 && string != '') ? string : '';
}
