<?php

/******************************************************************************/
/******************************************************************************/

class CHBSLocation
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
		return(PLUGIN_CHBS_CONTEXT.'_location');
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
					'name'=>__('Locations','chauffeur-booking-system'),
					'singular_name'=>__('Locations','chauffeur-booking-system'),
					'add_new'=>__('Add New','chauffeur-booking-system'),
					'add_new_item'=>__('Add New Location','chauffeur-booking-system'),
					'edit_item'=>__('Edit Location','chauffeur-booking-system'),
					'new_item'=>__('New Location','chauffeur-booking-system'),
					'all_items'=>__('Locations','chauffeur-booking-system'),
					'view_item'=>__('View Location','chauffeur-booking-system'),
					'search_items'=>__('Search Locations','chauffeur-booking-system'),
					'not_found'=>__('No Location Found','chauffeur-booking-system'),
					'not_found_in_trash'=>__('No Locations in Trash','chauffeur-booking-system'), 
					'parent_item_colon'=>'',
					'menu_name'=>__('Locations','chauffeur-booking-system')
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
		add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_location',array($this,'adminCreateMetaBoxClass'));
		
		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns')); 
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));
	}

	/**************************************************************************/
	
	function addMetaBox()
	{
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_location',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');		
	}
	
	/**************************************************************************/
	
	function addMetaBoxMain()
	{
		global $post;
		
		$Location=new CHBSLocation();
		$BookingForm=new CHBSBookingForm();
		
		$data=array();
		
		$data['meta']=CHBSPostMeta::getPostMeta($post);
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_location');
		
		$data['dictionary']['location']=$Location->getDictionary();
		$data['dictionary']['booking_form']=$BookingForm->getDictionary();
				
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_location.php');
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
		CHBSHelper::setDefault($meta,'location_name','');
		CHBSHelper::setDefault($meta,'location_name_coordinate_lat','');
		CHBSHelper::setDefault($meta,'location_name_coordinate_lng','');
		
		CHBSHelper::setDefault($meta,'vehicle_bid_max_percentage_discount','');
		
		CHBSHelper::setDefault($meta,'location_dropoff_disable_service_type_1',array(-1));
		CHBSHelper::setDefault($meta,'location_dropoff_disable_service_type_2',array(-1));
	}
	
	/**************************************************************************/
	
	function savePost($postId)
	{	  
		$Validation=new CHBSValidation();
		
		if(!$_POST) return(false);
		
		if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_location_noncename','savePost')===false) return(false);
		
		$meta=array();

		$this->setPostMetaDefault($meta);
		
		$meta['location_name']=CHBSHelper::getPostValue('location_name');
		$meta['location_name_coordinate_lat']=CHBSHelper::getPostValue('location_name_coordinate_lat');
		$meta['location_name_coordinate_lng']=CHBSHelper::getPostValue('location_name_coordinate_lng');
		
		/***/
		
		$meta['vehicle_bid_max_percentage_discount']=CHBSHelper::getPostValue('vehicle_bid_max_percentage_discount');
		if(!$Validation->isFloat($meta['vehicle_bid_max_percentage_discount'],0,99.99,false))
			$meta['vehicle_bid_max_percentage_discount']='';
		
		$meta['vehicle_bid_max_percentage_discount']=CHBSPrice::formatToSave($meta['vehicle_bid_max_percentage_discount'],true);
		
		/***/
		
		$data=array();
		
		foreach(array(1,2) as $serviceIndex)
		{
			$data[$serviceIndex]=(array)CHBSHelper::getPostValue('location_dropoff_disable_service_type_'.$serviceIndex);
			
			foreach($data[$serviceIndex] as $bookingFormIndex=>$locationIndex)
			{
				$data[$serviceIndex][$bookingFormIndex]=(array)$locationIndex;
				
				if(in_array(-1,$data[$serviceIndex][$bookingFormIndex]))
					$data[$serviceIndex][$bookingFormIndex]=array(-1);
				if(!count($data[$serviceIndex][$bookingFormIndex]))
					$data[$serviceIndex][$bookingFormIndex]=array(-1);
			}
			
			$meta['location_dropoff_disable_service_type_'.$serviceIndex]=$data[$serviceIndex];
		}
		
		/***/

		foreach($meta as $index=>$value)
			CHBSPostMeta::updatePostMeta($postId,$index,$value);
	}
	
	/**************************************************************************/
	
	function getDictionary($attr=array(),$sortingType=1)
	{
		global $post;
		
		$dictionary=array();
		
		$default=array
		(
			'location_id'=>0
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
		
		if($attribute['location_id'])
			$argument['p']=$attribute['location_id'];

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
	
	function manageEditColumns($column)
	{
		$column=array
		(
			'cb'=>$column['cb'],
			'title'=>__('Title','chauffeur-booking-system'),
			'map'=>__('Location on the map','chauffeur-booking-system')
		);
   
		return($column);		  
	}
	
	/**************************************************************************/
	
	function managePostsCustomColumn($column)
	{
		global $post;
		
		$Validation=new CHBSValidation();
		
		$meta=CHBSPostMeta::getPostMeta($post);
		
		switch($column) 
		{
			case 'map':
				
				if(($Validation->isNotEmpty($meta['location_name_coordinate_lat'])) && ($Validation->isNotEmpty($meta['location_name_coordinate_lng'])))
					echo '<a href="'.esc_url('https://www.google.com/maps/?q='.$meta['location_name_coordinate_lat'].','.$meta['location_name_coordinate_lng']).'" target="_blank">'.esc_html('Location on the Google Maps','chauffeur-booking-system').'</a>';
				else echo '-';
				
			break;	
		}
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