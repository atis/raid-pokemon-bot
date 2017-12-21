<?php
/**
 * Bot access check.
 * @param $update
 * @param $access_type
 */
function bot_access_check($update, $access_type = BOT_ACCESS, $return_result = false)
{
    // Restricted or public access
    if(!empty($access_type)) {
	$all_chats = '';
	// Always add maintainer and admins.
	$all_chats .= !empty(MAINTAINER_ID) ? MAINTAINER_ID . ',' : '';
	$all_chats .= !empty(BOT_ADMINS) ? BOT_ADMINS . ',' : '';
	$all_chats .= ($access_type == BOT_ADMINS) ? '' : $access_type;

	// Check each admin chat defined in $access_type 
	$chats = explode(',', $all_chats);
   	foreach($chats as $chat) {
	    // Get chat object 
	    $chat_obj = get_chat($chat);
            debug_log("Getting chat object for '" . $chat . "'");

	    // Check chat object for proper response.
	    if ($chat_obj['ok'] == true) {
		$allow_access = false;
		// ID matching $chat and private chat type?
		if ($chat_obj['result']['id'] == $update['message']['from']['id'] && $chat_obj['result']['type'] == "private") {
		    $allow_access = true;
		    break;
		} else {
		    // Result was ok, but access not granted. Continue then.
		    continue;
		}
	    } else {
		debug_log('Chat ' . $chat . ' does not exist! Continuing with next chat...');
		continue;
	    }

	    // Clear chat_obj since it did not match 
	    $chat_obj = '';

	    // Get administrators from chat
    	    $response = get_admins($chat);
            debug_log("Getting administrators from chat '" . $chat . "'");

    	    // Make sure we get a proper response
    	    if ($response['ok'] == true) { 
	        foreach($response['result'] as $admin) {
	                // If user is found as administrator allow access to the bot
	                if ($admin['user']['id'] == $update['message']['from']['id'] || $admin['user']['id'] == $update['inline_query']['from']['id']) {
		            $allow_access = true;
		            break 2;
		        }
                }
	    }
	}

        // Prepare logging of username, first_name and/or id
	$msg = '';
	if(!empty($chat_obj['result']['id'])) {
	    $msg .= !empty($chat_obj['result']['id']) ? "Id: " . $chat_obj['result']['id'] . CR : '';
	    $msg .= !empty($chat_obj['result']['username']) ? "Username: " . $chat_obj['result']['username'] . CR : '';
	    $msg .= !empty($chat_obj['result']['first_name']) ? "First Name: " . $chat_obj['result']['first_name'] . CR : '';
	} else if (!empty($update['message']['from']['id'])) {
	    $msg .= !empty($update['message']['from']['id']) ? "Id: " . $update['message']['from']['id'] . CR : '';
	    $msg .= !empty($update['message']['from']['username']) ? "Username: " . $update['message']['from']['username'] . CR : '';
	    $msg .= !empty($update['message']['from']['first_name']) ? "First Name: " . $update['message']['from']['first_name'] . CR : '';
	} else if (!empty($update['inline_query']['from']['id'])) {
	    $msg .= !empty($update['inline_query']['from']['id']) ? "Id: " . $update['inline_query']['from']['id'] . CR : '';
	    $msg .= !empty($update['inline_query']['from']['username']) ? "Username: " . $update['inline_query']['from']['username'] . CR : '';
	    $msg .= !empty($update['inline_query']['from']['first_name']) ? "First Name: " . $update['inline_query']['from']['first_name'] . CR : '';
	}

        // Allow or deny access to the bot and log result
        if ($allow_access && !$return_result) {
            debug_log("Allowing access to the bot for user:" . CR . $msg);
        } else if ($allow_access && $return_result) {
            debug_log("Allowing access to the bot for user:" . CR . $msg);
	    return $allow_access;
        } else if (!$allow_access && $return_result) {
            debug_log("Denying access to the bot for user:" . CR . $msg);
	    return $allow_access;
        } else {
            debug_log("Denying access to the bot for user:" . CR . $msg);
            $response_msg = '<b>You are not allowed to use this command or bot!</b>';
	    sendMessage($update['message']['chat']['id'], $response_msg);
            exit;
        }
    } else {
        $msg = '';
        $msg .= !empty($update['message']['from']['id']) ? "Id: " . $update['message']['from']['id'] . CR : '';
        $msg .= !empty($update['message']['from']['username']) ? "Username: " . $update['message']['from']['username'] . CR : '';
        $msg .= !empty($update['message']['from']['first_name']) ? "First Name: " . $update['message']['from']['first_name'] . CR : '';
        debug_log("Bot access is not restricted! Allowing access for user: " . CR . $msg);
    }
}

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
               AND    moderator = 1
            "
        );

        $row = $rs->fetch_row();

        if (empty($row['0'])) {
	    $admin_access = bot_access_check($update, BOT_ADMINS, true);
	    if (!$admin_access) {
		$keys = [];
		if (isset($update['callback_query']['inline_message_id'])) {
    		    editMessageText($update['callback_query']['inline_message_id'], '<b>' . getTranslation('raid_access_denied') . '</b>', $keys);
		} else {
    		    editMessageText($update['callback_query']['message']['message_id'], '<b>' . getTranslation('raid_access_denied') . '</b>', $keys, $update['callback_query']['message']['chat']['id'], $keys);
		}
                answerCallbackQuery($update['callback_query']['id'], getTranslation('raid_access_denied'));
                exit;
	    }
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
	
	// Now
	$now = time();

	// Compare time - check minutes before and after database value
	$beforeAfter = 15;
	$extendBefore = 180;

	// Seems raid is being created at the moment
        if ($raid['ts_end'] === NULL) {
	    // Compare via start_time.
	    $compare = "start";
	    $time4compare = $now;

	    // Set compare values.
	    $ts_compare_before = $raid['ts_start'] - ($beforeAfter*60);
	    $ts_compare_after = $raid['ts_start'] + ($beforeAfter*60);
	} else {
	    // Compare via end_time.
	    $compare = "end";
	    $time4compare = $now + $end*60;

	    // Set compare values.
	    // Extend compare time for raid times if $time4compare is equal to $now which means $end must be 0
	    $ts_compare_before = ($time4compare == $now) ? ($raid['ts_end'] - ($extendBefore*60)) : ($raid['ts_end'] - ($beforeAfter*60));
	    $ts_compare_after = $raid['ts_end'] + ($beforeAfter*60);
	}

        // Debug log unix times
        debug_log('Unix timestamp of ' . $compare . 'time new raid: ' . $time4compare);
        debug_log('Unix timestamp of ' . $compare . 'time -' . (($time4compare == $now) ? $extendBefore : $beforeAfter) . ' minutes of existing raid: ' . $ts_compare_before);
        debug_log('Unix timestamp of ' . $compare . 'time +' . $beforeAfter . ' minutes of existing raid: ' . $ts_compare_after);

        // Debug log
        debug_log('Searched database for raids at ' . $raid['gym_name']);
        debug_log('Database raid ID of last raid at '. $raid['gym_name'] . ': ' . $raid['id']);
        debug_log('New raid at ' . $raid['gym_name'] . ' will ' . $compare . ': ' . unix2tz($time4compare,$tz));
        debug_log('Existing raid at ' . $raid['gym_name'] . ' will ' . $compare . ' between ' . unix2tz($ts_compare_before,$tz) . ' and ' . unix2tz($ts_compare_after,$tz));

        // Check if end_time of new raid is between plus minus the specified minutes of existing raid
        if($time4compare >= $ts_compare_before && $time4compare <= $ts_compare_after){
	    // Update existing raid.
	    // Negative raid ID if compare method is start and not end time
	    $duplicate_id = ($compare == "start") ? (0-$raid['id']) : $raid['id'];
	    debug_log('New raid matches ' . $compare . 'time of existing raid!');
	    debug_log('Updating raid ID: ' . $duplicate_id);
    	} else {
	    // Create new raid.
	    debug_log('New raid ' . $compare . 'time does not match the ' . $compare . 'time of existing raid.');
	    debug_log('Creating new raid at gym: ' . $raid['gym_name']);
        }
    } else {
	debug_log("Gym '" . $gym . "' not found in database!");
	debug_log("Creating new raid at gym: " . $gym);
    }

    // Return ID, -ID or 0
    return $duplicate_id;
}

