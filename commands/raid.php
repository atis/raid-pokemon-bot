<?php
/**
 * Mimic inline message to create raid poll from external notifier.
 *
 */
$tz = TIMEZONE;

// Get data from message text. (remove: "/raid ")
$gym_data = trim(substr($update['message']['text'], 5));

// Create data array (max. 9)
$data = explode(',', $gym_data, 9);

/**
 * Info:
 * [0] = Boss name
 * [1] = latitude
 * [2] = longitude
 * [3] = raid duration in minutes
 * [4] = gym team
 * [5] = gym name
 * [6] = district (or street)
 * [7] = street (or district)
 * [8] = optional: raid countdown minutes
 */

// Invalid data received.
if (count($data) < 8) {
    send_message($update['message']['chat']['id'], 'Invalid input', []);
    exit;
}

// Raid boss name
$boss = $data[0];

// Get latitude / longitude from data.
$lat = floatval($data[1]);
$lon = floatval($data[2]);

// Format lat/long values.
$lat = substr($lat, 0, strpos('.', $lat) + 9);
$lon = substr($lon, 0, strpos('.', $lon) + 9);

// Endtime from input / config
if ((!empty($data[3])) && ($data[3] > 0) && ($data[3] < RAID_DURATION)) {
    $endtime = $data[3];
} else {
    $endtime = RAID_DURATION;
}

// Team
$team = $data[4];

// Escape comma in Raidname
$name = str_replace('|',',',$data[5]);

// Build address string.
if(!empty(GOOGLE_API_KEY)){
    $addr = get_address($lat, $lon);

    // Get full address - Street #, ZIP District
    $address = "";
    $address .= (!empty($addr['street']) ? $addr['street'] : "");
    $address .= (!empty($addr['street_number']) ? " " . $addr['street_number'] : "");
    $address .= ", ";
    $address .= (!empty($addr['postal_code']) ? $addr['postal_code'] . " " : "");
    $address .= (!empty($addr['district']) ? $addr['district'] : "");
} else {
    //Based on input order of [6] and [7] it'll be either: Street, District or District, Street
    $address = (!empty($data[6]) ? $data[6] : '') . (!empty($data[7]) ? ", " . $data[7] : "");
}

// Get countdown minutes when specified, otherwise 0 minutes until raid starts
$countdown = 0;
if (!empty($data[8])) {
    $countdown = $data[8];
}

// Insert new raid or update existing raid?
$raid_id = raid_duplication_check($name,($endtime + $countdown));

if ($raid_id != 0){
    // Update pokemon and team in raids table.
    my_query(
        "
        UPDATE    raids
        SET       pokemon = '{$db->real_escape_string($boss)}',
		  gym_team = '{$db->real_escape_string($team)}'
          WHERE   id = {$raid_id}
        "
    );

    // Debug log
    debug_log('Updated raid ID: ' . $raid_id);

    // Build query.
    $rs = my_query(
        "
        SELECT    *,
                          UNIX_TIMESTAMP(end_time)                        AS ts_end,
                          UNIX_TIMESTAMP(start_time)                      AS ts_start,
                          UNIX_TIMESTAMP(NOW())                           AS ts_now,
                          UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
            FROM      raids
              WHERE   id = {$raid_id}
        "
    );

    // Get row.
    $raid = $rs->fetch_assoc();

    //Debug
    // Set text.
    //$text = '<b>Raid aktualisiert!  ID = ' . $raid_id . "</b>" . CR;
    //$text .= CR . show_raid_poll($raid);

    // Send the message
    //sendMessage($update['message']['chat']['id'], $text);

    // Exit now after update of raid and message.
    exit;
}

// Address found.
if (!empty($address)) {
    // Build the query.
    $rs = my_query(
        "
        INSERT INTO   raids
        SET           pokemon = '{$db->real_escape_string($boss)}',
		              user_id = {$update['message']['from']['id']},
		              lat = '{$lat}',
		              lon = '{$lon}',
		              first_seen = NOW(),
		              start_time = DATE_ADD(first_seen, INTERVAL {$countdown} MINUTE),
		              end_time = DATE_ADD(start_time, INTERVAL {$endtime} MINUTE),
		              gym_team = '{$db->real_escape_string($team)}',
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
        SET           pokemon = '{$db->real_escape_string($boss)}',
		              user_id = {$update['message']['from']['id']},
		              lat = '{$lat}',
		              lon = '{$lon}',
		              first_seen = NOW(),
		              start_time = DATE_ADD(first_seen, INTERVAL {$countdown} MINUTE),
		              end_time = DATE_ADD(start_time, INTERVAL {$endtime} MINUTE),
		              gym_team = '{$db->real_escape_string($team)}',
		              gym_name = '{$db->real_escape_string($name)}',
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
		      UNIX_TIMESTAMP(start_time)                      AS ts_start,
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
