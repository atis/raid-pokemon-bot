<?php
/**
 * Get address by lat / lon.
 * @param $lat
 * @param $lon
 * @return bool|string
 */
function get_address($lat, $lon) {
    // Set maps geocode url.
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon;

    $googleApiKey = GOOGLE_API_KEY;

    // Append google api key if exists.
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
