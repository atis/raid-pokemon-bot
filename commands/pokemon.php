<?php
// Write to log.
debug_log('POKEMON');

// Build query.
$rs = my_query(
    "
    SELECT    timezone
    FROM      raids
      WHERE   id = (
                  SELECT    raid_id
                  FROM      attendance
                    WHERE   user_id = {$update['message']['from']['id']}
                  ORDER BY  id DESC LIMIT 1
              )
    "
);

// Get row.
$row = $rs->fetch_assoc();

// No data found.
if (!$row) {
    sendMessage($update['message']['from']['id'], 'Can\'t determine your location, please participate in at least 1 raid');
    exit;
}

// Build query.
$request = my_query(
    "
    SELECT    *,
              UNIX_TIMESTAMP(start_time)                      AS ts_start,
              UNIX_TIMESTAMP(end_time)                        AS ts_end,
              UNIX_TIMESTAMP(NOW())                           AS ts_now,
              UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
    FROM      raids
      WHERE   end_time>NOW()
        AND   (user_id = {$update['message']['from']['id']}
	 	  OR user_id IN (
			SELECT user_id 
			FROM users 
			WHERE moderator = 1
		  )
	      )
        AND   timezone='{$row['timezone']}'
    ORDER BY  end_time ASC LIMIT 20
    "
);

while ($raid = $request->fetch_assoc()) {
    // Create keys array.
    $keys = [
        [
            [
                'text'          => getTranslation('refresh_pokemon'),
                'callback_data' => $raid['id'] . ':raid_edit_poke:' . $raid['pokemon'],
            ]
        ]
    ];

    // Get message.
    $msg = show_raid_poll_small($raid);

    // Send message.
    send_message($update['message']['from']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);
}

exit;
