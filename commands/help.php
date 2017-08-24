<?php

	$rs = my_query('SELECT * FROM help WHERE id=-1001135811922');
	$row = $rs->fetch_assoc();
	if (!$row) exit;

	sendMessage('none',$update['message']['from']['id'],$row['message']);
	