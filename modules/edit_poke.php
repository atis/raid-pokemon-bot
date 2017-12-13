<?php
// Write to log.
debug_log('edit_poke()');
debug_log($update);
debug_log($data);

// Check raid access.
raid_access_check($update, $data);

// Set the id.
$id = $data['id'];

// Get the argument.
$arg = $data['arg'];

if (true || $arg == "minutes" || $arg == "clocktime") {
    if ($arg != "minutes" || $arg != "clocktime") {
        // Update pokemon in the raid table.
        my_query(
            "
            UPDATE    raids
                SET       pokemon = '{$data['arg']}'
              WHERE   id = {$id}
            "
        );
    }

    // Init empty keys array.
    $keys = array();

    if ($arg == "minutes") {
	// Set switch view.
	$switch_text = "Uhrzeit-Ansicht";
	$switch_view = "clocktime";

        for ($i = 1; $i <= 60; $i = $i + 1) {
            // Create the keys.
            $keys[] = array(
                // Just show the time, no text - not everyone has a phone or tablet with a large screen...
                'text'          => floor($i / 60) . ':' . str_pad($i % 60, 2, '0', STR_PAD_LEFT),
                //'text'          => unix2tz($now_plus_i,$tz,"H:i"),
                'callback_data' => $id . ':edit_start:' . $i
            );
        }
    } else {
	// Set switch view.
	$switch_text = "Minuten-Ansicht";
	$switch_view = "minutes";

        // Timezone - maybe there's a more elegant solution as date_default_timezone_set?!
        $tz = TIMEZONE;
        date_default_timezone_set($tz);

        // Now 
        $now = time();

        for ($i = 1; $i <= 60; $i = $i + 1) {
	    $now_plus_i = $now + $i*60;
            // Create the keys.
            $keys[] = array(
	        // Just show the time, no text - not everyone has a phone or tablet with a large screen...
                //'text'          => floor($i / 60) . ':' . str_pad($i % 60, 2, '0', STR_PAD_LEFT),
	        'text'	        => unix2tz($now_plus_i,$tz,"H:i"),
                'callback_data' => $id . ':edit_start:' . $i
            );
        }
    }

    // Raid already running
    $keys[] = array(
        'text'	        => "Raid läuft schon!",
        'callback_data' => $id . ':edit_start:0' 
    );

    // Switch view: clocktime / minutes until start
    $keys[] = array(
        'text'	        => $switch_text,
        'callback_data' => $id . ':edit_poke:' . $switch_view
    );

    // Get the inline key array.
    $keys = inline_key_array($keys, 5);

    // Write to log.
    debug_log($keys);

} else {
    // Edit pokemon.
    $keys = raid_edit_start_keys($id);
}

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

// Edit the message.
if ($arg == "minutes") {
    edit_message($update, 'In wie viel Minuten <b>beginnt</b> der Raid?', $keys);
} else {
    edit_message($update, 'Wann <b>beginnt</b> der Raid?', $keys);
}

// Build callback message string.
if ($arg == "minutes" || $arg == "clocktime") {
    $callback_response = 'Ansicht geändert!';
} else {
    $callback_response = 'Pokemon gespeichert: ' . $data['arg'];
}

// Answer callback.
answerCallbackQuery($update['callback_query']['id'], $callback_response);
