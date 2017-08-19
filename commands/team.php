<?php
// Get the team.
$gym_team = trim(strtolower(substr($update['message']['text'], 5)));

// Match team names.
$teams = array(
    'mystic'    => 'mystic',
    'instinct'  => 'instinct',
    'valor'     => 'valor',
    'rot'       => 'valor',
    'blau'      => 'mystic',
    'gelb'      => 'instinct',
    'r'         => 'valor',
    'b'         => 'mystic',
    'y'         => 'instinct'
);

// Valid team name.
if ($teams[$gym_team]) {
    // Update team in raids table.
    my_query(
        "
        UPDATE    raids
        SET       gym_team = '{$teams[$gym_team]}'
          WHERE   user_id = {$update['message']['from']['id']}
        ORDER BY  id DESC LIMIT 1
        "
    );

    // Send the message.
    sendMessage($update['message']['chat']['id'], 'Arena Team gesetzt auf: ' . ucfirst($teams[$gym_team]));

// Invalid team name.
} else {
    // Send the message.
    sendMessage($update['message']['chat']['id'], 'Ungültiger Team Name - schreibe: Mystic, Valor, Instinct oder Blau, Rot, Gelb');
}
