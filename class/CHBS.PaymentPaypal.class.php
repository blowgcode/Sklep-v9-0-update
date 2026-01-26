<?php

/******************************************************************************/
/******************************************************************************/

class CHBSPaymentPaypal
{
	/**************************************************************************/
	
	function __construct()
	{

	}
	
	/**************************************************************************/
	
	function createPaymentForm($postId,$bookingForm)
	{
		$Validation=new CHBSValidation();
		
		$formUrl='https://www.paypal.com/cgi-bin/webscr';
		if((int)$bookingForm['meta']['payment_paypal_sandbox_mode_enable']===1)
			$formUrl='https://www.sandbox.paypal.com/cgi-bin/webscr';
		
		$successUrl=$bookingForm['meta']['payment_paypal_success_url_address'];
		if($Validation->isEmpty($successUrl)) $successUrl=add_query_arg('action','success',get_the_permalink($postId));
		
		$cancelUrl=$bookingForm['meta']['payment_paypal_cancel_url_address'];
		if($Validation->isEmpty($cancelUrl)) $cancelUrl=add_query_arg('action','cancel',get_the_permalink($postId));		
		
		$html=
		'
			<form action="'.esc_url($formUrl).'" method="post" name="chbs-form-paypal">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="'.esc_attr($bookingForm['meta']['payment_paypal_email_address']).'">				
				<input type="hidden" name="item_name" value="">
				<input type="hidden" name="item_number" value="0">
				<input type="hidden" name="amount" value="0.00">	
				<input type="hidden" name="currency_code" value="">
				<input type="hidden" value="1" name="no_shipping">
				<input type="hidden" value="'.esc_url(get_the_permalink($postId)).'?action=ipn" name="notify_url">				
				<input type="hidden" value="'.esc_url($successUrl).'" name="return">
				<input type="hidden" value="'.esc_url($cancelUrl).'" name="cancel_return">
			</form>
		';
		
		return($html);
	}
	
	/**************************************************************************/
	
	function handleIPN()
	{
		$bookingId=(int)$_POST['item_number'];
		if($bookingId<=0) $bookingId=-1;
		
		$Booking=new CHBSBooking();
		$BookingForm=new CHBSBookingForm();
		$BookingStatus=new CHBSBookingStatus();
		
		$LogManager=new CHBSLogManager();
		
		$LogManager->add('paypal',2,__('[1] Receiving a payment.','chauffeur-booking-system'));	
		
		$booking=$Booking->getBooking($bookingId);
		
		if((!is_array($booking)) || (!count($booking))) 
		{
			$LogManager->add('paypal',2,sprintf(__('[2] Booking %s is not found.','chauffeur-booking-system'),$bookingId));	
			return;
		}
		
		$bookingForm=$BookingForm->getDictionary(array('booking_form_id'=>$booking['meta']['booking_form_id']));
		
		if(!count($bookingForm)) 
		{	
			$LogManager->add('paypal',2,sprintf(__('[3] Booking form %s is not found.','chauffeur-booking-system'),$booking['meta']['booking_form_id']));	
			return;
		}
		
		$bookingForm=$bookingForm[$booking['meta']['booking_form_id']];
		
		$request='cmd='.urlencode('_notify-validate');
		
		$postData=CHBSHelper::stripslashes($_POST);
		
		foreach($postData as $key=>$value) 
			$request.='&'.$key.'='.urlencode($value);
		
		$address='https://ipnpb.paypal.com/cgi-bin/webscr';
		if($bookingForm['meta']['payment_paypal_sandbox_mode_enable']==1)
			$address='https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
		
		$LogManager->add('paypal',2,sprintf(__('[4] Sending a request: %s on address: %s.','chauffeur-booking-system'),$request,$address));	
		
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$address);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$request);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
		$response=curl_exec($ch);
		
		if(curl_errno($ch)) 
		{	
			$LogManager->add('paypal',2,sprintf(__('[5] Error %s during processing cURL request.','chauffeur-booking-system'),curl_error($ch)));	
			return;
		}
		
		if(!strcmp(trim($response),'VERIFIED')==0) 
		{
			$LogManager->add('paypal',2,sprintf(__('[6] Request cannot be verified: %s.','chauffeur-booking-system'),$response));	
			return;
		}
		
		$meta=CHBSPostMeta::getPostMeta($bookingId);
						
		if(!((array_key_exists('payment_paypal_data',$meta)) && (is_array($meta['payment_paypal_data']))))
			$meta['payment_paypal_data']=array();
		
		$meta['payment_paypal_data'][]=$postData;
		
		CHBSPostMeta::updatePostMeta($bookingId,'payment_paypal_data',$meta['payment_paypal_data']);
		
		$LogManager->add('paypal',2,__('[7] Updating a booking about transaction details.','chauffeur-booking-system'));
		
		if($postData['payment_status']=='Completed')
		{
			if(CHBSOption::getOption('booking_status_payment_success')!=-1)
			{
				if($BookingStatus->isBookingStatus(CHBSOption::getOption('booking_status_payment_success')))
				{
					$LogManager->add('paypal',2,__('[11] Updating booking status.','chauffeur-booking-system'));
					
					$bookingOld=$Booking->getBooking($bookingId);

					CHBSPostMeta::updatePostMeta($bookingId,'booking_status_id',CHBSOption::getOption('booking_status_payment_success'));

					$bookingNew=$Booking->getBooking($bookingId);

					$emailAdminSend=false;
					$emailClientSend=false;

					$subject=sprintf(__('New booking "%s" has been received','chauffeur-booking-system'),$bookingNew['post']->post_title);

					if(((int)$bookingForm['meta']['email_notification_booking_new_client_enable']===1) && ((int)$bookingForm['meta']['email_notification_booking_new_client_payment_success_enable']===1))
					{
						$chbs_logEvent=1;
						$emailAdminSend=true;
						$Booking->sendEmail($bookingId,$bookingForm['meta']['booking_new_sender_email_account_id'],'booking_new_client',array($bookingNew['meta']['client_contact_detail_email_address']),$subject);
					}

					if(((int)$bookingForm['meta']['email_notification_booking_new_admin_enable']===1) && ((int)$bookingForm['meta']['email_notification_booking_new_admin_payment_success_enable']===1))
					{
						$chbs_logEvent=2;
						$emailClientSend=true;
						$Booking->sendEmail($bookingId,$bookingForm['meta']['booking_new_sender_email_account_id'],'booking_new_admin',preg_split('/;/',$bookingForm['meta']['booking_new_recipient_email_address']),$subject);
					}

					if(!$emailClientSend)
					{
						$emailSend=false;

						$WooCommerce=new CHBSWooCommerce();
						$WooCommerce->changeStatus(-1,$bookingId,$emailSend);									

						if(!$emailSend)
							$Booking->sendEmailBookingChangeStatus($bookingOld,$bookingNew);
					}
					
					$GoogleCalendar=new CHBSGoogleCalendar();
					$GoogleCalendar->sendBooking($bookingId,false,'after_booking_status_change');
				}
				else
				{
					$LogManager->add('paypal',2,__('[10] Cannot find a valid booking status.','chauffeur-booking-system'));	
				}
			}
			else
			{
				$LogManager->add('paypal',2,__('[9] Changing status of the booking after successful payment is off.','chauffeur-booking-system'));
			}
		}
		else
		{
			$LogManager->add('paypal',2,sprintf(__('[8] Payment status %s is not supported.','chauffeur-booking-system'),$postData['payment_status']));
		}
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/