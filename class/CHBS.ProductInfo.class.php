<?php

/******************************************************************************/
/******************************************************************************/

class CHBSProductInfo
{
	/**************************************************************************/
	
	private $productVersion;
	private $reverifyLifeTime;
	
	/**************************************************************************/
	
	function __construct()
	{		
		$this->reverifyLifeTime=0;
		
		if(CHBSPlugin::isAutoRideTheme())
		{
			$this->productVersion=AUTORIDE_THEME_VERSION;
		}
		else
		{
			$this->productVersion=PLUGIN_CHBS_VERSION;
		}
	}
	
	/**************************************************************************/
	
	function init()
	{
		add_action('wp_login',array($this,'logIn'));
		add_action('admin_notices',array($this,'adminNotice'));	
		
		/***/
		
		$Validation=new CHBSValidation();
		
		$productInfoNewVersion=CHBSOption::getOption('product_info_new_version');
		
		if($Validation->isNotEmpty($productInfoNewVersion))
		{
			if(in_array(version_compare($productInfoNewVersion,$this->productVersion),array(0,-1)))
			{
				CHBSOption::updateOption(array('product_info_new_version'=>''));
			}
		}
	}
	
	/**************************************************************************/
	
	function logIn()
	{
		$Validation=new CHBSValidation();

		$productInfoResponse=null;
		
		$dateLastCheck=CHBSOption::getOption('product_info_last_check_datetime');		
	
		if($Validation->isEmpty($dateLastCheck))
		{
			$productInfoResponse=$this->sendRequest();
		}
		else
		{
			$Date=new DateTime($dateLastCheck);
			
			$difference=$Date->diff(new DateTime('now')); 
			
			if($difference->format('%a')>=$this->reverifyLifeTime)
			{
				$productInfoResponse=$this->sendRequest();
			}
		}

		if(is_object($productInfoResponse))
		{		
			if(isset($productInfoResponse->{'version'}))
			{
				CHBSOption::updateOption(array('product_info_last_check_datetime'=>date_i18n('Y-m-d H:i:s')));
				
				if(version_compare($productInfoResponse->{'version'},$this->productVersion)===1)
				{
					CHBSOption::updateOption(array('product_info_new_version'=>$productInfoResponse->{'version'}));
				}
			}
		}
	}
	
	/**************************************************************************/
	
	function sendRequest()
	{
		CHBSOption::updateOption(array('product_info_new_version'=>''));
		
		$option=CHBSOption::getOptionObject();
		
		$LogManager=new CHBSLogManager();
		$LogManager->add('product_info',1,__('Get product info.','chauffeur-booking-system'));   
		
		/***/
		
		$url='https://quanticalabs.com/.tools/ProductInfo/index.php';
		
		$data['product_id']=$option['license_product_id'];
				
		/***/
		
		$ch=curl_init($url);
		
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		
		$response=curl_exec($ch);
				
		if($response!==false)
		{
			$response=json_decode($response);
		}
		
		curl_close($ch);
			
		return($response);
	}
	
	/**************************************************************************/
	
	function adminNotice()
	{
		$Validation=new CHBSValidation();
		
		$version=CHBSOption::getOption('product_info_new_version');
		
		if($Validation->isNotEmpty($version))
		{
			if(CHBSPlugin::isAutoRideTheme())
			{
				echo 
				'
					<div class="notice notice-error is-dismissible">
						<p>
							<b>'.esc_html('AutoRide.','chauffeur-booking-system').'</b> '.sprintf(__('A new version <b>(%s)</b> of the theme is available to <a href="%s" target="_blank">download on ThemeForest</a>.','chauffeur-booking-system'),$version,'https://themeforest.net/downloads').'
						</p>
					</div>
				';				
			}
			else
			{
				echo 
				'
					<div class="notice notice-error is-dismissible">
						<p>
							<b>'.esc_html('Chauffeur Booking System.','chauffeur-booking-system').'</b> '.sprintf(__('A new version <b>(%s)</b> of the plugin is available to <a href="%s" target="_blank">download on CodeCanyon</a>.','chauffeur-booking-system'),$version,'https://codecanyon.net/downloads').'
						</p>
					</div>
				';
			}
		}
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/