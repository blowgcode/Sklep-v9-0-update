<?php

/******************************************************************************/
/******************************************************************************/

class CHBSGeoCoding
{
	/**************************************************************************/

	function __construct()
	{
		
	}
	
	/**************************************************************************/
	
	function getCountryCode($lat,$lng)
	{
		$url='https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key='.CHBSOption::getOption('google_map_api_key');
		
		if(($countryCode=$this->getCountryCodeGlobal($lat,$lng))!==false) return($countryCode);
		
		if(($content=file_get_contents($url))===false) return(false);
		
		$document=json_decode($content);
		
		$LogManager=new CHBSLogManager();
		$LogManager->add('geolocation',1,print_r($document,true));   
				
		if($document->status!='OK') return(false);
		
		foreach($document->results as $result)
		{
			foreach($result->{'address_components'} as $component)
			{
				if(in_array('country',$component->types))
				{
					$this->setCountryCodeGlobal($lat,$lng,$component->{'short_name'});
					return($component->{'short_name'});
				}
			}
		}

		return(false);
	}
	
	/**************************************************************************/
	
	function getCountryCodeGlobal($lat,$lng)
	{
		global $chbsGlobalData;	
		
		$lat=strval($lat);
		$lng=strval($lng);	
		
		if(!is_array($chbsGlobalData['geocoding_country'])) return(false);
		
		if(!isset($chbsGlobalData['geocoding_country'][$lat])) return(false);
		
		if(!isset($chbsGlobalData['geocoding_country'][$lat][$lng])) return(false);
		
		return($chbsGlobalData['geocoding_country'][$lat][$lng]['country_code']);
	}
	
	/**************************************************************************/
	
	function setCountryCodeGlobal($lat,$lng,$code)
	{
		global $chbsGlobalData;	
		
		$lat=strval($lat);
		$lng=strval($lng);		
		
		$chbsGlobalData['geocoding_country'][$lat][$lng]['country_code']=$code;
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/