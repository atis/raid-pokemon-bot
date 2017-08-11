<?php

	$query = 'SELECT COUNT(*) FROM users WHERE user_id='.$update['message']['from']['id'].' AND moderator=1';
	$rs = my_query($query);
	$row = $rs->fetch_row();
	if (!$row[0]) {
		$msg = 'Not allowed';
		sendMessage('none',$update['message']['from']['id'],$msg);
		exit;
	}


	if (!$update['message']['reply_to_message']['text']) {
		$msg = 'Please reply-to message that you want to set as help text';
		sendMessage('none',$update['message']['from']['id'],$msg);
		exit;
	}

	$help = $db->real_escape_string($update['message']['reply_to_message']['text']);

	my_query('INSERT INTO help SET id='.$update['message']['chat']['id'].', message="'.$help.'" ON DUPLICATE KEY update message="'.$help.'"');
	
	$msg = 'Help text set';
	sendMessage('none',$update['message']['chat']['id'],$msg);
	exit;
