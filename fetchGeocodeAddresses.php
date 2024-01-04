<?php
if (isset($_GET['value'])) {
    $config = parse_ini_file('config.ini');
    $value = urlencode($_GET['value']);
    $url = "https://api.mapbox.com/geocoding/v5/mapbox.places/{$value}.json?country=US&access_token={$config['MapKey']}";

    $response = file_get_contents($url);
    header('Content-Type: application/json');
    echo $response;
}