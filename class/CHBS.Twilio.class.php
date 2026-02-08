<?php

/******************************************************************************/
/******************************************************************************/

class CHBSTwilio
{
	/**************************************************************************/
	
	function __construct()
	{
		
	}
	
	/**************************************************************************/
	
	function sendSMS($apiSid,$apiToken,$senderPhoneNumber,$recipientPhoneNumber,$message)
	{
		$data=array
		(
			'From'=>$senderPhoneNumber,
			'To'=>$recipientPhoneNumber,
			'Body'=>$message
		);

		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,'https://api.twilio.com/2010-04-01/Accounts/'.$apiSid.'/Messages');
		curl_setopt($ch,CURLOPT_USERPWD,$apiSid.':'.$apiToken);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($data));		
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		$response=curl_exec($ch);
		
		$LogManager=new CHBSLogManager();
		$LogManager->add('twilio',1,$response);			 
		
		curl_close($ch);
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/