/**
 * Insert gym.
 * @param $gym_name
 * @param $latitude
 * @param $longitude
 * @param $address
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
    } else {
        // Update gyms table to reflect gym changes.
        debug_log('Gym found in database gym list! Updating gym "' . $name . '" now.');
        $rs = my_query(
            "
            UPDATE        gyms
            SET           lat = '{$lat}',
                              lon = '{$lon}',
                              address = '{$db->real_escape_string($address)}'
               WHERE      gym_name = '{$name}'
            "
        );
    }
}

/**
 * Get gym.
 * @param $id
 * @return array
 */
function get_gym($id)
{
    // Get gyms from database
    $rs = my_query(
            "
            SELECT    *
            FROM      gyms
	    WHERE     id = {$id}
            "
        );

    $gym = $rs->fetch_assoc();

    return $gym;
}

/**
 * Get user.
 * @param $user_id
 * @return message
 */
function get_user($user_id)
{
    // Get user details.
    $rs = my_query(
        "
        SELECT    * 
                FROM      users
                  WHERE   user_id = {$user_id}
        "
    );

    // Fetch the row.
    $row = $rs->fetch_assoc();

    // Build message string.
    $msg = '';

    // Add name.
    $msg .= 'Name: <a href="tg://user?id=' . $row['user_id'] . '">' . htmlspecialchars($row['name']) . '</a>' . CR;

    // Unknown team.
    if ($row['team'] === NULL) {
        $msg .= 'Team: ' . $GLOBALS['teams']['unknown'] . CR;

    // Known team.
    } else {
        $msg .= 'Team: ' . $GLOBALS['teams'][$row['team']] . CR;
    }

    // Add level.
    if ($row['level'] != 0) {
        $msg .= 'Level: ' . $row['level'] . CR;
    }

    return $msg;
}

