<?php

debug_log('raid_edit_poke()');
debug_log($update);
debug_log($data);

raid_access_check($update, $data);


$id = $data['id'];

if (true) {
    $q = 'UPDATE raids SET pokemon="' . $data['arg'] . '" WHERE id=' . $id;
    my_query($q);

    $keys = array();
    for ($i = 120; $i >= 25; $i = $i - 5) {
        $keys[] = array('text' => floor($i / 60) . ':' . str_pad($i % 60, 2, '0', STR_PAD_LEFT) . ' left', 'callback_data' => $id . ':edit_left:' . $i);
    }
    $keys = inline_key_array($keys, 4);
    debug_log($keys);

} else {
    /* Edit pokemon */
    $keys = raid_edit_start_keys();
}

if (!$keys) $keys = [[['text' => 'Not supported', 'callback_data' => 'edit:not_supported']]];

edit_message($update, 'How much time is left for Raid?', $keys);

$callback_response = 'Pokemon set to ' . $data['arg'];
answerCallbackQuery($update['callback_query']['id'], $callback_response);
