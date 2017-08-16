<?php

$gym_data = trim(substr($update['message']['text'], 5));

$data = explode(',', $gym_data, 6);

if (count($data) < 6) {
    send_message($update['message']['chat']['id'], 'Invalid input', []);
    exit;
}
$query = 'UPDATE raids SET gym_name="' . $db->real_escape_string($gym_name) . '" WHERE user_id=' . $update['message']['from']['id'] . ' ORDER BY id DESC LIMIT 1';
$lat = floatval($data[1]);
$lon = floatval($data[2]);

$lat = substr($lat, 0, strpos('.', $lat) + 9);
$lon = substr($lon, 0, strpos('.', $lon) + 9);

$addr = get_address($lat, $lon);

$q = 'INSERT INTO raids SET
		pokemon="' . $db->real_escape_string($data[0]) . '",
		user_id=' . $update['message']['from']['id'] . ',
		lat="' . $lat . '",
		lon="' . $lon . '",
		first_seen=NOW(),
		end_time=DATE_ADD(first_seen, INTERVAL ' . $data[3] . ' MINUTE),
		gym_team="' . $db->real_escape_string($data[4]) . '",
		gym_name="' . $db->real_escape_string($data[5]) . '"
	';

$q .= ', timezone="' . TIMEZONE . '"';

if ($addr) {
    $q .= ', address="' . $db->real_escape_string($addr) . '"';
}

$rs = my_query($q);
$id = my_insert_id();
debug_log('ID=' . $id);

$rs = my_query('SELECT *,
		UNIX_TIMESTAMP(end_time) AS ts_end, 
		UNIX_TIMESTAMP(NOW()) as ts_now, 
		UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW()) AS t_left 
	FROM raids WHERE id=' . $id . '');
$raid = $rs->fetch_assoc();

$text = show_raid_poll($raid);
$keys = keys_vote($raid);


if ($update['message']['chat']['type'] == 'private' || $update['callback_query']['message']['chat']['type'] == 'private') {

    $keys = [[[
        'text' => 'Share', 'switch_inline_query' => strval($id),
    ]]];

    send_message($update['message']['chat']['id'], $text, $keys);

} else {
    $reply_to = $update['message']['chat']['id'];
    if ($update['message']['reply_to_message']['message_id']) $reply_to = $update['message']['reply_to_message']['message_id'];

    send_message($update['message']['chat']['id'], $text, $keys,
        ['reply_to_message_id' => $reply_to, 'reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]
    );
}

exit;
