<?php

/******************************************************************************/
/******************************************************************************/

class CHBSNexmo
{
	/**************************************************************************/
	
	function __construct()
	{
		
	}
	
	/**************************************************************************/
	
	function sendSMS($apiKey,$apiKeySecret,$senderName,$recipientPhoneNumber,$message)
	{
		$data=array();
		
		$data['api_key']=$apiKey;
		$data['api_secret']=$apiKeySecret;
		
		$data['from']=$senderName;
		$data['to']=$recipientPhoneNumber;
		
		$data['text']=$message;
		
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,'https://rest.nexmo.com/sms/json');
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($data));		
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		$response=curl_exec($ch);
		
		$LogManager=new CHBSLogManager();
		$LogManager->add('nexmo',1,print_r(json_decode($response),true));		

		curl_close($ch);
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/