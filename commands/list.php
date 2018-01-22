<?php
// Write to log.
debug_log('LIST');

// Check access.
bot_access_check($update, BOT_ACCESS);

// Get the userid and chattype
$userid = $update['message']['from']['id'];
$chattype = $update['message']['chat']['type'];

// Init empty keys array.
$keys = array();

// Create keys array.
$keys = [
    [
        [
            'text'          => getTranslation('list'),
            'callback_data' => $userid . ',' . $chattype . ':raids_list:0'
        ]
    ],
    [
        [
            'text'          => getTranslation('overview_share'),
            'callback_data' => '0:overview_share:0'
        ],
        [
            'text'          => getTranslation('overview_delete'),
            'callback_data' => '0:overview_delete:0'
        ]
    ]
];

// Set message.
$msg = '<b>' . getTranslation('raids_list_share_overview') . ':</b>';

// Send message.
send_message($update['message']['chat']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);

exit;
