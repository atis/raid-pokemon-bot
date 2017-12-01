<?php
$tz = TIMEZONE;

// Get latitude / longitude values from Telegram Mobile Client
if (isset($update['message']['location']['latitude'])) {
    $lat = $update['message']['location']['latitude'];
}
if (isset($update['message']['location']['longitude'])) {
    $lon = $update['message']['location']['longitude'];
}

// Get Userid and chatid from message or set below in case we get latitude and longitude by text message
if (isset($update['message']['from']['id'])) {
    $userid = $update['message']['from']['id'];
}
if (isset($update['message']['chat']['id'])) {
    $chatid = $update['message']['chat']['id'];
}
if (isset($update['message']['chat']['type'])) {
    $chattype = $update['message']['chat']['type'];
}

// Init count_data, gym and gym_id
$count_data = 0;
$gym = 0;
$gym_id = 0;

// Get latitude / longitude from message text if empty
// Necessary for Telegram Desktop Client as you cannot send a location :(
if (empty($lat) && empty($lon)) {
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

    // Get lat and lon from message text
    $coords = $data['arg'];

    // Create data array (max. 2)
    $count_data = substr_count($coords, ",");
    $data = explode(',', $coords, 2);

    // Latitude and longitude or Gym ID?
    if($data[0] == "ID") {
        $gym_id = $data[1];
        $gym = get_gym($gym_id);
    } else {
        // Set latitude / longitude
        $lat = $data[0];
        $lon = $data[1];

        // Debug
        debug_log('Lat=' . $lat);
        debug_log('Lon=' . $lon);
    }
}

// Init address and gym name
$fullAddress = "";
$gym_name = "";

// Address and gym name based on input
if($gym_id > 0) {
    // Get address from database
    $fullAddress = $gym['address'];
    $gym_name = $gym['gym_name'];
    debug_log('Gym ID: ' . $gym_id);
    debug_log('Gym Name: ' . $gym_name);
    debug_log('Gym Address: ' . $fullAddress);
} else {
    // Get the address.
    $addr = get_address($lat, $lon);

    // Get full address - Street #, ZIP District
    $fullAddress = "";
    $fullAddress .= (!empty($addr['street']) ? $addr['street'] : "");
    $fullAddress .= (!empty($addr['street_number']) ? " " . $addr['street_number'] : "");
    $fullAddress .= (!empty($fullAddress) ? ", " : "");
    $fullAddress .= (!empty($addr['postal_code']) ? $addr['postal_code'] . " " : "");
    $fullAddress .= (!empty($addr['district']) ? $addr['district'] : "");
}

// Address found.
if (!empty($fullAddress)) {
    // Create raid with address.
    $rs = my_query(
        "
        INSERT INTO   raids
        SET           user_id = {$userid},
			          lat = '{$lat}',
			          lon = '{$lon}',
			          first_seen = NOW(),
			          start_time = NOW(),
				  gym_name = '{$db->real_escape_string($gym_name)}',
			          timezone = '{$tz}',
			          address = '{$db->real_escape_string($fullAddress)}'
        "
    );

// No address found.
} else {
    // Create raid without address.
    $rs = my_query(
        "
        INSERT INTO   raids
        SET           user_id = {$userid},
			          lat = '{$lat}',
			          lon = '{$lon}',
			          first_seen = NOW(),
			          start_time = NOW(),
				  gym_name = '{$db->real_escape_string($gym_name)}',
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

// Build message.
$msg = 'Erstelle Raid in: <i>' . $fullAddress . '</i>';

// Answer callback or send message based on input prior raid creation
if($gym_id != 0) {
    // Edit the message.
    edit_message($update, $msg . CR . 'Bitte Raid Level auswählen:', $keys);

    // Build callback message string.
    $callback_response = 'Arena gespeichert.';

    // Answer callback.
    answerCallbackQuery($update['callback_query']['id'], $callback_response);
} else {
    // Private chat type.
    if ($chattype == 'private') {
        // Send the message.
        //send_message($update['message']['chat']['id'], $msg . CR . 'Bitte Raid level auswählen:', $keys);
        send_message($chatid, $msg . CR . 'Bitte Raid Level auswählen:', $keys);

    } else {
        //$reply_to = $update['message']['chat']['id'];
        $reply_to = $chatid;
        if ($update['message']['reply_to_message']['message_id']) {
            $reply_to = $update['message']['reply_to_message']['message_id'];
        }

        // Send the message.
        //send_message($update['message']['chat']['id'], $msg . CR . 'Bitte Raid level auswählen:', $keys, ['reply_to_message_id' => $reply_to, 'reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);
        send_message($chatid, $msg . CR . 'Bitte Raid Level auswählen:', $keys, ['reply_to_message_id' => $reply_to, 'reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);
    }

    exit();
}

