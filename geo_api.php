<?php
/**
 * Get timezone.
 * @param $lat
 * @param $lon
 * @param bool $time
 * @return bool
 */
function get_timezone($lat, $lon, $time = false) {
    if (!$time) $time = time();
    $url = 'https://maps.googleapis.com/maps/api/timezone/json?location='.$lat.','.$lon.'&timestamp='.$time.'&key='.TZ_API_KEY;
    echo $url;
    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    debug_log($url,'T>');
    $json_response = curl_exec($curl);
    debug_log($json_response,'<T');
    $response = json_decode($json_response,true);
    if ($response['status']=='OK') {
        return $response['timeZoneId'];
    } else {
        debug_log($json_response,'!');
        return false;
    }
}

/**
 * Get address by lat / lon.
 * @param $lat
 * @param $lon
 * @return bool|string
 */
function get_address($lat, $lon) {
    // Set maps geocode url.
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon;

    $googleApiKey = TZ_API_KEY;

    if (!empty($googleApiKey)) {
        $url .= '&key=' . $googleApiKey;
    }

    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    debug_log($url, 'G>');

    $json_response = curl_exec($curl);

    debug_log($json_response, '<G');

    $response = json_decode($json_response, true);

    if ($response['status'] == 'OK') {
        $result = '';
        $type = '';

        foreach ($response['results'] as $v) {
            if ($v['formatted_address'] && !$result) {
                $result = $v['formatted_address'];
                $type   = $v['geometry']['location_type'];
            }
            if ($type=='ROOFTOP') return $result;
        }

        // Return the result.
        return $result;

    } else {
        // Write to log.
        debug_log($json_response, '!');

        return false;
    }
}
