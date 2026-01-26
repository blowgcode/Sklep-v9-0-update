<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingDriver
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->bookingDriverEvent=array
		(
			1=>array
			(
				'label'=>__('Add driver to the booking.','chauffer-booking-system')
			),
			2=>array
			(
				'label'=>__('Remove driver from the booking.','chauffer-booking-system')
			),
			3=>array
			(
				'label'=>__('Sent e-mail message to the driver.','chauffer-booking-system')
			),
			4=>array
			(
				'label'=>__('Accept booking by driver.','chauffer-booking-system')
			),
			5=>array
			(
				'label'=>__('Reject booking by driver.','chauffer-booking-system')
			)
		);
	}

	/**************************************************************************/

	function setPostMetaDefault(&$meta)
	{
		CHBSHelper::setDefault($meta,'booking_driver_log',array());
		CHBSHelper::setDefault($meta,'booking_driver_status',-1);
	}

	/**************************************************************************/

	function getEventLabel($eventId)
	{
		if(array_key_exists($eventId,$this->bookingDriverEvent)) return($this->bookingDriverEvent[$eventId]['label']);
		return(null);
	}

	/**************************************************************************/

	function setDriver($newBooking,$oldBooking=false,$sendEmail=false,$setDriver=true)
	{
		$Driver=new CHBSDriver();

		/***/

		if($setDriver)
		{
			$driverId=-1;
			$driverDictionary=$Driver->getDictionary();

			if(array_key_exists(CHBSHelper::getPostValue('driver_id'),$driverDictionary))
				$driverId=CHBSHelper::getPostValue('driver_id');

			CHBSPostMeta::updatePostMeta($newBooking['post']->ID,'driver_id',$driverId); 
		}
		
		/***/

		if($oldBooking!==false)
		{
			if($newBooking['meta']['driver_id']!=$oldBooking['meta']['driver_id'])
			{
				if((int)$newBooking['meta']['driver_id']===-1)
				{
					$sendEmail=false;
					$this->addEvent($newBooking['post']->ID,2);
					$this->setBookingDriverAcceptanceData($newBooking['post']->ID,0,2,'0000-00-00','00:00');
					$this->setBookingDriverAcceptanceData($newBooking['post']->ID,0,1,'0000-00-00','00:00');
				}
				else
				{
					$sendEmail=true;
					$this->addEvent($newBooking['post']->ID,1); 
				}
			}
		}
		else $sendEmail=true;

		if($sendEmail)
		{
			$this->sendEmail($newBooking,$oldBooking);
			$this->setBookingDriverAcceptanceData($newBooking['post']->ID);
		}
	}

	/**************************************************************************/

	function addEvent($bookingId,$bookingDriverEventId)
	{
		$Driver=new CHBSDriver();
		$Booking=new CHBSBooking();

		if(($booking=$Booking->getBooking($bookingId))===false) return(false);

		if(($driverDictionary=$Driver->getDictionary())===false) return(false);

		$driverId=$booking['meta']['driver_id'];

		$data=array
		(
			'date'=>date_i18n('Y-m-d G:i'),
			'booking_driver_event_id'=>$bookingDriverEventId,
			'driver_id'=>$driverId,
			'driver_first_name'=>'',
			'driver_second_name'=>''
		);

		if(array_key_exists($driverId,$driverDictionary))
		{
			$data['driver_first_name']=$driverDictionary[$driverId]['meta']['first_name'];
			$data['driver_second_name']=$driverDictionary[$driverId]['meta']['second_name'];
		}

		$meta=CHBSPostMeta::getPostMeta($bookingId);
		if(!is_array($meta['booking_driver_log'])) $meta['booking_driver_log']=array();

		array_push($meta['booking_driver_log'],$data);

		CHBSPostMeta::updatePostMeta($bookingId,'booking_driver_log',$meta['booking_driver_log']); 
	}

	/**************************************************************************/

	function sendEmail($newBooking,$oldBooking=false)
	{
		global $chbs_logEvent;
		
		$Driver=new CHBSDriver();
		$Booking=new CHBSBooking();

		if(($driverDictionary=$Driver->getDictionary())===false) return(false);

		$recipient=array();

		if(array_key_exists($newBooking['meta']['driver_id'],$driverDictionary))
			$recipient[0]=$Driver->getNotificationRecipient($newBooking['post']->ID,1);

		if($oldBooking!==false)
		{
			if(array_key_exists($oldBooking['meta']['driver_id'],$driverDictionary))
				$recipient[1]=$Driver->getNotificationRecipient($oldBooking['post']->ID,1,'driver');
		}
		if(count($recipient[0]))
		{
			$chbs_logEvent=5;
			$Booking->sendEmail($newBooking['post']->ID,CHBSOption::getOption('sender_default_email_account_id'),'booking_assign_driver',$recipient[0],sprintf(__('You have been assigned to a booking "%s"','chauffeur-booking-system'),$newBooking['post']->post_title)); 
		}
		
		if(isset($recipient[1]))
		{
			if(count($recipient[1]))
			{
				$chbs_logEvent=6;
				$Booking->sendEmail($newBooking['post']->ID,CHBSOption::getOption('sender_default_email_account_id'),'booking_unassign_driver',$recipient[1],sprintf(__('You has been unassigned from booking "%s"','chauffeur-booking-system'),$oldBooking['post']->post_title)); 
			}
		}
	}

	/**************************************************************************/

	function generateLink($bookingId)
	{
		if((int)CHBSOption::getOption('booking_driver_acceptance_stage_1_enable')!==1) return(false);
		
		$Booking=new CHBSBooking();
		if(($booking=$Booking->getBooking($bookingId))===false) return(false);

		$address=get_permalink(CHBSOption::getOption('booking_driver_acceptance_confirmation_page'));

		if($address===false) return(false);

		$link=array();

		$driverId=$booking['meta']['driver_id'];

		$link['accept']=add_query_arg(array('token'=>self::createToken($bookingId,$driverId),'booking_id'=>$bookingId,'driver_id'=>$driverId,'status'=>'accept'),$address);
		$link['reject']=add_query_arg(array('token'=>self::createToken($bookingId,$driverId),'booking_id'=>$bookingId,'driver_id'=>$driverId,'status'=>'reject'),$address);

		return($link);
	}

	/**************************************************************************/
	
	static function createToken($bookingId,$driverId)
	{
		$salt=CHBSOption::getSalt();
		return(strtoupper(md5($salt.$bookingId.$driverId)));
	}
	
	/**************************************************************************/
	
	function setBookingDriverAcceptanceData($bookingId,$status=0,$stageNumber=1,$emailSendDate=null,$emailSendTime=null)
	{
		CHBSPostMeta::updatePostMeta($bookingId,'booking_acceptance_driver_status',$status); 
		CHBSPostMeta::updatePostMeta($bookingId,'booking_acceptance_driver_stage_number',$stageNumber);
			
		if(is_null($emailSendDate)) $emailSendDate=date_i18n('Y-m-d');
		if(is_null($emailSendTime)) $emailSendTime=date_i18n('H:i');
		
		CHBSPostMeta::updatePostMeta($bookingId,'booking_acceptance_driver_stage_'.$stageNumber.'_email_send_date',$emailSendDate);
		CHBSPostMeta::updatePostMeta($bookingId,'booking_acceptance_driver_stage_'.$stageNumber.'_email_send_time',$emailSendTime);	
	}

	/**************************************************************************/

	function createConfirmationForm()
	{
		global $chbs_logEvent;
		
		$html=null;

		$Driver=new CHBSDriver();
		$Booking=new CHBSBooking();
		$Validation=new CHBSValidation();
		$BookingStatus=new CHBSBookingStatus();

		$token=CHBSHelper::getGetValue('token',false);
		$status=CHBSHelper::getGetValue('status',false);
		$driverId=CHBSHelper::getGetValue('driver_id',false);
		$bookingId=CHBSHelper::getGetValue('booking_id',false);
		
		if((int)CHBSOption::getOption('booking_driver_acceptance_stage_1_enable')!==1) return(false);

		if($Validation->isEmpty($token)) return;
		if(($booking=$Booking->getBooking($bookingId))===false) return;

		if((int)$booking['meta']['booking_driver_status']===1) return;

		$confirmationToken=self::createToken($bookingId,$driverId);
		if(strcmp($token,$confirmationToken)!=0) return;
		
		$stage=1;
		if((int)$booking['meta']['booking_acceptance_driver_stage_number']===2) $stage=2;
		
		$driverDictionary=$Driver->getDictionary();
		if(!array_key_exists($driverId,$driverDictionary)) return;
		
		if($stage==1)
		{
			if($booking['meta']['driver_id']!==$driverId) return;
		}
		
		if($status=='accept')
		{
			CHBSPostMeta::updatePostMeta($bookingId,'driver_id',$driverId); 
			CHBSPostMeta::updatePostMeta($bookingId,'booking_acceptance_driver_status',1); 
			
			/***/
			
			$this->addEvent($bookingId,4);

			/***/
			
			if($BookingStatus->isBookingStatus(CHBSOption::getOption('booking_driver_acceptance_status_after_accept')))
				CHBSPostMeta::updatePostMeta($bookingId,'booking_status_id',CHBSOption::getOption('booking_driver_acceptance_status_after_accept'));

			$emailTemplate='booking_driver_accept';
			$emailSubject=sprintf(__('Driver %s accepts booking %s'),$booking['driver_full_name'],$booking['post']->post_title);

			$html='<div class="chbs-main"><div class="chbs-notice">'.__('<b>Thank you!</b> You have accepted this booking.','chauffeur-booking-system').'</div></div>';
		}
		else
		{
			$this->addEvent($bookingId,5);
			
			/***/
			
			CHBSPostMeta::updatePostMeta($bookingId,'driver_id',-1); 
			CHBSPostMeta::updatePostMeta($bookingId,'booking_acceptance_driver_status',0); 
			
			/***/
			
			if($BookingStatus->isBookingStatus(CHBSOption::getOption('booking_driver_acceptance_status_after_reject')))
				CHBSPostMeta::updatePostMeta($bookingId,'booking_status_id',CHBSOption::getOption('booking_driver_acceptance_status_after_reject'));

			$emailTemplate='booking_driver_reject';
			$emailSubject=sprintf(__('Driver %s rejects booking %s'),$booking['driver_full_name'],$booking['post']->post_title);

			$html='<div class="chbs-main"><div class="chbs-notice">'.__('You have rejected this booking.','chauffeur-booking-system').'</div></div>';
		}

		$recipient=preg_split('/;/',CHBSOption::getOption('booking_driver_acceptance_email_recipient'));

		foreach($recipient as $index=>$value)
		{
			if(!$Validation->isEmailAddress($value))
			unset($recipient[$index]);
		}

		if(count($recipient))
		{
			$chbs_logEvent=7;
			$Booking->sendEmail($booking['post']->ID,CHBSOption::getOption('sender_default_email_account_id'),$emailTemplate,$recipient,$emailSubject);   
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
		
		$GoogleCalendar=new CHBSGoogleCalendar();
		$GoogleCalendar->sendBooking($bookingId,false,'after_booking_status_change');
		
		/***/
		
		return($html);
	}
	
	/**************************************************************************/
	
	function setBookingDriverAcceptance($stage=1)
	{
		/****/
		
		if(!(((int)CHBSOption::getOption('booking_driver_acceptance_stage_1_enable')===1) && ((int)CHBSOption::getOption('booking_driver_acceptance_stage_2_enable')===1))) return(true);
		
		/****/
		
		global $post;
		
		$Driver=new CHBSDriver();
		$Booking=new CHBSBooking();
		$BookingStatus=new CHBSBookingStatus();
		
		if($stage===1)
		{
			if(($driverDictionary=$Driver->getDictionary())===false) return(false);

			/****/

			$query=$this->getBookingBookingDriverAcceptance($stage);
			
			/****/

			while($query->have_posts())
			{
				$query->the_post();

				if(($booking=$Booking->getBooking($post->ID))===false) continue;

				$oldBooking=$booking;
				$newBooking=$booking;

				foreach($driverDictionary as $driverDictionaryIndex=>$driverDictionaryValue)
				{
					CHBSPostMeta::updatePostMeta($post->ID,'driver_id',$driverDictionaryIndex);
					$newBooking['meta']['driver_id']=$driverDictionaryIndex;

					$this->sendEmail($newBooking,$oldBooking);

					$oldBooking=null;
				}

				CHBSPostMeta::updatePostMeta($post->ID,'driver_id',-1);

				$this->setBookingDriverAcceptanceData($post->ID,0,2);
			}
		}
		else if($stage===2)
		{
			$query=$this->getBookingBookingDriverAcceptance($stage);

			/****/

			$BookingStatus=new CHBSBookingStatus();
			
			while($query->have_posts())
			{
				$query->the_post();
				
				$bookingOld=$Booking->getBooking($post->ID);

				if($BookingStatus->isBookingStatus(CHBSOption::getOption('booking_driver_acceptance_status_after_reject')))
					CHBSPostMeta::updatePostMeta($post->ID,'booking_status_id',CHBSOption::getOption('booking_driver_acceptance_status_after_reject'));	
				
				$bookingNew=$Booking->getBooking($post->ID);
		
				$emailSend=false;
		
				$WooCommerce=new CHBSWooCommerce();
				$WooCommerce->changeStatus(-1,$post->ID,$emailSend);
		
				if(!$emailSend)
					$Booking->sendEmailBookingChangeStatus($bookingOld,$bookingNew);
		
				/***/
		
				$GoogleCalendar=new CHBSGoogleCalendar();
				$GoogleCalendar->sendBooking($post->ID,false,'after_booking_status_change');
				
				/***/
			}			
		}
	}
	
	/**************************************************************************/
	
	function getBookingBookingDriverAcceptance($stage=1)
	{
		$date=date_i18n('Y-m-d H:i',strtotime('-'.(int)CHBSOption::getOption('booking_driver_acceptance_stage_'.$stage.'_interval').' minutes',strtotime(date_i18n('Y-m-d H:i'))));
		
		list($date,$time)=preg_split('/ /',$date);
		
		$argument=array
		(
			'post_type'=>CHBSBooking::getCPTName(),
			'post_status'=>'publish',
			'posts_per_page'=>-1,
			'orderby'=>'meta_value',
			'meta_query'=>array
			(
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_woocommerce_product_id',
					'value'=>array(0),
					'compare'=>'IN'							
				),
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_booking_acceptance_driver_status',
					'value'=>0,
					'compare'=>'='							
				),		
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_booking_acceptance_driver_stage_number',
					'value'=>$stage,
					'compare'=>'='							
				),						
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_booking_acceptance_driver_stage_'.$stage.'_email_send_date',
					'value'=>$date,
					'compare'=>'='							
				),					
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_booking_acceptance_driver_stage_'.$stage.'_email_send_time',
					'value'=>$time,
					'compare'=>'<'							
				)
			)
		);

		$query=new WP_Query($argument);	
		
		return($query);
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/