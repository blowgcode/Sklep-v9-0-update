<?php

/******************************************************************************/
/******************************************************************************/

class CHBSPriceRule
{
	public $priceType;
    public $priceAlterType;
    public $priceUseType;
    public $priceSourceType;
	/**************************************************************************/
	
	public function __construct()
	{
		$this->priceType=array
		(
			1=>array(__('Variable pricing','chauffeur-booking-system')),
			2=>array(__('Fixed pricing','chauffeur-booking-system'))
		);
		
		$this->priceAlterType=array
		(
			1=>array(__('- Inherited -','chauffeur-booking-system')),
			2=>array(__('Set value','chauffeur-booking-system')),	
			3=>array(__('Increase by value','chauffeur-booking-system')),
			4=>array(__('Decrease by value','chauffeur-booking-system')), 
			5=>array(__('Increase by percentage','chauffeur-booking-system')),
			6=>array(__('Decrease by percentage','chauffeur-booking-system')) 
		);
		
		$this->priceUseType=array
		(
			'fixed'=>array('use_tax'=>1),
			'fixed_return'=>array('use_tax'=>1),	
			'fixed_return_new_ride'=>array('use_tax'=>1),
			'initial'=>array('use_tax'=>1),	 
			'initial_return'=>array('use_tax'=>1),	 
			'initial_return_new_ride'=>array('use_tax'=>1),	 
			'delivery'=>array('use_tax'=>1),
			'delivery_return'=>array('use_tax'=>1),	
			'distance'=>array('use_tax'=>1),
			'distance_return'=> array('use_tax'=>1),	
			'distance_return_new_ride'=>array('use_tax'=>1),
			'hour'=>array('use_tax'=>1),	   
			'hour_return'=>array('use_tax'=>1),
			'hour_return_new_ride'=>array('use_tax'=>1),	
			'extra_time'=>array('use_tax'=>1),
            'waypoint'=>array('use_tax'=>1),
			'waypoint_duration'=>array('use_tax'=>1),
			'passenger_adult'=>array('use_tax'=>1),
			'passenger_children'=>array('use_tax'=>1), 
			'payment_paypal_fixed'=>array('use_tax'=>0),  
			'payment_paypal_percentage'=>array('use_tax'=>0),   
			'payment_stripe_fixed'=>array('use_tax'=>0),
			'payment_stripe_percentage'=>array('use_tax'=>0)
		);
		
		$this->priceSourceType=array
		(
			1=>array(__('Set directly in the "Prices" tab','chauffeur-booking-system')),
			2=>array(__('Calculation based on distance (all ranges)','chauffeur-booking-system')),
			3=>array(__('Calculation based on distance (exact range)','chauffeur-booking-system')),
			6=>array(__('Calculation based on duration (all ranges)','chauffeur-booking-system')),
			7=>array(__('Calculation based on duration (exact range)','chauffeur-booking-system')),
			4=>array(__('Calculation based on distance between base and pickup location (all ranges)','chauffeur-booking-system')),		
			5=>array(__('Calculation based on distance between base and pickup location (exact range)','chauffeur-booking-system')),
			8=>array(__('Calculation based on distance between drop-off and base location (all ranges)','chauffeur-booking-system')),		
			9=>array(__('Calculation based on distance between drop-off and base location (exact range)','chauffeur-booking-system')),
		);
	}
	
	/**************************************************************************/
	
	function getPriceSourceType()
	{
		return($this->priceSourceType);
	}
	
	/**************************************************************************/
	
	function isPriceSourceType($type)
	{
		return(array_key_exists($type,$this->getPriceSourceType()));
	}
	
	/**************************************************************************/
	
	function getPriceSourceTypeName($type)
	{
		if(!$this->isPriceSourceType($type)) return('');
		return($this->priceSourceType[$type][0]);
	}
	
	/**************************************************************************/
	
	function getPriceIndexName($index,$type='value')
	{
		return('price_'.$index.'_'.$type);
	}
	
	/**************************************************************************/
	
	function extractPriceFromData($price,$data)
	{
		$priceComponent=array('value','alter_type_id','tax_rate_id');
		
		foreach($this->getPriceUseType() as $priceUseTypeIndex=>$priceUseTypeValue)
		{
			foreach($priceComponent as $priceComponentIndex=>$priceComponentValue)
			{
				$key=$this->getPriceIndexName($priceUseTypeIndex,$priceComponentValue);
				if(isset($data[$key])) $price[$key]=$data[$key];
				else
				{
					if($priceComponentValue==='alter_type_id') $price[$key]=2;
				}
			}
		}
		
		$price['price_type']=$data['price_type'];

		return($price);
	}
	
	/**************************************************************************/
	
	function getPriceType()
	{
		return($this->priceType);
	}
	
	/**************************************************************************/
	
	function isPriceType($priceType)
	{
		return(array_key_exists($priceType,$this->priceType));
	}
	
	/**************************************************************************/
	
	function getPriceAlterType()
	{
		return($this->priceAlterType);
	}
	
	/**************************************************************************/
	
	function isPriceAlterType($priceAlterType)
	{
		return(array_key_exists($priceAlterType,$this->priceAlterType));
	}
	
	/**************************************************************************/
	
	function getPriceUseType()
	{
		return($this->priceUseType);
	}
	
	/**************************************************************************/
	
	function isPriceUseType($priceUseType)
	{
		return(array_key_exists($priceUseType,$this->priceUseType));
	}
	
	/**************************************************************************/
	
	public function init()
	{
		$this->registerCPT();
	}
	
	/**************************************************************************/

	public static function getCPTName()
	{
		return(PLUGIN_CHBS_CONTEXT.'_price_rule');
	}
		
	/**************************************************************************/
	
