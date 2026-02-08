<?php

/******************************************************************************/
/******************************************************************************/

class CHBSUser
{
	
	/**************************************************************************/
	
	function init()
	{
		add_action('show_user_profile',array($this,'createUserBusinessAccountSection'));
		add_action('edit_user_profile',array($this,'createUserBusinessAccountSection'));	

		add_action('personal_options_update',array($this,'saveUserBusinessAccountSection'));
		add_action('edit_user_profile_update',array($this,'saveUserBusinessAccountSection'));	
	}
	
	/**************************************************************************/
	
	function createUserBusinessAccountSection($user)
	{
        global $post;
        
		$data=array();
        
		$data['meta']=array();
		
		$data['meta']['business_user_account_enable']=(int)get_the_author_meta('business_user_account_enable',$user->ID);
		$data['meta']['business_user_account_date_from']=get_the_author_meta('business_user_account_date_from',$user->ID);
		$data['meta']['business_user_account_date_to']=get_the_author_meta('business_user_account_date_to',$user->ID);
		$data['meta']['business_user_account_amount']=get_the_author_meta('business_user_account_amount',$user->ID);		
		$data['meta']['business_user_account_transaction']=get_the_author_meta('business_user_account_transaction',$user->ID);	
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/section_user_business_account.php');
		echo $Template->output();	 		
	}
	
	/**************************************************************************/
	
	function saveUserBusinessAccountSection($userId)
	{		
		if(!current_user_can('edit_user',$userId)) return(false);
		
		$Validation=new CHBSValidation();
		
		$data=CHBSHelper::getPostOption();
				
		if(!$Validation->isBool($data['business_user_account_enable']))
			$data['business_user_account_enable']=0;
		
		if((int)$data['business_user_account_enable']===1)
		{
			if(!$Validation->isDate($data['business_user_account_date_from']))
				$data['business_user_account_date_from']='';
			if(!$Validation->isDate($data['business_user_account_date_to']))
				$data['business_user_account_date_to']='';
			if(!$Validation->isPrice($data['business_user_account_amount']))
				$data['business_user_account_amount']='';	
		}
		else
		{
			$data['business_user_account_enable']=0;
			$data['business_user_account_date_from']='';
			$data['business_user_account_date_to']='';
			$data['business_user_account_amount']='';
		}
		
		update_user_meta($userId,'business_user_account_enable',(int)$data['business_user_account_enable']);
		update_user_meta($userId,'business_user_account_date_from',$data['business_user_account_date_from']);
		update_user_meta($userId,'business_user_account_date_to',$data['business_user_account_date_to']);
		update_user_meta($userId,'business_user_account_amount',$data['business_user_account_amount']);	
	}
	
	/**************************************************************************/
	
	function getUserBusinessAccountData($userId)
	{
		$data=array();	
		
		$data['enable']=(int)get_the_author_meta('business_user_account_enable',$userId);
		$data['date_from']=get_the_author_meta('business_user_account_date_from',$userId);
		$data['date_to']=get_the_author_meta('business_user_account_date_to',$userId);
		$data['amount']=get_the_author_meta('business_user_account_amount',$userId);	
		$data['transaction']=get_the_author_meta('business_user_account_transaction',$userId);
		
		if(!is_array($data['transaction'])) $data['transaction']=array();
		
		return($data);
	}
	
	/**************************************************************************/
	
	function getUserBusinessAccountAmount($bookingForm,$pickupDate)
	{
		$Date=new CHBSDate();
		$User=new CHBSUser();
		$WooCommerce=new CHBSWooCommerce();

		if(!$WooCommerce->isEnable($bookingForm['meta'])) return(false);
			
		if(!$User->isSignIn()) return(false);
			
		$userId=$User->getUserId();
		
		$userData=$User->getUserBusinessAccountData($userId);

		if((int)$userData['enable']!==1) return(false);
			
		if(!$Date->dateInRange($pickupDate,$userData['date_from'],$userData['date_to'])) return(false);

		return($userData['amount']);
	}
	
	/**************************************************************************/
	
