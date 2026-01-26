<?php

/******************************************************************************/
/******************************************************************************/

class CHBSLength
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->unit=array
		(
			1=>array(__('Kilometers','chauffeur-booking-system'),__('km','chauffeur-booking-system')),
			2=>array(__('Miles','chauffeur-booking-system'),__('mi','chauffeur-booking-system'))
		);
	}
	
	/**************************************************************************/
	
	function getUnit($unit=null)
	{
		if($unit===null)
			return($this->unit);
		else return($this->unit[$unit]);
	}
	
	/**************************************************************************/
	
	function getUnitName($unit)
	{
		if(!$this->isUnit($unit)) return($this->unit[1][0]);
		return($this->unit[$unit][0]);
	}
	
	/**************************************************************************/
	
	function getUnitShortName($unit)
	{
		if(!$this->isUnit($unit)) return($this->unit[1][1]);
		return($this->unit[$unit][1]);
	}	
	
	/**************************************************************************/
	
	function isUnit($unit)
	{
		return(array_key_exists($unit,$this->getUnit()));
	}
	
	/**************************************************************************/
	
	function format($value,$unit=-1)
	{
		if($unit==-1) $unit=CHBSOption::getOption('length_unit');
		
		if(!$this->isUnit($unit)) $unit=1;
		
		if($unit==2) $value=round($this->convertUnit($value),1);
		
		$value=$value.' '.$this->unit[$unit][1];
		return($value);
	}
	
	/**************************************************************************/
	
	function convertUnit($value,$from=1,$to=2)
	{
		if(($from==1) && ($to=2))
		{
			$value/=1.609344;
		}
		else if(($from==2) && ($to=1))
		{
			$value*=1.609344;
		}
		
		return(round($value,1));
	}
	
	/**************************************************************************/
	
	function label($unit=-1,$type=1)
	{
		$label=null;
		
		if($unit==-1) $unit=CHBSOption::getOption('length_unit');
		
		if(!$this->isUnit($unit)) $unit=1;		
		
		switch($type)
		{
			case 1:
				
				$label=$unit==2 ? esc_html__('Price per mile','chauffeur-booking-system') : esc_html__('Price per kilometer','chauffeur-booking-system');
				
			break;
		
			case 2:
				
				$label=$unit==2 ? esc_html__('Distance in miles','chauffeur-booking-system') : esc_html__('Distance in kilometers','chauffeur-booking-system');
				
			break;
		
			case 3:
				
				$label=$unit==2 ? esc_html__('Per mile','chauffeur-booking-system') : esc_html__('Per kilometer','chauffeur-booking-system');
				
			break;
		
			case 4:
				
				$label=$unit==2 ? esc_html__('Per mile (return)','chauffeur-booking-system') : esc_html__('Per kilometer (return)','chauffeur-booking-system');
				
			break;
		
			case 5:
				
				$label=$unit==2 ? esc_html__('Per mile (return, new ride)','chauffeur-booking-system') : esc_html__('Per kilometer (return, new ride)','chauffeur-booking-system');
				
			break;
		}

		return($label);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/