/**
 * Moderator keys.
 * @param $limit
 * @param $action
 * @return array
 */
function edit_moderator_keys($limit, $action)
{
    // Number of entries to display at once.
    $entries = 10;

    // Init empty keys array.
    $keys = array();

    // Get moderators from database
    if ($action == "list" || $action == "delete") {
        $rs = my_query(
                "
                SELECT    *
                FROM      users
                WHERE     moderator = 1 
	        ORDER BY  name
	        LIMIT     $limit, $entries
                "
            );

	// Number of entries
        $cnt = my_query(
                "
                SELECT    COUNT(*)
                FROM      users
                WHERE     moderator = 1 
                "
            );
    } else if ($action == "add") {
        $rs = my_query(
                "
                SELECT    *
                FROM      users
                WHERE     (moderator = 0 OR moderator IS NULL)
                ORDER BY  name
                LIMIT     $limit, $entries
                "
            );

	// Number of entries
        $cnt = my_query(
                "
                SELECT    COUNT(*)
                FROM      users
                WHERE     (moderator = 0 OR moderator IS NULL)
                "
            );
    }

    // Number of database entries found.
    $sum = $cnt->fetch_row();
    $count = $sum['0'];

    // List users / moderators
    while ($mod = $rs->fetch_assoc()) {
        $keys[] = array(
            'text'          => $mod['name'],
            'callback_data' => '0:mods_' . $action . ':' . $mod['user_id']
        );
    }

    // Add back key.
    if ($limit > 0) {
	$new_limit = $limit - $entries;
	$empty_back_key = array();
	$key_back = back_key($empty_back_key, $new_limit, "mods", $action);
	$key_back = $key_back[0];
	$keys = array_merge($key_back, $keys);
    }

    // Add next key.
    if (($limit + $entries) < $count) {
	$new_limit = $limit + $entries;
	$empty_next_key = array();
	$key_next = next_key($empty_next_key, $new_limit, "mods", $action);
	$key_next = $key_next[0];
	$keys = array_merge($keys, $key_next);
    }

    // Get the inline key array.
    $keys = inline_key_array($keys, 1);

    return $keys;
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
                'text'          => getTranslation('5stars'),
                'callback_data' => $id . ':edit:5'
            ]
        ],
        [
            [
                'text'          => getTranslation('4stars'),
                'callback_data' => $id . ':edit:4'
            ],
            [
                'text'          => getTranslation('3stars'),
                'callback_data' => $id . ':edit:3'
            ]
// No raids for level 2 or 1
/*        ],
        [
            [
                'text'          => getTranslation('2stars'),
                'callback_data' => $id . ':edit:2'
            ],
            [
                'text'          => getTranslation('5stars'),
                'callback_data' => $id . ':edit:1'
            ]
*/        ]
    ];

    return $keys;
}

/**
 * Raid gym first letter selection
 * @param $chat_id
 * @param $chattype
 * @return $keys array
 */
