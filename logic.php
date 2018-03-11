<?php 

function raid_access_check($update, $data) {
	$rs = my_query('SELECT * FROM raids WHERE id='.$data['id'].'');
	$raid = $rs->fetch_assoc();
	if ($update['callback_query']['from']['id']!=$raid['user_id']) {
		$query = 'SELECT COUNT(*) FROM users WHERE user_id='.$update['callback_query']['from']['id'].' AND moderator=1';
		$rs = my_query($query);
		$row = $rs->fetch_row();
		if ($row['0']) return true;

		$callback_response = 'You are not allowed to edit this raid';
		answerCallbackQuery($update['callback_query']['id'],$callback_response);
		exit;
	}
}


function inline_key_array($buttons, $columns) {
	$result = array();
	$col = 0;
	$row = 0;
	foreach ($buttons as $v) {
		$result[$row][$col] = $v;
		$col++;
		if ($col>=$columns) {
			$row++;
			$col=0;
		}
	}
	return $result;
}

function raid_edit_start_keys($id) {
		$keys = 
		[[[
				'text' => 'Legendary Raid *****','callback_data' => $id.':edit:type_5',
		]],[[
				'text' => '4 Star Raid ****', 'callback_data' => $id.':edit:type_4',
			],[
				'text' => '3 Star Raid ***', 'callback_data' => $id.':edit:type_3',
		]],[[
				'text' => '2 Star Raid **', 'callback_data' => $id.':edit:type_2',
			],[
				'text' => '1 Star Raid *', 'callback_data' => $id.':edit:type_1',
		]]];
		return $keys;
}

function keys_raid_people($data) {
		if (!is_array($data)) $data=array('id'=>$data);

			$keys = [[
				'text' => '+1', 'callback_data' => $data['id'].':vote:1',
					],[
				'text' => '+2', 'callback_data' => $data['id'].':vote:2'
					],[
				'text' => '+3', 'callback_data' => $data['id'].':vote:3'
					],[
				'text' => '+4', 'callback_data' => $data['id'].':vote:4'
					],[
				'text' => '+5', 'callback_data' => $data['id'].':vote:5'
			]];
			return $keys;
}

function keys_vote($raid) {
		$keys_team = [];
		$keys_time = [];

		$end_time = $raid['ts_end'];
		$now = $raid['ts_now'];

		$keys = [[[
			'text' => '+1', 'callback_data' => $raid['id'].':vote:1',
				],[
			'text' => '+2', 'callback_data' => $raid['id'].':vote:2'
				],[
			'text' => '+3', 'callback_data' => $raid['id'].':vote:3'
				],[
			'text' => '+4', 'callback_data' => $raid['id'].':vote:4'
				],[
			'text' => '+5', 'callback_data' => $raid['id'].':vote:5'
		]],[[
			'text' => TEAM_B.' Mystic', 'callback_data' => $raid['id'].':vote_team:mystic',
				],[
			'text' => TEAM_R.' Valor', 'callback_data' => $raid['id'].':vote_team:valor',
				],[
			'text' => TEAM_Y.' Instinct', 'callback_data' => $raid['id'].':vote_team:instinct',
		]]];

		if ($end_time<$now) {
			$keys[] = [array('text'=>'Raid Finished','callback_data'=>$raid['id'].':vote_time:'.(ceil(time()/300)*300))];
		} else {
			$col = 1;
			for ($i=ceil($now/300)*300; $i<=($end_time-300); $i=$i+300) {
				if ($col++>=5) {
					$keys[] = $keys_time;
					$keys_time = [];
					$col = 1;
				}
				$keys_time[] = array('text' => unix2tz($i,$raid['timezone']), 'callback_data' => $raid['id'].':vote_time:'.$i);
			}
			$keys[] = $keys_time;
		}


		$keys[] = [
			['text' => EMOJI_REFRESH, 'callback_data' => $raid['id'].':vote_refresh:0'],
			['text' => 'Arrived', 'callback_data' => $raid['id'].':vote_arrived:0'],
			['text' => 'Done', 'callback_data' => $raid['id'].':vote_done:0'],
			['text' => 'Won\'t come', 'callback_data' => $raid['id'].':vote_cancel:0'],
		];
		if ($end_time<$now) {
			$keys = [[['text'=>'Raid Finished','callback_data'=>$raid['id'].':vote_refresh:0']]];
		}
	return $keys;
}


