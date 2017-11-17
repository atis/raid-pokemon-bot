<?php
/**
 * Raid access check.
 * @param $update
 * @param $data
 * @return bool
 */
function raid_access_check($update, $data)
{
    // Build query.
    $rs = my_query(
        "
        SELECT    *
        FROM      raids
          WHERE   id = {$data['id']}
        "
    );

    $raid = $rs->fetch_assoc();

    if ($update['callback_query']['from']['id'] != $raid['user_id']) {
        // Build query.
        $rs = my_query(
            "
            SELECT    COUNT(*)
            FROM      users
              WHERE   user_id = {$update['callback_query']['from']['id']}
                AND   moderator = 1
            "
        );

        $row = $rs->fetch_row();

        if (empty($row['0'])) {
            $callback_response = 'You are not allowed to edit this raid';
            answerCallbackQuery($update['callback_query']['id'], $callback_response);
            exit;
        }
    }
}

/**
 * Raid duplication check.
 * @param $gym
 * @param $end
 * @return $raid['id'] or 0
 */
function raid_duplication_check($gym,$end)
{
    // Build query.
    $rs = my_query(
        "
        SELECT    *,
                          UNIX_TIMESTAMP(end_time)                        AS ts_end,
                          UNIX_TIMESTAMP(start_time)                      AS ts_start,
                          UNIX_TIMESTAMP(NOW())                           AS ts_now,
                          UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
            FROM      raids
            WHERE   gym_name = '{$gym}'
	    ORDER BY id DESC
	    LIMIT 1
        "
    );

    // Get row.
    $raid = $rs->fetch_assoc();

    // Set duplicate ID to 0
    $duplicate_id = 0;

    // If gym is in database and new end_time matches existing end_time the updated duplicate ID to raid ID from database
    if ($raid) {
	// Timezone - maybe there's a more elegant solution as date_default_timezone_set?!
        $tz = TIMEZONE;
        date_default_timezone_set($tz);

        // Now + $end = endtime of new raid
        $end = time() + $end*60;

        // Compare end time - check 5 minutes before and after database value
        $ts_end_before = $raid['ts_end'] - (5*60);
        $ts_end_after = $raid['ts_end'] + (5*60);

	// Debug log unix times
	debug_log("Unix timestamp of endtime new raid: " . $end);
	debug_log("Unix timestamp of endtime-5 existing raid: " . $ts_end_before);
	debug_log("Unix timestamp of endtime+5 existing raid: " . $ts_end_after);

        // Debug log
        debug_log("Searched database for raids at " . $raid['gym_name']);
        debug_log("Database raid ID of last raid at ". $raid['gym_name'] . ": " . $raid['id']);
        debug_log("New raid at " . $raid['gym_name'] . " will end: " . unix2tz($end,$tz));
        debug_log("Existing raid at " . $raid['gym_name'] . " will end between " . unix2tz($ts_end_before,$tz) . " and " . unix2tz($ts_end_after,$tz));

        // Check if end_time of new raid is between plus minus 5 minutes of existing raid
        if($end >= $ts_end_before && $end <= $ts_end_after){
	    // Update existing raid.
	    $duplicate_id = $raid['id'];
	    debug_log("New raid matches end_time of existing raid!");
	    debug_log("Updating raid ID: " . $duplicate_id);
    	} else {
	    // Create new raid.
	    debug_log("New raid end_time does not match the end_time of existing raid.");
	    debug_log("Creating new raid at gym: " . $raid['gym_name']);
        }
    } else {
	debug_log("Gym '" . $gym . "' not found in database!");
	debug_log("Creating new raid at gym: " . $gym);
    }

    // Return ID or 0
    return $duplicate_id;
}

/**
 * Insert gym.
 * @param $gym_name
 * @param $latitude
 * @param $longitude
 * @param $address
 * @return array
 */
