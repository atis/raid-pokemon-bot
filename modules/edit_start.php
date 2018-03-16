<?php
// Write to log.
debug_log('raid_edit_start()');
debug_log($update);
debug_log($data);

// Check raid access.
raid_access_check($update, $data);

// Set the id.
$id = $data['id'];

// Set the arg.
$arg = $data['arg'];
$slot_switch = 0;
if (strpos($arg, ',') !== false)
{ 
    $args = explode(",", $arg);
    $arg = $args[0];
    $slot_switch = $args[1];
    debug_log('More options got reqeusted for raid duration!');
    debug_log('Received argument and start_time in minutes: ' . $arg . ', ' . $slot_switch);
}

if (true || $arg == "more-options" || $arg == "ex-raid") {
    if ($arg != "more-options" && $arg !="ex-raid") {
        // Build query.
        my_query(
            "
            UPDATE    raids
            SET       start_time = DATE_ADD(start_time, INTERVAL {$arg} MINUTE)
              WHERE   id = {$id}
            "
        );
    }

    // Init empty keys array.
    $keys = array();

    // Raid pokemon duration short or 1 Minute / 5 minute time slots
    if($arg == "more-options") {
        if ($slot_switch == 0) {
	    $slotmax = RAID_POKEMON_DURATION_SHORT;
	    $slotsize = 1;
        } else {
	    $slotmax = RAID_POKEMON_DURATION_LONG;
	    $slotsize = 5;
        }

        for ($i = $slotmax; $i >= 15; $i = $i - $slotsize) {
            // Create the keys.
            $keys[] = array(
	        // Just show the time, no text - not everyone has a phone or tablet with a large screen...
                'text'          => floor($i / 60) . ':' . str_pad($i % 60, 2, '0', STR_PAD_LEFT),
                'callback_data' => $id . ':edit_left:' . $i
            );
        }
    } else {
        // Use raid pokemon duration short.
        $keys[] = array(
            'text'          => '0:' . RAID_POKEMON_DURATION_SHORT,
            'callback_data' => $id . ':edit_left:' . RAID_POKEMON_DURATION_SHORT
        );

        // Button for more options.
        $keys[] = array(
            'text'          => getTranslation('expand'),
            'callback_data' => $id . ':edit_start:more-options,' . $arg
        );
    }

    // Get the inline key array.
    $keys = inline_key_array($keys, 5);

    // Write to log.
    debug_log($keys);

} else {
    // Edit pokemon.
    $keys = raid_edit_start_keys($id);
}

// Edit the message.
edit_message($update, getTranslation('how_long_raid'), $keys);

// Build callback message string.
$callback_response = getTranslation('lead_time_set_to') . ' ' . $data['arg'] . ' ' . getTranslation('minutes');

// Answer callback.
answerCallbackQuery($update['callback_query']['id'], $callback_response);