function raid_edit_gyms_first_letter_keys($chatid, $chattype) {
    // Get gyms from database
    $rs = my_query(
            "
            SELECT    *
            FROM      gyms
            ORDER BY  gym_name
            "
        );

    // Init empty keys array.
    $keys = array();

    // Init previous first letter
    $previous = null;

    while ($gym = $rs->fetch_assoc()) {
	$first = strtoupper(substr($gym['gym_name'], 0, 1));
	// Add first letter to keys array
        if($previous !== $first) {
            $keys[] = array(
                'text'          => $first,
                'callback_data' => $chatid . ',' . $chattype . ':raid_by_gym:' . $first
            );
	}
        $previous = $first;
    }

    // Get the inline key array.
    $keys = inline_key_array($keys, 4);

    return $keys;
}

/**
 * Raid edit gym keys.
 * @param $chat_id
 * @param $chattype
 * @param $first
 * @return $keys array
 */
function raid_edit_gym_keys($chatid, $chattype, $first)
{
    // Get gyms from database
    $rs = my_query(
            "
            SELECT    *
            FROM      gyms
	    WHERE     UPPER(LEFT(gym_name, 1)) = UPPER('{$first}')
	    ORDER BY  gym_name
            "
        );

    // Init empty keys array.
    $keys = array();

    while ($gym = $rs->fetch_assoc()) {
	$keys[] = array(
            'text'          => $gym['gym_name'],
            'callback_data' => $chatid . ',' . $chattype . ':raid_create:ID,' . $gym['id']
        );
    }
    
    // Get the inline key array.
    $keys = inline_key_array($keys, 1);

    return $keys;
}

/**
 * Pokemon keys.
 * @param $raid_id
 * @param $raid_level
 * @return array
 */
function pokemon_keys($raid_id, $raid_level, $pokemonlist, $action)
{
    // Init empty keys array.
    $keys = array();

    // Iterate thru the pokemon list to create the keys
    foreach($pokemonlist as $level => $levelmons) {
        if($level == $raid_level) {
            // Create the keys.
            foreach($levelmons as $key => $pokemon) {
                $keys[] = array(
                    'text'          => $pokemon,
                    'callback_data' => $raid_id . ':' . $action . ':' . $pokemon
                );
	    }
	}
    }

    // Get the inline key array.
    $keys = inline_key_array($keys, 3);

    return $keys;
}

/**
 * Back key.
 * @param $keys
 * @param $id
 * @param $action
 * @param $arg
 * @return array
 */
function back_key($keys, $id, $action, $arg)
{
    $keys[] = [
            array(
                'text'          => getTranslation('back'),
                'callback_data' => $id . ':' . $action . ':' . $arg
            )
        ];

    return $keys;
}

/**
 * Next key.
 * @param $keys
 * @param $id
 * @param $action
 * @param $arg
 * @return array
 */
function next_key($keys, $id, $action, $arg) 
{
    $keys[] = [
            array(
                'text'          => getTranslation('next'), 
                'callback_data' => $id . ':' . $action . ':' . $arg
            )
        ];

    return $keys;
}

/**
 * Insert cleanup info to database.
 * @param $chat_id
 * @param $message_id
 * @param $raid_id
 */
function insert_cleanup($chat_id, $message_id, $raid_id)
{
    // Log ID's of raid, chat and message
    debug_log('Raid_ID: ' . $raid_id);
    debug_log('Chat_ID: ' . $chat_id);
    debug_log('Message_ID: ' . $message_id);

    if ((is_numeric($chat_id)) && (is_numeric($message_id)) && (is_numeric($raid_id)) && ($raid_id > 0)) {
        global $db;

        // Get raid times.
        $rs = my_query(
            "
            SELECT    *, 
                                  UNIX_TIMESTAMP(start_time)                      AS ts_start,
                                  UNIX_TIMESTAMP(end_time)                        AS ts_end,
                                  UNIX_TIMESTAMP(NOW())                           AS ts_now,
                                  UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
                    FROM      raids
                      WHERE   id = {$raid_id}
            "
        );
    
        // Fetch raid data.
        $raid = $rs->fetch_assoc();
	
	// Init found.
	$found = false;

        // Insert cleanup info to database
        if ($raid) {
	    // Check if cleanup info is already in database or not
	    // Needed since raids can be shared to multiple channels / supergroups!
	    $rs = my_query(
                "
		SELECT    *
            	    FROM      cleanup
                    WHERE     raid_id = '{$raid_id}'
                "
            );

	    // Chat_id and message_id equal to info from database
	    while ($cleanup = $rs->fetch_assoc()) {
		// Leave while loop if cleanup info is already in database
		if(($cleanup['chat_id'] == $chat_id) && ($cleanup['message_id'] == $message_id)) {
            	    debug_log('Cleanup preparation info is already in database!');
		    $found = true;
		    break;
		} 
	    }
	}

	// Insert into database when raid found but no cleanup info found
        if ($raid && !$found) {
            // Build query for cleanup table to add cleanup info to database
            debug_log('Adding cleanup info to database:');
            $rs = my_query(
                "
                INSERT INTO   cleanup
                SET           raid_id = '{$raid_id}',
                                  chat_id = '{$chat_id}',
                                  message_id = '{$message_id}'
                "
            );
	} 
    } else {
        debug_log('Invalid input for cleanup preparation!');
    }
}