function update_user($update) {
		global $db;
		
		$name = '';
		$sep = '';

		if ($update['message']) {
			$msg = $update['message']['from'];
		}

		if ($update['callback_query']) {
			$msg = $update['callback_query']['from'];
		}

		if ($update['inline_query']) {
			$msg = $update['inline_query']['from'];
		}

		$id = $msg['id'];
		if (!$id) {
			debug_log('No id','!');
			debug_log($update,'!');
			return false;
		}


		if ($msg['first_name']) {
			$name = $msg['first_name'];
			$sep = ' ';
		}
		if ($msg['last_name']) $name .= $sep.$msg['last_name'];

		
		$request = my_query('INSERT INTO users SET 
			user_id='.$id.', 
			nick="'.$db->real_escape_string($msg['username']).'", 
			name="'.$db->real_escape_string($name).'"
		ON DUPLICATE KEY UPDATE
			nick="'.$db->real_escape_string($msg['username']).'", 
			name="'.$db->real_escape_string($name).'"
		');
		return $request;
}

function send_response_vote($update, $data, $new=false) {
		$rs = my_query('SELECT *, 
			UNIX_TIMESTAMP(end_time) AS ts_end, 
			GREATEST(UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(first_seen)) as ts_now, 
			UNIX_TIMESTAMP(end_time)-GREATEST(UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(first_seen)) AS t_left 
		FROM raids WHERE id='.$data['id'].'');
		$raid = $rs->fetch_assoc();

		$msg = show_raid_poll($raid);
		$keys = keys_vote($raid);

		if ($new) {
			$loc = send_location('none',$update['callback_query']['message']['chat']['id'],$raid['lat'], $raid['lon']);
			debug_log('location:');
			debug_log($loc);
			$msg = send_message('none',$update['callback_query']['message']['chat']['id'],$msg."\n", $keys, ['reply_to_message_id'=>$loc['result']['message_id']]);
			answerCallbackQuery($update['callback_query']['id'],$msg);
		} else {
			edit_message($update, $msg, $keys);
			$msg = 'Raid attendance updated';
			answerCallbackQuery($update['callback_query']['id'],$msg);
		}
		exit;
}


function unix2tz($unix, $tz, $format = 'H:i') {
	if (!$unix) return false;
	if (!$tz) return false;
	$dt = new DateTime('@'.$unix);
	$dt->setTimeZone(new DateTimeZone($tz));
	return $dt->format($format);
}

function show_raid_poll($raid) {
	$time_left = floor($raid['t_left']/60);
	$time_left = floor($time_left/60).':'.str_pad($time_left%60,2,'0',STR_PAD_LEFT).' left';
	$hatch_time = $raid['ts_end']-RAID_TIME;
	if ($hatch_time>time()) {
		$hatch_time = ' (hatch '.unix2tz($hatch_time,$raid['timezone']).')';
	} else {
		$hatch_time = ' (hatched)';
	}

	$msg = '';
	if ($raid['gym_name'] || $raid['gym_team']) {
		$msg .= 'Gym: <b>'.$raid['gym_name'].'</b>';
		if ($raid['gym_team']) $msg .= ' '.$GLOBALS['teams'][$raid['gym_team']].' '.ucfirst($raid['gym_team']);
		$msg .= CR;
	}
	if ($raid['address']) {
		$addr = explode(',',$raid['address'],4);
		array_pop($addr);
		$addr = implode(',',$addr);
		$msg .= '<i>'.$addr.'</i>'.CR2;
	}
	$msg .= '#Raid <b>'.ucfirst($raid['pokemon']).'</b>'.CR2;
	//$msg .= CR;
	if ($time_left<0) {
		$msg .= 'Raid Finished'.CR2;
	} else {
		//$msg .= '<i>'.$time_left.'</i> until '.substr($raid['end_time'],11,5)."\n\n";
		$msg .= '<i>'.$time_left.'</i> until '.unix2tz($raid['ts_end'],$raid['timezone']).$hatch_time."\n\n";
	}
	$msg .= 'Location: https://maps.google.com/?q='.$raid['lat'].','.$raid['lon'].CR;
	
	$query = 'SELECT *, UNIX_TIMESTAMP(attend_time) AS ts_att  FROM attendance WHERE raid_id='.intval($raid['id']).' ORDER BY cancel ASC, raid_done DESC, team ASC, arrived DESC, attend_time ASC';
	$rs = my_query($query);
	$data = array();
	
	while ($row = $rs->fetch_assoc()) {
		if ($row['cancel']) $row['team']='cancel';
		if ($row['raid_done']) $row['team']='done';
		if (!$row['team']) $row['team']='unknown';
		$data[$row['team']][] = $row;
		if ($row['extra_people']) {
			for ($i=1; $i<=$row['extra_people']; $i++) {
				$data[$row['team']][] = false;
			}
		}
	}

	debug_log($data);
	
	if (count($data)==0) {
		$msg .= CR.'No participants yet.'.CR;
	
	}
	
	foreach ($GLOBALS['teams'] as $k=>$v) {
		if (!count($data[$k])) continue;
		$msg .= CR.$v.' <b>'.ucfirst($k).': '.count($data[$k]).'</b>'."\n";
		foreach ($data[$k] as $vv) {
			if ($vv===false) continue;
			if ($vv['raid_done']) continue;
			$query = 'SELECT * FROM users WHERE user_id='.$vv['user_id'];
			$rs = my_query($query);
			$row = $rs->fetch_assoc();
			$name = '@'.$row['nick'];
			if ($name=='@') $name = $row['name'];
			if ($name=='') $name = $vv['user_id'];
			$msg .= ' - '.$name.' ';
			if ($vv['arrived']) {
				$msg .= '[arrived '.unix2tz($vv['ts_att'],$raid['timezone']).'] ';
			} else if ($vv['cancel']) {
				$msg .= '[cancel] ';
			} else {
//				$msg .= '['.substr($vv['attend_time'],11,5).'] ';
				$msg .= '['.unix2tz($vv['ts_att'],$raid['timezone']).'] ';
			}
			if ($vv['extra_people']) $msg .= '+'.$vv['extra_people'];
			
			$msg .= CR;
		}
	}
	if (count($data['done'])) {
		$msg .= CR.' <b>Done: '.count($data['done']).'</b>'.CR;
	}

	$msg .= CR.'<i>Updated: '.unix2tz(time(), $raid['timezone'], 'H:i:s').'</i> ID = '.$raid['id'];

	return $msg;
}

function show_raid_poll_small($raid) {
	$time_left = floor($raid['t_left']/60);
	$time_left = floor($time_left/60).':'.str_pad($time_left%60,2,'0',STR_PAD_LEFT).' left';
	$hatch_time = $raid['ts_end']-RAID_TIME;
	if ($hatch_time>time()) {
		$hatch_time = ' (hatch '.unix2tz($hatch_time,$raid['timezone']).')';
		$time_left .= $hatch_time;
	}

	$msg = '<b>'.ucfirst($raid['pokemon']).'</b> '.$time_left.' <b>'.$raid['gym_name'].'</b>'.CR;
	if ($raid['address']) {
		$addr = explode(',',$raid['address'],4);
		array_pop($addr);
		$addr = implode(',',$addr);
		$msg .= '<i>'.$addr.'</i>'.CR2;
	}
	
	$query = 'SELECT team, COUNT(*) AS cnt, SUM(extra_people) AS extra FROM attendance WHERE raid_id='.$raid['id'].' AND (cancel=0 OR cancel IS NULL) AND (raid_done=0 OR raid_done IS NULL) GROUP BY team';
	$rs = my_query($query);
	$data = array();
	
	$total = 0;
	$sep = '';
	while ($row = $rs->fetch_assoc()) {
		$sum = $row['cnt']+$row['extra'];
		if ($sum==0) continue;
		$msg .= $sep.$GLOBALS['teams'][$row['team']].' '.$sum;
		$sep = ' | ';
		$total += $sum;
	}
	if (!$total) {
		$msg .= ' No participants'.CR;
	} else {
		$msg .= ' = <b>'.$total.'</b>'.CR;
	}

	return $msg;
}

function raid_list($update) {
	/* INLINE - LIST POLLS */

	if ($update['inline_query']['query']) {
		/* By ID */
		$request = my_query('SELECT *,
			UNIX_TIMESTAMP(end_time) AS ts_end, 
			GREATEST(UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(first_seen)) as ts_now, 
			UNIX_TIMESTAMP(end_time)-GREATEST(UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(first_seen)) AS t_left 
		 FROM raids WHERE id='.intval($update['inline_query']['query']));
		$rows = array();
		while($answer = $request->fetch_assoc()) {
			$rows[] = $answer;
		}
		debug_log($rows);
		answer_inline_query($update['inline_query']['id'], $rows);
	} else {
		/* By user */
		$request = my_query('SELECT *,
			UNIX_TIMESTAMP(end_time) AS ts_end, 
			GREATEST(UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(first_seen)) as ts_now, 
			UNIX_TIMESTAMP(end_time)-GREATEST(UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(first_seen)) AS t_left 
		FROM raids WHERE user_id = '.$update['inline_query']['from']['id'].' ORDER BY id DESC LIMIT 3;');
		$rows = array();
		while($answer = $request->fetch_assoc()) {
			$rows[] = $answer;
		}
	
		debug_log($rows);
		answer_inline_query($update['inline_query']['id'], $rows);
	}
}
