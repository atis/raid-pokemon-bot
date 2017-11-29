<?php
// Write to log.
debug_log('raid_edit_start()');
debug_log($update);
debug_log($data);

// Check raid access.
raid_access_check($update, $data);

// Set the id.
$id = $data['id'];

if (true) {

    // Build query.
    my_query(
        "
        UPDATE    raids
        SET       start_time = DATE_ADD(start_time, INTERVAL {$data['arg']} MINUTE)
          WHERE   id = {$id}
        "
    );

    // Init empty keys array.
    $keys = array();

    for ($i = 120; $i >= 15; $i = $i - 5) {
        // Create the keys.
        $keys[] = array(
	    // Just show the time, no text - not everyone has a phone or tablet with a large screen...
            //'text'          => 'noch ' . floor($i / 60) . ':' . str_pad($i % 60, 2, '0', STR_PAD_LEFT),
            'text'          => floor($i / 60) . ':' . str_pad($i % 60, 2, '0', STR_PAD_LEFT),
            'callback_data' => $id . ':edit_left:' . $i
        );
    }

    // Get the inline key array.
    $keys = inline_key_array($keys, 4);

    // Write to log.
    debug_log($keys);

} else {
    // Edit pokemon.
    $keys = raid_edit_start_keys($id);
}

// Edit the message.
edit_message($update, 'Wie lange läuft der Raid?', $keys);

// Build callback message string.
$callback_response = 'Vorlaufzeit gesetzt auf ' . $data['arg'] . ' Minuten';

// Answer callback.
answerCallbackQuery($update['callback_query']['id'], $callback_response);
