<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingGratuity
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->type=array
		(
			1=>array(__('Fixed','chauffeur-booking-system')),
			2=>array(__('Percentage','chauffeur-booking-system'))	  
		);
	}
	
	/**************************************************************************/
	
	function getType()
	{
		return($this->type);
	}
	
	/**************************************************************************/
	
	function isType($type)
	{
		return(array_key_exists($type,$this->type));
	}
	  
	/**************************************************************************/
	
	function isEnable($bookingFormMeta)
	{
		return(((int)$bookingFormMeta['price_hide']===0) && ((int)$bookingFormMeta['gratuity_enable']===1));
	}
	
	/**************************************************************************/
	
	function isEnableCustomer($bookingFormMeta)
	{
		return(($this->isEnable($bookingFormMeta)) && ((int)$bookingFormMeta['gratuity_customer_enable']===1));
	}
	
	/**************************************************************************/
	
	function calculateBookingGratuity($bookingForm,$priceTotalSum)
	{
		$gratuity=0.00;
		
		$Validation=new CHBSValidation();
		
		if($this->isEnable($bookingForm['meta']))
		{
			if((int)$bookingForm['meta']['gratuity_admin_type']===1)
			{
				$gratuity=CHBSPrice::numberFormat($bookingForm['meta']['gratuity_admin_value']);
			}
			else
			{
				$gratuity=CHBSPrice::numberFormat($priceTotalSum*($bookingForm['meta']['gratuity_admin_value']/100));
			}
			
			if($this->isEnableCustomer($bookingForm['meta']))
			{
				$type=1;
				$value=CHBSHelper::getPostValue('gratuity_customer_value');
						
				if($Validation->isEmpty($value))
				{
					if($bookingForm['booking_edit']->isBookingEdit())
					{
						$value=$bookingForm['booking_edit']->getFieldValue(null,array('meta','gratuity_value'));
					}
				}
				
				if($Validation->isEmpty($value))
				{
					$value=$gratuity;
					$type=(int)$bookingForm['meta']['gratuity_admin_type'];
				}
				else
				{
					if(preg_match('/\%$/',$value))
					{
						$type=2;
						$value=preg_replace('/\%/','',$value);
					}
					
					if($Validation->isPrice($value))
					{
						if(($type==2) && (in_array(2,$bookingForm['meta']['gratuity_customer_type'])))
						{
							$gratuity=$priceTotalSum*($value/100);
						}
						elseif(in_array(1,$bookingForm['meta']['gratuity_customer_type']))
						{
							$gratuity=$value;
						}
					}
				}
			}
		}
				
		return(CHBSPrice::numberFormat($gratuity));
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/