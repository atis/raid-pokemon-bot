<?php

raid_access_check($update, $data);

debug_log('raid_edit()');
debug_log($update);

$id = $data['id'];

if ($data['arg'] == 'type_5') {
    $keys =
        [[[
            'text' => 'Articuno', 'callback_data' => $id . ':edit_poke:articuno',
        ], [
            'text' => 'Lugia', 'callback_data' => $id . ':edit_poke:lugia',
        ], [
            'text' => 'Moltres', 'callback_data' => $id . ':edit_poke:moltres',
        ], [
            'text' => 'Zapdos', 'callback_data' => $id . ':edit_poke:zapdos',
        ]]];

} else if ($data['arg'] == 'type_4') {
    $keys =
        [[[
            'text' => 'Tyranitar', 'callback_data' => $id . ':edit_poke:tyranitar',
        ]], [[
            'text' => 'Snorlax', 'callback_data' => $id . ':edit_poke:snorlax',
        ], [
            'text' => 'Lapras', 'callback_data' => $id . ':edit_poke:lapras',
        ], [
            'text' => 'Rhydon', 'callback_data' => $id . ':edit_poke:rhydon',
        ]], [[
            'text' => 'Charizard', 'callback_data' => $id . ':edit_poke:charizard',
        ], [
            'text' => 'Venusaur', 'callback_data' => $id . ':edit_poke:venusaur',
        ], [
            'text' => 'Blastoise', 'callback_data' => $id . ':edit_poke:blastoise',
        ]]];
} else if ($data['arg'] == 'type_3') {
    $keys =
        [[[
            'text' => 'Machamp', 'callback_data' => $id . ':edit_poke:machamp',
        ]], [[
            'text' => 'Vaporeon', 'callback_data' => $id . ':edit_poke:vaporeon',
        ], [
            'text' => 'Flareon', 'callback_data' => $id . ':edit_poke:flareon',
        ], [
            'text' => 'Jolteon', 'callback_data' => $id . ':edit_poke:jolteon',
        ]], [[
            'text' => 'Alakazam', 'callback_data' => $id . ':edit_poke:alakazam',
        ], [
            'text' => 'Arcanine', 'callback_data' => $id . ':edit_poke:arcanine',
        ], [
            'text' => 'Gengar', 'callback_data' => $id . ':edit_poke:gengar',
        ]]];
} else if ($data['arg'] == 'type_2') {
    $keys =
        [[[
            'text' => 'Muk', 'callback_data' => $id . ':edit_poke:muk',
        ]]];
} else if ($data['arg'] == 'type_1') {
    $keys = [[['text' => 'Not supported', 'callback_data' => 'edit:not_supported']]];
} else {
    /* Edit pokemon */
    $keys = raid_edit_start_keys();
}

if (!$keys) $keys = [[['text' => 'Not supported', 'callback_data' => 'edit:not_supported']]];

if (isset($update['callback_query']['inline_message_id'])) {
    editMessageText($update['callback_query']['inline_message_id'], 'Choose Raid Boss:', $keys);
} else {
    editMessageText($update['callback_query']['message']['message_id'], 'Choose Raid Boss', $keys, $update['callback_query']['message']['chat']['id'], $keys);
}

//edit_message_keyboard($update['callback_query']['message']['message_id'],$keys,$update['callback_query']['message']['chat']['id']);
$callback_response = 'Ok';
answerCallbackQuery($update['callback_query']['id'], $callback_response);

