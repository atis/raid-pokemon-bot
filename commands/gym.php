<?php

$gym_name = trim(substr($update['message']['text'], 4));

// Write to log.
debug_log('SET gym name to ' . $gym_name);

if ($update['message']['chat']['type'] == 'private' || $update['callback_query']['message']['chat']['type'] == 'private') {

    $query = 'UPDATE raids SET gym_name="' . $db->real_escape_string($gym_name) . '" WHERE user_id=' . $update['message']['from']['id'] . ' ORDER BY id DESC LIMIT 1';
    my_query($query);

    sendMessage($update['message']['chat']['id'], 'Gym name updated');

} else {
    if ($update['message']['reply_to_message']['text']) {
        $lines = explode(CR, $update['message']['reply_to_message']['text']);
        $last_line = array_pop($lines);
        $pos = strpos($last_line, 'ID = ');
        $id = intval(trim(substr($last_line, $pos + 5)));
        debug_log('Gym ID=' . $id . ' name=' . $gym_name);

        $query = 'SELECT COUNT(*) FROM users WHERE user_id=' . $update['message']['from']['id'] . ' AND moderator=1';
        $rs = my_query($query);

        $row = $rs->fetch_row();
        $q = ' AND user_id=' . $update['message']['from']['id'];
        if ($row[0]) {
            $q = '';
        }

        $query = 'UPDATE raids SET gym_name="' . $db->real_escape_string($gym_name) . '" WHERE id=' . $id . ' ' . $q;
        my_query($query);

        $rs = my_query(
            "
            SELECT  *,
                    UNIX_TIMESTAMP(end_time)                        AS ts_end,
                    UNIX_TIMESTAMP(NOW())                           AS ts_now,
                    UNIX_TIMESTAMP(end_time)-UNIX_TIMESTAMP(NOW())  AS t_left
            FROM    raids
              WHERE id = {$id}
            "
        );

        $raid = $rs->fetch_assoc();

        $text = show_raid_poll($raid);
        $keys = keys_vote($raid);

        editMessageText($update['message']['reply_to_message']['message_id'], $text, $keys, $update['message']['chat']['id']);
    }
}

exit;