function insert_gym($name, $lat, $lon, $address)
{
    global $db;

    // Build query to check if gym is already in database or not
    $rs = my_query(
        "
        SELECT    COUNT(*)
        FROM      gyms
          WHERE   gym_name = '{$name}'
         "
        );

    $row = $rs->fetch_row();

    // Gym already in database or new
    if (empty($row['0'])) {
        // Build query for gyms table to add gym to database
        debug_log('Gym not found in database gym list! Adding gym "' . $name . '" to the database gym list.');
        $rs = my_query(
            "
            INSERT INTO   gyms
            SET           lat = '{$lat}',
                              lon = '{$lon}',
                              gym_name = '{$db->real_escape_string($name)}',
                              address = '{$db->real_escape_string($address)}'
            "
        );
    }
}

/**
 * Inline key array.
 * @param $buttons
 * @param $columns
 * @return array
 */
function inline_key_array($buttons, $columns)
{
    $result = array();
    $col = 0;
    $row = 0;

    foreach ($buttons as $v) {
        $result[$row][$col] = $v;
        $col++;

        if ($col >= $columns) {
            $row++;
            $col = 0;
        }
    }
    return $result;
}

/**
 * Raid edit start keys.
 * @param $id
 * @return array
 */
function raid_edit_start_keys($id)
{
    $keys = [
        [
            [
                'text'          => '5 Sterne Raid',
                'callback_data' => $id . ':edit:type_5'
            ]
        ],
        [
            [
                'text'          => '4 Sterne Raid',
                'callback_data' => $id . ':edit:type_4'
            ],
            [
                'text'          => '3 Sterne Raid',
                'callback_data' => $id . ':edit:type_3'
            ]
        ],
        [
            [
                'text'          => '2 Sterne Raid',
                'callback_data' => $id . ':edit:type_2'
            ],
            [
                'text'          => '1 Stern Raid',
                'callback_data' => $id . ':edit:type_1'
            ]
        ]
    ];

    return $keys;
}

/**
 * Keys raid people.
 * @param $data
 * @return array
 */
function keys_raid_people($data)
{

    if (!is_array($data)) {
        $data = array('id' => $data);
    }

    $keys = [
        [
            'text'          => '+1',
            'callback_data' => $data['id'] . ':vote:1'
        ],
        [
            'text'          => '+2',
            'callback_data' => $data['id'] . ':vote:2'
        ],
        [
            'text'          => '+3',
            'callback_data' => $data['id'] . ':vote:3'
        ],
        [
            'text'          => '+4',
            'callback_data' => $data['id'] . ':vote:4'
        ],
        [
            'text'          => '+5',
            'callback_data' => $data['id'] . ':vote:5'
        ]
    ];

    return $keys;
}

/**
 * Keys vote.
 * @param $raid
 * @return array
 */
