<?php
// Write to log.
debug_log('mods_list()');
debug_log($update);
debug_log($data);

// Set the id.
$id = $data['arg'];

if ($update['message']['chat']['type'] == 'private' || $update['callback_query']['message']['chat']['type'] == 'private') {
    // Build message string.
    $msg = '';
    $msg .= 'Infos zum Moderator:' . CR;

    // Add name.
    $msg .= get_user($id);

    // Create the keys.
    $keys = [];

    // Edit message.
    edit_message($update, $msg, $keys, false);

    // Build callback message string.
    $callback_response = 'OK';

    // Answer callback.
    answerCallbackQuery($update['callback_query']['id'], $callback_response);

}
