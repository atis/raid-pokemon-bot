<?php
// Write to log.
debug_log('START');

// Get the userid and chattype
$userid = $update['message']['from']['id'];
$chattype = $update['message']['chat']['type'];

// Create keys array.
$keys = [
	    [
	        [
	            'text'          => getTranslation('create_a_raid'),
		    'callback_data' => $userid . ',' . $chattype . ':raid_by_gym_letter:0',
	        ]
	    ]
	];

// Set message.
$msg = '<b>' . getTranslation('send_location') . '</b>' . CR2 . CR . '<b>' . getTranslation('raid_by_gym') . '</b>';

// Send message.
send_message($update['message']['chat']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);

exit;
