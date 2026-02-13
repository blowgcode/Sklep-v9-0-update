<?php

/******************************************************************************/
/******************************************************************************/

class CHBSEGAEExtension
{
	/**************************************************************************/
	
	public $event;
	public $eventGroup;
	
	public $library;
	public $optionDefault;
	public $libraryDefault;

	/**************************************************************************/	
	
	function __construct()
	{
		/***/
		
		$this->libraryDefault=array
		(
			'script'=>array
			(
				'use'=>1,
				'inc'=>true,
				'path'=>PLUGIN_CHBSEGAE_SCRIPT_URL,
				'file'=>'',
				'in_footer'=>true,
				'dependencies'=>array('jquery'),
			),
			'style'=>array
			(
				'use'=>1,
				'inc'=>true,
				'path'=>PLUGIN_CHBSEGAE_STYLE_URL,
				'file'=>'',
				'dependencies'=>array()
			)
		);
		
		/***/
		
		$this->optionDefault=array
		(
			'ga_code'=>'',
			'event'=>array(),
			'event_prefix'=>'',
			'event_console_log_enable'=>'0',
			'event_debug_view_enable'=>'1'
		);
		
		/***/
		
		$this->event=array
		(
			100=>array
			(
				'group_id'=>1,
				'name'=>'select',
				'label'=>esc_html__('Select (click, choose) a step from the top navigation bar or the dropdown list (in responsive mode)','chauffeur-booking-system-extension-ga-event')
			),
			200=>array
			(
				'group_id'=>2,
				'name'=>'click',
				'label'=>esc_html__('Click on the bottom navigation buttons','chauffeur-booking-system-extension-ga-event')
			),
			300=>array
			(
				'group_id'=>3,
				'name'=>'select',
				'label'=>esc_html__('Click on the service types in step #1 of the booking form','chauffeur-booking-system-extension-ga-event')
			),			
			400=>array
			(
				'group_id'=>4,
				'name'=>'select',
				'label'=>esc_html__('Click on the "Select" vehicle button in step #2 of the booking form','chauffeur-booking-system-extension-ga-event')
			),	
			500=>array
			(
				'group_id'=>5,
				'name'=>'select',
				'label'=>esc_html__('Click on the "Select" booking extra button in step #2 of the booking form to choose add-on','chauffeur-booking-system-extension-ga-event')
			),
			501=>array
			(
				'group_id'=>5,
				'name'=>'deselect',
				'label'=>esc_html__('Click on the "Select" booking extra button in step #2 of the booking form to deselect add-on','chauffeur-booking-system-extension-ga-event')
			),			
			600=>array
			(
				'group_id'=>6,
				'name'=>'sign_up',
				'label'=>esc_html__('Click on the "Don\'t have an account?" button in step #3 of the booking form','chauffeur-booking-system-extension-ga-event')
			),			
			601=>array
			(
				'group_id'=>6,
				'name'=>'sign_in',
				'label'=>esc_html__('Click on the "Sign in" button in step #3 of the booking form','chauffeur-booking-system-extension-ga-event')
			),				
			700=>array
			(
				'group_id'=>7,
				'name'=>'select',
				'label'=>esc_html__('Click on the payment method in step #3 of the booking form','chauffeur-booking-system-extension-ga-event')
			),
			800=>array
			(
				'group_id'=>8,
				'name'=>'apply',
				'label'=>esc_html__('Click on the "Apply code" button in step #4 of the booking form','chauffeur-booking-system-extension-ga-event')
			),
			900=>array
			(
				'group_id'=>9,
				'name'=>'apply',
				'label'=>esc_html__('Click on the "Apply gratuity" button in step #4 of the booking form','chauffeur-booking-system-extension-ga-event')
			),			
			1000=>array
			(
				'group_id'=>10,
				'name'=>'send',
				'label'=>esc_html__('Send a booking','chauffeur-booking-system-extension-ga-event')
			),
		);
		
		$this->eventGroup=array
		(
			1=>array
			(
				'name'=>'navigation_top',
				'label'=>esc_html__('Top navigation','chauffeur-booking-system-extension-ga-event'),
				'description'=>esc_html__('Events for the top navigation bar','chauffeur-booking-system-extension-ga-event')
			),		
			2=>array
			(
				'name'=>'navigation_bottom',
				'label'=>esc_html__('Bottom navigation','chauffeur-booking-system-extension-ga-event'),
				'description'=>esc_html__('Events for the bottom navigation buttons','chauffeur-booking-system-extension-ga-event')
			),	
			3=>array
			(
				'name'=>'service_type',
				'label'=>esc_html__('Service types','chauffeur-booking-system-extension-ga-event'),
				'description'=>esc_html__('Events for service types','chauffeur-booking-system-extension-ga-event')
			),				
			4=>array
			(
				'name'=>'vehicle',
				'label'=>esc_html__('Vehicles','chauffeur-booking-system-extension-ga-event'),
				'description'=>esc_html__('Events for vehicles','chauffeur-booking-system-extension-ga-event')
			),
			5=>array
			(
				'name'=>'booking_extra',
				'label'=>esc_html__('Booking extras','chauffeur-booking-system-extension-ga-event'),
				'description'=>esc_html__('Events for booking extras','chauffeur-booking-system-extension-ga-event')
			),
			6=>array
			(
				'name'=>'user',
				'label'=>esc_html__('Users','chauffeur-booking-system-extension-ga-event'),
				'description'=>esc_html__('Events for users','chauffeur-booking-system-extension-ga-event')
			),
			7=>array
			(
				'name'=>'payment',
				'label'=>esc_html__('Payments','chauffeur-booking-system-extension-ga-event'),
				'description'=>esc_html__('Events for payments','chauffeur-booking-system-extension-ga-event')
			),
			8=>array
			(
				'name'=>'coupon',
				'label'=>esc_html__('Coupons','chauffeur-booking-system-extension-ga-event'),
				'description'=>esc_html__('Events for coupons','chauffeur-booking-system-extension-ga-event')
			),
			9=>array
			(
				'name'=>'gratuity',
				'label'=>esc_html__('Gratuity','chauffeur-booking-system-extension-ga-event'),
				'description'=>esc_html__('Events for gratuity','chauffeur-booking-system-extension-ga-event')
			),			
			10=>array
			(
				'name'=>'booking',
				'label'=>esc_html__('Bookings','chauffeur-booking-system-extension-ga-event'),
				'description'=>esc_html__('Events for bookings','chauffeur-booking-system-extension-ga-event')
			)	
		);
		
		/***/
	}
	
