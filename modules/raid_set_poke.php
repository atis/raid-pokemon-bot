<?php
// Write to log.
debug_log('raid_set_poke()');
debug_log($update);
debug_log($data);

// Check raid access.
raid_access_check($update, $data);

// Set the id.
$id = $data['id'];

// Update pokemon in the raid table.
my_query(
    "
    UPDATE    raids
    SET       pokemon = '{$data['arg']}'
      WHERE   id = {$id}
    "
);

if ($update['message']['chat']['type'] == 'private' || $update['callback_query']['message']['chat']['type'] == 'private') {
    // Get raid times.
    $rs = my_query(
        "
        SELECT    *, 
                              UNIX_TIMESTAMP(start_time)                      AS ts_start,
                              UNIX_TIMESTAMP(end_time)                        AS ts_end,
                              UNIX_TIMESTAMP(NOW())                           AS ts_now,
                              UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
                FROM      raids
                  WHERE   id = {$data['id']}
        "
    );

    // Fetch the row.
    $raid = $rs->fetch_assoc();

    // Create the keys.
    $keys = [];

    // Build message string.
    $msg = '';
    $msg .= 'Raid Boss gespeichert:' . CR;
    // Pokemon
    if(!empty($raid['pokemon'])) {
        $msg .= '<b>' . ucfirst($raid['pokemon']) . '</b>';
    }
    // End time
    if(!empty($raid['ts_end'])) {
        $msg .= '<b> bis ' . unix2tz($raid['ts_end'], $raid['timezone']) . '</b>' . CR;
    }
    // Gym Name
    if(!empty($raid['gym_name'])) {
        $msg .= $raid['gym_name'] . CR;
    } 

    // Edit message.
    edit_message($update, $msg, $keys, false);

    // Build callback message string.
    $callback_response = 'OK';

    // Answer callback.
    answerCallbackQuery($update['callback_query']['id'], $callback_response);

}
