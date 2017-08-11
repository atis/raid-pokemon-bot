<?php

	$rs = my_query('SELECT * FROM help WHERE id='.$update['message']['chat']['id']);
	$row = $rs->fetch_assoc();
	if (!$row) exit;

	foreach ($update['message']['new_chat_members'] as $v) {
		sendMessage('none',$v['id'],$row['message']);
	}

