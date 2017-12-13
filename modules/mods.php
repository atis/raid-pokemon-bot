<?php
// Write to log.
debug_log('moderators()');
debug_log($update);
debug_log($data);

// Get the action.
$limit = $data['id'];

// Get the action.
$action = $data['arg'];

if ($update['message']['chat']['type'] == 'private' || $update['callback_query']['message']['chat']['type'] == 'private') {
    // List moderators.
    if ($action == "list") {
	// Set message.
	$msg = "Liste aller Moderatoren." . CR . "Für Details Moderator auswählen:";
        // Get moderators.
        $keys = edit_moderator_keys($limit, $action);

    // Add modertor.
    } else if ($action == "add" ) {
	// Set message.
	$msg = "Neuen Moderator hinzufügen:";
	// Get users.
        $keys = edit_moderator_keys($limit, $action);

    // Delete moderator.
    } else if ($action == "delete" ) {
	// Set message.
	$msg = "Moderator löschen:";
	// Get users.
        $keys = edit_moderator_keys($limit, $action);
    }

    // Empty keys?
    if (!$keys) {
	$msg = "Fehler! Keine Moderatoren oder Benutzer gefunden!";
    }

    // Edit message.
    edit_message($update, $msg, $keys, false);

    // Build callback message string.
    $callback_response = 'OK';

    // Answer callback.
    answerCallbackQuery($update['callback_query']['id'], $callback_response);
} 
