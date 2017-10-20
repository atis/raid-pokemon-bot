<?php
// Write to log.
debug_log('NEW');

// Get the userid and chattype
$userid = $update['message']['from']['id'];
$chattype = $update['message']['chat']['type'];

// Get lat and lon from message text. (remove: "/new ")
$coords = trim(substr($update['message']['text'], 4));

// #TODO
// Add check to validate latitude and longitude
// If lat and lon = valid
//     button to create raid
// else
//     no button and error message

// Create keys array.
$keys = [
	    [
	        [
	            'text'          => 'Raid anlegen',
	            'callback_data' => $userid . ',' . $chattype . ':raid_create:' . $coords,
	        ]
	    ]
	];

$msg = "Koordination erfolgreich übermittelt!";

// Send message.
send_message($update['message']['from']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);

exit;
