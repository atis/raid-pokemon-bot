<?php

debug_log('raid_edit_left()');
debug_log($update);
debug_log($data);

raid_access_check($update, $data);


$id = $data['id'];

$q = 'UPDATE raids SET end_time=DATE_ADD(first_seen, INTERVAL ' . $data['arg'] . ' MINUTE) WHERE id=' . $id;
my_query($q);

if ($update['message']['chat']['type'] == 'private' || $update['callback_query']['message']['chat']['type'] == 'private') {
    $keys = [[[
        'text' => 'Share', 'switch_inline_query' => strval($id),
    ]]];

    $msg = 'Raid saved.' . CR . 'Optional - set gym name and team:' . CR2 . '/gym <code>gym name</code>' . CR . '/team <code>Mystic/Valor/Instinct/Blue/Red/Yellow</code>';
    edit_message($update, $msg, $keys, false);

    $callback_response = 'End time set to ' . $data['arg'] . ' minutes';
    answerCallbackQuery($update['callback_query']['id'], $callback_response);

} else {

    $rs = my_query('SELECT *,
			UNIX_TIMESTAMP(end_time) AS ts_end, 
			UNIX_TIMESTAMP(NOW()) as ts_now, 
			UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW()) AS t_left 
		FROM raids WHERE id=' . $data['id'] . '');
    $raid = $rs->fetch_assoc();

    $text = show_raid_poll($raid);
    $keys = keys_vote($raid);

    edit_message($update, $text, $keys, $false);

    $callback_response = 'End time set to ' . $data['arg'] . ' minutes';
    answerCallbackQuery($update['callback_query']['id'], $callback_response);
}

