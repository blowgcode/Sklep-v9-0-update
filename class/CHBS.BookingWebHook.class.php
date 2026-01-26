<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingWebHook
{
	/**************************************************************************/
	
	public $runEvent;
	
	/**************************************************************************/
	
	function __construct()
	{		
		$this->runEvent=array
		(
			'after_booking_send'=>array(__('Sending the booking','chauffeur-booking-system'))
		);
		
		$BookingStatus=new CHBSBookingStatus();
		
		$bookingStatusDictionary=$BookingStatus->getBookingStatus();
		
		foreach($bookingStatusDictionary as $index=>$value)
			$this->runEvent['after_booking_status_update_to_'.$index]=array(sprintf(esc_html__('Updating the booking status to "%s"','chauffeur-booking-system'),esc_html__($value[0],'chauffeur-booking-system')));
	}
	
	/**************************************************************************/
	
	function getRunEvent($event=null)
	{
		if(is_null($event)) return($this->runEvent);
		else return($this->runEvent[$event]);
	}
	
	/**************************************************************************/
	
	function isRunEvent($event)
	{
		return(array_key_exists($event,$this->getRunEvent()));
	}
	
	/**************************************************************************/
	
	function getDefaultRunEvent()
	{
		return('after_booking_send');
	}
	
	/**************************************************************************/
	
	function run($bookingId,$runEvent='after_booking_send')
	{
		$Booking=new CHBSBooking();
		$Validation=new CHBSValidation();
		
		/***/

		if((int)CHBSOption::getOption('webhook_booking_enable')!==1) return(false);

		if($Validation->isEmpty(CHBSOption::getOption('webhook_booking_url_address'))) return(false);

		if(($booking=$Booking->getBooking($bookingId))===false) return(false);

		/***/
		
		$b=array(false,false);
		
		$b[0]=($runEvent==='after_booking_send') && (CHBSOption::getOption('webhook_booking_run_event')=='after_booking_send');
		$b[1]=($runEvent==='after_booking_status_change') && (CHBSOption::getOption('webhook_booking_run_event')=='after_booking_status_update_to_'.$booking['meta']['booking_status_id']);
		
		if(!in_array(true,$b,true)) return(false);
		
		/***/
		
		$runNumber=0;
		$runNumberMax=(int)CHBSOption::getOption('webhook_booking_run_number');
		
		if($runNumberMax>0)
		{
			if(array_key_exists('webhook_booking_run_number',$booking['meta']))
				$runNumber=(int)$booking['meta']['webhook_booking_run_number'];
		
			if($runNumber>=$runNumberMax) return(false);
		}
		
		CHBSPostMeta::updatePostMeta($bookingId,'webhook_booking_run_number',($runNumber+1));
		
		/***/
		
		$booking['billing']=$Booking->createBilling($bookingId);
		
		$ch=curl_init(CHBSOption::getOption('webhook_booking_url_address'));
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($booking));
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type:application/json'));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

		curl_exec($ch);
		curl_close($ch);
		
		return(true);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/