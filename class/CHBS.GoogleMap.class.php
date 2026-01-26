<?php

/******************************************************************************/
/******************************************************************************/

class CHBSGoogleMap
{
	/**************************************************************************/
	
	public $position;
	public $routeAvoid;
	public $mapTypeControlId;
	public $mapTypeControlStyle;
	
	/**************************************************************************/

	function __construct()
	{
		$this->position=array
		(
			'TOP_CENTER'=>__('Top center','chauffeur-booking-system'),
			'TOP_LEFT'=>__('Top left','chauffeur-booking-system'),
			'TOP_RIGHT'=>__('Top right','chauffeur-booking-system'),
			'LEFT_TOP'=>__('Left top','chauffeur-booking-system'),
			'RIGHT_TOP'=>__('Right top','chauffeur-booking-system'),
			'LEFT_CENTER'=>__('Left center','chauffeur-booking-system'),
			'RIGHT_CENTER'=>__('Right center','chauffeur-booking-system'),
			'LEFT_BOTTOM'=>__('Left bottom','chauffeur-booking-system'),
			'RIGHT_BOTTOM'=>__('Right bottom','chauffeur-booking-system'),
			'BOTTOM_CENTER'=>__('Bottom center','chauffeur-booking-system'),
			'BOTTOM_LEFT'=>__('Bottom left','chauffeur-booking-system'),
			'BOTTOM_RIGHT'=>__('Bottom right','chauffeur-booking-system')	
		);
		
		$this->mapTypeControlId=array
		(
			'ROADMAP'=>__('Roadmap','chauffeur-booking-system'),
			'SATELLITE'=>__('Satellite','chauffeur-booking-system'),
			'HYBRID'=>__('Hybrid','chauffeur-booking-system'),
			'TERRAIN'=>__('Terrain','chauffeur-booking-system')
		);
		
		$this->mapTypeControlStyle=array
		(
			'DEFAULT'=>__('Default','chauffeur-booking-system'),
			'HORIZONTAL_BAR'=>__('Horizontal Bar','chauffeur-booking-system'),
			'DROPDOWN_MENU'=>__('Dropdown Menu','chauffeur-booking-system')
		);
		
		$this->routeAvoid=array
		(
			'tolls'=>__('Tolls','chauffeur-booking-system'),
			'highways'=>__('Highways','chauffeur-booking-system'),
			'ferries'=>__('Ferries','chauffeur-booking-system')
		);
	}
	
	/**************************************************************************/
	
	function getMapTypeControlStyle()
	{
		return($this->mapTypeControlStyle);
	}
   
	 /**************************************************************************/
	
	function getPosition()
	{
		return($this->position);
	}
	
	/**************************************************************************/
	
	function getMapTypeControlId()
	{
		return($this->mapTypeControlId);
	}
	
	/**************************************************************************/
	
	function getRouteAvoid()
	{
		return($this->routeAvoid);
	}
	
	/**************************************************************************/
	
	function getDistance($coordinate)
	{
		$LogManager=new CHBSLogManager();
		
		$url='https://maps.googleapis.com/maps/api/distancematrix/json?origins='.join(',',$coordinate[0]).'&destinations='.join(',',$coordinate[1]).'&key='.CHBSOption::getOption('google_map_api_key');
		
		if(($data=file_get_contents($url))===false) 
		{	
			$LogManager->add('google_map',1,print_r($data,true));  			
			return(0);
		}

		if(is_null($data=json_decode($data))) 
		{
			$LogManager->add('google_map',1,print_r($data,true));   
			return(0);
		}

		if(property_exists($data,'error_message')) 
		{			
			$LogManager->add('google_map',1,print_r($data,true));   
			return(0);
		}		
		
		return($data->{'rows'}[0]->{'elements'}[0]->{'distance'}->{'value'});
	}
	
	/**************************************************************************/
	
	function getRouteURLAddress($coordinate)
	{
		$Validation=new CHBSValidation();
		
		$url=null;
		
		foreach($coordinate as $value)
		{
			if($Validation->isNotEmpty($url)) $url.='/';
			$url.=$value['lat'].','.$value['lng'];
		}
		
		return('https://www.google.com/maps/dir/'.$url);
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/