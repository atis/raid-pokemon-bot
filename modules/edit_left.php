<?php
// Check raid access.
raid_access_check($update, $data);

// Write to log.
debug_log('raid_edit_left()');
debug_log($update);
debug_log($data);

// Set the id.
$id = $data['id'];

// Build query.
my_query(
    "
    UPDATE    raids
    SET       end_time = DATE_ADD(start_time, INTERVAL {$data['arg']} MINUTE)
      WHERE   id = {$id}
    "
);

if ($update['message']['chat']['type'] == 'private' || $update['callback_query']['message']['chat']['type'] == 'private') {
    // Set the keys.
    $keys = [
        [
            [
                'text'                => 'Teilen',
                'switch_inline_query' => strval($id)
            ]
        ]
    ];

    // Get raid times.
    $rs = my_query(
        "
        SELECT    *, 
                              UNIX_TIMESTAMP(start_time)                      AS ts_start,
                              UNIX_TIMESTAMP(end_time)                        AS ts_end,
                              UNIX_TIMESTAMP(NOW())                           AS ts_now,
                              UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
                FROM      raids
                  WHERE   id = {$data['id']}
        "
    );

    // Fetch the row.
    $raid = $rs->fetch_assoc();

    // Build message string.
    $msg = '';
    $msg .= 'Raid gespeichert:' . CR;
    // Pokemon
    if(!empty($raid['pokemon'])) {
	$msg .= '<b>' . ucfirst($raid['pokemon']) . '</b>';
    }
    // End time
    if(!empty($raid['ts_end'])) {
	$msg .= '<b> bis ' . unix2tz($raid['ts_end'], $raid['timezone']) . '</b>' . CR;
    }
    // Gym Name
    if(!empty($raid['gym_name'])) {
	$msg .= $raid['gym_name'] . CR2 . CR;
	$msg .= 'Optional - Arena Team setzen:' . CR2;
    } else {
        $msg .= 'Optional - Arena Name und Arena Team:' . CR2;
        $msg .= '/gym <code>Name der Arena</code>' . CR;
    }
    $msg .= '/team <code>Mystic/Valor/Instinct/Blau/Rot/Gelb</code>';

    // Edit message.
    edit_message($update, $msg, $keys, false);

    // Build callback message string.
    $callback_response = 'Ablaufzeit gesetzt auf ' . $data['arg'] . ' Minuten';

    // Answer callback.
    answerCallbackQuery($update['callback_query']['id'], $callback_response);

} else {
    // Get raid times.
    $rs = my_query(
        "
        SELECT    *,
			      UNIX_TIMESTAMP(start_time)                      AS ts_start,
			      UNIX_TIMESTAMP(end_time)                        AS ts_end,
			      UNIX_TIMESTAMP(NOW())                           AS ts_now,
			      UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
		FROM      raids
		  WHERE   id = {$data['id']}
        "
    );

    // Fetch the row.
    $raid = $rs->fetch_assoc();

    // Get text and keys.
    $text = show_raid_poll($raid);
    $keys = keys_vote($raid);

    // Edit message.
    edit_message($update, $text, $keys, false);

    // Build callback message string.
    $callback_response = 'End time set to ' . $data['arg'] . ' minutes';

    // Answer callback.
    answerCallbackQuery($update['callback_query']['id'], $callback_response);
}

