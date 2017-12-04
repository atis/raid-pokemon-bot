<?php
// Check raid access.
raid_access_check($update, $data);

// Write to log.
debug_log('edit()');
debug_log($update);

// Set the id.
$raid_id = $data['id'];

// Set the raid level.
$raid_level = $data['arg'];

// Set the pokkemon list
$pokemonlist = $GLOBALS['pokemon'];

// Get the keys.
$keys = pokemon_keys($raid_id, $raid_level, $pokemonlist, "edit_poke");

// No keys found.
if (!$keys) {
    $keys = [
        [
            [
                'text'          => 'Nicht unterstützt',
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
$callback_response = 'Ok';

// Answer callback.
answerCallbackQuery($update['callback_query']['id'], $callback_response);
