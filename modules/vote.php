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

// Get extra people.
$extraPeople = intval($data['arg'] - 1);

// Check if we found the users team.
$team = !empty($row['team']) ? "'" . $row['team'] . "'" : NULL;

// Write to log.
debug_log($row);

// User has voted before.
if (!empty($answer)) {
    // Update attendance.
    my_query(
        "
        UPDATE    attendance
        SET       extra_people = {$extraPeople},
                  team = {$team}
          WHERE   raid_id = {$data['id']}
            AND   user_id = {$update['callback_query']['from']['id']}
        "
    );

// User has not voted before.
} else {
    // Create attendance.
    my_query(
        "
        INSERT INTO   attendance
        SET           raid_id = {$data['id']},
                      user_id = {$update['callback_query']['from']['id']},
                      extra_people = {$extraPeople},
                      team = {$team}
        "
    );
}

// Send vote response.
send_response_vote($update, $data);
