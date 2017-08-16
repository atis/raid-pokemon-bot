<?php

$gym_team = trim(strtolower(substr($update['message']['text'], 5)));

$teams = array(
    'mystic' => 'mystic',
    'instinct' => 'instinct',
    'valor' => 'valor',
    'red' => 'valor',
    'blue' => 'mystic',
    'yellow' => 'instinct',
    'r' => 'valor',
    'b' => 'mystic',
    'y' => 'instinct',
);

if ($teams[$gym_team]) {
    $query = 'UPDATE raids SET gym_team="' . $teams[$gym_team] . '" WHERE user_id=' . $update['message']['from']['id'] . ' ORDER BY id DESC LIMIT 1';
    my_query($query);

    sendMessage($update['message']['chat']['id'], 'Gym team set to ' . ucfirst($teams[$gym_team]));
} else {
    sendMessage($update['message']['chat']['id'], 'Invalid team name - type Mystic, Valor, Instinct or Blue, Red, Yellow');
}
	
	