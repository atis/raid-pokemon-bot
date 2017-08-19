<?php
$tz = TIMEZONE;

// Get latitude / longitude values.
$lat = $update['message']['location']['latitude'];
$lon = $update['message']['location']['longitude'];

// Get the address.
$addr = get_address($lat, $lon);

// Address found.
if (!empty($addr)) {
    // Create raid with address.
    $rs = my_query(
        "
        INSERT INTO   raids
        SET           user_id = {$update['message']['from']['id']},
			          lat = '{$lat}',
			          lon = '{$lon}',
			          first_seen = NOW(),
			          timezone = '{$tz}',
			          address = '{$db->real_escape_string($addr)}'
        "
    );

// No address found.
} else {
    // Create raid without address.
    $rs = my_query(
        "
        INSERT INTO   raids
        SET           user_id = {$update['message']['from']['id']},
			          lat = '{$lat}',
			          lon = '{$lon}',
			          first_seen = NOW(),
			          timezone = '{$tz}'
        "
    );
}

// Get last insert id from db.
$id = my_insert_id();

// Write to log.
debug_log('ID=' . $id);

// Get the keys.
$keys = raid_edit_start_keys($id);

// Build message.
$msg = 'Erstelle Raid in: <i>' . $addr . '</i>';

// Private chat type.
if ($update['message']['chat']['type'] == 'private') {
    // Send the message.
    send_message($update['message']['chat']['id'], $msg . CR . 'Bitte Raid level auswählen:', $keys);

} else {
    $reply_to = $update['message']['chat']['id'];
    if ($update['message']['reply_to_message']['message_id']) {
        $reply_to = $update['message']['reply_to_message']['message_id'];
    }

    // Send the message.
    send_message($update['message']['chat']['id'], $msg . CR . 'Bitte Raid level auswählen:', $keys, ['reply_to_message_id' => $reply_to, 'reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);
}

exit();
