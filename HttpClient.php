<?php
class httpclient
{
function CallGetJson($url)
{	
    $json = file_get_contents($url);

    $data = json_decode($json, TRUE);
     if($data['status']=="OK"){
		return $json;
      //return $data['results'];
    }
    // else
    // {
      // print_r($data);
    // }
}
function CallAPI($url,$context)
{	
    $json = file_get_contents($url,false,$context);

    $data = json_decode($json, TRUE);
    //print_r($data);
    // if($data['status']=="OK"){
		return $json;
      //return $data['results'];
    
    // else
    // {
      // print_r($data);
    // }
}
}
?>