/**
 * Run cleanup.
 * @param $telegram
 * @param $database
 */
function run_cleanup ($telegram = 2, $database = 2) {
    // Check configuration, cleanup of telegram needs to happen before database cleanup!
    if (CLEANUP_TIME_TG > CLEANUP_TIME_DB) {
	debug_log('Configuration issue! Cleanup time for telegram messages needs to be lower or equal to database cleanup time!');
	debug_log('Stopping cleanup process now!');
	exit;
    }

    /* Check input
     * 0 = Do nothing
     * 1 = Cleanup
     * 2 = Read from config
    */

    // Get cleanup values from config per default.
    if ($telegram == 2) {
	$telegram = (CLEANUP_TELEGRAM == true) ? 1 : 0;
    }

    if ($database == 2) {
	$database = (CLEANUP_DATABASE == true) ? 1 : 0;
    }

    // Start cleanup when at least one parameter is set to trigger cleanup
    if ($telegram == 1 || $database == 1) {
        // Get cleanup info.
        $rs = my_query(
            "
            SELECT    * 
                    FROM      cleanup
            "
        );

        // Init empty cleanup jobs array.
        $cleanup_jobs = array();

	// Fill array with cleanup jobs.
        while ($rowJob = $rs->fetch_assoc()) {
            $cleanup_jobs[] = $rowJob;
        }

        // Write to log.
        debug_log($cleanup_jobs);

        // Init previous raid id.
        $prev_raid_id = "FIRST_RUN";

        foreach ($cleanup_jobs as $row) {
	    // Set current raid id.
	    $current_raid_id = ($row['raid_id'] == 0) ? $row['cleaned'] : $row['raid_id'];

            // Write to log.
            debug_log("Cleanup ID: " . $row['id']);
            debug_log("Chat ID: " . $row['chat_id']);
            debug_log("Message ID: " . $row['message_id']);
            debug_log("Raid ID: " . $row['raid_id']);

	    // Get raid data only when raid_id changed compared to previous run
	    if ($prev_raid_id != $current_raid_id) {
                // Get the raid data by id.
                $rs = my_query(
                    "
                    SELECT  *,
                            UNIX_TIMESTAMP(end_time)                        AS ts_end,
                            UNIX_TIMESTAMP(start_time)                      AS ts_start,
                            UNIX_TIMESTAMP(NOW())                           AS ts_now,
                            UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
                    FROM    raids
                      WHERE id = {$current_raid_id}
                    "
                );

                // Fetch raid data.
                $raid = $rs->fetch_assoc();

	        // Set times. 
	        $end = $raid['ts_end'];
	        $tz = $raid['timezone'];
    	        $now = $raid['ts_now'];
	        $cleanup_time_tg = 60*CLEANUP_TIME_TG;
	        $cleanup_time_db = 60*CLEANUP_TIME_DB;

		// Write times to log.
		debug_log("Raid end time: " . unix2tz($end,$tz,"Y-m-d H:i:s"));
		debug_log("Raid cleanup time: " . unix2tz(($end + $cleanup_time),$tz,"Y-m-d H:i:s"));
		debug_log("Current time: " . unix2tz($now,$tz,"Y-m-d H:i:s"));

		// Write unix timestamps to log.
		debug_log("Unix timestamps:");
		debug_log("Raid end time: " . $end);
		debug_log("Telegram cleanup time: " . ($end + $cleanup_time_tg));
		debug_log("Database cleanup time: " . ($end + $cleanup_time_db));
		debug_log("Current time: " . $now);
	    }

	    // Time for telegram cleanup?
	    if (($end + $cleanup_time_tg) < $now) {
                // Delete raid poll telegram message if not already deleted
	        if ($telegram == 1 && $row['chat_id'] != 0 && $row['message_id'] != 0) {
		    // Delete telegram message.
                    debug_log('Deleting telegram message ' . $row['message_id'] . ' from chat ' . $row['chat_id'] . ' for raid ' . $row['raid_id']);
                    delete_message($row['chat_id'], $row['message_id']);
		    // Set database values of chat_id and message_id to 0 so we know telegram message was deleted already.
                    debug_log('Updating telegram cleanup inforamtion.');
		    my_query(
    		    "
    		        UPDATE    cleanup
    		        SET       chat_id = 0, 
    		                  message_id = 0 
      		        WHERE   id = {$row['id']}
		    "
		    );
	        } else {
		    if ($telegram == 1) {
			debug_log('Telegram message is already deleted!');
		    } else {
			debug_log('Telegram cleanup was not triggered! Skipping...');
		    }
		}
	    } else {
		debug_log('Skipping cleanup of telegram for this raid! Cleanup time has not yet come...');
	    }

	    // Time for database cleanup?
	    if (($end + $cleanup_time_db) < $now) {
                // Delete raid from attendance table.
	        // Make sure to delete only once - raid may be in multiple channels/supergroups, but only 1 time in database
	        if (($database == 1) && $row['raid_id'] != 0 && ($prev_raid_id != $current_raid_id)) {
		    // Delete raid from attendance table.
                    debug_log('Deleting attendances for raid ' . $current_raid_id);
                    my_query(
                    "
                        DELETE FROM    attendance
                        WHERE   id = {$row['raid_id']}
                    "
                    );

		    // Set database value of raid_id to 0 so we know attendance info was deleted already
		    // Use raid_id in where clause since the same raid_id can in cleanup more than once
                    debug_log('Updating database cleanup inforamtion.');
                    my_query(
                    "
                        UPDATE    cleanup
                        SET       raid_id = 0, 
				  cleaned = {$row['raid_id']}
                        WHERE   raid_id = {$row['raid_id']}
                    "
                    );
	        } else {
		    if ($database == 1) {
		        debug_log('Attendances are already deleted!');
		    } else {
			debug_log('Attendance cleanup was not triggered! Skipping...');
		    }
		}

		// Delete raid from cleanup table and raid table once every value is set to 0 and cleaned got updated from 0 to the raid_id
		// In addition trigger deletion only when previous and current raid_id are different to avoid unnecessary sql queries
		if ($row['raid_id'] == 0 && $row['chat_id'] == 0 && $row['message_id'] == 0 && $row['cleaned'] != 0 && ($prev_raid_id != $current_raid_id)) {
		    // Delete raid from raids table.
		    debug_log('Deleting raid ' . $row['cleaned'] . ' from database.');
                    my_query(
                    "
                        DELETE FROM    raids
                        WHERE   id = {$row['cleaned']}
                    "
                    );
		    
		    // Get all cleanup jobs which will be deleted now.
                    debug_log('Removing cleanup info from database:');
		    $rs_cl = my_query(
                    "
                        SELECT *
			FROM    cleanup
                        WHERE   cleaned = {$row['cleaned']}
                    "
		    );

		    // Log each cleanup ID which will be deleted.
		    while($rs_cleanups = $rs_cl->fetch_assoc()) {
 			debug_log('Cleanup ID: ' . $rs_cleanups['id'] . ', Former Raid ID: ' . $rs_cleanups['cleaned']);
		    }

		    // Finally delete from cleanup table.
                    my_query(
                    "
                        DELETE FROM    cleanup
                        WHERE   cleaned = {$row['cleaned']}
                    "
                    );
		} else {
		    if ($prev_raid_id != $current_raid_id) {
			debug_log('Time for complete removal of raid from database has not yet come.');
		    } else {
			debug_log('Complete removal of raid from database was already done!');
		    }
		}
	    } else {
		debug_log('Skipping cleanup of database for this raid! Cleanup time has not yet come...');
	    }
	
	    // Store current raid id as previous id for next loop
            $prev_raid_id = $current_raid_id;
        }

    // Write to log.
    debug_log('Finished with cleanup process!');
    }
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
                'text'          => getTranslation('alone'),
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
                'text'          => getTranslation('raid_done'),
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
            'text'          => getTranslation('here'),
            'callback_data' => $raid['id'] . ':vote_arrived:0'
        ],
        [
            'text'          => getTranslation('done'),
            'callback_data' => $raid['id'] . ':vote_done:0'
        ],
        [
            'text'          => getTranslation('cancellation'),
            'callback_data' => $raid['id'] . ':vote_cancel:0'
        ],
    ];

    if ($end_time < $now) {
        $keys = [
            [
                [
                    'text'          => getTranslation('raid_done'),
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
        $msg = getTranslation('vote_updated');
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
 * Weekday number to weekday name
 * @param weekdaynumber
 */
function weekday_number2name ($weekdaynumber)
{
    // Numeric value below 7 is required
    if(is_numeric($weekdaynumber) && $weekdaynumber <= 7) {
	switch($weekdaynumber) {
	    case 1: 
		$weekday = getTranslation('monday');
		break;
	    case 2: 
		$weekday = getTranslation('tuesday');
		break;
	    case 3: 
		$weekday = getTranslation('wensday');
		break;
	    case 4: 
		$weekday = getTranslation('thursday');
		break;
	    case 5: 
		$weekday = getTranslation('friday');
		break;
	    case 6: 
		$weekday = getTranslation('saterday');
		break;
	    case 7: 
		$weekday = getTranslation('sunday');
		break;
	}
    }
    // Return the weekday
    return $weekday;
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
            $msg .= getTranslation('gym') . ': <b>' . $raid['gym_name'] . '</b>';
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
    $msg .= getTranslation('raid_boss') . ': <b>' . ucfirst($raid['pokemon']) . '</b>' . CR;

    $time_left = floor($raid['t_left'] / 60);
    if ( strpos(str_pad($time_left % 60, 2, '0', STR_PAD_LEFT) , '-' ) !== false ) {
	// $time_left = 'beendet'; <-- REPLACED BY $tl_msg, so if clause below is still working ($time_left < 0)
        $tl_msg = '<b> ' . getTranslation('raid_done') . '</b>';
    } else {
	// Replace $time_left with $tl_msg too
        $tl_msg = ' — <b>' . getTranslation('still') . ' ' . floor($time_left / 60) . ':' . str_pad($time_left % 60, 2, '0', STR_PAD_LEFT) . 'h</b>';
    }

    // Raid has not started yet - adjust time left message
    if ($raid['ts_now'] < $raid['ts_start']) {
	$weekday_now = date('N', $raid['ts_now']);
	$weekday_start = date('N', $raid['ts_start']);
	$raid_day = weekday_number2name ($weekday_start);
	if ($weekday_now == $weekday_start) {
	    $msg .= '<b>' . getTranslation('raid_egg_opens') . ' ' . unix2tz($raid['ts_start'], $raid['timezone']) . '</b>' . CR;
	} else {
	    $msg .= '<b>' . getTranslation('raid_egg_opens_day') . ' ' .  $raid_day . ' ' . getTranslation('raid_egg_opens_at') . unix2tz($raid['ts_start'], $raid['timezone']) . '</b>' . CR;
	}

    // Raid has started and active or already ended
    } else {

        // Add raid is done message.
        // FIXED - $time_left got changed to text above, so added $tl_msg
        if ($time_left < 0) {
            $msg .= $tl_msg . CR2;

            // Add time left message.
        } else {
            $msg .= getTranslation('raid_until') . ' ' . unix2tz($raid['ts_end'], $raid['timezone']);
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

    // Add Ex-Raid Message if Pokemon is in Ex-Raid-List.
    $pokemonlist = $GLOBALS['pokemon'];
    foreach($pokemonlist as $level => $levelmons) {
        if($level == "X") {
            foreach($levelmons as $key => $pokemon) {
		if(strtolower($pokemon) == strtolower($raid['pokemon'])) {
		    $msg.= CR . EMOJI_WARN . ' <b>' . getTranslation('exraid_pass') . '</b> ' . EMOJI_WARN;
		    break 2;
	        }
	    }
        }
    }

    // Add no attendance found message.
    if (count($data) == 0) {
        $msg .= CR . getTranslation('no_participants_yet') . CR;
    }

    $rs = my_query(
        "
        SELECT DISTINCT UNIX_TIMESTAMP(attend_time) AS ts_att,
                        count(attend_time)          AS count,
                        sum(team = 'mystic')        AS count_mystic,
                        sum(team = 'valor')         AS count_valor,
                        sum(team = 'instinct')      AS count_instinct,
                        sum(team IS NULL)           AS count_no_team,
                        sum(extra_people)           AS extra,
			attend_time
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
        $msg .= CR . '<b>' . unix2tz($ts['ts_att'], $raid['timezone']) . '</b>' . ' [' . ($ts['count'] + $ts['extra']) . ']';
	if ($ts['count'] > 0) {
	    $msg .= ' — ';
	    $msg .= (($ts['count_mystic'] > 0) ? TEAM_B . $ts['count_mystic'] . '  ' : '');
	    $msg .= (($ts['count_valor'] > 0) ? TEAM_R . $ts['count_valor'] . '  ' : '');
	    $msg .= (($ts['count_instinct'] > 0) ? TEAM_Y . $ts['count_instinct'] . '  ' : '');
	    $msg .= ((($ts['count_no_team'] + $ts['extra']) > 0) ? TEAM_UNKNOWN . ($ts['count_no_team'] + $ts['extra']) : '');
	    $msg .= CR;
	}

        $user_rs = my_query(
            "
            SELECT        *
            FROM          attendance
              WHERE       UNIX_TIMESTAMP(attend_time) = {$ts['ts_att']}
                AND       raid_done != 1
                AND       cancel != 1
                AND       raid_id = {$raid['id']}
                ORDER BY  team ASC, arrived ASC
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
                $msg .= '[' . getTranslation('here') . '] ';

            // Cancelled.
            } else if ($vv['cancel']) {
                $msg .= '[' . getTranslation('cancel') . '] ';
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
        $msg .= CR . TEAM_DONE . ' <b>' . getTranslation('finished') . ': </b>' . ' [' . count($data['done']) . ']' . CR;

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
                $msg .= '[' . unix2tz($vv['ts_att'], $raid['timezone']) . '] ';
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
        $msg .= CR . TEAM_CANCEL . ' <b>' . getTranslation('cancel') . ': </b>' . ' [' . count($data['cancel']) . ']' . CR;

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
                $msg .= '[' . unix2tz($vv['ts_att'], $raid['timezone']) . '] ';
            }
            // Add extra people.
            if ($vv['extra_people']) {
                $msg .= '+' . $vv['extra_people'];
            }

            $msg .= CR;
        }
    }

    // Display user which created the raid.
    // Get user data.
    $rs = my_query(
        "
        SELECT  *
        FROM    users
        WHERE   user_id = {$raid['user_id']}
        "
    );

    // Get the row.
    $row = $rs->fetch_assoc();

    // Display creator.
    if ($row['user_id'] && $row['name']) {
        $msg .= CR . getTranslation('created_by') . ': <a href="tg://user?id=' . $row['user_id'] . '">' . htmlspecialchars($row['name']) . '</a>';
    }

    // Add update time and raid id to message.
    $msg .= CR . '<i>' . getTranslation('updated') . ': ' . unix2tz(time(), $raid['timezone'], 'H:i:s') . '</i>';
    $msg .= '  ID = ' . $raid['id']; // DO NOT REMOVE! --> NEEDED FOR CLEANUP PREPARATION!

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
    // Left for possible future redesign of small raid poll
    //$time_left = floor($raid['t_left'] / 60);
    //$time_left = 'noch ' . floor($time_left / 60) . ':' . str_pad($time_left % 60, 2, '0', STR_PAD_LEFT);

    // Build message string.
    $msg = '';
    // Pokemon
    if(!empty($raid['pokemon'])) {
        $msg .= '<b>' . ucfirst($raid['pokemon']) . '</b> ';
    }
    // Start time and end time
    if(!empty($raid['ts_start']) && !empty($raid['ts_end'])) {
        $msg .= '<b>' . getTranslation('from') . ' ' . unix2tz($raid['ts_start'], $raid['timezone']) . ' ' . getTranslation('to') . ' ' . unix2tz($raid['ts_end'], $raid['timezone'])  . '</b>' . CR;
    }
    // Gym Name
    if(!empty($raid['gym_name'])) {
        $msg .= $raid['gym_name'] . CR;
    }

    // Address found.
    if (!empty($raid['address'])) {
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
    $total_extra = 0;
    $sep = '';
    $msg_teams = '';

    while ($row = $rs->fetch_assoc()) {
        $sum = $row['cnt'];

        if ($sum == 0) continue;

        // Add to message.
        $msg_teams .= $sep . $GLOBALS['teams'][$row['team']] . ' ' . $sum;
        $sep = '   ';
        $total += $sum;
    
        if ($row['extra'] > 0) {
	    $total_extra += $row['extra'];
            $total += $row['extra'];
        }
    }
    if ($total_extra > 0) {
        $msg_teams .= $sep . TEAM_UNKNOWN . ' ' . $total_extra;
    }

    if (!$total) {
        $msg .= getTranslation('no_participants') . CR;
    } else {
        $msg .= EMOJI_GROUP . '<b> ' . $total . '</b> — ' . $msg_teams;
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

