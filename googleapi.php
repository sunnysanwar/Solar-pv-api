<?php
require_once('HttpClient.php');
require_once('LocAddress.php');

class googleapi
{
public  $api_key;	
public $httplient;

	
	function __construct() {
	//$configs = include 'config.php';
	//echo $configs;
	$ini = parse_ini_file('config.ini');
	//print_r($ini);
    $this->api_key = $ini['GoogleMapKey'];	
	$this->httplient=new HttpClient();
  }
  function getSolar($latitude,$longitude)
{
	$url = "https://solar.googleapis.com/v1/buildingInsights:findClosest?key={$this->api_key}&location.latitude={$latitude}&location.longitude={$longitude}&requiredQuality=HIGH";
	$json = file_get_contents($url);
	return $json;
	
}
function getAirQuality($latitude,$longitude)
{
	$url = "https://airquality.googleapis.com/v1/currentConditions:lookup?key={$this->api_key}";
	
	$req = '{
		"location": {
		  "latitude": '.$latitude.',
		  "longitude": '.$longitude.'
		},
		"extra_computations": [
		"HEALTH_RECOMMENDATIONS",
		"DOMINANT_POLLUTANT_CONCENTRATION",
		"POLLUTANT_CONCENTRATION",
		"LOCAL_AQI",
		"POLLUTANT_ADDITIONAL_INFO"
		],
		"language_code": "en"
		}';
		$options = array(
			'http' => array(
				'method'  => 'POST',
				'content' => $req ,
				'header'=>  "Content-Type: application/json\r\n" .
							"Accept: application/json\r\n" .
							"Cookie: NAME=VaLuE"
				)
			);
		$opts=stream_context_create($options);
		return $this->httplient->CallAPI($url,$opts );

}

function getDirection($startaddress,$waypoints,$endaddress,$output,$allowalternateroute){ 
	$url="https://maps.googleapis.com/maps/api/directions/json?key=". $this->api_key;	
	$url = $url."&origin=".urlencode($startaddress)."&destination=".urlencode($endaddress);
	if(!empty($waypoints))
	{
		$url=$url."&waypoints=optimize:true|".urlencode($waypoints)."|";
	}
	if($allowalternateroute)
	{
		$url=$url."&alternatives=true";
	}
	return $this->getResult($url,$output);
}
function getDistance($startaddress,$endaddress,$departuretime,$trafficmodel,$output='object'){ 
	$url="https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&key=". $this->api_key;	
	$url = $url."&origins=".urlencode($startaddress)."&destinations=".urlencode($endaddress);
	if($departuretime!='')
		$url = $url."&departuretime=".urlencode($departuretime);
	if($trafficmodel!='')
		$url = $url."&trafficmodel=".urlencode($trafficmodel);
	
	return $this->getResult($url,$output);
}
function getGeoPosition($address,$output='object'){    
	$url="https://maps.google.com/maps/api/geocode/json?sensor=false&key=". $this->api_key;
	$url = $url . "&address=" . urlencode($address);
	//print_r($url);
	return $this->getResult($url,$output);
}
function getResult($url,$output)
{
	$json=$this->httplient->CallGetJson($url);
	//print_r($json);
	if ($output=="object")
		return json_decode($json, TRUE);
	else
		return $json;
}
function getDistanceDuration($startaddress,$endaddress,$departuretime,$trafficmodel)
{
	$data=$this->getDistance($startaddress,$endaddress,$departuretime,$trafficmodel); 
	$res=array();
	if($data!=null)
	{
		$elements=$data['rows'][0]['elements'];
		foreach($elements as $e)
		{
			$dist=$e['distance']['value'];
			$dur= $e['duration']['value'];		
			array_push($res,[$dist,$dur]) ;
		}
		
	}
	return $res;

}
function getGeoCode($address)
{
	$data=$this->getGeoPosition($address->StreetAddress);
	if($data==null)
	{
		throw new Exception('Invalid address('.$address.')');
		return null;
	}
	else if($data['status']!='OK')
	{
		throw new Exception('Invalid address('.$address.')');
		return null;
	}
	//$addr=new LocAddress(0,$address);  
	if($data!=null)
	{
		$lat    = $data['results'][0]['geometry']['location']['lat'] ?? '';
		$long   = $data['results'][0]['geometry']['location']['lng'] ?? '';	
				
		$address->SetGPS($lat,$long); 
	}
	return $address;

}
function  AddressToGeoCode($address)
{
	$gpsAddr=$this->getGeoCode($address);
	return $gpsAddr;	
}
/*
function  AddressesToGeoCodes($address)
{
	$addrArray=array();	
	if(is_array($address))
	{
		foreach ($address as $addr) {
			
			$gpsAddr=$this->getGeoCode($addr);
			array_push($addrArray, $gpsAddr);
		}
		
	}
	return $addrArray;
}
*/
function  AddressesToGeoCodes($address)
{
	$addrArray=array();	
	if(is_array($address))
	{
		foreach ($address as $addr) {
			
			$gpsAddr=$this->getGeoCode($addr);
			array_push($addrArray, $gpsAddr);
		}
		
	}
	return $addrArray;
}
/*
function  convertToGeoCode($startaddress,$destinations)
{
	
	$StartAddress=$this->GoogleApi->getGeoCode($startaddress);
	print_r(json_encode($StartAddress));
	if($destinations!='')
	{
		$destArray=explode('|', $destinations);
	
		$Destinations=array();
		
		foreach ($destArray as $dest) {
			
			$gpsAddr=$this->GoogleApi->getGeoCode($dest);
			

			array_push($Destinations, $gpsAddr);            
			
		}
		 print_r(json_encode($Destinations));
	}   
   
}
*/
}
