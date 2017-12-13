<?php
// Write to log.
debug_log('MODS');

// Check access - user must be admin!
bot_access_check($update, BOT_ADMINS);

// Init empty keys array.
$keys = array();

// Create keys array.
$keys = [
    [
        [
            'text'          => 'Anzeigen',
            'callback_data' => '0:mods:list'
        ],
        [
            'text'          => 'Hinzufügen',
            'callback_data' => '0:mods:add'
        ],
        [
            'text'          => 'Löschen',
            'callback_data' => '0:mods:delete'
        ]
    ]
];

// Set message.
$msg = '<b>Moderatoren anzeigen, hinzufügen oder löschen:</b>';

// Send message.
send_message($update['message']['chat']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);

exit;
