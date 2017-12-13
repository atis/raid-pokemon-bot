<?php
// Write to log.
debug_log('mods_add()');
debug_log($update);
debug_log($data);

// Set the id.
$id = $data['arg'];

if ($update['message']['chat']['type'] == 'private' || $update['callback_query']['message']['chat']['type'] == 'private') {
    // Update the user.
    my_query(
        "
        UPDATE	  users 
        SET       moderator = 1
                  WHERE   id = {$id}
        "
    );

    // Build message string.
    $msg = '';
    $msg .= '<b>Moderator gespeichert!</b>' . CR . CR;
    $msg .= 'Infos zum neuen Moderator:' . CR;
    $msg .= get_user($id);

    // Create the keys.
    $keys = [];

    // Edit message.
    edit_message($update, $msg, $keys, false);

    // Build callback message string.
    $callback_response = 'Moderator gespeichert!';

    // Answer callback.
    answerCallbackQuery($update['callback_query']['id'], $callback_response);

}
