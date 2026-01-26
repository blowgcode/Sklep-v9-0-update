<?php

/******************************************************************************/
/******************************************************************************/

class CHBSGoogleCalendar
{
	/**************************************************************************/
	
	function __construct()
	{
		$BookingStatus=new CHBSBookingStatus();
		
		$bookingStatus=$BookingStatus->getBookingStatus();
		
		$this->addEventAction=array
		(
			'after_booking_send'=>array(__('Sending the booking','chauffeur-booking-system'))
		);
		
		foreach($bookingStatus as $index=>$value)
			$this->addEventAction['after_booking_status_update_to_'.$index]=array(sprintf(esc_html__('Updating the booking status to "%s"','chauffeur-booking-system'),esc_html__($value[0],'chauffeur-booking-system')));
	}
	
	/**************************************************************************/
	
	function getAddEventAction($action=null)
	{
		if(is_null($action)) return($this->addEventAction);
		else return($this->addEventAction[$action]);
	}
	
	/**************************************************************************/
	
	function isAddEventAction($action)
	{
		return(array_key_exists($action,$this->getAddEventAction()));
	}
	
	/**************************************************************************/
	
	function getDefaultAddEventAction()
	{
		return('after_booking_send');
	}
	
	/**************************************************************************/
	
	function sendBooking($bookingId,$bookingReturn=false,$action='after_booking_send')
	{
		$Booking=new CHBSBooking();
		$Validation=new CHBSValidation();
		$BookingForm=new CHBSBookingForm();
		$BookingHelper=new CHBSBookingHelper();
		
		/***/
		
		if(($booking=$Booking->getBooking($bookingId))===false) return(false);
		
		if(((int)$booking['meta']['google_calendar_add_event']===1) && (!$bookingReturn)) return(false);
		
		$bookingFormId=$booking['meta']['booking_form_id'];
		
		$dictionary=$BookingForm->getDictionary(array('booking_form_id'=>$bookingFormId));
		if(count($dictionary)!=1) return(false);
		
		$bookingForm=$dictionary[$bookingFormId];
		
		if((int)$bookingForm['meta']['google_calendar_enable']!==1) return(false);
		
		if(($Validation->isEmpty($bookingForm['meta']['google_calendar_id'])) || ($Validation->isEmpty($bookingForm['meta']['google_calendar_settings']))) return(false);
		
		/***/
		
		$b=array(false,false);
		
		$b[0]=($action==='after_booking_send') && ($bookingForm['meta']['google_calendar_add_event_action']=='after_booking_send');
		$b[1]=($action==='after_booking_status_change') && ($bookingForm['meta']['google_calendar_add_event_action']=='after_booking_status_update_to_'.$booking['meta']['booking_status_id']);
		
		if(!in_array(true,$b,true)) return(false);
		
		/***/
		
		$this->token=get_option(PLUGIN_CHBS_CONTEXT.'_google_calendar_token','');
		$this->expiration=get_option(PLUGIN_CHBS_CONTEXT.'_google_calendar_expiration','');
		
		$this->calendar_id=$bookingForm['meta']['google_calendar_id']; 
		$this->settings=json_decode($bookingForm['meta']['google_calendar_settings']); 
		
		/***/
		
		$token=$this->getToken($bookingForm);
		if(!$token) return(false);
		
		$this->token=get_option(PLUGIN_CHBS_CONTEXT.'_google_calendar_token','');
		$this->expiration=get_option(PLUGIN_CHBS_CONTEXT.'_google_calendar_expiration','');
		
		/***/

		$Timezone=new DateTimeZone(wp_timezone_string());
		
		/***/
		
		$duration=floor($booking['meta']['duration']);
		$bookingExtraTime=(int)$booking['meta']['extra_time_value'];
		
		if($bookingReturn)
		{
			$start=$booking['meta']['return_date'].' '.$booking['meta']['return_time'];
			$startDate=new DateTime($start,$Timezone);
		
			$endDate=clone $startDate;
			$endDate->modify('+'.($duration+ceil($bookingExtraTime/2)).' minutes');   
			
			$bookingReturn=false;
		}
		else
		{
			if(in_array($booking['meta']['service_type_id'],array(1,3)))
			{
				if(in_array($booking['meta']['transfer_type_id'],array(2)))
					$duration*=2;
				if(in_array($booking['meta']['transfer_type_id'],array(3)))
					$bookingReturn=true;
			}	
			
			if($bookingReturn)
				$bookingExtraTime=ceil($bookingExtraTime/2);
			
			$start=$booking['meta']['pickup_date'].' '.$booking['meta']['pickup_time'];
			$startDate=new DateTime($start,$Timezone);
		
			$endDate=clone $startDate;
			$endDate->modify('+'.($duration+$bookingExtraTime).' minutes');   
		}
		
		/***/
		
		$data=array();
		
		$data['booking']=$booking;
		$data['booking']['billing']=$Booking->createBilling($bookingId);
		
		/***/
		
		$bookingTitle=null;
		
		$bookingTitle.=$booking['post']->post_title;
				
		/***/
				
		$bookingDescription=$BookingHelper->createNotification($data);
		
		$bookingDetails=array
		(
			'summary'=>$bookingTitle,
			'description'=>$bookingDescription,
			'start'=>array
			(
				'dateTime'=>$startDate->format(DateTime::RFC3339),
			),
			'end'=>array
			(
				'dateTime'=>$endDate->format(DateTime::RFC3339),
			),
		);
						
		/***/

		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,'https://www.googleapis.com/calendar/v3/calendars/'.$this->calendar_id.'/events?access_token='.$this->token);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($bookingDetails));
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json')); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		
		$response=curl_exec($ch);
		$responseDecoded=json_decode($response);
		
		$LogManager=new CHBSLogManager();
		$LogManager->add('google_calendar',1,print_r($responseDecoded,true));   
		
		curl_close($ch);
		
		CHBSPostMeta::updatePostMeta($booking['post'],'google_calendar_add_event',1);
		
		if((is_object($responseDecoded)) && (property_exists($responseDecoded,'kind')) && ($responseDecoded->kind=='calendar#event'))
		{
			if($bookingReturn)
			{
				$this->sendBooking($bookingId,true,$action);
			}
			
			return(true);
		}
		
		return(false);
	}
	
	/**************************************************************************/
	
	function getCalendarList($bookingForm)
	{
		$token=$this->getToken($bookingForm);
		if(!$token) return(false);
		
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,'https://www.googleapis.com/calendar/v3/users/me/calendarList?access_token='.$token);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		
		$response=curl_exec($ch);
		$responseDecoded=json_decode($response);
		
		$LogManager=new CHBSLogManager();
		$LogManager->add('google_calendar',2,print_r($responseDecoded,true));   
		
		curl_close($ch);
		
		if((is_object($responseDecoded)) && (property_exists($responseDecoded,'kind')) && ($responseDecoded->kind=='calendar#calendarList'))
			return($responseDecoded);
		
		return(false);
	}
	
	/**************************************************************************/
	
	function getToken($bookingForm)
	{
		if((int)$bookingForm['meta']['google_calendar_regenerate_token_enable']===1)
		{
			
		}	
		else
		{
			if(($this->token) && ($this->expiration) && ($this->expiration>time()))
			{
				return($this->token);
			}
		}
		
		/***/
		
		$header='{"alg":"RS256","typ":"JWT"}';
		$headerEncoded=$this->base64URLEncode($header);
		
		/***/
		
		$assertionTime=time();
		$expirationTime=$assertionTime+3600;
		
		$claimSet='{
		  "iss":"'.$this->settings->client_email.'",
		  "scope":"https://www.googleapis.com/auth/calendar",
		  "aud":"https://www.googleapis.com/oauth2/v4/token",
		  "exp":'.$expirationTime.',
		  "iat":'.$assertionTime.'
		}';
		
		$claimSetEncoded=$this->base64URLEncode($claimSet);

		/***/
		
		$signature='';
		openssl_sign($headerEncoded.'.'.$claimSetEncoded,$signature,$this->settings->private_key,'SHA256');
		$signatureEncoded=$this->base64URLEncode($signature);
		$assertion=$headerEncoded.'.'.$claimSetEncoded.'.'.$signatureEncoded;

		/***/
		
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,'https://www.googleapis.com/oauth2/v4/token');
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/x-www-form-urlencoded'));
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch,CURLOPT_POSTFIELDS,'grant_type=urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Ajwt-bearer&assertion='.$assertion);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		
		$response=curl_exec($ch);
		$responseDecoded=json_decode($response);
		
		$LogManager=new CHBSLogManager();
		$LogManager->add('google_calendar',3,print_r($responseDecoded,true));   

		if((is_object($responseDecoded)) && (property_exists($responseDecoded,'access_token')))
		{
			$this->token=$responseDecoded->access_token;
			$this->expiration=$expirationTime;
			
			update_option(PLUGIN_CHBS_CONTEXT.'_google_calendar_token',$this->token);
			update_option(PLUGIN_CHBS_CONTEXT.'_google_calendar_expiration',$this->expiration);
			
			return($this->token);
		}
			
		return(false);
	}
	
	/**************************************************************************/
	
	function base64URLEncode($data)
	{
		return(rtrim(strtr(base64_encode($data),'+/','-_'),'='));
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/