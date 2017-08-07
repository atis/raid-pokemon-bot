<?php 

	error_reporting(E_ALL ^ E_NOTICE);
	
	
	$start = microtime(true);
	require_once('config.php');
	require_once('debug.php');
	require_once('constants.php');
	require_once('functions.php');
	require_once('logic.php');
	require_once('geo_api.php');

	$apikey = $_GET['apikey'];

	if (hash('sha512',$apikey) == CONFIG_HASH) {
		define('API_KEY',$apikey);
		$botsplit = explode(':',$apikey);
		define('BOT_ID',$botsplit[0]);
		define('BOT_KEY',$botsplit[1]);
	} else {
		sendMessageEcho('none',MAINTAINER_ID,$_SERVER['REMOTE_ADDR'].' '.$_SERVER['HTTP_X_FORWARDED_FOR'].' '.$apikey);
		exit('Nop');
	}

	$content = file_get_contents('php://input');

	$update = json_decode($content, true);
	if (!$update) { 
		debug_log($content, '!');
	} else { 
		debug_log($update,'<');
	}

	$command = NULL;

	$db = new mysqli('localhost',BOT_ID,BOT_KEY,BOT_ID);
	if ($db->connect_errno) {
		debug_log("Failed to connect to Database!".$db->connect_error(), '!');
		sendMessage('none',$update['message']['chat']['id'],"Failed to connect to Database!\nPlease contact ".MAINTAINER." and forward this message...\n");
	}

	update_user($update);
	if (isset($update['callback_query'])) {
		if ($update['callback_query']['data']) {
			$d = explode(':', $update['callback_query']['data']);
			$data['id'] = $d[0];
			$data['action'] = $d[1];
			$data['arg'] = $d[2];
		}
		debug_log('DATA=');
		debug_log($data);

		$module = 'modules/'.basename($data['action']).'.php';
		debug_log($module);
		if (file_exists($module)) {
			include_once($module);
			exit;
		} else {
			debug_log('No action');
		}


	} else if (isset($update['inline_query'])){
		/* INLINE - LIST POLLS */
		raid_list($update);
		exit;
	} else if (isset($update['message']['location'])) { 
		include_once('modules/raid_create.php');
		exit();
		
	} else if (isset($update['message'])) {
		if (substr($update['message']['text'],0,1) == '/') {
			$command = strtolower(str_replace('/','',str_replace(BOT_NAME,'',explode(' ',$update['message']['text'])[0])));
			$module = 'commands/'.basename($command).'.php';
			debug_log($module);

			if (file_exists($module)) {
				include_once($module);
				exit;
			}

			sendMessage('none',$update['message']['chat']['id'],'<b>Please send location to start Raid announce</b> ');
		}
	}