function keys_vote($raid)
{
    // Init keys time array.
    $keys_time = [];

    $end_time = $raid['ts_end'];
    $now = $raid['ts_now'];
    $start_time = $raid['ts_start'];

    $keys = [
        [
            [
                'text'          => 'alleine',
                'callback_data' => $raid['id'] . ':vote:1'
            ],
            [
                'text'          => '+1',
                'callback_data' => $raid['id'] . ':vote:2'
            ],
            [
                'text'          => '+2',
                'callback_data' => $raid['id'] . ':vote:3'
            ],
            [
                'text'          => '+3',
                'callback_data' => $raid['id'] . ':vote:4'
            ],
            [
                'text'          => '+4',
                'callback_data' => $raid['id'] . ':vote:5'
            ]
        ],
        [
            [
                'text'          => TEAM_B,
                'callback_data' => $raid['id'] . ':vote_team:mystic'
            ],
            [
                'text'          => TEAM_R,
                'callback_data' => $raid['id'] . ':vote_team:valor'
            ],
            [
                'text'          => TEAM_Y,
                'callback_data' => $raid['id'] . ':vote_team:instinct'
            ],
            [
                'text'          => 'Lvl +',
                'callback_data' => $raid['id'] . ':vote_level:up'
            ],
            [
                'text'          => 'Lvl -',
                'callback_data' => $raid['id'] . ':vote_level:down'
            ]
        ]
    ];

    if ($end_time < $now) {
        $keys[] = [
            array(
                'text'          => 'Raid beendet.',
                'callback_data' => $raid['id'] . ':vote_time:' . (ceil(time() / 900) * 900)
            )
        ];

    } else {
	$timePerSlot = 60*RAID_SLOTS;
	$timeBeforeEnd = 60*RAID_LAST_START;
        $col = 1;
        //for ($i = ceil($now / $timePerSlot) * $timePerSlot; $i <= ($end_time - $timeBeforeEnd); $i = $i + $timePerSlot) {
        for ($i = ceil($start_time / $timePerSlot) * $timePerSlot; $i <= ($end_time - $timeBeforeEnd); $i = $i + $timePerSlot) {

            if ($col++ >= 4) {
                $keys[] = $keys_time;
                $keys_time = [];
                $col = 1;
            }
	    // Plus 60 seconds, so vote button for e.g. 10:00 will disappear after 10:00:59 / at 10:01:00 and not right after 09:59:59 / at 10:00:00
	    if (($i + 60) > $now) {
		// Display vote buttons for now + 1 additional minute
                $keys_time[] = array(
                    'text'          => unix2tz($i, $raid['timezone']),
                    'callback_data' => $raid['id'] . ':vote_time:' . $i
                );
	    }

	    // This is our last run of the for loop since $i + timePerSlot are ahead of $end_time - $timeBeforeEnd
	    // Offer a last raid, which is x minutes before the raid ends, x = $timeBeforeEnd
            if (($i + $timePerSlot) > ($end_time - $timeBeforeEnd)) {
		// Set the time for the last possible raid and add vote key if there is enough time left
                $timeLastRaid = $end_time - $timeBeforeEnd;
		if($timeLastRaid > $i + $timeBeforeEnd && ($timeLastRaid >= $now)){
		    // Round last raid time to 5 minutes to avoid crooked voting times
		    $near5 = 5*60;
		    $timeLastRaid = round($timeLastRaid / $near5) * $near5;
                    $keys_time[] = array(
                        'text'          => unix2tz($timeLastRaid, $raid['timezone']),
                        'callback_data' => $raid['id'] . ':vote_time:' . $timeLastRaid
                    );
		}
            }
        }

        $keys[] = $keys_time;
    }


    $keys[] = [
        [
            'text'          => EMOJI_REFRESH,
            'callback_data' => $raid['id'] . ':vote_refresh:0'
        ],
        [
            'text'          => 'Bin da',
            'callback_data' => $raid['id'] . ':vote_arrived:0'
        ],
        [
            'text'          => 'Fertig',
            'callback_data' => $raid['id'] . ':vote_done:0'
        ],
        [
            'text'          => 'Absage',
            'callback_data' => $raid['id'] . ':vote_cancel:0'
        ],
    ];

    if ($end_time < $now) {
        $keys = [
            [
                [
                    'text'          => 'Raid beendet',
                    'callback_data' => $raid['id'] . ':vote_refresh:0'
                ]
            ]
        ];
    }
    return $keys;
}

/**
 * Update user.
 * @param $update
 * @return bool|mysqli_result
 */
