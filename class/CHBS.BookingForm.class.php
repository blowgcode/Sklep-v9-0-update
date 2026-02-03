<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingForm
{
	/**************************************************************************/
	
	public $fieldMandatory;
	public $calculationMethod;
	public $vehicleSortingType;
	public $maximumBookingNumberTimeUnit;
	
	/**************************************************************************/
	
	function __construct()
	{
		$this->fieldMandatory=array
		(
			'client_contact_detail_phone_number'=>array
			(
				'label'=>__('Phone number','chauffeur-booking-system'),
				'mandatory'=>0
			),
			'client_billing_detail_company_name'=>array
			(
				'label'=>__('Company registered name','chauffeur-booking-system'),
				'mandatory'=>0
			),
			'client_billing_detail_tax_number'=>array
			(
				'label'=>__('Tax number','chauffeur-booking-system'),
				'mandatory'=>0
			),
			'client_billing_detail_street_name'=>array
			(
				'label'=>__('Street name','chauffeur-booking-system'),
				'mandatory'=>1
			),
			'client_billing_detail_street_number'=>array
			(
				'label'=>__('Street number','chauffeur-booking-system'),
				'mandatory'=>0
			),
			'client_billing_detail_city'=>array
			(
				'label'=>__('City','chauffeur-booking-system'),
				'mandatory'=>1
			),
			'client_billing_detail_state'=>array
			(
				'label'=>__('State','chauffeur-booking-system'),
				'mandatory'=>0
			),
			'client_billing_detail_postal_code'=>array
			(
				'label'=>__('Postal code','chauffeur-booking-system'),
				'mandatory'=>1
			),
			'client_billing_detail_country_code'=>array
			(
				'label'=>__('Country','chauffeur-booking-system'),
				'mandatory'=>1
			)
		);
		
		$this->vehicleSortingType=array
		(
			1=>array(__('Price ascending','chauffeur-booking-system')),
			2=>array(__('Price descending','chauffeur-booking-system')),
			3=>array(__('Order ascending','chauffeur-booking-system')),
			4=>array(__('Order descending','chauffeur-booking-system')),
			5=>array(__('Name ascending','chauffeur-booking-system')),
			6=>array(__('Name descending','chauffeur-booking-system')),
		);
		
		$this->calculationMethod=array
		(
			1=>array(__('Distance','chauffeur-booking-system'),array(1,3)),
			2=>array(__('Distance + Duration','chauffeur-booking-system'),array(1,3)),
			3=>array(__('(Distance + Duration) x Number of passengers','chauffeur-booking-system'),array(1,3)),
			4=>array(__('Passengers','chauffeur-booking-system'),array(1,3)),
			5=>array(__('Duration','chauffeur-booking-system'),array(2)),
			6=>array(__('Duration x Number of passengers','chauffeur-booking-system'),array(2)),
			7=>array(__('Distance + Duration + Passengers','chauffeur-booking-system'),array(1,3))
		);
		
		$this->maximumBookingNumberTimeUnit=array
		(
			1=>array(__('Time','chauffeur-booking-system')),
			2=>array(__('Date','chauffeur-booking-system')),
			3=>array(__('Date/Time','chauffeur-booking-system')),
			4=>array(__('Day of the week','chauffeur-booking-system')),
			5=>array(__('Pickup/return date'),'chauffeur-booking-system'),
		);
	}
		
	/**************************************************************************/
	
	public function init()
	{
		$this->registerCPT();
	}
	
	/**************************************************************************/

	public static function getCPTName()
	{
		return(PLUGIN_CHBS_CONTEXT.'_booking_form');
	}
	
	/**************************************************************************/
	
	private function registerCPT()
	{
		$BookingCancel=new CHBSBookingCancel();
		$BookingDriver=new CHBSBookingDriver();
		
		register_post_type
		(
			self::getCPTName(),
			array
			(
				'labels'=>array
				(
					'name'=>__('Booking Forms','chauffeur-booking-system'),
					'singular_name'=>__('Booking Form','chauffeur-booking-system'),
					'add_new'=>__('Add New','chauffeur-booking-system'),
					'add_new_item'=>__('Add New Booking Form','chauffeur-booking-system'),
					'edit_item'=>__('Edit Booking Form','chauffeur-booking-system'),
					'new_item'=>__('New Booking Form','chauffeur-booking-system'),
					'all_items'=>__('Booking Forms','chauffeur-booking-system'),
					'view_item'=>__('View Booking Form','chauffeur-booking-system'),
					'search_items'=>__('Search Booking Forms','chauffeur-booking-system'),
					'not_found'=>__('No Booking Forms Found','chauffeur-booking-system'),
					'not_found_in_trash'=>__('No Booking Forms Found in Trash','chauffeur-booking-system'), 
					'parent_item_colon'=>'',
					'menu_name'=>__('Booking Forms','chauffeur-booking-system')
				),	
				'public'=>false,  
				'show_ui'=>true, 
				'show_in_menu'=>'edit.php?post_type='.CHBSBooking::getCPTName(),
				'capability_type'=>'post',
				'menu_position'=>2,
				'hierarchical'=>false,  
				'rewrite'=>false,  
				'supports'=>array('title','page-attributes','thumbnail')  
			)
		);
		
		add_action('save_post',array($this,'savePost'));
		add_action('add_meta_boxes_'.self::getCPTName(),array($this,'addMetaBox'));
		add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_booking_form',array($this,'adminCreateMetaBoxClass'));
		
		add_shortcode(PLUGIN_CHBS_CONTEXT.'_booking_form',array($this,'createBookingForm'));
		add_shortcode(PLUGIN_CHBS_CONTEXT.'_booking_cancel_confirmation',array($BookingCancel,'createConfirmationForm'));
		add_shortcode(PLUGIN_CHBS_CONTEXT.'_booking_driver_acceptance_confirmation',array($BookingDriver,'createConfirmationForm'));
		
		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns')); 
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));
	}
	
	/**************************************************************************/
	
	static function getShortcodeName()
	{
		return(PLUGIN_CHBS_CONTEXT.'_booking_form');
	}
	
	/**************************************************************************/
	
	function addMetaBox()
	{
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_booking_form',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');		
	}
	
	/**************************************************************************/
	
	function addMetaBoxMain()
	{
		global $post;
			   
		$Route=new CHBSRoute();
		$Driver=new CHBSDriver();
		
		$Payment=new CHBSPayment();
		$Country=new CHBSCountry();
		$Vehicle=new CHBSVehicle();
		$Geofence=new CHBSGeofence();
		$Location=new CHBSLocation();
		$Currency=new CHBSCurrency();
		$GoogleMap=new CHBSGoogleMap();
		$WooCommerce=new CHBSWooCommerce();
		$ServiceType=new CHBSServiceType();
		$TransferType=new CHBSTransferType();
		$EmailAccount=new CHBSEmailAccount();
		$BookingExtra=new CHBSBookingExtra();
		$PaymentStripe=new CHBSPaymentStripe();
		$BookingStatus=new CHBSBookingStatus();
		$GoogleCalendar=new CHBSGoogleCalendar();
		$BookingGratuity=new CHBSBookingGratuity();
		$BookingFormStyle=new CHBSBookingFormStyle();
		$BookingFormElement=new CHBSBookingFormElement();
		
		$data=array();
		
		$data['meta']=CHBSPostMeta::getPostMeta($post);
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_booking_form');
		
		$data['dictionary']['route']=$Route->getDictionary();
		$data['dictionary']['payment']=$Payment->getPayment();
		
		$data['dictionary']['color']=$BookingFormStyle->getColor();
		
		$data['dictionary']['vehicle']=$Vehicle->getDictionary();
		$data['dictionary']['vehicle_category']=$Vehicle->getCategory();
		
		$data['dictionary']['service_type']=$ServiceType->getServiceType();
		$data['dictionary']['transfer_type']=$TransferType->getTransferType();
		
		$data['dictionary']['email_account']=$EmailAccount->getDictionary();
		$data['dictionary']['booking_extra_category']=$BookingExtra->getCategory();
  
		$data['dictionary']['country']=$Country->getCountry();
		
		$data['dictionary']['booking_status']=$BookingStatus->getBookingStatus();
		
		$data['dictionary']['google_map']['position']=$GoogleMap->getPosition();
		$data['dictionary']['google_map']['route_avoid']=$GoogleMap->getRouteAvoid();
		$data['dictionary']['google_map']['map_type_control_id']=$GoogleMap->getMapTypeControlId();
		$data['dictionary']['google_map']['map_type_control_style']=$GoogleMap->getMapTypeControlStyle();
		
		$data['dictionary']['form_element_panel']=$BookingFormElement->getPanel($data['meta']);
		
		$data['dictionary']['form_element_field_type']=$BookingFormElement->getFieldType();
		$data['dictionary']['form_element_field_layout']=$BookingFormElement->getFieldLayout();
		
		$data['dictionary']['driver']=$Driver->getDictionary();
		
		$data['dictionary']['location']=$Location->getDictionary();
		
		$data['dictionary']['currency']=$Currency->getCurrency();
		
		$data['dictionary']['vehicle_sorting_type']=$this->vehicleSortingType;
		
		$data['dictionary']['gratuity_type']=$BookingGratuity->getType();
		
		$data['dictionary']['field_mandatory']=$this->fieldMandatory;
		
		$data['dictionary']['payment_stripe_method']=$PaymentStripe->getPaymentMethod();
		
		$data['dictionary']['calculation_method']=$this->calculationMethod;
		
		$data['dictionary']['google_calendar_add_event_action']=$GoogleCalendar->getAddEventAction();
		
		$data['dictionary']['geofence']=$Geofence->getDictionary();
		
		$data['dictionary']['maximum_booking_number_time_unit']=$this->getMaximumBookingNumberTimeUnit();
		
		$data['dictionary']['woocommerce_product_category']=$WooCommerce->getProductCategoryDictionary();
		
		CHBSBookingHelper::getBookingTextShortcodes(false,$label);
		
		$data['message_shortcode']=$label;
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_booking_form.php');
		echo $Template->output();			
	}
	
	/**************************************************************************/
	
	function adminCreateMetaBoxClass($class) 
	{
		array_push($class,'to-postbox-1');
		return($class);
	}
	
	/**************************************************************************/
	
	function savePost($postId)
	{	  
		if(!$_POST) return(false);

		if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_booking_form_noncename','savePost')===false) return(false);
		
		$meta=array();
		
		$Date=new CHBSDate();
		$Route=new CHBSRoute();
		$Length=new CHBSLength();
		$Driver=new CHBSDriver();
		$Vehicle=new CHBSVehicle();
		$Payment=new CHBSPayment();
		$Country=new CHBSCountry();
		$Currency=new CHBSCurrency();
		$Location=new CHBSLocation();
		$Geofence=new CHBSGeofence();
		$Validation=new CHBSValidation();
		$WooCommerce=new CHBSWooCommerce();
		$ServiceType=new CHBSServiceType();
		$EmailAccount=new CHBSEmailAccount();
		$BookingExtra=new CHBSBookingExtra();
		$PaymentStripe=new CHBSPaymentStripe();
		$BookingStatus=new CHBSBookingStatus();
		$GoogleCalendar=new CHBSGoogleCalendar();
		$BookingGratuity=new CHBSBookingGratuity();
		$BookingFormStyle=new CHBSBookingFormStyle();
		
		$this->setPostMetaDefault($meta);
		
		/***/
		/***/
		
		$meta['service_type_id']=(array)CHBSHelper::getPostValue('service_type_id');
		foreach($meta['service_type_id'] as $index=>$value)
		{
			if(!$ServiceType->isServiceType($value))
				unset($meta['service_type_id'][$index]);
		}
		
		if(!count($meta['service_type_id']))
			$meta['service_type_id']=array(1);
		
		$meta['service_type_id_default']=(int)CHBSHelper::getPostValue('service_type_id_default');
		
		if(!in_array($meta['service_type_id_default'],$meta['service_type_id']))
			$meta['service_type_id_default']=reset($meta['service_type_id']);
		
		/***/
		
		$field=array('transfer_type_enable_1','transfer_type_enable_3');
		
		foreach($field as $fieldIndex)
		{
			$meta[$fieldIndex]=(array)CHBSHelper::getPostValue($fieldIndex);
			foreach($meta[$fieldIndex] as $index=>$value)
			{
				if(!$ServiceType->isServiceType($value))
					unset($meta[$fieldIndex][$index]);
			}		
			if(!count($meta[$fieldIndex]))
				$meta[$fieldIndex]=array(1); 
		}
		
		/***/
		
		$meta['transfer_type_list_item_empty_enable']=CHBSHelper::getPostValue('transfer_type_list_item_empty_enable');
		if(!$Validation->isBool($meta['transfer_type_list_item_empty_enable']))
			$meta['transfer_type_list_item_empty_enable']=0;		
		
		$meta['transfer_type_list_item_empty_text']=CHBSHelper::getPostValue('transfer_type_list_item_empty_text'); 
		
		/***/
		
		$meta['vehicle_category_id']=(array)CHBSHelper::getPostValue('vehicle_category_id');
		if(in_array(-1,$meta['vehicle_category_id']))
		{
			$meta['vehicle_category_id']=array(-1);
		}
		else
		{
			$category=$Vehicle->getCategory();
			foreach($meta['vehicle_category_id'] as $index=>$value)
			{
				if(!isset($category[$value]))
					unset($category[$value]);				
			}
		}
		
		if(!count($meta['vehicle_category_id']))
			$meta['vehicle_category_id']=array(-1);
			
		$meta['vehicle_id_default']=(int)CHBSHelper::getPostValue('vehicle_id_default');
		
		$meta['vehicle_select_enable']=(int)CHBSHelper::getPostValue('vehicle_select_enable');
		if(!$Validation->isBool($meta['vehicle_select_enable']))
			$meta['vehicle_select_enable']=1;		
		
		/***/
		
		$meta['vehicle_filter_enable']=(array)CHBSHelper::getPostValue('vehicle_filter_enable');
		foreach($meta['vehicle_filter_enable'] as $index=>$value)
		{
			if(!in_array($value,array(1,2,3,4)))
				unset($meta['vehicle_filter_enable'][$index]);
		}
		
		/***/  
		
		$meta['vehicle_sorting_type']=CHBSHelper::getPostValue('vehicle_sorting_type');
		if(!array_key_exists($meta['vehicle_sorting_type'],$this->vehicleSortingType))
			$meta['vehicle_sorting_type']=1;
		
		/***/	
			
		$meta['vehicle_pagination_vehicle_per_page']=(int)CHBSHelper::getPostValue('vehicle_pagination_vehicle_per_page');
		if(!$Validation->isNumber($meta['vehicle_pagination_vehicle_per_page'],1,99))
			$meta['vehicle_pagination_vehicle_per_page']=0;
 
		$meta['vehicle_limit']=(int)CHBSHelper::getPostValue('vehicle_limit');
		if(!$Validation->isNumber($meta['vehicle_limit'],1,99))
			$meta['vehicle_limit']=0;
		
		$meta['vehicle_bid_enable']=(int)CHBSHelper::getPostValue('vehicle_bid_enable');
		if(!$Validation->isBool($meta['vehicle_bid_enable']))
			$meta['vehicle_bid_enable']=0;		
		
		$meta['vehicle_bid_max_percentage_discount']=(int)CHBSHelper::getPostValue('vehicle_bid_max_percentage_discount');
		if(!$Validation->isFloat($meta['vehicle_bid_max_percentage_discount'],0,99.99))
			$meta['vehicle_bid_max_percentage_discount']=0;		
		
		/***/
		
		$meta['field_mandatory']=(array)CHBSHelper::getPostValue('field_mandatory');
		foreach($meta['field_mandatory'] as $index=>$value)
		{
			if(!array_key_exists($value,$this->fieldMandatory))
				unset($meta['field_mandatory'][$index]);
		}
		
		/***/
		
		$meta['step_third_enable']=(int)CHBSHelper::getPostValue('step_third_enable');
		if(!$Validation->isBool($meta['step_third_enable']))
			$meta['step_third_enable']=1;	
		
		/***/
		
		$meta['phone_number_iti_library_enable']=(int)CHBSHelper::getPostValue('phone_number_iti_library_enable');
		if(!$Validation->isBool($meta['phone_number_iti_library_enable']))
			$meta['phone_number_iti_library_enable']=1;			
		
		/***/
				
		$meta['route_id']=(array)CHBSHelper::getPostValue('route_id');
		if(in_array(-1,$meta['route_id']))
		{
			$meta['route_id']=array(-1);
		}
		else
		{
			$directory=$Route->getDictionary();
			foreach($meta['route_id'] as $index=>$value)
			{
				if(!isset($directory[$value]))
					unset($directory[$value]);				
			}
		}
		
		if(!count($meta['route_id']))
			$meta['route_id']=array(-1);		
		
		$meta['route_list_item_empty_enable']=CHBSHelper::getPostValue('route_list_item_empty_enable');
		if(!$Validation->isBool($meta['route_list_item_empty_enable']))
			$meta['route_list_item_empty_enable']=0;		
		
		$meta['route_list_item_empty_text']=CHBSHelper::getPostValue('route_list_item_empty_text'); 
		
		$meta['route_list_autocomplete_enable']=CHBSHelper::getPostValue('route_list_autocomplete_enable');
		if(!$Validation->isBool($meta['route_list_autocomplete_enable']))
			$meta['route_list_autocomplete_enable']=0;	
		
		$meta['route_pickup_location_field_enable']=CHBSHelper::getPostValue('route_pickup_location_field_enable');
		if(!$Validation->isBool($meta['route_pickup_location_field_enable']))
			$meta['route_pickup_location_field_enable']=0;			
		
		$meta['route_dropoff_location_field_enable']=CHBSHelper::getPostValue('route_dropoff_location_field_enable');
		if(!$Validation->isBool($meta['route_dropoff_location_field_enable']))
			$meta['route_dropoff_location_field_enable']=0;	
		
		/***/
		
		$meta['booking_extra_category_id']=(array)CHBSHelper::getPostValue('booking_extra_category_id');
		if(in_array(-1,$meta['booking_extra_category_id']))
		{
			$meta['booking_extra_category_id']=array(-1);
		}
		else if(in_array(-2,$meta['booking_extra_category_id']))
		{
			$meta['booking_extra_category_id']=array(-2);
		}		
		else
		{
			$category=$BookingExtra->getCategory();
			foreach($meta['booking_extra_category_id'] as $index=>$value)
			{
				if(!isset($category[$value]))
					unset($meta['booking_extra_category_id'][$index]);				
			}
		}
		
		if(!count($meta['booking_extra_category_id']))
			$meta['booking_extra_category_id']=array(-1);		  
		
		/***/
		
		$meta['booking_extra_category_display_enable']=CHBSHelper::getPostValue('booking_extra_category_display_enable');
		if(!$Validation->isBool($meta['booking_extra_category_display_enable']))
			$meta['booking_extra_category_display_enable']=0;
		
		/***/
		
		$meta['currency']=(array)CHBSHelper::getPostValue('currency');
		if(in_array(-1,$meta['currency']))
		{
			$meta['currency']=array(-1);
		}
		else
		{
			foreach($Currency->getCurrency() as $index=>$value)
			{
				if(!$Currency->isCurrency($index))
				{
					unset($meta['currency'][$index]);
				}
			}
		}
		
		if(!count($meta['currency']))
			$meta['currency']=array(-1);		 

		/***/

		$meta['extra_time_step']=CHBSHelper::getPostValue('extra_time_step');
		$meta['extra_time_enable']=CHBSHelper::getPostValue('extra_time_enable');
		$meta['extra_time_range_min']=CHBSHelper::getPostValue('extra_time_range_min');
		$meta['extra_time_range_max']=CHBSHelper::getPostValue('extra_time_range_max');
		$meta['extra_time_unit']=CHBSHelper::getPostValue('extra_time_unit');
		$meta['extra_time_mandatory']=CHBSHelper::getPostValue('extra_time_mandatory');
		
		if(!$Validation->isBool($meta['extra_time_enable']))
			$meta['extra_time_enable']=1;
		if(!$Validation->isNumber($meta['extra_time_range_min'],0,9999))
			$meta['extra_time_range_min']=0;	
		if(!$Validation->isNumber($meta['extra_time_range_max'],1,9999))
			$meta['extra_time_range_max']=24;			
		if(!$Validation->isNumber($meta['extra_time_step'],1,9999))
			$meta['extra_time_step']=1; 
		if(!in_array($meta['extra_time_unit'],array(1,2)))
			$meta['extra_time_unit']=2;
		if(!$Validation->isBool($meta['extra_time_mandatory']))
			$meta['extra_time_mandatory']=1;
		
		if(($meta['extra_time_range_min']>=$meta['extra_time_range_max']) || (!count(array_intersect(array(1,3),$meta['service_type_id']))))
		{
			$meta['extra_time_step']=1;
			$meta['extra_time_range_min']=0;
			$meta['extra_time_range_max']=24;
		}

		/***/
		
		$meta['duration_min']=CHBSHelper::getPostValue('duration_min');
		$meta['duration_max']=CHBSHelper::getPostValue('duration_max');		
		$meta['duration_step']=CHBSHelper::getPostValue('duration_step');	 

		if(!$Validation->isNumber($meta['duration_min'],1,9999))
			$meta['duration_min']=1;	
		if(!$Validation->isNumber($meta['duration_max'],1,9999))
			$meta['duration_max']=24;			
		if(!$Validation->isNumber($meta['duration_step'],1,9999))
			$meta['duration_step']=1;	   
		
		if(($meta['duration_min']>=$meta['duration_max']) || (!count(array_intersect(array(2),$meta['service_type_id']))))
		{
			$meta['duration_min']=1;
			$meta['duration_max']=24;
			$meta['duration_step']=1; 
		}	
		
		/***/
		
		$meta['duration_distance_min']=CHBSHelper::getPostValue('duration_distance_min');
		$meta['duration_distance_max']=CHBSHelper::getPostValue('duration_distance_max');		
		$meta['duration_distance_step']=CHBSHelper::getPostValue('duration_distance_step');	 
		$meta['duration_distance_list']=CHBSHelper::getPostValue('duration_distance_list');	 
			
		if(!$Validation->isNumber($meta['duration_distance_min'],1,9999))
			$meta['duration_distance_min']=0;	
		if(!$Validation->isNumber($meta['duration_distance_max'],1,9999))
			$meta['duration_distance_max']=0;			
		if(!$Validation->isNumber($meta['duration_distance_step'],1,9999))
			$meta['duration_distance_step']=1;	   
		
		if(($meta['duration_distance_min']>=$meta['duration_distance_max']) || (!count(array_intersect(array(2),$meta['service_type_id']))))
		{
			$meta['duration_distance_min']=0;
			$meta['duration_distance_max']=0;
			$meta['duration_distance_step']=1; 
		}	
		
		if(CHBSOption::getOption('length_unit')==2)
		{
			$meta['duration_distance_min']=$Length->convertUnit($meta['duration_distance_min'],2,1);
			$meta['duration_distance_max']=$Length->convertUnit($meta['duration_distance_max'],2,1);
			$meta['duration_distance_step']=$Length->convertUnit($meta['duration_distance_step'],2,1);
		}
		
		$durationDistanceListTarget=array();
		$durationDistanceListSource=preg_split('/;/',$meta['duration_distance_list']);
		
		foreach($durationDistanceListSource as $index=>$value)
		{
			if($Validation->isNumber($value,0,9999))
			{
				if(CHBSOption::getOption('length_unit')==2)
				{
					$value=$Length->convertUnit($value,2,1);
				}
				
				$durationDistanceListTarget[]=$value;	
			}
		}
		
		$meta['duration_distance_list']=join(';',$durationDistanceListTarget);
		
		$meta['duration_distance_included_enable']=CHBSHelper::getPostValue('duration_distance_included_enable');
		if(!$Validation->isBool($meta['duration_distance_included_enable']))
			$meta['duration_distance_included_enable']=0;	
		
		/***/
		
		$meta['waypoint_duration_enable']=CHBSHelper::getPostValue('waypoint_duration_enable');
		$meta['waypoint_duration_minimum_value']=CHBSHelper::getPostValue('waypoint_duration_minimum_value');		
		$meta['waypoint_duration_maximum_value']=CHBSHelper::getPostValue('waypoint_duration_maximum_value');
		$meta['waypoint_duration_step_value']=CHBSHelper::getPostValue('waypoint_duration_step_value');			

		if(!$Validation->isBool($meta['waypoint_duration_enable']))
			$meta['waypoint_duration_enable']=0;
		if(!$Validation->isNumber($meta['waypoint_duration_minimum_value'],0,9999))
			$meta['waypoint_duration_minimum_value']=1;	
		if(!$Validation->isNumber($meta['waypoint_duration_maximum_value'],0,9999))
			$meta['waypoint_duration_maximum_value']=24;			
		if(!$Validation->isNumber($meta['waypoint_duration_step_value'],1,9999))
			$meta['waypoint_duration_step_value']=1;	  		
		
		if($meta['waypoint_duration_minimum_value']>=$meta['waypoint_duration_maximum_value'])
		{
			$meta['waypoint_duration_minimum_value']=1;
			$meta['waypoint_duration_maximum_value']=24;
			$meta['waypoint_duration_step_value']=1; 
		}			
		
		/***/
		
		$meta['booking_period_from']=CHBSHelper::getPostValue('booking_period_from');
		if(!$Validation->isNumber($meta['booking_period_from'],0,9999))
			$meta['booking_period_from']='';		  
		$meta['booking_period_to']=CHBSHelper::getPostValue('booking_period_to');
		if(!$Validation->isNumber($meta['booking_period_to'],0,9999))
			$meta['booking_period_to']='';  
		$meta['booking_period_type']=CHBSHelper::getPostValue('booking_period_type');
		if(!in_array($meta['booking_period_type'],array(1,2,3)))
			$meta['booking_period_type']=1;		
		
		/***/
		
		$meta['booking_vehicle_interval']=CHBSHelper::getPostValue('booking_vehicle_interval');
		if(!$Validation->isNumber($meta['booking_vehicle_interval'],0,9999))
			$meta['booking_vehicle_interval']=0;			  
		
		/***/
		
		$meta['price_hide']=CHBSHelper::getPostValue('price_hide');
		if(!$Validation->isBool($meta['price_hide']))
			$meta['price_hide']=0;   
		
		/***/
		
		$meta['order_sum_split']=CHBSHelper::getPostValue('order_sum_split');
		if(!$Validation->isBool($meta['order_sum_split']))
			$meta['order_sum_split']=0;  
		
		/***/
		
		$meta['vehicle_sum_split']=CHBSHelper::getPostValue('vehicle_sum_split');
		
		if(!is_array($meta['vehicle_sum_split'])) $meta['vehicle_sum_split']=array(-1);
		
		foreach($meta['vehicle_sum_split'] as $index=>$value)
		{
			if(!in_array($value,array(0,2,3)))
				unset($meta['vehicle_sum_split'][$index]);
		}
		
		if(!count($meta['vehicle_sum_split'])) $meta['vehicle_sum_split']=array(-1);
		if(in_array(-1,$meta['vehicle_sum_split'])) $meta['vehicle_sum_split']=array(-1);
		
		/***/
		
		$meta['show_net_price_hide_tax']=CHBSHelper::getPostValue('show_net_price_hide_tax');
		if(!$Validation->isBool($meta['show_net_price_hide_tax']))
			$meta['show_net_price_hide_tax']=0; 
		
		$meta['show_price_before_discount']=CHBSHelper::getPostValue('show_price_before_discount');
		if(!$Validation->isBool($meta['show_price_before_discount']))
			$meta['show_price_before_discount']=0; 
		
		$meta['tax_rate_geofence_enable']=CHBSHelper::getPostValue('tax_rate_geofence_enable');
		if(!$Validation->isBool($meta['tax_rate_geofence_enable']))
			$meta['tax_rate_geofence_enable']=0; 
		
		/***/
		
		$meta['coupon_enable']=CHBSHelper::getPostValue('coupon_enable');
		if(!$Validation->isBool($meta['coupon_enable']))
			$meta['coupon_enable']=0;	
		
		$meta['coupon_discount_value_show']=CHBSHelper::getPostValue('coupon_discount_value_show');
		if(!$Validation->isBool($meta['coupon_discount_value_show']))
			$meta['coupon_discount_value_show']=0;
		
		/***/

		$meta['gratuity_enable']=CHBSHelper::getPostValue('gratuity_enable');
		if(!$Validation->isBool($meta['gratuity_enable']))
			$meta['gratuity_enable']=0;  

		$meta['gratuity_admin_type']=CHBSHelper::getPostValue('gratuity_admin_type');
		if(!$BookingGratuity->isType($meta['gratuity_admin_type']))
			$meta['gratuity_admin_type']=1;	
			
		$meta['gratuity_admin_value']=CHBSPrice::formatToSave(CHBSHelper::getPostValue('gratuity_admin_value'),false);
		if(!CHBSPrice::isPrice($meta['gratuity_admin_value']))
			$meta['gratuity_admin_value']=CHBSPrice::getDefaultPrice();
		$meta['gratuity_admin_value']=CHBSPrice::formatToSave($meta['gratuity_admin_value'],true);
		
		$meta['gratuity_customer_enable']=CHBSHelper::getPostValue('gratuity_customer_enable');
		if(!$Validation->isBool($meta['gratuity_customer_enable']))
			$meta['gratuity_customer_enable']=0;		  
		
		$meta['gratuity_customer_type']=(array)CHBSHelper::getPostValue('gratuity_customer_type');
		foreach($meta['gratuity_customer_type'] as $index=>$value)
		{
			if(!$BookingGratuity->isType($value))
				unset($meta['gratuity_customer_type'][$index]);
		}
  
		/***/
		
		$meta['vehicle_price_round']=CHBSPrice::formatToSave(CHBSHelper::getPostValue('vehicle_price_round'),false);   
		if(!$Validation->isFloat($meta['vehicle_price_round'],0.00,999999.99))
			$meta['vehicle_price_round']=CHBSPrice::getDefaultPrice();		 
		
		/***/
		
		$meta['booking_summary_hide_fee']=CHBSHelper::getPostValue('booking_summary_hide_fee');
		if(!in_array($meta['booking_summary_hide_fee'],array(0,1,2)))
			$meta['booking_summary_hide_fee']=0;		 
		
		/***/
		
		$meta['prevent_double_vehicle_booking_enable']=CHBSHelper::getPostValue('prevent_double_vehicle_booking_enable');
		if(!$Validation->isBool($meta['prevent_double_vehicle_booking_enable']))
			$meta['prevent_double_vehicle_booking_enable']=0;	   
		
		/***/
		
		$meta['vehicle_in_the_same_booking_passenger_sum_enable']=CHBSHelper::getPostValue('vehicle_in_the_same_booking_passenger_sum_enable');
		if(!$Validation->isBool($meta['vehicle_in_the_same_booking_passenger_sum_enable']))
			$meta['vehicle_in_the_same_booking_passenger_sum_enable']=0;	   
		
		/***/		
		
		$meta['step_fourth_enable']=CHBSHelper::getPostValue('step_fourth_enable');
		if(!$Validation->isBool($meta['step_fourth_enable']))
			$meta['step_fourth_enable']=1;
		
		/***/		
		
		$meta['step_second_enable']=CHBSHelper::getPostValue('step_second_enable');
		if(!$Validation->isBool($meta['step_second_enable']))
			$meta['step_second_enable']=1;
		
		/***/		
		
		$meta['thank_you_page_enable']=CHBSHelper::getPostValue('thank_you_page_enable');
		if(!$Validation->isBool($meta['thank_you_page_enable']))
			$meta['thank_you_page_enable']=1;		
		
		$meta['thank_you_page_button_back_to_home_label']=CHBSHelper::getPostValue('thank_you_page_button_back_to_home_label');
		$meta['thank_you_page_button_back_to_home_url_address']=CHBSHelper::getPostValue('thank_you_page_button_back_to_home_url_address');
		
		$meta['payment_disable_success_url_address']=CHBSHelper::getPostValue('payment_disable_success_url_address');
		
		/***/
		
		$meta['distance_minimum']=CHBSHelper::getPostValue('distance_minimum');		
		if(!$Validation->isNumber($meta['distance_minimum'],0,99999))
			$meta['distance_minimum']=0;	  
		if(CHBSOption::getOption('length_unit')==2)
			$meta['distance_minimum']=$Length->convertUnit($meta['distance_minimum'],2,1);
		
		$meta['distance_maximum']=CHBSHelper::getPostValue('distance_maximum');		
		if(!$Validation->isNumber($meta['distance_maximum'],0,99999))
			$meta['distance_maximum']=0;	  
		if(CHBSOption::getOption('length_unit')==2)
			$meta['distance_maximum']=$Length->convertUnit($meta['distance_maximum'],2,1);
		
		/***/
		
		$meta['duration_minimum']=CHBSHelper::getPostValue('duration_minimum');		
		if(!$Validation->isNumber($meta['duration_minimum'],0,999999999))
			$meta['duration_minimum']=0;	 
		
		$meta['duration_maximum']=CHBSHelper::getPostValue('duration_maximum');		
		if(!$Validation->isNumber($meta['duration_maximum'],0,999999999))
			$meta['duration_maximum']=0;	
		
		/***/
		
		$meta['order_value_minimum']=CHBSPrice::formatToSave(CHBSHelper::getPostValue('order_value_minimum'),false);		
		if(!CHBSPrice::isPrice($meta['order_value_minimum']))
			$meta['order_value_minimum']=CHBSPrice::getDefaultPrice();   
		$meta['order_value_minimum']=CHBSPrice::formatToSave($meta['order_value_minimum'],true);

		$meta['order_value_maximum']=CHBSPrice::formatToSave(CHBSHelper::getPostValue('order_value_maximum'),false);		
		if(!CHBSPrice::isPrice($meta['order_value_maximum']))
			$meta['order_value_maximum']=CHBSPrice::getDefaultPrice();   
		$meta['order_value_maximum']=CHBSPrice::formatToSave($meta['order_value_maximum'],true);
		
		/***/
		
		$meta['timepicker_step']=CHBSHelper::getPostValue('timepicker_step');
		if(!$Validation->isNumber($meta['timepicker_step'],1,9999))
			$meta['timepicker_step']=30;		   
		
		$meta['timepicker_dropdown_list_enable']=CHBSHelper::getPostValue('timepicker_dropdown_list_enable');
		if(!$Validation->isBool($meta['timepicker_dropdown_list_enable']))
			$meta['timepicker_dropdown_list_enable']=0;		  
		
		$meta['timepicker_today_start_time_type']=CHBSHelper::getPostValue('timepicker_today_start_time_type');
		if(!in_array($meta['timepicker_today_start_time_type'],array(1,2)))
			$meta['timepicker_today_start_time_type']=1;	
		
		$meta['timepicker_hour_range_enable']=CHBSHelper::getPostValue('timepicker_hour_range_enable');
		if(!$Validation->isBool($meta['timepicker_hour_range_enable']))
			$meta['timepicker_hour_range_enable']=0;		  		
		
		$meta['timepicker_field_readonly']=CHBSHelper::getPostValue('timepicker_field_readonly');
		if(!$Validation->isBool($meta['timepicker_field_readonly']))
			$meta['timepicker_field_readonly']=0;	
		
		$meta['form_preloader_enable']=CHBSHelper::getPostValue('form_preloader_enable');
		if(!$Validation->isBool($meta['form_preloader_enable']))
			$meta['form_preloader_enable']=0;		   

		$meta['form_preloader_image_src']=CHBSHelper::getPostValue('form_preloader_image_src');
		
		$meta['form_preloader_background_opacity']=CHBSHelper::getPostValue('form_preloader_background_opacity');
		if(!$Validation->isNumber($meta['form_preloader_background_opacity'],0,100))
			$meta['form_preloader_background_opacity']=20;
		
		$meta['form_preloader_background_color']=CHBSHelper::getPostValue('form_preloader_background_color');
		if(!$Validation->isColor($meta['form_preloader_background_color']))
			$meta['form_preloader_background_color']='FFFFFF';
			
		/***/
		
		$meta['billing_detail_state']=CHBSHelper::getPostValue('billing_detail_state');
		if(!in_array($meta['billing_detail_state'],array(1,2,3,4)))
			$meta['billing_detail_state']=1;   
		
		$meta['billing_detail_list_state']=CHBSHelper::getPostValue('billing_detail_list_state');
		
		/***/   
		
		$meta['booking_status_default_id']=CHBSHelper::getPostValue('booking_status_default_id');
		if(!$BookingStatus->isBookingStatus($meta['booking_status_default_id']))
			$meta['booking_status_default_id']=1;

		/***/
		
		$driverDictionary=$Driver->getDictionary();
		$meta['driver_default_id']=CHBSHelper::getPostValue('driver_default_id');
		if(!array_key_exists($meta['driver_default_id'],$driverDictionary))
			$meta['driver_default_id']=-1;

		/***/
		
		$meta['country_default']=CHBSHelper::getPostValue('country_default');
		if(!$Country->isCountry($meta['country_default']))
			$meta['country_default']=-1;
		
		/***/
		
		$meta['geolocation_server_side_enable']=CHBSHelper::getPostValue('geolocation_server_side_enable');
		if(!$Validation->isBool($meta['geolocation_server_side_enable']))
			$meta['geolocation_server_side_enable']=0;		
		
		/***/ 
		
		$meta['total_time_display_enable']=CHBSHelper::getPostValue('total_time_display_enable');
		if(!$Validation->isBool($meta['total_time_display_enable']))
			$meta['total_time_display_enable']=1;		
		
		/***/
	   
		$meta['summary_sidebar_sticky_enable']=CHBSHelper::getPostValue('summary_sidebar_sticky_enable');
		if(!$Validation->isBool($meta['summary_sidebar_sticky_enable']))
			$meta['summary_sidebar_sticky_enable']=0;
		
		/***/
		
		$meta['scroll_to_booking_extra_after_select_vehicle_enable']=CHBSHelper::getPostValue('scroll_to_booking_extra_after_select_vehicle_enable');
		if(!$Validation->isBool($meta['scroll_to_booking_extra_after_select_vehicle_enable']))
			$meta['scroll_to_booking_extra_after_select_vehicle_enable']=0;		
 
		/***/
		
		$meta['dropoff_location_field_enable']=CHBSHelper::getPostValue('dropoff_location_field_enable');
		if(!$Validation->isBool($meta['dropoff_location_field_enable']))
			$meta['dropoff_location_field_enable']=0;  
		
		/***/
		
		$meta['passenger_number_vehicle_list_enable']=CHBSHelper::getPostValue('passenger_number_vehicle_list_enable');
		if(!$Validation->isBool($meta['passenger_number_vehicle_list_enable']))
			$meta['passenger_number_vehicle_list_enable']=1;  
		
		/***/
		
		$meta['suitcase_number_vehicle_list_enable']=CHBSHelper::getPostValue('suitcase_number_vehicle_list_enable');
		if(!$Validation->isBool($meta['suitcase_number_vehicle_list_enable']))
			$meta['suitcase_number_vehicle_list_enable']=1;  
		
		/***/
		
		$meta['use_my_location_link_enable']=CHBSHelper::getPostValue('use_my_location_link_enable');
		if(!$Validation->isBool($meta['use_my_location_link_enable']))
			$meta['use_my_location_link_enable']=0;  
	
		/***/
		
		$meta['pickup_time_field_write_enable']=CHBSHelper::getPostValue('pickup_time_field_write_enable');
		if(!$Validation->isBool($meta['pickup_time_field_write_enable']))
			$meta['pickup_time_field_write_enable']=0;  
		
		$meta['vehicle_price_calculation_first_step_enable']=CHBSHelper::getPostValue('vehicle_price_calculation_first_step_enable');
		if(!$Validation->isBool($meta['vehicle_price_calculation_first_step_enable']))
			$meta['vehicle_price_calculation_first_step_enable']=0;  
		
		/***/
		
		$meta['booking_extra_button_toggle_visibility_enable']=CHBSHelper::getPostValue('booking_extra_button_toggle_visibility_enable');
		if(!$Validation->isBool($meta['booking_extra_button_toggle_visibility_enable']))
			$meta['booking_extra_button_toggle_visibility_enable']=0;  
		$meta['booking_extra_visibility_status']=CHBSHelper::getPostValue('booking_extra_visibility_status');
		if(!$Validation->isBool($meta['booking_extra_visibility_status']))
			$meta['booking_extra_visibility_status']=1;  
		$meta['booking_extra_note_display_enable']=CHBSHelper::getPostValue('booking_extra_note_display_enable');
		if(!$Validation->isBool($meta['booking_extra_note_display_enable']))
			$meta['booking_extra_note_display_enable']=0;  		
		$meta['booking_extra_note_mandatory_enable']=CHBSHelper::getPostValue('booking_extra_note_mandatory_enable');
		if(!$Validation->isBool($meta['booking_extra_note_mandatory_enable']))
			$meta['booking_extra_note_mandatory_enable']=0;  		
		
		$meta['booking_extra_price_change_return_percentage']=CHBSHelper::getPostValue('booking_extra_price_change_return_percentage');
		if(!$Validation->isFloat($meta['booking_extra_price_change_return_percentage'],0.00,9999.99))
			$meta['booking_extra_price_change_return_percentage']=0;  	
		else $meta['booking_extra_price_change_return_percentage']=CHBSPrice::formatToSave($meta['booking_extra_price_change_return_percentage']);  	

		$meta['booking_extra_price_change_return_new_ride_percentage']=CHBSHelper::getPostValue('booking_extra_price_change_return_new_ride_percentage');
		if(!$Validation->isFloat($meta['booking_extra_price_change_return_new_ride_percentage'],0.00,9999.99))
			$meta['booking_extra_price_change_return_new_ride_percentage']=0;  			
		else $meta['booking_extra_price_change_return_new_ride_percentage']=CHBSPrice::formatToSave($meta['booking_extra_price_change_return_new_ride_percentage']);  	
		
		/***/
		
		$meta['woocommerce_enable']=CHBSHelper::getPostValue('woocommerce_enable');
		if(!$Validation->isBool($meta['woocommerce_enable']))
			$meta['woocommerce_enable']=0;	   
	  
		/***/
		
		$meta['woocommerce_account_enable_type']=CHBSHelper::getPostValue('woocommerce_account_enable_type');
		if(!in_array($meta['woocommerce_account_enable_type'],array(0,1,2)))
			$meta['woocommerce_account_enable_type']=0;	   
		
		/***/
		
		$meta['woocommerce_add_to_cart_enable']=CHBSHelper::getPostValue('woocommerce_add_to_cart_enable');
		if(!$Validation->isBool($meta['woocommerce_add_to_cart_enable']))
			$meta['woocommerce_add_to_cart_enable']=0;	   
		
		$meta['woocommerce_add_to_cart_process_booking_enable']=CHBSHelper::getPostValue('woocommerce_add_to_cart_process_booking_enable');
		if(!$Validation->isBool($meta['woocommerce_add_to_cart_process_booking_enable']))
			$meta['woocommerce_add_to_cart_process_booking_enable']=0;	   
		
		$meta['woocommerce_add_to_cart_update_client_detail_enable']=CHBSHelper::getPostValue('woocommerce_add_to_cart_update_client_detail_enable');
		if(!$Validation->isBool($meta['woocommerce_add_to_cart_update_client_detail_enable']))
			$meta['woocommerce_add_to_cart_update_client_detail_enable']=0;	 
		
		$meta['woocommerce_add_to_cart_update_client_billing_details_enable']=CHBSHelper::getPostValue('woocommerce_add_to_cart_update_client_billing_details_enable');
		if(!$Validation->isBool($meta['woocommerce_add_to_cart_update_client_billing_details_enable']))
			$meta['woocommerce_add_to_cart_update_client_billing_details_enable']=0;	 
		
		/***/
		
		$wooCommerceProductCategory=$WooCommerce->getProductCategoryDictionary();
		$meta['woocommerce_add_to_cart_product_category']=CHBSHelper::getPostValue('woocommerce_add_to_cart_product_category');
		if(!((array_key_exists($meta['woocommerce_add_to_cart_product_category'],$wooCommerceProductCategory)) || (in_array($meta['woocommerce_add_to_cart_product_category'],array(-1,-2)))))
		{
			$meta['woocommerce_add_to_cart_product_category']=-1;
		}
		
		/***/

		$meta['passenger_adult_enable_service_type_1']=CHBSHelper::getPostValue('passenger_adult_enable_service_type_1');
		if(!$Validation->isBool($meta['passenger_adult_enable_service_type_1']))
			$meta['passenger_adult_enable_service_type_1']=0; 
		
		$meta['passenger_children_enable_service_type_1']=CHBSHelper::getPostValue('passenger_children_enable_service_type_1');
		if(!$Validation->isBool($meta['passenger_children_enable_service_type_1']))
			$meta['passenger_children_enable_service_type_1']=0; 
		
		$meta['passenger_adult_enable_service_type_2']=CHBSHelper::getPostValue('passenger_adult_enable_service_type_2');
		if(!$Validation->isBool($meta['passenger_adult_enable_service_type_2']))
			$meta['passenger_adult_enable_service_type_2']=0; 
		
		$meta['passenger_children_enable_service_type_2']=CHBSHelper::getPostValue('passenger_children_enable_service_type_2');
		if(!$Validation->isBool($meta['passenger_children_enable_service_type_2']))
			$meta['passenger_children_enable_service_type_2']=0; 
		
		$meta['passenger_adult_enable_service_type_3']=CHBSHelper::getPostValue('passenger_adult_enable_service_type_3');
		if(!$Validation->isBool($meta['passenger_adult_enable_service_type_3']))
			$meta['passenger_adult_enable_service_type_3']=0; 
		
		$meta['passenger_children_enable_service_type_3']=CHBSHelper::getPostValue('passenger_children_enable_service_type_3');
		if(!$Validation->isBool($meta['passenger_children_enable_service_type_3']))
			$meta['passenger_children_enable_service_type_3']=0;		 
		
		$meta['passenger_adult_default_number']=CHBSHelper::getPostValue('passenger_adult_default_number');
		if(!$Validation->isNumber($meta['passenger_adult_default_number'],0,99,true))
			$meta['passenger_adult_default_number']=0;
		$meta['passenger_children_default_number']=CHBSHelper::getPostValue('passenger_children_default_number'); 
		if(!$Validation->isNumber($meta['passenger_children_default_number'],0,99,true))
			$meta['passenger_children_default_number']=0;			 
  
		$meta['show_price_per_single_passenger']=CHBSHelper::getPostValue('show_price_per_single_passenger');
		if(!$Validation->isBool($meta['show_price_per_single_passenger']))
			$meta['show_price_per_single_passenger']=0;		 
		
		$meta['passenger_use_person_label']=CHBSHelper::getPostValue('passenger_use_person_label');
		if(!$Validation->isBool($meta['passenger_use_person_label']))
			$meta['passenger_use_person_label']=0; 
		
		$meta['passenger_number_dropdown_list_enable']=CHBSHelper::getPostValue('passenger_number_dropdown_list_enable');
		if(!$Validation->isBool($meta['passenger_number_dropdown_list_enable']))
			$meta['passenger_number_dropdown_list_enable']=0; 
		
		$meta['passenger_number_dropdown_list_display_type']=CHBSHelper::getPostValue('passenger_number_dropdown_list_display_type');
		if(!$Validation->isBool($meta['passenger_number_dropdown_list_display_type']))
			$meta['passenger_number_dropdown_list_display_type']=0; 
		
		/***/
		
		$calculationMethodValid=false;
		
		for($i=1;$i<=3;$i++)
		{
			$calculationMethodValid=false;
			
			$meta['calculation_method_service_type_'.$i]=(int)CHBSHelper::getPostValue('calculation_method_service_type_'.$i);
			
			foreach($this->calculationMethod as $index=>$value)
			{
				if($index===$meta['calculation_method_service_type_'.$i])
				{
					if(in_array($i,$value[1]))
					{
						$calculationMethodValid=true;
						break;
					}
				}
			}
			
			if(!$calculationMethodValid)
				$meta['calculation_method_service_type_'.$i]=($i==2 ? 5 : 1);
		}
		
		/***/
		
		$meta['base_location']=CHBSHelper::getPostValue('base_location');
		$meta['base_location_coordinate_lat']=CHBSHelper::getPostValue('base_location_coordinate_lat');
		$meta['base_location_coordinate_lng']=CHBSHelper::getPostValue('base_location_coordinate_lng');
		
		if($Validation->isEmpty($meta['base_location']))
		{
			$meta['base_location_coordinate_lat']='';
			$meta['base_location_coordinate_lng']='';
		}
		
		$meta['waypoint_enable']=CHBSHelper::getPostValue('waypoint_enable');
		if(!$Validation->isBool($meta['waypoint_enable']))
			$meta['waypoint_enable']=0;		  
		
		$locationDictionary=$Location->getDictionary();
		
		$field=array('location_fixed_pickup_service_type_1','location_fixed_dropoff_service_type_1','location_fixed_pickup_service_type_2','location_fixed_dropoff_service_type_2');
		
		foreach($field as $fieldName)
		{
			$meta[$fieldName]=(array)CHBSHelper::getPostValue($fieldName);
			foreach($meta[$fieldName] as $index=>$value)
			{
				if($value==-1)
				{
					$meta[$fieldName]=array(-1);
					break;
				}

				if(!array_key_exists($value,$locationDictionary))						
					unset($meta[$fieldName][$index]);
			}
		}	   
		
		$meta['location_fixed_list_item_empty_enable']=CHBSHelper::getPostValue('location_fixed_list_item_empty_enable');
		if(!$Validation->isBool($meta['location_fixed_list_item_empty_enable']))
			$meta['location_fixed_list_item_empty_enable']=0;		   
		
		$meta['location_fixed_list_item_empty_text']=CHBSHelper::getPostValue('location_fixed_list_item_empty_text');
		
		$meta['location_fixed_autocomplete_enable']=CHBSHelper::getPostValue('location_fixed_autocomplete_enable');
		if(!$Validation->isBool($meta['location_fixed_autocomplete_enable']))
			$meta['location_fixed_autocomplete_enable']=0;				
		
		$meta['ride_time_multiplier']=CHBSHelper::getPostValue('ride_time_multiplier');
		if(!$Validation->isFloat($meta['ride_time_multiplier'],0,99.99))
			$meta['ride_time_multiplier']=1.00;  
		else $meta['ride_time_multiplier']=number_format(preg_replace('/,/','.',$meta['ride_time_multiplier']),2,'.','');
			
		$meta['ride_time_rounding']=CHBSHelper::getPostValue('ride_time_rounding');
		if(!$Validation->isNumber($meta['ride_time_rounding'],1,60))
			$meta['ride_time_rounding']=1.00;   
		
		$meta['icon_field_enable']=CHBSHelper::getPostValue('icon_field_enable');
		if(!$Validation->isBool($meta['icon_field_enable']))
			$meta['icon_field_enable']=0;		 
		
		$meta['navigation_top_enable']=CHBSHelper::getPostValue('navigation_top_enable');
		if(!$Validation->isBool($meta['navigation_top_enable']))
			$meta['navigation_top_enable']=0;		

		$meta['service_tab_enable']=CHBSHelper::getPostValue('service_tab_enable');
		if(!$Validation->isBool($meta['service_tab_enable']))
			$meta['service_tab_enable']=0; 
		
		$meta['step_1_right_panel_visibility']=CHBSHelper::getPostValue('step_1_right_panel_visibility');
		if(!$Validation->isBool($meta['step_1_right_panel_visibility']))
			$meta['step_1_right_panel_visibility']=0;   
		
		$meta['vehicle_more_info_default_show']=CHBSHelper::getPostValue('vehicle_more_info_default_show');
		if(!$Validation->isBool($meta['vehicle_more_info_default_show']))
			$meta['vehicle_more_info_default_show']=0;				 
		
		$meta['booking_title']=CHBSHelper::getPostValue('booking_title');

		$meta['booking_form_post_id']=CHBSHelper::getPostValue('booking_form_post_id');
		if(!$Validation->isNumber($meta['booking_form_post_id'],1,999999999999))
			$meta['booking_form_post_id']=0;		
		
		
		$meta['google_recaptcha_enable']=CHBSHelper::getPostValue('google_recaptcha_enable');
		if(!$Validation->isBool($meta['google_recaptcha_enable']))
			$meta['google_recaptcha_enable']=0; 
		
		/***/
		/***/
		
		$businessHour=array();
		$businessHourPost=CHBSHelper::getPostValue('business_hour');

		foreach(array_keys($Date->day) as $index)
		{
			$businessHour[$index]=array('start'=>null,'stop'=>null,'default'=>null);
			
			$businessHourPost[$index][0]=$Date->formatTimeToStandard($businessHourPost[$index][0]);
			$businessHourPost[$index][1]=$Date->formatTimeToStandard($businessHourPost[$index][1]);
			$businessHourPost[$index][2]=$Date->formatTimeToStandard($businessHourPost[$index][2]);
			
			if((isset($businessHourPost[$index][0])) && (isset($businessHourPost[$index][1])))
			{
				if(($Validation->isTime($businessHourPost[$index][0],false)) && ($Validation->isTime($businessHourPost[$index][1],false)))
				{
					$result=$Date->compareTime($businessHourPost[$index][0],$businessHourPost[$index][1]);

					if($result==2)
					{
						$businessHour[$index]['start']=$businessHourPost[$index][0];
						$businessHour[$index]['stop']=$businessHourPost[$index][1];
					
						if(($Validation->isTime($businessHourPost[$index][2],false)))
						{
							if($Date->timeInRange($businessHourPost[$index][2],$businessHourPost[$index][0],$businessHourPost[$index][1]))
							{
								$businessHour[$index]['default']=$businessHourPost[$index][2];
							}
						}

						if((!$Validation->isTime($businessHour[$index]['default'])))
							$businessHour[$index]['default']=$businessHour[$index]['start'];
					}
				}
			}
		}

		$meta['business_hour']=$businessHour;

		/***/
		
		$dateExclude=array();
		$dateExcludePost=array();
		
		$dateExcludePostStart=CHBSHelper::getPostValue('date_exclude_start');
		$dateExcludePostStop=CHBSHelper::getPostValue('date_exclude_stop');
		
		foreach($dateExcludePostStart as $index=>$value)
		{
			if(isset($dateExcludePostStop[$index]))
				$dateExcludePost[]=array($dateExcludePostStart[$index],$dateExcludePostStop[$index]);
		}
	  
		foreach($dateExcludePost as $index=>$value)
		{
			$value[0]=$Date->formatDateToStandard($value[0]);
			$value[1]=$Date->formatDateToStandard($value[1]);
			
			if(!$Validation->isDate($value[0],true)) continue;
			if(!$Validation->isDate($value[1],true)) continue;

			if($Date->compareDate($value[0],$value[1])==1) continue;
			if($Date->compareDate(date_i18n('d-m-Y'),$value[1])==1) continue;
			
			$dateExclude[]=array('start'=>$value[0],'stop'=>$value[1]);
		}
		
		$meta['date_exclude']=$dateExclude;
		
		/***/
		
		$dictionaryVehicle=$Vehicle->getDictionary();
		
		$maximumBookingNumber=array();
		$maximumBookingNumberPost=CHBSHelper::getPostValue('maximum_booking_number');
	
		foreach($maximumBookingNumberPost['time_unit'] as $index=>$value)
		{
			$t=array($value,
				$maximumBookingNumberPost['date_start'][$index],
				$maximumBookingNumberPost['time_start'][$index],
				$maximumBookingNumberPost['date_stop'][$index],
				$maximumBookingNumberPost['time_stop'][$index],
				$maximumBookingNumberPost['week_day_number'][$index],
				$maximumBookingNumberPost['number'][$index]);
			
			if(!$this->isMaximumBookingNumberTimeUnit($t[0])) continue;
			
			if(in_array($value,array(1,3,5)))
			{
				$t[2]=$Date->formatTimeToStandard($t[2]);
				$t[4]=$Date->formatTimeToStandard($t[4]);
				
				if(!$Validation->isTime($t[2])) continue;
				if(!$Validation->isTime($t[4])) continue;
			}
			
			if(in_array($value,array(2,3)))
			{
				$t[1]=$Date->formatDateToStandard($t[1]);
				$t[3]=$Date->formatDateToStandard($t[3]);

				if(!$Validation->isDate($t[1])) continue;
				if(!$Validation->isDate($t[3])) continue;				
			}
			
			if(in_array($value,array(4)))
			{
				if(!in_array($t[5],array(1,2,3,4,5,6,7))) continue;
			}
			
			if(!$Validation->isNumber($t[6],-1,999999999)) continue;
			
			$s=array('time_unit'=>$value,'number'=>$t[6]);
			
			if(in_array($value,array(1,3,5)))
			{
				$s['time_start']=$t[2];
				$s['time_stop']=$t[4];
			}
			
			if(in_array($value,array(2,3)))
			{
				$s['date_start']=$t[1];
				$s['date_stop']=$t[3];
			}
			
			if(in_array($value,array(4)))
			{
				$s['week_day_number']=$t[5];
			}
		
			/**/
			
			$vehicle=array();
			if(count($dictionaryVehicle))
			{
				if((array_key_exists('vehicle',$maximumBookingNumberPost)) && (is_array($maximumBookingNumberPost['vehicle'])))
				{
					foreach($dictionaryVehicle as $vehicleIndex=>$vehicleValue)
					{
						$vehicle[$vehicleIndex]=-1;
						
						if((array_key_exists($vehicleIndex,$maximumBookingNumberPost['vehicle'])) && (array_key_exists($index,$maximumBookingNumberPost['vehicle'][$vehicleIndex]['number'])))
						{
							$vehicleBookingNumber=$maximumBookingNumberPost['vehicle'][$vehicleIndex]['number'][$index];
							
							if($Validation->isNumber($vehicleBookingNumber,-1,999999999))
							{
								$vehicle[$vehicleIndex]=$vehicleBookingNumber;
							}
						}
					}
				}
			}
			$s['vehicle']=$vehicle;
			
			/***/
			
			array_push($maximumBookingNumber,$s);
		}

		$meta['maximum_booking_number']=$maximumBookingNumber;
		
		/***/
		/***/
		
		$meta['payment_mandatory_enable']=CHBSHelper::getPostValue('payment_mandatory_enable');
		if(!$Validation->isBool($meta['payment_mandatory_enable']))
			$meta['payment_mandatory_enable']=0; 
		
		$meta['payment_processing_enable']=CHBSHelper::getPostValue('payment_processing_enable');
		if(!$Validation->isBool($meta['payment_processing_enable']))
			$meta['payment_processing_enable']=1;   
		
		$meta['payment_woocommerce_step_3_enable']=CHBSHelper::getPostValue('payment_woocommerce_step_3_enable');
		if(!$Validation->isBool($meta['payment_woocommerce_step_3_enable']))
			$meta['payment_woocommerce_step_3_enable']=1;			  
				
		$meta['payment_deposit_enable']=CHBSHelper::getPostValue('payment_deposit_enable');
		if(!$Validation->isBool($meta['payment_deposit_enable']))
			$meta['payment_deposit_enable']=0;		 
		
		$meta['payment_deposit_value']=CHBSHelper::getPostValue('payment_deposit_value');
		if(!$Validation->isNumber($meta['payment_deposit_value'],0,100))
			$meta['payment_deposit_value']=30;			 
		
		if($meta['payment_deposit_enable']==0)
			$meta['payment_deposit_value']=30;
		
		/***/
		
		$meta['payment_id']=(array)CHBSHelper::getPostValue('payment_id');
		foreach($meta['payment_id'] as $index=>$value)
		{
			if(!$Payment->isPayment($value))
				unset($meta['payment_id'][$value]);
		}
		
		$meta['payment_default_id']=(int)CHBSHelper::getPostValue('payment_default_id');
		if(!$Payment->isPayment($meta['payment_default_id']))
			$meta['payment_default_id']=-1;
		
		/**/
			
		$meta['payment_stripe_api_key_secret']=CHBSHelper::getPostValue('payment_stripe_api_key_secret');
		$meta['payment_stripe_api_key_publishable']=CHBSHelper::getPostValue('payment_stripe_api_key_publishable');		
		
		$meta['payment_stripe_method']=CHBSHelper::getPostValue('payment_stripe_method');
				
		if(is_array($meta['payment_stripe_method']))
		{
			foreach($meta['payment_stripe_method'] as $index=>$value)
			{
				if(!$PaymentStripe->isPaymentMethod($value))
					unset($meta['payment_stripe_method'][$index]);
			}
		}
		
		if((!is_array($meta['payment_stripe_method'])) || (!count($meta['payment_stripe_method'])))
			$meta['payment_stripe_method']=array('card');
				
		$meta['payment_stripe_product_id']=CHBSHelper::getPostValue('payment_stripe_product_id');
		
		$meta['payment_stripe_redirect_duration']=CHBSHelper::getPostValue('payment_stripe_redirect_duration');		
		if(!$Validation->isNumber($meta['payment_stripe_redirect_duration'],-1,99))
			$meta['payment_stripe_redirect_duration']=5;
		
		$meta['payment_stripe_success_url_address']=CHBSHelper::getPostValue('payment_stripe_success_url_address');
		$meta['payment_stripe_cancel_url_address']=CHBSHelper::getPostValue('payment_stripe_cancel_url_address');
		$meta['payment_stripe_logo_src']=CHBSHelper::getPostValue('payment_stripe_logo_src');
		$meta['payment_stripe_info']=CHBSHelper::getPostValue('payment_stripe_info');
		
		/**/
		
		$meta['payment_paypal_email_address']=CHBSHelper::getPostValue('payment_paypal_email_address');
		
		$meta['payment_paypal_sandbox_mode_enable']=CHBSHelper::getPostValue('payment_paypal_sandbox_mode_enable');
		if(!$Validation->isBool($meta['payment_paypal_sandbox_mode_enable']))
			$meta['payment_paypal_sandbox_mode_enable']=0;
		
		$meta['payment_paypal_redirect_duration']=CHBSHelper::getPostValue('payment_paypal_redirect_duration');
		if(!$Validation->isNumber($meta['payment_paypal_redirect_duration'],-1,99))
			$meta['payment_paypal_redirect_duration']=5;		

		$meta['payment_paypal_success_url_address']=CHBSHelper::getPostValue('payment_paypal_success_url_address');
		$meta['payment_paypal_cancel_url_address']=CHBSHelper::getPostValue('payment_paypal_cancel_url_address');		
		$meta['payment_paypal_logo_src']=CHBSHelper::getPostValue('payment_paypal_logo_src');
		$meta['payment_paypal_info']=CHBSHelper::getPostValue('payment_paypal_info');
		
		$meta['payment_tpay_client_id']=CHBSHelper::getPostValue('payment_tpay_client_id');
		$meta['payment_tpay_client_secret']=CHBSHelper::getPostValue('payment_tpay_client_secret');
		$meta['payment_tpay_notification_secret']=CHBSHelper::getPostValue('payment_tpay_notification_secret');
		
		$meta['payment_tpay_sandbox_mode_enable']=CHBSHelper::getPostValue('payment_tpay_sandbox_mode_enable');
		if(!$Validation->isBool($meta['payment_tpay_sandbox_mode_enable']))
			$meta['payment_tpay_sandbox_mode_enable']=0;
		
		$meta['payment_tpay_redirect_duration']=CHBSHelper::getPostValue('payment_tpay_redirect_duration');
		if(!$Validation->isNumber($meta['payment_tpay_redirect_duration'],-1,99))
			$meta['payment_tpay_redirect_duration']=5;
		
		$meta['payment_tpay_success_url_address']=CHBSHelper::getPostValue('payment_tpay_success_url_address');
		$meta['payment_tpay_cancel_url_address']=CHBSHelper::getPostValue('payment_tpay_cancel_url_address');
		$meta['payment_tpay_logo_src']=CHBSHelper::getPostValue('payment_tpay_logo_src');
		$meta['payment_tpay_info']=CHBSHelper::getPostValue('payment_tpay_info');
		
		/**/
		
		$meta['payment_cash_success_url_address']=CHBSHelper::getPostValue('payment_cash_success_url_address');
		$meta['payment_cash_logo_src']=CHBSHelper::getPostValue('payment_cash_logo_src');
		$meta['payment_cash_info']=CHBSHelper::getPostValue('payment_cash_info');
	
		/**/
		
		$meta['payment_wire_transfer_success_url_address']=CHBSHelper::getPostValue('payment_wire_transfer_success_url_address');
		$meta['payment_wire_transfer_logo_src']=CHBSHelper::getPostValue('payment_wire_transfer_logo_src');
		$meta['payment_wire_transfer_info']=CHBSHelper::getPostValue('payment_wire_transfer_info');

		/***/
		
		$meta['payment_credit_card_pickup_success_url_address']=CHBSHelper::getPostValue('payment_credit_card_pickup_success_url_address');
		$meta['payment_credit_card_pickup_logo_src']=CHBSHelper::getPostValue('payment_credit_card_pickup_logo_src');
		$meta['payment_credit_card_pickup_info']=CHBSHelper::getPostValue('payment_credit_card_pickup_info');
		
		/***/
		
		$field=array('pickup','waypoint','dropoff');
		
		foreach($field as $fieldName)
		{
			$meta['driving_zone_restriction_'.$fieldName.'_location_enable']=CHBSHelper::getPostValue('driving_zone_restriction_'.$fieldName.'_location_enable');
			if(!$Validation->isBool($meta['driving_zone_restriction_'.$fieldName.'_location_enable']))
				$meta['driving_zone_restriction_'.$fieldName.'_location_enable']=0;			
		
			$meta['driving_zone_restriction_'.$fieldName.'_location_country']=(array)CHBSHelper::getPostValue('driving_zone_restriction_'.$fieldName.'_location_country');
			foreach($meta['driving_zone_restriction_'.$fieldName.'_location_country'] as $index=>$value)
			{
				if($value==-1)
				{
					$meta['driving_zone_restriction_'.$fieldName.'_location_country']=array(-1);
					break;
				}

				if(!$Country->isCountry($value))
					unset($meta['driving_zone_restriction_'.$fieldName.'_location_country'][$index]);
			}
			
			if(!count($meta['driving_zone_restriction_'.$fieldName.'_location_country']))
				$meta['driving_zone_restriction_'.$fieldName.'_location_country']=array(-1);
			
			$meta['driving_zone_restriction_'.$fieldName.'_location_area_radius']=CHBSLength::formatToSave(CHBSHelper::getPostValue('driving_zone_restriction_'.$fieldName.'_location_area_radius'),false,-1);
			if(!$Validation->isFloat($meta['driving_zone_restriction_'.$fieldName.'_location_area_radius'],0,99999.99,false,1))
				$meta['driving_zone_restriction_'.$fieldName.'_location_area_radius']=((int)CHBSOption::getOption('length_unit')===1 ? 50 : 80.4672);
			
			if(CHBSOption::getOption('length_unit')==2)
				$meta['driving_zone_restriction_'.$fieldName.'_location_area_radius']=$Length->convertUnit($meta['driving_zone_restriction_'.$fieldName.'_location_area_radius'],2,1);
			
			$meta['driving_zone_restriction_'.$fieldName.'_location_area_radius']=CHBSLength::formatToSave($meta['driving_zone_restriction_'.$fieldName.'_location_area_radius'],false,1);
			
			$meta['driving_zone_restriction_'.$fieldName.'_location_area']=CHBSHelper::getPostValue('driving_zone_restriction_'.$fieldName.'_location_area');
			$meta['driving_zone_restriction_'.$fieldName.'_location_area_coordinate_lat']=CHBSHelper::getPostValue('driving_zone_restriction_'.$fieldName.'_location_area_coordinate_lat');
			$meta['driving_zone_restriction_'.$fieldName.'_location_area_coordinate_lng']=CHBSHelper::getPostValue('driving_zone_restriction_'.$fieldName.'_location_area_coordinate_lng');
		}
		
		$meta['driving_zone_restriction_location_field_relation_type']=CHBSHelper::getPostValue('driving_zone_restriction_location_field_relation_type');
		if(!in_array($meta['driving_zone_restriction_location_field_relation_type'],array(1,2)))
			$meta['driving_zone_restriction_location_field_relation_type']=1;	
		
		/***/
		
		$locationReplace=array();
		
		$dictionaryGeofence=$Geofence->getDictionary();
		
		$meta['location_replace']=(array)CHBSHelper::getPostValue('location_replace');
		foreach($meta['location_replace']['geofence'] as $index=>$value)
		{
			$d=array($value,$meta['location_replace']['location']['address']['name'][$index],$meta['location_replace']['location']['address']['coordinate_lat'][$index],$meta['location_replace']['location']['address']['coordinate_lng'][$index]);

			if(!array_key_exists($d[0],$dictionaryGeofence)) continue;
			
			if($Validation->isEmpty($d[1])) continue;
			if($Validation->isEmpty($d[2])) continue;
			if($Validation->isEmpty($d[3])) continue;
			
			$locationReplace[]=array('geofence_id'=>$d[0],'location'=>array('name'=>$d[1],'coordinate'=>array('lat'=>$d[2],'lng'=>$d[3])));
		}

		$meta['location_replace']=$locationReplace;
		
		/***/

		$pickupTimeGeofence=array();

		$meta['pickup_time_geofence']=(array)CHBSHelper::getPostValue('pickup_time_geofence');
		if(array_key_exists('geofence',$meta['pickup_time_geofence']))
		{
			foreach($meta['pickup_time_geofence']['geofence'] as $index=>$value)
			{
				$time=$meta['pickup_time_geofence']['time'][$index];

				if(!array_key_exists($value,$dictionaryGeofence)) continue;
				if($Validation->isEmpty($time)) continue;

				$pickupTimeGeofence[]=array('geofence_id'=>$value,'time'=>$time);
			}
		}

		$meta['pickup_time_geofence']=$pickupTimeGeofence;

		/***/
		/***/
				
		$FormElement=new CHBSBookingFormElement();
		$FormElement->save($postId);		
		
		/***/
		/***/
		
		$dictionary=$EmailAccount->getDictionary();
		$meta['booking_new_sender_email_account_id']=CHBSHelper::getPostValue('booking_new_sender_email_account_id');
		
		if(!array_key_exists($meta['booking_new_sender_email_account_id'],$dictionary))
			$meta['booking_new_sender_email_account_id']=-1;
		
		$meta['booking_new_recipient_email_address']='';
		$recipient=preg_split('/;/',CHBSHelper::getPostValue('booking_new_recipient_email_address'));
		
		foreach($recipient as $index=>$value)
		{
			if($Validation->isEmailAddress($value))
			{
				if($Validation->isNotEmpty($meta['booking_new_recipient_email_address'])) $meta['booking_new_recipient_email_address'].=';';
				$meta['booking_new_recipient_email_address'].=$value;
			}
		} 
		
		$meta['email_notification_booking_new_client_enable']=CHBSHelper::getPostValue('email_notification_booking_new_client_enable');
		if(!$Validation->isBool($meta['email_notification_booking_new_client_enable']))
			$meta['email_notification_booking_new_client_enable']=1;
		
		$meta['email_notification_booking_new_client_payment_success_enable']=CHBSHelper::getPostValue('email_notification_booking_new_client_payment_success_enable');
		if(!$Validation->isBool($meta['email_notification_booking_new_client_payment_success_enable']))
			$meta['email_notification_booking_new_client_payment_success_enable']=1;		
		
		$meta['email_notification_booking_new_admin_enable']=CHBSHelper::getPostValue('email_notification_booking_new_admin_enable');
		if(!$Validation->isBool($meta['email_notification_booking_new_admin_enable']))
			$meta['email_notification_booking_new_admin_enable']=0;
		
		$meta['email_notification_booking_new_admin_payment_success_enable']=CHBSHelper::getPostValue('email_notification_booking_new_admin_payment_success_enable');
		if(!$Validation->isBool($meta['email_notification_booking_new_admin_payment_success_enable']))
			$meta['email_notification_booking_new_admin_payment_success_enable']=0;
		
		/***/
		
		$meta['nexmo_sms_enable']=CHBSHelper::getPostValue('nexmo_sms_enable');
		if(!$Validation->isBool($meta['nexmo_sms_enable']))
			$meta['nexmo_sms_enable']=0;
		
		$meta['nexmo_sms_api_key']=CHBSHelper::getPostValue('nexmo_sms_api_key');
		$meta['nexmo_sms_api_key_secret']=CHBSHelper::getPostValue('nexmo_sms_api_key_secret');
		
		$meta['nexmo_sms_sender_name']=CHBSHelper::getPostValue('nexmo_sms_sender_name');
		$meta['nexmo_sms_recipient_phone_number']=CHBSHelper::getPostValue('nexmo_sms_recipient_phone_number');
		
		$meta['nexmo_sms_message']=CHBSHelper::getPostValue('nexmo_sms_message');
		
		/***/
		
		$meta['twilio_sms_enable']=CHBSHelper::getPostValue('twilio_sms_enable');
		if(!$Validation->isBool($meta['twilio_sms_enable']))
			$meta['twilio_sms_enable']=0;
		
		$meta['twilio_sms_api_sid']=CHBSHelper::getPostValue('twilio_sms_api_sid');
		$meta['twilio_sms_api_token']=CHBSHelper::getPostValue('twilio_sms_api_token');
		
		$meta['twilio_sms_sender_phone_number']=CHBSHelper::getPostValue('twilio_sms_sender_phone_number');
		$meta['twilio_sms_recipient_phone_number']=CHBSHelper::getPostValue('twilio_sms_recipient_phone_number');
		
		$meta['twilio_sms_message']=CHBSHelper::getPostValue('twilio_sms_message');
		
		/***/
		
		$meta['customer_sms_enable']=CHBSHelper::getPostValue('customer_sms_enable');
		if(!in_array($meta['customer_sms_enable'],array(0,1,2)))
			$meta['customer_sms_enable']=0;
		
		$meta['customer_sms_message']=CHBSHelper::getPostValue('customer_sms_message');
		
		/***/
		
		$meta['telegram_enable']=CHBSHelper::getPostValue('telegram_enable');
		if(!$Validation->isBool($meta['telegram_enable']))
			$meta['telegram_enable']=0;
		
		$meta['telegram_token']=CHBSHelper::getPostValue('telegram_token');
		$meta['telegram_group_id']=CHBSHelper::getPostValue('telegram_group_id');
		$meta['telegram_message']=CHBSHelper::getPostValue('telegram_message');
				
		/***/
		/***/
		
		$GoogleMap=new CHBSGoogleMap();
		
		$meta['google_map_map_id']=CHBSHelper::getPostValue('google_map_map_id');

		$meta['google_map_default_location_type']=CHBSHelper::getPostValue('google_map_default_location_type');
		if(!in_array($meta['google_map_default_location_type'],array(1,2)))
			$meta['google_map_default_location_type']=1;	   
		
		$meta['google_map_default_location_fixed']=CHBSHelper::getPostValue('google_map_default_location_fixed');
		$meta['google_map_default_location_fixed_coordinate_lat']=CHBSHelper::getPostValue('google_map_default_location_fixed_coordinate_lat');
		$meta['google_map_default_location_fixed_coordinate_lng']=CHBSHelper::getPostValue('google_map_default_location_fixed_coordinate_lng');
		
		$meta['google_map_route_avoid']=(array)CHBSHelper::getPostValue('google_map_route_avoid');
		if(in_array(-1,$meta['google_map_route_avoid']))
		{
			$meta['google_map_route_avoid']=array(-1);
		}
		else
		{
			$avoid=$GoogleMap->getRouteAvoid();
			foreach($meta['google_map_route_avoid'] as $index=>$value)
			{
				if(!isset($avoid[$value]))
					unset($meta['google_map_route_avoid'][$value]);				
			}
		}
		
		$meta['google_map_route_type']=CHBSHelper::getPostValue('google_map_route_type');
		if(!in_array($meta['google_map_route_type'],array(1,2)))
			$meta['google_map_route_type']=1;
		
		$meta['google_map_traffic_layer_enable']=CHBSHelper::getPostValue('google_map_traffic_layer_enable');  
		$meta['google_map_draggable_enable']=CHBSHelper::getPostValue('google_map_draggable_enable');  
		
		if(!$Validation->isBool($meta['google_map_traffic_layer_enable']))
			$meta['google_map_traffic_layer_enable']=0;	 
		if(!$Validation->isBool($meta['google_map_draggable_enable']))
			$meta['google_map_draggable_enable']=1;					 

		/***/
		
		$meta['google_map_map_type_control_enable']=CHBSHelper::getPostValue('google_map_map_type_control_enable');  
		$meta['google_map_map_type_control_id']=CHBSHelper::getPostValue('google_map_map_type_control_id'); 
		$meta['google_map_map_type_control_style']=CHBSHelper::getPostValue('google_map_map_type_control_style'); 
		$meta['google_map_map_type_control_position']=CHBSHelper::getPostValue('google_map_map_type_control_position');  
		
		if(!$Validation->isBool($meta['google_map_map_type_control_enable']))
			$meta['google_map_map_type_control_enable']=0;   
		if(!array_key_exists($meta['google_map_map_type_control_id'],$GoogleMap->getMapTypeControlId()))
			$meta['google_map_map_type_control_id']='SATELLITE';		
		if(!array_key_exists($meta['google_map_map_type_control_style'],$GoogleMap->getMapTypeControlStyle()))
			$meta['google_map_map_type_control_style']='DEFAULT';		 
		if(!array_key_exists($meta['google_map_map_type_control_position'],$GoogleMap->getPosition()))
			$meta['google_map_map_type_control_position']='TOP_CENTER';
		
		/***/
		
		$meta['google_map_zoom_control_enable']=CHBSHelper::getPostValue('google_map_zoom_control_enable');  
		$meta['google_map_zoom_control_position']=CHBSHelper::getPostValue('google_map_zoom_control_position');  
		
		if(!$Validation->isBool($meta['google_map_zoom_control_enable']))
			$meta['google_map_zoom_control_enable']=0;   
		if(!array_key_exists($meta['google_map_zoom_control_position'],$GoogleMap->getPosition()))
			$meta['google_map_zoom_control_position']='TOP_CENTER';		
		
		/***/
		/***/
		
		$meta['google_calendar_enable']=CHBSHelper::getPostValue('google_calendar_enable');  
		$meta['google_calendar_id']=CHBSHelper::getPostValue('google_calendar_id');  
		$meta['google_calendar_settings']=CHBSHelper::getPostValue('google_calendar_settings');  
		$meta['google_calendar_regenerate_token_enable']=CHBSHelper::getPostValue('google_calendar_regenerate_token_enable');  
		$meta['google_calendar_add_event_action']=CHBSHelper::getPostValue('google_calendar_add_event_action');  
		$meta['google_calendar_event_title']=CHBSHelper::getPostValue('google_calendar_event_title');
		$meta['google_calendar_event_description']=CHBSHelper::getPostValue('google_calendar_event_description');
		
		if(!$Validation->isBool($meta['google_calendar_enable']))
			$meta['google_calendar_enable']=0;		
		
		if(!$Validation->isBool($meta['google_calendar_regenerate_token_enable']))
			$meta['google_calendar_regenerate_token_enable']=0;			
		
		if(!$GoogleCalendar->isAddEventAction($meta['google_calendar_add_event_action']))
			$meta['google_calendar_add_event_action']=$GoogleCalendar->getDefaultAddEventAction();
		
		/***/
		
		$meta['style_color']=(array)CHBSHelper::getPostValue('style_color');   
		foreach($meta['style_color'] as $index=>$value)
		{
			if(!$BookingFormStyle->isColor($index))
			{
				unset($meta['style_color'][$index]);
				continue;
			}
			
			if(!$Validation->isColor($value,true))
				$meta['style_color'][$index]='';
		}
		
		/***/
		/***/

		foreach($meta as $index=>$value)
			CHBSPostMeta::updatePostMeta($postId,$index,$value);	
		
		$BookingFormStyle->createCSSFile();
	}
	
	/**************************************************************************/
	
	function setPostMetaDefault(&$meta)
	{
		$BookingFormStyle=new CHBSBookingFormStyle();
		
		CHBSHelper::setDefault($meta,'service_type_id',array(1,2,3));
		CHBSHelper::setDefault($meta,'service_type_id_default',1);
		
		CHBSHelper::setDefault($meta,'transfer_type_enable_1',array(1,2,3));
		CHBSHelper::setDefault($meta,'transfer_type_enable_3',array(1,2,3));
		CHBSHelper::setDefault($meta,'transfer_type_list_item_empty_enable',0);
		CHBSHelper::setDefault($meta,'transfer_type_list_item_empty_text','');		
		
		CHBSHelper::setDefault($meta,'vehicle_category_id',array(-1));
		CHBSHelper::setDefault($meta,'vehicle_id_default',-1);	
		CHBSHelper::setDefault($meta,'vehicle_select_enable',1);  
		CHBSHelper::setDefault($meta,'vehicle_filter_enable',array(1,2,4));
		CHBSHelper::setDefault($meta,'vehicle_sorting_type',0);
		CHBSHelper::setDefault($meta,'vehicle_pagination_vehicle_per_page',0);
		CHBSHelper::setDefault($meta,'vehicle_limit',0);
		CHBSHelper::setDefault($meta,'vehicle_bid_enable',0);
		CHBSHelper::setDefault($meta,'vehicle_bid_max_percentage_discount',0);
		
		CHBSHelper::setDefault($meta,'route_id',array(-1));
		CHBSHelper::setDefault($meta,'route_list_item_empty_enable',0);
		CHBSHelper::setDefault($meta,'route_list_item_empty_text','');
		CHBSHelper::setDefault($meta,'route_list_autocomplete_enable',0);
		CHBSHelper::setDefault($meta,'route_pickup_location_field_enable',0);
		CHBSHelper::setDefault($meta,'route_dropoff_location_field_enable',0);
		
		CHBSHelper::setDefault($meta,'booking_extra_category_id',array(-1));
		
		CHBSHelper::setDefault($meta,'booking_extra_category_display_enable',0);

		CHBSHelper::setDefault($meta,'currency',array(-1));
		
		CHBSHelper::setDefault($meta,'extra_time_enable',1);
		CHBSHelper::setDefault($meta,'extra_time_range_min',0);
		CHBSHelper::setDefault($meta,'extra_time_range_max',24);
		CHBSHelper::setDefault($meta,'extra_time_step',1);
		CHBSHelper::setDefault($meta,'extra_time_unit',2);
		CHBSHelper::setDefault($meta,'extra_time_mandatory',0);
		
		CHBSHelper::setDefault($meta,'duration_min',1);
		CHBSHelper::setDefault($meta,'duration_max',24);
		CHBSHelper::setDefault($meta,'duration_step',1);
		
		CHBSHelper::setDefault($meta,'duration_distance_min',0);
		CHBSHelper::setDefault($meta,'duration_distance_max',0);
		CHBSHelper::setDefault($meta,'duration_distance_step',1);
		CHBSHelper::setDefault($meta,'duration_distance_list','');
		CHBSHelper::setDefault($meta,'duration_distance_included_enable',0);
		
		CHBSHelper::setDefault($meta,'waypoint_duration_enable',0);
		CHBSHelper::setDefault($meta,'waypoint_duration_minimum_value',1);
		CHBSHelper::setDefault($meta,'waypoint_duration_maximum_value',60);
		CHBSHelper::setDefault($meta,'waypoint_duration_step_value',1);
		
		CHBSHelper::setDefault($meta,'booking_period_from','');
		CHBSHelper::setDefault($meta,'booking_period_to','');
		CHBSHelper::setDefault($meta,'booking_period_type',1);
		
		CHBSHelper::setDefault($meta,'booking_vehicle_interval',0);
		
		CHBSHelper::setDefault($meta,'booking_summary_hide_fee',2);
		CHBSHelper::setDefault($meta,'price_hide',0);	   
		CHBSHelper::setDefault($meta,'order_sum_split',0);	   
		CHBSHelper::setDefault($meta,'vehicle_sum_split',array(-1));	   
		
		CHBSHelper::setDefault($meta,'show_net_price_hide_tax',0);  
		CHBSHelper::setDefault($meta,'show_price_before_discount',0);  
		
		CHBSHelper::setDefault($meta,'tax_rate_geofence_enable',0);  
		
		CHBSHelper::setDefault($meta,'coupon_enable',0);
		CHBSHelper::setDefault($meta,'coupon_discount_value_show',0);
		
		CHBSHelper::setDefault($meta,'gratuity_enable',0);
		CHBSHelper::setDefault($meta,'gratuity_admin_type',1);
		CHBSHelper::setDefault($meta,'gratuity_admin_value','');
		CHBSHelper::setDefault($meta,'gratuity_customer_enable',0);
		CHBSHelper::setDefault($meta,'gratuity_customer_type',array(1));
		
		CHBSHelper::setDefault($meta,'vehicle_price_round','');
		
		CHBSHelper::setDefault($meta,'prevent_double_vehicle_booking_enable',0);
		CHBSHelper::setDefault($meta,'vehicle_in_the_same_booking_passenger_sum_enable',0);
		
		CHBSHelper::setDefault($meta,'step_fourth_enable',1);
		CHBSHelper::setDefault($meta,'step_second_enable',1);
		CHBSHelper::setDefault($meta,'thank_you_page_enable',1);
		CHBSHelper::setDefault($meta,'thank_you_page_button_back_to_home_label',__('Back To Home','chauffeur-booking-system'));
		CHBSHelper::setDefault($meta,'thank_you_page_button_back_to_home_url_address','');
		CHBSHelper::setDefault($meta,'payment_disable_success_url_address','');
		
		CHBSHelper::setDefault($meta,'distance_minimum',0);
		CHBSHelper::setDefault($meta,'distance_maximum',0);
		CHBSHelper::setDefault($meta,'duration_minimum',0);
		CHBSHelper::setDefault($meta,'duration_maximum',0);
		CHBSHelper::setDefault($meta,'order_value_minimum',CHBSPrice::getDefaultPrice());
		CHBSHelper::setDefault($meta,'order_value_maximum',CHBSPrice::getDefaultPrice());
		
		CHBSHelper::setDefault($meta,'timepicker_step',30);
		CHBSHelper::setDefault($meta,'timepicker_dropdown_list_enable',1);
		CHBSHelper::setDefault($meta,'timepicker_today_start_time_type',1);
		CHBSHelper::setDefault($meta,'timepicker_hour_range_enable',0);
		CHBSHelper::setDefault($meta,'timepicker_field_readonly',0);
		
		CHBSHelper::setDefault($meta,'form_preloader_enable',1);
		CHBSHelper::setDefault($meta,'form_preloader_image_src','');
		CHBSHelper::setDefault($meta,'form_preloader_background_opacity',20);
		CHBSHelper::setDefault($meta,'form_preloader_background_color','FFFFFF');
		
		CHBSHelper::setDefault($meta,'billing_detail_state',1);
		CHBSHelper::setDefault($meta,'billing_detail_list_state','');	   
		
		CHBSHelper::setDefault($meta,'booking_status_default_id',1);
		
		CHBSHelper::setDefault($meta,'driver_default_id',-1);
		CHBSHelper::setDefault($meta,'country_default',-1);
		
		CHBSHelper::setDefault($meta,'geolocation_server_side_enable',1);
		
		CHBSHelper::setDefault($meta,'total_time_display_enable',1);
		
		CHBSHelper::setDefault($meta,'summary_sidebar_sticky_enable',0);
		
		CHBSHelper::setDefault($meta,'scroll_to_booking_extra_after_select_vehicle_enable',1);
		
		CHBSHelper::setDefault($meta,'dropoff_location_field_enable',1);
		
		CHBSHelper::setDefault($meta,'passenger_number_vehicle_list_enable',1);
		CHBSHelper::setDefault($meta,'suitcase_number_vehicle_list_enable',1);
		CHBSHelper::setDefault($meta,'use_my_location_link_enable',0);
		CHBSHelper::setDefault($meta,'pickup_time_field_write_enable',1);
		CHBSHelper::setDefault($meta,'vehicle_price_calculation_first_step_enable',0);
		
		CHBSHelper::setDefault($meta,'booking_extra_button_toggle_visibility_enable',0);
		CHBSHelper::setDefault($meta,'booking_extra_visibility_status',1);
		
		CHBSHelper::setDefault($meta,'booking_extra_note_display_enable',0);
		CHBSHelper::setDefault($meta,'booking_extra_note_mandatory_enable',0);
		
		CHBSHelper::setDefault($meta,'booking_extra_price_change_return_percentage',100);
		CHBSHelper::setDefault($meta,'booking_extra_price_change_return_new_ride_percentage',100);

		$fieldMandatory=array();
		foreach($this->fieldMandatory as $index=>$value)
		{
			if((int)$value['mandatory']===1)
				$fieldMandatory[]=$index;
		}	
		
		CHBSHelper::setDefault($meta,'field_mandatory',$fieldMandatory);
		
		CHBSHelper::setDefault($meta,'woocommerce_enable',0);
		CHBSHelper::setDefault($meta,'woocommerce_account_enable_type',1);
		CHBSHelper::setDefault($meta,'woocommerce_add_to_cart_enable',0);
		CHBSHelper::setDefault($meta,'woocommerce_add_to_cart_process_booking_enable',0);
		CHBSHelper::setDefault($meta,'woocommerce_add_to_cart_update_client_detail_enable',0);
		CHBSHelper::setDefault($meta,'woocommerce_add_to_cart_update_client_billing_details_enable',0);
		CHBSHelper::setDefault($meta,'woocommerce_add_to_cart_product_category',-1);
		
		CHBSHelper::setDefault($meta,'passenger_adult_enable_service_type_1',0);
		CHBSHelper::setDefault($meta,'passenger_children_enable_service_type_1',0);
		CHBSHelper::setDefault($meta,'passenger_adult_enable_service_type_2',0);
		CHBSHelper::setDefault($meta,'passenger_children_enable_service_type_2',0);
		CHBSHelper::setDefault($meta,'passenger_adult_enable_service_type_3',0);
		CHBSHelper::setDefault($meta,'passenger_children_enable_service_type_3',0);
		
		CHBSHelper::setDefault($meta,'passenger_adult_default_number','');
		CHBSHelper::setDefault($meta,'passenger_children_default_number','');
		
		CHBSHelper::setDefault($meta,'show_price_per_single_passenger',0);
		CHBSHelper::setDefault($meta,'passenger_use_person_label',0);
		CHBSHelper::setDefault($meta,'passenger_number_dropdown_list_enable',0);
		CHBSHelper::setDefault($meta,'passenger_number_dropdown_list_display_type',0);
		
		CHBSHelper::setDefault($meta,'calculation_method_service_type_1',1);
		CHBSHelper::setDefault($meta,'calculation_method_service_type_2',5);
		CHBSHelper::setDefault($meta,'calculation_method_service_type_3',1);		
		
		CHBSHelper::setDefault($meta,'base_location','');
		CHBSHelper::setDefault($meta,'base_location_coordinate_lat','');
		CHBSHelper::setDefault($meta,'base_location_coordinate_lng','');		
		
		CHBSHelper::setDefault($meta,'waypoint_enable',1);

		CHBSHelper::setDefault($meta,'location_fixed_autocomplete_enable',0);
		
		CHBSHelper::setDefault($meta,'location_fixed_pickup_service_type_1',array(-1));
		CHBSHelper::setDefault($meta,'location_fixed_dropoff_service_type_1',array(-1));
		CHBSHelper::setDefault($meta,'location_fixed_pickup_service_type_2',array(-1));
		CHBSHelper::setDefault($meta,'location_fixed_dropoff_service_type_2',array(-1));

		CHBSHelper::setDefault($meta,'location_fixed_list_item_empty_enable',0);
		CHBSHelper::setDefault($meta,'location_fixed_list_item_empty_text','');
		
		CHBSHelper::setDefault($meta,'ride_time_multiplier','1.00');
		CHBSHelper::setDefault($meta,'ride_time_rounding','1.00');
		
		CHBSHelper::setDefault($meta,'icon_field_enable',0);
		
		CHBSHelper::setDefault($meta,'navigation_top_enable',1);
		CHBSHelper::setDefault($meta,'service_tab_enable',0);
		CHBSHelper::setDefault($meta,'step_1_right_panel_visibility',1);
		CHBSHelper::setDefault($meta,'vehicle_more_info_default_show',0);
		
		CHBSHelper::setDefault($meta,'booking_title',__('Booking %s','chauffeur-booking-system'));
		CHBSHelper::setDefault($meta,'booking_form_post_id',0);
		
		CHBSHelper::setDefault($meta,'google_recaptcha_enable',0);
		
		CHBSHelper::setDefault($meta,'step_third_enable',1);
		CHBSHelper::setDefault($meta,'phone_number_iti_library_enable',1);
		
		/***/
		
		for($i=1;$i<8;$i++)
		{
			if(!isset($meta['business_hour'][$i]))
				$meta['business_hour'][$i]=array('start'=>null,'stop'=>null);
		}	

		if(!array_key_exists('date_exclude',$meta))
			$meta['date_exclude']=array();		
		
		if(!array_key_exists('maximum_booking_number',$meta))
			$meta['maximum_booking_number']=array();				
		
		/***/
		
		CHBSHelper::setDefault($meta,'payment_mandatory_enable',0);
		CHBSHelper::setDefault($meta,'payment_processing_enable',1);
		CHBSHelper::setDefault($meta,'payment_woocommerce_step_3_enable',1);		
		
		CHBSHelper::setDefault($meta,'payment_deposit_enable',0);
		CHBSHelper::setDefault($meta,'payment_deposit_value',30);
		
		CHBSHelper::setDefault($meta,'payment_id',array(1));
		CHBSHelper::setDefault($meta,'payment_default_id',-1);
		
		CHBSHelper::setDefault($meta,'payment_stripe_api_key_secret','');
		CHBSHelper::setDefault($meta,'payment_stripe_api_key_publishable','');
		CHBSHelper::setDefault($meta,'payment_stripe_method',array('card'));
		CHBSHelper::setDefault($meta,'payment_stripe_product_id','');
		CHBSHelper::setDefault($meta,'payment_stripe_redirect_duration','5');
		CHBSHelper::setDefault($meta,'payment_stripe_success_url_address','');
		CHBSHelper::setDefault($meta,'payment_stripe_cancel_url_address','');
		CHBSHelper::setDefault($meta,'payment_stripe_logo_src','');
		CHBSHelper::setDefault($meta,'payment_stripe_info','');
		
		CHBSHelper::setDefault($meta,'payment_paypal_email_address','');
		CHBSHelper::setDefault($meta,'payment_paypal_redirect_duration','5');
		CHBSHelper::setDefault($meta,'payment_paypal_success_url_address','');
		CHBSHelper::setDefault($meta,'payment_paypal_cancel_url_address','');		
		CHBSHelper::setDefault($meta,'payment_paypal_sandbox_mode_enable',0);
		CHBSHelper::setDefault($meta,'payment_paypal_logo_src','');		
		CHBSHelper::setDefault($meta,'payment_paypal_info','');
		
		CHBSHelper::setDefault($meta,'payment_tpay_client_id','');
		CHBSHelper::setDefault($meta,'payment_tpay_client_secret','');
		CHBSHelper::setDefault($meta,'payment_tpay_notification_secret','');
		CHBSHelper::setDefault($meta,'payment_tpay_sandbox_mode_enable',0);
		CHBSHelper::setDefault($meta,'payment_tpay_redirect_duration','5');
		CHBSHelper::setDefault($meta,'payment_tpay_success_url_address','');
		CHBSHelper::setDefault($meta,'payment_tpay_cancel_url_address','');
		CHBSHelper::setDefault($meta,'payment_tpay_logo_src','');
		CHBSHelper::setDefault($meta,'payment_tpay_info','');

		CHBSHelper::setDefault($meta,'payment_cash_success_url_address','');
		CHBSHelper::setDefault($meta,'payment_cash_logo_src','');
		CHBSHelper::setDefault($meta,'payment_cash_info','');

		CHBSHelper::setDefault($meta,'payment_wire_transfer_success_url_address','');
		CHBSHelper::setDefault($meta,'payment_wire_transfer_logo_src','');
		CHBSHelper::setDefault($meta,'payment_wire_transfer_info','');
		
		CHBSHelper::setDefault($meta,'payment_credit_card_pickup_success_url_address','');
		CHBSHelper::setDefault($meta,'payment_credit_card_pickup_logo_src','');
		CHBSHelper::setDefault($meta,'payment_credit_card_pickup_info','');
		
		/***/
	 
		CHBSHelper::setDefault($meta,'driving_zone_restriction_pickup_location_enable','0');
		CHBSHelper::setDefault($meta,'driving_zone_restriction_waypoint_location_enable','0');
		CHBSHelper::setDefault($meta,'driving_zone_restriction_dropoff_location_enable','0');

		CHBSHelper::setDefault($meta,'driving_zone_restriction_pickup_location_country','-1');
		CHBSHelper::setDefault($meta,'driving_zone_restriction_waypoint_location_country','-1');
		CHBSHelper::setDefault($meta,'driving_zone_restriction_dropoff_location_country','-1');

		CHBSHelper::setDefault($meta,'driving_zone_restriction_pickup_location_area','');
		CHBSHelper::setDefault($meta,'driving_zone_restriction_pickup_location_area_radius',((int)CHBSOption::getOption('length_unit')===1 ? 50 : 80.4672));
		CHBSHelper::setDefault($meta,'driving_zone_restriction_pickup_location_area_coordinate_lat','');
		CHBSHelper::setDefault($meta,'driving_zone_restriction_pickup_location_area_coordinate_lng','');

		CHBSHelper::setDefault($meta,'driving_zone_restriction_waypoint_location_area','');
		CHBSHelper::setDefault($meta,'driving_zone_restriction_waypoint_location_area_radius',((int)CHBSOption::getOption('length_unit')===1 ? 50 : 80.4672));
		CHBSHelper::setDefault($meta,'driving_zone_restriction_waypoint_location_area_coordinate_lat','');
		CHBSHelper::setDefault($meta,'driving_zone_restriction_waypoint_location_area_coordinate_lng','');
		
		CHBSHelper::setDefault($meta,'driving_zone_restriction_dropoff_location_area','');
		CHBSHelper::setDefault($meta,'driving_zone_restriction_dropoff_location_area_radius',((int)CHBSOption::getOption('length_unit')===1 ? 50 : 80.4672));
		CHBSHelper::setDefault($meta,'driving_zone_restriction_dropoff_location_area_coordinate_lat','');
		CHBSHelper::setDefault($meta,'driving_zone_restriction_dropoff_location_area_coordinate_lng','');
		
		CHBSHelper::setDefault($meta,'driving_zone_restriction_location_field_relation_type',1);
		
		CHBSHelper::setDefault($meta,'location_replace',array());
		CHBSHelper::setDefault($meta,'pickup_time_geofence',array());
		
		/***/
		
		CHBSHelper::setDefault($meta,'booking_new_sender_email_account_id',-1);
		CHBSHelper::setDefault($meta,'booking_new_recipient_email_address','');
		
		CHBSHelper::setDefault($meta,'email_notification_booking_new_client_enable',1);
		CHBSHelper::setDefault($meta,'email_notification_booking_new_admin_enable',1);
		
		CHBSHelper::setDefault($meta,'email_notification_booking_new_client_payment_success_enable',0);
		CHBSHelper::setDefault($meta,'email_notification_booking_new_admin_payment_success_enable',0);
		
		CHBSHelper::setDefault($meta,'nexmo_sms_enable',0);
		CHBSHelper::setDefault($meta,'nexmo_sms_api_key','');
		CHBSHelper::setDefault($meta,'nexmo_sms_api_key_secret','');
		CHBSHelper::setDefault($meta,'nexmo_sms_sender_name','');
		CHBSHelper::setDefault($meta,'nexmo_sms_recipient_phone_number','');
		CHBSHelper::setDefault($meta,'nexmo_sms_message',__('New booking has been received.','chauffeur-booking-system'));
	 
		CHBSHelper::setDefault($meta,'twilio_sms_enable',0);
		CHBSHelper::setDefault($meta,'twilio_sms_api_sid','');
		CHBSHelper::setDefault($meta,'twilio_sms_api_token','');
		CHBSHelper::setDefault($meta,'twilio_sms_sender_phone_number','');
		CHBSHelper::setDefault($meta,'twilio_sms_recipient_phone_number','');
		CHBSHelper::setDefault($meta,'twilio_sms_message',__('New booking has been received.','chauffeur-booking-system'));
		
		CHBSHelper::setDefault($meta,'customer_sms_enable',0);
		CHBSHelper::setDefault($meta,'customer_sms_message',__('You sent a booking for a pickup from [chbs_pickup_location] to [chbs_dropoff_location] on [chbs_pickup_date] [chbs_pickup_time] in our [chbs_vehicle_name]. Your total order amount is [chbs_booking_sum_gross].','chauffeur-booking-system'));
		
		CHBSHelper::setDefault($meta,'telegram_enable',0);
		CHBSHelper::setDefault($meta,'telegram_token','');
		CHBSHelper::setDefault($meta,'telegram_group_id','');
		CHBSHelper::setDefault($meta,'telegram_message',__('New booking has been received.','chauffeur-booking-system'));
		
		/***/
				
		CHBSHelper::setDefault($meta,'google_map_map_id','');
		
		CHBSHelper::setDefault($meta,'google_map_default_location_type',1);
		CHBSHelper::setDefault($meta,'google_map_default_location_fixed','');
		CHBSHelper::setDefault($meta,'google_map_default_location_fixed_coordinate_lat','');
		CHBSHelper::setDefault($meta,'google_map_default_location_fixed_coordinate_lng','');
		
		CHBSHelper::setDefault($meta,'google_map_route_avoid',-1);
		CHBSHelper::setDefault($meta,'google_map_route_type',1);
		
		CHBSHelper::setDefault($meta,'google_map_draggable_enable',1);
		CHBSHelper::setDefault($meta,'google_map_traffic_layer_enable',0);
		
		CHBSHelper::setDefault($meta,'google_map_map_type_control_enable',0);
		CHBSHelper::setDefault($meta,'google_map_map_type_control_id','SATELLITE');
		CHBSHelper::setDefault($meta,'google_map_map_type_control_style','DEFAULT');
		CHBSHelper::setDefault($meta,'google_map_map_type_control_position','TOP_CENTER');
		
		CHBSHelper::setDefault($meta,'google_map_zoom_control_enable',0);
		CHBSHelper::setDefault($meta,'google_map_zoom_control_style','DEFAULT');
		CHBSHelper::setDefault($meta,'google_map_zoom_control_position','TOP_CENTER');
		
		CHBSHelper::setDefault($meta,'google_map_pan_control_enable',0);
		CHBSHelper::setDefault($meta,'google_map_pan_control_position','TOP_CENTER');		

		CHBSHelper::setDefault($meta,'google_map_scale_control_enable',0);
		CHBSHelper::setDefault($meta,'google_map_scale_control_position','TOP_CENTER');		
		
		CHBSHelper::setDefault($meta,'google_map_street_view_enable',0);
		CHBSHelper::setDefault($meta,'google_map_street_view_postion','TOP_CENTER');		
		
		/***/
		
		CHBSHelper::setDefault($meta,'google_calendar_enable',0);
		CHBSHelper::setDefault($meta,'google_calendar_id','');
		CHBSHelper::setDefault($meta,'google_calendar_regenerate_token_enable',1);
		CHBSHelper::setDefault($meta,'google_calendar_settings','');
		CHBSHelper::setDefault($meta,'google_calendar_event_title','');
		CHBSHelper::setDefault($meta,'google_calendar_event_description','');
		
		/***/
		
		CHBSHelper::setDefault($meta,'style_color',array_fill(1,count($BookingFormStyle->getColor()),''));   
		
		$metaTemp=apply_filters(PLUGIN_CHBS_CONTEXT.'_set_post_meta_default',null,$meta);
		if(is_array($metaTemp)) $meta=$metaTemp;
	}
	
	/**************************************************************************/
	
	function getDictionary($attr=array(),$sortingType=1)
	{
		global $post;
		
		$dictionary=array();
		
		$default=array
		(
			'booking_form_id'=>0,
			'suppress_filters'=>false
		);
		
		$attribute=shortcode_atts($default,$attr);
		
		CHBSHelper::preservePost($post,$bPost);
		
		$argument=array
		(
			'post_type'=>self::getCPTName(),
			'post_status'=>'publish',
			'posts_per_page'=>-1
		);
		
		if((int)$sortingType===1)
		{
			$argument['orderby']=array('menu_order'=>'asc','title'=>'asc');
		}
		if((int)$sortingType===2)
		{
			$argument['orderby']=array('title'=>'asc');
		}
		
		if(array_key_exists('booking_form_id',$attr))
		{
			$argument['p']=$attribute['booking_form_id'];
			if((int)$argument['p']<=0) return($dictionary);
		}
		if(array_key_exists('suppress_filters',$attr))
		{
			$argument['suppress_filters']=$attribute['suppress_filters'];
		}	
		
		$query=new WP_Query($argument);
		
		if($query===false) return($dictionary);
		
		while($query->have_posts())
		{
			$query->the_post();
			if(is_null($post)) continue;
			
			$dictionary[$post->ID]['post']=$post;
			$dictionary[$post->ID]['meta']=CHBSPostMeta::getPostMeta($post);
		}

		CHBSHelper::preservePost($post,$bPost,0);	
		
		return($dictionary);		
	}
	
	/**************************************************************************/
	
	function createBookingForm($attr)
	{
		$Plugin=new CHBSPlugin();
		$Length=new CHBSLength();
		$TaxRate=new CHBSTaxRate();
		$TransferType=new CHBSTransferType();
		
		if((int)CHBSOption::getOption('system_library_on_shortcode_page_enable')===1)
		{
			$Plugin->prepareLibrary();
			$Plugin->addLibrary('style',2,'enqueue');
			$Plugin->addLibrary('script',2,'enqueue');
		}
		
		$action=CHBSHelper::getGetValue('action',false);
		if($action==='ipn')
		{
			$PaymentPaypal=new CHBSPaymentPaypal();
			$PaymentPaypal->handleIPN();
			return(null);
		}
		
		$default=array
		(
			'booking_form_id'=>0,
			'currency'=>'',
			'widget_mode'=>0,
			'widget_style'=>1,
			'widget_service_type_id'=>1,
			'widget_booking_form_url'=>'',
			'widget_booking_form_new_window'=>0,
            'widget_second_step'=>1,
			'fixed_location_pickup_id'=>0,
			'fixed_location_dropoff_id'=>0,
			'css_class'=>''
		);
		
		$data=array();
		
		$attribute=shortcode_atts($default,$attr);	
		
		if(!is_array($data=$this->checkBookingForm($attribute['booking_form_id'],$attribute['currency'],true))) return;

		if(!CHBSPlugin::isAutoRideTheme())
		{
			$Plugin=new CHBSPlugin();
			$Plugin->addLibrarySingle('script','chbs-google-map');
		}
		
		$data['shortcode_attribute']=$attribute;
		
		$data['ajax_url']=admin_url('admin-ajax.php');
		
		$data['booking_form_post_id']=$attribute['booking_form_id'];
		$data['booking_form_html_id']=CHBSHelper::createId('chbs_booking_form');
		
		$data['dictionary']['transfer_type']=$TransferType->getTransferType();

		$data['dictionary']['tax_rate']=$TaxRate->getDictionary();
				
		$dictionary=$Length->getUnit();
		$data['length_unit']=$dictionary[CHBSOption::getOption('length_unit')];
		$data['length_unit_id']=CHBSOption::getOption('length_unit');
	   
		if($attribute['widget_mode']==1)
		{
			if(!in_array($attribute['widget_service_type_id'],$data['meta']['service_type_id']))
			{
				$attribute['widget_service_type_id']=$data['meta']['service_type_id'][0];
			}
		}
		
		$data['css_class']=$attribute['css_class'];
		
		$data['widget_mode']=$attribute['widget_mode'];
		$data['widget_style']=$attribute['widget_style'];
        $data['widget_second_step']=$attribute['widget_second_step'];
		$data['widget_service_type_id']=$attribute['widget_service_type_id'];
		$data['widget_booking_form_url']=$attribute['widget_booking_form_url'];
		$data['widget_booking_form_new_window']=$attribute['widget_booking_form_new_window'];
        
		$data['datetime_period']=$this->getBookingFormDateAvailable($data['meta']);
				
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'public/public.php');
		return($Template->output());
	}
	
	/**************************************************************************/
	
	function bookingFormDisplayError($message,$displayError)
	{
		if(!$displayError) return;
		echo '<div class="chbs-booking-form-error">'.esc_html($message).'</div>';
	}
	
	/**************************************************************************/
	
	function checkBookingForm($bookingFormId,$currency=null,$displayError=false)
	{
		/****/
		
		if((int)CHBSOption::getOption('google_map_ask_load_enable')===1)
		{
			if(((int)CHBSHelper::getGetValue('google_maps_enable')===-1) || ((int)CHBSHelper::getPostValue('google_maps_enable')===-1)  || ((int)CHBSCookie::get('google_maps_enable')===-1))
			{
				return(false);
			}
		}
		
		/****/
		
		$data=array();
		
		$Date=new CHBSDate();
		$Driver=new CHBSDriver();
		$License=new CHBSLicense();
		$Validation=new CHBSValidation();
		$WooCommerce=new CHBSWooCommerce();
		$BookingEdit=new CHBSBookingEdit();
		
		/****/
		
		$AVRule=new CHBSAVRule();
		$TaxRate=new CHBSTaxRate();
		$Country=new CHBSCountry();
		$Geofence=new CHBSGeofence();
		$PriceRule=new CHBSPriceRule();
		
		$data['dictionary']['driver']=$Driver->getDictionary();
		$data['dictionary']['av_rule']=$AVRule->getDictionary();
		$data['dictionary']['country']=$Country->getCountry();
		$data['dictionary']['geofence']=$Geofence->getDictionary();
		$data['dictionary']['tax_rate']=$TaxRate->getDictionary();

		$data['license_verified']=$License->isVerified();
		
		/***/
		
		$BookingEdit->setBooking();
		$data['booking_edit']=$BookingEdit;
		
		/****/
		
		$bookingForm=$this->getDictionary(array('booking_form_id'=>$bookingFormId));
		if(!count($bookingForm)) 
		{
			$this->bookingFormDisplayError(__('Booking form with provided ID doesn\'t exist.','chauffeur-booking-form'),$displayError);
			return(-1);
		}
		
		$data['post']=$bookingForm[$bookingFormId]['post'];
		$data['meta']=$bookingForm[$bookingFormId]['meta'];
		
		/***/
		
		if((array_key_exists('location_replace',$data['meta'])) && (is_array($data['meta']['location_replace'])) && (count($data['meta']['location_replace'])))
		{
			foreach($data['meta']['location_replace'] as $index=>$value)
			{
				if(!array_key_exists($value['geofence_id'],$data['dictionary']['geofence'])) continue;
				
				$data['meta']['location_replace'][$index]['geofence_shape_coordinate']=$data['dictionary']['geofence'][$value['geofence_id']]['meta']['shape_coordinate'];
			}
		}

		if((array_key_exists('pickup_time_geofence',$data['meta'])) && (is_array($data['meta']['pickup_time_geofence'])) && (count($data['meta']['pickup_time_geofence'])))
		{
			foreach($data['meta']['pickup_time_geofence'] as $index=>$value)
			{
				if(!array_key_exists($value['geofence_id'],$data['dictionary']['geofence'])) continue;

				$data['meta']['pickup_time_geofence'][$index]['geofence_shape_coordinate']=$data['dictionary']['geofence'][$value['geofence_id']]['meta']['shape_coordinate'];
			}
		}
	   
		/***/
		
		if(is_array($data['meta']['business_hour']))
		{
			foreach($data['meta']['business_hour'] as $index=>$value)
			{
				$data['meta']['business_hour'][$index]['default_format']='';
				if((array_key_exists('default',$value)) && ($Validation->isTime($value['default'])))
					$data['meta']['business_hour'][$index]['default_format']=$Date->formatTimeToDisplay($value['default']);
			}
		}
		
		/***/
				
		if($Validation->isNotEmpty(CHBSHelper::getGetValue('currency',false)))
			$currency=CHBSHelper::getGetValue('currency',false);
		else if($Validation->isNotEmpty(CHBSHelper::getPostValue('currency'))) 		
			$currency=CHBSHelper::getPostValue('currency');
		
		if(in_array($currency,$data['meta']['currency']))
			$data['currency']=$currency;
		else $data['currency']=CHBSOption::getOption('currency');
		
		CHBSGlobalData::setGlobalData('currency_id',$data['currency']);
		
		/***/
		
		if(in_array(3,$data['meta']['service_type_id']))
		{
			$data['dictionary']['route']=$this->getBookingFormRoute($data['meta']);
			if(!count($data['dictionary']['route'])) 
			{
				$this->bookingFormDisplayError(__('There are not assigned routes for flat rate service type. Please create at least one route or disable [Flat rate] service type in booking form settings.','chauffeur-booking-form'),$displayError);
				return(-2);
			}
		} 
		
		/***/
		
		$vehicleIdDefault=0;
		
		$data['dictionary']['vehicle']=$this->getBookingFormVehicle($data,$vehicleIdDefault);
		if(!count($data['dictionary']['vehicle'])) 
		{ 
			$this->bookingFormDisplayError(__('Plugin cannot find at least one vehicle.','chauffeur-booking-form'),$displayError);
			return(-3);
		}
		
		$data['dictionary']['price_rule']=$PriceRule->getDictionary(array(),true);
		
		/***/
		
		if($WooCommerce->isEnable($data['meta']))
		{
			$data['dictionary']['payment_woocommerce']=$WooCommerce->getPaymentDictionary();
		}
		else 
		{
			$data['dictionary']['payment']=$this->getBookingFormPayment($data);
		}
		
		$data['dictionary']['booking_extra']=$this->getBookingFormExtra($data);
			  
		$data['dictionary']['booking_extra_category']=$this->getBookingFormExtraCategory($data['dictionary']['booking_extra']);
		
		$data['dictionary']['vehicle_category']=$this->getBookingFormVehicleCategory($data['meta']);
  
		$data['vehicle_bag_count_range']=$this->getVehicleBagCountRange($data['dictionary']['vehicle']);
		$data['vehicle_passenger_count_range']=$this->getVehiclePassengerCountRange($data);
		
		$data['step']=array();
		$data['step']['disable']=array();
		
		if(($data['meta']['step_second_enable']!=1) && (count($data['dictionary']['vehicle'])==1))
		{
			array_push($data['step']['disable'],2);
		}
		
		if(($data['meta']['step_third_enable']!=1) && ($WooCommerce->isAddToCartEnable($data)))
		{
			array_push($data['step']['disable'],3);
		}
		
		if($data['meta']['step_fourth_enable']!=1)
		{
			array_push($data['step']['disable'],4);
		}
		
		$data['step']['dictionary']=array
		(
			1=>array
			(
				'navigation'=>array
				(
					'number'=>__('1','chauffeur-booking-system'),
					'label'=>__('Enter Ride Details','chauffeur-booking-system'),
				),
				'button'=>array
				(
					'next'=>__('Choose a vehicle','chauffeur-booking-system')
				)
			),
			2=>array
			(
				'navigation'=>array
				(				
					'number'=>__('2','chauffeur-booking-system'),
					'label'=>__('Choose a Vehicle','chauffeur-booking-system')
				),
				'button'=>array
				(
					'prev'=>__('Choose ride details','chauffeur-booking-system'),
					'next'=>__('Enter contact details','chauffeur-booking-system')
				)
			),
			3=>array
			(
				'navigation'=>array
				(
					'number'=>__('3','chauffeur-booking-system'),
					'label'=>__('Enter Contact Details','chauffeur-booking-system')
				),
				'button'=>array
				(
					'prev'=>__('Choose a vehicle','chauffeur-booking-system'),
					'next'=>__('Booking summary','chauffeur-booking-system')
				)
			),
			4=>array
			(
				'navigation'=>array
				(
					'number'=>__('4','chauffeur-booking-system'),
					'label'=>__('Booking Summary','chauffeur-booking-system')
				),
				'button'=>array
				(
					'prev'=>__('Enter contact details','chauffeur-booking-system'),
					'next'=>((int)$data['meta']['price_hide']===1 ? __('Send now','chauffeur-booking-system') : ($WooCommerce->isAddToCartEnable($data) ? __('Add to cart','chauffeur-booking-system') : __('Book now','chauffeur-booking-system')))
				)
			)			
		);
		
		if((in_array(2,$data['step']['disable'])) && (in_array(3,$data['step']['disable'])) && (in_array(4,$data['step']['disable'])))
		{
			$data['step']['dictionary'][1]['button']['next']=$data['step']['dictionary'][4]['button']['next'];
		}
		elseif((in_array(2,$data['step']['disable'])) && (in_array(3,$data['step']['disable'])))
		{
			$data['step']['dictionary'][4]['navigation']['number']=$data['step']['dictionary'][2]['navigation']['number'];
			$data['step']['dictionary'][1]['button']['next']=$data['step']['dictionary'][3]['button']['next'];
			$data['step']['dictionary'][4]['button']['prev']=$data['step']['dictionary'][2]['button']['prev'];
		}
		elseif((in_array(2,$data['step']['disable'])) && (in_array(4,$data['step']['disable'])))
		{
			$data['step']['dictionary'][3]['navigation']['number']=$data['step']['dictionary'][2]['navigation']['number'];
			
			$data['step']['dictionary'][1]['button']['next']=$data['step']['dictionary'][2]['button']['next'];
			$data['step']['dictionary'][3]['button']['next']=$data['step']['dictionary'][4]['button']['next'];
			$data['step']['dictionary'][3]['button']['prev']=$data['step']['dictionary'][2]['button']['prev'];
		}
		elseif((in_array(3,$data['step']['disable'])) && (in_array(4,$data['step']['disable'])))
		{
			$data['step']['dictionary'][2]['button']['next']=$data['step']['dictionary'][4]['button']['next'];
		}		
		else if(in_array(2,$data['step']['disable']))
		{
			$data['step']['dictionary'][4]['navigation']['number']=$data['step']['dictionary'][3]['navigation']['number'];
			$data['step']['dictionary'][3]['navigation']['number']=$data['step']['dictionary'][2]['navigation']['number'];
			
			$data['step']['dictionary'][1]['button']['next']=$data['step']['dictionary'][2]['button']['next'];
			$data['step']['dictionary'][3]['button']['prev']=$data['step']['dictionary'][2]['button']['prev'];
		}
		elseif(in_array(3,$data['step']['disable']))
		{
			$data['step']['dictionary'][4]['navigation']['number']=$data['step']['dictionary'][3]['navigation']['number'];
			
			$data['step']['dictionary'][2]['button']['next']=$data['step']['dictionary'][3]['button']['next'];
			$data['step']['dictionary'][4]['button']['prev']=$data['step']['dictionary'][3]['button']['prev'];
		}
		elseif(in_array(4,$data['step']['disable']))
		{
			$data['step']['dictionary'][3]['button']['next']=$data['step']['dictionary'][4]['button']['next'];
		}

		foreach($data['step']['disable'] as $value)
			unset($data['step']['dictionary'][$value]);
		
		/***/
	   
		$data['vehicle_id_default']=$vehicleIdDefault;

		/***/
		   
		$GeoLocation=new CHBSGeoLocation();
		
		if($data['meta']['country_default']=='-1')
		{
			if((int)$data['meta']['geolocation_server_side_enable']===1)
			{
				$data['client_country_code']=$GeoLocation->getCountryCode();
			}
		}
		else $data['client_country_code']=$data['meta']['country_default'];
		
		/***/
		
		$Location=new CHBSLocation();
		
		$data['dictionary']['location']=$Location->getDictionary();
		
		$field=array('location_fixed_pickup_service_type_1','location_fixed_dropoff_service_type_1','location_fixed_pickup_service_type_2','location_fixed_dropoff_service_type_2');

		foreach($field as $fieldName)
		{
			$locationTemp=array();
			foreach($data['dictionary']['location'] as $index=>$value)
			{
				if(in_array($index,$data['meta'][$fieldName]))
					$locationTemp[]=$index;
			}
			$data['meta'][$fieldName]=$locationTemp;
			
			foreach($data['meta'][$fieldName] as $index=>$value)
			{
				if($value==-1)
				{
					$data['meta'][$fieldName]=array();
					break;
				}

				if(array_key_exists($value,$data['dictionary']['location']))						
				{
					$location=$data['dictionary']['location'][$value];
					$data['meta'][$fieldName][$value]=array('id'=>$value,'name'=>$location['post']->post_title,'address'=>$location['meta']['location_name'],'lat'=>$location['meta']['location_name_coordinate_lat'],'lng'=>$location['meta']['location_name_coordinate_lng'],'dropoff_disable'=>array());
				}
				
				if($fieldName==='location_fixed_pickup_service_type_1')
				{
					$t=$data['dictionary']['location'][$value]['meta']['location_dropoff_disable_service_type_1'];
					
					if(is_array($t))
					{
						if(array_key_exists($bookingFormId,$t))
						{
							if(!in_array(-1,$t[$bookingFormId]))
								$data['meta'][$fieldName][$value]['dropoff_disable']=$t[$bookingFormId];
						}
					}
				}
				
				if($fieldName==='location_fixed_pickup_service_type_2')
				{
					$t=$data['dictionary']['location'][$value]['meta']['location_dropoff_disable_service_type_2'];
					
					if(is_array($t))
					{
						if(array_key_exists($bookingFormId,$t))
						{
							if(!in_array(-1,$t[$bookingFormId]))
								$data['meta'][$fieldName][$value]['dropoff_disable']=$t[$bookingFormId];
						}
					}
				}
			}
			
			foreach($data['meta'][$fieldName] as $index=>$value)
			{
				if(!is_array($value)) unset($data['meta'][$fieldName][$index]);
			}		
		}
		
		$data['meta']['waypoint_enable']=($data['meta']['waypoint_enable']==1) && (!count($data['meta']['location_fixed_pickup_service_type_1'])) && (!count($data['meta']['location_fixed_dropoff_service_type_1']));

		$data['duration_distance']=$this->getBookingFormDurationDistance($data['meta']);
		
		/***/
		
		$GoogleReCaptcha=new CHBSGoogleReCaptcha();
		$data['google_recaptcha_enable']=$GoogleReCaptcha->isEnable($data);
		
		/***/
		
		$BookingHelper=new CHBSBookingHelper();
		$BookingHelper->setTaxRateDistance($data);
		
		/***/

		return($data);
	}
	
	/**************************************************************************/
	
	function getBookingFormVehicleFilter($bookingForm,$dictionary)
	{
		$AVRule=new CHBSAVRule();
		
		$av=$AVRule->getAVFromRule($bookingForm,array(),'vehicle');
		
		foreach($dictionary as $index=>$value)
		{
			if(array_key_exists($index,$av))
			{
				if((int)$av[$index]===0) unset($dictionary[$index]);
			}
		}		
		
		return($dictionary);
	}
	
	/**************************************************************************/
	
	function getBookingFormVehicle($bookingForm,&$vehicleIdDefault)
	{
		$category=array();
		
		$Date=new CHBSDate();
		
		$Vehicle=new CHBSVehicle();
		
		/***/
		
		$vehicleIdDefault=0;
		
		$dictionary=$Vehicle->getDictionary(array(),$bookingForm['meta']['vehicle_sorting_type'],true);
		
		$dictionary=$this->getBookingFormVehicleFilter($bookingForm,$dictionary);
				
		/****/
		
		$data=CHBSHelper::getPostOption();
		
		$passengerSum=0;
		
		if(array_key_exists('service_type_id',$data))
		{
			if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult'))
				$passengerSum+=(int)$data['passenger_adult_service_type_'.$data['service_type_id']];
			if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'children'))
				$passengerSum+=(int)$data['passenger_children_service_type_'.$data['service_type_id']];
		}
		
		if($passengerSum>0)
		{
			foreach($dictionary as $index=>$value)
			{
				if($passengerSum>$value['meta']['passenger_count'])
				{
					unset($dictionary[$index]);
				}
			}
		}

		/***/
		
		if(count($dictionary)===1) $vehicleIdDefault=key($dictionary);
		else
		{
			if(array_key_exists($bookingForm['meta']['vehicle_id_default'],$dictionary))
				$vehicleIdDefault=$bookingForm['meta']['vehicle_id_default'];
		}
		
		if($vehicleIdDefault>0)
		{
			if(($bookingForm['meta']['step_second_enable']!=1) || ($bookingForm['meta']['vehicle_select_enable']!=1))
			{
				$dictionary=$Vehicle->getDictionary(array('vehicle_id'=>$vehicleIdDefault));
				
				$dictionary=$this->getBookingFormVehicleFilter($bookingForm,$dictionary);
				
				$Vehicle->getVehicleAttribute($dictionary);
				
				return($dictionary);
			}
		}
		
		/***/
		
		$category=array();
		
		if(count($bookingForm['meta']['vehicle_category_id']))
			$category=array_diff($bookingForm['meta']['vehicle_category_id'],array(-1));
		
		if(count($category))
		{
			foreach($dictionary as $vehicleIndex=>$vehicleValue)
			{
				$vehicleCategory=wp_get_post_terms($vehicleIndex,CHBSVehicle::getCPTCategoryName());

				$found=false;

				foreach($vehicleCategory as $vehicleCategoryIndex=>$vehicleCategoryValue)
				{
					if(in_array((int)$vehicleCategoryValue->{'term_id'},$category))
					{
						$found=true;
						break;
					}
				}

				if(!$found) unset($dictionary[$vehicleIndex]);
			}
		}

		/***/
		
		$data=CHBSHelper::getPostOption();
				
		if(isset($data['service_type_id']))
		{
			$serviceTypeId=$data['service_type_id'];
			
			/***/
			
			if((int)$serviceTypeId===3)
			{
				$Route=new CHBSRoute();
				
				$route=$Route->getDictionary(array('route_id'=>(int)$data['route_service_type_3']));
				$route=$Route->getEnableVehicleFromRoute($route);
				
				foreach($dictionary as $index=>$value)
				{
					if(!in_array($index,$route))
						unset($dictionary[$index]);
				}
			}
			
			/***/
			
			$pickupDate=$Date->formatDateToStandard($data['pickup_date_service_type_'.$serviceTypeId]);
			$pickupTime=$Date->formatTimeToStandard($data['pickup_time_service_type_'.$serviceTypeId]);	
			
			/***/
			
			$returnDate=null;
			$returnTime=null;
			
			if(in_array($serviceTypeId,array(1,3)))
			{
				if((int)$data['transfer_type_service_type_'.$serviceTypeId]===3)
				{
					$returnDate=$Date->formatDateToStandard($data['return_date_service_type_'.$serviceTypeId]);
					$returnTime=$Date->formatTimeToStandard($data['return_time_service_type_'.$serviceTypeId]);					   
				}
			}
			
			/***/
					
			$duration=$data['duration_sum'];
			
			if($bookingForm['meta']['step_second_enable']==1)
				$dictionary=$Vehicle->checkAvailability($dictionary,$pickupDate,$pickupTime,$returnDate,$returnTime,$duration,$data,$bookingForm);
		}
		
		$dictionary=$this->getBookingFormVehicleFilter($bookingForm,$dictionary);
		
		$Vehicle->getVehicleAttribute($dictionary);
		
		return($dictionary);
	}
	
	/**************************************************************************/
	
	function getBookingFormVehicleCategory($meta)
	{
		$Vehicle=new CHBSVehicle();
		$dictionary=$Vehicle->getCategory();
	 
		$vehicleCategory=array();
		if(count($meta['vehicle_category_id']))
			$vehicleCategory=array_diff($meta['vehicle_category_id'],array(-1));
				
		if(!count($vehicleCategory)) return($dictionary);
		
		foreach($dictionary as $index=>$value)
		{
			if(!in_array($index,$vehicleCategory))
				unset($dictionary[$index]);
		}

		return($dictionary);
	}
	
	/**************************************************************************/
	
	function getBookingFormRoute($meta)
	{
		$Route=new CHBSRoute();
		
		$route=array();
		if(count($meta['route_id']))
			$route=array_diff($meta['route_id'],array(-1));	  
		
		$dictionary=$Route->getDictionary(array('route_id'=>$route),1,true);
		
		return($dictionary);
	}
   
	/**************************************************************************/
	
	function getBookingFormPayment($bookingForm)
	{
		$AVRule=new CHBSAVRule();
		$Payment=new CHBSPayment();
		
		$payment=$Payment->getPayment();
		foreach($payment as $index=>$value)
		{
			if(!in_array($index,$bookingForm['meta']['payment_id']))
			   unset($payment[$index]);
		}
		
		/***/
		
		$av=$AVRule->getAVFromRule($bookingForm);
		
		foreach($payment as $index=>$value)
		{
			if(array_key_exists($index,$av))
			{
				if((int)$av[$index]===0) unset($payment[$index]);
			}
		}
		
		/***/
		
		return($payment);
	}
	
	/**************************************************************************/
	
	function getBookingFormExtra($bookingForm)
	{
		$category=array();
		
		$data=CHBSHelper::getPostOption();
		
		$AVRule=new CHBSAVRule();
		$ServiceType=new CHBSServiceType();
		$TransferType=new CHBSTransferType();
		$GeofenceChecker=new CHBSGeofenceChecker();
			
		if(in_array(-2,$bookingForm['meta']['booking_extra_category_id']))
			return(array());
		
		if(count($bookingForm['meta']['booking_extra_category_id']))
			$category=array_diff($bookingForm['meta']['booking_extra_category_id'],array(-1));
  
		$BookingExtra=new CHBSBookingExtra();
		$dictionary=$BookingExtra->getDictionary(array('category_id'=>$category));
		
		/****/
		
		$av=$AVRule->getAVFromRule($bookingForm,array(),'booking_extra');
		
		foreach($dictionary as $index=>$value)
		{
			if(array_key_exists($index,$av))
			{
				if((int)$av[$index]===0) unset($dictionary[$index]);
			}
		}
		
		/****/
		
		$serviceTypeId=array_key_exists('service_type_id',$data) ? $data['service_type_id'] : 0;
		
		if($ServiceType->isServiceType($serviceTypeId))
		{
			$transferTypeId=array_key_exists('transfer_type_service_type_'.$serviceTypeId,$data) ? $data['transfer_type_service_type_'.$serviceTypeId] : 0;
			if(!$TransferType->isTransferType($transferTypeId)) $transferTypeId=1;
			
			foreach($dictionary as $index=>$value)
			{
				if(array_key_exists('service_type_id_enable',$value['meta']))
				{
					if(is_array($value['meta']['service_type_id_enable']))
					{
						if(!in_array($serviceTypeId,$value['meta']['service_type_id_enable']))
							unset($dictionary[$index]);
					}
				}
				if(array_key_exists('transfer_type_id_enable',$value['meta']))
				{
					if(is_array($value['meta']['transfer_type_id_enable']))
					{
						if(!in_array($transferTypeId,$value['meta']['transfer_type_id_enable']))
							unset($dictionary[$index]);
					}
				}	
				if(!in_array(-1,$value['meta']['geofence_pickup']))
				{
					if($GeofenceChecker->locationInGeofence($value['meta']['geofence_pickup'],$bookingForm['dictionary']['geofence'],$data['pickup_location_coordinate_service_type_'.$serviceTypeId])===false)
					{
						unset($dictionary[$index]);
					}
				}
				if(!in_array(-1,$value['meta']['geofence_dropoff']))
				{				
					if($GeofenceChecker->locationInGeofence($value['meta']['geofence_dropoff'],$bookingForm['dictionary']['geofence'],$data['dropoff_location_coordinate_service_type_'.$serviceTypeId])===false)
					{
						unset($dictionary[$index]);
					}
				}
			}
		}
		
		$Coupon=new CHBSCoupon();
		$coupon=$Coupon->checkCode();
		
		if($coupon!==false)
		{
			$discountPercentage=$coupon['meta']['discount_percentage'];
			foreach($dictionary as $index=>$value)
				$dictionary[$index]['meta']['price']=round($dictionary[$index]['meta']['price']*(1-$discountPercentage/100),2);
		}
		
		return($dictionary);		
	}
	
	/**************************************************************************/
	
	function getBookingFormExtraCategory($bookingExtra)
	{
		$BookingExtra=new CHBSBookingExtra();
		$bookingExtraCategory=$BookingExtra->getCategory();
		
		foreach($bookingExtraCategory as $index=>$value)
			$bookingExtraCategory[$index]['_unset']=1;
		
		foreach($bookingExtra as $bookingExtraIndex=>$bookingExtraValue)
		{
			if((isset($bookingExtraValue['category'])) && (is_array($bookingExtraValue['category'])))
			{
				foreach($bookingExtraValue['category'] as $categoryIndex=>$categoryValue)
				{
					foreach($categoryValue as $categoryValueValue)
					{
						if(isset($bookingExtraCategory[$categoryValueValue['term_id']]))
							$bookingExtraCategory[$categoryValueValue['term_id']]['_unset']=0;
					}
				}
			}
		}
		
		foreach($bookingExtraCategory as $index=>$value)
		{
			if((int)$bookingExtraCategory[$index]['_unset']===1)
				unset($bookingExtraCategory[$index]);
		}
		
		return($bookingExtraCategory);
	}
	
	/**************************************************************************/
	
	function getAvailableStepNumber($stepCurrent,$stepRequest,$bookingForm)
	{
		if(in_array($stepRequest,$bookingForm['step']['disable']))
			return($this->getAvailableStepNumber($stepCurrent,($stepRequest>$stepCurrent ? $stepRequest+1 : $stepRequest-1),$bookingForm));
		
		return($stepRequest);
	}
	
	/**************************************************************************/

	function goToStep()
	{
		$response=array();
		
		$User=new CHBSUser();
		$Date=new CHBSDate();
		$Length=new CHBSLength();
		$Payment=new CHBSPayment();
		$Country=new CHBSCountry();
		$Validation=new CHBSValidation();
		$WooCommerce=new CHBSWooCommerce();
		$TransferType=new CHBSTransferType();
		$BookingFormElement=new CHBSBookingFormElement();
	   
		$data=CHBSHelper::getPostOption();
			  
		if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
		{
			if($bookingForm===-3)
			{
				$response['step']=1;
				$this->setErrorGlobal($response,__('No vehicles available in the selected time period.','chauffeur-booking-system'));
				$this->createFormResponse($response);
			}
		}
	
		/***/
		
		$GoogleReCaptcha=new CHBSGoogleReCaptcha();
		if(!$GoogleReCaptcha->verify($bookingForm,$data))
		{
			$response['step']=1;
			$this->setErrorGlobal($response,__('Request blocked by Google reCAPTCHA.','chauffeur-booking-system'));
			$this->createFormResponse($response);			
		}
		
		/***/
		
		$response['booking_summary_hide_fee']=$bookingForm['meta']['booking_summary_hide_fee'];
		
		if((!in_array($data['step_request'],array(2,3,4,5))) || (!in_array($data['step'],array(1,2,3,4))))
		{
			$response['step']=1;
			$this->createFormResponse($response);			
		}

		$data['step_request']=$this->getAvailableStepNumber($data['step'],$data['step_request'],$bookingForm);
		
		/***/
		/***/
		
		if((int)$data['vehicle_price_calculation_first_step_action']!==0)
		{
			$data=CHBSBookingHelper::fillBookingFormSampleData($data);
		}
		
		/***/
		
		if((int)CHBSOption::getOption('google_map_server_data_validation_enable')===1)
		{
			if($data['step_request']>4)
			{
				$GoogleMapAPI=new CHBSGoogleMapAPI();
				$result=$GoogleMapAPI->validateBookingFormData($bookingForm,$data);

				if($result<0)
				{
					$this->setErrorGlobal($response,__('Invalid ride details.','chauffeur-booking-system'));

					$response['step']=1;
					$response['step_request']=1;

					$this->createFormResponse($response);	
				}
			}
		}
		
		/***/
		
		if($data['step_request']>1)
		{
			if(!in_array($data['service_type_id'],$bookingForm['meta']['service_type_id']))
				$data['service_type_id']=1;
			
			$data['pickup_date_service_type_'.$data['service_type_id']]=$Date->formatDateToStandard($data['pickup_date_service_type_'.$data['service_type_id']]);
			$data['pickup_time_service_type_'.$data['service_type_id']]=$Date->formatTimeToStandard($data['pickup_time_service_type_'.$data['service_type_id']]);		  

			$pickupTimeForValidation=$data['pickup_time_service_type_'.$data['service_type_id']];
			$pickupTimeForComparison=$Date->getTimeRangeStart($pickupTimeForValidation);

			$dateTimeError=false;
			$validateReturnDateTime=false;
					
			if(is_array($bookingForm['meta']['transfer_type_enable_'.$data['service_type_id']]))
			{
				if(count($bookingForm['meta']['transfer_type_enable_'.$data['service_type_id']]))
				{
					if(!$TransferType->isTransferType($data['transfer_type_service_type_'.$data['service_type_id']]))
						$this->setErrorLocal($response,CHBSHelper::getFormName('transfer_type_service_type_'.$data['service_type_id'],false),__('Select a valid transfer type.','chauffeur-booking-system'));
					else 
					{
						if((int)$data['transfer_type_service_type_'.$data['service_type_id']]===3)
						{
							$validateReturnDateTime=true;

							$data['return_date_service_type_'.$data['service_type_id']]=$Date->formatDateToStandard($data['return_date_service_type_'.$data['service_type_id']]);
							$data['return_time_service_type_'.$data['service_type_id']]=$Date->formatTimeToStandard($data['return_time_service_type_'.$data['service_type_id']]);						
						}
					}
				}
			}

			if(!$validateReturnDateTime)
			{
				CHBSHelper::removeUIndex($data,'return_date_service_type_'.$data['service_type_id'],'return_time_service_type_'.$data['service_type_id']);
				
				$data['return_date_service_type_'.$data['service_type_id']]=null;
				$data['return_time_service_type_'.$data['service_type_id']]=null;
			}
			
			/***/
			
			// check if format of pickup date is valid
			if(!$Validation->isDate($data['pickup_date_service_type_'.$data['service_type_id']]))
			{
				$dateTimeError=true;
				$this->setErrorLocal($response,CHBSHelper::getFormName('pickup_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
			}
			// check if format of pickup time is valid
			if((!$Validation->isTime($pickupTimeForValidation)) && (!$Validation->isTimeRange($pickupTimeForValidation)))
			{   
				$dateTimeError=true;
				$this->setErrorLocal($response,CHBSHelper::getFormName('pickup_time_service_type_'.$data['service_type_id'],false),__('Enter a valid time.','chauffeur-booking-system'));
			}
			if($validateReturnDateTime)
			{
				// check if format of return date is valid
				if(!$Validation->isDate($data['return_date_service_type_'.$data['service_type_id']]))
				{
					$dateTimeError=true;
					$this->setErrorLocal($response,CHBSHelper::getFormName('return_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
				}
				// check if format of return time is valid
				if(!$Validation->isTime($data['return_time_service_type_'.$data['service_type_id']]))
				{   
					$dateTimeError=true;
					$this->setErrorLocal($response,CHBSHelper::getFormName('return_time_service_type_'.$data['service_type_id'],false),__('Enter a valid time.','chauffeur-booking-system'));
				}				
			}
			
			/***/
			
			if(!$dateTimeError)
			{
				// check if pickup date/time is later than current date/time
				if(in_array($Date->compareDate($data['pickup_date_service_type_'.$data['service_type_id']].' '.$pickupTimeForComparison,date_i18n('Y-m-d H:i')),array(2)))
				{
					$dateTimeError=true;
					$this->setErrorLocal($response,CHBSHelper::getFormName('pickup_date_service_type_'.$data['service_type_id'],false),__('Pickup date and time have to be later than the current time.','chauffeur-booking-system'));					
				}					
			}			
			
			/***/
			
			if(!$dateTimeError)
			{
				if($validateReturnDateTime)
				{
					// check if return date/time is later than pickup date/time
					if(in_array($Date->compareDate($data['pickup_date_service_type_'.$data['service_type_id']].' '.$pickupTimeForComparison,$data['return_date_service_type_'.$data['service_type_id']].' '.$data['return_time_service_type_'.$data['service_type_id']]),array(0,1)))
					{
						$dateTimeError=true;
						$this->setErrorLocal($response,CHBSHelper::getFormName('return_date_service_type_'.$data['service_type_id'],false),__('Return date and time have to be later than pickup date and time.','chauffeur-booking-system'));					
					}
				}
			}		  
			
			
			/****/
			
			$AVRule=new CHBSAVRule();
			$bookingPeriodRule=$AVRule->getAVFromRule($bookingForm,array(),'booking_period');
			
			// check booking period for pickup date/time
			if(!$dateTimeError)
			{
				if(count($bookingPeriodRule))
				{
					$bookingPeriodFrom=$bookingPeriodRule['booking_period_from'];
					$bookingPeriodType=$bookingPeriodRule['booking_period_type'];
				}
				else
				{
					$bookingPeriodFrom=$bookingForm['meta']['booking_period_from'];
					$bookingPeriodType=$bookingForm['meta']['booking_period_type'];
				}
				
				if(!$Validation->isNumber($bookingPeriodFrom,0,9999))
					$bookingPeriodFrom=0;
				
				list($date1,$date2)=$this->getDatePeriod($data,$bookingForm,'pickup',$bookingPeriodFrom,$bookingPeriodType);
				
				if($Date->compareDate($date1,$date2)===2)
				{
					$this->setErrorLocal($response,CHBSHelper::getFormName('pickup_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
					$dateTimeError=true;					
				}	   

				if(!$dateTimeError)
				{
					$bookingPeriodTo=$bookingForm['meta']['booking_period_to'];
					if($Validation->isNumber($bookingPeriodTo,0,9999))
					{
						$bookingPeriodTo+=$bookingPeriodFrom;
						
						list($date1,$date2)=$this->getDatePeriod($data,$bookingForm,'pickup',$bookingPeriodTo,$bookingPeriodType);	
						if($Date->compareDate($date1,$date2)===1)
						{
							$this->setErrorLocal($response,CHBSHelper::getFormName('pickup_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
							$dateTimeError=true;					
						}							   
					}
				}
			}
			
			// check booking period for return date/time
			if((!$dateTimeError) && ($validateReturnDateTime))
			{
				if(count($bookingPeriodRule))
				{
					$bookingPeriodFrom=$bookingPeriodRule['booking_period_from'];
					$bookingPeriodType=$bookingPeriodRule['booking_period_type'];
				}
				else
				{
					$bookingPeriodFrom=$bookingForm['meta']['booking_period_from'];
					$bookingPeriodType=$bookingForm['meta']['booking_period_type'];
				}
				
				if(!$Validation->isNumber($bookingPeriodFrom,0,9999))
					$bookingPeriodFrom=0;
				
				list($date1,$date2)=$this->getDatePeriod($data,$bookingForm,'return',$bookingPeriodFrom,$bookingPeriodType);
				if($Date->compareDate($date1,$date2)===2)
				{
					$this->setErrorLocal($response,CHBSHelper::getFormName('return_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
					$dateTimeError=true;					
				}	   

				if(!$dateTimeError)
				{
					$bookingPeriodTo=$bookingForm['meta']['booking_period_to'];
					if($Validation->isNumber($bookingPeriodTo,0,9999))
					{
						$bookingPeriodTo+=$bookingPeriodFrom;
						list($date1,$date2)=$this->getDatePeriod($data,$bookingForm,'return',$bookingPeriodTo,$bookingPeriodType);
						
						if($Date->compareDate($date1,$date2)===1)
						{
							$this->setErrorLocal($response,CHBSHelper::getFormName('return_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
							$dateTimeError=true;					
						}							   
					}
				}
			}
			
			/****/
			
			// check exclude dates
			if(!$dateTimeError)
			{
				if(is_array($bookingForm['meta']['date_exclude']))
				{
					foreach($bookingForm['meta']['date_exclude'] as $index=>$value)
					{
						if($Date->dateInRange($data['pickup_date_service_type_'.$data['service_type_id']],$value['start'],$value['stop']))
						{
							$this->setErrorLocal($response,CHBSHelper::getFormName('pickup_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
							$dateTimeError=true;
							break;
						}
						
						if($validateReturnDateTime)
						{
							if($Date->dateInRange($data['return_date_service_type_'.$data['service_type_id']],$value['start'],$value['stop']))
							{
								$this->setErrorLocal($response,CHBSHelper::getFormName('return_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
								$dateTimeError=true;
								break;
							}							
						}
					}
				}
			}
			
			/***/

			// check business hours
			if(!$dateTimeError)
			{
				$number=$Date->getDayNumberOfWeek($data['pickup_date_service_type_'.$data['service_type_id']]);
				
				if(isset($bookingForm['meta']['business_hour'][$number]))
				{
					if(($Validation->isNotEmpty($bookingForm['meta']['business_hour'][$number]['start'])) && ($Validation->isNotEmpty($bookingForm['meta']['business_hour'][$number]['stop'])))
					{
						if(!$Date->timeInRange($pickupTimeForComparison,$bookingForm['meta']['business_hour'][$number]['start'],$bookingForm['meta']['business_hour'][$number]['stop']))
						{
							$this->setErrorLocal($response,CHBSHelper::getFormName('pickup_time_service_type_'.$data['service_type_id'],false),__('Enter a valid time.','chauffeur-booking-system'));
							$dateTimeError=true;
						}
					}
					else
					{
						$this->setErrorLocal($response,CHBSHelper::getFormName('pickup_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
						$dateTimeError=true;						
					}
				}
			}
			if((!$dateTimeError) && ($validateReturnDateTime))
			{
				$number=$Date->getDayNumberOfWeek($data['return_date_service_type_'.$data['service_type_id']]);
				
				if(isset($bookingForm['meta']['business_hour'][$number]))
				{
					if(($Validation->isNotEmpty($bookingForm['meta']['business_hour'][$number]['start'])) && ($Validation->isNotEmpty($bookingForm['meta']['business_hour'][$number]['stop'])))
					{
						if(!$Date->timeInRange($data['return_time_service_type_'.$data['service_type_id']],$bookingForm['meta']['business_hour'][$number]['start'],$bookingForm['meta']['business_hour'][$number]['stop']))
						{
							$this->setErrorLocal($response,CHBSHelper::getFormName('return_time_service_type_'.$data['service_type_id'],false),__('Enter a valid time in the correct format.','chauffeur-booking-system'));
							$dateTimeError=true;
						}
					}
					else
					{
						$this->setErrorLocal($response,CHBSHelper::getFormName('return_date_service_type_'.$data['service_type_id'],false),__('Enter a valid date.','chauffeur-booking-system'));
						$dateTimeError=true;						
					}
				}				
			}
			
			/***/
			
			if(in_array($data['service_type_id'],array(1,2)))
			{			  
				if(count($bookingForm['meta']['location_fixed_pickup_service_type_'.$data['service_type_id']]))
				{
					if(!array_key_exists($data['fixed_location_pickup_service_type_'.$data['service_type_id']],$bookingForm['dictionary']['location']))
						$this->setErrorLocal($response,CHBSHelper::getFormName('fixed_location_pickup_service_type_'.$data['service_type_id'],false),__('Select a valid location.','chauffeur-booking-system'));
				}
				else
				{
					if(!$Validation->isCoordinateGroup($data['pickup_location_coordinate_service_type_'.$data['service_type_id']]))
						$this->setErrorLocal($response,CHBSHelper::getFormName('pickup_location_coordinate_service_type_'.$data['service_type_id'],false),__('Enter a valid location.','chauffeur-booking-system'));

					if(($data['service_type_id']==1) && ($bookingForm['meta']['waypoint_enable']==1))
					{
						if(is_array($data['waypoint_location_coordinate_service_type_1']))
						{
							unset($data['waypoint_location_coordinate_service_type_1'][0]);
							foreach($data['waypoint_location_coordinate_service_type_1'] as $index=>$value)
							{
								if(!$Validation->isCoordinateGroup($value))
									$this->setErrorLocal($response,CHBSHelper::getFormName('waypoint_location_service_type_1-'.$index,false),__('Enter a valid location.','chauffeur-booking-system'));
							}
						}
					}
				}
				
				if(($data['service_type_id']==1) || (($data['service_type_id']==2) && ((int)$bookingForm['meta']['dropoff_location_field_enable']===1)))
				{
					if(count($bookingForm['meta']['location_fixed_dropoff_service_type_'.$data['service_type_id']]))
					{  
						if(!array_key_exists($data['fixed_location_dropoff_service_type_'.$data['service_type_id']],$bookingForm['dictionary']['location']))
							$this->setErrorLocal($response,CHBSHelper::getFormName('fixed_location_dropoff_service_type_'.$data['service_type_id'],false),__('Select a valid location.','chauffeur-booking-system'));
						else 
						{
							$Location=new CHBSLocation();

							$bookingFormId=$bookingForm['post']->ID;

							$fixedLocationPickupId=$data['fixed_location_pickup_service_type_'.$data['service_type_id']];

							$fixedLocationPickupData=$Location->getDictionary(array('location_id'=>$fixedLocationPickupId));

							$t=$fixedLocationPickupData[$fixedLocationPickupId]['meta'];

							if((is_array($t)) && (array_key_exists('location_dropoff_disable_service_type_'.$data['service_type_id'],$t)))
							{
								$t=$t['location_dropoff_disable_service_type_'.$data['service_type_id']];

								if(array_key_exists($bookingFormId,$t))
								{
									if(!in_array(-1,$t[$bookingFormId]))
									{
										if(in_array($data['fixed_location_dropoff_service_type_'.$data['service_type_id']],$t[$bookingFormId]))
											$this->setErrorLocal($response,CHBSHelper::getFormName('fixed_location_dropoff_service_type_'.$data['service_type_id'],false),__('This drop-off location is not available for the selected pickup location.','chauffeur-booking-system'));
									}
								}
							}
						}
					}
					else
					{
						if($data['service_type_id']==1)
						{
							if(!$Validation->isCoordinateGroup($data['dropoff_location_coordinate_service_type_'.$data['service_type_id']]))
								$this->setErrorLocal($response,CHBSHelper::getFormName('dropoff_location_coordinate_service_type_'.$data['service_type_id'],false),__('Enter a valid location.','chauffeur-booking-system'));
						}
						else if($data['service_type_id']==2)
						{
							if($Validation->isNotEmpty($data['dropoff_location_coordinate_service_type_'.$data['service_type_id']]))
							{
								if(!$Validation->isCoordinateGroup($data['dropoff_location_coordinate_service_type_'.$data['service_type_id']]))
									$this->setErrorLocal($response,CHBSHelper::getFormName('dropoff_location_coordinate_service_type_'.$data['service_type_id'],false),__('Enter a valid location.','chauffeur-booking-system'));
							}
						}
					}
				}
			}
			
			if(in_array($data['service_type_id'],array(3)))
			{
				if(!array_key_exists($data['route_service_type_3'],$bookingForm['dictionary']['route']))
					$this->setErrorLocal($response,CHBSHelper::getFormName('route_service_type_3',false),__('Enter a valid route.','chauffeur-booking-system'));
				else
				{
					$pickupHour=$bookingForm['dictionary']['route'][$data['route_service_type_3']]['meta']['pickup_hour'];

					$dayOfWeek=$Date->getDayNumberOfWeek($data['pickup_date_service_type_3']);

					if((is_array($pickupHour[$dayOfWeek])) && (is_array($pickupHour[$dayOfWeek]['hour'])) && (count($pickupHour[$dayOfWeek]['hour'])))
					{
						$pickupHourFound=false;
						
						foreach($pickupHour[$dayOfWeek]['hour'] as $index=>$value)
						{
							if($value==$data['pickup_time_service_type_3'])
							{
								$pickupHourFound=true;
								break;
							}
						}
						
						if(!$pickupHourFound)
							$this->setErrorLocal($response,CHBSHelper::getFormName('pickup_time_service_type_3',false),__('Enter a valid time.','chauffeur-booking-system'));
					}
				}
			}			
			
			if(in_array($data['service_type_id'],array(2)))
			{
				$find=false;
				$value=$data['duration_service_type_2'];
				
				for($i=$bookingForm['meta']['duration_min'];$i<=$bookingForm['meta']['duration_max'];$i+=$bookingForm['meta']['duration_step'])
				{
					if($i==$value)
					{
						$find=true;
						break;
					}
				}
				
				if(!$find) $this->setErrorLocal($response,CHBSHelper::getFormName('duration_service_type_2',false),__('Enter a valid duration.','chauffeur-booking-system'));
			}
			
			if(is_array($bookingForm['meta']['transfer_type_enable_'.$data['service_type_id']]))
			{
				if(count($bookingForm['meta']['transfer_type_enable_'.$data['service_type_id']]))
				{
					if(!$TransferType->isTransferType($data['transfer_type_service_type_'.$data['service_type_id']]))
						$this->setErrorLocal($response,CHBSHelper::getFormName('transfer_type_service_type_3',false),__('Select a valid transfer type.','chauffeur-booking-system'));
					else 
					{
						if($data['transfer_type_service_type_'.$data['service_type_id']]===3)
						{


						}
					}
				}
			}
			
			$passengerSum=0;
			
			if((CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult')) || CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'children'))
			{
				if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult'))
				{
					if(!$Validation->isNumber($data['passenger_adult_service_type_'.$data['service_type_id']],0,99))
					{
						$this->setErrorLocal($response,CHBSHelper::getFormName('passenger_adult_service_type_'.$data['service_type_id'],false),__('Enter a valid number of adult passengers.','chauffeur-booking-system'));
					}
					else $passengerSum+=$data['passenger_adult_service_type_'.$data['service_type_id']];
				}
							
				if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'children'))
				{
					if(!$Validation->isNumber($data['passenger_children_service_type_'.$data['service_type_id']],0,99))
					{
						$this->setErrorLocal($response,CHBSHelper::getFormName('passenger_children_service_type_'.$data['service_type_id'],false),__('Enter a valid number of children passengers.','chauffeur-booking-system'));
					}
					else $passengerSum+=$data['passenger_children_service_type_'.$data['service_type_id']];
				}				
				
				if($passengerSum===0)
				{
					if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult'))
						$this->setErrorLocal($response,CHBSHelper::getFormName('passenger_adult_service_type_'.$data['service_type_id'],false),__('Enter a valid number of adult passengers.','chauffeur-booking-system'));
					if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult'))
						$this->setErrorLocal($response,CHBSHelper::getFormName('passenger_children_service_type_'.$data['service_type_id'],false),__('Enter a valid number of children passengers.','chauffeur-booking-system'));
				}
			}
			
			if(in_array($data['service_type_id'],array(1,3)))
			{
				if($bookingForm['meta']['extra_time_enable']==1)
				{
					$error=false;
					
					$value=$data['extra_time_service_type_'.$data['service_type_id']];
					
					if($bookingForm['meta']['extra_time_mandatory']==1)
					{
						if($value<=0)
						{
							$error=true;
							$this->setErrorLocal($response,CHBSHelper::getFormName('extra_time_service_type_'.$data['service_type_id'],false),__('Enter non-zero value of extra time.','chauffeur-booking-system'));
						}
					}
					
					if(!$error)
					{
						$find=false;
						
						for($i=$bookingForm['meta']['extra_time_range_min'];$i<=$bookingForm['meta']['extra_time_range_max'];$i+=$bookingForm['meta']['extra_time_step'])
						{
							if($i==$value)
							{
								$find=true;
								break;
							}						
						}
					
						if(!$find) $this->setErrorLocal($response,CHBSHelper::getFormName('extra_time_service_type_'.$data['service_type_id'],false),__('Select a valid extra time value.','chauffeur-booking-system'));
					}
				}
			}
			
			if(!isset($response['error']))
			{
				if(in_array($data['service_type_id'],array(1)))
				{
					$distanceSum=$data['distance_sum'];
					$distanceMinimum=$bookingForm['meta']['distance_minimum'];
					$distanceMaximum=$bookingForm['meta']['distance_maximum'];
					
					if(CHBSOption::getOption('length_unit')==2)
					{
						$distanceSum=round($Length->convertUnit($distanceSum,1,2),1);
						$distanceMinimum=round($Length->convertUnit($bookingForm['meta']['distance_minimum'],1,2),1);
						$distanceMaximum=round($Length->convertUnit($bookingForm['meta']['distance_maximum'],1,2),1);
					}
				
					if(!isset($response['error']))
					{
						if(($distanceMinimum>0) && ($distanceMinimum>$distanceSum))
						{
							if(CHBSOption::getOption('length_unit')==2)
								$this->setErrorGlobal($response,sprintf(__('Distance cannot to be lower than %s miles.','chauffeur-booking-system'),$distanceMinimum));
							else $this->setErrorGlobal($response,sprintf(__('Distance cannot to be lower than %s kilometers.','chauffeur-booking-system'),$distanceMinimum));
						}
					}
					if(!isset($response['error']))
					{
						if(($distanceMaximum>0) && ($distanceMaximum<$distanceSum))
						{
							if(CHBSOption::getOption('length_unit')==2)
								$this->setErrorGlobal($response,sprintf(__('Distance cannot to be greater than %s miles.','chauffeur-booking-system'),$distanceMaximum));
							else $this->setErrorGlobal($response,sprintf(__('Distance cannot to be greater than %s kilometers.','chauffeur-booking-system'),$distanceMaximum));
						}
					}
				}
			}
			
			if(!isset($response['error']))
			{
				if(in_array($data['service_type_id'],array(1,2)))
				{
					$durationSum=$data['duration_sum'];
					$durationMinimum=$bookingForm['meta']['duration_minimum'];					
					$durationMaximum=$bookingForm['meta']['duration_maximum'];					

					if(!isset($response['error']))
					{
						if(($durationMinimum>0) && ($durationMinimum>$durationSum))
							$this->setErrorGlobal($response,sprintf(__('Duration cannot to be lower than %s minutes.','chauffeur-booking-system'),$durationMinimum));
					}
					if(!isset($response['error']))
					{
						if(($durationMaximum>0) && ($durationMaximum<$durationSum))
							$this->setErrorGlobal($response,sprintf(__('Duration cannot to be greater than %s minutes.','chauffeur-booking-system'),$durationMaximum));
					}
				}
			}
			
			if(!isset($response['error']))
			{
				$r=CHBSBookingHelper::checkMaximumBookingNumber($data,$bookingForm);
				if($r['error'])
				{
					$this->setErrorGlobal($response,$r['message']);
				}
				
				if(!$r['error'])
				{
					$r=CHBSBookingHelper::checkMaximumBookingNumber($data,$bookingForm,'pickup','return');
					if($r['error'])
					{
						$this->setErrorGlobal($response,$r['message']);
					}
				}
	
				if(!$r['error'])
				{
					$r=CHBSBookingHelper::checkMaximumBookingNumber($data,$bookingForm,'return','return');
					if($r['error'])
					{
						$this->setErrorGlobal($response,$r['message']);
					}
				}

				if(!$r['error'])
				{
					$r=CHBSBookingHelper::checkMaximumBookingNumber($data,$bookingForm,'return','pickup');
					if($r['error'])
					{
						$this->setErrorGlobal($response,$r['message']);
					}
				}
			}
			
			if(isset($response['error']))
			{
				$response['step']=1;
				$this->createFormResponse($response);
			}
		}		
		
		/***/
		
		if($data['step_request']>2)
		{
			$error=false;
		
			/***/

			if(!array_key_exists($data['vehicle_id'],$bookingForm['dictionary']['vehicle']))
			{
				$error=true;
				$this->setErrorGlobal($response,__('Select a vehicle.','chauffeur-booking-system'));
			}
			
			if(!$error)
			{
				if($bookingForm['meta']['step_second_enable']==1)
				{
					$bookingExtraId=preg_split('/,/',$data['booking_extra_id']);

					foreach($bookingForm['dictionary']['booking_extra'] as $index=>$value)
					{
						if((in_array(-1,$value['meta']['vehicle_id'])) || (in_array($data['vehicle_id'],$value['meta']['vehicle_id'])))
						{
							if((int)$value['meta']['mandatory']===1)
							{
								if(((int)$data['booking_extra_value'][$index]===-1) || (!array_key_exists($index,$data['booking_extra_value'])))
								{
									$error=true;
									$this->setErrorGlobal($response,__('Select all booking extra marked as required (*).','chauffeur-booking-system'));
									break;
								}
							}

							if(((int)$bookingForm['meta']['booking_extra_note_display_enable']===1) && ((int)$bookingForm['meta']['booking_extra_note_mandatory_enable']===1))
							{
								if(in_array($index,$bookingExtraId))
								{
									if($Validation->isEmpty($data['booking_extra_'.$index.'_note']))
									{
										$error=true;
										$this->setErrorGlobal($response,__('Enter all required (*) notes in the booking extras.','chauffeur-booking-system'));
										break;									
									}
								}
							}
						}
					}
				}
			}
			
			if(!$error)
			{
				// *rule
				$argument=array
				(
					'booking_form_id'=>(int)$data['booking_form_id'],
					'service_type_id'=>(int)$data['service_type_id'],
					'transfer_type_id'=>$data['transfer_type_service_type_'.$data['service_type_id']],
					'pickup_location_coordinate'=>$data['pickup_location_coordinate_service_type_'.$data['service_type_id']],
					'dropoff_location_coordinate'=>$data['dropoff_location_coordinate_service_type_'.$data['service_type_id']],
					'fixed_location_pickup'=>(int)$data['fixed_location_pickup_service_type_'.$data['service_type_id']],
					'fixed_location_dropoff'=>(int)$data['fixed_location_dropoff_service_type_'.$data['service_type_id']],
					'route_id'=>(int)$data['route_service_type_3'],
					'vehicle_id'=>(int)$data['vehicle_id'],
					'pickup_date'=>$data['pickup_date_service_type_'.$data['service_type_id']],
					'pickup_time'=>$data['pickup_time_service_type_'.$data['service_type_id']],
					'return_date'=>$data['return_date_service_type_'.$data['service_type_id']],
					'return_time'=>$data['return_time_service_type_'.$data['service_type_id']],					
					'base_location_distance'=>CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id']),
					'base_location_return_distance'=>CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id'],true),					
					'distance'=>$data['distance_map'],
					'distance_sum'=>$data['distance_sum'],
					'duration'=>in_array($data['service_type_id'],array(1,3)) ? 0 : $data['duration_service_type_2']*60,
					'duration_map'=>$data['duration_map'],
					'duration_sum'=>in_array($data['service_type_id'],array(1,3)) ? $data['duration_sum'] : $data['duration_service_type_2']*60,
					'passenger_sum'=>$passengerSum,
                    'waypoint_count'=>CHBSBookingHelper::getWaypointCount($data,$bookingForm,$data['service_type_id'],$data['transfer_type_service_type_'.$data['service_type_id']])
				);

				$PriceRule=new CHBSPriceRule();
				
				$priceRule=$PriceRule->getPriceFromRule($argument,$bookingForm);
   
				if((int)$priceRule['calculation_on_request_enable']===1)
				{
					$error=true;
					$this->setErrorGlobal($response,__('Select a vehicle.','chauffeur-booking-system'));						
				}
			}
			
			if(!$error)
			{
				if((int)$bookingForm['meta']['price_hide']===0)
				{
					$AVRule=new CHBSAVRule();
					$orderValueRule=$AVRule->getAVFromRule($bookingForm,array(),'minimum_order_value');
					
					if((count($orderValueRule)) || ($bookingForm['meta']['order_value_minimum']>0) || ($bookingForm['meta']['order_value_maximum']>0))
					{
						$Booking=new CHBSBooking();

						$data['booking_form']=$bookingForm;

						if(($price=$Booking->calculatePrice($data,null,false,true))!==false)	  
						{
							$orderValueMinimum=CHBSPrice::numberFormat(CHBSPrice::numberFormat($bookingForm['meta']['order_value_minimum'])*CHBSCurrency::getExchangeRate());
							$orderValueMaximum=CHBSPrice::numberFormat(CHBSPrice::numberFormat($bookingForm['meta']['order_value_maximum'])*CHBSCurrency::getExchangeRate());
								
							if(count($orderValueRule))
							{
								if($orderValueRule['minimum_order_value']>$price['total']['sum']['gross']['value'])									
									$this->setErrorGlobal($response,$orderValueRule['minimum_order_error_message']);
							}
							if(!isset($response['error']))
							{
								if($bookingForm['meta']['order_value_minimum']>0)
								{
									if($orderValueMinimum>$price['total']['sum']['gross']['value'])
										$this->setErrorGlobal($response,sprintf(__('Minimum value of order is %s.','chauffeur-booking-system'),CHBSPrice::format($orderValueMinimum,CHBSCurrency::getFormCurrency())));
								}
							}
							if(!isset($response['error']))
							{
								if($bookingForm['meta']['order_value_maximum']>0)
								{
									if($orderValueMaximum<$price['total']['sum']['gross']['value'])
										$this->setErrorGlobal($response,sprintf(__('Maximum value of order is %s.','chauffeur-booking-system'),CHBSPrice::format($orderValueMaximum,CHBSCurrency::getFormCurrency())));
								}
							}
						}						
					}
				}
			}
			
			if(isset($response['error'])) 
				$data['step']=$data['step_request']=$response['step']=2;
		}
		 
		/***/
		
		if(!isset($response['error']))
		{
			if($data['step_request']>3)
			{
				if(!in_array(3,$bookingForm['step']['disable']))
				{
					$error=false;

					if($WooCommerce->isEnable($bookingForm['meta']))
					{
						if(!$User->isSignIn())
						{
							if(((int)$data['client_account']===0) && ((int)$bookingForm['meta']['woocommerce_account_enable_type']===2))
							{
								$this->setErrorGlobal($response,__('Login to your account or provide all needed details.','chauffeur-booking-system'));   
							}
						}
					}

					if(!$error)
					{
						if($Validation->isEmpty($data['client_contact_detail_first_name']))
							$this->setErrorLocal($response,CHBSHelper::getFormName('client_contact_detail_first_name',false),__('Enter your first name','chauffeur-booking-system'));
						if($Validation->isEmpty($data['client_contact_detail_last_name']))
							$this->setErrorLocal($response,CHBSHelper::getFormName('client_contact_detail_last_name',false),__('Enter your last name','chauffeur-booking-system'));
						if(!$Validation->isEmailAddress($data['client_contact_detail_email_address']))
							$this->setErrorLocal($response,CHBSHelper::getFormName('client_contact_detail_email_address',false),__('Enter valid e-mail address','chauffeur-booking-system'));
						if(in_array('client_contact_detail_phone_number',$bookingForm['meta']['field_mandatory']))
						{
							if($Validation->isEmpty($data['client_contact_detail_phone_number']))
								$this->setErrorLocal($response,CHBSHelper::getFormName('client_contact_detail_phone_number',false),__('Please enter valid phone number.','chauffeur-booking-system'));
						}
						if((int)$bookingForm['meta']['billing_detail_state']!=4)
						{
							if(((int)$data['client_billing_detail_enable']===1) || ((int)$bookingForm['meta']['billing_detail_state']===3))
							{
								if(in_array('client_billing_detail_company_name',$bookingForm['meta']['field_mandatory']))
								{	
									if($Validation->isEmpty($data['client_billing_detail_company_name']))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_billing_detail_company_name',false),__('Enter valid company name.','chauffeur-booking-system'));			   
								}
								if(in_array('client_billing_detail_tax_number',$bookingForm['meta']['field_mandatory']))
								{							
									if($Validation->isEmpty($data['client_billing_detail_tax_number']))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_billing_detail_tax_number',false),__('Enter valid tax number.','chauffeur-booking-system'));			   
								}
								if(in_array('client_billing_detail_street_name',$bookingForm['meta']['field_mandatory']))
								{
									if($Validation->isEmpty($data['client_billing_detail_street_name']))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_billing_detail_street_name',false),__('Enter street name','chauffeur-booking-system'));			   
								}
								if(in_array('client_billing_detail_street_number',$bookingForm['meta']['field_mandatory']))
								{
									if($Validation->isEmpty($data['client_billing_detail_street_number']))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_billing_detail_street_number',false),__('Enter valid street number.','chauffeur-booking-system'));			   
								}	
								if(in_array('client_billing_detail_city',$bookingForm['meta']['field_mandatory']))
								{
									if($Validation->isEmpty($data['client_billing_detail_city']))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_billing_detail_city',false),__('Enter city name','chauffeur-booking-system'));				 
								}							
								if(in_array('client_billing_detail_state',$bookingForm['meta']['field_mandatory']))
								{
									if($Validation->isEmpty($data['client_billing_detail_state']))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_billing_detail_state',false),__('Enter valid state name.','chauffeur-booking-system'));				
								}
								if(in_array('client_billing_detail_postal_code',$bookingForm['meta']['field_mandatory']))	
								{
									if($Validation->isEmpty($data['client_billing_detail_postal_code'] ?? ''))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_billing_detail_postal_code',false),__('Enter valid postal code.','chauffeur-booking-system'));				  
								}
								if(in_array('client_billing_detail_country_code',$bookingForm['meta']['field_mandatory']))
								{
									if(!$Country->isCountry($data['client_billing_detail_country_code']))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_billing_detail_country_code',false),__('Enter valid country name.','chauffeur-booking-system')); 
								}							
							}
						}

						if($WooCommerce->isEnable($bookingForm['meta']))
						{
							if(!$User->isSignIn())
							{
								if(((int)$data['client_sign_up_enable']===1) || ((int)$bookingForm['meta']['woocommerce_account_enable_type']===2))
								{
									$validationResult=$User->validateCreateUser($data['client_contact_detail_email_address'],$data['client_sign_up_login'],$data['client_sign_up_password'],$data['client_sign_up_password_retype']);

									if(in_array('EMAIL_INVALID',$validationResult))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_contact_detail_email_address',false),__('E-mail address is invalid.','chauffeur-booking-system')); 
									if(in_array('EMAIL_EXISTS',$validationResult))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_contact_detail_email_address',false),__('E-mail address already exists','chauffeur-booking-system'));							 

									if(in_array('LOGIN_INVALID',$validationResult))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_sign_up_login',false),__('Login cannot be empty.','chauffeur-booking-system'));							 
									if(in_array('LOGIN_EXISTS',$validationResult))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_sign_up_login',false),__('Login already exists.','chauffeur-booking-system'));							   

									if(in_array('PASSWORD1_INVALID',$validationResult))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_sign_up_password',false),__('Password cannot be empty.','chauffeur-booking-system'));							   
									if(in_array('PASSWORD2_INVALID',$validationResult))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_sign_up_password_retype',false),__('Password cannot be empty.','chauffeur-booking-system'));							 
									if(in_array('PASSWORD_MISMATCH',$validationResult))
										$this->setErrorLocal($response,CHBSHelper::getFormName('client_sign_up_password_retype',false),__('Passwords are not the same.','chauffeur-booking-system'));							  
								}
							}
						}

						$error=$BookingFormElement->validateField($bookingForm['meta'],$data);
						foreach($error as $errorValue)
							$this->setErrorLocal($response,$errorValue['name'],$errorValue['message_error']); 

						if(!CHBSBookingHelper::isPayment($data['payment_id'],$bookingForm['meta'],3))
							$this->setErrorGlobal($response,__('Select a payment method.','chauffeur-booking-system'));		
					}

					if(isset($response['error']))
					{
						$data['step']=$data['step_request']=$response['step']=3;
					} 
				}
				
				if(!$WooCommerce->isAddToCartEnable($bookingForm))
				{
					$BookingFormElement=new CHBSBookingFormElement();
					$response['agreement_html']=$BookingFormElement->createAgreement($bookingForm['meta'],$data['service_type_id']);
				}
			}
		}
		
		/***/
		
		if(!isset($response['error']))
		{
			if($data['step_request']>4)
			{
				if(!in_array(4,$bookingForm['step']['disable']))
				{
					if(!$WooCommerce->isAddToCartEnable($bookingForm))
					{
						$error=$BookingFormElement->validateAgreement($bookingForm['meta'],$data);
						if($error)
							$this->setErrorGlobal($response,__('Approve all agreements.','chauffeur-booking-system'));
					}
				}
				
				if(isset($response['error']))
				{
					$response['step']=4;
				}
				else
				{
					$Booking=new CHBSBooking();

					$bookingId=$Booking->sendBooking($data,$bookingForm);
					
					$response['booking_id']=$bookingId;  
					
					/***/
					
					$booking=$Booking->getBooking($bookingId);
					
					if(((int)$bookingForm['meta']['payment_processing_enable']===1) && ((int)$booking['meta']['business_user_paid']!==1))
					{
						if(!$WooCommerce->isEnable($bookingForm['meta']))
						{
							if(!$Payment->isPayment($data['payment_id']))
							{
								$data['payment_id']=0;
							}
							
							/***/
							
							$response['step']=5;
							$response['payment_id']=(int)$data['payment_id'];  

							$bookingBilling=$Booking->createBilling($bookingId);			  
							
							if($response['payment_id']!==0)
							{
								$payment=$Payment->getPayment($data['payment_id']);

								$response['payment_info']=nl2br(esc_html($bookingForm['meta']['payment_'.$payment[1].'_info']));

								$response['button_back_to_home_label']=esc_html($bookingForm['meta']['thank_you_page_button_back_to_home_label']);
								$response['button_back_to_home_url_address']=esc_url($bookingForm['meta']['thank_you_page_button_back_to_home_url_address']);

								$response['payment_prefix']=preg_replace('/_/','-',$payment[1]);
							}
							
							/***/
							
							$r=apply_filters('chbs_payment_prepare_request_data',null,$response,$bookingForm,$booking,$bookingBilling,$data['post_id']);
							
							if($r===false)
							{
								$this->setErrorGlobal($response,__('An error occurs during processing this payment. Plugin cannot continue the work.','chauffeur-booking-system'));
							}
							else if((is_array($r)) && (array_key_exists('payment_process',$r)))
							{
								$response=$r;
							}
							else
							{
								if(!$Payment->isPaymentOnline($response['payment_id']))
								{
									$response['payment_'.$Payment->getPaymentPrefix($data['payment_id']).'_success_url_address']=esc_url($bookingForm['meta']['payment_'.$Payment->getPaymentPrefix($data['payment_id']).'_success_url_address']);
								}
								elseif($data['payment_id']==2)
								{
									$PaymentStripe=new CHBSPaymentStripe();

									$sessionId=$PaymentStripe->createSession($booking,$bookingBilling,$bookingForm);

									$response['stripe_session_id']=$sessionId;
									$response['stripe_redirect_duration']=$bookingForm['meta']['payment_stripe_redirect_duration'];
									$response['stripe_publishable_key']=$bookingForm['meta']['payment_stripe_api_key_publishable'];

									if($sessionId===false)
									{
										$this->setErrorGlobal($response,__('An error occurs during processing this payment. Plugin cannot continue the work.','chauffeur-booking-system'));
									}
								}
								elseif($data['payment_id']==3)
								{
									$response['form']['item_name']=$booking['post']->post_title;
									$response['form']['item_number']=$booking['post']->ID;

									$response['form']['currency_code']=$booking['meta']['currency_id'];

									$response['form']['amount']=$bookingBilling['summary']['pay'];

									$response['payment_paypal_redirect_duration']=$bookingForm['meta']['payment_paypal_redirect_duration'];
								}
								else
								{
									$response['payment_id']=-2;
									$response['payment_disable_success_url_address']=esc_url($bookingForm['meta']['payment_disable_success_url_address']);
								}
							}
						}
						else
						{
							$response['step']=5;
							$response['payment_id']=-1;
							$response['payment_url']=$WooCommerce->getPaymentURLAddress($bookingId+1);
							
							if($Validation->isNotEmpty($response['payment_url']))
								$response['thank_you_page_enable']=$bookingForm['meta']['thank_you_page_enable'];
							else $response['thank_you_page_enable']=1;
						}
					}
					else
					{
						$response['payment_id']=-2;
						$response['payment_disable_success_url_address']=esc_url($bookingForm['meta']['payment_disable_success_url_address']);					
					}
				}
			}
		}
						
		/***/
		/***/
		
		if(($data['step_request']==2) || (($data['step']==1) && ((int)$data['vehicle_price_calculation_first_step_action']!=0)))
		{
			$redirectToUrlAddress=null;
			
			$vehicleHtml=$this->vehicleFilter(false,$redirectToUrlAddress,$vehiclePriceCalculationStepFirst);
			
			if($Validation->isNotEmpty($vehicleHtml))
			{
				$response['vehicle']=$vehicleHtml;
				$response['vehicle_passenger_filter_field']=$this->createVehiclePassengerFilterField($bookingForm['vehicle_passenger_count_range']['min'],$bookingForm['vehicle_passenger_count_range']['max'],$passengerSum);
			
				$response['vehicle_price_calculation_step_first']=$vehiclePriceCalculationStepFirst;
				
				if(!is_null($redirectToUrlAddress))
				{
					$response['redirect_to_url_address']=$redirectToUrlAddress;
				}
			}
			else 
			{
				$response['step']=1;
				$this->setErrorGlobal($response,__('There are no vehicles that match your filter criteria.','chauffeur-booking-system'));
				$this->createFormResponse($response);
			}

			$response['booking_extra']=$this->createBookingFormExtra($bookingForm,$data);
		}
		
		/***/
		
		if($data['step_request']>=3)
		{
			$userData=array();
			
			$User=new CHBSUser();
			$WooCommerce=new CHBSWooCommerce();
			
			if($bookingForm['booking_edit']->isBookingEdit())
			{
				$userData=$bookingForm['booking_edit']->booking['booking']['_meta']['user_data'];
			}
			else
			{
				if(($WooCommerce->isEnable($bookingForm['meta'])) && ($User->isSignIn()))
				{
					if(!array_key_exists('client_contact_detail_first_name',$data))
					{
						$userData=$WooCommerce->getUserData();
					}
				}

				if(!array_key_exists('client_contact_detail_first_name',$data))
				{
					$userData['client_billing_detail_country_code']=$bookingForm['client_country_code'];
				}
			}
			
			$response['client_form_sign_in']=$this->createClientFormSignIn($bookingForm);
			$response['client_form_sign_up']=$this->createClientFormSignUp($bookingForm,$userData);
			
			$response['payment_form']=$this->createPaymentForm($bookingForm);
		}
		
		/***/
		
		if(!isset($response['error']))
		{
			$response['step']=$data['step_request'];
			$data['step']=$response['step'];
		}
		else $data['step_request']=$data['step'];
		
		$response['summary']=$this->createSummary($data,$bookingForm);
		
		$this->createFormResponse($response);
		
		/***/
	}
	
	/**************************************************************************/
	
	function getDatePeriod($data,$bookingForm,$type,$delta,$bookingPeriodType=-1)
	{
		$date=array();
		
		if($bookingPeriodType===-1)
			$bookingPeriodType=$bookingForm['meta']['booking_period_type'];

		if((int)$bookingPeriodType===1)
		{
			$date[0]=$data[$type.'_date_service_type_'.$data['service_type_id']];
			$date[1]=date_i18n('d-m-Y',CHBSDate::strtotime('+'.$delta.' days'));
		}
		elseif((int)$bookingPeriodType===2)
		{
			$date[0]=$data[$type.'_date_service_type_'.$data['service_type_id']].' '.$data[$type.'_time_service_type_'.$data['service_type_id']];;
			$date[1]=date_i18n('d-m-Y H:i',CHBSDate::strtotime('+'.$delta.' hours'));							
		}
		elseif((int)$bookingPeriodType===3)
		{
			$date[0]=$data[$type.'_date_service_type_'.$data['service_type_id']].' '.$data[$type.'_time_service_type_'.$data['service_type_id']];;
			$date[1]=date_i18n('d-m-Y H:i',CHBSDate::strtotime('+'.$delta.' minutes'));							
		} 
		
		return($date);
	}

	/**************************************************************************/
	
	function setErrorLocal(&$response,$field,$message)
	{
		if(!isset($response['error']))
		{
			$response['error']['local']=array();
			$response['error']['global']=array();
		}
		
		array_push($response['error']['local'],array('field'=>$field,'message'=>$message));
	}
	
	/**************************************************************************/
	
	function setErrorGlobal(&$response,$message)
	{
		if(!isset($response['error']))
		{
			$response['error']['local']=array();
			$response['error']['global']=array();
		}
		
		array_push($response['error']['global'],array('message'=>$message));
	}
	
	/**************************************************************************/
	
	function createFormResponse($response)
	{
		echo json_encode($response);
		exit();		
	}
	
	/**************************************************************************/
	
	function createSummaryPriceElementAjax($bid=false)
	{
		$Date=new CHBSDate();
		
		$response=array();
		
		$data=CHBSHelper::getPostOption();
		
		$serviceTypeId=$data['service_type_id'];
		
		$data['pickup_date_service_type_'.$serviceTypeId]=$Date->formatDateToStandard($data['pickup_date_service_type_'.$serviceTypeId]);
		$data['pickup_time_service_type_'.$serviceTypeId]=$Date->formatTimeToStandard($data['pickup_time_service_type_'.$serviceTypeId]);  
		
		if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
		{
			$response['html']=null;
			$this->createFormResponse($response);
		}
		
		CHBSBookingHelper::getPriceType($data['booking_form'],$priceType,$sumType,$showTax,$data['step']);
		
		$price=array();
		
		$response['html']=$this->createSummaryPriceElement($data,$bookingForm,$price);
		
		if($bid)
		{
			if(array_key_exists('bid_min',$price['other']))
			{
				$response['bid_vehicle_min_value']=$price['other']['bid_min'];
				$response['bid_vehicle_min_format']=CHBSPrice::format($response['bid_vehicle_min_value'],CHBSCurrency::getFormCurrency());
				$response['bid_question']=sprintf(__('A minimum price to use is %s. Would like to continue with this value?','chauffeur-booking-system'),html_entity_decode($response['bid_vehicle_min_format']));
			}	
			else if(array_key_exists('bid_value',$price['other']))
			{
				$response['bid_notice']=__('Your BID amount accepted.','chauffeur-booking-system');
			}
		}
		
		$this->createFormResponse($response);
	}
	
	/**************************************************************************/
	
	function createSummaryPriceElement($data,$bookingForm,&$price)
	{
		if((int)$bookingForm['meta']['price_hide']===1)
		{
			return(null);
		}
		
		$Coupon=new CHBSCoupon();
		$Booking=new CHBSBooking();
		
		$data['booking_form']=$bookingForm;
			  
		if(($price=$Booking->calculatePrice($data,null,$data['booking_form']['meta']['booking_summary_hide_fee'],true))===false) return(null);
		
		$priceWithoutCoupon=null;
		if((int)$data['booking_form']['meta']['coupon_discount_value_show']===1)
		{
			$coupon=$Coupon->checkCode();
			
			if($coupon!==false)
			{
				if(($priceWithoutCoupon=$Booking->calculatePrice($data,null,$data['booking_form']['meta']['booking_summary_hide_fee'],true,false))===false) return(null);
			}
		}
		
		CHBSBookingHelper::getPriceType($data['booking_form'],$priceType,$sumType,$showTax,$data['step']);
		
		if(in_array((int)$data['booking_form']['meta']['booking_summary_hide_fee'],array(0,2)))
		{
			if($price['initial']['sum'][$priceType]['value']!=0)
			{
				$html.=
				'
					<div class="chbs-summary-price-element-deliver-fee">
						<span>'.__('Initial fee','chauffeur-booking-system').'</span>
						<span>'.$price['initial']['sum'][$priceType]['format'].'</span>
					</div>
				';
			}
			if($price['delivery']['sum'][$priceType]['value']!=0)
			{
				$html.=
				'
					<div class="chbs-summary-price-element-deliver-fee">
						<span>'.__('Delivery fee','chauffeur-booking-system').'</span>
						<span>'.$price['delivery']['sum'][$priceType]['format'].'</span>
					</div>
				';
			}
			if($price['delivery_return']['sum'][$priceType]['value']!=0)
			{
				$html.=
				'
					<div class="chbs-summary-price-element-deliver-fee">
						<span>'.__('Return to base fee','chauffeur-booking-system').'</span>
						<span>'.$price['delivery_return']['sum'][$priceType]['format'].'</span>
					</div>
				';
			}
			if($price['extra_time']['sum'][$priceType]['value']!=0)
			{
				$html.=
				'
					<div class="chbs-summary-price-element-time-extra">
						<span>'.__('Extra time','chauffeur-booking-system').'</span>
						<span>'.$price['extra_time']['sum'][$priceType]['format'].'</span>
					</div>
				';
			}
			if($price['waypoint']['sum'][$priceType]['value']!=0)
			{
				$html.=
				'
					<div class="chbs-summary-price-element-waypoint">
						<span>'.__('Waypoints','chauffeur-booking-system').'</span>
						<span>'.$price['waypoint']['sum'][$priceType]['format'].'</span>
					</div>
				';
			}
			if($price['waypoint_duration']['sum'][$priceType]['value']!=0)
			{
				$html.=
				'
					<div class="chbs-summary-price-element-waypoint-duration">
						<span>'.__('Waypoints duration','chauffeur-booking-system').'</span>
						<span>'.$price['waypoint_duration']['sum'][$priceType]['format'].'</span>
					</div>
				';
			}
		}
		
		if($price['vehicle']['sum'][$priceType]['value']!=0)
		{
			$priceVehicleIndex=in_array((int)$data['booking_form']['meta']['booking_summary_hide_fee'],array(2)) ? 'vehicle_exclude_fee' : 'vehicle';
		
			$html.=
			'
				<div class="chbs-summary-price-element-vehicle-fee">
					<span>'.__('Selected vehicle','chauffeur-booking-system').'</span>
					<span>'.$price[$priceVehicleIndex]['sum'][$priceType]['format'].'</span>
				</div>
			';
			
			if(in_array($data['service_type_id'],array(1,3)))
			{
				if(in_array($data['transfer_type_service_type_'.$data['service_type_id']],$bookingForm['meta']['vehicle_sum_split']))
				{
					$html.=
					'
						<div class="chbs-summary-price-element-vehicle-fee-one-way">
							<span>'.__('One way','chauffeur-booking-system').'</span>
							<span>'.$price['vehicle']['sum_one_way'][$priceType]['format'].'</span>
						</div>
						<div class="chbs-summary-price-element-vehicle-fee-return">
							<span>'.__('Return','chauffeur-booking-system').'</span>
							<span>'.$price['vehicle']['sum_return'][$priceType]['format'].'</span>
						</div>
					';
				}
			}
		}
		
		if($price['booking_extra']['sum'][$priceType]['value']!=0)
		{		
			$html.=
			'
				<div class="chbs-summary-price-element-booking-extra">
					<span>'.__('Extra options','chauffeur-booking-system').'</span>
					<span>'.$price['booking_extra']['sum'][$priceType]['format'].'</span>
				</div>
			';   
		}
		
		if(($priceType==='net') && ($showTax))
		{
			if($price['tax']['sum']['value']!=0)
			{
				$html.=
				'
					<div class="chbs-summary-price-element-booking-extra">
						<span>'.__('Tax','chauffeur-booking-system').'</span>
						<span>'.$price['tax']['sum']['format'].'</span>
					</div>
				';
			}
		}
		
		if($price['gratuity']['value']>0.00)
		{
			$html.=
			'
				<div class="chbs-summary-price-element-booking-extra">
					<span>'.__('Gratuity','chauffeur-booking-system').'</span>
					<span>'.$price['gratuity']['format'].'</span>
				</div>
			';				   
		}
		
		if($price['paypal_fee']['value']!=0)
		{
			$html.=
			'
				<div class="chbs-summary-price-element-booking-extra">
					<span>'.__('PayPal fee','chauffeur-booking-system').'</span>
					<span>'.$price['paypal_fee']['format'].'</span>
				</div>
			';				   
		}		
		if($price['stripe_fee']['value']!=0)
		{
			$html.=
			'
				<div class="chbs-summary-price-element-booking-extra">
					<span>'.__('Stripe fee','chauffeur-booking-system').'</span>
					<span>'.$price['stripe_fee']['format'].'</span>
				</div>
			';				   
		}	
		
		/****/
		
		$totalLabel=__('Total','chauffeur-booking-system');
		
		$discount=CHBSBookingHelper::calculateCouponDiscount($price,$priceWithoutCoupon);
		if($discount!==false)
		{
			$totalLabel=sprintf(__('Total <span>(incl. %s discount)</span>','chauffeur-booking-system'),$discount);
		}
		
		/****/
		
		$html.=
		'
			<div class="chbs-summary-price-element-total">
				<span>'.$totalLabel.'</span>
				<span>'.$price['total']['sum'][$sumType]['format'].'</span>
			</div>
		';	

		/***/
		
		if(CHBSBookingHelper::isPaymentDepositEnable($data['booking_form']['meta']))
		{
			$html.=
			'
				<div class="chbs-summary-price-element-pay">
					<span>'.sprintf(__('To pay <span>(%s%% deposit)</span>','chauffeur-booking-system'),$bookingForm['meta']['payment_deposit_value']).'</span>
					<span>'.$price['pay']['sum']['gross']['format'].'</span>
				</div>
			';
		}

		$html=
		'
			<div class="chbs-summary-price-element">
				'.$html.'
			</div>
		';

		return($html);
	}
	
	/**************************************************************************/
	
	function createSummary($data,$bookingForm)
	{
		$response=array();
		
		$User=new CHBSUser();
		$Date=new CHBSDate();
		$Length=new CHBSLength();
		$Country=new CHBSCountry();
		$TaxRate=new CHBSTaxRate();
		$Duration=new CHBSDuration();
		$Validation=new CHBSValidation();
		$WooCommerce=new CHBSWooCommerce();
		$ServiceType=new CHBSServiceType();
		$TransferType=new CHBSTransferType();
		$BookingExtra=new CHBSBookingExtra();
		$BookingGratuity=new CHBSBookingGratuity();
		$BookingFormSummary=new CHBSBookingFormSummary();
		
		$serviceType=$ServiceType->getServiceType($data['service_type_id']);
		
		/***/
		
		$taxRateDictionary=$TaxRate->getDictionary();
		
		/***/
  
		$price=array();
		
		$priceHtml=apply_filters('chbs_booking_summary_before_price',null,$data['booking_form_id'],$data['step']);
		$priceHtml.=$this->createSummaryPriceElement($data,$bookingForm,$price);
		
		/***/
		
		$pickupDate=$Date->formatDateToDisplay($data['pickup_date_service_type_'.$data['service_type_id']]);
		$pickupTime=$Date->formatTimeToDisplay($data['pickup_time_service_type_'.$data['service_type_id']]);
		
		/***/
				
		$bookingExtraHtml=array();
		
		if((int)$bookingForm['meta']['price_hide']===0)
		{
			$bookingExtra=$BookingExtra->validate($data,$bookingForm,$taxRateDictionary);
			foreach($bookingExtra as $value)
			{
				$dictionary=$bookingForm['dictionary']['booking_extra'][$value['id']];
				$dictionary['quantity']=$value['quantity'];

				$bookingExtraPrice=$BookingExtra->calculatePrice($dictionary,$bookingForm,$data);
				
				$note=null;
				if($Validation->isNotEmpty($value['note']))
					$note=' - <span class="chbs-summary-field-value-booking-extra-note">'.esc_html($value['note']).'</span>';

				array_push($bookingExtraHtml,$value['quantity'].' x '.$value['name'].' - '.$bookingExtraPrice['sum']['gross']['format'].$note);
			}
		}
		
		/***/
		
		$userHtml=null;
		$userBusinessAccountAmountHtml=null;
		
		if($WooCommerce->isEnable($bookingForm['meta']))
		{
			if($User->isSignIn())
			{
				$userData=$User->getCurrentUserData();
				$userHtml=$userData->data->display_name;
				
				if(($amount=$User->getUserBusinessAccountAmount($bookingForm,$pickupDate))!==false)
					$userBusinessAccountAmountHtml=$amount;
			}
		}
		
		/***/
		
		$routeHtml=array();
		
		if(in_array($data['service_type_id'],array(1,2)))
		{
			$waypointLocationHtml=null;
						
			if(count($bookingForm['meta']['location_fixed_pickup_service_type_'.$data['service_type_id']]))
			{
				$pickupLocationId=$data['fixed_location_pickup_service_type_'.$data['service_type_id']];
				$pickupLocationHtml=$bookingForm['meta']['location_fixed_pickup_service_type_'.$data['service_type_id']][$pickupLocationId]['address'];				
			}
			else
			{
				$pickupLocation=json_decode($data['pickup_location_coordinate_service_type_'.$data['service_type_id']]);
				$pickupLocationHtml=$pickupLocation->{'address'};
								
				if(($data['service_type_id']==1) && ($bookingForm['meta']['waypoint_enable']==1))
				{
					if(is_array($data['waypoint_location_coordinate_service_type_1']))
					{
						foreach($data['waypoint_location_coordinate_service_type_1'] as $index=>$value)
						{
							$waypointLocation=json_decode($value);
							
							if($Validation->isNotEmpty($waypointLocationHtml)) $waypointLocationHtml.="\n";
														
							$waypointLocationHtml.=$waypointLocation->{'address'};	
							
							if((int)$bookingForm['meta']['waypoint_duration_enable']===1)
							{
								$waypointLocationHtml.=sprintf(__(' (%s minutes)','chauffeur-booking-system'),(int)$data['waypoint_duration_service_type_1'][$index]);
							}
						}
					}
				}
			}
			
			if(count($bookingForm['meta']['location_fixed_dropoff_service_type_'.$data['service_type_id']]))
			{
				$dropoffLocationId=$data['fixed_location_dropoff_service_type_'.$data['service_type_id']];
				$dropoffLocationHtml=$bookingForm['meta']['location_fixed_dropoff_service_type_'.$data['service_type_id']][$dropoffLocationId]['address'];
			}
			else
			{
				$dropoffLocation=json_decode($data['dropoff_location_coordinate_service_type_'.$data['service_type_id']]);
				$dropoffLocationHtml=$dropoffLocation->{'address'};				   
			}
			
			if($data['service_type_id']==1)
			{
				$routeHtml[0]=array('label'=>__('Pickup location','chauffeur-booking-system'),'value'=>$pickupLocationHtml);
				$routeHtml[1]=array('label'=>__('Waypoints','chauffeur-booking-system'),'value'=>$waypointLocationHtml);
				$routeHtml[2]=array('label'=>__('Drop-off location','chauffeur-booking-system'),'value'=>$dropoffLocationHtml);
			}
			else $routeHtml[0]=array('label'=>__('Pickup location','chauffeur-booking-system'),'value'=>$pickupLocationHtml);
		}
		else
		{
			if((int)$bookingForm['meta']['route_pickup_location_field_enable']===1)
			{
				if($Validation->isNotEmpty($data['pickup_location_coordinate_service_type_3']))
				{
					$location=json_decode($data['pickup_location_coordinate_service_type_3']);
					$locationHtml=$location->{'address'};
					
					$routeHtml[]=array('label'=>__('Pickup location','chauffeur-booking-system'),'value'=>$locationHtml);
				}
			}
			
			$routeHtml[]=array('label'=>__('Route','chauffeur-booking-system'),'value'=>$bookingForm['dictionary']['route'][$data['route_service_type_3']]['post']->post_title);
			
			if((int)$bookingForm['meta']['route_dropoff_location_field_enable']===1)
			{
				if($Validation->isNotEmpty($data['dropoff_location_coordinate_service_type_3']))
				{
					$location=json_decode($data['dropoff_location_coordinate_service_type_3']);
					$locationHtml=$location->{'address'};
					
					$routeHtml[]=array('label'=>__('Drop-off location','chauffeur-booking-system'),'value'=>$locationHtml);
				}
			}
		}

		/***/
		
		$returnDate=null;
		$returnTime=null;
		$transferType=null;

		if(in_array($data['service_type_id'],array(1,3)))
		{
			if((count($bookingForm['meta']['transfer_type_enable_1'])) || (count($bookingForm['meta']['transfer_type_enable_3'])))
			{
				$transferType=$TransferType->getTransferTypeName($data['transfer_type_service_type_'.$data['service_type_id']]);
				
				if((int)$data['transfer_type_service_type_'.$data['service_type_id']]===3)
				{
					if($Validation->isNotEmpty($data['return_date_service_type_'.$data['service_type_id']]))
						$returnDate=$Date->formatDateToDisplay($data['return_date_service_type_'.$data['service_type_id']]);
					if($Validation->isNotEmpty($data['return_time_service_type_'.$data['service_type_id']]))
						$returnTime=$Date->formatTimeToDisplay($data['return_time_service_type_'.$data['service_type_id']]); 
				}
			}			
		}

		/***/
		
		$passengerHtml=null;
		if((CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult')) || (CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'children')))
			$passengerHtml=CHBSBookingHelper::getPassengerLabel($data['passenger_adult_service_type_'.$data['service_type_id']],$data['passenger_children_service_type_'.$data['service_type_id']],1,$bookingForm['meta']['passenger_use_person_label']);
		
		/***/
		
		$distanceIncluded=CHBSBookingHelper::getDistanceIncluded($data,$bookingForm);
		
		switch($data['step_request'])
		{
			case 2:
				
				if(!is_null($userHtml))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Logged as','chauffeur-booking-system'),
							$userHtml
						)
					);					
				}
				
				if(!is_null($userBusinessAccountAmountHtml))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Business account credits','chauffeur-booking-system'),
							$userBusinessAccountAmountHtml
						)
					);					
				}
				
				$BookingFormSummary->add
				(
					array
					(
						__('Service type','chauffeur-booking-system'),
						$serviceType[0]
					)
				);
				
				if($Validation->isNotEmpty($transferType))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Transfer type','chauffeur-booking-system'),
							$transferType
						)
					);					
				}
				
				foreach($routeHtml as $routeHtmlData)
				{
					$BookingFormSummary->add
					(
						array
						(
							$routeHtmlData['label'],
							$routeHtmlData['value']
						)
					);
				}
				
				if($data['service_type_id']==2)
				{
					if($Validation->isNotEmpty($dropoffLocationHtml))
					{
						$BookingFormSummary->add
						(
							array
							(
								__('Drop-off location','chauffeur-booking-system'),
								$dropoffLocationHtml
							)
						);
					}
				}
				
				$BookingFormSummary->add
				(
					array
					(
						__('Pickup date, time','chauffeur-booking-system'),
						$pickupDate.', '.$pickupTime
					)
				);
				
				if(($Validation->isNotEmpty($returnDate)) && ($Validation->isNotEmpty($returnTime)))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Return date, time','chauffeur-booking-system'),
							$returnDate.', '.$returnTime
						)
					);
				}
				
				if(($bookingForm['meta']['extra_time_enable']==1) && (in_array($data['service_type_id'],array(1,3))))
				{
					$value=$data['extra_time_service_type_'.$data['service_type_id']];
					
					if($value>0)
					{
						$BookingFormSummary->add
						(
							array
							(
								__('Extra time','chauffeur-booking-system'),
								sprintf(((int)$bookingForm['meta']['extra_time_unit']===1 ? __('%s minutes','chauffeur-booking-system') : __('%s hours','chauffeur-booking-system')),$value)
							)
						);
					}
				}
				
				/***/
				
				if($distanceIncluded===-1)
				{
					$field=array
					(
						array
						(
							__('Total distance','chauffeur-booking-system'),
							$Length->format($data['distance_sum'])
						)
					);
				}
				else
				{
					$field=array
					(
						array
						(
							__('Incl. distance','chauffeur-booking-system'),
							$distanceIncluded
						)
					);					
				}
				
				if((int)$bookingForm['meta']['total_time_display_enable']===1)
				{
					$field[]=array(__('Total time','chauffeur-booking-system'),$Duration->format($data['duration_sum']));					
					$BookingFormSummary->add($field,2);
				}
				else $BookingFormSummary->add($field[0]);
				
				/***/
				
				if($Validation->isNotEmpty($passengerHtml))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Passengers','chauffeur-booking-system'),
							$passengerHtml
						)
					);
				}	 
				
				$response[0]=$BookingFormSummary->create(__('Summary','chauffeur-booking-system')).$priceHtml;
				
			break;
		
			case 3:
				
				if(!is_null($userHtml))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Logged as','chauffeur-booking-system'),
							$userHtml
						)
					);					
				}
				
				if(!is_null($userBusinessAccountAmountHtml))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Business account credits','chauffeur-booking-system'),
							$userBusinessAccountAmountHtml
						)
					);					
				}
				
				$BookingFormSummary->add
				(
					array
					(
						__('Service type','chauffeur-booking-system'),
						$serviceType[0]
					)
				);
				
				if($Validation->isNotEmpty($transferType))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Transfer type','chauffeur-booking-system'),
							$transferType
						)
					);					
				}
				
				foreach($routeHtml as $routeHtmlData)
				{
					$BookingFormSummary->add
					(
						array
						(
							$routeHtmlData['label'],
							$routeHtmlData['value']
						)
					);
				}
				
				if($data['service_type_id']==2)
				{
					if($Validation->isNotEmpty($dropoffLocationHtml))
					{
						$BookingFormSummary->add
						(
							array
							(
								__('Drop-off location','chauffeur-booking-system'),
								$dropoffLocationHtml
							)
						);
					}
				}
				
				$BookingFormSummary->add
				(
					array
					(
						__('Pickup date, time','chauffeur-booking-system'),
						$pickupDate.', '.$pickupTime
					)
				);
				
				if(($Validation->isNotEmpty($returnDate)) && ($Validation->isNotEmpty($returnTime)))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Return date, time','chauffeur-booking-system'),
							$returnDate.', '.$returnTime
						)
					);
				}				
				
				if(($bookingForm['meta']['extra_time_enable']==1) && (in_array($data['service_type_id'],array(1,3))))
				{
					$value=$data['extra_time_service_type_'.$data['service_type_id']];
					
					if($value>0)
					{
						$BookingFormSummary->add
						(
							array
							(
								__('Extra time','chauffeur-booking-system'),
								sprintf(((int)$bookingForm['meta']['extra_time_unit']===1 ? __('%s minutes','chauffeur-booking-system') : __('%s hours','chauffeur-booking-system')),$value)
							)
						);
					}
				}
				
				/***/
				
				if($distanceIncluded===-1)
				{
					$field=array
					(
						array
						(
							__('Total distance','chauffeur-booking-system'),
							$Length->format($data['distance_sum'])
						)
					);
				}
				else
				{
					$field=array
					(
						array
						(
							__('Incl. distance','chauffeur-booking-system'),
							$distanceIncluded
						)
					);					
				}
				
				if((int)$bookingForm['meta']['total_time_display_enable']===1)
				{
					$field[]=array(__('Total time','chauffeur-booking-system'),$Duration->format($data['duration_sum']));					
					$BookingFormSummary->add($field,2);
				}
				else $BookingFormSummary->add($field[0]);
				
				/***/	
				
				$BookingFormSummary->add
				(
					array
					(
						__('Vehicle','chauffeur-booking-system'),
						$bookingForm['dictionary']['vehicle'][$data['vehicle_id']]['post']->post_title
					)
				);
				
				if(count($bookingExtraHtml))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Extra options','chauffeur-booking-system'),
							$bookingExtraHtml
						),
						3
					);
				}
				
				if($Validation->isNotEmpty($passengerHtml))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Passengers','chauffeur-booking-system'),
							$passengerHtml
						)
					);
				}	   
				
				$response[0]=$BookingFormSummary->create(__('Summary','chauffeur-booking-system')).$priceHtml;
				
			break;
		
			case 4:
				
				/***/
				
				$BookingFormElement=new CHBSBookingFormElement();
				$panel=$BookingFormElement->getPanel($bookingForm['meta']);
				
				if(!is_null($userHtml))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Logged as','chauffeur-booking-system'),
							$userHtml
						)
					);					
				}
				
				if(!is_null($userBusinessAccountAmountHtml))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Business account credits','chauffeur-booking-system'),
							$userBusinessAccountAmountHtml
						)
					);					
				}
				
				$BookingFormSummary->add
				(
					array
					(
						array
						(
							__('First name','chauffeur-booking-system'),
							$data['client_contact_detail_first_name']
						),
						array
						(
							__('Last name','chauffeur-booking-system'),
							$data['client_contact_detail_last_name']
						)
					),
					2
				);
				
				$BookingFormSummary->add
				(
					array
					(
						__('E-mail address','chauffeur-booking-system'),
						$data['client_contact_detail_email_address']
					)
				);	   
				
				if($Validation->isNotEmpty($data['client_contact_detail_phone_number']))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Phone number','chauffeur-booking-system'),
							$data['client_contact_detail_phone_number']
						)
					);
				}
				
				foreach($panel as $panelValue)
				{
					if(in_array($panelValue['id'],array(1)))
					{
						foreach($bookingForm['meta']['form_element_field'] as $fieldValue)
						{
							if($fieldValue['panel_id']==$panelValue['id'])
							{
								$fieldName='form_element_field_'.$fieldValue['id'];

								$BookingFormSummary->add
								(
									array
									(
										$fieldValue['label'],
										$data[$fieldName]
									)
								); 
							}
						}
					}
				}
				
				/***/
				   
				if(($data['client_billing_detail_enable']==1) && ($bookingForm['meta']['billing_detail_state']!=4))
				{
					$BookingFormSummary->add
					(
						array
						(
							array
							(
								__('Company name','chauffeur-booking-system'),
								$data['client_billing_detail_company_name']
							),
							array
							(
								__('Tax number','chauffeur-booking-system'),
								$data['client_billing_detail_tax_number']
							)
						),
						2
					);
					$BookingFormSummary->add
					(
						array
						(
							__('Billing address','chauffeur-booking-system'),
							array
							(
								$data['client_billing_detail_street_name'].' '.$data['client_billing_detail_street_number'],
								($data['client_billing_detail_postal_code'] ?? '').' '.$data['client_billing_detail_city'],
								$data['client_billing_detail_state'],
								$Country->getCountryName($data['client_billing_detail_country_code'])
							)
						),
						3
					);
				}
				
				foreach($panel as $panelValue)
				{
					if(in_array($panelValue['id'],array(2)))
					{
						foreach($bookingForm['meta']['form_element_field'] as $fieldValue)
						{
							if($fieldValue['panel_id']==$panelValue['id'])
							{
								$fieldName='form_element_field_'.$fieldValue['id'];

								$BookingFormSummary->add
								(
									array
									(
										$fieldValue['label'],
										$data[$fieldName]
									)
								); 
							}
						}
					}
				}
				
				if($Validation->isNotEmpty($data['comment']))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Comments','chauffeur-booking-system'),
							$data['comment']
						)
					);
				}
				
				if($Validation->isNotEmpty($passengerHtml))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Passengers','chauffeur-booking-system'),
							$passengerHtml
						)
					);
				}  

				/***/
				
				$response[0].=$BookingFormSummary->create(__('Contact & Billing Info','chauffeur-booking-system'),3);
				
				/***/
				
				$paymentName=CHBSBookingHelper::getPaymentName($data['payment_id'],-1,$bookingForm['meta']);
				
				if(!is_null($paymentName))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Your choice','chauffeur-booking-system'),
							$paymentName
						)
					);  

					$response[0].=$BookingFormSummary->create(__('Payment Method','chauffeur-booking-system'),3);
				}
				
				/***/
								
				foreach($panel as $panelValue)
				{	
					if(in_array($panelValue['id'],array(1,2))) continue;
					
					foreach($bookingForm['meta']['form_element_field'] as $fieldValue)
					{
						if($fieldValue['panel_id']==$panelValue['id'])
						{
							$fieldName='form_element_field_'.$fieldValue['id'];
					
							$BookingFormSummary->add
							(
								array
								(
									$fieldValue['label'],
									$data[$fieldName]
								)
							); 
						}
					}
					
					$response[0].=$BookingFormSummary->create($panelValue['label'],3);
				}

				/***/
				
				$BookingFormSummary->add
				(
					array
					(
						__('Service type','chauffeur-booking-system'),
						$serviceType[0]
					)
				);
				
				if($Validation->isNotEmpty($transferType))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Transfer type','chauffeur-booking-system'),
							$transferType
						)
					);					
				}
				
				foreach($routeHtml as $routeHtmlData)
				{
					$BookingFormSummary->add
					(
						array
						(
							$routeHtmlData['label'],
							$routeHtmlData['value']
						)
					);
				}
				
				if($data['service_type_id']==2)
				{
					if($Validation->isNotEmpty($dropoffLocationHtml))
					{
						$BookingFormSummary->add
						(
							array
							(
								__('Drop-off location','chauffeur-booking-system'),
								$dropoffLocationHtml
							)
						);
					}
				}
				
				$BookingFormSummary->add
				(
					array
					(
						__('Pickup date, time','chauffeur-booking-system'),
						$pickupDate.', '.$pickupTime
					)
				);
				
				if(($Validation->isNotEmpty($returnDate)) && ($Validation->isNotEmpty($returnTime)))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Return date, time','chauffeur-booking-system'),
							$returnDate.', '.$returnTime
						)
					);
				}
				
				if(($bookingForm['meta']['extra_time_enable']==1) && (in_array($data['service_type_id'],array(1,3))))
				{
					$value=$data['extra_time_service_type_'.$data['service_type_id']];
					
					if($value>0)
					{
						$BookingFormSummary->add
						(
							array
							(
								__('Extra time','chauffeur-booking-system'),
								sprintf(((int)$bookingForm['meta']['extra_time_unit']===1 ? __('%s minutes','chauffeur-booking-system') : __('%s hours','chauffeur-booking-system')),$value)
							)
						);
					}
				}
				
				if($distanceIncluded===-1)
				{
					$field=array
					(
						array
						(
							__('Total distance','chauffeur-booking-system'),
							$Length->format($data['distance_sum'])
						)
					);
				}
				else
				{
					$field=array
					(
						array
						(
							__('Incl. distance','chauffeur-booking-system'),
							$distanceIncluded
						)
					);					
				}
				
				if((int)$bookingForm['meta']['total_time_display_enable']===1)
				{
					$field[]=array(__('Total time','chauffeur-booking-system'),$Duration->format($data['duration_sum']));					
					$BookingFormSummary->add($field,2);
				}
				else $BookingFormSummary->add($field[0]);
				
				$class=array('chbs-google-map-summary');
				
				if((int)$bookingForm['meta']['step_1_right_panel_visibility']!==1)
					array_push($class,'chbs-hidden');
								
				$googleMapHtml='<div'.CHBSHelper::createCSSClassAttribute($class).'></div>';
				
				$response[1]=$googleMapHtml.$BookingFormSummary->create(__('Ride details','chauffeur-booking-system'),1);
				
				/***/
				
				$BookingFormSummary->add
				(
					array
					(
						__('Vehicle','chauffeur-booking-system'),
						$bookingForm['dictionary']['vehicle'][$data['vehicle_id']]['post']->post_title
					)
				);	 
				
				if(count($bookingExtraHtml))
				{
					$BookingFormSummary->add
					(
						array
						(
							__('Extra options','chauffeur-booking-system'),
							$bookingExtraHtml
						),
						3
					);
				}
				
				/***/
				
				$gratuityHtml=null;
				if($BookingGratuity->isEnableCustomer($bookingForm['meta']))
				{
					$gratuityHtml=
					'
						<div class="chbs-clear-fix chbs-gratuity-section">
							<div class="chbs-form-field">
								<label>
									'.__('Gratuity:','chauffeur-booking-system').'
									<span class="chbs-tooltip chbs-meta-icon-question" title="'.esc_html__('Enter value (fixed or percentage) of gratuity. If you wish to enter percentage value, use % sign after value, e.g.: 20% means 20 percent gratuity from net sum of booking.','chauffeur-booking-system').'"></span>	 
								</label>
								<input maxlength="12" name="'.CHBSHelper::getFormName('gratuity_customer_value',false).'" value="'.esc_html(array_key_exists('gratuity_customer_value',$data) ? $data['gratuity_customer_value'] : '').'" type="text">
							</div>
							<a href="#" class="chbs-button chbs-button-style-2">
								'.__('Apply gratuity','chauffeur-booking-system').'
								<span class="chbs-meta-icon-arrow-horizontal"></span>
							</a>
						</div>
					';
				}
				
				/***/
							  
				$couponHtml=null;
				if((int)$bookingForm['meta']['coupon_enable']===1)
				{
					$couponHtml=
					'
						<div class="chbs-clear-fix chbs-coupon-code-section">
							<div class="chbs-form-field">
								<label>'.__('Do you have a discount code?','chauffeur-booking-system').'</label>
								<input maxlength="12" name="'.CHBSHelper::getFormName('coupon_code',false).'" value="'.esc_html(array_key_exists('coupon_code',$data) ? $data['coupon_code'] : '').'" type="text">
							</div>
							<a href="#" class="chbs-button chbs-button-style-2">
								'.__('Apply code','chauffeur-booking-system').'
								<span class="chbs-meta-icon-arrow-horizontal"></span>
							</a>
						</div>
					';
				}
				
				$thumbnailHtml=null;
				$thumbnail=get_the_post_thumbnail_url($data['vehicle_id'],PLUGIN_CHBS_CONTEXT.'_vehicle');
		
				if($thumbnail!==false)
					$thumbnailHtml='<div><img src="'.esc_url($thumbnail).'" alt=""/></div>';
															  
				$response[2]=$thumbnailHtml.$BookingFormSummary->create(__('Vehicle info','chauffeur-booking-system'),2).$gratuityHtml.$couponHtml.$priceHtml;
				
			break;
		}
		
		return($response);
	}
	
	/**************************************************************************/ 
	
	function createVehicle($data,&$priceToSort,&$urlAddress)
	{
		$html=array(null);
		
		$Vehicle=new CHBSVehicle();
		$Validation=new CHBSValidation();
		
		$urlAddress=null;
		
		/***/
		
		$thumbnail=get_the_post_thumbnail_url($data['vehicle_id'],PLUGIN_CHBS_CONTEXT.'_vehicle');
		if($thumbnail!==false)
		{
			$htmlGallery=null;
			
			$galleryImageUrl=array();
			
			foreach($data['vehicle']['meta']['gallery_image_id'] as $value)
			{
				$url=wp_get_attachment_image_src($value,'full');
				if($url!==false) array_push($galleryImageUrl,$url[0]);
			}
			
			if(count($galleryImageUrl))
			{
				foreach($galleryImageUrl as $galleryImageUrlValue)
					$htmlGallery.='<li><img src="'.esc_url($galleryImageUrlValue).'"></li>';
				
				$htmlGallery='<div class="chbs-vehicle-gallery"><ul>'.$htmlGallery.'</ul></div>';
			}
			
			/***/
			
			$alt=null;
			$class=array('chbs-vehicle-image');
			
			if($Validation->isNotEmpty($htmlGallery))
			{
				$alt=__('Click to open vehicle gallery.','chauffeur-booking-system');
				array_push($class,'chbs-vehicle-image-has-gallery');
			}
			
			$html[0]='<div'.CHBSHelper::createCSSClassAttribute($class).'><img src="'.esc_url($thumbnail).'" alt="'.esc_attr($alt).'"/></div>'.$htmlGallery;
		}
			
		/***/
		
		// *rule
		$argument=array
		(
			'booking_form_id'=>$data['booking_form_id'],
			'service_type_id'=>$data['service_type_id'],
			'transfer_type_id'=>$data['transfer_type_id'],
			'pickup_location_coordinate'=>$data['pickup_location_coordinate'],
			'dropoff_location_coordinate'=>$data['dropoff_location_coordinate'],
			'fixed_location_pickup'=>$data['fixed_location_pickup'],
			'fixed_location_dropoff'=>$data['fixed_location_dropoff'],
			'transfer_type_id'=>$data['transfer_type_id'],
			'route_id'=>$data['route_id'],
			'vehicle_id'=>$data['vehicle_id'],
			'pickup_date'=>$data['pickup_date'],
			'pickup_time'=>$data['pickup_time'],
			'return_date'=>$data['return_date'],
			'return_time'=>$data['return_time'],
			'passenger_adult'=>$data['passenger_adult'],
			'passenger_children'=>$data['passenger_children'],
			'base_location_distance'=>$data['base_location_distance'],
			'base_location_return_distance'=>$data['base_location_return_distance'],
			'distance'=>$data['distance'],
			'distance_sum'=>$data['distance_sum'],
			'duration'=>$data['duration'],
			'duration_map'=>$data['duration_map'],
			'duration_sum'=>$data['duration_sum'],
			'booking_form'=>$data['booking_form'],
            'waypoint_count'=>$data['waypoint_count']
		);
		
		$priceWithoutRule=array();
		if(CHBSBookingHelper::isPriceBeforeDiscountEnable($data['booking_form']))
		{
			$priceWithoutRule=$Vehicle->calculatePrice($argument,true,true,array('enable'=>0),false);
		}
		
		$price=$Vehicle->calculatePrice($argument,true,true,array('enable'=>0));
		
		/***/
		
		$htmlDescription=null;
		
		if(CHBSPlugin::isAutoRideTheme())
		{
			if($Validation->isNotEmpty($data['vehicle']['meta']['description']))
				$htmlDescription='<p>'.$data['vehicle']['meta']['description'].'</p>';			
		}
		else
		{
			if($Validation->isNotEmpty($data['vehicle']['post']->post_content))
				$htmlDescription='<p>'.$data['vehicle']['post']->post_content.'</p>';
		}

		if((array_key_exists('attribute',$data['vehicle'])) && (is_array($data['vehicle']['attribute'])))
		{
			$i=0;
			$htmlAttribute=array(null,null);
			$count=ceil(count($data['vehicle']['attribute'])/2);
			
			foreach($data['vehicle']['attribute'] as $value)
			{
				$index=($i++)<$count ? 0 : 1;
				$htmlAttribute[$index].=
				'
					<li class="chbs-clear-fix">
						<div>'.esc_html($value['name']).'</div>
						<div>'.esc_html($value['value']).'</div>
					</li>
				';
			}
			
			if($Validation->isNotEmpty($htmlAttribute[0]))
				$htmlAttribute[0]='<ul class="chbs-list-reset">'.$htmlAttribute[0].'</ul>';
			if($Validation->isNotEmpty($htmlAttribute[1]))
				$htmlAttribute[1]='<ul class="chbs-list-reset">'.$htmlAttribute[1].'</ul>';				
			
			$htmlDescription.=
			'
				<div class="chbs-vehicle-content-description-attribute chbs-clear-fix">
					'.$htmlAttribute[0].'
					'.$htmlAttribute[1].'	
				</div>
			';
		}
		
		if($Validation->isNotEmpty($htmlDescription))
		{
			$classDescription=array('chbs-vehicle-content-description');
			if((int)$data['booking_form']['meta']['vehicle_more_info_default_show']===1)
				array_push($classDescription,'chbs-state-open');
			
			$htmlDescription='<div'.CHBSHelper::createCSSClassAttribute($classDescription).'><div>'.$htmlDescription.'</div></div>';
		}
		
		/****/
		
		$htmlMoreInfo=null;
		if($Validation->isNotEmpty($htmlDescription))
		{
			$htmlMoreInfo=$this->createShowMoreButton($data['booking_form']['meta']['vehicle_more_info_default_show']);
		}

		/***/
		
		$htmlPrice=null;
		if(((int)$data['booking_form']['meta']['price_hide']===0) && (((int)$price['price']['base']['custom_vehicle_selection_enable']===0) || (((int)$price['price']['base']['custom_vehicle_selection_enable']===1) && ((int)$price['price']['base']['custom_vehicle_selection_hide_price']===0))))
		{
			CHBSBookingHelper::getPriceType($data['booking_form'],$priceType,$sumType,$taxShow,$data['step']);
			
			$priceToDisplay=$price['price']['sum'][$priceType]['formatHtml'];
			
			/***/
			
			$priceBeforeDiscount=null;
			if(CHBSBookingHelper::isPriceBeforeDiscountEnable($data['booking_form'],$priceWithoutRule['price']['sum'][$priceType]['value'],$price['price']['sum'][$priceType]['value']))
			{
				$priceBeforeDiscount='<span class="chbs-vehicle-content-price-before-discount">'.$priceWithoutRule['price']['sum'][$priceType]['formatHtml'].'</span>';
			}
			
			/***/
			
			if((int)$data['booking_form']['meta']['show_price_per_single_passenger']===1)
			{
				if(CHBSBookingHelper::isPassengerEnable($data['booking_form']['meta'],$data['service_type_id'],-1))
				{
					if($price['price']['other'][$priceType]['price_passenger_children']['value']!=$price['price']['other'][$priceType]['price_passenger_adult']['value'])
					{
						$priceToDisplay=$price['price']['other'][$priceType]['price_passenger_children']['formatHtml'].' - '.$price['price']['other'][$priceType]['price_passenger_adult']['formatHtml'];
					}
					else $priceToDisplay=$price['price']['other'][$priceType]['price_passenger_adult']['formatHtml'];
				}
				else if(CHBSBookingHelper::isPassengerEnable($data['booking_form']['meta'],$data['service_type_id'],'adult'))
				{
					$priceToDisplay=$price['price']['other'][$priceType]['price_passenger_adult']['formatHtml'];
				}
				else if(CHBSBookingHelper::isPassengerEnable($data['booking_form']['meta'],$data['service_type_id'],'children'))
				{
					$priceToDisplay=$price['price']['other'][$priceType]['price_passenger_children']['formatHtml'];
				}
			}
			
			$htmlPrice=
			'
				<div class="chbs-vehicle-content-price">
					<span>
						<span>'.$priceToDisplay.'</span>
						'.$priceBeforeDiscount.'	
					</span>
				</div>  
			';
			
			if(CHBSBookingHelper::isVehicleBidPriceEnable($data['booking_form']))
			{
				$option=CHBSHelper::getPostOption();
				
				$class=array(array(),array());
				
				$class[0]=array('chbs-vehicle-content-price-bid');
				$class[1]=array('chbs-hidden');
				$class[2]=array('chbs-hidden');
				
				$value=null;
				
				if(is_array($option['vehicle_bid_price']))
				{
					if(array_key_exists((int)$data['vehicle_id'],$option['vehicle_bid_price']))
						$value=$option['vehicle_bid_price'][(int)$data['vehicle_id']];
				}
					
				if($data['vehicle_selected_id']==$data['vehicle_id'])
				{
					if(CHBSPrice::isPrice($value)) unset($class[2][0]);
					else unset($class[1][0]);
				}
						
				$htmlPrice.=
				'
					<div'.CHBSHelper::createCSSClassAttribute($class[0]).'>
						<div'.CHBSHelper::createCSSClassAttribute($class[1]).'>
							<a href="#" class="chbs-button chbs-button-style-3">'.esc_html('Bid price','chauffeur-booking-system').'</a>
						</div>
						<div'.CHBSHelper::createCSSClassAttribute($class[2]).'>
							<input type="text" placeholder="'.esc_attr('Enter a price','chauffeur-booking-system').'" name="'.CHBSHelper::getFormName('vehicle_bid_price['.(int)$data['vehicle_id'].']',false).'" value="'.esc_attr($value).'">
							<a href="#" class="chbs-button chbs-button-style-3">'.esc_html('Bid','chauffeur-booking-system').'</a>
							<a href="#" class="chbs-button chbs-button-style-3">'.esc_html('Cancel','chauffeur-booking-system').'</a>
						</div>
					</div>
				';
			}
		}		
				
		if((int)$price['price']['base']['custom_vehicle_selection_enable']===1)
		{
			if((int)$price['price']['base']['custom_vehicle_selection_first_step_redirect']===1)
				$urlAddress=$price['price']['base']['custom_vehicle_selection_button_url_address'];
			
			$htmlSelect=
			'
				<a href="'.esc_attr($price['price']['base']['custom_vehicle_selection_button_url_address']).'" target="'.((int)$price['price']['base']['custom_vehicle_selection_button_url_target']===1 ? '_self' : '_blank').'" class="chbs-button chbs-button-style-2 chbs-button-on-request">
					'.esc_html($price['price']['base']['custom_vehicle_selection_button_label']).'
				</a>			   
			';
		}
		else 
		{
			$htmlSelect=
			'
				<a href="#" class="chbs-button chbs-button-style-2 '.($data['vehicle_selected_id']==$data['vehicle_id'] ? 'chbs-state-selected' : null).'">
					'.esc_html__('Select','chauffeur-booking-system').'
					<span class="chbs-meta-icon-tick"></span>
				</a>			
			';
		}
		
		$distance=CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id'],false,false);
		$returnDistance=CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id'],true,false);
		
		/***/
		
		$htmlVehicleInfo=null;
		if((int)$data['booking_form']['meta']['passenger_number_vehicle_list_enable']===1)
		{
			$htmlVehicleInfo.=
			'
				<span class="chbs-meta-icon-people"></span>
				<span class="chbs-circle">'.$data['vehicle']['meta']['passenger_count'].'</span>				
			';
		}
		if((int)$data['booking_form']['meta']['suitcase_number_vehicle_list_enable']===1)
		{
			$htmlVehicleInfo.=
			'
				<span class="chbs-meta-icon-bag"></span>
				<span class="chbs-circle">'.$data['vehicle']['meta']['bag_count'].'</span>			
			';
		}
		if($Validation->isNotEmpty($htmlVehicleInfo))
		{
			$htmlVehicleInfo=
			'
				<div class="chbs-vehicle-content-meta-info">
					<div>
						'.$htmlVehicleInfo.'
					</div>
				</div>				
			';
		}
		
		/***/
		
		$vehicleName=get_the_title($data['vehicle_id']);
		
		$html=
		'
			<div class="chbs-vehicle chbs-clear-fix" data-id="'.esc_attr($data['vehicle_id']).'" data-name="'.esc_attr($vehicleName).'" data-base_location_cooridnate_lat="'.esc_attr($data['vehicle']['meta']['base_location_coordinate_lat']).'"  data-base_location_cooridnate_lng="'.esc_attr($data['vehicle']['meta']['base_location_coordinate_lng']).'">

				'.$html[0].'

				<div class="chbs-vehicle-content">
				
					<div class="chbs-vehicle-content-header"> 
						<span>'.$vehicleName.'</span>
						'.$htmlSelect.'
					</div>
					
					'.$htmlPrice.'
					
					'.$htmlDescription.'
								 
					<div class="chbs-vehicle-content-meta">
					
						<div>
					
							'.$htmlMoreInfo.'
							'.$htmlVehicleInfo.'

						</div>

					</div>

				</div>
				
				<input type="hidden" name="'.CHBSHelper::getFormName('base_location_vehicle_distance['.(int)$data['vehicle_id'].']',false).'" value="'.$distance.'"/>
				<input type="hidden" name="'.CHBSHelper::getFormName('base_location_vehicle_return_distance['.(int)$data['vehicle_id'].']',false).'" value="'.$returnDistance.'"/>

			</div>
		';
		
		$priceToSort=$price['price']['sum']['gross']['value'];
		
		return($html);
	}

	/**************************************************************************/ 
	
	function getVehiclePassengerCountRange($bookingForm)
	{
		$count=array();
		foreach($bookingForm['dictionary']['vehicle'] as $value)
			array_push($count,$value['meta']['passenger_count']);
			
		$count=array('min'=>1,'max'=>max($count));
		
		$data=CHBSHelper::getPostOption();
		
		if(array_key_exists('service_type_id',$data))
		{
			if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id']))
			{
				$sum=CHBSBookingHelper::getPassenegerSum($bookingForm['meta'],$data);

				if($sum>1) $count['min']=$sum;
				if($count['min']>$count['max']) $count['max']=$count['min'];
			}
		}
		
		return($count);
	}
	
	 /**************************************************************************/ 
	
	function getVehicleBagCountRange($vehicle)
	{
		$count=array();
		foreach($vehicle as $value)
			array_push($count,$value['meta']['bag_count']);
			
		$count=array('min'=>1,'max'=>max($count));
		
		return($count);	  
	}
	
	/**************************************************************************/
	
	function vehicleFilter($ajax=true,&$redirectToUrlAddress=null,&$vehiclePriceCalculationStepFirst=array())
	{		   
		if(!is_bool($ajax)) $ajax=true;
			
		$redirectToUrlAddress=null;
		$vehiclePriceCalculationStepFirst=array();
		
		$html=null;
		$response=array();
		
		$Date=new CHBSDate();
		$Validation=new CHBSValidation();
		
		$data=CHBSHelper::getPostOption();
		
		$serviceTypeId=$data['service_type_id'];
		
		$data['pickup_date_service_type_'.$serviceTypeId]=$Date->formatDateToStandard($data['pickup_date_service_type_'.$serviceTypeId]);
		$data['pickup_time_service_type_'.$serviceTypeId]=$Date->formatTimeToStandard($data['pickup_time_service_type_'.$serviceTypeId]);  
		
		if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
		{
			if(!$ajax) return($html);
			
			$this->setErrorGlobal($response,__('There are no vehicles that match your filter criteria.','chauffeur-booking-system'));
			$this->createFormResponse($response);
		}
		
		$response['booking_summary_hide_fee']=$bookingForm['meta']['booking_summary_hide_fee'];
		
		if(!$Validation->isNumber($data['vehicle_standard'],1,4)) $data['vehicle_standard']=1;
		if(!$Validation->isNumber($data['vehicle_bag_count'],1,99)) $data['vehicle_bag_count']=1;
		if(!$Validation->isNumber($data['vehicle_passenger_count'],1,99)) $data['vehicle_passenger_count']=1;		
		
		$sum=CHBSBookingHelper::getPassenegerSum($bookingForm['meta'],$data);
		
		if($sum>0) 
		{
			if($data['vehicle_passenger_count']<$sum)
				$data['vehicle_passenger_count']=$sum;
		}
		
		$attribute=array();
		
		/***/
		
		$meta=$bookingForm['meta'];
		
		$vehicleCategory=$this->getBookingFormVehicleCategory($bookingForm['meta']);
		
		if($data['vehicle_category']!=0)
			$attribute=array('category_id'=>$data['vehicle_category']);
		
		if(isset($attribute['category_id']))
		{
			if(!array_key_exists($attribute['category_id'],$vehicleCategory))
				$attribute['category_id']=array_keys($vehicleCategory);
			
			$bookingForm['meta']['vehicle_category_id']=(array)$attribute['category_id'];
		}
		else
		{
			if(!in_array(-1,$bookingForm['meta']['vehicle_category_id']))
			{
				$attribute['category_id']=array_keys($vehicleCategory);
				$bookingForm['meta']['vehicle_category_id']=(array)$attribute['category_id'];
			}
		}
		
		/***/
		
		$vehicleIdDefault=0;
		
		$dictionary=$this->getBookingFormVehicle($bookingForm,$vehicleIdDefault);
		
		$vehicleHtml=array();
		$vehiclePrice=array();
		$vehicleURLAddress=array();
		
		foreach($dictionary as $index=>$value)
		{
			if(!(($value['meta']['passenger_count']>=$data['vehicle_passenger_count']) && ($value['meta']['bag_count']>=$data['vehicle_bag_count']) && ($value['meta']['standard']>=$data['vehicle_standard']))) continue;
			
			// *rule
			$argument=array
			(
				'booking_form_id'=>$bookingForm['post']->ID,
				'service_type_id'=>$data['service_type_id'],
				'transfer_type_id'=>$data['transfer_type_service_type_'.$data['service_type_id']],
				'pickup_location_coordinate'=>$data['pickup_location_coordinate_service_type_'.$data['service_type_id']],
				'dropoff_location_coordinate'=>$data['dropoff_location_coordinate_service_type_'.$data['service_type_id']],				
				'fixed_location_pickup'=>$data['fixed_location_pickup_service_type_'.$data['service_type_id']],
				'fixed_location_dropoff'=>$data['fixed_location_dropoff_service_type_'.$data['service_type_id']],				
				'transfer_type_id'=>$data['transfer_type_service_type_'.$data['service_type_id']],
				'route_id'=>$data['route_service_type_3'],
				'vehicle'=>$value,
				'vehicle_id'=>$value['post']->ID,
				'vehicle_selected_id'=>$data['vehicle_id'],
				'pickup_date'=>$data['pickup_date_service_type_'.$data['service_type_id']],
				'pickup_time'=>$data['pickup_time_service_type_'.$data['service_type_id']],
				'return_date'=>$data['return_date_service_type_'.$data['service_type_id']],
				'return_time'=>$data['return_time_service_type_'.$data['service_type_id']],
				'passenger_adult'=>$data['passenger_adult_service_type_'.$data['service_type_id']],
				'passenger_children'=>$data['passenger_children_service_type_'.$data['service_type_id']],
				'base_location_distance'=>CHBSBookingHelper::getBaseLocationDistance($value['post']->ID),
				'base_location_return_distance'=>CHBSBookingHelper::getBaseLocationDistance($value['post']->ID,true),
				'distance'=>$data['distance_map'],
				'distance_sum'=>$data['distance_sum'],
				'duration'=>in_array($data['service_type_id'],array(1,3)) ? 0 : $data['duration_service_type_2']*60,
				'duration_map'=>$data['duration_map'],
				'duration_sum'=>in_array($data['service_type_id'],array(1,3)) ? $data['duration_sum'] : $data['duration_service_type_2']*60,
				'booking_form'=>$bookingForm,
                'waypoint_count'=>CHBSBookingHelper::getWaypointCount($data,$bookingForm,$data['service_type_id'],$data['transfer_type_service_type_'.$data['service_type_id']])
			);
			
			$price=0;
			$urlAddress=null;
			
			$vehicleHtml[$index]=$this->createVehicle($argument,$price,$urlAddress);
			
			$vehiclePrice[$index]=$price;
			$vehicleUrlAddress[$index]=$urlAddress;
		}

		/***/
		
		if(!is_array($vehicleUrlAddress)) $vehicleUrlAddress=array();
		
		$vehicleUrlAddress=array_unique(array_values($vehicleUrlAddress));
		
		if((count($vehicleUrlAddress)===1) && (!is_null($vehicleUrlAddress[0])))
			$redirectToUrlAddress=$vehicleUrlAddress[0];

		/***/

		if(in_array((int)$bookingForm['meta']['vehicle_sorting_type'],array(1,2)))
		{
			asort($vehiclePrice);		 
			if((int)$bookingForm['meta']['vehicle_sorting_type']===2)
				$vehiclePrice=array_reverse($vehiclePrice,true);
		}
		
		/***/
		
		$i=0;
		if($bookingForm['meta']['vehicle_limit']>0)
		{
			foreach($vehiclePrice as $index=>$value)
			{
				if((++$i)>$bookingForm['meta']['vehicle_limit'])
					unset($vehicleHtml[$index],$vehiclePrice[$index]);
			}
		}
		
		/**/
		
		$i=0;
		$vehiclePerPage=(int)$bookingForm['meta']['vehicle_pagination_vehicle_per_page'];
		
		foreach($vehiclePrice as $index=>$value)
		{
			$class=array();
			
			if($vehiclePerPage>0)
			{
				if(($i++)>=$vehiclePerPage)
					array_push($class,'chbs-hidden');
			}
			
			$html.='<li'.CHBSHelper::createCSSClassAttribute($class).'>'.$vehicleHtml[$index].'</li>';
		}
		
		$html='<ul class="chbs-list-reset">'.$html.'</ul>';
		
		$html.=$this->createPagination($dictionary,$bookingForm['meta']['vehicle_pagination_vehicle_per_page']);
		
		$response['html']=$html;
		
		/***/
		
		$vehiclePriceCalculationStepFirst=array('price_min'=>0.00,'price_max'=>0.00,'show'=>1);
		
		if(count($vehiclePrice))
		{
			$vehiclePriceCalculationStepFirst['price_min']=min($vehiclePrice);
			$vehiclePriceCalculationStepFirst['price_max']=max($vehiclePrice);			
		}
		
		$vehiclePriceCalculationStepFirst['price_min']=CHBSPrice::format($vehiclePriceCalculationStepFirst['price_min'],CHBSCurrency::getFormCurrency());
		$vehiclePriceCalculationStepFirst['price_max']=CHBSPrice::format($vehiclePriceCalculationStepFirst['price_max'],CHBSCurrency::getFormCurrency());
		
		if($vehiclePriceCalculationStepFirst['price_min']!=$vehiclePriceCalculationStepFirst['price_max'])
		{
			$vehiclePriceCalculationStepFirst['message']=sprintf(__('Price for a vehicle is from <span>%s</span> to <span>%s</span>.','chauffeur-booking-system'),$vehiclePriceCalculationStepFirst['price_min'],$vehiclePriceCalculationStepFirst['price_max']);
		}
		else
		{
			if($vehiclePriceCalculationStepFirst['price_min']==0.00)
			{
				$vehiclePriceCalculationStepFirst['show']=0;
			}
			
			$vehiclePriceCalculationStepFirst['message']=sprintf(__('Price for a vehicle is <span>%s</span>.','chauffeur-booking-system'),$vehiclePriceCalculationStepFirst['price_min']);
		}
		
		$response['vehicle_price_calculation_step_first']=$vehiclePriceCalculationStepFirst;
		
		/***/

		if($Validation->isEmpty($html))
		{
			if($ajax)
			{
				$this->setErrorGlobal($response,__('There are no vehicles that match your filter criteria.','chauffeur-booking-system'));
				$this->createFormResponse($response);
			}
		}
		
		if(!$ajax) return($html);
		
		$this->createFormResponse($response);
	}
	
	/**************************************************************************/
	
	function createClientFormSignUp($bookingForm,$userData=array())
	{
		$User=new CHBSUser();
		$WooCommerce=new CHBSWooCommerce();
		$BookingFormElement=new CHBSBookingFormElement();
		
		/***/
		
		$data=CHBSHelper::getPostOption();
		if(count($userData)) $data=array_merge($data,$userData);

		/***/
		
		$html=null;
		$htmlElement=array(null,null,null,null,null,null);
		
		foreach($bookingForm['dictionary']['country'] as $index=>$value)
			$htmlElement[0].='<option value="'.esc_attr($index).'" '.($data['client_billing_detail_country_code']==$index ? 'selected' : null).'>'.esc_html($value[0]).'</option>';
		
		$htmlElement[1]=$BookingFormElement->createField(1,$data['service_type_id'],$data['transfer_type_service_type_'.$data['service_type_id']],$bookingForm);
		
		$htmlElement[2]=$BookingFormElement->createField(2,$data['service_type_id'],$data['transfer_type_service_type_'.$data['service_type_id']],$bookingForm);
		
		$panel=$BookingFormElement->getPanel($bookingForm['meta']);
		foreach($panel as $index=>$value)
		{
			if(in_array($value['id'],array(1,2))) continue;
			$htmlElement[3].=$BookingFormElement->createField($value['id'],$data['service_type_id'],$data['transfer_type_service_type_'.$data['service_type_id']],$bookingForm);
		}
		
		if($WooCommerce->isEnable($bookingForm['meta']))
		{
			if(!$User->isSignIn())
			{
				if(in_array((int)$bookingForm['meta']['woocommerce_account_enable_type'],array(1,2)))
				{
					$class=array(array('chbs-form-checkbox'),array('chbs-panel'));
					
					if(in_array((int)$bookingForm['meta']['woocommerce_account_enable_type'],array(2)))
					{
						
					}
					else
					{
						if((int)$data['client_sign_up_enable']===0)
						{
							array_push($class[1],'chbs-hidden');
						}
						else
						{
							array_push($class[0],'chbs-state-selected');
						}
					}
					
					$htmlElement[4].=
					'
						<div class="chbs-clear-fix">
							<label class="chbs-form-label-group">
					';
					
					if(in_array((int)$bookingForm['meta']['woocommerce_account_enable_type'],array(2)))
					{
						$htmlElement[4].=esc_html__('New account','chauffeur-booking-system');
					}
					else
					{
						$htmlElement[4].=
						'
								<span'.CHBSHelper::createCSSClassAttribute($class[0]).'>
									<span class="chbs-meta-icon-tick"></span>
								</span>
								<input type="hidden" name="'.CHBSHelper::getFormName('client_sign_up_enable',false).'" value="'.esc_attr($data['client_sign_up_enable']).'"/> 
								'.esc_html__('Create an account?','chauffeur-booking-system').'
						';						
					}
					
					$htmlElement[4].=
					'					
							</label>					
						</div>

						<div'.CHBSHelper::createCSSClassAttribute($class[1]).'>

							<div class="chbs-clear-fix">
								<div class="chbs-form-field chbs-form-field-width-33">
									<label>'.esc_html__('Login *','chauffeur-booking-system').'</label>
									<input type="text" name="'.CHBSHelper::getFormName('client_sign_up_login',false).'"/>
								</div>
								<div class="chbs-form-field chbs-form-field-width-33">
									<label>
										'.esc_html__('Password *','chauffeur-booking-system').'
										&nbsp;
										<a href="#" class="chbs-sign-up-password-generate">'.esc_html__('Generate','chauffeur-booking-system').'</a>
										<a href="#" class="chbs-sign-up-password-show">'.esc_html__('Show','chauffeur-booking-system').'</a>
									</label>
									<input type="password" name="'.CHBSHelper::getFormName('client_sign_up_password',false).'"/>
								</div>
								<div class="chbs-form-field chbs-form-field-width-33">
									<label>'.esc_html__('Re-type password *','chauffeur-booking-system').'</label>
									<input type="password" name="'.CHBSHelper::getFormName('client_sign_up_password_retype',false).'"/>
								</div>
							</div>

						</div>
					';
				}
			}
		}
		
		/***/
		
		$class=array('chbs-client-form-sign-up','chbs-hidden');
		
		if($WooCommerce->isEnable($bookingForm['meta']))
		{
			if(($User->isSignIn()) || ((int)$data['client_account']===1) || ((int)$bookingForm['meta']['woocommerce_account_enable_type']===0)) unset($class[1]);
		}  
		else unset($class[1]);
 
		$html=
		'
			<div'.CHBSHelper::createCSSClassAttribute($class).'>

				<div class="chbs-box-shadow">
				
					<div class="chbs-clear-fix">
						<label class="chbs-form-label-group">'.esc_html__('Contact details','chauffeur-booking-system').'</label>
						<div class="chbs-form-field chbs-form-field-width-50">
							<label>'.esc_html__('First name *','chauffeur-booking-system').'</label>
							<input type="text" name="'.CHBSHelper::getFormName('client_contact_detail_first_name',false).'" value="'.esc_attr($data['client_contact_detail_first_name']).'"/>
						</div>
						<div class="chbs-form-field chbs-form-field-width-50">
							<label>'.esc_html__('Last name *','chauffeur-booking-system').'</label>
							<input type="text" name="'.CHBSHelper::getFormName('client_contact_detail_last_name',false).'" value="'.esc_attr($data['client_contact_detail_last_name']).'"/>
						</div>
					</div>

					<div class="chbs-clear-fix">
						<div class="chbs-form-field chbs-form-field-width-50">
							<label>'.esc_html__('E-mail address *','chauffeur-booking-system').'</label>
							<input type="text" name="'.CHBSHelper::getFormName('client_contact_detail_email_address',false).'"  value="'.esc_attr($data['client_contact_detail_email_address']).'"/>
						</div>
						<div class="chbs-form-field chbs-form-field-width-50">
							<label>'.esc_html__('Phone number','chauffeur-booking-system').(in_array('client_contact_detail_phone_number',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
							<input type="text" name="'.CHBSHelper::getFormName('client_contact_detail_phone_number_placeholder',false).'"  value="'.esc_attr($data['client_contact_detail_phone_number_placeholder']).'"/>
							<input type="hidden" name="'.CHBSHelper::getFormName('client_contact_detail_phone_number',false).'"  value="'.esc_attr($data['client_contact_detail_phone_number']).'"/>
						</div>
					</div>

					<div class="chbs-clear-fix">
						<div class="chbs-form-field">
							<label>'.esc_html__('Comments','chauffeur-booking-system').'</label>
							<textarea name="'.CHBSHelper::getFormName('comment',false).'">'.esc_html(array_key_exists('comment',$data) ? $data['comment'] : CHBSRequestData::post('chbs_comment_hidden')).'</textarea>
						</div>
					</div>
					
					'.$htmlElement[1].'

					'.$htmlElement[4].'
		';
		
		/***/
		
		if((int)$bookingForm['meta']['billing_detail_state']===4) return($html.$htmlElement[3].'</div></div>');
		
		/***/
		
		$checkboxHtml=null;
		$class=array(array('chbs-form-checkbox'),array('chbs-client-form-billing-address','chbs-panel'));

		if((int)$bookingForm['meta']['billing_detail_state']===3)
		{
			$class[0]=$class[1]=array();
			$checkboxHtml='<input type="hidden" name="'.CHBSHelper::getFormName('client_billing_detail_enable',false).'" value="1"/> ';
		}
		else
		{
			if(!array_key_exists('client_billing_detail_enable',$data))
				$data['client_billing_detail_enable']=(int)$bookingForm['meta']['billing_detail_state']-1;
			
			if((int)$data['client_billing_detail_enable']===0)
			{
				array_push($class[1],'chbs-hidden');
			}
			else
			{
				array_push($class[0],'chbs-state-selected');
			}
			
			$checkboxHtml=
			'
				<span'.CHBSHelper::createCSSClassAttribute($class[0]).'>
					<span class="chbs-meta-icon-tick"></span>
				</span>
				<input type="hidden" name="'.CHBSHelper::getFormName('client_billing_detail_enable',false).'" value="'.esc_attr($data['client_billing_detail_enable']).'"/> 
			';
		}
		
		/***/
		
		$state=CHBSHelper::splitBy($bookingForm['meta']['billing_detail_list_state']);
		if(count($state))
		{
			foreach($state as $value)
				$htmlElement[5].='<option value="'.esc_attr($value).'" '.($data['client_billing_detail_state']==$value ? 'selected' : null).'>'.esc_html($value).'</option>';
			
			$htmlElement[5]=
			'
				<select name="'.CHBSHelper::getFormName('client_billing_detail_state',false).'">
					'.$htmlElement[5].'
				</select>  
			';
		}
		else
		{
			$htmlElement[5]=
			'
				<input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_state',false).'" value="'.esc_attr($data['client_billing_detail_state']).'"/>
			';
		}
		
		/***/
		
		$html.=
		'
					<div class="chbs-clear-fix">
						<label class="chbs-form-label-group">
							'.$checkboxHtml.'
							'.esc_html__('Billing address','chauffeur-booking-system').'
						</label>					
					</div>

					<div'.CHBSHelper::createCSSClassAttribute($class[1]).'>

						<div class="chbs-clear-fix">
							<div class="chbs-form-field chbs-form-field-width-50">
								<label>'.esc_html__('Company registered name','chauffeur-booking-system').(in_array('client_billing_detail_company_name',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
								<input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_company_name',false).'" value="'.esc_attr($data['client_billing_detail_company_name']).'"/>
							</div>
							<div class="chbs-form-field chbs-form-field-width-50">
								<label>'.esc_html__('Tax number','chauffeur-booking-system').(in_array('client_billing_detail_tax_number',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
								<input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_tax_number',false).'" value="'.esc_attr($data['client_billing_detail_tax_number']).'"/>
							</div>
						</div>

						<div class="chbs-clear-fix">
							<div class="chbs-form-field chbs-form-field-width-33">
								<label>'.esc_html__('Street','chauffeur-booking-system').(in_array('client_billing_detail_street_name',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
								<input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_street_name',false).'" value="'.esc_attr($data['client_billing_detail_street_name']).'"/>
							</div>
							<div class="chbs-form-field chbs-form-field-width-33">
								<label>'.esc_html__('Street number','chauffeur-booking-system').(in_array('client_billing_detail_street_number',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
								<input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_street_number',false).'" value="'.esc_attr($data['client_billing_detail_street_number']).'"/>
							</div>
							<div class="chbs-form-field chbs-form-field-width-33">
								<label>'.esc_html__('City','chauffeur-booking-system').(in_array('client_billing_detail_city',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
								<input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_city',false).'" value="'.esc_attr($data['client_billing_detail_city']).'"/>
							</div>					
						</div>

						<div class="chbs-clear-fix">
							<div class="chbs-form-field chbs-form-field-width-33">
								<label>'.esc_html__('State','chauffeur-booking-system').(in_array('client_billing_detail_state',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
								'.$htmlElement[5].'
							</div>
							<div class="chbs-form-field chbs-form-field-width-33">
								<label>'.esc_html__('Postal code','chauffeur-booking-system').(in_array('client_billing_detail_postal_code',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
								<input type="text" name="'.CHBSHelper::getFormName('client_billing_detail_postal_code',false).'" value="'.esc_attr($data['client_billing_detail_postal_code'] ?? '').'"/>
							</div>
							<div class="chbs-form-field chbs-form-field-width-33">
								<label>'.esc_html__('Country','chauffeur-booking-system').(in_array('client_billing_detail_country_code',$bookingForm['meta']['field_mandatory']) ? ' *' : '').'</label>
								<select name="'.CHBSHelper::getFormName('client_billing_detail_country_code',false).'">
									'.$htmlElement[0].'
								</select>
							</div>					
						</div> 
						
						'.$htmlElement[2].'
							
					</div>
					
					'.$htmlElement[3].'
						
				</div>
				
			</div>
		';
		
		return($html);
	}
	
	/**************************************************************************/
   
	function createClientFormSignIn($bookingForm)
	{
		$User=new CHBSUser();
		$WooCommerce=new CHBSWooCommerce();
		
		if(!$WooCommerce->isEnable($bookingForm['meta'])) return;
		if($User->isSignIn()) return;
		
		if((int)$bookingForm['meta']['woocommerce_account_enable_type']===0) return;
		
		$data=CHBSHelper::getPostOption();
		
		$html=
		'
			<div class="chbs-client-form-sign-in">

				<div class="chbs-box-shadow">
				
					<div class="chbs-clear-fix">
						<label class="chbs-form-label-group">'.esc_html__('Sign In','chauffeur-booking-system').'</label>
						<div class="chbs-form-field chbs-form-field-width-50">
							<label>'.esc_html__('Login *','chauffeur-booking-system').'</label>
							<input type="text" name="'.CHBSHelper::getFormName('client_sign_in_login',false).'" value=""/>
						</div>
						<div class="chbs-form-field chbs-form-field-width-50">
							<label>'.esc_html__('Password *','chauffeur-booking-system').'</label>
							<input type="password" name="'.CHBSHelper::getFormName('client_sign_in_password',false).'" value=""/>
						</div>
					</div>
				 
				</div>
				
				<div class="chbs-clear-fix">
				
				   <a href="#" class="chbs-button chbs-button-style-2 chbs-button-sign-up">
						'.esc_html__('Don\'t Have an Account?','chauffeur-booking-system').'
				   </a> 
				   
				   <a href="#" class="chbs-button chbs-button-style-1 chbs-button-sign-in">
					   '.esc_html__('Sign In','chauffeur-booking-system').'
				   </a> 
				   
				   <input type="hidden" name="'.CHBSHelper::getFormName('client_account',false).'" value="'.(int)$data['client_account'].'"/> 
					
				</div>

			</div>
		';
		
		return($html);
	}
	
	/**************************************************************************/
	
	function manageEditColumns($column)
	{
		$column=array
		(
			'cb'=>$column['cb'],
			'title'=>__('Title','chauffeur-booking-system'),
			'service_type'=>__('Service type','chauffeur-booking-system')
		);
   
		return($column);		  
	}
	
	/**************************************************************************/
	
	function managePostsCustomColumn($column)
	{
		global $post;
		
		$meta=CHBSPostMeta::getPostMeta($post);
		
		$Validation=new CHBSValidation();
		$ServiceType=new CHBSServiceType();
		
		switch($column) 
		{
			case 'service_type':
				
				$html=null;
				
				foreach($meta['service_type_id'] as $value)
				{
					$serviceType=$ServiceType->getServiceType($value);
					
					if($Validation->isNotEmpty($html)) $html.=', ';
					$html.=$serviceType[0];
				}
				
				echo esc_html($html);
				
			break;
		}
	}
	
	/**************************************************************************/
	
	function manageEditSortableColumns($column)
	{
		return($column);	   
	}
	
	/**************************************************************************/   
	
	function userSignIn()
	{
		$data=CHBSHelper::getPostOption();
		
		$response=array('user_sign_in'=>0);
		
		if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
		{
			$this->setErrorGlobal($response,__('Login error.','chauffeur-booking-system'));
			$this->createFormResponse($response);
		}
		
		$User=new CHBSUser();
		$WooCommerce=new CHBSWooCommerce();
		
		if(!$User->signIn($data['client_sign_in_login'],$data['client_sign_in_password']))
			$this->setErrorGlobal($response,__('Login error.','chauffeur-booking-system'));
		else 
		{
			$userData=$WooCommerce->getUserData();
			
			$response['user_sign_in']=1;  
			
			$response['summary']=$this->createSummary($data,$bookingForm);
			$response['client_form_sign_up']=$this->createClientFormSignUp($bookingForm,$userData);
		}
		
		$this->createFormResponse($response);
	}
	
	/**************************************************************************/
	
	function createVehiclePassengerFilterField($min,$max,$passengerSum=1)
	{
		$html=null;
		
		for($i=$min;$i<=$max;$i++)
			$html.='<option value="'.esc_attr($i).'"'.($i==$passengerSum ? ' selected="selected"' : '').'>'.esc_html($i).'</option>';
			
		$html='<select name="'.CHBSHelper::getFormName('vehicle_passenger_count',false).'">'.$html.'</select>';

		return($html);
	}
	
	/**************************************************************************/
	
	function checkCouponCode()
	{		
		$response=array();
		
		$Date=new CHBSDate();
		
		$data=CHBSHelper::getPostOption();
		
		$serviceTypeId=(int)$data['service_type_id'];
		
		$data['pickup_date_service_type_'.$serviceTypeId]=$Date->formatDateToStandard($data['pickup_date_service_type_'.$serviceTypeId]);
		$data['pickup_time_service_type_'.$serviceTypeId]=$Date->formatTimeToStandard($data['pickup_time_service_type_'.$serviceTypeId]);		  
		
		if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
		{
			$response['html']=null;
			CHBSHelper::createJSONResponse($response);
		}
		
		$price=array();
		$response['html']=$this->createSummaryPriceElement($data,$bookingForm,$price);
		
		$Coupon=new CHBSCoupon();
		$coupon=$Coupon->checkCode();
		
		$response['error']=$coupon===false ? 1 : 0;
		
		if($response['error']===1)
		   $response['message']=__('Provided coupon is invalid.','chauffeur-booking-system'); 
		else 
			$response['message']=__('Provided coupon is valid. Discount has been granted.','chauffeur-booking-system');
		
		CHBSHelper::createJSONResponse($response);
	}
	
	/**************************************************************************/
	
	function setGratuityCustomer()
	{
		$response=array();
		
		$Date=new CHBSDate();
		
		$data=CHBSHelper::getPostOption();
		
		$serviceTypeId=$data['service_type_id'];
		
		$data['pickup_date_service_type_'.$serviceTypeId]=$Date->formatDateToStandard($data['pickup_date_service_type_'.$serviceTypeId]);
		$data['pickup_time_service_type_'.$serviceTypeId]=$Date->formatTimeToStandard($data['pickup_time_service_type_'.$serviceTypeId]);  
		
		if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
		{
			$response['html']=null;
			CHBSHelper::createJSONResponse($response);
		}
		
		$price=array();
		$response['html']=$this->createSummaryPriceElement($data,$bookingForm,$price);
		
		if($price['gratuity']['value']>0.00)
		   $response['message']=__('Gratuity has been added to the sum of the booking.','chauffeur-booking-system'); 
		else 
			$response['message']=__('Gratuity has been set to 0.00.','chauffeur-booking-system');
		
		$response['gratuity']=$price['gratuity']['value'];
		
		CHBSHelper::createJSONResponse($response);		
	}
	
	/**************************************************************************/
	
	function createBookingFormExtra($bookingForm,$data)
	{
		$html=null;
				   
		if(count($bookingForm['dictionary']['booking_extra']))
		{
			$Validation=new CHBSValidation();
			$BookingExtra=new CHBSBookingExtra();

			$html.=
			'
				<h4 class="chbs-booking-extra-header">
					<span class="chbs-circle chbs-meta-icon-cart"></span>
					<span>'.esc_html__('Extra options','chauffeur-booking-system').'</span>
			';
			
			if((int)$bookingForm['meta']['booking_extra_button_toggle_visibility_enable']===1)
			{
				$html.=$this->createShowMoreButton($bookingForm['meta']['booking_extra_visibility_status'],array('more'=>__('Show all','chauffeur-booking-system'),'less'=>__('Hide','chauffeur-booking-system')));
			}
			
			$html.=
			'
				</h4>
			';
			
			$class=array();
			if((int)$bookingForm['meta']['booking_extra_button_toggle_visibility_enable']===1)
			{
				if((int)$bookingForm['meta']['booking_extra_visibility_status']===1) array_push($class,'chbs-state-open');
			}
			else array_push($class,'chbs-state-open');
			
			$html.=
			'
				<div'.CHBSHelper::createCSSClassAttribute($class).'>
			';
			
			if((int)$bookingForm['meta']['booking_extra_category_display_enable']===1)
			{
				if((is_array($bookingForm['dictionary']['booking_extra_category'])) && (count($bookingForm['dictionary']['booking_extra_category'])))
				{
					$htmlCategory=null;
					
					foreach($bookingForm['dictionary']['booking_extra_category'] as $index=>$value)
						$htmlCategory.='<div><a href="#" data-category_id="'.(int)$value['term_id'].'">'.esc_html($value['name']).'</a></div>';
					
					$html.=
					'
						<div class="chbs-booking-extra-category-list">
							'.$htmlCategory.'
						</div>
					';
				}
			}
						
			$html.=
			'
				<div class="chbs-booking-extra-list">
					<ul class="chbs-list-reset">
			';
			
			foreach($bookingForm['dictionary']['booking_extra'] as $index=>$value)
			{
				$price=$BookingExtra->calculatePrice($value,$bookingForm,$data);
								
				$htmlColumn=array(null,null,null);
				
				/***/
				
				$class=array();
				if($value['meta']['quantity_enable']==1)
					array_push($class,'chbs-booking-extra-list-item-quantity-enable');
			
				$category=null;
				if(count($value['category'][0]))
					$category=join(',',array_keys($value['category'][0]));
				
				$html.=
				'
						<li'.CHBSHelper::createCSSClassAttribute($class).' data-category_id="'.esc_attr($category).'" data-vehicle_id="'.esc_attr(join(',',$value['meta']['vehicle_id'])).'" data-booking_extra_id="'.esc_attr($index).'" data-booking_extra_name="'.esc_attr($value['post']->post_title).'">
				';
				
				/***/
								
				if((int)$value['meta']['quantity_enable']===1)
				{
					$fieldName='booking_extra_'.$index.'_quantity';
					
					$htmlColumn[1].=
					'
							<div class="chbs-column-2">
								<div class="chbs-form-field">
									<label>'.esc_html__('Number','chauffeur-booking-system').'</label>
									<div class="chbs-quantity-section">
										<span class="chbs-quantity-section-button chbs-meta-icon-minus" data-step="-1"></span>
										<input type="text" name="'.CHBSHelper::getFormName($fieldName,false).'" value="'.(array_key_exists($fieldName,$data) ? $data[$fieldName] : 1).'" data-quantity-max="'.(int)$value['meta']['quantity_max'].'"/>
										<span class="chbs-quantity-section-button chbs-meta-icon-plus" data-step="1"></span>
									</div>
								</div>  
							</div>
					';
				}
				else
				{
					$htmlColumn[1].=
					'
							<div class="chbs-column-2"></div>
					';
				}
				
				/***/
				
				$buttonSelected=false;
			
				if((int)$value['meta']['mandatory']===1)
				{
					$fieldName='booking_extra_value';
					$fieldValue=-1;
					
					if((array_key_exists($fieldName,$data)) && (array_key_exists($index,$data[$fieldName])))
						$fieldValue=$data[$fieldName][$index];
					
					$class=array(array('chbs-button','chbs-button-style-2'),array('chbs-button','chbs-button-style-2'));
					
					if($fieldValue==$index) 
					{
						$buttonSelected=true;
						array_push($class[1],'chbs-state-selected');
					}
					elseif($fieldValue==0) 
					{	
						array_push($class[0],'chbs-state-selected');
					}
					else
					{
						if((int)CHBSGlobalData::getGlobalData('booking_extra_created')!==1)
						{
							if((int)$value['meta']['state_default']===1)
							{
								$buttonSelected=true;
								array_push($class[1],'chbs-state-selected');	
							}
						}
					}
					
					$htmlColumn[2].=
					'
								<div class="chbs-column-3">
									<div class="chbs-button-radio">
										<a href="#"'.CHBSHelper::createCSSClassAttribute($class[1]).' data-value="'.esc_attr($index).'">
											'.esc_html__('Yes','chauffeur-booking-system').'
										</a>
										<a href="#"'.CHBSHelper::createCSSClassAttribute($class[0]).' data-value="0">
											'.esc_html__('No','chauffeur-booking-system').'
										</a>
										<input type="hidden" name="'.CHBSHelper::getFormName($fieldName.'['.$index.']',false).'"  value="'.$fieldValue.'"/>
										<span>*</span>
									</div>
								</div>
							</li>
					';					
				}
				else
				{
					$bookingExtraIdSelected=preg_split('/,/',$data['booking_extra_id']);
					
					$class=array('chbs-button','chbs-button-style-2');
					if(in_array($index,$bookingExtraIdSelected))
					{
						$buttonSelected=true;
						array_push($class,'chbs-state-selected');
					}
					else
					{
						if((int)CHBSGlobalData::getGlobalData('booking_extra_created')!==1)
						{
							if((int)$value['meta']['state_default']===1)
							{
								$buttonSelected=true;
								array_push($class,'chbs-state-selected');	
							}
						}
					}
					
					$htmlColumn[2].=
					'
								<div class="chbs-column-3">
									<a href="#"'.CHBSHelper::createCSSClassAttribute($class).' data-value="'.esc_attr($index).'">
										'.esc_html__('Select','chauffeur-booking-system').'
										<span class="chbs-meta-icon-tick"></span>
									</a>
								</div>
							</li>
					';
				}
			
				/***/
						
				$htmlThumbnail=null;
				if(($url=get_the_post_thumbnail_url($index))!==false)
				{
					$htmlThumbnail=
					'
						<div class="chbs-column-1-left">
							<img src="'.esc_attr($url).'"/>
						</div>
					';
				}
				
				$htmlColumn[0].=
				'
							<div class="chbs-column-1">
								'.$htmlThumbnail.'
								<div class="chbs-column-1-right">
									<span class="chbs-booking-form-extra-name">
										'.$value['post']->post_title.'
									</span>					
				';
				
				if((int)$bookingForm['meta']['price_hide']===0)
				{
					CHBSBookingHelper::getPriceType($bookingForm,$priceType,$sumType,$taxShow,$data['step']);
					
					$htmlColumn[0].=
					'
									<span class="chbs-booking-form-extra-price">
										'.$price['price'][$priceType]['format'].'
									</span>
					';
				}
				
				$htmlReadMoreLink=null;
				if($Validation->isNotEmpty($value['meta']['read_more_link_url_address']))
				{
					$htmlReadMoreLink='...&nbsp;<a href="'.esc_url($value['meta']['read_more_link_url_address']).'" target="_blank">'.__('read more','chauffeur-booking-system').'</a>';
				}
				
				$htmlColumn[0].=
				'
									<span class="chbs-booking-form-extra-description">
										'.esc_html($value['meta']['description']).$htmlReadMoreLink.'
									</span>
		
				';
				
				if((int)$bookingForm['meta']['booking_extra_note_display_enable']===1)
				{
					$fieldName='booking_extra_'.$index.'_note';

					$class=array('chbs-booking-form-extra-note');
					if(!$buttonSelected) array_push($class,'chbs-hidden');

					$htmlColumn[0].=
					'				
										<div'.CHBSHelper::createCSSClassAttribute($class).'>
											<div class="chbs-form-field">
												<label>'.esc_html__('Notes','chauffeur-booking-system').((int)$bookingForm['meta']['booking_extra_note_mandatory_enable']===1 ? ' *' : '').'</label>
												<textarea rows="1" cols="1" name="'.CHBSHelper::getFormName($fieldName,false).'">'.nl2br((array_key_exists($fieldName,$data) ? $data[$fieldName] : '')).'</textarea>
											</div>									
										</div>
					';
				}
				
				$htmlColumn[0].=
				'													
								</div>
							</div>
				';
			
				$html.=$htmlColumn[0].$htmlColumn[1].$htmlColumn[2].'</li>';
			}

			$html.=
			'
						</ul>
					</div>
				</div>
			';
		}
		
		CHBSGlobalData::setGlobalData('booking_extra_created',1);

		return($html);
	}
	
	/**************************************************************************/
	
	function getBookingFormDateAvailable($meta)
	{
		$date=array();
		
		$Date=new CHBSDate();
		$Validation=new CHBSValidation();
		
		$type=array(1=>'days',2=>'hours',3=>'minutes');
		
		/***/
		
		$timeStart=date_i18n('G:i');
		$dateStart=date_i18n('d-m-Y');
		
		$dateTimeStart=$dateStart.' '.$timeStart;
		
		$dayNumber=$Date->getDayNumberOfWeek($dateTimeStart);
		
		for($i=1;$i<=7;$i++)
		{		
			$businessHourStart=null;
			$businessHourStop=null;
			
			if(isset($meta['business_hour'][$dayNumber]['start']))
				$businessHourStart=$meta['business_hour'][$dayNumber]['start'];
			
			if(isset($meta['business_hour'][$dayNumber]['stop']))
				$businessHourStop=$meta['business_hour'][$dayNumber]['stop'];
		
			if(($Validation->isNotEmpty($businessHourStart)) && ($Validation->isNotEmpty($businessHourStop)))
			{
				if($i===1)
				{
					if($Date->timeInRange($timeStart,$businessHourStart,$businessHourStop))
					{
						break;
					}
				}
				else
				{					
					$dateTimeStart=date('d-m-Y',strtotime($dateStart.' +'.($i-1).' day')).' '.$businessHourStart;
					break;
				}
			}
			
			$dayNumber++;
			if($dayNumber===7) $dayNumber=1;
		}
		
		$dateStart=$dateTimeStart;
		
		/***/
		
		$step=$meta['timepicker_step'];
		
		if((int)$meta['timepicker_today_start_time_type']===2)
		{
			if($step<=0) $step=1;

			$i=0;
			while(1)
			{
				$dateTemp=strtotime($dateStart.' + '.$i.' minute');
				$minute=date_i18n('i',$dateTemp);
				if($minute%$step==0) break;
				$i++;
			}
		}
		else $dateTemp=strtotime($dateStart);
		
		/***/
			  
		$dateStart=strtotime('+ '.(int)$meta['booking_period_from'].' '.$type[(int)$meta['booking_period_type']],$dateTemp);
		
		/***/
		
		if($Validation->isEmpty($meta['booking_period_to'])) $dateStop=null;
		else $dateStop=strtotime('+ '.(int)$meta['booking_period_to'].' '.$type[(int)$meta['booking_period_type']],$dateStart);
		
		/***/
		
		$date['min']=date_i18n('d-m-Y H:i:s',$dateStart);
		$date['max']=is_null($dateStop) ? null : date_i18n('d-m-Y H:i:s',$dateStop);
		
		$date['min_format']=date(CHBSOption::getOption('date_format'),strtotime($date['min']));
		$date['max_format']=is_null($date['max']) ? null : date(CHBSOption::getOption('date_format'),strtotime($date['max']));

		return($date);
	} 
	
	/**************************************************************************/
	
	function createPagination($dictionary,$vehiclePerPage)
	{
		$vehicleTotal=count($dictionary);
		
		if($vehiclePerPage<=0) return(null);
		if($vehiclePerPage>=$vehicleTotal) return(null);
		
		$html=
		'
			<div class="chbs-pagination" data-page_current="1" data-vehicle_per_page="'.(int)$vehiclePerPage.'">
				<a href="#-1" class="chbs-pagination-prev"></a>
				<a href="#1" class="chbs-pagination-next"></a>
			</div>
		';
		
		return($html);
	}

	/**************************************************************************/
	
	function checkVehicleBidPrice()
	{		
		$response=array();
		
		$data=CHBSHelper::getPostOption();
		
		if(!is_array($bookingForm=$this->checkBookingForm($data['booking_form_id'])))
		{
			$response['html']=null;
			CHBSHelper::createJSONResponse($response);
		}
		
		if(!array_key_exists($data['vehicle_id'],$bookingForm['dictionary']['vehicle']))
		{
			$response['html']=null;
			CHBSHelper::createJSONResponse($response);			
		}
		
		$this->createSummaryPriceElementAjax(true);
	}
	
	/**************************************************************************/
	
	function createPaymentForm($bookingForm)
	{
		$Payment=new CHBSPayment();
		$Validation=new CHBSValidation();
		$PaymentTpay=new CHBSPaymentTpay();
	
		$html=null;

		if((int)$bookingForm['meta']['payment_woocommerce_step_3_enable']===0)
			unset($bookingForm['dictionary']['payment_woocommerce']);
			
		if(((array_key_exists('payment_woocommerce',$bookingForm['dictionary'])) && (count($bookingForm['dictionary']['payment_woocommerce']))) || ((array_key_exists('payment',$bookingForm['dictionary'])) && (count($bookingForm['dictionary']['payment']))))
		{
			if((int)$bookingForm['meta']['price_hide']===0)
			{
				$html.=
				'
					<h4 class="chbs-payment-header">
						'.esc_html__('Choose payment method','chauffeur-booking-system').'
					</h4>					
				';
				
				if(array_key_exists('payment_woocommerce',$bookingForm['dictionary']))
				{
					$htmlItem=null;
					
					foreach($bookingForm['dictionary']['payment_woocommerce'] as $index=>$value)
					{
						$htmlItem.=
						'
							<li>
								<a href="#" class="chbs-payment-type-woocommerce-'.esc_attr($value->{'id'}).'" data-payment-id="'.esc_attr($value->{'id'}).'" data-payment-name="'.esc_attr($value->{'title'}).'">			   
									<span class="chbs-payment-name">'.esc_html($value->{'title'}).'</span>
									<span class="chbs-meta-icon-tick"></span>
								</a>
							</li>
						';
					}
					
					$html.=
					'
						<ul class="chbs-payment chbs-payment-woocommerce chbs-list-reset">
							'.$htmlItem.'
						</ul>
					';
				}
				else
				{
					$htmlItem=null;
					
					foreach($bookingForm['dictionary']['payment'] as $index=>$value)
					{
						$style=array();
						$class=array('chbs-payment-type-'.$index);
						
						$paymentName=$Payment->getPaymentName($index);
						
						if($Validation->isNotEmpty($bookingForm['meta']['payment_'.$value[1].'_logo_src']))
						{
							$class[]='chbs-payment-background-image';
							$style['background-image']='url(\''.$bookingForm['meta']['payment_'.$value[1].'_logo_src'].'\')';
						}
						
						$htmlItem.=
						'
							<li>
								<a href="#" data-payment-id="'.esc_attr($index).'" data-payment-name="'.esc_attr($paymentName).'" '.CHBSHelper::createCSSClassAttribute($class).' '.CHBSHelper::createStyleAttribute($style).'>			   
						';
								
						if($index==1)
						{
							$htmlItem.=
							'
									<span class="chbs-meta-icon-wallet"></span>
									<span class="chbs-payment-name">'.esc_html($paymentName).'</span>
							';
						}
						else if($index==4)
						{
							$htmlItem.=
							'					
									<span class="chbs-meta-icon-bank"></span>
									<span class="chbs-payment-name">'.esc_html($Payment->getPaymentName($index)).'</span>
							';
					  
						}
						else if($index==5)
						{
							$htmlItem.=
							'				   
									<span class="chbs-meta-icon-bank"></span>
									<span class="chbs-payment-name">'.esc_html($Payment->getPaymentName($index)).'</span>
							';
						}
						
						$htmlItem.=
						'	
									<span class="chbs-meta-icon-tick"></span>
								</a>
							</li>	
						';
					}
					
					$html.=
					'	
						<ul class="chbs-payment chbs-list-reset">
							'.$htmlItem.'
						</ul>
					';
				}
			}
		}
		
		if((array_key_exists(CHBSPaymentTpay::PAYMENT_ID,$bookingForm['dictionary']['payment'])) && ((int)$bookingForm['meta']['payment_processing_enable']===1))
		{
			$tpayHtml=$PaymentTpay->getBankSelectionForm($bookingForm);
			
			$html.=
			'
				<div class="chbs-payment-tpay-selection chbs-hidden" data-payment-id="'.(int)CHBSPaymentTpay::PAYMENT_ID.'">
					<h5 class="chbs-payment-header">
						'.esc_html__('Choose Tpay payment method','chauffeur-booking-system').'
					</h5>
					'.$tpayHtml.'
				</div>
			';
		}
		
		return($html);
	}
	
	/**************************************************************************/
	
	function createShowMoreButton($status=1,$label=array())
	{
		if(!array_key_exists('more',$label))
			$label['more']=__('More info','chauffeur-booking-system');
		if(!array_key_exists('less',$label))
			$label['less']=__('Less info','chauffeur-booking-system');		
		
		$html.=
		'
			<span class="chbs-show-more-button">
				<a href="#" class="'.((int)$status===1 ? 'chbs-state-selected' : '').'">
					<span class="chbs-circle chbs-meta-icon-arrow-vertical-small"></span>
					<span>'.esc_html($label['more']).'</span>
					<span>'.esc_html($label['less']).'</span>
				</a> 
			</span>
		';
		
		return($html);
	}
	
	/**************************************************************************/
	
	function getMaximumBookingNumberTimeUnit()
	{
		return($this->maximumBookingNumberTimeUnit);
	}
	
	/**************************************************************************/
	
	function isMaximumBookingNumberTimeUnit($index)
	{
		return(array_key_exists($index,$this->getMaximumBookingNumberTimeUnit()) ? true : false);
	}
	
	/**************************************************************************/
	
	function fileUpload()
	{			
		$response=array('error'=>0);
		
		if(!is_array($_FILES))
			CHBSHelper::createJSONResponse($response);
   
		$name=key($_FILES);
			
		if(!is_array($_FILES[$name]))
			CHBSHelper::createJSONResponse($response);
	  
		/***/
		
		if(!CHBSFile::checkFileExtensionToUpload($_FILES[$name]['name']))
		{
			$response=array('error'=>1,'message'=>sprintf(esc_html__('Uploaded file type is not allowed. You can upload files with such a extensions like: %s.','chauffeur-booking-system'),preg_replace('/;/',', ',CHBSOption::getOption('file_extension_to_upload'))));
			CHBSHelper::createJSONResponse($response);			
		}

		/***/
		
		$fileName=CHBSHelper::createId();
		
		move_uploaded_file($_FILES[$name]['tmp_name'],dirname($_FILES[$name]['tmp_name']).'/'.$fileName);
		
		$response['name']=$_FILES[$name]['name'];
		$response['type']=$_FILES[$name]['type'];
		
		$response['tmp_name']=$fileName;
		
		CHBSHelper::createJSONResponse($response);
	}
	
	/**************************************************************************/
	
	function getBookingFormDurationDistance($meta)
	{
		$Length=new CHBSLength();
		$Validation=new CHBSValidation();
		
		/***/
		
		$distance=array();
		$durationDistance=array();
		
		/***/
		
		if($Validation->isNotEmpty($meta['duration_distance_list']))
			$distance=preg_split('/;/',$meta['duration_distance_list']);
		else
		{
			if($meta['duration_distance_max']>$meta['duration_distance_min'])
			{
				for($i=$meta['duration_distance_min'];$i<=$meta['duration_distance_max'];$i+=$meta['duration_distance_step']) $distance[]=$i;
			}
		}

		/***/
		
		$j=0;
		for($i=$meta['duration_min'];$i<=$meta['duration_max'];$i+=$meta['duration_step'])
		{
			$hasDistance=array_key_exists($j,$distance);
			$durationDistance[$i]['distance']=$hasDistance ? (int)$distance[$j] : 0;
			$durationDistance[$i]['label']=$hasDistance ? sprintf(esc_html__('%d hours (%s %s)','chauffeur-booking-system'),$i,$distance[$j],$Length->label(CHBSOption::getOption('length_unit'),7)) : sprintf(esc_html__('%d hours','chauffeur-booking-system'),$i);
			$j++;
		}
		
		/***/
		
		return($durationDistance);
	}
		
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/
