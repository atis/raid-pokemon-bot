<?php
$tz = TIMEZONE;

// Get gym data.
$gym_data = trim(substr($update['message']['text'], 5));

$data = explode(',', $gym_data, 6);

// Invalid data.
if (count($data) < 6) {
    send_message($update['message']['chat']['id'], 'Invalid input', []);
    exit;
}

// Get latitude / longitude from data.
$lat = floatval($data[1]);
$lon = floatval($data[2]);

// Format lat/long values.
$lat = substr($lat, 0, strpos('.', $lat) + 9);
$lon = substr($lon, 0, strpos('.', $lon) + 9);

// Get the address.
$addr = get_address($lat, $lon);

// Address found.
if ($addr) {
    // Build the query.
    $rs = my_query(
        "
        INSERT INTO   raids
        SET           pokemon = '{$db->real_escape_string($data[0])}',
		              user_id = {$update['message']['from']['id']},
		              lat = '{$lat}',
		              lon = '{$lon}',
		              first_seen = NOW(),
		              end_time = DATE_ADD(first_seen, INTERVAL {$data[3]} MINUTE),
		              gym_team = '{$db->real_escape_string($data[4])}',
		              gym_name = '{$db->real_escape_string($data[5])}',
		              timezone = '{$tz}',
		              address = '{$db->real_escape_string($addr)}'
        "
    );

// No address found.
} else {
    // Build the query.
    $rs = my_query(
        "
        INSERT INTO   raids
        SET           pokemon = '{$db->real_escape_string($data[0])}',
		              user_id = {$update['message']['from']['id']},
		              lat = '{$lat}',
		              lon = '{$lon}',
		              first_seen = NOW(),
		              end_time = DATE_ADD(first_seen, INTERVAL {$data[3]} MINUTE),
		              gym_team = '{$db->real_escape_string($data[4])}',
		              gym_name = '{$db->real_escape_string($data[5])}',
		              timezone = '{$tz}'
        "
    );
}

// Get last insert id from db.
$id = my_insert_id();

// Write to log.
debug_log('ID=' . $id);

// Build query.
$rs = my_query(
    "
    SELECT    *,
		      UNIX_TIMESTAMP(end_time)                        AS ts_end,
		      UNIX_TIMESTAMP(NOW())                           AS ts_now,
		      UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
	FROM      raids
	  WHERE   id = {$id}
    "
);

// Get row.
$raid = $rs->fetch_assoc();

// Set text.
$text = show_raid_poll($raid);

// Private chat type.
if ($update['message']['chat']['type'] == 'private' || $update['callback_query']['message']['chat']['type'] == 'private') {
    // Set keys.
    $keys = [
        [
            [
                'text'                => 'Share',
                'switch_inline_query' => strval($id),
            ]
        ]
    ];

    // Send the message.
    send_message($update['message']['chat']['id'], $text, $keys);

} else {
    // Set reply to.
    $reply_to = $update['message']['chat']['id'];

    // Set keys.
    $keys = keys_vote($raid);

    if ($update['message']['reply_to_message']['message_id']) {
        $reply_to = $update['message']['reply_to_message']['message_id'];
    }

    // Send the message.
    send_message($update['message']['chat']['id'], $text, $keys, ['reply_to_message_id' => $reply_to, 'reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);
}

exit;
