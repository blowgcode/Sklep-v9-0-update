<?php

/******************************************************************************/
/******************************************************************************/

class CHBSVehicleCompany
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
		return(PLUGIN_CHBS_CONTEXT.'_vehicle_company');
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
					'name'=>__('Vehicle Companies','chauffeur-booking-system'),
					'singular_name'=>__('Vehicle Companies','chauffeur-booking-system'),
					'add_new'=>__('Add New','chauffeur-booking-system'),
					'add_new_item'=>__('Add New Vehicle Company','chauffeur-booking-system'),
					'edit_item'=>__('Edit Vehicle Company','chauffeur-booking-system'),
					'new_item'=>__('New Vehicle Company','chauffeur-booking-system'),
					'all_items'=>__('Vehicle Companies','chauffeur-booking-system'),
					'view_item'=>__('View Vehicle Company','chauffeur-booking-system'),
					'search_items'=>__('Search Vehicle Companies','chauffeur-booking-system'),
					'not_found'=>__('No Vehicle Companies Found','chauffeur-booking-system'),
					'not_found_in_trash'=>__('No Vehicle Companies in Trash','chauffeur-booking-system'), 
					'parent_item_colon'=>'',
					'menu_name'=>__('Vehicle Companies','chauffeur-booking-system')
				),
				'public'=>false,  
				'show_ui'=>true, 
				'show_in_menu'=>'edit.php?post_type='.CHBSBooking::getCPTName(),
				'capability_type'=>'post',
				'menu_position'=>2,
				'hierarchical'=>false,  
				'rewrite'=>false,  
				'supports'=>array('title')  
			)
		);
		
		add_action('save_post',array($this,'savePost'));
		add_action('add_meta_boxes_'.self::getCPTName(),array($this,'addMetaBox'));
		add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_vehicle_company',array($this,'adminCreateMetaBoxClass'));
		
		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns')); 
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));
	}

	/**************************************************************************/
	
	function addMetaBox()
	{
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_vehicle_company',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');		
	}
	
	/**************************************************************************/
	
	function addMetaBoxMain()
	{
		global $post;
		
		$Country=new CHBSCountry();
		
		$data=array();
		
		$data['meta']=CHBSPostMeta::getPostMeta($post);
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_vehicle_company');
		
		$data['dictionary']['country']=$Country->getCountry();
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_vehicle_company.php');
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
		$GeoLocation=new CHBSGeoLocation();
				
		CHBSHelper::setDefault($meta,'contact_phone_number','');
		CHBSHelper::setDefault($meta,'contact_email_address','');
		
		CHBSHelper::setDefault($meta,'address_street_name','');
		CHBSHelper::setDefault($meta,'address_street_number','');
		CHBSHelper::setDefault($meta,'address_city','');
		CHBSHelper::setDefault($meta,'address_state','');
		CHBSHelper::setDefault($meta,'address_postal_code','');
		CHBSHelper::setDefault($meta,'address_country',$GeoLocation->getCountryCode());
	}
	
	/**************************************************************************/
	
	function savePost($postId)
	{	  
		if(!$_POST) return(false);
		
		if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_vehicle_company_noncename','savePost')===false) return(false);
		
		$Country=new CHBSCountry();
		$Validation=new CHBSValidation();
		
		$option=CHBSHelper::getPostOption();
		
		if(!$Validation->isEmailAddress($option['contact_email_address']))
			$option['contact_email_address']='';
		
		if(!$Country->isCountry($option['address_country']))
			$option['address_country']='US';
		
		$key=array
		(
			'contact_phone_number',
			'contact_email_address',
			'address_street_name',
			'address_street_number',
			'address_city',
			'address_state',
			'address_postal_code',
			'address_country'
		);

		foreach($key as $value)
			CHBSPostMeta::updatePostMeta($postId,$value,$option[$value]);
	}
	
	/**************************************************************************/
	
	function getDictionary($attr=array(),$sortingType=1)
	{
		global $post;
		
		$dictionary=array();
		
		$default=array
		(
			'vehicle_company_id'=>0
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
		
		if($attribute['vehicle_company_id'])
			$argument['p']=$attribute['vehicle_company_id'];

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
			'title'=>__('Name','chauffeur-booking-system'),
			'contact_phone_number'=>__('Phone number','chauffeur-booking-system'),
			'contact_email_address'=>__('E-mail address','chauffeur-booking-system'),
			'address'=>__('Address','chauffeur-booking-system')
		);
   
		return($column);		  
	}
	
	/**************************************************************************/
	
	function managePostsCustomColumn($column)
	{
		global $post;
		
		$meta=CHBSPostMeta::getPostMeta($post);
		
		switch($column) 
		{
			case 'contact_phone_number':
				
				echo esc_html($meta['contact_phone_number']);

			break;
		
			case 'contact_email_address':
				
				echo esc_html($meta['contact_email_address']);
				
			break;
		
			case 'address':
				
				$data=array
				(
					'name'=>$post->post_title,
					'street'=>$meta['address_street_name'],
					'street_number'=>$meta['address_street_number'],
					'city'=>$meta['address_city'],
					'state'=>$meta['address_state'],
					'postal_code'=>$meta['address_postal_code'],
					'country'=>$meta['address_country']
				);
				
				echo CHBSHelper::displayAddress($data);
				
			break;
		}
	}
	
	/**************************************************************************/
	
	function manageEditSortableColumns($column)
	{
		return($column);	   
	}
	
	/**************************************************************************/
	
	function isVehicleCompany($vehicleCompanyId,$dictionary)
	{
		return(array_key_exists($vehicleCompanyId,$dictionary) ? true : false);
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/