function update_user($update)
{
    global $db;

    $name = '';
    $nick = '';
    $sep = '';

    if (isset($update['message'])) {
        $msg = $update['message']['from'];
    }

    if (isset($update['callback_query'])) {
        $msg = $update['callback_query']['from'];
    }

    if (isset($update['inline_query'])) {
        $msg = $update['inline_query']['from'];
    }

    if (!empty($msg['id'])) {
        $id = $msg['id'];

    } else {
        debug_log('No id', '!');
        debug_log($update, '!');
        return false;
    }

    if ($msg['first_name']) {
        $name = $msg['first_name'];
        $sep = ' ';
    }

    if (isset($msg['last_name'])) {
        $name .= $sep . $msg['last_name'];
    }

    if (isset($msg['username'])) {
        $nick = $msg['username'];
    }

    // Create or update the user.
    $request = my_query(
        "
        INSERT INTO users
        SET         user_id = {$id},
                    nick    = '{$db->real_escape_string($nick)}',
                    name    = '{$db->real_escape_string($name)}'
        ON DUPLICATE KEY
        UPDATE      nick    = '{$db->real_escape_string($nick)}',
                    name    = '{$db->real_escape_string($name)}'
        "
    );

    return $request;
}

/**
 * Send response vote.
 * @param $update
 * @param $data
 * @param bool $new
 */
function send_response_vote($update, $data, $new = false)
{
    // Get the raid data by id.
    $rs = my_query(
        "
        SELECT  *,
                UNIX_TIMESTAMP(end_time)                        AS ts_end,
                UNIX_TIMESTAMP(start_time)                      AS ts_start,
                UNIX_TIMESTAMP(NOW())                           AS ts_now,
                UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
        FROM    raids
          WHERE id = {$data['id']}
        "
    );

    // Get the row.
    $raid = $rs->fetch_assoc();

    $msg = show_raid_poll($raid);
    $keys = keys_vote($raid);

    // Write to log.
    debug_log($keys);

    if ($new) {
        $loc = send_location($update['callback_query']['message']['chat']['id'], $raid['lat'], $raid['lon']);

        // Write to log.
        debug_log('location:');
        debug_log($loc);

        // Send the message.
        send_message($update['callback_query']['message']['chat']['id'], $msg . "\n", $keys, ['reply_to_message_id' => $loc['result']['message_id']]);
        // Answer the callback.
        answerCallbackQuery($update['callback_query']['id'], $msg);

    } else {
        // Edit the message.
        edit_message($update, $msg, $keys);
        // Change message string.
        $msg = 'Abstimmung aktualisiert';
        // Answer the callback.
        answerCallbackQuery($update['callback_query']['id'], $msg);
    }

    exit;
}

/**
 * Convert unix timestamp to time string by timezone settings.
 * @param $unix
 * @param $tz
 * @param string $format
 * @return bool|string
 */
function unix2tz($unix, $tz, $format = 'H:i')
{
    // Unix timestamp is required.
    if (!empty($unix)) {
        // Create dateTime object.
        $dt = new DateTime('@' . $unix);

        // Set the timezone.
        $dt->setTimeZone(new DateTimeZone($tz));

        // Return formatted time.
        return $dt->format($format);

    } else {
        return false;
    }
}

/**
 * Show raid poll.
 * @param $raid
 * @return string
 */