	function isUserBusinessAccount($bookingForm,$bookingId,$pickupDate)
	{	
		$Date=new CHBSDate();
		$User=new CHBSUser();
		$Booking=new CHBSBooking();
		$WooCommerce=new CHBSWooCommerce();

		if(!$WooCommerce->isEnable($bookingForm['meta'])) return(false);
			
		if(!$User->isSignIn()) return(false);
			
		$userId=$User->getUserId();
		
		$userData=$User->getUserBusinessAccountData($userId);

		if((int)$userData['enable']!==1) return(false);
			
		if(!$Date->dateInRange($pickupDate,$userData['date_from'],$userData['date_to'])) return(false);
				
		//$userBooking=$Booking->getUserBooking($userId,array('date_from'=>$userData['date_from'],'date_to'=>$userData['date_to']));
		//$userBookingSum=$Booking->getSumBooking($userBooking);

		$billing=$Booking->createBilling($bookingId);
		
		if($userData['amount']>=$billing['summary']['value_gross']) return(true);
	}
	
	/**************************************************************************/
	
	function updateUserBusinessAccountTransaction($bookingId)
	{
		$Booking=new CHBSBooking();
		
		if(($booking=$Booking->getBooking($bookingId))===false) return(false);
		
		$billing=$Booking->createBilling($bookingId);
		
		$userData=$this->getUserBusinessAccountData($booking['meta']['user_id']);
		
		$transaction=array();
		
		$transaction['booking_id']=$booking['post']->ID;
		$transaction['booking_date']=get_the_date('d-m-Y',$booking['post']->ID);
		$transaction['booking_time']=get_the_time('H:i:s',$booking['post']->ID);
		$transaction['booking_sum']=$billing['summary']['value_gross'];
		$transaction['amount_before_booking']=$userData['amount'];
		$transaction['amount_after_booking']=$userData['amount']-$billing['summary']['value_gross'];		
			
		$userData['transaction'][]=$transaction;
		
		update_user_meta($booking['meta']['user_id'],'business_user_account_transaction',$userData['transaction']);	
		update_user_meta($booking['meta']['user_id'],'business_user_account_amount',$transaction['amount_after_booking']);	
	}

	/**************************************************************************/
	
    function isSignIn()
    {
        $user=$this->getCurrentUserData();
        return((int)$user->id===0 ? false : true);
    }
    
    /**************************************************************************/
    
    function getUserId()
    {
        $user=$this->getCurrentUserData();
        return((int)$user->id);        
    }
    
    /**************************************************************************/
    
    function signOut()
    {
        
    }
    
    /**************************************************************************/
    
    function signIn($login,$password)
    {
        if($this->isSignIn()) $this->signOut();
   
        $credentials=array
        (
            'user_login'        =>  $login,
            'user_password'     =>  $password,
            'remember'          =>  true
        );
 
        $user=wp_signon($credentials,true);

        if(is_wp_error($user)) return(false);
        
        wp_set_current_user($user->ID);
        
        return(true);
    }
    
    /**************************************************************************/
    
    function getCurrentUserData()
    {
        return(wp_get_current_user());
    }
    
    /**************************************************************************/
    
    function validateCreateUser($email,$login,$password1,$password2)
    {
        $result=array();
        
        $Validation=new CHBSValidation();
        
        if(!$Validation->isEmailAddress($email)) $result[]='EMAIL_INVALID';
        else
        {
            if(email_exists($email)) $result[]='EMAIL_EXISTS';
        }
        
        if($Validation->isEmpty($login)) $result[]='LOGIN_INVALID';
        else
        {
            if(username_exists($login)) $result[]='LOGIN_EXISTS';                
        }
        
        if($Validation->isEmpty($password1)) $result[]='PASSWORD1_INVALID';
        if($Validation->isEmpty($password2)) $result[]='PASSWORD2_INVALID';
        
        if((!in_array('PASSWORD1_INVALID',$result)) && (!in_array('PASSWORD2_INVALID',$result)))
        {
            if(strcmp($password1,$password2)!==0)
                $result[]='PASSWORD_MISMATCH'; 
        }

        return($result);
    }
    
    /**************************************************************************/
    
    function createUser($email,$login,$password)
    {
        $data=array
        (
            'user_login'=>$login,
            'user_pass'=>$password,
            'user_email'=>$email,
            'role'=>'customer'
        );
        
        $userId=wp_insert_user($data);
                
        if(!is_wp_error($userId))
            wp_send_new_user_notifications($userId);
        
        return($userId);
    }
    
    /**************************************************************************/
}