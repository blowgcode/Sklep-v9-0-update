<?php

/******************************************************************************/
/******************************************************************************/

class CHBSWebHook
{
	/**************************************************************************/
	
	function __construct()
	{		
		
	}
	
	/**************************************************************************/
	
	function afterSentBooking($bookingId)
	{
		$Booking=new CHBSBooking();
		$Validation=new CHBSValidation();
		
		/***/
		
		if((int)CHBSOption::getOption('webhook_after_sent_booking_enable')!==1) return(false);
		
		if($Validation->isEmpty(CHBSOption::getOption('webhook_after_sent_booking_url_address'))) return(false);
		
		if(($booking=$Booking->getBooking($bookingId))===false) return(false);
		
		$booking['billing']=$Booking->createBilling($bookingId);
		
		$ch=curl_init(CHBSOption::getOption('webhook_after_sent_booking_url_address'));
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