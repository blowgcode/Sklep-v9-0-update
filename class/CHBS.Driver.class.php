<?php

/******************************************************************************/
/******************************************************************************/

class CHBSDriver
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
		return(PLUGIN_CHBS_CONTEXT.'_driver');
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
					'name'=>__('Drivers','chauffeur-booking-system'),
					'singular_name'=>__('Drivers','chauffeur-booking-system'),
					'add_new'=>__('Add New','chauffeur-booking-system'),
					'add_new_item'=>__('Add New Driver','chauffeur-booking-system'),
					'edit_item'=>__('Edit Driver','chauffeur-booking-system'),
					'new_item'=>__('New Driver','chauffeur-booking-system'),
					'all_items'=>__('Drivers','chauffeur-booking-system'),
					'view_item'=>__('View Driver','chauffeur-booking-system'),
					'search_items'=>__('Search Drivers','chauffeur-booking-system'),
					'not_found'=>__('No Drivers Found','chauffeur-booking-system'),
					'not_found_in_trash'=>__('No Drivers in Trash','chauffeur-booking-system'), 
					'parent_item_colon'=>'',
					'menu_name'=>__('Drivers','chauffeur-booking-system')
				),	
				'public'=>false,  
				'show_ui'=>true, 
				'show_in_menu'=>'edit.php?post_type='.CHBSBooking::getCPTName(),
				'capability_type'=>'post',
				'menu_position'=>2,
				'hierarchical'=>false,  
				'rewrite'=>false,  
				'supports'=>array('editor','title','thumbnail')  
			)
		);
		
		add_action('save_post',array($this,'savePost'));
		add_action('add_meta_boxes_'.self::getCPTName(),array($this,'addMetaBox'));
		add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_driver',array($this,'adminCreateMetaBoxClass'));
		
		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns')); 
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));
	}

	/**************************************************************************/
	
	function addMetaBox()
	{
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_driver',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');		
	}
	
	/**************************************************************************/
	
	function addMetaBoxMain()
	{
		global $post;
		
		$SocialProfile=new CHBSSocialProfile();
		
		$data=array();
		
		$data['meta']=CHBSPostMeta::getPostMeta($post);
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_driver');
		
		$data['dictionary']['social_profile']=$SocialProfile->getSocialProfile();
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_driver.php');
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
		CHBSHelper::setDefault($meta,'first_name','');
		CHBSHelper::setDefault($meta,'second_name','');
		CHBSHelper::setDefault($meta,'position','');
		CHBSHelper::setDefault($meta,'notification_type',array(-1));
		
		CHBSHelper::setDefault($meta,'contact_email_address','');
		CHBSHelper::setDefault($meta,'contact_phone_number','');
		
		CHBSHelper::setDefault($meta,'contact_telegram_token','');
		CHBSHelper::setDefault($meta,'contact_telegram_group_id','');
	}
	
	/**************************************************************************/
	
	function savePost($postId)
	{	  
		if(!$_POST) return(false);
		
		if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_driver_noncename','savePost')===false) return(false);
		
		$Validation=new CHBSValidation();
		$SocialProfile=new CHBSSocialProfile();
		
		$meta=array();

		$this->setPostMetaDefault($meta);
		
		$data=CHBSHelper::getPostOption();

		CHBSPostMeta::updatePostMeta($postId,'first_name',$data['first_name']);
		CHBSPostMeta::updatePostMeta($postId,'second_name',$data['second_name']);
		CHBSPostMeta::updatePostMeta($postId,'position',$data['position']);
		
		CHBSPostMeta::updatePostMeta($postId,'contact_email_address',$data['contact_email_address']);
		CHBSPostMeta::updatePostMeta($postId,'contact_phone_number',$data['contact_phone_number']);
		
		CHBSPostMeta::updatePostMeta($postId,'contact_telegram_token',$data['contact_telegram_token']);
		CHBSPostMeta::updatePostMeta($postId,'contact_telegram_group_id',$data['contact_telegram_group_id']);
		
		/***/
		
		$data['notification_type']=(array)$data['notification_type'];
		foreach($data['notification_type'] as $index=>$value)
		{
			if($value===-1)
			{
				$data['notification_type']=array(-1);
				break;
			}
			
			if(!in_array($value,array(1,2)))
				unset($data['notification_type'][$index]);
		}
		
		if(!count($data['notification_type']))
			$data['notification_type']=array(-1);
		
		CHBSPostMeta::updatePostMeta($postId,'notification_type',$data['notification_type']);
		
		/***/
		
		$socialProfile=array();
		$tSocialProfile=array();

		foreach($data['social_profile']['profile'] as $index=>$value)
		{
			if((int)$index===0) continue;
			
			if(!$SocialProfile->isSocialProfile($value)) continue;
			
			$address=$data['social_profile']['url_address'][$index];
			
			if($Validation->isEmpty($address)) continue;
			
			if(!in_array($value,$tSocialProfile))
			{
				$tSocialProfile[]=$value;
				$socialProfile[]=array('profile'=>$value,'url_address'=>$address);
			}
		}
		
		CHBSPostMeta::updatePostMeta($postId,'social_profile',$socialProfile);
	}
	
	/**************************************************************************/
	
	function getDictionary($attr=array())
	{
		global $post;
		
		$dictionary=array();
		
		$default=array
		(
			'driver_id'=>0
		);
		
		$attribute=shortcode_atts($default,$attr);
		CHBSHelper::preservePost($post,$bPost);
		
		$argument=array
		(
			'post_type'=>self::getCPTName(),
			'post_status'=>'publish',
			'posts_per_page'=>-1,
			'orderby'=>array('title'=>'asc')
		);
		
		if($attribute['driver_id'])
			$argument['p']=$attribute['driver_id'];

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
			'title'=>$column['title'],
			'thumbnail'=>__('Thumbnail','chauffeur-booking-system'),
			'name'=>__('Name','chauffeur-booking-system'),
			'position'=>__('Position','chauffeur-booking-system'),
			'phone_number'=>__('Phone number','chauffeur-booking-system'),
			'email_address'=>__('E-mail address','chauffeur-booking-system')
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
			case 'name':
				
				echo esc_html(trim($meta['first_name'].' '.$meta['second_name']));
				
			break;
		
			case 'thumbnail':
				
				echo get_the_post_thumbnail($post,array(200,150));
				
			break;
		
			case 'position':
				
				echo esc_html($meta['position']);
				
			break;
		
			case 'phone_number':
				
				echo esc_html($meta['contact_phone_number']);
				
			break;		
		
			case 'email_address':
				
				echo esc_html($meta['contact_email_address']);
				
			break;	
		}
	}
	
	/**************************************************************************/
	
	function manageEditSortableColumns($column)
	{
		return($column);	   
	}
	
	/**************************************************************************/
	
	function getNotificationRecipient($id,$notificationType=1,$findBy='booking')
	{
		$recipient=array();
		
		$Booking=new CHBSBooking();
		$Validation=new CHBSValidation();
		
		if($findBy==='booking')
		{
			if(($booking=$Booking->getBooking($id))===false) return($recipient);
			$driverId=$booking['meta']['driver_id'];
		}
		else $driverId=$id;
			
		$dictionary=$this->getDictionary();
		
		if(!array_key_exists($driverId,$dictionary)) return($recipient);
		   
		$meta=$dictionary[$driverId]['meta'];
		
		if(!in_array($notificationType,$meta['notification_type'])) return($recipient);
		
		switch($notificationType)
		{
			case 1:
		 
				if($Validation->isNotEmpty($meta['contact_email_address']))
					$recipient[]=$meta['contact_email_address'];
				
			break;
		}
		
		return($recipient);
	}
	
	/**************************************************************************/
	
	function sendTelegramBooking($bookingId)
	{
		$Booking=new CHBSBooking();
		$BookingDriver=new CHBSBookingDriver();
		$BookingHelper=new CHBSBookingHelper();
		
		$Telegram=new CHBSTelegram();
		$Validation=new CHBSValidation();
		
		if(($booking=$Booking->getBooking($bookingId))===false) return(false);
		
		$driverId=$booking['meta']['driver_id'];
		$driverDictionary=$this->getDictionary();
		
		if(!array_key_exists($driverId,$driverDictionary)) return(false);
		
		if(!is_array($driverDictionary[$driverId]['meta']['notification_type'])) return(false);
		if(!in_array(2,$driverDictionary[$driverId]['meta']['notification_type'])) return(false);
		
		if($Validation->isEmpty($driverDictionary[$driverId]['meta']['contact_telegram_token'])) return(false);
		if($Validation->isEmpty($driverDictionary[$driverId]['meta']['contact_telegram_group_id'])) return(false);
		
		/***/
		
		$data=array();
		
		$data['booking']=$booking;
		$data['booking']['billing']=$Booking->createBilling($bookingId);
		
		$link=$BookingDriver->generateLink($bookingId);

		if(is_array($link))
		{
			$data['booking_driver_accept_link']=$link['accept'];
			$data['booking_driver_reject_link']=$link['reject'];
		}
		
		/***/
		
		$message=$BookingHelper->createNotification($data,'%0A');
		
		$Telegram->sendMessage($driverDictionary[$driverId]['meta']['contact_telegram_token'],$driverDictionary[$driverId]['meta']['contact_telegram_group_id'],$message,'HTML');
	
		/***/
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/