function show_raid_poll($raid)
{
    // Init empty message string.
    $msg = '';

    // Display gym details.
    if ($raid['gym_name'] || $raid['gym_team']) {
        // Add gym name to message.
        if ($raid['gym_name']) {
            $msg .= 'Arena: <b>' . $raid['gym_name'] . '</b>';
        }
        // Add team to message.
        if ($raid['gym_team']) {

		// FB: Korrekt Team Color
		$team = '';
		if ($raid['gym_team'] == 'valor')
			$team = TEAM_R;
		else if ($raid['gym_team'] == 'instinct')
			$team = TEAM_Y;
		else if ($raid['gym_team'] == 'mystic')
			$team = TEAM_B;
            $msg .= ' ' . $team;
        }

        $msg .= CR;
    }

    // Add google maps link to message.
    if (!empty($raid['address'])) {
        $msg .= '<a href="https://maps.google.com/?daddr=' . $raid['lat'] . ',' . $raid['lon'] . '">' . $raid['address'] . '</a>' . CR;
    } else {
	$msg .= '<a href="http://maps.google.com/maps?q=' . $raid['lat'] . ',' . $raid['lon'] . '">http://maps.google.com/maps?q=' . $raid['lat'] . ',' . $raid['lon'] . '</a>' . CR;
    }

    // Display raid boss name.
    $msg .= 'Raid Boss: <b>' . ucfirst($raid['pokemon']) . '</b>' . CR;

    $time_left = floor($raid['t_left'] / 60);
    if ( strpos(str_pad($time_left % 60, 2, '0', STR_PAD_LEFT) , '-' ) !== false ) {
	// $time_left = 'beendet'; <-- REPLACED BY $tl_msg, so if clause below is still working ($time_left < 0)
        $tl_msg = '<b>Raid beendet.</b>';
    } else {
	// Replace $time_left with $tl_msg too
        $tl_msg = ' — <b>noch ' . floor($time_left / 60) . ':' . str_pad($time_left % 60, 2, '0', STR_PAD_LEFT) . 'h</b>';
    }

    // Raid has not started yet - adjust time left message
    if ($raid['ts_now'] < $raid['ts_start']) {
	$msg .= '<b>Raid-Ei öffnet sich um ' . unix2tz($raid['ts_start'], $raid['timezone']) . '</b>' . CR;

    // Raid has started and active or already ended
    } else {

        // Add raid is done message.
        // FIXED - $time_left got changed to text above, so added $tl_msg
        if ($time_left < 0) {
            $msg .= $tl_msg . CR2;

            // Add time left message.
        } else {
            $msg .= 'Raid bis ' . unix2tz($raid['ts_end'], $raid['timezone']);
	    $msg .= $tl_msg . CR;
        }
    }

    // Get attendance for this raid.
    $rs = my_query(
        "
        SELECT      *,
                    UNIX_TIMESTAMP(attend_time) AS ts_att
        FROM        attendance
          WHERE     raid_id = {$raid['id']}
          ORDER BY  cancel ASC,
                    raid_done DESC,
                    team ASC,
                    arrived DESC,
                    attend_time ASC
        "
    );

    // Init empty data array.
    $data = array();

    // For each attendance.
    while ($row = $rs->fetch_assoc()) {
        // Set cancel text.
        if ($row['cancel']) {
            $row['team'] = 'cancel';
        }
        // Set done text.
        if ($row['raid_done']) {
            $row['team'] = 'done';
        }
        if (!$row['team']) {
            $row['team'] = 'unknown';
        }

        $data[$row['team']][] = $row;

        if ($row['extra_people']) {
            for ($i = 1; $i <= $row['extra_people']; $i++) {
                $data[$row['team']][] = false;
            }
        }
    }

    debug_log($data);

    // Add no attendance found message.
    if (count($data) == 0) {
        $msg .= CR . 'Noch keine Teilnehmer.' . CR;
    }

    $rs = my_query(
        "
        SELECT DISTINCT UNIX_TIMESTAMP(attend_time) AS ts_att,
                        count(attend_time)          AS count,
                        sum(extra_people)           AS extra
        FROM            attendance
          WHERE         raid_id = {$raid['id']}
            AND         attend_time IS NOT NULL
            AND         raid_done != 1
            AND         cancel != 1
          GROUP BY      attend_time
          ORDER BY      attend_time ASC
        "
    );

    // Init empty time slots array.
    $timeSlots = array();

    while ($row = $rs->fetch_assoc()) {
        $timeSlots[] = $row;
    }

    // Write to log.
    debug_log($timeSlots);

    // TIMES
    foreach ($timeSlots as $ts) {
        // Add to message.
        $msg .= CR . '<b>' . unix2tz($ts['ts_att'], $raid['timezone']) . '</b>' . ' [' . ($ts['count'] + $ts['extra']) . ']' . CR;

        $user_rs = my_query(
            "
            SELECT        *
            FROM          attendance
              WHERE       UNIX_TIMESTAMP(attend_time) = {$ts['ts_att']}
                AND       raid_done != 1
                AND       cancel != 1
                AND       raid_id = {$raid['id']}
                ORDER BY  team ASC
            "
        );

        // Init empty attend users array.
        $att_users = array();


        while ($rowUsers = $user_rs->fetch_assoc()) {
            $att_users[] = $rowUsers;
        }

        // Write to log.
        debug_log($att_users);

        foreach ($att_users as $vv) {
            // Write to log.
            debug_log($vv['user_id']);

            // Get user data.
            $rs = my_query(
                "
                SELECT  *
                FROM    users
                WHERE   user_id = {$vv['user_id']}
                "
            );

            // Get the row.
            $row = $rs->fetch_assoc();

            // Always use name.
            $name = '<a href="tg://user?id=' . $row['user_id'] . '">' . htmlspecialchars($row['name']) . '</a>';

            // Unknown team.
            if ($row['team'] === NULL) {
                $msg .= ' └ ' . $GLOBALS['teams']['unknown'] . ' ';

            // Known team.
            } else {
                $msg .= ' └ ' . $GLOBALS['teams'][$row['team']] . ' ';
            }

            // Add level.
            if ($row['level'] != 0) {
                $msg .= '<b>'.$row['level'].'</b>';
                $msg .= ' ';
            }

            // Add name.
            $msg .= $name;
            $msg .= ' ';

            // Arrived.
            if ($vv['arrived']) {
		// No time is displayed, but undefined_index error in log, so changed it:
                //$msg .= '[Bin da' . unix2tz($vv['ts_att'], $raid['timezone']) . '] ';
                $msg .= '[Bin da] ';

            // Cancelled.
            } else if ($vv['cancel']) {
                $msg .= '[abgesagt] ';
            }

            // Add extra people.
            if ($vv['extra_people']) {
                $msg .= '+' . $vv['extra_people'];
            }

            $msg .= CR;
        }
    }

    // DONE
    if (isset($data['done']) ? count($data['done']) : '' ) {
    //if (count($data['done'])) {
        // Add to message.
        $msg .= CR . TEAM_DONE . ' <b>Fertig: </b>' . ' [' . count($data['done']) . ']' . CR;

        foreach ($data['done'] as $vv) {

            if (!$vv['raid_done']) continue;

            $rs = my_query(
                "
                SELECT    *
                FROM      users
                  WHERE   user_id = {$vv['user_id']}
                "
            );

            $row = $rs->fetch_assoc();

            $name = '<a href="tg://user?id=' . $row['user_id'] . '">' . htmlspecialchars($row['name']) . '</a>';

            // Add to message.
            $msg .= ' └ ' . $GLOBALS['teams'][$row['team']] . ' ' . $name . ' ';

            // Done.
            if ($vv['raid_done']) {
                $msg .= '[Fertig ' . unix2tz($vv['ts_att'], $raid['timezone']) . '] ';
            }
            // Add extra people.
            if ($vv['extra_people']) {
                $msg .= '+' . $vv['extra_people'];
            }

            $msg .= CR;
        }
    }

    // CANCEL
    if (isset($data['cancel']) ? count($data['cancel']) : '' ) {
    //if (count($data['cancel'])) {
        // Add to message.
        $msg .= CR . TEAM_CANCEL . ' <b>Abgesagt: </b>' . ' [' . count($data['cancel']) . ']' . CR;

        foreach ($data['cancel'] as $vv) {

            if (!$vv['cancel']) continue;

            $rs = my_query(
                "
                SELECT    *
                FROM      users
                  WHERE   user_id = {$vv['user_id']}
                "
            );

            $row = $rs->fetch_assoc();

            $name = '<a href="tg://user?id=' . $row['user_id'] . '">' . htmlspecialchars($row['name']) . '</a>';

            $msg .= ' └ ' . $GLOBALS['teams'][$row['team']] . ' ' . $name . ' ';

            // Cancel.
            if ($vv['cancel']) {
                $msg .= '[Abgesagt ' . unix2tz($vv['ts_att'], $raid['timezone']) . '] ';
            }
            // Add extra people.
            if ($vv['extra_people']) {
                $msg .= '+' . $vv['extra_people'];
            }

            $msg .= CR;
        }
    }

    // Add update time and raid id to message.
    $msg .= CR . '<i>Aktualisiert: ' . unix2tz(time(), $raid['timezone'], 'H:i:s') . '</i>';
    $msg .= '  ID = ' . $raid['id']; // Debug.

    // Return the message.
    return $msg;
}