	/**************************************************************************/
	
	function getEvent()
	{
		return($this->event);
	}
	
	/**************************************************************************/
	
	function getEventGroup()
	{
		return($this->eventGroup);
	}
	
	/**************************************************************************/
	
	function getEventName($eventId)
	{
		$eventGroupId=$this->event[$eventId]['group_id'];
		
		$eventName=$this->event[$eventId]['name'];
		$eventGroupName=$this->eventGroup[$eventGroupId]['name'];
		
		return($eventGroupName.'_'.$eventName);
	}
	
	/**************************************************************************/
	
	public function prepareLibrary()
	{
		$ExtensionOption=new CHBSExtensionOption(PLUGIN_CHBSEGAE_PREFIX);
	
		$ExtensionOption->createOption();		
		
		$this->library=array
		(
			'script' => array
			(
				'chbsegae-admin'=>array
				(
					'file'=>'admin.js'
				),
				'chbsegae-ga-event'=>array
				(
					'use'=>2,
					'file'=>'CHBSEGAE.GAEvent.js'
				)			
			),
			'style'=>array
			(
				'chbsegae-themeOption-overwrite'=>array
				(
					'file'=>'jquery.themeOption.overwrite.css'
				),
			)
		);		
	}	
	
	/**************************************************************************/
	
	public function addLibrary($type,$use,$action='register')
	{
		foreach($this->library[$type] as $index=>$value)
			$this->library[$type][$index]=array_merge($this->libraryDefault[$type],$value);
		
		foreach($this->library[$type] as $index=>$data)
		{
			if(!$data['inc']) continue;
			
			if($data['use']!=3)
			{
				if($data['use']!=$use) continue;
			}			
			
			if($type=='script')
			{
				if($action=='register')
					wp_register_script($index,$data['path'].$data['file'],$data['dependencies'],null);
				else
					wp_enqueue_script($index,$data['path'].$data['file'],$data['dependencies'],null,false);
			}
			else 
			{
				if($action=='register')
					wp_register_style($index,$data['path'].$data['file'],$data['dependencies'],null);
				else
					wp_enqueue_style($index,$data['path'].$data['file'],$data['dependencies'],null,false);
			}
		}
	}
	
	/**************************************************************************/
	
	public function activate()
	{	
		require_once(PLUGIN_CHBSEGAE_PATH.'include.php');
		
		$ExtensionOption=new CHBSExtensionOption(PLUGIN_CHBSEGAE_PREFIX);
	
		$ExtensionOption->createOption();
		
		$optionSave=array();
		$optionCurrent=$ExtensionOption->getOptionObject();
		
		foreach($this->optionDefault as $index=>$value)
		{
			if(!array_key_exists($index,$optionCurrent))
				$optionSave[$index]=$value;
		}
		
		$optionSave=array_merge((array)$optionSave,$optionCurrent);
		foreach($optionSave as $index=>$value)
		{
			if(!array_key_exists($index,$this->optionDefault))
				unset($optionSave[$index]);
		}
		
		/***/
		
		$event=$this->getEvent();
		foreach($event as $eventIndex=>$eventData)
		{
			if(!array_key_exists($eventIndex,$optionSave['event']))
				$optionSave['event'][$eventIndex]=1;
		}
		
		/***/
		
		$ExtensionOption->resetOption();
		$ExtensionOption->updateOption($optionSave);
	}
	
	/**************************************************************************/
	
	public function deactivate()
	{

	}
	
	/**************************************************************************/
	
