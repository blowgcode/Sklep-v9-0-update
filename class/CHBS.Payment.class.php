<?php

/******************************************************************************/
/******************************************************************************/

class CHBSPayment
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->payment=array
		(
			'1'=>array(__('Cash','chauffeur-booking-system'),'cash'),
			'2'=>array(__('Stripe','chauffeur-booking-system'),'stripe'),
			'3'=>array(__('PayPal','chauffeur-booking-system'),'paypal'),
			'4'=>array(__('Wire transfer','chauffeur-booking-system'),'wire_transfer'),
			'5'=>array(__('Credit card on pickup','chauffeur-booking-system'),'credit_card_pickup'),
			'6'=>array(__('Tpay','chauffeur-booking-system'),'tpay'),
		);
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