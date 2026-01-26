<?php

/******************************************************************************/
/******************************************************************************/

class CHBSCountry
{
	/**************************************************************************/

	function __construct()
	{
		$this->country=CHBSGlobalData::setGlobalData('country',array($this,'init'));
	}
	
	/**************************************************************************/

	function init()
	{
		$country=array
		(
			
			'PL'=>array(__('Poland','chauffeur-booking-system')),
			'CZ'=>array(__('Czech Republic','chauffeur-booking-system')),
			'AT'=>array(__('Austria','chauffeur-booking-system')),			
			'DE'=>array(__('Niemcy','chauffeur-booking-system'))			
		); 
		
		return($country);
	}
	
	/**************************************************************************/
	
	function getCountry($country=null)
	{
		if(is_null($country)) return($this->country);
		else return($this->country[$country]);
	}
	
	/**************************************************************************/
	
	function isCountry($index)
	{
		return(array_key_exists($index,$this->country));
	}
	
	/**************************************************************************/
	
	function getCountryName($index)
	{
		return($this->country[$index][0]);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/