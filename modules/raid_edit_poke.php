<?php
// Write to log.
debug_log('raid_edit_poke()');
debug_log($update);
debug_log($data);

// Check raid access.
raid_access_check($update, $data);

// Set the id.
$id = $data['id'];

    // Set keys.
    $keys = [
        [
            [
                'text'          => 'Arktos',
                'callback_data' => $id . ':raid_set_poke:arktos'
            ],
            [
                'text'          => 'Lugia',
                'callback_data' => $id . ':raid_set_poke:lugia'
            ],
            [
                'text'          => 'Lavados',
                'callback_data' => $id . ':raid_set_poke:lavados'
            ],
            [
                'text'          => 'Zapdos',
                'callback_data' => $id . ':raid_set_poke:zapdos'
            ]
        ],
        [
            [
                'text'          => 'Mewtu',
                'callback_data' => $id . ':raid_set_poke:mewtu'
            ],
            [
                'text'          => 'Mew',
                'callback_data' => $id . ':raid_set_poke:mew'
            ],
            [
                'text'          => 'Ho-Oh',
                'callback_data' => $id . ':raid_set_poke:hooh'
            ],
            [
                'text'          => 'Celebi',
                'callback_data' => $id . ':raid_set_poke:celebi'
            ]
        ],
        [
            [
                'text'          => 'Raikou',
                'callback_data' => $id . ':raid_set_poke:raikou'
            ],
            [
                'text'          => 'Entei',
                'callback_data' => $id . ':raid_set_poke:entei'
            ],
            [
                'text'          => 'Suicune',
                'callback_data' => $id . ':raid_set_poke:suicune'
            ]
        ]
    ];

if (isset($update['callback_query']['inline_message_id'])) {
    editMessageText($update['callback_query']['inline_message_id'], 'Raid Boss auswählen:', $keys);
} else {
    editMessageText($update['callback_query']['message']['message_id'], 'Raid Boss auswählen:', $keys, $update['callback_query']['message']['chat']['id'], $keys);
}

// Build callback message string.
$callback_response = 'Ok';

// Answer callback.
answerCallbackQuery($update['callback_query']['id'], $callback_response);
