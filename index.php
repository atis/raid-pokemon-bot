<?php
// Set error reporting in debug mode.
if (DEBUG === true) {
    error_reporting(E_ALL ^ E_NOTICE);
}

// Get current unix timestamp as float.
$start = microtime(true);

// Include files.
require_once('config.php');
require_once('debug.php');
require_once('constants.php');
require_once('functions.php');
require_once('logic.php');
require_once('geo_api.php');

// Get api key from get parameters.
$apiKey = $_GET['apikey'];

// Check if hashed api key is matching config.
if (hash('sha512', $apiKey) == CONFIG_HASH) {
    // Split the api key.
    $splitKey = explode(':', $apiKey);

    // Set constants.
    define('API_KEY', $apiKey);

// Api key is wrong!
} else {
    // Echo data.
    sendMessageEcho(MAINTAINER_ID, $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['HTTP_X_FORWARDED_FOR'] . ' ' . $apiKey);
    // And exit script.
    exit();
}

// Get content from POST data.
$content = file_get_contents('php://input');

// Decode the json string.
$update = json_decode($content, true);

// Update var is false.
if (!$update) {
    // Write to log.
    debug_log($content, '!');

} else {
    // Write to log.
    debug_log($update, '<');
}

// Init command.
$command = NULL;

// Establish mysql connection.
$db = new mysqli('localhost', DB_NAME, DB_PASSWORD, DB_USER);

// Error connecting to db.
if ($db->connect_errno) {
    // Write connection error to log.
    debug_log("Failed to connect to Database!" . $db->connect_error(), '!');
    // Echo data.
    sendMessage($update['message']['chat']['id'], "Failed to connect to Database!\nPlease contact " . MAINTAINER . " and forward this message...\n");
}

// Update the user.
$userUpdate = update_user($update);

// Write to log.
debug_log('Update user: ' . $userUpdate);

// Callback query received.
if (isset($update['callback_query'])) {
    // Init empty data array.
    $data = array();

    // Callback data found.
    if ($update['callback_query']['data']) {
        // Split callback data and assign to data array.
        $splitData = explode(':', $update['callback_query']['data']);
        $data['id']     = $splitData[0];
        $data['action'] = $splitData[1];
        $data['arg']    = $splitData[2];
    }

    // Write data to log.
    debug_log('DATA=');
    debug_log($data);

    // Set module path by sent action name.
    $module = 'modules/' . basename($data['action']) . '.php';

    // Write module to log.
    debug_log($module);

    // Check if the module file exists.
    if (file_exists($module)) {
        // Dynamically include module file and exit.
        include_once($module);
        exit();

    // Module file is missing.
    } else {
        // Write to log.
        debug_log('No action');
    }

// Inline query received.
} else if (isset($update['inline_query'])) {
    // List polls and exit.
    raid_list($update);
    exit();

// Location received.
} else if (isset($update['message']['location'])) {
    // Create raid and exit.
    include_once('modules/raid_create.php');
    exit();

// Message is required to check for commands.
} else if (isset($update['message'])) {
    // Check message text for a leading slash.
    if (substr($update['message']['text'], 0, 1) == '/') {
        // Get command name.
        $com = strtolower(str_replace('/', '', str_replace(BOT_NAME, '', explode(' ', $update['message']['text'])[0])));

        // Set command path.
        $command = 'commands/' . basename($com) . '.php';

        // Write to log.
        debug_log($command);

        // Check if command file exits.
        if (file_exists($command)) {
            // Dynamically include command file and exit.
            include_once($command);
            exit();
        }

        // Echo bot response.
        sendMessage($update['message']['chat']['id'], '<b>Bitte sende mir zuerst einen Standort.</b>');
    }
}

