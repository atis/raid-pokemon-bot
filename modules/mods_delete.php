<?php
// Write to log.
debug_log('mods_del()');
debug_log($update);
debug_log($data);

// Set the id.
$user_id = $data['arg'];

if ($update['message']['chat']['type'] == 'private' || $update['callback_query']['message']['chat']['type'] == 'private') {
    // Update the user.
    my_query(
        "
        UPDATE	  users 
        SET       moderator = 0
                  WHERE   user_id = {$user_id}
        "
    );

    // Build message string.
    $msg = '';
    $msg .= '<b>Moderator entfernt!</b>' . CR;
    $msg .= get_user($user_id);

    // Create the keys.
    $keys = [];

    // Edit message.
    edit_message($update, $msg, $keys, false);

    // Build callback message string.
    $callback_response = 'Moderator entfernt!';

    // Answer callback.
    answerCallbackQuery($update['callback_query']['id'], $callback_response);

}