	private function registerCPT()
	{
		register_post_type
		(
			self::getCPTName(),
			array
			(
				'labels'=>array
				(
					'name'=>__('Pricing Rules','chauffeur-booking-system'),
					'singular_name'=>__('Pricing Rule','chauffeur-booking-system'),
					'add_new'=>__('Add New','chauffeur-booking-system'),
					'add_new_item'=>__('Add New Pricing Rule','chauffeur-booking-system'),
					'edit_item'=>__('Edit Pricing Rule','chauffeur-booking-system'),
					'new_item'=>__('New Pricing Rule','chauffeur-booking-system'),
					'all_items'=>__('Pricing Rules','chauffeur-booking-system'),
					'view_item'=>__('View Pricing Rule','chauffeur-booking-system'),
					'search_items'=>__('Search Pricing Rules','chauffeur-booking-system'),
					'not_found'=>__('No Pricing Rules Found','chauffeur-booking-system'),
					'not_found_in_trash'=>__('No Pricing Rules in Trash','chauffeur-booking-system'), 
					'parent_item_colon'=>'',
					'menu_name'=>__('Pricing Rules','chauffeur-booking-system')
				),	
				'public'=>false,  
				'show_ui'=>true, 
				'show_in_menu'=>'edit.php?post_type='.CHBSBooking::getCPTName(),
				'capability_type'=>'post',
				'menu_position'=>2,
				'hierarchical'=>false,  
				'rewrite'=>false,  
				'supports'=>array('title','page-attributes')  
			)
		);
		
		add_action('save_post',array($this,'savePost'));
		add_action('add_meta_boxes_'.self::getCPTName(),array($this,'addMetaBox'));
		add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_price_rule',array($this,'adminCreateMetaBoxClass'));
		
		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns')); 
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));
	}

	/**************************************************************************/
	
	function addMetaBox()
	{
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_price_rule',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');		
	}
	
	/**************************************************************************/
	
	function addMetaBoxMain()
	{
		global $post;
		
		$data=array();
		
		$Route=new CHBSRoute();
		$Vehicle=new CHBSVehicle();
		$TaxRate=new CHBSTaxRate();
		$Country=new CHBSCountry();
		$Geofence=new CHBSGeofence();
		$Location=new CHBSLocation();
		$PriceType=new CHBSPriceType();
		$ServiceType=new CHBSServiceType();
		$BookingForm=new CHBSBookingForm();
		$TransferType=new CHBSTransferType();
		$VehicleCompany=new CHBSVehicleCompany();
		
		$data['meta']=CHBSPostMeta::getPostMeta($post);
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_price_rule');

		$data['dictionary']['route']=$Route->getDictionary(array(),2);
		$data['dictionary']['country']=$Country->getCountry();
		$data['dictionary']['vehicle']=$Vehicle->getDictionary(array(),5);
		$data['dictionary']['tax_rate']=$TaxRate->getDictionary();
		$data['dictionary']['geofence']=$Geofence->getDictionary(array(),2);
		$data['dictionary']['location']=$Location->getDictionary(array(),2);
		$data['dictionary']['price_type']=$PriceType->getPriceType();
		$data['dictionary']['alter_type']=$this->getPriceAlterType();
		$data['dictionary']['booking_form']=$BookingForm->getDictionary(array(),2);
		$data['dictionary']['service_type']=$ServiceType->getServiceType();
		$data['dictionary']['transfer_type']=$TransferType->getTransferType();
		$data['dictionary']['vehicle_company']=$VehicleCompany->getDictionary(array(),2);
		
		$data['dictionary']['price_source_type']=$this->getPriceSourceType();
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_price_rule.php');
		echo $Template->output();			
	}
	
	 /**************************************************************************/
	
	function adminCreateMetaBoxClass($class) 
	{
		array_push($class,'to-postbox-1');
		return($class);
	}

	/**************************************************************************/
	
	function setPostMetaDefault(&$meta)
	{
		$TaxRate=new CHBSTaxRate();
		
		CHBSHelper::setDefault($meta,'booking_form_id',array(-1));
		CHBSHelper::setDefault($meta,'service_type_id',array(-1));
		CHBSHelper::setDefault($meta,'transfer_type_id',array(-1));
		
		CHBSHelper::setDefault($meta,'route_id',array(-1));
		CHBSHelper::setDefault($meta,'vehicle_id',array(-1));
		CHBSHelper::setDefault($meta,'vehicle_company_id',array(-1));
		
		CHBSHelper::setDefault($meta,'pickup_day_number',array(-1));
		CHBSHelper::setDefault($meta,'pickup_return_date_difference','');
		
		CHBSHelper::setDefault($meta,'location_fixed_pickup',array(-1));
		CHBSHelper::setDefault($meta,'location_fixed_dropoff',array(-1));
		
		CHBSHelper::setDefault($meta,'location_country_pickup',array(-1));
		CHBSHelper::setDefault($meta,'location_country_dropoff',array(-1));	   
		
		CHBSHelper::setDefault($meta,'location_geofence_pickup',array(-1));
		CHBSHelper::setDefault($meta,'location_geofence_dropoff',array(-1));
		
		CHBSHelper::setDefault($meta,'location_zip_code_pickup','');
		CHBSHelper::setDefault($meta,'location_zip_code_dropoff','');
		
		CHBSHelper::setDefault($meta,'location_swap_enable',0);

		CHBSHelper::setDefault($meta,'price_source_type',1);
		
		CHBSHelper::setDefault($meta,'process_next_rule_enable',0);
        CHBSHelper::setDefault($meta,'rule_level','');
		
		CHBSHelper::setDefault($meta,'minimum_order_value',CHBSPrice::getDefaultPrice());
		
		CHBSHelper::setDefault($meta,'custom_vehicle_selection_enable',0);
		CHBSHelper::setDefault($meta,'custom_vehicle_selection_button_url_address','');
		CHBSHelper::setDefault($meta,'custom_vehicle_selection_button_label',__('Select','chauffeur-booking-system'));
		CHBSHelper::setDefault($meta,'custom_vehicle_selection_button_url_target',1);
		CHBSHelper::setDefault($meta,'custom_vehicle_selection_first_step_redirect',0);
		CHBSHelper::setDefault($meta,'custom_vehicle_selection_hide_price',1);
				
		CHBSHelper::setDefault($meta,'price_type',1);
		
		foreach($this->getPriceUseType() as $index=>$value)
		{
			CHBSHelper::setDefault($meta,'price_'.$index.'_value',CHBSPrice::getDefaultPrice());
			CHBSHelper::setDefault($meta,'price_'.$index.'_alter_type_id',2);
			
			if((int)$value['use_tax']===1)
				CHBSHelper::setDefault($meta,'price_'.$index.'_tax_rate_id',$TaxRate->getDefaultTaxPostId());			
		}
		
		if((!array_key_exists('distance',$meta)) || (!is_array($meta['distance']))) 
			$meta['distance']=array();
		
		foreach($meta['distance'] as $index=>$value)
		{
			if(!$this->isPriceAlterType($meta['distance'][$index]['price_alter_type_id']))
				$meta['distance'][$index]['price_alter_type_id']=2;
		}
	}
	
	/**************************************************************************/
	
	function savePost($postId)
	{	  
		if(!$_POST) return(false);
		
		if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_price_rule_noncename','savePost')===false) return(false);
		
		$Date=new CHBSDate();
		$Route=new CHBSRoute();
		$Vehicle=new CHBSVehicle();
		$TaxRate=new CHBSTaxRate();
		$Country=new CHBSCountry();
		$Geofence=new CHBSGeofence();
		$Location=new CHBSLocation();
		$ServiceType=new CHBSServiceType();
		$BookingForm=new CHBSBookingForm();
		$TransferType=new CHBSTransferType();
		$VehicleCompany=new CHBSVehicleCompany();
		
		$Validation=new CHBSValidation();
		
		$option=CHBSHelper::getPostOption();
		
		/***/
		
		$dictionary=array
		(
			'booking_form_id'=>array
			(
				'dictionary'=>$BookingForm->getDictionary()
			),
			'service_type_id'=>array
			(
				'dictionary'=>$ServiceType->getServiceType()
			),  
			'transfer_type_id'=>array
			(
				'dictionary'=>$TransferType->getTransferType()
			), 
			'route_id'=>array
			(
				'dictionary'=>$Route->getDictionary()
			),
			'vehicle_id'=>array
			(
				'dictionary'=>$Vehicle->getDictionary()
			),
			'vehicle_company_id'=>array
			(
				'dictionary'=>$VehicleCompany->getDictionary()
			),
			'pickup_day_number'=>array
			(
				'dictionary'=>array(1,2,3,4,5,6,7)
			),
			'location_fixed_pickup'=>array
			(
				'dictionary'=>$Location->getDictionary()
			),
			'location_fixed_dropoff'=>array
			(
				'dictionary'=>$Location->getDictionary()
			),		
			'location_country_pickup'=>array
			(
				'dictionary'=>$Country->getCountry()
			),
			'location_country_dropoff'=>array
			(
				'dictionary'=>$Country->getCountry()
			),				
			'location_geofence_pickup'=>array
			(
				'dictionary'=>$Geofence->getDictionary()
			),
			'location_geofence_dropoff'=>array
			(
				'dictionary'=>$Geofence->getDictionary()
			)	  
		);
		
		foreach($dictionary as $dIndex=>$dValue)
		{
			$option[$dIndex]=(array)CHBSHelper::getPostValue($dIndex);
			if(in_array(-1,$option[$dIndex]))
			{
				$option[$dIndex]=array(-1);
			}
			else
			{
				foreach($option[$dIndex] as $oIndex=>$oValue)
				{
					if(!isset($dValue['dictionary']))
						unset($option[$dIndex][$oIndex]);				
				}
			}			 
		}
		
		/***/
		
		if(!$Validation->isBool($option['location_swap_enable']))
			$option['location_swap_enable']=1;
		
		/***/
		
		$date=array();
		foreach($option['pickup_date']['start'] as $index=>$value)
		{
			$t=array($value,$option['pickup_date']['stop'][$index]);
			
			$t[0]=$Date->formatDateToStandard($t[0]);
			$t[1]=$Date->formatDateToStandard($t[1]);
			
			if(!$Validation->isDate($t[0])) continue;
			if(!$Validation->isDate($t[1])) continue;
			
			if($Date->compareDate($t[0],$t[1])==1) continue;
			
			array_push($date,array('start'=>$t[0],'stop'=>$t[1]));
		}
		$option['pickup_date']=$date;

		/***/
		
		$date=array();
		foreach($option['return_date']['start'] as $index=>$value)
		{
			$t=array($value,$option['return_date']['stop'][$index]);
			
			$t[0]=$Date->formatDateToStandard($t[0]);
			$t[1]=$Date->formatDateToStandard($t[1]);
			
			if(!$Validation->isDate($t[0])) continue;
			if(!$Validation->isDate($t[1])) continue;
			
			if($Date->compareDate($t[0],$t[1])==1) continue;
			
			array_push($date,array('start'=>$t[0],'stop'=>$t[1]));
		}
		$option['return_date']=$date;

		/***/
		
		$time=array();
		foreach($option['pickup_time']['start'] as $index=>$value)
		{
			$t=array($value,$option['pickup_time']['stop'][$index]);
			
			$t[0]=$Date->formatTimeToStandard($t[0]);
			$t[1]=$Date->formatTimeToStandard($t[1]);
			
			if(!$Validation->isTime($t[0])) continue;
			if(!$Validation->isTime($t[1])) continue;
			
			if($Date->compareTime($t[0],$t[1])!=2) continue;
			
			array_push($time,array('start'=>$t[0],'stop'=>$t[1]));
		}		
		$option['pickup_time']=$time;
		
		/***/
		
		$time=array();
		foreach($option['return_time']['start'] as $index=>$value)
		{
			$t=array($value,$option['return_time']['stop'][$index]);
			
			$t[0]=$Date->formatTimeToStandard($t[0]);
			$t[1]=$Date->formatTimeToStandard($t[1]);
			
			if(!$Validation->isTime($t[0])) continue;
			if(!$Validation->isTime($t[1])) continue;
			
			if($Date->compareTime($t[0],$t[1])!=2) continue;
			
			array_push($time,array('start'=>$t[0],'stop'=>$t[1]));
		}		
		$option['return_time']=$time;
		
		/***/
		
		if($Validation->isNotEmpty($option['pickup_return_date_difference']))
		{
			if(!$Validation->isNumber($option['pickup_return_date_difference'],0,999999999))
			{
				$option['pickup_return_date_difference']='';;
			}
		}
		
		/***/
		
		$distance=array();	  
		foreach($option['distance']['start'] as $index=>$value)
		{
			$t=array($value,$option['distance']['stop'][$index],$option['distance']['price'][$index],$option['distance']['price_alter_type_id'][$index]);
			
			if(!$Validation->isFloat($t[0],0,999999999.99,false,1)) continue;
			if(!$Validation->isFloat($t[1],0,999999999.99,false,1)) continue;
			
			if(!CHBSPrice::isPrice($t[2],true)) continue;
		
			if(!$this->isPriceAlterType($t[3])) continue;
			
			if($t[0]>$t[1]) continue;
			
			if(CHBSOption::getOption('length_unit')==2)
			{
				$Length=new CHBSLength();
				
				$t[0]=$Length->convertUnit($t[0],2,1);
				$t[1]=$Length->convertUnit($t[1],2,1);
			}

			array_push($distance,array('start'=>CHBSFormat::toSaveFloat($t[0],false,1),'stop'=>CHBSFormat::toSaveFloat($t[1],false,1),'price'=>CHBSPrice::formatToSave($t[2]),'price_alter_type_id'=>$t[3]));
		}
		$option['distance']=$distance;
		
		/***/
		
		$distance=array();
		foreach($option['distance_base_to_pickup']['start'] as $index=>$value)
		{
			$t=array($value,$option['distance_base_to_pickup']['stop'][$index],$option['distance_base_to_pickup']['price'][$index]);
			
			if(!$Validation->isFloat($t[0],0,999999999.99,false,1)) continue;
			if(!$Validation->isFloat($t[1],0,999999999.99,false,1)) continue;
			if(!CHBSPrice::isPrice($t[2],true)) continue;
			
			if($t[0]>$t[1]) continue;
			
			if(CHBSOption::getOption('length_unit')==2)
			{
				$Length=new CHBSLength();
				
				$t[0]=$Length->convertUnit($t[0],2,1);
				$t[1]=$Length->convertUnit($t[1],2,1);
			}

			array_push($distance,array('start'=>CHBSFormat::toSaveFloat($t[0],false,1),'stop'=>CHBSFormat::toSaveFloat($t[1],false,1),'price'=>CHBSPrice::formatToSave($t[2])));
		}
		$option['distance_base_to_pickup']=$distance;
		
		/***/
		
		$distance=array();
		foreach($option['distance_drop_off_to_base']['start'] as $index=>$value)
		{
			$t=array($value,$option['distance_drop_off_to_base']['stop'][$index],$option['distance_drop_off_to_base']['price'][$index]);
			
			if(!$Validation->isFloat($t[0],0,999999999.99,false,1)) continue;
			if(!$Validation->isFloat($t[1],0,999999999.99,false,1)) continue;
			if(!CHBSPrice::isPrice($t[2],true)) continue;
			
			if($t[0]>$t[1]) continue;
			
			if(CHBSOption::getOption('length_unit')==2)
			{
				$Length=new CHBSLength();
				
				$t[0]=$Length->convertUnit($t[0],2,1);
				$t[1]=$Length->convertUnit($t[1],2,1);
			}

			array_push($distance,array('start'=>CHBSFormat::toSaveFloat($t[0],false,1),'stop'=>CHBSFormat::toSaveFloat($t[1],false,1),'price'=>CHBSPrice::formatToSave($t[2])));
		}
		$option['distance_drop_off_to_base']=$distance;		
		
		/***/
		
		$passenger=array();
	   
		foreach($option['passenger']['start'] as $index=>$value)
		{
			$d=array($value,$option['passenger']['stop'][$index]);
			
			if(!$Validation->isNumber($d[0],1,99)) continue;
			if(!$Validation->isNumber($d[1],1,99)) continue;
  
			if($d[0]>$d[1]) continue;

			array_push($passenger,array('start'=>$d[0],'stop'=>$d[1]));
		}		
		
		$option['passenger']=$passenger;
	   
		/***/
		
		$duration=array();
		foreach($option['duration']['start'] as $index=>$value)
		{
			$d=array($value,$option['duration']['stop'][$index],$option['duration']['price'][$index]);
			
			$d[0]=CHBSDate::fillTime($d[0]);
			$d[1]=CHBSDate::fillTime($d[1]);

			if(!$Validation->isTimeDuration($d[0])) continue;
			if(!$Validation->isTimeDuration($d[1])) continue;
			if(!CHBSPrice::isPrice($d[2],true)) continue;
			
			if($d[0]>$d[1]) continue;

			array_push($duration,array('start'=>CHBSDate::fillTime($d[0],4),'stop'=>CHBSDate::fillTime($d[1],4),'price'=>CHBSPrice::formatToSave($d[2])));
		}		
		$option['duration']=$duration;	
		
		/***/
		
		if(!$Validation->isBool($option['process_next_rule_enable']))
			$option['process_next_rule_enable']=0;	
        
        if($Validation->isNotEmpty($option['rule_level']))
        {
            if(!$Validation->isNumber($option['rule_level'],-9999,9999))
                $option['rule_level']='';
        }
        else $option['rule_level']='';
        
		if(!$this->isPriceSourceType($option['price_source_type']))
			$option['price_source_type']=1; 
		
		if(!$Validation->isPrice($option['minimum_order_value'],false))
		   $option['minimum_order_value']=0.00;
		
		$option['minimum_order_value']=CHBSPrice::formatToSave($option['minimum_order_value'],true);
	   
		/***/
		
		if(!$Validation->isBool($option['custom_vehicle_selection_enable']))
			$option['custom_vehicle_selection_enable']=0;				
		if(!in_array($option['custom_vehicle_selection_button_url_target'],array(1,2)))
			$option['custom_vehicle_selection_button_url_target']=1;	
		if(!$Validation->isBool($option['custom_vehicle_selection_first_step_redirect']))
			$option['custom_vehicle_selection_first_step_redirect']=0;				
		if(!$Validation->isBool($option['custom_vehicle_selection_hide_price']))
			$option['custom_vehicle_selection_hide_price']=1;		
		
		/***/
		
		if(!$this->isPriceType($option['price_type']))
			$option['price_type']=1;

		/***/
		
		foreach($this->getPriceUseType() as $index=>$value)
		{
			if(!CHBSPrice::isPrice($option['price_'.$index.'_value'],false))
				$option['price_'.$index.'_value']=0.00;
			
			$option['price_'.$index.'_value']=CHBSPrice::formatToSave($option['price_'.$index.'_value'],false);
			
			if(!$this->isPriceAlterType($option['price_'.$index.'_alter_type_id']))
				$option['price_'.$index.'_alter_type_id']=1;
			
			if(in_array($option['price_'.$index.'_alter_type_id'],array(5,6)))
			{
				if(!$Validation->isNumber($option['price_'.$index.'_alter_type_id'],0,100))
					$option['price_'.$index.'_alter_type_id']=0;
			}
			
			if((int)$value['use_tax']===1)
			{
				if((int)$option['price_'.$index.'_tax_rate_id']===-1)
					$option['price_'.$index.'_tax_rate_id']=-1;
				else
				{
					if(!$TaxRate->isTaxRate($option['price_'.$index.'_tax_rate_id']))
						$option['price_'.$index.'_tax_rate_id']=0; 
				}
			}
		}

		/***/

		$key=array
		(
			'booking_form_id',
			'service_type_id',
			'transfer_type_id',
			'route_id',
			'vehicle_id',
			'vehicle_company_id',
			'location_fixed_pickup',
			'location_fixed_dropoff',
			'location_country_pickup',
			'location_country_dropoff',
			'location_geofence_pickup',
			'location_geofence_dropoff',
			'location_zip_code_pickup',
			'location_zip_code_dropoff',
			'location_swap_enable',
			'pickup_day_number',
			'pickup_date',
			'return_date',
			'pickup_time',
			'return_time',
			'pickup_return_date_difference',
			'distance',
			'distance_base_to_pickup',
			'distance_drop_off_to_base',
			'passenger',
			'duration',
			'process_next_rule_enable',
            'rule_level',
			'price_source_type',
			'minimum_order_value',
			'custom_vehicle_selection_enable',
			'custom_vehicle_selection_button_url_address',
			'custom_vehicle_selection_button_label',
			'custom_vehicle_selection_button_url_target',
			'custom_vehicle_selection_first_step_redirect',
			'custom_vehicle_selection_hide_price',
			'price_type',
		);
		
		foreach($this->getPriceUseType() as $index=>$value)
		{
			array_push($key,'price_'.$index.'_value','price_'.$index.'_alter_type_id');
			if((int)$value['use_tax']===1) array_push($key,'price_'.$index.'_tax_rate_id');
		}   
		foreach($key as $value)
			CHBSPostMeta::updatePostMeta($postId,$value,$option[$value]);
	}
	
	/**************************************************************************/

	function manageEditColumns($column)
	{
		$column=array
		(
			'cb'=>$column['cb'],
			'title'=>$column['title'],
			'condition'=>__('Conditions','chauffeur-booking-system'),
			'price'=>__('Prices','chauffeur-booking-system')
		);
   
		return($column);		  
	}
	
	/**************************************************************************/
	
	function getPricingRuleAdminListDictionary()
	{
		$dictionary=array();
	
		$Date=new CHBSDate();
		$Route=new CHBSRoute();
		$Country=new CHBSCountry();
		$Vehicle=new CHBSVehicle();
		$Geofence=new CHBSGeofence();
		$Location=new CHBSLocation();
		$ServiceType=new CHBSServiceType();
		$BookingForm=new CHBSBookingForm();
		$TransferType=new CHBSTransferType();
		$VehicleCompany=new CHBSVehicleCompany();
		
		$dictionary['route']=$Route->getDictionary();
		$dictionary['vehicle']=$Vehicle->getDictionary();
		$dictionary['geofence']=$Geofence->getDictionary();
		$dictionary['location']=$Location->getDictionary();
		$dictionary['booking_form']=$BookingForm->getDictionary();
		$dictionary['vehicle_company']=$VehicleCompany->getDictionary();
		
		$dictionary['country']=$Country->getCountry();
		$dictionary['service_type']=$ServiceType->getServiceType();
		$dictionary['transfer_type']=$TransferType->getTransferType();
		
		$dictionary['day']=$Date->day;
		
		return($dictionary);
	}
	
	/**************************************************************************/
	
	function displayPricingRuleAdminListValue($data,$dictionary,$link=false,$sort=false)
	{
		if(in_array(-1,$data)) return(__('','chauffeur-booking-system'));
		
		$html=null;
		
		$Validation=new CHBSValidation();
		
		$dataSort=array();

		foreach($data as $value)
		{
			if(!array_key_exists($value,$dictionary)) continue;

			if(array_key_exists('post',$dictionary[$value]))
				$label=$dictionary[$value]['post']->post_title;
			else $label=$dictionary[$value][0];			

			$dataSort[$value]=$label;
		}

		if($sort) asort($dataSort);

		$data=$dataSort;
		
		foreach($data as $index=>$value)
		{
			$label=$value;
			
			if($link) $label='<a href="'.get_edit_post_link($index).'">'.$value.'</a>';
			if($Validation->isNotEmpty($html)) $html.=', ';
			$html.=$label;
		}
		
		return($html);
	}
	
	/**************************************************************************/
	
	function managePostsCustomColumn($column)
	{
		global $post;
		
		$Date=new CHBSDate();
		$Length=new CHBSLength();
		$PriceType=new CHBSPriceType();
		$Validation=new CHBSValidation();
		
		$meta=CHBSPostMeta::getPostMeta($post);
		
		$dictionary=CHBSGlobalData::setGlobalData('pricing_rule_admin_list_dictionary',array($this,'getPricingRuleAdminListDictionary'));
		
		switch($column) 
		{
			case 'condition':
				
				$html=array
				(
					'pickup_date'=>'',
					'return_date'=>'',
					'pickup_time'=>'',
					'return_time'=>'',
					'distance'=>'',
					'distance_base_to_pickup'=>'',
					'distance_drop_off_to_base'=>'',
					'duration'=>'',
					'passenger'=>''
				);
				
				if((isset($meta['pickup_date'])) && (count($meta['pickup_date'])))
				{
					foreach($meta['pickup_date'] as $value)
					{
						if(!$Validation->isEmpty($html['pickup_date'])) $html['pickup_date'].=', ';
						$html['pickup_date'].=$Date->formatDateToDisplay($value['start']).' - '.$Date->formatDateToDisplay($value['stop']);
					}
				}
	
				if((isset($meta['return_date'])) && (count($meta['return_date'])))
				{
					foreach($meta['return_date'] as $value)
					{
						if(!$Validation->isEmpty($html['return_date'])) $html['return_date'].=', ';
						$html['return_date'].=$Date->formatDateToDisplay($value['start']).' - '.$Date->formatDateToDisplay($value['stop']);
					}
				}				
				
				if((isset($meta['pickup_time'])) && (count($meta['pickup_time'])))
				{
					foreach($meta['pickup_time'] as $value)
					{
						if(!$Validation->isEmpty($html['pickup_time'])) $html['pickup_time'].=', ';
						$html['pickup_time'].=$Date->formatTimeToDisplay($value['start']).' - '.$Date->formatTimeToDisplay($value['stop']);
					}
				}
				
				if((isset($meta['return_time'])) && (count($meta['return_time'])))
				{
					foreach($meta['return_time'] as $value)
					{
						if(!$Validation->isEmpty($html['return_time'])) $html['return_time'].=', ';
						$html['return_time'].=$Date->formatTimeToDisplay($value['start']).' - '.$Date->formatTimeToDisplay($value['stop']);
					}
				}
				
				if((isset($meta['distance'])) && (count($meta['distance'])))
				{
					foreach($meta['distance'] as $value)
					{
						if(CHBSOption::getOption('length_unit')==2)
						{
							$value['start']=round($Length->convertUnit($value['start'],1,2),1);
							$value['stop']=round($Length->convertUnit($value['stop'],1,2),1); 
						}
						
						if(!$Validation->isEmpty($html['distance'])) $html['distance'].=', ';
						$html['distance'].=$value['start'].' - '.$value['stop'].' '.$Length->getUnitShortName(CHBSOption::getOption('length_unit'));
						
						if(in_array($meta['price_source_type'],array(2,3)))
							$html['distance'].=' ('.self::displayPriceAlter($value['price'],$value['price_alter_type_id']).')';
					}
				}  
				
				if((isset($meta['distance_base_to_pickup'])) && (count($meta['distance_base_to_pickup'])))
				{
					foreach($meta['distance_base_to_pickup'] as $value)
					{
						if(CHBSOption::getOption('length_unit')==2)
						{
							$value['start']=round($Length->convertUnit($value['start'],1,2),1);
							$value['stop']=round($Length->convertUnit($value['stop'],1,2),1); 
						}
						
						if(!$Validation->isEmpty($html['distance_base_to_pickup'])) $html['distance_base_to_pickup'].=', ';
						$html['distance_base_to_pickup'].=$value['start'].' - '.$value['stop'].' '.$Length->getUnitShortName(CHBSOption::getOption('length_unit'));
						
						if(in_array($meta['price_source_type'],array(4,5)))
							$html['distance_base_to_pickup'].=' ('.CHBSPrice::format($value['price'],CHBSOption::getOption('currency')).')';
					}
				}   
				
				if((isset($meta['distance_drop_off_to_base'])) && (count($meta['distance_drop_off_to_base'])))
				{
					foreach($meta['distance_drop_off_to_base'] as $value)
					{
						if(CHBSOption::getOption('length_unit')==2)
						{
							$value['start']=round($Length->convertUnit($value['start'],1,2),1);
							$value['stop']=round($Length->convertUnit($value['stop'],1,2),1); 
						}
						
						if(!$Validation->isEmpty($html['distance_drop_off_to_base'])) $html['distance_drop_off_to_base'].=', ';
						$html['distance_drop_off_to_base'].=$value['start'].' - '.$value['stop'].' '.$Length->getUnitShortName(CHBSOption::getOption('length_unit'));
						
						if(in_array($meta['price_source_type'],array(4,5)))
							$html['distance_drop_off_to_base'].=' ('.CHBSPrice::format($value['price'],CHBSOption::getOption('currency')).')';
					}
				} 				
				
				if((isset($meta['passenger'])) && (count($meta['passenger'])))
				{
					foreach($meta['passenger'] as $value)
					{
						if(!$Validation->isEmpty($html['passenger'])) $html['passenger'].=', ';
						$html['passenger'].=$value['start'].' - '.$value['stop'];						
					}
				}				
				
				if((isset($meta['duration'])) && (count($meta['duration'])))
				{
					foreach($meta['duration'] as $value)
					{
						if(!$Validation->isEmpty($html['duration'])) $html['duration'].=', ';
						$html['duration'].=$value['start'].' - '.$value['stop'];	

						if(in_array($meta['price_source_type'],array(6,7)))
							$html['duration'].=' ('.CHBSPrice::format($value['price'],CHBSOption::getOption('currency')).')';						
					}
				}	
				
				/***/
				
				echo 
				'
					<table class="to-table-post-list">
						<tr>
							<td>'.esc_html__('Booking forms','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['booking_form_id'],$dictionary['booking_form'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Service types','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['service_type_id'],$dictionary['service_type'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Transfer types','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['transfer_type_id'],$dictionary['transfer_type'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Routes','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['route_id'],$dictionary['route'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Vehicles','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['vehicle_id'],$dictionary['vehicle'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Vehicle companies','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['vehicle_company_id'],$dictionary['vehicle_company'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Pickup locations','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['location_fixed_pickup'],$dictionary['location'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Drop-off locations','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['location_fixed_dropoff'],$dictionary['location'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Pickup country locations','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['location_country_pickup'],$dictionary['country'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Drop-off country locations','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['location_country_dropoff'],$dictionary['country'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Pickup geofence areas','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['location_geofence_pickup'],$dictionary['geofence'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Drop-off geofence areas','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['location_geofence_dropoff'],$dictionary['geofence'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Pickup ZIP codes','chauffeur-booking-system').'</td>
							<td>'.esc_html($meta['location_zip_code_pickup']).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Drop-off ZIP codes','chauffeur-booking-system').'</td>
							<td>'.esc_html($meta['location_zip_code_dropoff']).'</td>
						</tr>						
						<tr>
							<td>'.esc_html__('Pickup day numbers','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['pickup_day_number'],$dictionary['day'],true,true).'</td>
						</tr>						
						<tr>
							<td>'.esc_html__('Pickup dates','chauffeur-booking-system').'</td>
							<td>'.$html['pickup_date'].'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Return dates','chauffeur-booking-system').'</td>
							<td>'.$html['return_date'].'</td>
						</tr>						
						<tr>
							<td>'.esc_html__('Pickup hours','chauffeur-booking-system').'</td>
							<td>'.$html['pickup_time'].'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Return hours','chauffeur-booking-system').'</td>
							<td>'.$html['return_time'].'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Return/pickup date difference','chauffeur-booking-system').'</td>
							<td>'.esc_html($meta['pickup_return_date_difference']).'</td>
						</tr>						
						<tr>
							<td>'.esc_html__('Distance','chauffeur-booking-system').'</td>
							<td>'.$html['distance'].'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Distance from base to pickup location','chauffeur-booking-system').'</td>
							<td>'.$html['distance_base_to_pickup'].'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Distance from dropoff to base location','chauffeur-booking-system').'</td>
							<td>'.$html['distance_drop_off_to_base'].'</td>
						</tr>						
						<tr>
							<td>'.esc_html__('Ride duration','chauffeur-booking-system').'</td>
							<td>'.$html['duration'].'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Passengers','chauffeur-booking-system').'</td>
							<td>'.$html['passenger'].'</td>
						</tr>
					</table>
				';

			break;
		
			case 'price':
				
				$Length=new CHBSLength();

				echo 
				'
					<table class="to-table-post-list">
						<tr>
							<td>'.esc_html__('Price source','chauffeur-booking-system').'</td>
							<td>'.$this->getPriceSourceTypeName($meta['price_source_type']).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Booking sum type','chauffeur-booking-system').'</td>
							<td>'.$PriceType->getPriceTypeName($meta['price_type']).'</td>
						</tr>  
				';
				
				if((int)$meta['price_type']===2)
				{
					echo
					'
						<tr>
							<td>'.esc_html__('Fixed price','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_fixed_value'],$meta['price_fixed_alter_type_id']).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Fixed price (return)','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_fixed_return_value'],$meta['price_fixed_return_alter_type_id']).'</td>
						</tr>  
						<tr>
							<td>'.esc_html__('Fixed price (return, new ride)','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_fixed_return_new_ride_value'],$meta['price_fixed_return_new_ride_alter_type_id']).'</td>
						</tr>  
					';
				}
				else
				{
					echo
					'
						<tr>
							<td>'.__('Initial fee','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_initial_value'],$meta['price_initial_alter_type_id']).'</td>
						</tr>
						<tr>
							<td>'.__('Initial fee (return)','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_initial_return_value'],$meta['price_initial_return_alter_type_id']).'</td>
						</tr>
						<tr>
							<td>'.__('Initial fee (return, new ride)','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_initial_return_new_ride_value'],$meta['price_initial_return_new_ride_alter_type_id']).'</td>
						</tr>
						<tr>
							<td>'.__('Delivery fee','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_delivery_value'],$meta['price_delivery_alter_type_id']).'</td>
						</tr>
						<tr>
							<td>'.__('Delivery (return) fee','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_delivery_return_value'],$meta['price_delivery_return_alter_type_id']).'</td>
						</tr>
						<tr>
							<td>'.$Length->label(CHBSOption::getOption('length_unit'),1).'</td>
							<td>'.self::displayPriceAlter($meta['price_distance_value'],$meta['price_distance_alter_type_id']).'</td>
						</tr>
						<tr>
							<td>'.$Length->label(CHBSOption::getOption('length_unit'),4).'</td>
							<td>'.self::displayPriceAlter($meta['price_distance_return_value'],$meta['price_distance_return_alter_type_id']).'</td>
						</tr>
						<tr>
							<td>'.$Length->label(CHBSOption::getOption('length_unit'),5).'</td>
							<td>'.self::displayPriceAlter($meta['price_distance_return_new_ride_value'],$meta['price_distance_return_new_ride_alter_type_id']).'</td>
						</tr>
						<tr>
							<td>'.__('Price per hour','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_hour_value'],$meta['price_hour_alter_type_id']).'</td>
						</tr>
						<tr>
							<td>'.__('Price per hour (return)','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_hour_return_value'],$meta['price_hour_return_alter_type_id']).'</td>
						</tr>
						<tr>
							<td>'.__('Price per hour (return, new ride)','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_hour_return_new_ride_value'],$meta['price_hour_return_new_ride_alter_type_id']).'</td>
						</tr>
					';
				}
				
				echo
				'
					<tr>
						<td>'.__('Price per extra time','chauffeur-booking-system').'</td>
						<td>'.self::displayPriceAlter($meta['price_extra_time_value'],$meta['price_extra_time_alter_type_id']).'</td>
					</tr>	
					<tr>
						<td>'.__('Price per waypoint','chauffeur-booking-system').'</td>
						<td>'.self::displayPriceAlter($meta['price_waypoint_value'],$meta['price_waypoint_alter_type_id']).'</td>
					</tr>
					<tr>
						<td>'.__('Price per waypoint duration','chauffeur-booking-system').'</td>
						<td>'.self::displayPriceAlter($meta['price_waypoint_duration_value'],$meta['price_waypoint_duration_alter_type_id']).'</td>
					</tr>
				';
				
				if((int)$meta['price_type']===1)
				{
					echo
					'
						<tr>
							<td>'.__('Price per adult','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_passenger_adult_value'],$meta['price_passenger_adult_alter_type_id']).'</td>
						</tr>
						<tr>
							<td>'.__('Price per child','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_passenger_children_value'],$meta['price_passenger_children_alter_type_id']).'</td>
						</tr>
					';
				}
				
				echo
				'
						<tr>
							<td>'.__('PayPal flat fee','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_payment_paypal_fixed_value'],$meta['price_payment_paypal_fixed_alter_type_id'],'payment_paypal_fixed').'</td>
						</tr>
						<tr>
							<td>'.__('PayPal percentage fee','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_payment_paypal_percentage_value'],$meta['price_payment_paypal_percentage_alter_type_id'],'payment_paypal_percentage').'</td>
						</tr>					
						<tr>
							<td>'.__('Stripe flat fee','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_payment_stripe_fixed_value'],$meta['price_payment_stripe_fixed_alter_type_id'],'payment_stripe_fixed').'</td>
						</tr>
						<tr>
							<td>'.__('Stripe percentage fee','chauffeur-booking-system').'</td>
							<td>'.self::displayPriceAlter($meta['price_payment_stripe_percentage_value'],$meta['price_payment_stripe_percentage_alter_type_id'],'payment_stripe_percentage').'</td>
						</tr>	
						<tr>
							<td>'.__('Minimum order value','chauffeur-booking-system').'</td>
							<td>'.CHBSPrice::format($meta['minimum_order_value'],CHBSOption::getOption('currency')).'</td>
						</tr>
						<tr>
							<td>'.__('Level','chauffeur-booking-system').'</td>
							<td>'.esc_html($meta['rule_level']).'</td>
						</tr>
						<tr>
							<td>'.__('Priority','chauffeur-booking-system').'</td>
							<td>'.(int)$post->menu_order.'</td>
						</tr>
						<tr>
							<td>'.__('Next rule processing','chauffeur-booking-system').'</td>
							<td>'.((int)$meta['process_next_rule_enable']===1 ? esc_html__('Enable','chauffeur-booking-system') : esc_html__('Disable','chauffeur-booking-system')).'</td>
						</tr>
					</table>
				';
				
			break;
		}
	}
	
	/**************************************************************************/
	
	static function displayPriceAlter($price,$priceAlterTypeId,$priceUseType=null)
	{
		$charBefore=null;
		
		if(in_array($priceAlterTypeId,array(3,5)))
			$charBefore='+ ';
		if(in_array($priceAlterTypeId,array(4,6)))
			$charBefore='- ';	 
		
		if(in_array($priceAlterTypeId,array(1)))
		{
			return(__('Inherited','chauffeur-booking-system'));
		}
		elseif(in_array($priceAlterTypeId,array(2)))
		{
			if(in_array($priceUseType,array('payment_paypal_percentage','payment_stripe_percentage')))
				return($price.'%');
			return(CHBSPrice::format($price,CHBSOption::getOption('currency')));
		}
		elseif(in_array($priceAlterTypeId,array(3,4)))
		{
			if(in_array($priceUseType,array('payment_paypal_percentage','payment_stripe_percentage')))
				return($price.'%');
			return($charBefore.CHBSPrice::format($price,CHBSOption::getOption('currency')));
		}
		elseif(in_array($priceAlterTypeId,array(5,6)))
		{
			return($charBefore.$price.'%');
		}
	}
	
	/**************************************************************************/
	
	function manageEditSortableColumns($column)
	{
		return($column);	   
	}
	
	/**************************************************************************/
	
	function getPriceFromRule($bookingData,$bookingForm,$priceRule=array())
	{
		/* init */
		
		$Date=new CHBSDate();
		$GeoCoding=new CHBSGeoCoding();
		$Validation=new CHBSValidation();
		$GeofenceChecker=new CHBSGeofenceChecker();

		if(!array_key_exists('price_type',$priceRule))
			$priceRule['price_type']=1;
		
		if(!array_key_exists('custom_vehicle_selection_enable',$priceRule))
			$priceRule['custom_vehicle_selection_enable']=0;		
		if(!array_key_exists('custom_vehicle_selection_button_url_address',$priceRule))
			$priceRule['custom_vehicle_selection_button_url_address']='';
		if(!array_key_exists('custom_vehicle_selection_button_label',$priceRule))
			$priceRule['custom_vehicle_selection_button_label']=__('Select','chauffeur-booking-system');
		if(!array_key_exists('custom_vehicle_selection_button_url_target',$priceRule))
			$priceRule['custom_vehicle_selection_button_url_target']=1;
		if(!array_key_exists('custom_vehicle_selection_first_step_redirect',$priceRule))
			$priceRule['custom_vehicle_selection_first_step_redirect']=0;
		if(!array_key_exists('custom_vehicle_selection_hide_price',$priceRule))
			$priceRule['custom_vehicle_selection_hide_price']=1;		
		
		if(!array_key_exists('minimum_order_value',$priceRule))
			$priceRule['minimum_order_value']=0;
		
		/* get rule */
		
		$rule=$bookingForm['dictionary']['price_rule'];
		if($rule===false) return($priceRule);

		/* process rule */
        
        $ruleLevel=9999;
        $processNextRuleEnable=-1;
		
		foreach($rule as $ruleData)
		{
			$sum=0;
			
			$pricePerHour=-1;
			$pricePerDistance=-1;
			$pricePerDelivery=-1;
			$pricePerDeliveryReturn=-1;
            
            if($ruleLevel!=$ruleData['meta']['rule_level'])
            {
                $ruleLevel=$ruleData['meta']['rule_level'];
                $processNextRuleEnable=-1;               
            }
            else if($processNextRuleEnable===0) continue;
			
			if(!in_array(-1,$ruleData['meta']['booking_form_id']))
			{
				if(!in_array($bookingData['booking_form_id'],$ruleData['meta']['booking_form_id'])) continue;
			}
			
			if(!in_array(-1,$ruleData['meta']['service_type_id']))
			{
				if(!in_array($bookingData['service_type_id'],$ruleData['meta']['service_type_id'])) continue;
			}  
			
			if(in_array($bookingData['service_type_id'],array(1,3)))
			{
				if(!in_array(-1,$ruleData['meta']['transfer_type_id']))
				{
					if(!in_array($bookingData['transfer_type_id'],$ruleData['meta']['transfer_type_id'])) continue;
				}
			}	 
			
			if($bookingData['service_type_id']==3)
			{
				if(!in_array(-1,$ruleData['meta']['route_id']))
				{
					if(!in_array($bookingData['route_id'],$ruleData['meta']['route_id'])) continue;
				}
			}
			
			if(!in_array(-1,$ruleData['meta']['vehicle_id']))
			{
				if(!in_array($bookingData['vehicle_id'],$ruleData['meta']['vehicle_id'])) continue;
			} 
			
			if(!in_array(-1,$ruleData['meta']['vehicle_company_id']))
			{
				$Vehicle=new CHBSVehicle();
				$vehicleDictionary=$Vehicle->getDictionary(array('vehicle_id'=>$bookingData['vehicle_id']));

				if(count($vehicleDictionary)===1)
				{
					if(in_array($vehicleDictionary[$bookingData['vehicle_id']]['meta']['vehicle_company_id'],$ruleData['meta']['vehicle_company_id']))
					{
						
					}
					else continue;
				}
				else continue;
			}	
			
			/***/
			/***/
			
			$locationKey=array
			(
				array
				(
					array('pickup','pickup'),
					array('dropoff','dropoff'),
				),
				array
				(
					array('dropoff','pickup'),
					array('pickup','dropoff'),
				),				
			);
				
			/***/
			
			$locationInside=array(array(0,0),array(0,0));
			
			for($i=0;$i<=1;$i++)
			{
				if($Validation->isNotEmpty($ruleData['meta']['location_zip_code_'.$locationKey[0][$i][0]]))
				{
					$locationInside[0][$i]=-1;
					$location=json_decode($bookingData[$locationKey[0][$i][1].'_location_coordinate']);
					
					if($Validation->isNotEmpty($location->{'zip_code'}))
					{
						$code=$this->getZipCode($ruleData['meta']['location_zip_code_'.$locationKey[0][$i][0]]);
						if(in_array($location->{'zip_code'},$code)) $locationInside[0][$i]=1;
					}
				}
			}

			if((int)$ruleData['meta']['location_swap_enable']===1)
			{
				if(in_array(-1,$locationInside[0]))
				{
					for($i=0;$i<=1;$i++)
					{
						if($Validation->isNotEmpty($ruleData['meta']['location_zip_code_'.$locationKey[1][$i][0]]))
						{
							$locationInside[1][$i]=-1;
							$location=json_decode($bookingData[$locationKey[1][$i][1].'_location_coordinate']);

							if($Validation->isNotEmpty($location->{'zip_code'}))
							{
								$code=$this->getZipCode($ruleData['meta']['location_zip_code_'.$locationKey[1][$i][0]]);
								if(in_array($location->{'zip_code'},$code)) $locationInside[1][$i]=1;
							}
						}
					}					
				}
			}

			if((int)$ruleData['meta']['location_swap_enable']===1)
			{
				if((in_array(-1,$locationInside[0])) && (in_array(-1,$locationInside[1]))) continue;
			}
			else
			{
				if((in_array(-1,$locationInside[0]))) continue;
			}
			
			/***/

			$locationInside=array(array(0,0),array(0,0));
			
			for($i=0;$i<=1;$i++)
			{
				if(!in_array(-1,$ruleData['meta']['location_country_'.$locationKey[0][$i][0]]))
				{
					$locationInside[0][$i]=-1;
					if($Validation->isNotEmpty($bookingData[$locationKey[0][$i][1].'_location_coordinate']))
					{
						if(!is_null($document=json_decode($bookingData[$locationKey[0][$i][1].'_location_coordinate'])))
						{
							$country=$GeoCoding->getCountryCode($document->{'lat'},$document->{'lng'});
							if($country!==false)
							{
								if(in_array($country,$ruleData['meta']['location_country_'.$locationKey[0][$i][0]])) 
									$locationInside[0][$i]=1;
							}
						}
					}
				}
			}			
			
			if((int)$ruleData['meta']['location_swap_enable']===1)
			{
				if(in_array(-1,$locationInside[0]))
				{
					for($i=0;$i<=1;$i++)
					{
						if(!in_array(-1,$ruleData['meta']['location_country_'.$locationKey[1][$i][0]]))
						{
							$locationInside[1][$i]=-1;
							if($Validation->isNotEmpty($bookingData[$locationKey[1][$i][1].'_location_coordinate']))
							{
								if(!is_null($document=json_decode($bookingData[$locationKey[1][$i][1].'_location_coordinate'])))
								{
									$country=$GeoCoding->getCountryCode($document->{'lat'},$document->{'lng'});
									if($country!==false)
									{
										if(in_array($country,$ruleData['meta']['location_country_'.$locationKey[1][$i][0]])) 
											$locationInside[1][$i]=1;
									}
								}
							}
						}
					}						
				}			
			}
	
			if((int)$ruleData['meta']['location_swap_enable']===1)
			{
				if((in_array(-1,$locationInside[0])) && (in_array(-1,$locationInside[1]))) continue;
			}
			else
			{
				if((in_array(-1,$locationInside[0]))) continue;
			}

			/***/
			
			$locationInside=array(array(0,0),array(0,0));
			
			for($i=0;$i<=1;$i++)
			{
				if(!in_array(-1,$ruleData['meta']['location_fixed_'.$locationKey[0][$i][0]]))
				{
					$locationInside[0][$i]=-1;
					if(in_array($bookingData['fixed_location_'.$locationKey[0][$i][1]],$ruleData['meta']['location_fixed_'.$locationKey[0][$i][0]])) $locationInside[0][$i]=1;
				}
			}
			
			if((int)$ruleData['meta']['location_swap_enable']===1)
			{
				if(in_array(-1,$locationInside[0]))
				{
					for($i=0;$i<=1;$i++)
					{
						if(!in_array(-1,$ruleData['meta']['location_fixed_'.$locationKey[1][$i][0]]))
						{
							$locationInside[1][$i]=-1;
							if(in_array($bookingData['fixed_location_'.$locationKey[1][$i][1]],$ruleData['meta']['location_fixed_'.$locationKey[1][$i][0]])) $locationInside[1][$i]=1;
						}
					}					
				}
			}
			
			if((int)$ruleData['meta']['location_swap_enable']===1)
			{
				if((in_array(-1,$locationInside[0])) && (in_array(-1,$locationInside[1]))) continue;
			}
			else
			{
				if((in_array(-1,$locationInside[0]))) continue;
			}

			/***/
			
			$locationInside=array(array(0,0),array(0,0));
			
			for($i=0;$i<=1;$i++)
			{
				$locationInside[0][$i]=-1;
				if($GeofenceChecker->locationInGeofence($ruleData['meta']['location_geofence_'.$locationKey[0][$i][0]],$bookingForm['dictionary']['geofence'],$bookingData[$locationKey[0][$i][1].'_location_coordinate'])===true) $locationInside[0][$i]=1;
			}						
						
			if((int)$ruleData['meta']['location_swap_enable']===1)
			{
				if(in_array(-1,$locationInside[0]))
				{	
					for($i=0;$i<=1;$i++)
					{
						$locationInside[1][$i]=-1;
						if($GeofenceChecker->locationInGeofence($ruleData['meta']['location_geofence_'.$locationKey[1][$i][0]],$bookingForm['dictionary']['geofence'],$bookingData[$locationKey[1][$i][1].'_location_coordinate'])===true) $locationInside[1][$i]=1;
					}
				}
			}
	
			if((int)$ruleData['meta']['location_swap_enable']===1)
			{
				if((in_array(-1,$locationInside[0])) && (in_array(-1,$locationInside[1]))) continue;
			}
			else
			{
				if((in_array(-1,$locationInside[0]))) continue;
			}

			/***/
			/***/
			
			if(!in_array(-1,$ruleData['meta']['pickup_day_number']))
			{
				if(!in_array(date_i18n('N',CHBSDate::strtotime($bookingData['pickup_date'])),$ruleData['meta']['pickup_day_number'])) continue;
			}
	
			if(is_array($ruleData['meta']['pickup_date']))
			{
				if(count($ruleData['meta']['pickup_date']))
				{
					$match=!count($ruleData['meta']['pickup_date']);

					foreach($ruleData['meta']['pickup_date'] as $value)
					{
						if($Date->dateInRange($bookingData['pickup_date'],$value['start'],$value['stop']))
						{
							$match=true;
							break;
						}
					}

					if(!$match) continue;
				}
			}
			
			if(in_array($bookingData['service_type_id'],array(1,3)))
			{
				if(in_array($bookingData['transfer_type_id'],array(3)))
				{
					if(is_array($ruleData['meta']['return_date']))
					{
						if(count($ruleData['meta']['return_date']))
						{
							$match=!count($ruleData['meta']['return_date']);

							foreach($ruleData['meta']['return_date'] as $value)
							{
								if($Date->dateInRange($bookingData['return_date'],$value['start'],$value['stop']))
								{
									$match=true;
									break;
								}
							}

							if(!$match) continue;
						}
					}					
				}
			}
			
			if(is_array($ruleData['meta']['pickup_time']))
			{
				if(count($ruleData['meta']['pickup_time']))
				{
					$match=!count($ruleData['meta']['pickup_time']);

					foreach($ruleData['meta']['pickup_time'] as $value)
					{
						if($Date->timeInRange($bookingData['pickup_time'],$value['start'],$value['stop']))
						{
							$match=true;
							break;
						}
					}

					if(!$match) continue;
				}
			}
			
			if(in_array($bookingData['service_type_id'],array(1,3)))
			{
				if(in_array($bookingData['transfer_type_id'],array(3)))
				{
					if(is_array($ruleData['meta']['return_time']))
					{
						if(count($ruleData['meta']['return_time']))
						{
							$match=!count($ruleData['meta']['return_time']);

							foreach($ruleData['meta']['return_time'] as $value)
							{
								if($Date->timeInRange($bookingData['return_time'],$value['start'],$value['stop']))
								{
									$match=true;
									break;
								}
							}

							if(!$match) continue;
						}
					}					
				}	
			}
			
			if(in_array($bookingData['transfer_type_id'],array(3)))
			{
				if($Validation->isNotEmpty($ruleData['meta']['pickup_return_date_difference']))
				{
					$difference=$Date->getDateDifferenceInDay($bookingData['return_date'],$bookingData['pickup_date']);
					if($difference!==(int)$ruleData['meta']['pickup_return_date_difference']) continue;
				}
			}
			
			if(in_array($bookingData['service_type_id'],array(1,3)))
			{
				if(is_array($ruleData['meta']['distance']))
				{
					$match=!count($ruleData['meta']['distance']);

					$key=(int)CHBSOption::getOption('pricing_rule_return_use_type')==2 ? 'distance' : 'distance_sum';
					
					$bookingData[$key]=round($bookingData[$key],1);
					
					if((int)$ruleData['meta']['price_source_type']===2)
					{
						$match=true;
						
						$sum=0;
						$pricePerDistance=0;
							
						if($bookingData[$key]>0)
						{
							for($i=0;$i<$bookingData[$key];$i+=0.1)
							{
								$i=round($i,1);
								foreach($ruleData['meta']['distance'] as $value)
								{
									$value['stop']=round($value['stop'],1);
									$value['start']=round($value['start'],1);
									
									if(($value['start']<=$i) && ($value['stop']>$i))
									{
										$priceTemp=$this->getAlterPrice($priceRule['price_distance_value'],$value['price'],$value['price_alter_type_id']);
										$sum+=($priceTemp/10);
									}
								}		
							}
							
							if($bookingData[$key]>0)
								$pricePerDistance=$sum/$bookingData[$key];
						}
					}
					if((int)$ruleData['meta']['price_source_type']===3)
					{
						foreach($ruleData['meta']['distance'] as $value)
						{
							$value['stop']=round($value['stop'],1);
							$value['start']=round($value['start'],1);
							
							if(($value['start']<=$bookingData[$key]) && ($value['stop']>$bookingData[$key]))
							{
								$match=true;
								$priceTemp=$this->getAlterPrice($priceRule['price_distance_value'],$value['price'],$value['price_alter_type_id']);
								$pricePerDistance=$priceTemp;
								break;
							}
						}		
					}
					else
					{
						foreach($ruleData['meta']['distance'] as $value)
						{
							$value['stop']=round($value['stop'],1);
							$value['start']=round($value['start'],1);
							
							if(($value['start']<=$bookingData[$key]) && ($value['stop']>$bookingData[$key]))
							{
								$match=true;
								break;						
							}
						}
					}

					if(!$match) continue;
				}
			}
			
			/***/
			
			if(is_array($ruleData['meta']['distance_base_to_pickup']))
			{
				$match=!count($ruleData['meta']['distance_base_to_pickup']);

				if((int)$ruleData['meta']['price_source_type']===4)
				{
					$sum=0;
					$match=true;
					$pricePerDelivery=0;
					
					$bookingData['base_location_distance']=round($bookingData['base_location_distance'],1);

					if($bookingData['base_location_distance']>0)
					{
						for($i=0;$i<$bookingData['base_location_distance'];$i+=0.1)
						{
							$i=round($i,1);
							foreach($ruleData['meta']['distance_base_to_pickup'] as $value)
							{
								$value['stop']=round($value['stop'],1);
								$value['start']=round($value['start'],1);
								
								if(($value['start']<=$i) && ($value['stop']>$i))
								{
									$sum+=($value['price']/10);
								}
							}		
						}

						if($bookingData['base_location_distance']>0)
							$pricePerDelivery=$sum/$bookingData['base_location_distance'];
					}
				}
				if((int)$ruleData['meta']['price_source_type']===5)
				{
					foreach($ruleData['meta']['distance_base_to_pickup'] as $value)
					{
						$value['stop']=round($value['stop'],1);
						$value['start']=round($value['start'],1);
						
						if(($value['start']<=$bookingData['base_location_distance']) && ($value['stop']>$bookingData['base_location_distance']))
						{
							$match=true;
							$pricePerDelivery=$value['price'];
							break;
						}
					}		
				}
				else
				{
					foreach($ruleData['meta']['distance_base_to_pickup'] as $value)
					{
						$value['stop']=round($value['stop'],1);
						$value['start']=round($value['start'],1);
						
						if(($value['start']<=$bookingData['base_location_distance']) && ($value['stop']>$bookingData['base_location_distance']))
						{
							$match=true;
							break;						
						}
					}
				}

				if(!$match) continue;
			}	

			/***/
			
			if(is_array($ruleData['meta']['distance_drop_off_to_base']))
			{
				$match=!count($ruleData['meta']['distance_drop_off_to_base']);
				
				if((int)$ruleData['meta']['price_source_type']===8)
				{
					$sum=0;
					$match=true;
					$pricePerDelivery=0;

					$bookingData['base_location_return_distance']=round($bookingData['base_location_return_distance'],1);
					
					if($bookingData['base_location_return_distance']>0)
					{
						for($i=0;$i<$bookingData['base_location_return_distance'];$i+=0.1)
						{
							$i=round($i,1);
							foreach($ruleData['meta']['distance_drop_off_to_base'] as $value)
							{
								$value['stop']=round($value['stop'],1);
								$value['start']=round($value['start'],1);
								
								if(($value['start']<=$i) && ($value['stop']>$i))
								{
									$sum+=($value['price']/10);
								}
							}		
						}

						if($bookingData['base_location_return_distance']>0)
							$pricePerDeliveryReturn=$sum/$bookingData['base_location_return_distance'];
					}
				}
				if((int)$ruleData['meta']['price_source_type']===9)
				{
					foreach($ruleData['meta']['distance_drop_off_to_base'] as $value)
					{
						$value['stop']=round($value['stop'],1);
						$value['start']=round($value['start'],1);
						
						if(($value['start']<=$bookingData['base_location_return_distance']) && ($value['stop']>$bookingData['base_location_return_distance']))
						{
							$match=true;
							$pricePerDeliveryReturn=$value['price'];
							break;
						}
					}		
				}
				else
				{
					foreach($ruleData['meta']['distance_drop_off_to_base'] as $value)
					{
						$value['stop']=round($value['stop'],1);
						$value['start']=round($value['start'],1);
							
						if(($value['start']<=$bookingData['base_location_return_distance']) && ($value['stop']>$bookingData['base_location_return_distance']))
						{
							$match=true;
							break;						
						}
					}
				}

				if(!$match) continue;
			}	
			
			/***/
			
			if(is_array($ruleData['meta']['duration']))
			{
				$match=!count($ruleData['meta']['duration']);
				
				if(in_array($bookingData['service_type_id'],array(2))) $key='duration_sum';
				else $key=(int)CHBSOption::getOption('pricing_rule_return_use_type')==2 ? 'duration_map' : 'duration_sum';

				$bookingDuration=$bookingData[$key];
				
				if((int)$ruleData['meta']['price_source_type']===6)
				{
					$sum=0;
					$match=true;
					$pricePerHour=0;
			
					for($i=0;$i<$bookingDuration;$i++)
					{
						foreach($ruleData['meta']['duration'] as $value)
						{
							$startHourToMinute=CHBSDate::convertTimeDurationToMinute($value['start']);
							$stopHourToMinute=CHBSDate::convertTimeDurationToMinute($value['stop']); 
							
							if(($startHourToMinute<=$i) && ($stopHourToMinute>$i))
							{
								$sum+=($value['price']/60);
							}
						}		
					}
					
					if($bookingDuration>0) $pricePerHour=($sum/$bookingDuration)*60;
				}				
				if((int)$ruleData['meta']['price_source_type']===7)
				{	
					foreach($ruleData['meta']['duration'] as $value)
					{
						$startHourToMinute=CHBSDate::convertTimeDurationToMinute($value['start']);
						$stopHourToMinute=CHBSDate::convertTimeDurationToMinute($value['stop']); 
			
						if(($startHourToMinute<=$bookingDuration) && ($bookingDuration<$stopHourToMinute))
						{
							$match=true;
							$pricePerHour=$value['price'];
							break;						
						}
					}						
				}			
				else
				{
					foreach($ruleData['meta']['duration'] as $value)
					{
						$startHourToMinute=CHBSDate::convertTimeDurationToMinute($value['start']);
						$stopHourToMinute=CHBSDate::convertTimeDurationToMinute($value['stop']); 
			
						if(($startHourToMinute<=$bookingDuration) && ($bookingDuration<$stopHourToMinute))
						{
							$match=true;
							break;						
						}
					}					
				}
				
				if(!$match) continue;
			} 
			
			/***/
			
			if((CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$bookingData['service_type_id'],'adult')) || (CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$bookingData['service_type_id'],'children')))
			{
				if(is_array($ruleData['meta']['passenger']))
				{
					if(count($ruleData['meta']['passenger']))
					{
						$match=!count($ruleData['meta']['passenger']);
						foreach($ruleData['meta']['passenger'] as $value)
						{
							if(($value['start']<=$bookingData['passenger_sum']) && ($bookingData['passenger_sum']<=$value['stop']))
							{
								$match=true;
								break;						
							}
						}

						if(!$match) continue;
					}
				}
			}
			
			if($pricePerDistance!=-1)
			{
				$priceRule['price_distance_value']=$priceRule['price_distance_return_value']=$priceRule['price_distance_return_new_ride_value']=$pricePerDistance;
				$pricePerDistance=-1;
			}	
			elseif($pricePerHour!=-1)
			{
				$priceRule['price_hour_value']=$priceRule['price_hour_return_value']=$priceRule['price_hour_return_new_ride_value']=$pricePerHour;
				$pricePerHour=-1;
			}		
			elseif($pricePerDelivery!=-1)
			{
				$priceRule['price_delivery_value']=$pricePerDelivery;
				$pricePerDelivery=-1;				
			}
			elseif($pricePerDeliveryReturn!=-1)
			{
				$priceRule['price_delivery_return_value']=$pricePerDeliveryReturn;
				$pricePerDeliveryReturn=-1;				
			}			
			else
			{
				foreach($this->getPriceUseType() as $index=>$value)
				{
					if((int)$ruleData['meta']['price_'.$index.'_alter_type_id']===2)
					{
						$priceRule['price_'.$index.'_value']=$ruleData['meta']['price_'.$index.'_value'];
					}
					elseif(in_array((int)$ruleData['meta']['price_'.$index.'_alter_type_id'],array(3,4))) 
					{
						if((int)$ruleData['meta']['price_'.$index.'_alter_type_id']===3)
							$priceRule['price_'.$index.'_value']+=$ruleData['meta']['price_'.$index.'_value'];
						if((int)$ruleData['meta']['price_'.$index.'_alter_type_id']===4)
							$priceRule['price_'.$index.'_value']-=$ruleData['meta']['price_'.$index.'_value'];
					}
					elseif(in_array((int)$ruleData['meta']['price_'.$index.'_alter_type_id'],array(5,6)))
					{
						if((int)$ruleData['meta']['price_'.$index.'_alter_type_id']===5)
						{
							$priceRule['price_'.$index.'_value']=$priceRule['price_'.$index.'_value']*(1+$ruleData['meta']['price_'.$index.'_value']/100); 
						}
						elseif((int)$ruleData['meta']['price_'.$index.'_alter_type_id']===6)
						{
							$priceRule['price_'.$index.'_value']=$priceRule['price_'.$index.'_value']*(1-$ruleData['meta']['price_'.$index.'_value']/100); 
						}
					}
					
					if(!in_array($index,array('payment_paypal_percentage','payment_paypal_fixed','payment_stripe_percentage','payment_stripe_fixed')))
					{
						if($priceRule['price_'.$index.'_value']<0)
							$priceRule['price_'.$index.'_value']=0;
					}
				}
			}
			
			foreach($this->getPriceUseType() as $index=>$value)
			{
				if((int)$value['use_tax']===1)
				{
					if((int)$ruleData['meta']['price_'.$index.'_tax_rate_id']!==-1)
						$priceRule['price_'.$index.'_tax_rate_id']=$ruleData['meta']['price_'.$index.'_tax_rate_id'];
				}
			}
			
			$priceRule['price_type']=$ruleData['meta']['price_type'];
			$priceRule['minimum_order_value']=$ruleData['meta']['minimum_order_value'];

			$priceRule['custom_vehicle_selection_enable']=$ruleData['meta']['custom_vehicle_selection_enable'];
			$priceRule['custom_vehicle_selection_button_url_address']=$ruleData['meta']['custom_vehicle_selection_button_url_address'];
			$priceRule['custom_vehicle_selection_button_label']=$ruleData['meta']['custom_vehicle_selection_button_label'];
			$priceRule['custom_vehicle_selection_button_url_target']=$ruleData['meta']['custom_vehicle_selection_button_url_target'];
			$priceRule['custom_vehicle_selection_first_step_redirect']=$ruleData['meta']['custom_vehicle_selection_first_step_redirect'];
			$priceRule['custom_vehicle_selection_hide_price']=$ruleData['meta']['custom_vehicle_selection_hide_price'];	
			
			if((int)$ruleData['meta']['process_next_rule_enable']===1)
            {
				$processNextRuleEnable=1;
            }
            else $processNextRuleEnable=0;  
		}

		return($priceRule);
	}
	
	/**************************************************************************/
	
	function getAlterPrice($price,$value,$alterTypeId)
	{
		if((int)$alterTypeId===2)
		{
			$price=$value;
		}
		elseif(in_array((int)$alterTypeId,array(3,4))) 
		{
			if((int)$alterTypeId===3)
				$price+=$value;
			if((int)$alterTypeId===4)
				$price-=$value;
		}
		elseif(in_array((int)$alterTypeId,array(5,6)))
		{
			if((int)$alterTypeId===5)
			{
				$price=$price*(1+$value/100); 
			}
			elseif((int)$alterTypeId===6)
				$price=$price*(1-$value/100); 
		}

		return($price);
	}
	
	/**************************************************************************/
	
	function getDictionary($attr=array(),$formatPrice=false)
	{
		global $post;
        
        $Validation=new CHBSValidation();
		
		$dictionary=array();
		
		$default=array
		(
			'price_rule_id'=>0
		);
		
		$attribute=shortcode_atts($default,$attr);
		CHBSHelper::preservePost($post,$bPost);
		
		$argument=array
		(
			'post_type'=>self::getCPTName(),
			'post_status'=>'publish',
			'posts_per_page'=>-1,
			'orderby'=>array('menu_order'=>'desc')
		);
		
		if($attribute['price_rule_id'])
			$argument['p']=$attribute['price_rule_id'];
			   
		$query=new WP_Query($argument);
		if($query===false) return($dictionary);
		
		while($query->have_posts())
		{
			$query->the_post();
			$dictionary[$post->ID]['post']=$post;
			$dictionary[$post->ID]['meta']=CHBSPostMeta::getPostMeta($post);
		}
		
		/***/
		
		if($formatPrice)
		{
			CHBSPrice::numberFormatDictionary($dictionary);
		}
        
        /***/
        
        $rule=array();
        $order=array();
        
        foreach($dictionary as $index=>$value)
        {
            if($Validation->isEmpty($value['meta']['rule_level']))
            {
                $order[9999][]=$index;
            }
            else 
            {
                $order[(int)$value['meta']['rule_level']][]=$index;
            }
        }
        
        krsort($order);
        
        /***/
        
        foreach($order as $orderValue)
        {
            foreach($orderValue as $value)
            {
                $rule[$value]=$dictionary[$value];
            }
        }
        
        /***/
		
		CHBSHelper::preservePost($post,$bPost,0);	
		
		return($rule);		
	}
	
	/**************************************************************************/
	
	function getZipCode($zipCode)
	{
		$Validation=new CHBSValidation();
		
		$zipCodeResult=array();
		
		$zipCode=preg_split('/;/',$zipCode);
		
		foreach($zipCode as $zipCodeValue)
		{
			list($min,$max)=preg_split('/\:/',$zipCodeValue);
			
			if(($Validation->isNumber($min,1,999999999)) && ($Validation->isNumber($max,1,999999999)))
			{
				for($i=$min;$i<=$max;$i++)
					$zipCodeResult[]=$i;
			}
			else $zipCodeResult[]=$zipCodeValue;
		}
		
		return($zipCodeResult);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/