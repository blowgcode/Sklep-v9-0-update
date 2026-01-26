<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingCancel
{
	/**************************************************************************/
	
	function __construct()
	{

	}
	
	/**************************************************************************/
	
	function generateBookingCancelURL($bookingId)
	{
		if((int)CHBSOption::getOption('booking_cancel_enable')!==1) return(false);
		
		$Booking=new CHBSBooking();
		if(($booking=$Booking->getBooking($bookingId))===false) return(false);

		$address=get_permalink(CHBSOption::getOption('booking_cancel_confirmation_page'));

		if($address===false) return(false);

		$link=add_query_arg(array('token'=>self::createToken($bookingId),'booking_id'=>$bookingId),$address);

		return($link);
	}

	/**************************************************************************/
	
	static function createToken($bookingId)
	{
		$salt=CHBSOption::getSalt(PLUGIN_CHBS_CONTEXT,2);
		return(strtoupper(md5($salt.$bookingId)));
	}
	
	/**************************************************************************/
	
	function createConfirmationForm()
	{
		global $chbs_logEvent;
		
		$html=null;

		$Booking=new CHBSBooking();
		$Validation=new CHBSValidation();
		$BookingStatus=new CHBSBookingStatus();

		$token=CHBSHelper::getGetValue('token',false);
		$bookingId=CHBSHelper::getGetValue('booking_id',false);

		if((int)CHBSOption::getOption('booking_cancel_enable')!==1) return(false);

		if($Validation->isEmpty($token)) return;
		
		if(($booking=$Booking->getBooking($bookingId))===false) return;
		
		$confirmationToken=self::createToken($bookingId);
		if(strcmp($token,$confirmationToken)!=0) return;

		if(!in_array($booking['meta']['booking_status_id'],CHBSOption::getOption('booking_cancel_cancellable_booking_status_id')))
		{
			$html='<div class="chbs-main chbs-booking-form-id-'.(int)$booking['meta']['booking_form_id'].'"><div class="chbs-notice">'.sprintf(__('Booking "%s" with "%s" status cannot be cancelled.','chauffeur-booking-system'),$bookingId,$BookingStatus->getBookingStatusName($booking['meta']['booking_status_id'])).'</div></div>';
		}
		else
		{
			$html='<div class="chbs-main chbs-booking-form-id-'.(int)$booking['meta']['booking_form_id'].'"><div class="chbs-notice">'.sprintf(__('Booking "%s" has been cancelled.','chauffeur-booking-system'),$bookingId).'</div></div>';
			
			/***/
			
			CHBSPostMeta::updatePostMeta($bookingId,'booking_status_id',3);

			/***/

			$recipient=preg_split('/;/',CHBSOption::getOption('booking_cancel_email_recipient'));

			foreach($recipient as $index=>$value)
			{
				if(!$Validation->isEmailAddress($value))
					unset($recipient[$index]);
			}

			if(count($recipient))
			{
				$chbs_logEvent=8;
				
				$emailTemplate='booking_cancel_customer';
				$emailSubject=sprintf(__('Booking "%s" has been cancelled by the customer','chauffeur-booking-system'),$booking['post']->post_title);
				
				$Booking->sendEmail($bookingId,CHBSOption::getOption('sender_default_email_account_id'),$emailTemplate,$recipient,$emailSubject);   
			}

			/***/

			$bookingOld=$booking;
			$bookingNew=$Booking->getBooking($bookingId);

			$emailSend=false;

			$WooCommerce=new CHBSWooCommerce();
			$WooCommerce->changeStatus(-1,$bookingId,$emailSend);

			if(!$emailSend)
				$Booking->sendEmailBookingChangeStatus($bookingOld,$bookingNew);

			/***/
			
			do_action('chbs_booking_status_change',$bookingNew,$bookingOld);
			
			/***/
		}
		
		return($html);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/