/**
 * Show small raid poll.
 * @param $raid
 * @return string
 */
function show_raid_poll_small($raid)
{
    $time_left = floor($raid['t_left'] / 60);
    $time_left = 'noch ' . floor($time_left / 60) . ':' . str_pad($time_left % 60, 2, '0', STR_PAD_LEFT);

    $msg = '<b>' . ucfirst($raid['pokemon']) . '</b> ' . $time_left . ' <b>' . $raid['gym_name'] . '</b>' . CR;

    // Address found.
    if ($raid['address']) {
        /*
        $addr = explode(',', $raid['address'], 4);
        array_pop($addr);
        $addr = implode(',', $addr);
        // Add to message.
        */
        $msg .= '<i>' . $raid['address'] . '</i>' . CR2;
    }

    // Build query.
    $rs = my_query(
        "
        SELECT      team,
                    COUNT(*)                            AS cnt,
                    SUM(extra_people)                   AS extra
        FROM        attendance
          WHERE     raid_id = {$raid['id']}
            AND     (cancel = 0 OR cancel IS NULL)
            AND     (raid_done = 0 OR raid_done IS NULL)
          GROUP BY  team
        "
    );

    $total = 0;
    $sep = '';

    while ($row = $rs->fetch_assoc()) {
        $sum = $row['cnt'] + $row['extra'];

        if ($sum == 0) continue;

        // Add to message.
        $msg .= $sep . $GLOBALS['teams'][$row['team']] . ' ' . $sum;
        $sep = ' | ';
        $total += $sum;
    }

    if (!$total) {
        $msg .= ' Keine Teilnehmer' . CR;
    } else {
        $msg .= ' = <b>' . $total . '</b>' . CR;
    }

    return $msg;
}

