<?php

/******************************************************************************/
/******************************************************************************/

class CHBSLogManager
{
	/**************************************************************************/
	
	public $type;
	
	/**************************************************************************/
	
	function __construct()
	{
		$this->type=array
		(
			'mail'=>array
			(
				1=>array
				(
					'description'=>__('Sending an notification about new booking to the customer.','chauffeur-booking-system')
				),
				2=>array
				(
					'description'=>__('Sending an notification about new booking on defined e-mail addresses.','chauffeur-booking-system')
				),			   
				3=>array
				(
					'description'=>__('Sending an notification about new booking to the driver.','chauffeur-booking-system')
				),
				4=>array
				(
					'description'=>__('Sending an notification about new changes in the booking to the customer.','chauffeur-booking-system')
				),
				5=>array
				(
					'description'=>__('Sending an notification about new assigned driver to the booking.','chauffeur-booking-system')
				),
				6=>array
				(
					'description'=>__('Sending an notification about new unassigned driver to the booking.','chauffeur-booking-system')
				),
				7=>array
				(
					'description'=>__('Sending an notification with information about accepting/rejecting booking by driver.','chauffeur-booking-system')
				),
				8=>array
				(
					'description'=>__('Sending an notification about cancelling booking on defined e-mail addresses.','chauffeur-booking-system')
				)
			),
			'nexmo'=>array
			(
				1=>array
				(
					'description'=>__('Sending an notification about new booking on defined phone number.','chauffeur-booking-system')
				)
			),
			'twilio'=>array
			(
				1=>array
				(
					'description'=>__('Sending an notification about new booking on defined phone number.','chauffeur-booking-system')
				)				
			),
			'telegram'=>array
			(
				1=>array
				(
					'description'=>__('Sending an notification about new booking on defined phone number.','chauffeur-booking-system')
				),
				2=>array
				(
					'description'=>__('Response and request data.','chauffeur-booking-system')
				)				
			),
			'google_calendar'=>array
			(
				1=>array
				(
					'description'=>__('Adding a new event to the calendar.','chauffeur-booking-system')
				),
				2=>array
				(
					'description'=>__('Getting a calendar list.','chauffeur-booking-system')
				),
				3=>array
				(
					'description'=>__('Getting token.','chauffeur-booking-system')
				)
			),
			'geolocation'=>array
			(
				1=>array
				(
					'description'=>__('Checking (in the pricing rule) if location belongs to country.','chauffeur-booking-system')
				),
				2=>array
				(
					'description'=>__('Getting country code based on customer IP address.','chauffeur-booking-system')
				)				
			),
			'stripe'=>array
			(
				1=>array
				(
					'description'=>esc_html__('Creating a payment.','chauffeur-booking-system')
				),
				2=>array
				(
					'description'=>esc_html__('Receiving a payment.','chauffeur-booking-system')
				)	
			),
			'paypal'=>array
			(
				1=>array
				(
					'description'=>esc_html__('Creating a payment.','chauffeur-booking-system')
				),
				2=>array
				(
					'description'=>esc_html__('Receiving a payment.','chauffeur-booking-system')
				)	
			),
			'google_map'=>array
			(
				1=>array
				(
					'description'=>esc_html__('Calculating a distance between two points.','chauffeur-booking-system')
				),
				2=>array
				(
					'description'=>esc_html__('Calculating a distance between two points.','chauffeur-booking-system')
				)
			),
			'fixerio'=>array
			(
				1=>array
				(
					'description'=>esc_html__('Importing an exchange rates.','chauffeur-booking-system')
				)	
			),
			'google_recaptcha'=>array
			(
				1=>array
				(
					'description'=>__('Verifying the user\'s response.','chauffeur-booking-system')
				)
			),
			'license'=>array
			(
				1=>array
				(
					'description'=>__('License verification','chauffeur-booking-system')
				)
			),
			'product_info'=>array
			(
				1=>array
				(
					'description'=>__('Product info checking','chauffeur-booking-system')
				)
			),
			'booking_data_validation'=>array
			(
				1=>array
				(
					'description'=>__('Booking data validation','chauffeur-booking-system')
				)
			)
		);
		
		$this->type=apply_filters(PLUGIN_CHBS_CONTEXT.'_log_manager_type',$this->type);
	}
		
	/**************************************************************************/
	
	function add($type,$event,$message)
	{	
		$Validation=new CHBSValidation();
		
		if($Validation->isEmpty($message)) return;
		
		$logType=$this->get($type);
		
		array_unshift($logType,array
		(
			'event'=>$event,
			'timestamp'=>strtotime('now'),
			'message'=>$message
		));
		
		if(count($logType)>100) $logType=array_slice($logType,0,100);
		
		$logFull=$this->get();
		$logFull[$type]=$logType;
		
		$r=update_option(PLUGIN_CHBS_OPTION_PREFIX.'_log',$logFull);
		
		if($r===false) $r=update_option(PLUGIN_CHBS_OPTION_PREFIX.'_log','');
	}
	
	/**************************************************************************/
	
	function get($type=null)
	{
		$log=get_option(PLUGIN_CHBS_OPTION_PREFIX.'_log');
	
		if(!is_array($log)) $log=array();
		if(is_null($type)) return($log);
		
		if(!array_key_exists($type,$log)) $log[$type]=array();
		if(!is_array($log[$type])) $log[$type]=array();
		
		return($log[$type]);
	}
	
	/**************************************************************************/
	
	function show($type)
	{
		$log=$this->get($type);
		
		if(!count($log)) return;
		
		$Validation=new CHBSValidation();
		
		$i=0;
		$html=null;
		
		foreach($log as $value)
		{
			if(!$this->isLogTypeEvent($type,$value['event'])) continue;
			
			if($Validation->isNotEmpty($html)) $html.='<br/>';
			
			$html.=
			'
				<li>
					<div class="to-log-entry">
						['.(++$i).']['.date_i18n('d-m-Y G:i:s',$value['timestamp']).']<br/>
						<b>'.$this->type[$type][$value['event']]['description'].'</b><br/><br/>
						'.nl2br(esc_html($value['message'])).'
					</div>
				</li>
			';
		}
		
		$html='<ul>'.$html.'</ul>';
		
		return($html);
	}
	
	/**************************************************************************/
	
	static function showS($type)
	{
		$LogManager=new CHBSLogManager();
		return($LogManager->show($type));
	}
	
	/**************************************************************************/
	
	function isLogTypeEvent($type,$event)
	{
		return(isset($this->type[$type][$event]));
	}
	
	/**************************************************************************/

	function logWPMailError($wp_error)
	{
		global $chbs_logEvent;
		
		if(!$this->isLogTypeEvent('mail',$chbs_logEvent)) return;
		
		$this->add('mail',$chbs_logEvent,print_r($wp_error,true));
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/