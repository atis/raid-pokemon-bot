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

    // 1 Minute or 5 minute time slots
    if ($data['arg'] == 0) {
	$slotmax = 60;
	$slotsize = 1;
    } else {
	$slotmax = 120;
	$slotsize = 5;
    }

    for ($i = $slotmax; $i >= 15; $i = $i - $slotsize) {
        // Create the keys.
        $keys[] = array(
	    // Just show the time, no text - not everyone has a phone or tablet with a large screen...
            //'text'          => 'noch ' . floor($i / 60) . ':' . str_pad($i % 60, 2, '0', STR_PAD_LEFT),
            'text'          => floor($i / 60) . ':' . str_pad($i % 60, 2, '0', STR_PAD_LEFT),
            'callback_data' => $id . ':edit_left:' . $i
        );
    }

    // Get the inline key array.
    $keys = inline_key_array($keys, 5);

    // Write to log.
    debug_log($keys);

} else {
    // Edit pokemon.
    $keys = raid_edit_start_keys($id);
}

// Edit the message.
edit_message($update, getTranslation('how_long_raid'), $keys);

// Build callback message string.
$callback_response = getTranslation('lead_time_set_to') . $data['arg'] . getTranslation('minutes');

// Answer callback.
answerCallbackQuery($update['callback_query']['id'], $callback_response);
