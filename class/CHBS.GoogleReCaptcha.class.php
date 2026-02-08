<?php

/******************************************************************************/
/******************************************************************************/

class CHBSGoogleReCaptcha
{
	/**************************************************************************/
	
	function verify($bookingForm,$data)
	{
		if(!$this->isEnable($bookingForm)) return(true);

		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,'https://www.google.com/recaptcha/api/siteverify');
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query(array('secret'=>CHBSOption::getOption('google_recaptcha_secret_key'),'response'=>$data['recaptcha_token'])));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$response=curl_exec($ch);
		$responseJSON=json_decode($response,true);
		curl_close($ch);
		
		$LogManager=new CHBSLogManager();
		$LogManager->add('google_recaptcha',1,print_r($responseJSON,true));   
		
		if(((int)$responseJSON['success']===1) && ($responseJSON['score']>=CHBSOption::getOption('google_recaptcha_score')))
		{
			return(true);
		}
		
		return(false);
	}
	
	/**************************************************************************/
	
	function isEnable($bookingForm)
	{
		$Validation=new CHBSValidation();
		
		$b=array_fill(0,3,false);
		
		$b[0]=(int)$bookingForm['meta']['google_recaptcha_enable']===1 ? true : false;
		$b[1]=(int)CHBSOption::getOption('google_recaptcha_enable')===1 ? true : false;
		$b[2]=$Validation->isNotEmpty(CHBSOption::getOption('google_recaptcha_site_key'));
		
		return(in_array(false,$b,true) ? false : true);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/