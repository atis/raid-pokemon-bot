<?php
// Check raid access.
raid_access_check($update, $data);

// Write to log.
debug_log('raid_share()');
debug_log($update);
debug_log($data);

// Get raid id.
$id = $data['id'];

// Get chat id.
$chat = $data['arg'];

// Get raid.
$rs = my_query(
    "
    SELECT    *, 
                          UNIX_TIMESTAMP(start_time)                      AS ts_start,
                          UNIX_TIMESTAMP(end_time)                        AS ts_end,
                          UNIX_TIMESTAMP(NOW())                           AS ts_now,
                          UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
            FROM      raids
              WHERE   id = {$id}
    "
);

// Fetch the row.
$raid = $rs->fetch_assoc();

// Get text and keys.
$text = show_raid_poll($raid);
$keys = keys_vote($raid);

// Send the message.
send_message($chat, $text, $keys, ['reply_to_message_id' => $chat, 'disable_web_page_preview' => 'true']);

// Send location.
if (RAID_LOCATION == true) {
    // Send location.
    $loc = send_venue($chat, $raid['lat'], $raid['lon'], "", !empty($raid['address']) ? $raid['address'] . ', ID = ' . $raid['id'] : $raid['pokemon'] . ', ' . $raid['id']); // DO NOT REMOVE " ID = " --> NEEDED FOR CLEANUP PREPARATION!

    // Write to log.
    debug_log('location:');
    debug_log($loc);
}

// Set callback keys and message
$callback_msg = getTranslation('successfully_shared');

// Edit message.
edit_message($update, $callback_msg, $callback_keys, false);

// Answer callback.
answerCallbackQuery($update['callback_query']['id'], $callback_msg);

exit;
