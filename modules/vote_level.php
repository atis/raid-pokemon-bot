<?php
// Update users level.
$action=$data['arg'];

if ($action=='up') {

	my_query(
		"
		UPDATE    users
		SET       level = level +1
		  WHERE   user_id = {$update['callback_query']['from']['id']}
		  AND     (level !=40)
		"
	);
	
}

if ($action=='down') {

	my_query(
		"
		UPDATE    users
		SET       level = level -1
		  WHERE   user_id = {$update['callback_query']['from']['id']}
		  AND     (level != 0)
		"
	);
	
}

// Send vote response.
send_response_vote($update, $data);