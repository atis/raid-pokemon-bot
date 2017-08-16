<?php
// Build the query.
$rs = my_query(
    "
    SELECT    *
    FROM      attendance
      WHERE   raid_id = {$data['id']}
        AND   user_id = {$update['callback_query']['from']['id']}
    "
);

// Get the answer.
$answer = $rs->fetch_assoc();

// Write to log.
debug_log($answer);

// Build the query.
$rs = my_query(
    "
    SELECT    *
    FROM      users
      WHERE   user_id = {$update['callback_query']['from']['id']}
    "
);

// Get the row.
$row = $rs->fetch_assoc();

$qq = 'extra_people=' . intval($data['arg'] - 1);

if ($row['team']) {
    $qq .= ', team="' . $row['team'] . '"';
}

// Write to log.
debug_log($row);
debug_log($qq);

if (!$answer) {
    my_query('INSERT INTO attendance SET raid_id=' . $data['id'] . ', user_id=' . $update['callback_query']['from']['id'] . ', ' . $qq);
} else {
    my_query('UPDATE attendance SET ' . $qq . ' WHERE raid_id=' . $data['id'] . ' AND user_id=' . $update['callback_query']['from']['id']);
}

// Send vote response.
send_response_vote($update, $data);
