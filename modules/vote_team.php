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
        SET       team = '{$data['arg']}'
          WHERE   raid_id = {$data['id']}
            AND   user_id = {$update['callback_query']['from']['id']}
        "
    );

// User has not voted before.
// Do nothing to avoid that the same person appears twice in raid attendances when quickly pressing a team and a voting time button
/*} else {
    // Create attendance.
    my_query(
        "
        INSERT INTO   attendance
        SET           raid_id = {$data['id']},
                      user_id = {$update['callback_query']['from']['id']},
                      team = '{$data['arg']}'
        "
    );
*/
}

// Update users team.
my_query(
    "
    UPDATE    users
    SET       team = '{$data['arg']}'
      WHERE   user_id = {$update['callback_query']['from']['id']}
    "
);

// Send vote response.
send_response_vote($update, $data);
		
