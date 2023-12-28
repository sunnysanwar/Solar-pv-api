<?php
class LocAddress
    {
        public $StreetAddress;
        public $Latitude;
        public $Longitude;
        public $Distance;
        public $DurationSec;
        public $StartAddress;
        public $Id;

        function __construct($id,$address)
        {
            $this->Id =$id;
            $this->StreetAddress =$address;
        }
        
        function  SetGPS($lat,$lng)
        {
            $this->Latitude = $lat;
            $this->Longitude = $lng;
        }
        
        function  SetAddress($address,$latitude,$longitude)
        {
            $this->StreetAddress = $address;
            $this->Latitude = $latitude;
            $this->Longitude = $longitude;
        }
        function SetTravelDistance($distance,$durationsec,$startaddress)
        {
            $this->Distance=$distance;
            $this->DurationSec=$durationsec;
            $this->StartAddress=$startaddress;
        }
    }
    ?>