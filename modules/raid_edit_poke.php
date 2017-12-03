<?php
// Write to log.
debug_log('raid_edit_poke()');
debug_log($update);
debug_log($data);

// Check raid access.
raid_access_check($update, $data);

// Set the id.
$raid_id = $data['id'];

// Init raid level and level found
$raid_level = 0;
$level_found = false;

// Get current pokemon
$old_pokemon = $data['arg'];

// Iterate thru the pokemon list to get raid level
$pokemonlist = $GLOBALS['pokemon'];
foreach($pokemonlist as $level => $levelmons) {
    debug_log("Searching raid boss '" . $old_pokemon . "' in level " . $level . " raids");
    // Compare pokemon by pokemon to get raid level
    foreach($levelmons as $key => $pokemon) {
        if(strtolower($old_pokemon) == strtolower($pokemon)) {
	    $level_found = true;
	    $raid_level = $level;
	    debug_log("Found raid boss '" . $pokemon . "' in level " . $level . " raids");
	    break 2;
	}
    }
}

if ($level_found) {
    // Get the keys.
    $keys = pokemon_keys($raid_id, $raid_level, $pokemonlist, "raid_set_poke");
} else {
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

if (isset($update['callback_query']['inline_message_id'])) {
    editMessageText($update['callback_query']['inline_message_id'], 'Raid Boss auswählen:', $keys);
} else {
    editMessageText($update['callback_query']['message']['message_id'], 'Raid Boss auswählen:', $keys, $update['callback_query']['message']['chat']['id'], $keys);
}

// Build callback message string.
$callback_response = 'Pokemon auswählen';

// Answer callback.
answerCallbackQuery($update['callback_query']['id'], $callback_response);
