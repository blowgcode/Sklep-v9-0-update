<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingServiceNotification
{
	/**************************************************************************/

	function __construct($eventType)
	{
		$this->currentDate=date_i18n('Y-m-d');
		$this->currentTime=date_i18n('H:i');
		
		$this->eventType=(int)$eventType;
	}

	/**************************************************************************/

	function send()
	{
		/***/
		
		global $chbs_phpmailer;
		
		$Email=new CHBSEmail();
		$Booking=new CHBSBooking();
		$Validation=new CHBSValidation();
		$EmailAccount=new CHBSEmailAccount();
	
		if(((int)CHBSOption::getOption('email_service_reminder_customer_enable')!=1) && ((int)CHBSOption::getOption('email_service_reminder_driver_enable')!=1) && ((int)CHBSOption::getOption('email_service_post_arrival_message_customer_enable')!=1)) return(false);

		/***/
		
		$emailAccountId=(int)CHBSOption::getOption('sender_default_email_account_id');
	   		
		if($emailAccountId===-1) return(false);
		if(($emailAccount=$EmailAccount->getDictionary(array('email_account_id'=>$emailAccountId)))===false) return(false);

		/***/
		
		$emailAccount=$emailAccount[$emailAccountId];
		
		$chbs_phpmailer['sender_name']=$emailAccount['meta']['sender_name'];
		$chbs_phpmailer['sender_email_address']=$emailAccount['meta']['sender_email_address'];
		
		$chbs_phpmailer['smtp_auth_enable']=$emailAccount['meta']['smtp_auth_enable'];
		$chbs_phpmailer['smtp_auth_debug_enable']=$emailAccount['meta']['smtp_auth_debug_enable'];
		
		$chbs_phpmailer['smtp_auth_username']=$emailAccount['meta']['smtp_auth_username'];
		$chbs_phpmailer['smtp_auth_password']=$emailAccount['meta']['smtp_auth_password'];
		
		$chbs_phpmailer['smtp_auth_host']=$emailAccount['meta']['smtp_auth_host'];
		$chbs_phpmailer['smtp_auth_port']=$emailAccount['meta']['smtp_auth_port'];
		
		$chbs_phpmailer['smtp_auth_secure_connection_type']=$emailAccount['meta']['smtp_auth_secure_connection_type'];
		
		/***/
		
		$data['style']=$Email->getEmailStyle();
		
		/***/
		
		if(((int)CHBSOption::getOption('email_service_reminder_customer_enable')===1) && ($this->eventType===1))
		{	
			$booking=$this->getBooking();
			if(count($booking)) 
			{
				foreach($booking as $index=>$value)
				{
					$data['booking']=$value;
					$data['booking']['billing']=$Booking->createBilling($index);

					$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'email_customer_before_service.php');
					$body=$Template->output();		

					$Email->send(array($value['meta']['client_contact_detail_email_address']),__('Chauffeur Service Reminder','chauffeur-booking-system'),$body);
				}	
			}
		}
		
		/***/
		
		if(((int)CHBSOption::getOption('email_service_reminder_driver_enable')===1) && ($this->eventType===2))
		{
			$booking=$this->getBooking();
			if(count($booking))
			{
				$driver=$this->getDriver();
				if(count($driver)) 
				{				
					foreach($booking as $index=>$value)
					{
						$driverId=(int)$value['meta']['driver_id'];

						if(!array_key_exists($driverId,$driver)) continue;
						if(!in_array(1,$driver[$driverId]['meta']['notification_type'])) continue;
						if(!$Validation->isEmailAddress($driver[$driverId]['meta']['contact_email_address'])) continue;
						
						$data['booking']=$value;
						$data['booking']['billing']=$Booking->createBilling($index);

						$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'email_driver_before_service.php');
						$body=$Template->output();		

						$Email->send(array($driver[$driverId]['meta']['contact_email_address']),__('Chauffeur Service Reminder','chauffeur-booking-system'),$body);
					}	
				}
			}			
		}
		
		/***/
		
		if(((int)CHBSOption::getOption('email_service_post_arrival_message_customer_enable')===1) && ($this->eventType===3))
		{
			$booking=$this->getBooking();
			if(count($booking)) 
			{
				foreach($booking as $index=>$value)
				{
					if((int)$value['meta']['email_service_post_arrival_message_customer_send']!==1)
					{
						$data=array();
						
						$data['booking']=$value;
						$data['booking']['billing']=$Booking->createBilling($index);
						
						if(in_array(CHBSOption::getOption('email_service_post_arrival_message_customer_duration_unit'),array(1,2,3)))
						{
							$durationUnit='minutes';
							if((int)CHBSOption::getOption('email_service_post_arrival_message_customer_duration_unit')===2) $durationUnit='hours';
							if((int)CHBSOption::getOption('email_service_post_arrival_message_customer_duration_unit')===3) $durationUnit='days';
							
							$dateTimeFormat='Y-m-d H:i';
							
							$currentTime=$this->currentTime;
						}
						else
						{
							$durationUnit='days';
							$dateTimeFormat='Y-m-d';
							
							$currentTime=null;
						}
						
						if($value['meta']['return_date']=='00-00-0000')
						{
							$dateStop=date_i18n($dateTimeFormat,strtotime($value['meta']['pickup_date'].' '.$value['meta']['pickup_time'].' + '.(int)$data['booking']['billing']['summary']['duration_s1_minute'].' minutes'));
						}
						else
						{
							$dateStop=date_i18n($dateTimeFormat,strtotime($value['meta']['return_date'].' '.$value['meta']['return_time'].' + '.(int)$data['booking']['billing']['summary']['duration_s1_minute'].' minutes'));
						}
						
						$dateStop=date_i18n($dateTimeFormat,strtotime($dateStop.' + '.(int)CHBSOption::getOption('email_service_post_arrival_message_customer_duration').' '.$durationUnit));
					
						if($dateStop==trim($this->currentDate.' '.$currentTime))
						{
							$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'email_customer_after_service.php');
							$body=$Template->output();		

							$Email->send(array($value['meta']['client_contact_detail_email_address']),__('Thank you for using our services','chauffeur-booking-system'),$body);							
						
							CHBSPostMeta::updatePostMeta($value['post']->ID,'email_service_post_arrival_message_customer_send',1); 
						}
					}
				}	
			}				
		}
	}
	
	/**************************************************************************/
	
	function getDriver()
	{
		$Driver=new CHBSDriver();
		$dictionary=$Driver->getDictionary();
		
		return($dictionary);
	}
	
	/**************************************************************************/
	
	function getBooking()
	{
		global $post;
		
		$dictionary=array();
		$Booking=new CHBSBooking();
		
		CHBSHelper::preservePost($post,$bPost);
		
		if(in_array($this->eventType,array(1,2)))
		{
			$prefix=$this->eventType===1 ? 'customer' : 'driver';
			
			$metaQuery=array
			(
				'relation'=>'OR',
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_pickup_datetime',
					'value'=>date_i18n('Y-m-d H:i:00',strtotime(date_i18n('Y-m-d H:i:00').'+ '.(int)CHBSOption::getOption('email_service_reminder_'.$prefix.'_duration').' minutes')),
					'compare'=>'='
				),
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_return_datetime',
					'value'=>date_i18n('Y-m-d H:i:00',strtotime(date_i18n('Y-m-d H:i:00').'+ '.(int)CHBSOption::getOption('email_service_reminder_'.$prefix.'_duration').' minutes')),
					'compare'=>'='
				)
			);
		}
		
		if(in_array($this->eventType,array(3)))
		{
			$metaQuery=array
			(
				'relation'=>'OR',
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_booking_status_id',
					'value'=>array(4),
					'compare'=>'IN'
				)
			);	
		}
		
		$argument=array
		(
			'post_type'=>CHBSBooking::getCPTName(),
			'post_status'=>'publish',
			'posts_per_page'=>-1,
			'orderby'=>array('menu_order'=>'asc','title'=>'asc'),
			'suppress_filters'=>true,
			'meta_query'=>$metaQuery
		);
		
		$query=new WP_Query($argument);
		if($query===false) return($dictionary);
		
		while($query->have_posts())
		{
			$query->the_post();
			if(is_null($post)) continue;

			$dictionary[$post->ID]=$Booking->getBooking($post->ID);
		}

		CHBSHelper::preservePost($post,$bPost,0);	
		
		return($dictionary);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/