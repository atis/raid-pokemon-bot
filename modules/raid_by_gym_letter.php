<?php
// Get Userid, chatid and chattype from message 
if (isset($update['message']['from']['id'])) {
    $userid = $update['message']['from']['id'];
}
if (isset($update['message']['chat']['id'])) {
    $chatid = $update['message']['chat']['id'];
}
if (isset($update['message']['chat']['type'])) {
    $chattype = $update['message']['chat']['type'];
}

// Get the userid, chat id and type
$id_type = $data['id'];

// Create data array (max. 2)
$userdata = explode(',', $id_type, 2);

// Set userid, chat id and type
$userid = $userdata[0];
$chatid = $userid;
$chattype = $userdata[1];

// Debug
debug_log('User ID=' . $userid);
debug_log('Chat type=' . $chatid);
debug_log('Chat type=' . $chattype);

// Init id to 0
$id = 0;

// Get the keys.
$keys = raid_edit_gyms_first_letter_keys($chatid, $chattype);

// No keys found.
if (!$keys) {
    // Create the keys.
    $keys = [
        [
            [
                'text'          => 'Not supported',
                'callback_data' => 'edit:not_supported'
            ]
        ]
    ];
}

// Edit the message.
edit_message($update, 'Bitte Anfangsbuchstabe der Arena auswählen:', $keys);

// Build callback message string.
$callback_response = 'Anfangsbuchstabe ausgewählt.';

// Answer callback.
answerCallbackQuery($update['callback_query']['id'], $callback_response);

exit();
