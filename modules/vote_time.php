<?php

		$rs = my_query('SELECT * FROM attendance WHERE raid_id='.$data['id'].' AND user_id='.$update['callback_query']['from']['id'].'');
		$answer = $rs->fetch_assoc();
		debug_log($answer);

		if (!$answer) {
			$query = 'SELECT * FROM users WHERE user_id='.$update['callback_query']['from']['id'];
			$rs = my_query($query);
			$row = $rs->fetch_assoc();
		
			my_query('INSERT INTO attendance SET raid_id='.$data['id'].', user_id='.$update['callback_query']['from']['id'].', attend_time=FROM_UNIXTIME('.$data['arg'].'), team="'.$row['team'].'"');
		} else {
			my_query('UPDATE attendance SET attend_time=FROM_UNIXTIME('.$data['arg'].'), cancel=0, arrived=0,raid_done=0 WHERE raid_id='.$data['id'].' AND user_id='.$update['callback_query']['from']['id'].'');
		}
		
		send_response_vote($update, $data);
		
