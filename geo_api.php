<?php
/**
 * Get address by lat / lon.
 * @param $lat
 * @param $lon
 * @return bool|string
 */
function get_address($lat, $lon)
{
    // Init defaults.
    $location = array();
    $location['street'] = '';
    $location['street_number'] = '';
    $location['postal_code'] = '';
    $location['district'] = '';

    // Set maps geocode url.
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $lat . ',' . $lon . '&language=' . strtolower( LANGUAGE );

    $googleApiKey = GOOGLE_API_KEY;

    // Append google api key if exists.
    if (!empty($googleApiKey)) {
        $url .= '&key=' . $googleApiKey;
    }

    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // Use Proxyserver for curl if configured
    if (CURL_USEPROXY == true) {
        curl_setopt($curl, CURLOPT_PROXY, CURL_PROXYSERVER);
    }

    debug_log($url, 'G>');

    $json_response = curl_exec($curl);

    debug_log($json_response, '<G');

    // Get response object from reverse method using Google Maps API.
    $data = json_decode($json_response);

    // Received valid data from Google.
    if (!empty($data) && !empty($data->status) && $data->status == 'OK' && !empty($data->results)) {

        // Init vars.
        $locality = '';
        $sublocalityLv2 = '';
        $sublocality = '';

        // Iterate each result.
        foreach ($data->results as $result) {

            // Check for address components.
            if (!empty($result->address_components)) {
                // Iterate each address component.
                foreach ($result->address_components as $address_component) {

                    // Street found.
                    if (in_array('route', $address_component->types) && !empty($address_component->long_name)) {
                        // Set street by first found.
                        $location['street'] = empty($location['street']) ? $address_component->long_name : $location['street'];
                    }

                    // Street number found.
                    if (in_array('street_number', $address_component->types) && !empty($address_component->long_name)) {
                        // Set street by first found.
                        $location['street_number'] = empty($location['street_number']) ? $address_component->long_name : $location['street_number'];
                    }

                    // Postal code found.
                    if (in_array('postal_code', $address_component->types) && !empty($address_component->long_name)) {
                        // Set street by first found.
                        $location['postal_code'] = empty($location['postal_code']) ? $address_component->long_name : $location['postal_code'];
                    }

                    // Sublocality level2 found.
                    if (in_array('sublocality_level_2', $address_component->types) && !empty($address_component->long_name)) {
                        // Set sublocality level 2 by first found.
                        $sublocalityLv2 = empty($sublocalityLv2) ? $address_component->long_name : $sublocalityLv2;
                    }

                    // Sublocality found.
                    if (in_array('sublocality', $address_component->types) && !empty($address_component->long_name)) {
                        // Set sublocality by first found.
                        $sublocality = empty($sublocality) ? $address_component->long_name : $sublocality;
                    }

                    // Locality found.
                    if (in_array('locality', $address_component->types) && !empty($address_component->long_name)) {
                        // Set sublocality by first found.
                        $locality = empty($sublocality) ? $address_component->long_name : $sublocality;
                    }
                }
            }
            break;
        }

        // Set district by priority.
        if (!empty($sublocalityLv2)) {
            $location['district'] = $sublocalityLv2;

        } else if ($sublocality) {
            $location['district'] = $sublocality;

        } else if ($locality) {
            $location['district'] = $locality;
        }

        // Rename street responses.
        switch ($location['street']) {
            case 'Unnamed Road':
                $location['street'] = getTranslation('forest');
                break;
        }

        // Return the location array.
        return $location;

    } else {
        return false;
    }
}
