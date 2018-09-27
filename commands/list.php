<?php
	
	debug_log('LIST');
	$q = 'SELECT timezone FROM raids WHERE id=(SELECT raid_id FROM attendance WHERE user_id='.$update['message']['from']['id'].' ORDER BY id DESC LIMIT 1)';
	debug_log($q);
	$rs = my_query($q);
	$row = $rs->fetch_assoc();
	if (!$row) {
		sendMessage('none',$update['message']['from']['id'],'Can\'t determine your location, please participate in at least 1 raid');
		exit;
	}

	$tz = $row['timezone'];

	$request = my_query('SELECT *,
		UNIX_TIMESTAMP(end_time) AS ts_end, 
		UNIX_TIMESTAMP(NOW()) as ts_now, 
		UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW()) AS t_left
	 FROM raids WHERE end_time>NOW() AND timezone="'.$tz.'" ORDER BY end_time ASC LIMIT 20');

	while($raid = $request->fetch_assoc()) {
		$keys = [[[
			'text' => 'Expand', 'callback_data' => $raid['id'].':vote_refresh:0',
		]]];
		/* Ignore Ex raids */
		//if ($raid['pokemon']=='mewtwo') continue;
		if ($raid['pokemon']=='deoxys') continue;
		$msg = show_raid_poll_small($raid);
		send_message('none',$update['message']['from']['id'],$msg, $keys, 
			['reply_markup' => ['selective'=>true, 'one_time_keyboard'=>true]]
		);
	}

	
	exit;
