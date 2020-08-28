<?php

	raid_access_check($update, $data);

	debug_log('raid_edit()');
	debug_log($update);

	$id = $data['id'];

	
	if (in_array($data['arg'], array('type_5','type_3','type_1','type_m'))) {
		require_once('raidboss.php');
		$type = str_replace('type_','',$data['arg']);
		$keys = raidboss_keys($id.':edit_poke:', $GLOBALS['raidboss'][$type], array('text'=>'<<','callback_data'=>$id.':edit_level'));
	} else {
		/* Edit pokemon */
		$keys = raid_edit_start_keys();
	}

	if (!$keys) $keys = [[[ 'text' => 'Not supported', 'callback_data' => 'edit:not_supported' ]]];

	if (isset($update['callback_query']['inline_message_id'])) {
		editMessageText($update['callback_query']['inline_message_id'],'Choose Raid Boss:',$keys);
	} else {
		editMessageText($update['callback_query']['message']['message_id'],'Choose Raid Boss',$keys,$update['callback_query']['message']['chat']['id'],$keys);
	}
	
	//edit_message_keyboard($update['callback_query']['message']['message_id'],$keys,$update['callback_query']['message']['chat']['id']);
	$callback_response = 'Ok';
	answerCallbackQuery($update['callback_query']['id'],$callback_response);

