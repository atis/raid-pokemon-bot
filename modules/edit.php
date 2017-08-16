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
                'text'          => 'Articuno',
                'callback_data' => $id . ':edit_poke:articuno'
            ],
            [
                'text'          => 'Lugia',
                'callback_data' => $id . ':edit_poke:lugia'
            ],
            [
                'text'          => 'Moltres',
                'callback_data' => $id . ':edit_poke:moltres'
            ],
            [
                'text'          => 'Zapdos',
                'callback_data' => $id . ':edit_poke:zapdos'
            ]
        ]
    ];

// Level 4 boss.
} else if ($data['arg'] == 'type_4') {
    // Set keys.
    $keys = [
        [
            [
                'text'          => 'Tyranitar',
                'callback_data' => $id . ':edit_poke:tyranitar'
            ]
        ],
        [
            [
                'text'          => 'Snorlax',
                'callback_data' => $id . ':edit_poke:snorlax'
            ],
            [
                'text'          => 'Lapras',
                'callback_data' => $id . ':edit_poke:lapras'
            ],
            [
                'text'          => 'Rhydon',
                'callback_data' => $id . ':edit_poke:rhydon'
            ]
        ],
        [
            [
                'text'          => 'Charizard',
                'callback_data' => $id . ':edit_poke:charizard'
            ],
            [
                'text'          => 'Venusaur',
                'callback_data' => $id . ':edit_poke:venusaur'
            ],
            [
                'text'          => 'Blastoise',
                'callback_data' => $id . ':edit_poke:blastoise'
            ]
        ]
    ];

// Level 3 boss.
} else if ($data['arg'] == 'type_3') {
    // Set keys.
    $keys = [
        [
            [
                'text'          => 'Machamp',
                'callback_data' => $id . ':edit_poke:machamp'
            ]
        ],
        [
            [
                'text'          => 'Vaporeon',
                'callback_data' => $id . ':edit_poke:vaporeon'
            ],
            [
                'text'          => 'Flareon',
                'callback_data' => $id . ':edit_poke:flareon'
            ],
            [
                'text'          => 'Jolteon',
                'callback_data' => $id . ':edit_poke:jolteon'
            ]
        ],
        [
            [
                'text'          => 'Alakazam',
                'callback_data' => $id . ':edit_poke:alakazam'
            ],
            [
                'text'          => 'Arcanine',
                'callback_data' => $id . ':edit_poke:arcanine'
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
                'text'          => 'Muk',
                'callback_data' => $id . ':edit_poke:muk'
            ]
        ]
    ];

// Level 1 boss.
} else if ($data['arg'] == 'type_1') {
    // Set keys.
    $keys = [
        [
            [
                'text' => 'Not supported',
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
                'text'          => 'Not supported',
                'callback_data' => 'edit:not_supported'
            ]
        ]
    ];
}

if (isset($update['callback_query']['inline_message_id'])) {
    editMessageText($update['callback_query']['inline_message_id'], 'Choose Raid Boss:', $keys);
} else {
    editMessageText($update['callback_query']['message']['message_id'], 'Choose Raid Boss', $keys, $update['callback_query']['message']['chat']['id'], $keys);
}

$callback_response = 'Ok';
answerCallbackQuery($update['callback_query']['id'], $callback_response);
