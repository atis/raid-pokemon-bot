<?php
// Get action.
$action = $data['arg'];

// Up-vote.
if ($action == 'up') {
    // Increase users level.
    my_query(
        "
        UPDATE    users
        SET       level = level+1
          WHERE   user_id = {$update['callback_query']['from']['id']}
            AND   level < 40
        "
    );
}

// Down-vote.
if ($action == 'down') {
    // Decrease users level.
    my_query(
        "
        UPDATE    users
        SET       level = level-1
          WHERE   user_id = {$update['callback_query']['from']['id']}
            AND   level > 0
        "
    );
}

// Send vote response.
send_response_vote($update, $data);