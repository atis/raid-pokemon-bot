<?php

		$rs = my_query('SELECT * FROM attendance WHERE raid_id='.$data['id'].' AND user_id='.$update['callback_query']['from']['id'].'');
		$answer = $rs->fetch_assoc();
		debug_log($answer);
		
		if (!$answer) {
			my_query('INSERT INTO attendance SET raid_id='.$data['id'].', user_id='.$update['callback_query']['from']['id'].', team="'.$data['arg'].'"');
		} else {
			my_query('UPDATE attendance SET team="'.$data['arg'].'" WHERE raid_id='.$data['id'].' AND user_id='.$update['callback_query']['from']['id'].'');
		}
		
		my_query('UPDATE users SET team="'.$data['arg'].'" WHERE user_id='.$update['callback_query']['from']['id'].'');
		
		send_response_vote($update, $data);
		