	public function init()
	{  
		require_once(PLUGIN_CHBSEGAE_PATH.'include.php');
		
		add_action('admin_init',array($this,'adminInit'));
		add_action('admin_menu',array($this,'adminMenu'));
		
		add_action('wp_ajax_'.PLUGIN_CHBSEGAE_CONTEXT.'_option_page_save',array($this,'adminOptionPanelSave'));
		
		if(!is_admin())
		{
			add_action('wp_enqueue_scripts',array($this,'publicInit'));
		}	
	}
	
	/**************************************************************************/
	
	public function publicInit()
	{
		$this->prepareLibrary();
		
		$this->addLibrary('style',2);
		$this->addLibrary('script',2);
		$this->addLibrary('style',2,'enqueue');
		$this->addLibrary('script',2,'enqueue');
		
		$data=array();
		
		$Validation=new CHBSValidation();
		
		$ExtensionOption=new CHBSExtensionOption(PLUGIN_CHBSEGAE_PREFIX);
		$ExtensionOption->createOption();		
		
		$option=$ExtensionOption->getOptionObject();
		
		foreach($option['event'] as $eventId=>$eventData)
		{
			$data['event'][$this->getEventName($eventId)]=array('enable'=>$eventData);
		}
		
		$data['event_prefix']=$option['event_prefix'];
		
		$data['event_debug_view_enable']=$option['event_debug_view_enable'];
		$data['event_console_log_enable']=$option['event_console_log_enable'];
		
		$script=
		'
			var chbseGAEvent;
			jQuery(document).ready(function($)
			{
				chbseGAEvent=$().CHBSEGAEvent('.json_encode($data).');
				chbseGAEvent.setup();
			});
		';
		
		if($Validation->isNotEmpty($option['ga_code']))
		{
			CHBSHelper::addInlineScript('chbs-booking-form',$option['ga_code']);
		}
		
		CHBSHelper::addInlineScript('chbs-booking-form',$script);
	}
	
	/**************************************************************************/
	
	public function adminInit()
	{
		$this->prepareLibrary();
		
		$this->addLibrary('style',1);
		$this->addLibrary('script',1);
		$this->addLibrary('style',1,'enqueue');
		$this->addLibrary('script',1,'enqueue');
		
		$data=array();
		
		wp_localize_script('jquery-themeOption','qsData',array('l10n_print_after'=>'chbsegaeData='.json_encode($data).';'));
	}
	
	/**************************************************************************/
	
	public function adminMenu()
	{
		add_options_page(esc_html__('Chauffeur Booking System: GA Events','chauffeur-booking-system-extension-ga-event'),esc_html__('Chauffeur Booking System: GA Events','chauffeur-booking-system-extension-ga-event'),'edit_theme_options',PLUGIN_CHBSEGAE_CONTEXT,array($this,'adminCreateOptionPage'));
	}
	
	/**************************************************************************/
	
	public function adminCreateOptionPage()
	{
		$data=array();
		
		$ExtensionOption=new CHBSExtensionOption(PLUGIN_CHBSEGAE_PREFIX);
		
		$ExtensionOption->createOption();
		
		$data['option']=$ExtensionOption->getOptionObject();
		
		$data['dictionary']['event']=$this->getEvent();
		$data['dictionary']['event_group']=$this->getEventGroup();
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBSEGAE_CONTEXT.'_option_page','option_page');
	
		echo CHBSTemplate::outputS($data,PLUGIN_CHBSEGAE_TEMPLATE_PATH.'admin/option.php');
	}
	
	/**************************************************************************/
	
	public function adminOptionPanelSave()
	{			
		if(CHBSHelper::verifyNonce('option_page','option_page',PLUGIN_CHBSEGAE_CONTEXT)===false) return(false);
		
		$response=array('global'=>array('error'=>1));

		$Notice=new CHBSNotice();
		$Validation=new CHBSValidation();
		$ExtensionOption=new CHBSExtensionOption(PLUGIN_CHBSEGAE_PREFIX);
		$ExtensionHelper=new CHBSExtensionHelper(PLUGIN_CHBSEGAE_PREFIX);
		
		$invalidValue=esc_html__('This field includes invalid value.','chauffeur-booking-system-extension-ga-event');
		
		$option=$ExtensionHelper->getPostOption();
		
		/***/
		
		if(!$Validation->isBool($option['event_console_log_enable']))
			$Notice->addError($ExtensionHelper->getFormName('event_console_log_enable',false),$invalidValue); 
		if(!$Validation->isBool($option['event_debug_view_enable']))
			$Notice->addError($ExtensionHelper->getFormName('event_debug_view_enable',false),$invalidValue); 
		
		/***/
		
		$event=$this->getEvent();
	
		foreach($event as $eventId=>$eventData)
		{
			if(!$Validation->isBool($option['event'][$eventId]))
				$option['event'][$eventId]=0;
		}

		/***/
		
		if($Notice->isError())
		{
			$response['local']=$Notice->getError();
		}
		else
		{
			$response['global']['error']=0;
			$ExtensionOption->updateOption($option);
		}
		
		/***/
		
		$response['global']['notice']=$Notice->createHTML(PLUGIN_CHBS_TEMPLATE_PATH.'admin/notice.php');

		echo json_encode($response);
		exit;
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/