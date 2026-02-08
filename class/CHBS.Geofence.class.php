<?php

/******************************************************************************/
/******************************************************************************/

class CHBSGeofence
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
		return(PLUGIN_CHBS_CONTEXT.'_geofence');
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
					'name'=>__('Geofence','chauffeur-booking-system'),
					'singular_name'=>__('Geofence','chauffeur-booking-system'),
					'add_new'=>__('Add New','chauffeur-booking-system'),
					'add_new_item'=>__('Add New Geofence','chauffeur-booking-system'),
					'edit_item'=>__('Edit Geofence','chauffeur-booking-system'),
					'new_item'=>__('New Geofence','chauffeur-booking-system'),
					'all_items'=>__('Geofence','chauffeur-booking-system'),
					'view_item'=>__('View Geofence','chauffeur-booking-system'),
					'search_items'=>__('Search Geofence','chauffeur-booking-system'),
					'not_found'=>__('No Geofence Found','chauffeur-booking-system'),
					'not_found_in_trash'=>__('No Geofence in Trash','chauffeur-booking-system'), 
					'parent_item_colon'=>'',
					'menu_name'=>__('Geofence','chauffeur-booking-system')
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
		add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_geofence',array($this,'adminCreateMetaBoxClass'));
		
		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns')); 
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));
	}

	/**************************************************************************/
	
	function addMetaBox()
	{
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_geofence',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');		
	}
	
	/**************************************************************************/
	
	function addMetaBoxMain()
	{
		global $post;
		
		$TaxRate=new CHBSTaxRate();
		$GeoLocation=new CHBSGeoLocation();
		
		$data=array();
		
		$data['meta']=CHBSPostMeta::getPostMeta($post);
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_geofence');
		
		$data['coordinate']=$GeoLocation->getCoordinate();
		
		$data['dictionary']['tax_rate']=$TaxRate->getDictionary();
	
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_geofence.php');
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
		CHBSHelper::setDefault($meta,'shape_coordinate',array());
		CHBSHelper::setDefault($meta,'tax_rate_id',-1);
	}
	
	/**************************************************************************/
	
	function savePost($postId)
	{	  
		if(!$_POST) return(false);
		
		if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_geofence_noncename','savePost')===false) return(false);

		$TaxRate=new CHBSTaxRate();
		$Validation=new CHBSValidation();
		$GeofenceImport=new CHBSGeofenceImport();
		
		$option=CHBSHelper::getPostOption();
		
		CHBSPostMeta::updatePostMeta($postId,'shape_coordinate',json_decode($option['shape_coordinate']));
		
		$meta['tax_rate_id']=$option['tax_rate_id'];
		if(!$TaxRate->isTaxRate($meta['tax_rate_id']))
			$meta['tax_rate_id']=-1;
		
		CHBSPostMeta::updatePostMeta($postId,'tax_rate_id',$meta['tax_rate_id']);
		
		/***/
		
		$coordinate=null;
		$coordinateSave=array();
		
		if($Validation->isNotEmpty($option['import_coordinate']))
		{
			$coordinate=array($option['import_coordinate']);	

			foreach($coordinate as $coordinateValue)
			{
				$coordinateValue=$GeofenceImport->formatCoordinateToSave($coordinateValue);

				if(count($coordinateValue))
					$coordinateSave[CHBSHelper::createId()]=$coordinateValue;
			}

			if(count($coordinateSave))
			{
				$coordinateSave=array_merge($coordinateSave,(array)json_decode($option['shape_coordinate']));

				CHBSPostMeta::updatePostMeta($postId,'shape_coordinate',(object)$coordinateSave);
			}
		}
	}
	
	/**************************************************************************/
	
	function getDictionary($attr=array(),$sortingType=1)
	{
		global $post;
		
		$dictionary=array();
		
		$default=array
		(
			'geofence_id'=>0
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
		
		if($attribute['geofence_id'])
			$argument['p']=$attribute['geofence_id'];

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