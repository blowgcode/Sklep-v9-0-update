<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingEdit
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->booking=false;
		$this->bookingEditId=-1;
		$this->bookingEditIdHash=null;
	}
	
	/**************************************************************************/
	
	function isBookingEdit()
	{
		return($this->booking===false ? false : true);
	}
	
	/**************************************************************************/
	
	function setBooking()
	{
		$Booking=new CHBSBooking();
		
		$bookingId=$this->checkBookingId();
		
		if($bookingId<=0) return(false);
		
		if(($booking=$Booking->getBooking($bookingId))===false) return(false);
		
		/***/
		
		$i=0;
		$j=0;
		
		foreach($booking['meta']['coordinate'] as $index=>$value)
		{
			$type=null;
			
			if($i===0) $type='pickup';
			else if($i==count($booking['meta']['coordinate'])-1) $type='dropoff';
			else $type='waypoint'; 
			
			$address=CHBSHelper::getAddress($value);
			$coordinate=array
			(
				'lat'=>$value['lat'],
				'lng'=>$value['lng'],
				'address'=>$address,
				'zip_code'=>$value['zip_code']
			);
			
			if(in_array($type,array('pickup','dropoff')))
			{
				$booking['_meta'][$type.'_location']['address']=$address;
				$booking['_meta'][$type.'_location']['coordinate']=$coordinate;	
			}
			else
			{
				$booking['_meta'][$type.'_location'][$j]['address']=$address;
				$booking['_meta'][$type.'_location'][$j]['coordinate']=json_encode($coordinate,JSON_UNESCAPED_UNICODE);
				$j++;
			}
			
			$i++;
		}

		/***/
		
		$booking['_meta']['booking_extra_id']=null;
		foreach($booking['meta']['booking_extra'] as $value)
		{
			if(!is_null($booking['_meta']['booking_extra_id'])) $booking['_meta']['booking_extra_id'].=',';
			$booking['_meta']['booking_extra_id'].=$value['id'];
		}
		
		/***/
		
		$booking['_meta']['user_data']=$booking['meta'];
		
		/***/
		
		$booking['_meta']['extra_time_value']=$booking['meta']['extra_time_value']/60;
		
		$this->booking['booking']=$booking;
		
		$this->bookingEditId=$booking['post']->ID;
		$this->bookingEditIdHash=$this->getBookingIdHash($this->bookingEditId);
	}
	
	/**************************************************************************/
	
	function checkBookingId()
	{
		$Validation=new CHBSValidation();
		
		$bookingEditId=CHBSHelper::getGetValue('booking_edit_id',true);
		
		if($Validation->isEmpty($bookingEditId))
			$bookingEditId=CHBSHelper::getPostValue('booking_edit_id',true);
		
		if($Validation->isEmpty($bookingEditId)) return(-1);
		
		list(,$bookingId)=preg_split('/_/',$bookingEditId);
		
		if($this->getBookingIdHash($bookingId)!=$bookingEditId) return(-1);
		
		return($bookingId);
	}
	
	/**************************************************************************/
	
	function getBookingIdHash($bookingId)
	{
		return(strtoupper(md5(CHBSOption::getOption('salt')).'_'.$bookingId));
	}
	
	/**************************************************************************/
	
	function getFieldValue($fieldNameForm,$fieldNameBooking,$serviceTypeId=-1,$defaultValue=null,$type='field')
	{
		if($this->isBookingEdit()===false)
		{
			if($type==='coordinate') return(CHBSRequestData::getCoordinateFromWidget($serviceTypeId,$fieldNameForm));
			else return(CHBSRequestData::getFromWidget($serviceTypeId,$fieldNameForm,$defaultValue));
		}
		else
		{
			if(($serviceTypeId===-1) || ((int)$this->booking['booking']['meta']['service_type_id']===(int)$serviceTypeId))
			{
				$count=count($fieldNameBooking);
				$booking=$this->booking['booking'];
				
				for($i=0;$i<$count;$i++)
					$booking=$booking[$fieldNameBooking[$i]];
				
				if($type=='field') return($booking);
				else return(json_encode($booking,JSON_UNESCAPED_UNICODE));
			}
		}
	}
	
	/**************************************************************************/
	
	function getBookingFormElementValue($id)
	{
		if(!$this->isBookingEdit()) return(null);
		
		if(!array_key_exists('form_element_field',$this->booking['booking']['meta'])) return(null);
		
		foreach($this->booking['booking']['meta']['form_element_field'] as $value)
		{
			if($value['id']===$id) return($value['value']);
		}
		
		return(null);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/

