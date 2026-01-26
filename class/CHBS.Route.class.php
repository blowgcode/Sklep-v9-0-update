<?php

/******************************************************************************/
/******************************************************************************/

class CHBSRoute
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->priceSource=array
		(
			'1'=>array(__('Vehicle','chauffeur-booking-system')),
			'2'=>array(__('Route','chauffeur-booking-system')),
		);
	}
	
	/**************************************************************************/
	
	function getPriceSource()
	{
		return($this->priceSource);
	}

	/**************************************************************************/
	
	function isPriceSource($priceSource)
	{
		return(array_key_exists($priceSource,$this->priceSource));
	}
		
	/**************************************************************************/
	
	public function init()
	{
		$this->registerCPT();
	}
	
	/**************************************************************************/

	public static function getCPTName()
	{
		return(PLUGIN_CHBS_CONTEXT.'_route');
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
					'name'=>__('Routes','chauffeur-booking-system'),
					'singular_name'=>__('Route','chauffeur-booking-system'),
					'add_new'=>__('Add New','chauffeur-booking-system'),
					'add_new_item'=>__('Add New Route','chauffeur-booking-system'),
					'edit_item'=>__('Edit Route','chauffeur-booking-system'),
					'new_item'=>__('New Route','chauffeur-booking-system'),
					'all_items'=>__('Routes','chauffeur-booking-system'),
					'view_item'=>__('View Route','chauffeur-booking-system'),
					'search_items'=>__('Search Routes','chauffeur-booking-system'),
					'not_found'=>__('No Routes Found','chauffeur-booking-system'),
					'not_found_in_trash'=>__('No Routes Found in Trash','chauffeur-booking-system'), 
					'parent_item_colon'=>'',
					'menu_name'=>__('Routes','chauffeur-booking-system')
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
		add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_route',array($this,'adminCreateMetaBoxClass'));
		
		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns')); 
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));
	}
	
	/**************************************************************************/
	
	function addMetaBox()
	{
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_route',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');		
	}
	
	/**************************************************************************/
	
	function addMetaBoxMain()
	{
		global $post;
		
		$TaxRate=new CHBSTaxRate();
		$Vehicle=new CHBSVehicle();
		$PriceType=new CHBSPriceType();
		$GeoLocation=new CHBSGeoLocation();
		
		$data=array();
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_route');
		
		$data['meta']=CHBSPostMeta::getPostMeta($post);
				
		$data['coordinate']=$GeoLocation->getCoordinate();
		
		$data['dictionary']['vehicle']=$Vehicle->getDictionary();
		$data['dictionary']['tax_rate']=$TaxRate->getDictionary();
		
		$data['dictionary']['price_type']=$PriceType->getPriceType();
		
		$data['dictionary']['price_source']=$this->getPriceSource();
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_route.php');
		echo $Template->output();			
	}
	
	/**************************************************************************/
	
	function adminCreateMetaBoxClass($class) 
	{
		array_push($class,'to-postbox-1');
		return($class);
	}
	
	/**************************************************************************/
	
	function getDictionary($attr=array(),$sortingType=1,$formatPrice=false)
	{
		global $post;
		
		$dictionary=array();
		
		$default=array
		(
			'route_id'=>0
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
		
		if($attribute['route_id'])
		{
			if(is_array($attribute['route_id']))
			{
				if(count($attribute['route_id']))
					$argument['post__in']=$attribute['route_id'];
			}
			else
			{
				if($attribute['route_id']>0)
				   $argument['p']=$attribute['route_id']; 
			}
		}

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
		
		CHBSHelper::preservePost($post,$bPost,0);	
		
		return($dictionary);		
	}
	
	/**************************************************************************/
	
	function setPostMetaDefault(&$meta)
	{
		CHBSHelper::setDefault($meta,'coordinate',array());
		
		$TaxRate=new CHBSTaxRate();
		$PriceRule=new CHBSPriceRule();
		$GlobalData=new CHBSGlobalData();
		
		$vehicleDictionary=$GlobalData->setGlobalData('vehicle_dictionary',array(new CHBSVehicle(),'getDictionary'));
		
		$defaultPrice=CHBSPrice::getDefaultPrice();
		
		$priceUseTypeDictionary=$PriceRule->getPriceUseType();
		
		foreach($vehicleDictionary as $vehicleIndex=>$vehicleValue)
		{
			if(!isset($meta['vehicle'][$vehicleIndex]['enable']))
				$meta['vehicle'][$vehicleIndex]['enable']='1';
			if(!isset($meta['vehicle'][$vehicleIndex]['price_source']))
				$meta['vehicle'][$vehicleIndex]['price_source']='1';
			if(!isset($meta['vehicle'][$vehicleIndex]['price_type']))
				$meta['vehicle'][$vehicleIndex]['price_type']='1';
			
			foreach($priceUseTypeDictionary as $priceUseTypeIndex=>$priceUseTypeValue)
			{
				if(!isset($meta['vehicle'][$vehicleIndex]['price_'.$priceUseTypeIndex.'_value']))
					$meta['vehicle'][$vehicleIndex]['price_'.$priceUseTypeIndex.'_value']=$defaultPrice;
				if(!isset($meta['vehicle'][$vehicleIndex]['price_'.$priceUseTypeIndex.'_tax_rate_id']))
					$meta['vehicle'][$vehicleIndex]['price_'.$priceUseTypeIndex.'_tax_rate_id']=$TaxRate->getDefaultTaxPostId();				
			}	
		}
		
		for($i=1;$i<8;$i++)
		{
			if(!isset($meta['pickup_hour'][$i]))
				$meta['pickup_hour'][$i]=array('hour'=>null);
		}	
	}
	
	/**************************************************************************/
	
	function getEnableVehicleFromRoute(&$route)
	{
		$vehicle=array();
		
		foreach($route as $routeData)
		{
			foreach($routeData['meta']['vehicle'] as $vehicleIndex=>$vehicleData)
			{
				if((int)$vehicleData['enable']===1) $vehicle[]=$vehicleIndex;
			}
		}
		
		return($vehicle);
	}
	
	/**************************************************************************/
	
	function savePost($postId)
	{	  
		if(!$_POST) return(false);
		
		if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_route_noncename','savePost')===false) return(false);
		
		$option=CHBSHelper::getPostOption();
		
		$Date=new CHBSDate();
		$TaxRate=new CHBSTaxRate();
		$Vehicle=new CHBSVehicle();
		$PriceRule=new CHBSPriceRule();
		$PriceType=new CHBSPriceType();
		$Validation=new CHBSValidation();

		/***/
		
		CHBSPostMeta::updatePostMeta($postId,'coordinate',json_decode($option['coordinate']));
		
		/***/
		
		$defaultPrice=CHBSPrice::getDefaultPrice();
		
		$vehicle=array();
		
		$vehicleDictionary=$Vehicle->getDictionary();
		
		$priceDictionary=array('price_fixed','price_fixed_return','price_fixed_return_new_ride','price_initial','price_delivery','price_delivery_return','price_distance','price_distance_return','price_distance_return_new_ride','price_hour','price_hour_return','price_hour_return_new_ride','price_extra_time','price_passenger_adult','price_passenger_children');
		
		foreach($vehicleDictionary as $index=>$value)
		{
			if(!isset($option['vehicle'][$index])) continue;
			
			if(!$Validation->isBool($option['vehicle'][$index]['enable']))
				$vehicle[$index]['enable']=0;
			else $vehicle[$index]['enable']=$option['vehicle'][$index]['enable'];
			
			if((isset($option['vehicle'][$index]['price_type'])) && ($PriceType->isPriceType($option['vehicle'][$index]['price_type'])))
				$vehicle[$index]['price_type']=$option['vehicle'][$index]['price_type'];
			else $vehicle[$index]['price_type']=1;

			if((isset($option['vehicle'][$index]['price_source'])) && ($this->isPriceSource($option['vehicle'][$index]['price_source'])))
				$vehicle[$index]['price_source']=$option['vehicle'][$index]['price_source'];
			else $vehicle[$index]['price_source']=1;
		
			foreach($PriceRule->getPriceUseType() as $priceIndex=>$priceValue)
			{
				if((isset($option['vehicle'][$index]['price_'.$priceIndex.'_value'])) && (CHBSPrice::isPrice($option['vehicle'][$index]['price_'.$priceIndex.'_value'])))
					$vehicle[$index]['price_'.$priceIndex.'_value']=$option['vehicle'][$index]['price_'.$priceIndex.'_value'];
				else $vehicle[$index]['price_'.$priceIndex.'_value']=$defaultPrice;		
						
				$vehicle[$index]['price_'.$priceIndex.'_value']=CHBSPrice::formatToSave($vehicle[$index]['price_'.$priceIndex.'_value'],false);

				if((int)$priceValue['use_tax']===1)
				{
					if((int)$option['vehicle'][$index]['price_'.$priceIndex.'_tax_rate_id']===-1)
						$vehicle[$index]['price_'.$priceIndex.'_tax_rate_id']=-1;
					else
					{
						if($TaxRate->isTaxRate($option['vehicle'][$index]['price_'.$priceIndex.'_tax_rate_id']))
							$vehicle[$index]['price_'.$priceIndex.'_tax_rate_id']=$option['vehicle'][$index]['price_'.$priceIndex.'_tax_rate_id'];
						else $vehicle[$index]['price_'.$priceIndex.'_tax_rate_id']=0; 
					}
				}
			}
		}
		
		CHBSPostMeta::updatePostMeta($postId,'vehicle',$vehicle);
		
		/***/
		
		$pickupHour=array();
		$pickupHourPost=CHBSHelper::getPostValue('pickup_hour');
		
		foreach(array_keys($Date->day) as $dayIndex)
		{
			$hour=preg_split('/;/',$pickupHourPost[$dayIndex]['hour']);
			
			foreach($hour as $hourIndex=>$hourValue)
			{
				$hourValue=$Date->formatTimeToStandard($hourValue);
			
				if($Validation->isTime($hourValue,false))
					$pickupHour[$dayIndex]['hour'][$hourIndex]=$hourValue;
			}
		}
		
		CHBSPostMeta::updatePostMeta($postId,'pickup_hour',$pickupHour);
		
		/***/
	}
	
	/**************************************************************************/
	
	function manageEditColumns($column)
	{
		$column=array
		(
			'cb'=>$column['cb'],
			'title'=>__('Title','chauffeur-booking-system')
		);
   
		return($column);		  
	}
	
	/**************************************************************************/
	
	function managePostsCustomColumn($column)
	{

	}
	
	/**************************************************************************/
	
	function manageEditSortableColumns($column)
	{
		return($column);	   
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/