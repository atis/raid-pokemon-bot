<?php
// Write to log.
debug_log('EXIT()');
debug_log($update);
debug_log($data);

// Set empty keys.
$keys = [];

// Build message string.
$msg = getTranslation('action_aborted');

// Edit the message.
edit_message($update, $msg, $keys);

// Answer callback.
answerCallbackQuery($update['callback_query']['id'], $msg);

exit();
