<?php
// Check raid access.
raid_access_check($update, $data);

// Write to log.
debug_log('raid_edit()');
debug_log($update);

// Set the id.
$id = $data['id'];

// Level 5 boss.
if ($data['arg'] == 'type_5') {
    // Set keys.
    $keys = [
        [
            [
                'text'          => 'Arktos',
                'callback_data' => $id . ':edit_poke:arktos'
            ],
            [
                'text'          => 'Lugia',
                'callback_data' => $id . ':edit_poke:lugia'
            ],
            [
                'text'          => 'Lavados',
                'callback_data' => $id . ':edit_poke:lavados'
            ],
            [
                'text'          => 'Zapdos',
                'callback_data' => $id . ':edit_poke:zapdos'
            ]
        ],
        [
            [
                'text'          => 'Mewtu',
                'callback_data' => $id . ':edit_poke:mewtu'
            ],
            [
                'text'          => 'Mew',
                'callback_data' => $id . ':edit_poke:mew'
            ],
            [
                'text'          => 'Ho-Oh',
                'callback_data' => $id . ':edit_poke:hooh'
            ],
            [
                'text'          => 'Celebi',
                'callback_data' => $id . ':edit_poke:celebi'
            ]
        ],
        [
            [
                'text'          => 'Raikou',
                'callback_data' => $id . ':edit_poke:raikou'
            ],
            [
                'text'          => 'Entei',
                'callback_data' => $id . ':edit_poke:entei'
            ],
            [
                'text'          => 'Suicune',
                'callback_data' => $id . ':edit_poke:suicune'
            ]
        ]
    ];

// Level 4 boss.
} else if ($data['arg'] == 'type_4') {
    // Set keys.
    $keys = [
        [
            [
                'text'          => 'Despotar',
                'callback_data' => $id . ':edit_poke:despotar'
            ]
        ],
        [
            [
                'text'          => 'Relaxo',
                'callback_data' => $id . ':edit_poke:relaxo'
            ],
            [
                'text'          => 'Lapras',
                'callback_data' => $id . ':edit_poke:lapras'
            ],
            [
                'text'          => 'Rizeros',
                'callback_data' => $id . ':edit_poke:rizeros'
            ]
        ],
        [
            [
                'text'          => 'Glurak',
                'callback_data' => $id . ':edit_poke:glurak'
            ],
            [
                'text'          => 'Bisasflor',
                'callback_data' => $id . ':edit_poke:bisasflor'
            ],
            [
                'text'          => 'Turtok',
                'callback_data' => $id . ':edit_poke:turtok'
            ]
        ]
    ];

// Level 3 boss.
} else if ($data['arg'] == 'type_3') {
    // Set keys.
    $keys = [
        [
            [
                'text'          => 'Machomei',
                'callback_data' => $id . ':edit_poke:machomei'
            ]
        ],
        [
            [
                'text'          => 'Aquana',
                'callback_data' => $id . ':edit_poke:aquana'
            ],
            [
                'text'          => 'Flamara',
                'callback_data' => $id . ':edit_poke:flamara'
            ],
            [
                'text'          => 'Blitza',
                'callback_data' => $id . ':edit_poke:blitza'
            ]
        ],
        [
            [
                'text'          => 'Simsala',
                'callback_data' => $id . ':edit_poke:simsala'
            ],
            [
                'text'          => 'Arkani',
                'callback_data' => $id . ':edit_poke:arkani'
            ],
            [
                'text'          => 'Gengar',
                'callback_data' => $id . ':edit_poke:gengar'
            ]
        ]
    ];

// Level 2 boss.
} else if ($data['arg'] == 'type_2') {
    // Set keys.
    $keys = [
        [
            [
                'text'          => 'Sleimok',
                'callback_data' => $id . ':edit_poke:sleimok'
            ]
        ]
    ];

// Level 1 boss.
} else if ($data['arg'] == 'type_1') {
    // Set keys.
    $keys = [
        [
            [
                'text' => 'Nicht unterstützt',
                'callback_data' => 'edit:not_supported'
            ]
        ]
    ];


} else {
    // Edit pokemon.
    $keys = raid_edit_start_keys($id);
}

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
} else {
    $level = substr($data['arg'], -1);
    $keys[] = [
        array(
            'text'          => EMOJI_EGG . ' Raid-Ei',
            'callback_data' => $id . ':edit_poke:Level ' . $level . ' Ei'
        )
    ];
}

if (isset($update['callback_query']['inline_message_id'])) {
    editMessageText($update['callback_query']['inline_message_id'], 'Raid Boss auswählen:', $keys);
} else {
    editMessageText($update['callback_query']['message']['message_id'], 'Raid Boss auswählen', $keys, $update['callback_query']['message']['chat']['id'], $keys);
}

// Build callback message string.
$callback_response = 'Ok';

// Answer callback.
answerCallbackQuery($update['callback_query']['id'], $callback_response);
