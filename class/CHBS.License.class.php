<?php

/******************************************************************************/
/******************************************************************************/

class CHBSLicense
{
	private $prefix;
	private $productId;
	
	private $reverifyLifeTime;

	/**************************************************************************/
	
	function __construct($productId=0,$prefix=PLUGIN_CHBS_PREFIX)
	{
		$this->prefix=$prefix;
		$this->productId=$productId;
		
		$this->reverifyLifeTime=3;
	}
	
	/**************************************************************************/
	
	function init()
	{
		add_action('wp_login',array($this,'logIn'));
	}
	
	/**************************************************************************/
	/**************************************************************************/
	
	function verify()
	{
		return(true);
	}
	
	/**************************************************************************/
	
	function setAsVerified($licenseId=null,$licensePurchaseCode=null,$licenseCode=null,$licenseRefreshToken=null,$licenseSupportDateTime=null)
	{
		if($this->prefix!==PLUGIN_CHBS_PREFIX) return;
		
		$optionSave=array('license_license_id'=>$licenseId,'license_purchase_code'=>$licensePurchaseCode,'license_code'=>$licenseCode,'license_refresh_token'=>$licenseRefreshToken,'license_support_datetime'=>$licenseSupportDateTime,'license_last_check_datetime'=>date_i18n('Y-m-d H:i:s'));
		CHBSOption::updateOption($optionSave);
	}
	
	/**************************************************************************/
	
	function setAsUnVerified()
	{
		if($this->prefix!==PLUGIN_CHBS_PREFIX) return;
		
		$optionSave=array('license_id'=>'','license_purchase_code'=>'','license_code'=>'','license_refresh_token'=>'','license_support_datetime'=>'','license_last_check_datetime'=>date_i18n('Y-m-d H:i:s'));
		CHBSOption::updateOption($optionSave);
	}
	
	/**************************************************************************/
	
	function isVerified()
	{
		if($this->prefix!==PLUGIN_CHBS_PREFIX) return(true);
		
		$Validation=new CHBSValidation();
		
		$licenseCode=CHBSOption::getOption('license_code');
		
		if($Validation->isNotEmpty($licenseCode)) return(true);
			
		return(false);
	}
	
	/**************************************************************************/
	/**************************************************************************/
	
	function sendRequest($action)
	{
		$Validation=new CHBSValidation();
		
		$option=CHBSOption::getOptionObject();
		
		if($Validation->isEmpty($option['license_purchase_code'])) return(false);
		
		$LogManager=new CHBSLogManager();
		$LogManager->add('license',1,sprintf(__('Request - action %s','chauffeur-booking-system'),$action));   
		
		/***/
		
		$url='https://quanticalabs.com/.tools/License/license.php';
		
		$data=self::generateVerifyPostField($action);
		
		$data['purchase_code']=$option['license_purchase_code'];
				
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
	
	function processResponse($licenseResponse)
	{
		$status=(int)$licenseResponse->{'license_status'};
		
		if($status<200)
		{
			$this->setAsUnVerified();
		}
		else
		{
			$this->setAsVerified($licenseResponse->{'license_id'},$licenseResponse->{'license_purchase_code'},$licenseResponse->{'license_code'},$licenseResponse->{'license_refresh_token'},$licenseResponse->{'license_support_datetime'});
		}
		
		$LogManager=new CHBSLogManager();
		$LogManager->add('license',1,sprintf(__('Response - status %s','chauffeur-booking-system'),$status));   
	}
	
	/**************************************************************************/
	/**************************************************************************/

	function logIn()
	{
		$Validation=new CHBSValidation();
		
		$licenseResponse=null;
		
		$dateLastCheck=CHBSOption::getOption('license_last_check_datetime');
		
		if($Validation->isEmpty($dateLastCheck))
		{
			$licenseResponse=$this->sendRequest('reverify');
		}
		else
		{
			$Date=new DateTime($dateLastCheck);
			
			$difference=$Date->diff(new DateTime('now')); 
			
			if($difference->format('%a')>=$this->reverifyLifeTime)
			{
				$licenseResponse=$this->sendRequest('reverify');
			}
		}

		if(is_object($licenseResponse))
		{
			$this->processResponse($licenseResponse);
		}
	}
		
	/**************************************************************************/
	
	static function generateVerifyURLAddress($action='verify')
	{
		$data=self::generateVerifyPostField($action);
		
		$url='https://quanticalabs.com/.tools/License/license.php?'.http_build_query($data);
		return($url);
	}
	
	/**************************************************************************/
	
	static function generateVerifyPostField($action)
	{
		$data=array();
		
		$option=CHBSOption::getOptionObject();
				
		$data['action']=$action;
		$data['run_code']=$option['run_code'];
		
		$data['code']=$option['license_code'];
		$data['refresh_token']=$option['license_refresh_token'];
		
		$data['domain']=CHBSPlugin::getDomain();
		$data['product_id']=$option['license_product_id'];
		
		$data['return_url']=site_url();
		
		return($data);
	}

	/**************************************************************************/
	/**************************************************************************/
	
	function isSupportActive()
	{
		$Validation=new CHBSValidation();
		
		$supportDateTime=CHBSOption::getOption('license_support_datetime');
		
		if($Validation->isEmpty($supportDateTime)) return(true);
		
		$DateSupport=new DateTime($supportDateTime);
		
		$difference=$DateSupport->diff(new DateTime('now')); 

		if($difference->format('%a')>0)
		{
			return(true);
		}
		
		return(false);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/