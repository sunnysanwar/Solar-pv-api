<?php
require_once('GoogleAPI.php');
function GenerateCSVlineFromArray($array)
{
    $line = "";
    foreach ($array as $value) {
        $line .= $value[1] . ",";
    }
    //cut last comma
    $line = substr($line, 0, strlen($line) - 1);
    return $line;
}
function getPollutionData($res)
{
    $dPoints = ['so2', 'no2', 'co', 'o3', 'pm25', 'pm10'];
    $apiData = array();

    for ($i = 0; $i < count($dPoints); $i++) {
        $x = strval($dPoints[$i]);
        $v = "";
        for ($p = 0; $p < count($res->pollutants); $p++) {
            $pol = $res->pollutants[$p];
            if ($pol->code == $x) {
                $v = $pol->concentration->value;
                array_push($apiData, [$x, $v]);
            }
        }
    }
    return $apiData;
}
function getSolarData($res)
{
    $dPoints = ['so2', 'no2', 'co', 'o3', 'pm25', 'pm10'];
    $apiData = array();

    for ($i = 0; $i < count($dPoints); $i++) {
        $x = strval($dPoints[$i]);
        $v = "";
        for ($p = 0; $p < count($res->pollutants); $p++) {
            $pol = $res->pollutants[$p];
            if ($pol->code == $x) {
                $v = $pol->concentration->value;
                array_push($apiData, [$x, $v]);
            }
        }
    }
    return $apiData;
}
function getSolarApiData($res)
{
    $apiData = array();
    if ($res != null) {
        array_push($apiData,['carbonOffsetFactorKgPerMwh',$res->solarPotential->carbonOffsetFactorKgPerMwh]);
        array_push($apiData, ['panelCapacityWatts',$res->solarPotential->panelCapacityWatts]);
        array_push($apiData, ['maxArrayAreaMeters2',$res->solarPotential->maxArrayAreaMeters2]);
        array_push($apiData, ['maxSunshineHoursPerYear',$res->solarPotential->maxSunshineHoursPerYear]);
        array_push($apiData, ['carbonOffsetFactorKgPerMwh',$res->solarPotential->carbonOffsetFactorKgPerMwh]);
        if (count($res->solarPotential->solarPanelConfigs) > 0) {
            array_push($apiData,['yearlyEnergyDcKwh',$res->solarPotential->solarPanelConfigs[0]->yearlyEnergyDcKwh]);
            array_push($apiData,['panelsCount', $res->solarPotential->solarPanelConfigs[0]->panelsCount]);
        }
    }
    return $apiData;
}
$google = new googleapi();
$myLocations = array();
$csv = '';

$apiToProcess = 'Solar';
if (isset($_GET['method']))
    $apiToProcess = $_GET["method"];

if (isset($_POST['latitude']) && isset($_POST['longitude']))
array_push($myLocations,[$_POST['latitude'],$_POST['longitude']]);

if(count($myLocations)==0)
{
    echo 'Please enter lat/lng';
    return;
}


//Air Quality
//array_push($myLocations,[55.3286193,-131.5937879]);
//array_push($myLocations,[65.4368364,	-165.3121481]);

//Solar Sample Locations
//array_push($myLocations, [40.6955389, -73.78149]);
//array_push($myLocations, [40.6776634, -73.9102104]);


//Make CSV header row
if ($apiToProcess == 'AirQuality') {
    $csv = 'so2,no2,co,o3,pm25,pm10';
} else {
    $csv = 'maxArrayPanelsCount,panelCapacityWatts,maxArrayAreaMeters2,maxSunshineHoursPerYear,carbonOffsetFactorKgPerMwh,panelsCount,yearlyEnergyDcKwhso2';
}
$csv = $csv . "\r\n";

for ($l = 0; $l < count($myLocations); $l++) {
    $lat = $myLocations[$l][0];
    $lng = $myLocations[$l][1];
    if ($lat != null && $lng != null) {
        $result = null;
        if ($apiToProcess == 'AirQuality') {
            $res = $google->getAirQuality($lat, $lng);
            $jsonData = json_decode($res);
            $result = getPollutionData($jsonData);
        } else {
            $res = $google->getSolar($lat, $lng);
            if($res!=null)
            {    
                $jsonData = json_decode($res);
                if($jsonData!=null)
                    $result = getSolarApiData($jsonData);
            }
        }

        $lineCSV = GenerateCSVlineFromArray($result);
        $csv = $csv . $lineCSV . "\r\n";
    }
}
$myfile = fopen($apiToProcess . ".txt", "w") or die("Unable to open file!");
fwrite($myfile, $csv);
fclose($myfile);

// $fp = fopen('file.csv', 'w');
// foreach ($list as $fields) {
//     fputcsv($fp, $fields);
// }
// fclose($fp);

echo $csv;//GenerateCSVlineFromArray($result);
