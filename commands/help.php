<?php

	$rs = my_query('SELECT * FROM help WHERE id=253227190');
	$row = $rs->fetch_assoc();
	if (!$row) exit;

	sendMessage('none',$update['message']['from']['id'],$row['message']);
	