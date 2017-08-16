<?php
/**
 * Get last insert id from db.
 * @return mixed
 */
function my_insert_id() {
	global $db;

	return $db->insert_id;
}

/**
 * Get db query.
 * @param $query
 * @return bool|mysqli_result
 */
function my_query($query) {
	global $db;

	debug_log($query, '?');

	$res = $db->query($query);

	if ($db->error) {
		debug_log($db->error, '!');
	}

	return $res;
}

/**
 * Write debug log.
 * @param $val
 * @param string $type
 */
function debug_log($val, $type = '*') {
    // Write to log only if debug is enabled.
    if (DEBUG === true) {

        $date = @date('Y-m-d H:i:s');
        $usec = microtime(true);
        $date = $date . '.' . str_pad(substr($usec, 11, 4), 4, '0', STR_PAD_RIGHT);

        $bt = debug_backtrace();
        $bl = '';

        while ($btl = array_shift($bt)) {
            if ($btl['function'] == __FUNCTION__) continue;
            $bl = '[' . basename($btl['file']) . ':' . $btl['line'] . '] ';
            break;
        }

        if (gettype($val) != 'string') $val = var_export($val, 1);
        $rows = explode("\n", $val);
        foreach ($rows as $v) {
            error_log('[' . $date . '][' . getmypid() . '] ' . $bl . $type . ' ' . $v . "\n", 3, CONFIG_LOGFILE);
        }
    }
}