/**
 * Raid list.
 * @param $update
 */
function raid_list($update)
{
    // Init empty rows array.
    $rows = array();

    // Inline list polls.
    if ($update['inline_query']['query']) {

        $iqq = intval($update['inline_query']['query']);

        // By ID.
        $request = my_query(
            "
            SELECT    *,
			          UNIX_TIMESTAMP(end_time)                        AS ts_end,
			          UNIX_TIMESTAMP(start_time)                      AS ts_start,
			          UNIX_TIMESTAMP(NOW())                           AS ts_now,
			          UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
		    FROM      raids
		      WHERE   id = {$iqq}
            "
        );

    } else {
        // By user.
        $request = my_query(
            "
            SELECT      *,
			            UNIX_TIMESTAMP(end_time)                        AS ts_end,
			            UNIX_TIMESTAMP(start_time)                      AS ts_start,
			            UNIX_TIMESTAMP(NOW())                           AS ts_now,
			            UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
		    FROM        raids
		      WHERE     user_id = {$update['inline_query']['from']['id']}
		      ORDER BY  id DESC LIMIT 3
            "
        );
    }

    while ($answer = $request->fetch_assoc()) {
        $rows[] = $answer;
    }

    debug_log($rows);
    answerInlineQuery($update['inline_query']['id'], $rows);
}

