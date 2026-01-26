<?php

/******************************************************************************/
/******************************************************************************/

class CHBSServiceType
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->serviceType=array
		(
			1=>array(__('Distance','chauffeur-booking-system')),
			2=>array(__('Hourly','chauffeur-booking-system')),
			3=>array(__('Flat rate ','chauffeur-booking-system'))
		);
	}
	
	/**************************************************************************/
	
	function getServiceType($serviceType=null)
	{
		if($serviceType===null) return($this->serviceType);
		else return($this->serviceType[$serviceType]);
	}
	
	/**************************************************************************/
	
	function isServiceType($serviceType)
	{
		return(array_key_exists($serviceType,$this->serviceType));
	}
	
	/**************************************************************************/
	
	function getServiceTypeName($serviceType)
	{
		return($this->serviceType[$serviceType][0]);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/






