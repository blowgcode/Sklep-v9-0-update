<?php

/******************************************************************************/
/******************************************************************************/

class CHBSEmail
{
	/**************************************************************************/
	
	function __construct()
	{

	}
	
	/**************************************************************************/
	
	function phpMailerInit($mail)
	{
		global $chbs_phpmailer;
		
		$mail->CharSet='UTF-8';
		$mail->SetFrom($chbs_phpmailer['sender_email_address'],$chbs_phpmailer['sender_name']);
		
		if($chbs_phpmailer['smtp_auth_enable'])
		{
			$mail->IsSMTP();
			$mail->SMTPAuth=true; 
			
			if($chbs_phpmailer['smtp_auth_debug_enable']==1) $mail->SMTPDebug=3;
			
			$mail->Username=$chbs_phpmailer['smtp_auth_username'];
			$mail->Password=$chbs_phpmailer['smtp_auth_password'];
			
			$mail->Host=$chbs_phpmailer['smtp_auth_host'];
			$mail->Port=$chbs_phpmailer['smtp_auth_port'];
			
			if($chbs_phpmailer['smtp_auth_secure_connection_type']!='none')
				$mail->SMTPSecure=$chbs_phpmailer['smtp_auth_secure_connection_type'];
		}		
	}
	
	/**************************************************************************/
	
	function send($recipient,$subject,$body)
	{
		global $chbs_phpmailer;
		
		$Validation=new CHBSValidation();
		
		foreach($recipient as $recipientIndex=>$recipientData)
		{
			if(!$Validation->isEmailAddress($recipientData))
				unset($recipient[$recipientIndex]);
		}

		if(!count($recipient)) return;
		if(!$Validation->isEmailAddress($chbs_phpmailer['sender_email_address'])) return;
		
		$header=array();
		$header[]='Content-type: text/html';	
		
		add_action('phpmailer_init',array($this,'phpMailerInit'));
		
		$result=wp_mail($recipient,$subject,$body,$header);
		
		return($result); 
	}
	
	/**************************************************************************/
	
	function getEmailStyle()
	{
		$style=array();
		
		$style['separator'][1]='style="padding:0px;height:45px"';
		$style['separator'][2]='style="padding:0px;height:30px"';
		$style['separator'][3]='style="padding:0px;height:15px"';

		$style['base']='style="font-family:Arial;font-size:15px;color:#777777;line-height:150%;"';
		
		$style['cell'][1]='style="padding:0px 5px 0px 0px;width:250px;vertical-align:top;"';
		$style['cell'][2]='style="padding:0px 0px 0px 5px;width:300px;vertical-align:top;"';
		$style['cell'][3]='style="padding:0px;"';
		
		$style['header']='style="padding:0px 0px 5px 0px;font-weight:bold;color:#444444;border-bottom:dotted 1px #AAAAAA;text-transform:uppercase"';
		
		$style['list'][1]='style="margin:0px;padding:0px;list-style-position:inside;"';
		$style['list'][2]='style="margin:0px;padding:0px;"';
		
		return($style);
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/