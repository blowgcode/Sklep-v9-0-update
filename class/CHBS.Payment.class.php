<?php

/******************************************************************************/
/******************************************************************************/

class CHBSPayment
{
	/**************************************************************************/
	
	public $payment;
	
	/**************************************************************************/
	
	function __construct()
	{
		$this->payment=array
		(
			'1'=>array(__('Cash','chauffeur-booking-system'),'cash',false),
			'2'=>array(__('Stripe','chauffeur-booking-system'),'stripe',true),
			'3'=>array(__('PayPal','chauffeur-booking-system'),'paypal',true),
			'4'=>array(__('Wire transfer','chauffeur-booking-system'),'wire_transfer',false),
			'5'=>array(__('Credit card on pickup','chauffeur-booking-system'),'credit_card_pickup',false),
		);
		
		$this->payment=apply_filters(PLUGIN_CHBS_CONTEXT.'_payment_filter',$this->payment);
	}
	
	/**************************************************************************/
	
	function isPaymentBuiltIn($paymentId)
	{
		return(in_array($paymentId,[1,2,3,4,5]) ? true : false);
	}
	
	/**************************************************************************/
	
	function isPaymentOnline($paymentId)
	{
		return($this->payment[$paymentId][2]);
	}
	
	/**************************************************************************/
	
	function getPayment($payment=null)
	{
		if($payment===null) return($this->payment);
		else return($this->payment[$payment]);
	}
	
	/**************************************************************************/
	
	function isPayment($payment)
	{
		return(array_key_exists($payment,$this->payment));
	}
	  
	/**************************************************************************/
	
	function getPaymentName($payment)
	{
		if(!$this->isPayment($payment)) return;
		return($this->payment[$payment][0]);
	}
	
	/**************************************************************************/
	
	function getPaymentPrefix($payment)
	{
		return($this->payment[$payment][1]);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/