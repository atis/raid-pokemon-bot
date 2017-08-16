<?php
$rs = my_query('SELECT * FROM attendance WHERE raid_id=' . $data['id'] . ' AND user_id=' . $update['callback_query']['from']['id'] . '');
$answer = $rs->fetch_assoc();
debug_log($answer);

if (!$answer) {
    //my_query('INSERT INTO attendance SET raid_id='.$data['id'].', user_id='.$update['callback_query']['from']['id'].', cancel=1, attend_time=NULL');
} else {
    my_query('UPDATE attendance SET cancel=1, raid_done=0, arrived=0 WHERE raid_id=' . $data['id'] . ' AND user_id=' . $update['callback_query']['from']['id'] . '');
}

send_response_vote($update, $data);
		

