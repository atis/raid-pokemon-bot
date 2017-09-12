<?php
/**
 * Mimic inline message to create raid poll from external notifier.
 *
 */
$tz = TIMEZONE;

// Get data from message text. (remove: "/raid ")
$gym_data = trim(substr($update['message']['text'], 5));

// Create data array (max. 8)
$data = explode(',', $gym_data, 8);

/**
 * Info:
 * [0] = Boss name
 * [1] = latitude
 * [2] = longitude
 * [3] = minutes
 * [4] = gym team
 * [5] = gym name
 * [6] = district
 * [7] = street
 */

// Invalid data received.
if (count($data) < 8) {
    send_message($update['message']['chat']['id'], 'Invalid input', []);
    exit;
}

// Get latitude / longitude from data.
$lat = floatval($data[1]);
$lon = floatval($data[2]);

// Format lat/long values.
$lat = substr($lat, 0, strpos('.', $lat) + 9);
$lon = substr($lon, 0, strpos('.', $lon) + 9);

// Escape comma in Raidname
$name = str_replace('|',',',$data[5]);

// Build address string.
$address = (!empty($data[6]) ? $data[6] : '') . (!empty($data[7]) ? ", " . $data[7] : "");

// Address found.
if (!empty($address)) {
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
		              gym_name = '{$db->real_escape_string($name)}',
		              timezone = '{$tz}',
		              address = '{$db->real_escape_string($address)}'
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

// Send location.
$loc = send_location($update['message']['chat']['id'], $raid['lat'], $raid['lon']);

// Write to log.
debug_log('location:');
debug_log($loc);

// Set text.
$text = show_raid_poll($raid);

// Private chat type.
if ($update['message']['chat']['type'] == 'private' || $update['callback_query']['message']['chat']['type'] == 'private') {
    // Set keys.
    $keys = [
        [
            [
                'text'                => 'Teilen',
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
