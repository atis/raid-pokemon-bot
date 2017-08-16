<?php

$lat = $update['message']['location']['latitude'];
$lon = $update['message']['location']['longitude'];

$addr = get_address($lat, $lon);

$q = 'INSERT INTO raids SET
			user_id=' . $update['message']['from']['id'] . ',
			lat="' . $lat . '",
			lon="' . $lon . '",
			first_seen=NOW()
		';

$q .= ', timezone="' . TIMEZONE . '"';

if ($addr) {
    $q .= ', address="' . $db->real_escape_string($addr) . '"';
}

$rs = my_query($q);
$id = my_insert_id();
debug_log('ID=' . $id);

$keys = raid_edit_start_keys($id);

$msg = 'Create Raid at <i>' . $addr . '</i>';

if ($update['message']['chat']['type'] == 'private') {
    send_message($update['message']['chat']['id'], $msg . CR . 'Choose Raid level:', $keys);
} else {
    $reply_to = $update['message']['chat']['id'];
    if ($update['message']['reply_to_message']['message_id']) $reply_to = $update['message']['reply_to_message']['message_id'];

    send_message($update['message']['chat']['id'], $msg . CR . 'Choose Raid level:', $keys,
        ['reply_to_message_id' => $reply_to, 'reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]
    );
}
exit();
