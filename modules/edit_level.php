<?php
		$lat = $update['message']['location']['latitude'];
		$lon = $update['message']['location']['longitude'];

		if (!$id) {
			raid_access_check($update, $data);
			$id = $data['id'];
		}
		
		if (!$addr) $addr = get_address($lat, $lon);

		debug_log('ID='.$id);

		$keys = raid_edit_start_keys($id);

		$msg = 'Edit Raid at <i>'.$addr.'</i>';

		edit_message($update, 'How much time is left for Raid?', $keys);
		exit();
