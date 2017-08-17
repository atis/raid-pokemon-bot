<?php
// Check if the user has voted for this raid before.
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

// User has voted before.
if (!empty($answer)) {
    // Update attendance.
    my_query(
        "
        UPDATE    attendance
        SET       arrived = 1,
                  raid_done = 0,
                  cancel = 0
          WHERE   raid_id = {$data['id']}
            AND   user_id = {$update['callback_query']['from']['id']}
        "
    );

// User has not voted before.
} else {
    /*
    // Get users data.
    $rs = my_query(
        "
        SELECT    *
        FROM      users
          WHERE   user_id = {$update['callback_query']['from']['id']}
        "
    );

    // Get the row.
    $row = $rs->fetch_assoc();

    // Check if we found the users team.
    $team = !empty($row['team']) ? "'" . $row['team'] . "'" : NULL;

    // Create attendance.
    my_query(
        "
        INSERT INTO   attendance
        SET           raid_id = {$data['id']},
                      user_id = {$update['callback_query']['from']['id']},
                      arrived = 1,
                      team = {$team},
                      attend_time = NOW()
        "
    );
*/
}

// Send vote response.
send_response_vote($update, $data);
