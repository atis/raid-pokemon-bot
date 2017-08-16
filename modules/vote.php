<?php

$rs = my_query('SELECT * FROM attendance WHERE raid_id=' . $data['id'] . ' AND user_id=' . $update['callback_query']['from']['id'] . '');
$answer = $rs->fetch_assoc();
debug_log($answer);

$rs = my_query('SELECT * FROM users WHERE user_id=' . $update['callback_query']['from']['id'] . '');
$row = $rs->fetch_assoc();

$qq = 'extra_people=' . intval($data['arg'] - 1);
if ($row['team']) $qq .= ', team="' . $row['team'] . '"';
debug_log($row);
debug_log($qq);

if (!$answer) {
    my_query('INSERT INTO attendance SET raid_id=' . $data['id'] . ', user_id=' . $update['callback_query']['from']['id'] . ', ' . $qq);
} else {
    my_query('UPDATE attendance SET ' . $qq . ' WHERE raid_id=' . $data['id'] . ' AND user_id=' . $update['callback_query']['from']['id'] . '');
}

send_response_vote($update, $data);
		

