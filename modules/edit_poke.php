<?php

	debug_log('raid_edit_poke()');
	debug_log($update);
	debug_log($data);

	raid_access_check($update, $data);


	$id = $data['id'];

	if (true) {
		$q = 'UPDATE raids SET pokemon="'.$data['arg'].'" WHERE id='.$id;
		my_query($q);

		$keys = array();
		$hatch_time = HATCH_TIME;
		$raid_time = RAID_TIME;
		$disallow_end = DISALLOW_END;
		$submit_intervals = SUBMIT_INTERVALS;
		
		for ($i=($hatch_time+$raid_time); $i>=$disallow_end; $i=$i-$submit_intervals) {
			$after_hatch = $i;
			$before_hatch = $i-$raid_time;
			if ($i>$raid_time) {
				$hours=floor($before_hatch/60);
				$minutes = str_pad($before_hatch%60,2,'0',STR_PAD_LEFT);

				//$keys[] = array('text' => '🥚+'.$hours.':'.$minutes, 'callback_data' => $id.':edit_left:'.$i);
				$keys[] = array('text' => EMOJI_EGG.'+'.$hours.':'.$minutes, 'callback_data' => $id.':edit_left:'.$i);
			} else {
				$hours=floor($after_hatch/60);
				$minutes = str_pad($after_hatch%60,2,'0',STR_PAD_LEFT);

				$keys[] = array('text' => $hours.':'.$minutes, 'callback_data' => $id.':edit_left:'.$i);
			}
		}
		

		$keys[] = array('text' => '<<', 'callback_data' => $id.':edit_level');
		$keys = inline_key_array($keys,4);
		debug_log($keys);

	} else {
		/* Edit pokemon */
		$keys = raid_edit_start_keys();
	}

	if (!$keys) $keys = [[[ 'text' => 'Not supported', 'callback_data' => 'edit:not_supported' ]]];
	
	edit_message($update, 'How much time is left for Raid?', $keys);

	$callback_response = 'Pokemon set to '.$data['arg'];
	answerCallbackQuery($update['callback_query']['id'],$callback_response);
