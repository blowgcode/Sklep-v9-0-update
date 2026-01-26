<?php

/******************************************************************************/
/******************************************************************************/

class CHBSEmailAccount
{
	public $secureConnectionType;
	/**************************************************************************/
	
	public function __construct()
	{
		$this->secureConnectionType=array
		(
			'ssl'=>array(__('SSL','chauffeur-booking-system')),
			'tls'=>array(__('TLS','chauffeur-booking-system')),
			'none'=>array(__('None','chauffeur-booking-system'))
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
		return(PLUGIN_CHBS_CONTEXT.'_email_account');
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
					'name'=>__('E-mail Accounts','chauffeur-booking-system'),
					'singular_name'=>__('E-mail Accounts','chauffeur-booking-system'),
					'add_new'=>__('Add New','chauffeur-booking-system'),
					'add_new_item'=>__('Add New E-mail Account','chauffeur-booking-system'),
					'edit_item'=>__('Edit E-mail Account','chauffeur-booking-system'),
					'new_item'=>__('New E-mail Account','chauffeur-booking-system'),
					'all_items'=>__('E-mail Accounts','chauffeur-booking-system'),
					'view_item'=>__('View E-mail Account','chauffeur-booking-system'),
					'search_items'=>__('Search E-mail Accounts','chauffeur-booking-system'),
					'not_found'=>__('No E-mail Accounts Found','chauffeur-booking-system'),
					'not_found_in_trash'=>__('No E-mail Accounts in Trash','chauffeur-booking-system'), 
					'parent_item_colon'=>'',
					'menu_name'=>__('E-mail Accounts','chauffeur-booking-system')
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
		add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_email_account',array($this,'adminCreateMetaBoxClass'));
		
		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns')); 
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));
	}

	/**************************************************************************/
	
	function addMetaBox()
	{
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_email_account',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');		
	}
	
	/**************************************************************************/
	
	function addMetaBoxMain()
	{
		global $post;
		
		$data=array();
		
		$data['meta']=CHBSPostMeta::getPostMeta($post);
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_email_account');
		
		$data['dictionary']['secure_connection_type']=$this->secureConnectionType;
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_email_account.php');
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
		CHBSHelper::setDefault($meta,'sender_name','');
		CHBSHelper::setDefault($meta,'sender_email_address','');
		
		CHBSHelper::setDefault($meta,'smtp_auth_enable','0');
		CHBSHelper::setDefault($meta,'smtp_auth_username','');
		CHBSHelper::setDefault($meta,'smtp_auth_password','');
		CHBSHelper::setDefault($meta,'smtp_auth_host','');
		CHBSHelper::setDefault($meta,'smtp_auth_port','');
		CHBSHelper::setDefault($meta,'smtp_auth_secure_connection_type','ssl');
		CHBSHelper::setDefault($meta,'smtp_auth_debug_enable','0');
	}
	
	/**************************************************************************/
	
	function savePost($postId)
	{	  
		if(!$_POST) return(false);
		
		if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_email_account_noncename','savePost')===false) return(false);
		
		$meta=array();

		$Validation=new CHBSValidation();
		
		$this->setPostMetaDefault($meta);
		
		$data=CHBSHelper::getPostOption();

		CHBSPostMeta::updatePostMeta($postId,'sender_name',$data['sender_name']);
		CHBSPostMeta::updatePostMeta($postId,'sender_email_address',$data['sender_email_address']);
				
		if($data['smtp_auth_enable']==1)
		{
			CHBSPostMeta::updatePostMeta($postId,'smtp_auth_enable',1);
			CHBSPostMeta::updatePostMeta($postId,'smtp_auth_username',$data['smtp_auth_username']); 
			CHBSPostMeta::updatePostMeta($postId,'smtp_auth_password',$data['smtp_auth_password']);
			CHBSPostMeta::updatePostMeta($postId,'smtp_auth_host',$data['smtp_auth_host']); 
			CHBSPostMeta::updatePostMeta($postId,'smtp_auth_port',$data['smtp_auth_port']);
			
			if(array_key_exists($data['smtp_auth_secure_connection_type'],$this->secureConnectionType))
				CHBSPostMeta::updatePostMeta($postId,'smtp_auth_secure_connection_type',$data['smtp_auth_secure_connection_type']); 
			else CHBSPostMeta::updatePostMeta($postId,'smtp_auth_secure_connection_type','ssl');
			
			if($Validation->isBool($data['smtp_auth_debug_enable']))
				CHBSPostMeta::updatePostMeta($postId,'smtp_auth_debug_enable',$data['smtp_auth_debug_enable']);   
			else CHBSPostMeta::updatePostMeta($postId,'smtp_auth_debug_enable',0);   
		}
		else
		{
			CHBSPostMeta::updatePostMeta($postId,'smtp_auth_enable',0);
			CHBSPostMeta::updatePostMeta($postId,'smtp_auth_username',''); 
			CHBSPostMeta::updatePostMeta($postId,'smtp_auth_password','');
			CHBSPostMeta::updatePostMeta($postId,'smtp_auth_host',''); 
			CHBSPostMeta::updatePostMeta($postId,'smtp_auth_port','');
			CHBSPostMeta::updatePostMeta($postId,'smtp_auth_secure_connection_type','ssl'); 
			CHBSPostMeta::updatePostMeta($postId,'smtp_auth_debug_enable',0);			 
		}
	}
	
	/**************************************************************************/
	
	function getDictionary($attr=array())
	{
		global $post;
		
		$dictionary=array();
		
		$default=array
		(
			'email_account_id'=>0
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
		
		if($attribute['email_account_id'])
			$argument['p']=$attribute['email_account_id'];

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
			'sender'=>__('Sender','chauffeur-booking-system')
		);
   
		return($column);		  
	}
	
	/**************************************************************************/
	
	function managePostsCustomColumn($column)
	{
		global $post;
		
		$meta=CHBSPostMeta::getPostMeta($post);
		
		$Validation=new CHBSValidation();
		
		switch($column) 
		{
			case 'sender':
				
				$html=null;
				
				if($Validation->isNotEmpty($meta['sender_name']))
					$html.=esc_html($meta['sender_name']);
				
				if($Validation->isNotEmpty($meta['sender_email_address']))
					$html.=' <a href="mailto:'.esc_attr($meta['sender_email_address']).'">&lt;'.$meta['sender_email_address'].'&gt;</a>';
				
				echo trim($html);
				
			break;
		}
	}
	
	/**************************************************************************/
	
	function manageEditSortableColumns($column)
	{
		return($column);	   
	}
	
	/**************************************************************************/
	
	function sendTestEmail()
	{
		$Email=new CHBSEmail();
		$EmailAccount=new CHBSEmailAccount();
		
		$response=array('error'=>0,'error_message'=>'');
		
		$emailAccountId=(int)CHBSHelper::getPostValue('email_account_id',false);
		
		if(($dictionary=$EmailAccount->getDictionary(array('email_account_id'=>$emailAccountId)))===false)
		{
			$response['error']=1;
			$response['error_message']=__('Cannot set details needed to send an e-mail message.','chauffeur-booking-system');
			CHBSHelper::createJSONResponse($response);
		}
		
		$emailAccount=$dictionary[$emailAccountId];
		
		/***/
		
		global $chbs_phpmailer;
		
		$chbs_phpmailer['sender_name']=$emailAccount['meta']['sender_name'];
		$chbs_phpmailer['sender_email_address']=$emailAccount['meta']['sender_email_address'];
		
		$chbs_phpmailer['smtp_auth_enable']=$emailAccount['meta']['smtp_auth_enable'];
		$chbs_phpmailer['smtp_auth_debug_enable']=$emailAccount['meta']['smtp_auth_debug_enable'];
		
		$chbs_phpmailer['smtp_auth_username']=$emailAccount['meta']['smtp_auth_username'];
		$chbs_phpmailer['smtp_auth_password']=$emailAccount['meta']['smtp_auth_password'];
		
		$chbs_phpmailer['smtp_auth_host']=$emailAccount['meta']['smtp_auth_host'];
		$chbs_phpmailer['smtp_auth_port']=$emailAccount['meta']['smtp_auth_port'];
		
		$chbs_phpmailer['smtp_auth_secure_connection_type']=$emailAccount['meta']['smtp_auth_secure_connection_type'];
		
		/***/
		
		$data=array();
		
		$data['style']=$Email->getEmailStyle();
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'email_test.php');
		$body=$Template->output();
		
		/***/
		
		global $chbs_logEvent;
		
		$chbs_logEvent=-1;
		
		add_action('wp_mail_failed','logWPMailErrorLocal',10,1);
		
		function logWPMailErrorLocal($wp_error)
		{
			global $chbsGlobalData;
			$chbsGlobalData['wp_mail_error']=$wp_error;
		} 
		
		/***/
	 
		global $chbsGlobalData;
		
		$Email->send(array(CHBSHelper::getPostValue('receiver_email_address',false)),__('Test message','chauffeur-booking-system'),$body);
	   
		$response['error']=0;
		$response['email_response']=esc_html(print_r($chbsGlobalData['wp_mail_error'],true));
		
		CHBSHelper::createJSONResponse($response);
	}
		
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/