<?php
// Write to log.
debug_log('raid_edit_poke()');
debug_log($update);
debug_log($data);

// Check raid access.
raid_access_check($update, $data);

// Set the id.
$id = $data['id'];

if (true) {
    // Update pokemon in the raid table.
    my_query(
        "
        UPDATE    raids
        SET       pokemon = '{$data['arg']}'
          WHERE   id = {$id}
        "
    );

    // Init empty keys array.
    $keys = array();

    for ($i = 60; $i >= 0; $i = $i - 1) {
        // Create the keys.
        $keys[] = array(
	    // Just show the time, no text - not everyone has a phone or tablet with a large screen...
            //'text'          => 'noch ' . floor($i / 60) . ':' . str_pad($i % 60, 2, '0', STR_PAD_LEFT),
            'text'          => floor($i / 60) . ':' . str_pad($i % 60, 2, '0', STR_PAD_LEFT),
            'callback_data' => $id . ':edit_start:' . $i
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
edit_message($update, 'Wann beginnt der Raid?' . CR . 'Raid läuft schon? --- Einfach 0:00 auswählen!', $keys);

// Build callback message string.
$callback_response = 'Pokemon gespeichert: ' . $data['arg'];

// Answer callback.
answerCallbackQuery($update['callback_query']['id'], $callback_response);
