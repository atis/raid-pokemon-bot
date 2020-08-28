#!/usr/bin/php -q
<?php

//	require_once('../raidboss.php');
	require_once('../logic.php');
	
	foreach ($raidboss as $stars => $bosses) {
		$out = raidboss_keys('0:edit_poke:', $bosses, array('text'=>'<<','callback_data'=>'0:edit'));
		
		print_r($out);
	}