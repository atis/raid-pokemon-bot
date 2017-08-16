<?php

$rs = my_query('SELECT * FROM attendance WHERE raid_id=' . $data['id'] . ' AND user_id=' . $update['callback_query']['from']['id'] . '');
$answer = $rs->fetch_assoc();
debug_log($answer);

if (!$answer) {
    //$query = 'SELECT * FROM users WHERE user_id='.$update['callback_query']['from']['id'];
    //$rs = my_query($query);
    //$row = $rs->fetch_assoc();

    //my_query('INSERT INTO attendance SET raid_id='.$data['id'].', user_id='.$update['callback_query']['from']['id'].', arrived=1, team="'.$row['team'].'", attend_time=NOW()');
} else {
    my_query('UPDATE attendance SET arrived=1,raid_done=0,cancel=0 WHERE raid_id=' . $data['id'] . ' AND user_id=' . $update['callback_query']['from']['id'] . '');
}

send_response_vote($update, $data);
		
