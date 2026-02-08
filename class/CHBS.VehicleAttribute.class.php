<?php

/******************************************************************************/
/******************************************************************************/

class CHBSVehicleAttribute
{
	/**************************************************************************/
	
	public $attributeType;
	
	/**************************************************************************/
	
	function __construct()
	{
		$this->attributeType=array
		(
			'1'=>array(__('Text Value','chauffeur-booking-system')),
			'2'=>array(__('Single Choice','chauffeur-booking-system')),
			'3'=>array(__('Multi Choice','chauffeur-booking-system'))
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
		return(PLUGIN_CHBS_CONTEXT.'_vehicle_attr');
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
					'name'=>__('Vehicle Attributes','chauffeur-booking-system'),
					'singular_name'=>__('Vehicle Attribute','chauffeur-booking-system'),
					'add_new'=>__('Add New','chauffeur-booking-system'),
					'add_new_item'=>__('Add New Vehicle Attribute','chauffeur-booking-system'),
					'edit_item'=>__('Edit Vehicle Attribute','chauffeur-booking-system'),
					'new_item'=>__('New Vehicle Attribute','chauffeur-booking-system'),
					'all_items'=>__('Vehicle Attributes','chauffeur-booking-system'),
					'view_item'=>__('View Vehicle Attribute','chauffeur-booking-system'),
					'search_items'=>__('Search Vehicle Attributes','chauffeur-booking-system'),
					'not_found'=>__('No Vehicle Attributes Found','chauffeur-booking-system'),
					'not_found_in_trash'=>__('No Vehicle Attributes Found in Trash','chauffeur-booking-system'), 
					'parent_item_colon'=>'',
					'menu_name'=>__('Vehicle Attributes','chauffeur-booking-system')
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
		add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_vehicle_attribute',array($this,'adminCreateMetaBoxClass'));
		
		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns')); 
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));
	}

	/**************************************************************************/
	
	function addMetaBox()
	{
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_vehicle_attribute',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');		
	}
	
	/**************************************************************************/
	
	function addMetaBoxMain()
	{
		global $post;
		
		$data=array();
		
		$data['meta']=CHBSPostMeta::getPostMeta($post);
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_vehicle_attribute');
		
		$data['dictionary']['attribute_type']=$this->getAttributeType();

		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_vehicle_attribute.php');
		echo $Template->output();			
	}
	
	 /**************************************************************************/
	
	function adminCreateMetaBoxClass($class) 
	{
		array_push($class,'to-postbox-1');
		return($class);
	}
	
	/**************************************************************************/
	
	function getAttributeType()
	{
		return($this->attributeType);
	}
	
	/**************************************************************************/
	
	function isAttributeType($attributeType)
	{
		return(array_key_exists($attributeType,$this->getAttributeType()));
	}
	
	/**************************************************************************/
	
	function setPostMetaDefault(&$meta)
	{
		CHBSHelper::setDefault($meta,'attribute_type','1');
		CHBSHelper::setDefault($meta,'attribute_value',array());
	}
	
	/**************************************************************************/
	
	function savePost($postId)
	{	  
		if(!$_POST) return(false);
		
		if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_vehicle_attribute_noncename','savePost')===false) return(false);
		
		$meta=array();

		$Validation=new CHBSValidation();
		
		$this->setPostMetaDefault($meta);
		
		/***/
		
		if(CHBSHelper::getPostValue('is_edit_mode'))
		{
			$postMeta=CHBSPostMeta::getPostMeta($postId);
			$meta['attribute_type']=$postMeta['attribute_type'];
		}
		else
		{
			$meta['attribute_type']=CHBSHelper::getPostValue('attribute_type');
		}
		
		if(!$this->isAttributeType($meta['attribute_type']))
			$meta['attribute_type']=1;	  
		
		if($meta['attribute_type']!=1)
		{
			if(array_key_exists(PLUGIN_CHBS_CONTEXT.'_attribute_value',$_POST))
			{
				$data=CHBSHelper::getPostValue('attribute_value');
				if(!is_array($data)) $data=array();

				foreach($data as $index=>$value)
				{
					if($Validation->isNotEmpty($value))
					   $meta['attribute_value'][]=array('id'=>$index,'value'=>$value);
				}
			}
		}

		/***/

		foreach($meta as $index=>$value)
			CHBSPostMeta::updatePostMeta($postId,$index,$value);
	}
	
	/**************************************************************************/
	
	function getDictionary($attr=array())
	{
		global $post;
		
		$dictionary=array();
		
		$default=array
		(
			'vehicle_attribute_id'=>0
		);
		
		$attribute=shortcode_atts($default,$attr);
		CHBSHelper::preservePost($post,$bPost);
		
		$argument=array
		(
			'post_type'=>self::getCPTName(),
			'post_status'=>'publish',
			'posts_per_page'=>-1,
			'orderby'=>array('menu_order'=>'asc','title'=>'asc')
		);
		
		if($attribute['vehicle_attribute_id'])
			$argument['p']=$attribute['vehicle_attribute_id'];

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
			'attribute_type'=>__('Type','chauffeur-booking-system'),
			'attribute_value'=>__('Values','chauffeur-booking-system')
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
			case 'attribute_type':
				
				echo esc_html($this->attributeType[$meta['attribute_type']][0]);
				
			break;
		
			case 'attribute_value':
			
				$html=null;
				
				if(in_array($meta['attribute_type'],array(2,3)))
				{
					if((array_key_exists('attribute_value',$meta)) && (is_array($meta['attribute_value'])))
					{
						foreach($meta['attribute_value'] as $value)
						{
							if($Validation->isNotEmpty($html)) $html.=', ';
							$html.=esc_html($value['value']);
						}
					}
				}	
				
				if($Validation->isEmpty($html)) $html='-';
				
				echo $html;
				
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