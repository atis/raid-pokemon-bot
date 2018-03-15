<?php

	$gym_data = trim(substr($update['message']['text'],5));

	$data = explode(',',$gym_data,5);

	if (count($data)<5) {
		send_message('none',$update['message']['chat']['id'],'Invalid input',[]);
		exit;
	}

	$lat = floatval($data[1]);
	$lon = floatval($data[2]);

	$lat = substr($lat,0,strpos('.',$lat)+9);
	$lon = substr($lon,0,strpos('.',$lon)+9);
		
	$tz = get_timezone($lat, $lon);
	$addr = get_address($lat, $lon);
	
	if (!$tz) {
		send_message('none',$update['message']['chat']['id'],'Invalid input',[]);
		exit;
	}


	if (preg_match('|([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2})(?:/([0-9]*))?|',$data[3],$match)) {
		/* Start time */
		$dt = new DateTime($match[1], new DateTimeZone($tz));
		$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
		$first_seen = '"'.$dt->format('Y-m-d H:i:s').'"';

		$duration = $match[2];
		if (!$duration) $duration = 45;
		$end_time = 'DATE_ADD('.$first_seen.', INTERVAL '.intval($duration).' MINUTE)';
	} else if (preg_match('|([0-9]{2}:[0-9]{2})|',$data[3],$match)) {
		$dt = new DateTime($match[0]);
		$dt->setTimeZone(new DateTimeZone($tz));
		$first_seen = 'NOW()';
		$end_time = '"'.$dt->format('Y-m-d H:i:s').'"';
	} else if (intval($data[3])>0) {
		$first_seen = 'NOW()';
		$end_time = 'DATE_ADD(first_seen, INTERVAL '.intval($data[3]).' MINUTE)';
	} else {
		send_message('none',$update['message']['chat']['id'],'Time format must be either minutes left, or YYYY-MM-DD hh:mm',[]);
		exit;

	}

	$q = 'INSERT INTO raids SET 
		pokemon="'.$db->real_escape_string($data[0]).'",
		user_id='.$update['message']['from']['id'].', 
		lat="'.$lat.'", 
		lon="'.$lon.'",
		first_seen='.$first_seen.',
		end_time='.$end_time.',
		gym_name="'.$db->real_escape_string($data[4]).'"
	';

	if ($tz!==false) {
		$q .= ', timezone="'.$tz.'"';
	}
	if ($addr) {
		$q .= ', address="'.$db->real_escape_string($addr).'"';
	}

	$rs = my_query($q);
	$id = my_insert_id();
	debug_log('ID='.$id);

	$rs = my_query('SELECT *, 
		UNIX_TIMESTAMP(end_time) AS ts_end, 
		UNIX_TIMESTAMP(NOW()) as ts_now, 
		UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW()) AS t_left 
	FROM raids WHERE id='.$id.'');
	$raid = $rs->fetch_assoc();

	$text = show_raid_poll($raid);
	$keys = keys_vote($raid);



	if ($update['message']['chat']['type']=='private' || $update['callback_query']['message']['chat']['type']=='private') {

		$keys = [[[
			'text'=>'Share','switch_inline_query'=>strval($id),
		]]];
	
		send_message('none',$update['message']['chat']['id'],$text, $keys);

	} else {
		$reply_to = $update['message']['chat']['id'];
		if ($update['message']['reply_to_message']['message_id']) $reply_to = $update['message']['reply_to_message']['message_id'];

		send_message('none',$update['message']['chat']['id'],$text, $keys, 
			['reply_to_message_id'=>$reply_to, 'reply_markup' => ['selective'=>true, 'one_time_keyboard'=>true]]
		);
	}
	
	exit;
