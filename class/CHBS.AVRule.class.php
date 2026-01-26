<?php

/******************************************************************************/
/******************************************************************************/

class CHBSAVRule
{
	/**************************************************************************/
	
	function __construct()
	{

	}
	
	/**************************************************************************/	
	
	public function init()
	{
		$this->registerCPT();
	}
	
	/**************************************************************************/

	public static function getCPTName()
	{
		return(PLUGIN_CHBS_CONTEXT.'_av_rule');
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
					'name'=>__('Availability Rules','chauffeur-booking-system'),
					'singular_name'=>__('Availability Rule','chauffeur-booking-system'),
					'add_new'=>__('Add New','chauffeur-booking-system'),
					'add_new_item'=>__('Add New Availability Rule','chauffeur-booking-system'),
					'edit_item'=>__('Edit Availability Rule','chauffeur-booking-system'),
					'new_item'=>__('New Availability Rule','chauffeur-booking-system'),
					'all_items'=>__('Availability Rules','chauffeur-booking-system'),
					'view_item'=>__('View Availability Rule','chauffeur-booking-system'),
					'search_items'=>__('Search Availability Rules','chauffeur-booking-system'),
					'not_found'=>__('No Availability Rules Found','chauffeur-booking-system'),
					'not_found_in_trash'=>__('No Availability Rules in Trash','chauffeur-booking-system'), 
					'parent_item_colon'=>'',
					'menu_name'=>__('Availability Rules','chauffeur-booking-system')
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
		add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_av_rule',array($this,'adminCreateMetaBoxClass'));
		
		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns')); 
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));
	}

	/**************************************************************************/
	
	function addMetaBox()
	{
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_av_rule',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');		
	}
	
	/**************************************************************************/
	
	function addMetaBoxMain()
	{
		global $post;
		
		$data=array();
		
		$Payment=new CHBSPayment();
		$Vehicle=new CHBSVehicle();
		$Location=new CHBSLocation();
		$Geofence=new CHBSGeofence();
		$ServiceType=new CHBSServiceType();
		$BookingForm=new CHBSBookingForm();
		$BookingExtra=new CHBSBookingExtra();
		
		$data['meta']=CHBSPostMeta::getPostMeta($post);
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_av_rule');

		$data['dictionary']['payment']=$Payment->getPayment();
		$data['dictionary']['location']=$Location->getDictionary();
		$data['dictionary']['geofence']=$Geofence->getDictionary();
		$data['dictionary']['booking_form']=$BookingForm->getDictionary();
		$data['dictionary']['service_type']=$ServiceType->getServiceType();

		$data['dictionary']['vehicle']=$Vehicle->getDictionary(array(),5);
		$data['dictionary']['booking_extra']=$BookingExtra->getDictionary(array(),5);
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_av_rule.php');
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
		CHBSHelper::setDefault($meta,'booking_form_id',array(-1));
		CHBSHelper::setDefault($meta,'service_type_id',array(-1));
		
		CHBSHelper::setDefault($meta,'location_fixed_pickup',array(-1));
		CHBSHelper::setDefault($meta,'location_fixed_dropoff',array(-1));
		
		CHBSHelper::setDefault($meta,'location_geofence_pickup',array(-1));
		CHBSHelper::setDefault($meta,'location_geofence_dropoff',array(-1));
		
		CHBSHelper::setDefault($meta,'process_next_rule_enable',0);
		
		CHBSHelper::setDefault($meta,'vehicle',array());
		CHBSHelper::setDefault($meta,'booking_extra',array());
		CHBSHelper::setDefault($meta,'payment',array());
		
		CHBSHelper::setDefault($meta,'booking_period_from','');
		CHBSHelper::setDefault($meta,'booking_period_to','');
		CHBSHelper::setDefault($meta,'booking_period_type',1);
		
		CHBSHelper::setDefault($meta,'minimum_order_value',CHBSPrice::getDefaultPrice());
		CHBSHelper::setDefault($meta,'minimum_order_error_message','');
		
		$Vehicle=new CHBSVehicle();
		$dictionary=$Vehicle->getDictionary();
		foreach($dictionary as $index=>$value)
		{
			if(!isset($meta['vehicle'][$index]))
				$meta['vehicle'][$index]['availability']=-1;
		}
		
		$BookingExtra=new CHBSBookingExtra();
		$dictionary=$BookingExtra->getDictionary();
		foreach($dictionary as $index=>$value)
		{
			if(!isset($meta['booking_extra'][$index]))
				$meta['booking_extra'][$index]['availability']=-1;
		}
		
		$Payment=new CHBSPayment();
		$dictionary=$Payment->getPayment();
		foreach($dictionary as $index=>$value)
		{
			if(!isset($meta['payment'][$index]))
				$meta['payment'][$index]['availability']=-1;
		}		
	}
	
	/**************************************************************************/
	
	function savePost($postId)
	{	  
		if(!$_POST) return(false);
		
		if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_av_rule_noncename','savePost')===false) return(false);
		
		$option=CHBSHelper::getPostOption();		
	
		$Date=new CHBSDate();
		$Vehicle=new CHBSVehicle();
		$Payment=new CHBSPayment();
		$Geofence=new CHBSGeofence();
		$Location=new CHBSLocation();
		$Validation=new CHBSValidation();
		$ServiceType=new CHBSServiceType();
		$BookingForm=new CHBSBookingForm();
		$BookingExtra=new CHBSBookingExtra();
		
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
			'location_fixed_pickup'=>array
			(
				'dictionary'=>$Location->getDictionary()
			),
			'location_fixed_dropoff'=>array
			(
				'dictionary'=>$Location->getDictionary()
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

			if(!count($option[$dIndex]))
				$option[$dIndex]=array(-1);			
		}
		
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
		
		if(!$Validation->isBool($option['process_next_rule_enable']))
			$option['process_next_rule_enable']=0;		
		
		/***/
		
		$vehicle=array();
		$dictionary=$Vehicle->getDictionary();
		
		foreach($dictionary as $index=>$value)
		{
			$availability=$option['vehicle'][$index]['availability'];
			if(!in_array($availability,array(-1,0,1))) $availability=-1;
			
			$vehicle[$index]['availability']=$availability;
		}
		
		$option['vehicle']=$vehicle;
		
		/***/
		
		$bookingExtra=array();
		$dictionary=$BookingExtra->getDictionary();
		
		foreach($dictionary as $index=>$value)
		{
			$availability=$option['booking_extra'][$index]['availability'];
			if(!in_array($availability,array(-1,0,1))) $availability=-1;
			
			$bookingExtra[$index]['availability']=$availability;
		}
		
		$option['booking_extra']=$bookingExtra;
		
		/***/
		
		$payment=array();
		$dictionary=$Payment->getPayment();
		
		foreach($dictionary as $index=>$value)
		{
			$availability=$option['payment'][$index]['availability'];
			if(!in_array($availability,array(-1,0,1))) $availability=-1;
			
			$payment[$index]['availability']=$availability;
		}
		
		$option['payment']=$payment;
		
		/***/
		
		if(!$Validation->isNumber($option['booking_period_from'],0,9999))
			$option['booking_period_from']='';		  
		if(!$Validation->isNumber($option['booking_period_to'],0,9999))
			$option['booking_period_to']='';  
		if(!in_array($option['booking_period_type'],array(1,2,3)))
			$option['booking_period_type']=1;	
		
		/***/
		
		if(!CHBSPrice::isPrice($option['minimum_order_value'],false))
		   $option['minimum_order_value']=0.00;
		
		$option['minimum_order_value']=CHBSPrice::formatToSave($option['minimum_order_value'],true);
		
		/***/
		
		$key=array
		(
			'booking_form_id',
			'service_type_id',
			'location_fixed_pickup',
			'location_fixed_dropoff',
			'location_geofence_pickup',
			'location_geofence_dropoff',
			'pickup_date',
			'return_date',
			'pickup_time',
			'return_time',
			'process_next_rule_enable',
			'vehicle',
			'booking_extra',
			'payment',
			'booking_period_from',
			'booking_period_to',
			'booking_period_type',
			'minimum_order_value',
			'minimum_order_error_message'
		);
		
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
			'vehicle'=>__('Vehicles','chauffeur-booking-system'),
			'booking_extra'=>__('Booking extras','chauffeur-booking-system'),
			'payment'=>__('Payments','chauffeur-booking-system'),
			'other'=>__('Other','chauffeur-booking-system'),
		);
   
		return($column);		   
	}
	
	/**************************************************************************/
	
	function getPricingRuleAdminListDictionary()
	{
		$dictionary=array();
	
		$Location=new CHBSLocation();
		$Geofence=new CHBSGeofence();
		$ServiceType=new CHBSServiceType();
		$BookingForm=new CHBSBookingForm();
		
		$dictionary['location']=$Location->getDictionary();
		$dictionary['geofence']=$Geofence->getDictionary();
		$dictionary['booking_form']=$BookingForm->getDictionary();
		$dictionary['service_type']=$ServiceType->getServiceType();

		return($dictionary);
	}
	
	/**************************************************************************/
	
	function displayPricingRuleAdminListValue($data,$dictionary,$link=false,$sort=false)
	{
		if(in_array(-1,$data)) return(__(' - ','chauffeur-booking-system'));
		
		$html=null;
		
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
			$html.='<div>'.$label.'</div>';
		}
		
		return($html);
	}
	
	/**************************************************************************/
	
	function managePostsCustomColumn($column)
	{
		global $post;
		
		$Date=new CHBSDate();
		$Vehicle=new CHBSVehicle();
		$Payment=new CHBSPayment();
		$Validation=new CHBSValidation();
		$BookingExtra=new CHBSBookingExtra();
		
		$meta=CHBSPostMeta::getPostMeta($post);
		
		$dictionary=CHBSGlobalData::setGlobalData('pricing_rule_admin_list_dictionary',array($this,'getPricingRuleAdminListDictionary'));
		
		/***/
		
		$availabilityTable=array
		(
			1=>array
			(
				'label'=>esc_html__('Available','chauffeur-booking-system'),
				'value'=>null
			),
			0=>array
			(
				'label'=>esc_html__('Unavailable','chauffeur-booking-system'),
				'value'=>null
			),
			-1=>array
			(
				'label'=>esc_html__('Not set','chauffeur-booking-system'),
				'value'=>null
			)					
		);
				
		/***/
		
		switch($column) 
		{
			case 'condition':
				
				$html=array
				(
					'pickup_date'=>'',
					'return_date'=>'',
					'pickup_time'=>'',
					'return_time'=>''
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
							<td>'.esc_html__('Fixed pickup locations','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['location_fixed_pickup'],$dictionary['location'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Fixed drop-off locations','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['location_fixed_dropoff'],$dictionary['location'],true,true).'</td>
						</tr>
						<tr>
							<td>'.esc_html__('Pickup geofence areas','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['location_geofence_pickup'],$dictionary['geofence'],true,true).'</td>
						</tr>						
						<tr>
							<td>'.esc_html__('Return geofence areas','chauffeur-booking-system').'</td>
							<td>'.$this->displayPricingRuleAdminListValue($meta['location_geofence_dropoff'],$dictionary['geofence'],true,true).'</td>
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
					</table>
				';

			break;
		
			case 'vehicle':
				
				$dictionary=$Vehicle->getDictionary();
				
				foreach($dictionary as $index=>$value)
				{
					$vehicle='<a href="'.get_edit_post_link($index).'">'.$value['post']->post_title.'</a>';
					
					if((!isset($meta['vehicle'])) || (!isset($meta['vehicle'][$index])) || (!isset($meta['vehicle'][$index]['availability']))) $availability=-1;
					else $availability=$meta['vehicle'][$index]['availability'];
					
					if($Validation->isNotEmpty($availabilityTable[$availability]['value']))
						$availabilityTable[$availability]['value'].=', ';
					
					$availabilityTable[$availability]['value'].=$vehicle;
				}
				
				foreach($availabilityTable as $index=>$value)
				{
					echo
					'
						<div>
							<div>'.$value['label'].':</div>
							<div>'.($Validation->isEmpty($value['value']) ? '' : $value['value']).'</div>
						</div><br/>  
					';
				}
				
			break;
		
			case 'booking_extra':
				
				$dictionary=$BookingExtra->getDictionary();
				
				foreach($dictionary as $index=>$value)
				{
					$bookingExtra='<a href="'.get_edit_post_link($index).'">'.$value['post']->post_title.'</a>';
					
					if((!isset($meta['booking_extra'])) || (!isset($meta['booking_extra'][$index])) || (!isset($meta['booking_extra'][$index]['availability']))) $availability=-1;
					else $availability=$meta['booking_extra'][$index]['availability'];
					
					if($Validation->isNotEmpty($availabilityTable[$availability]['value']))
						$availabilityTable[$availability]['value'].=', ';
					
					$availabilityTable[$availability]['value'].=$bookingExtra;
				}
				
				foreach($availabilityTable as $index=>$value)
				{
					echo
					'
						<div>
							<div>'.$value['label'].':</div>
							<div>'.($Validation->isEmpty($value['value']) ? '' : $value['value']).'</div>
						</div><br/>  
					';
				}
				
			break;
		
			case 'payment':
				
				$dictionary=$Payment->getPayment();
				
				foreach($dictionary as $index=>$value)
				{
					$payment=esc_html($value[0]);
					
					if((!isset($meta['payment'])) || (!isset($meta['payment'][$index])) || (!isset($meta['payment'][$index]['availability']))) $availability=-1;
					else $availability=$meta['payment'][$index]['availability'];
					
					if($Validation->isNotEmpty($availabilityTable[$availability]['value']))
						$availabilityTable[$availability]['value'].=', ';
					
					$availabilityTable[$availability]['value'].=$payment;
				}
				
				foreach($availabilityTable as $index=>$value)
				{
					echo
					'
						<div>
							<div>'.$value['label'].':</div>
							<div>'.($Validation->isEmpty($value['value']) ? '' : $value['value']).'</div>
						</div><br/>  
					';
				}

			break;
			
			case 'other':
				
				echo
				'
					<table class="to-table-post-list">
						<tr>
							<td>'.__('Order','chauffeur-booking-system').'</td>
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
	
	function manageEditSortableColumns($column)
	{
		return($column);	   
	}
	
	/**************************************************************************/
	
	function getDictionary($attr=array())
	{
		global $post;
		
		$dictionary=array();
		
		$default=array
		(
			'availability_rule_id'=>0
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
		
		if($attribute['availability_rule_id'])
			$argument['p']=$attribute['availability_rule_id'];
			   
		$query=new WP_Query($argument);
		if($query===false) return($dictionary);
		
		while($query->have_posts())
		{
			$query->the_post();
			$dictionary[$post->ID]['post']=$post;
			$dictionary[$post->ID]['meta']=CHBSPostMeta::getPostMeta($post);
		}
		
		CHBSHelper::preservePost($post,$bPost,0);	
		
		return($dictionary);		
	}
	
	/**************************************************************************/
	
	function getAVFromRule($bookingForm,$bookingData=array(),$objectType='payment')
	{
		$availability=array();
		
		$data=CHBSHelper::getPostOption();
		
		$bookingDataDefault=array();
		
		/***/
		
		CHBSHelper::removeUIndex($data,'service_type_id');

		$key=array
		(
			'booking_form_id'=>'booking_form_id',
			'service_type_id'=>'service_type_id',
			'fixed_location_pickup'=>'fixed_location_pickup_service_type_'.$data['service_type_id'],
			'fixed_location_dropoff'=>'fixed_location_dropoff_service_type_'.$data['service_type_id'],
			'pickup_location_coordinate'=>'pickup_location_coordinate_service_type_'.$data['service_type_id'],
			'dropoff_location_coordinate'=>'dropoff_location_coordinate_service_type_'.$data['service_type_id']
		);
		
		foreach($key as $index=>$value)
		{
			$bookingDataDefault[$index]=null;
			
			if(array_key_exists($value,$data))
				$bookingDataDefault[$index]=$data[$value];
		}
		
		$bookingData=array_merge($bookingData,$bookingDataDefault);
		
		/***/
		
		$rule=$bookingForm['dictionary']['av_rule'];
		if($rule===false) return($availability);

		foreach($rule as $ruleData)
		{
			if(!in_array(-1,$ruleData['meta']['booking_form_id']))
			{
				if(!in_array($bookingData['booking_form_id'],$ruleData['meta']['booking_form_id'])) continue;
			}
			
			if(!in_array(-1,$ruleData['meta']['service_type_id']))
			{
				if(!in_array($bookingData['service_type_id'],$ruleData['meta']['service_type_id'])) continue;
			}
			
			if(!in_array(-1,$ruleData['meta']['location_fixed_pickup']))
			{
				if(!in_array($bookingData['fixed_location_pickup'],$ruleData['meta']['location_fixed_pickup'])) continue;
			}

			if(!in_array(-1,$ruleData['meta']['location_fixed_dropoff']))
			{
				if(!in_array($bookingData['fixed_location_dropoff'],$ruleData['meta']['location_fixed_dropoff'])) continue;
			}
			
			if(!in_array(-1,$ruleData['meta']['location_geofence_pickup']))
			{
				$GeofenceChecker=new CHBSGeofenceChecker();
				
				$coordinate=$GeofenceChecker->transformShape($ruleData['meta']['location_geofence_pickup'],$bookingForm['dictionary']['geofence']);
				
				if(is_array($coordinate))
				{
					$inside=false;
					
					$pickupLocation=json_decode($bookingData['pickup_location_coordinate']);
					
					foreach($coordinate as $coordinateValue)
					{
						$result=$GeofenceChecker->pointInPolygon(new CHBSPoint($pickupLocation->lat,$pickupLocation->lng),$coordinateValue);
						
						if($result)
						{
							$inside=true;
							break;
						}
					}
					
					if(!$inside) continue;
				}
			}   
			
			if(!in_array(-1,$ruleData['meta']['location_geofence_dropoff']))
			{
				$GeofenceChecker=new CHBSGeofenceChecker();
				
				$coordinate=$GeofenceChecker->transformShape($ruleData['meta']['location_geofence_dropoff'],$bookingForm['dictionary']['geofence']);
				
				if(is_array($coordinate))
				{
					$inside=false;
					
					$dropoffLocation=json_decode($bookingData['dropoff_location_coordinate']);
					
					foreach($coordinate as $coordinateValue)
					{
						$result=$GeofenceChecker->pointInPolygon(new CHBSPoint($dropoffLocation->lat,$dropoffLocation->lng),$coordinateValue);
						
						if($result)
						{
							$inside=true;
							break;
						}
					}
					
					if(!$inside) continue;
				}
			}  
			
			switch($objectType)
			{
				case 'booking_period':
					
					$availability['booking_period_from']=$ruleData['meta']['booking_period_from'];
					$availability['booking_period_to']=$ruleData['meta']['booking_period_to'];
					$availability['booking_period_type']=$ruleData['meta']['booking_period_type'];
					
				break;
			
				case 'minimum_order_value':
					
					$availability['minimum_order_value']=$ruleData['meta']['minimum_order_value'];
					$availability['minimum_order_error_message']=$ruleData['meta']['minimum_order_error_message'];
					
				break;
			
				default:
					
					foreach($ruleData['meta'][$objectType] as $index=>$value)
					{
						if(in_array($value['availability'],array(-1,0,1)))
							$availability[$index]=$value['availability'];
					}					
			}
			
			if((int)$ruleData['meta']['process_next_rule_enable']!==1) return($availability);
		}
		
		return($availability);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/