<?php

/******************************************************************************/
/******************************************************************************/

class CHBSTransferType
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->transferType=array
		(
			'1'=>array(__('One Way','chauffeur-booking-system')),
			'2'=>array(__('Return','chauffeur-booking-system')),
			'3'=>array(__('Return (new ride)','chauffeur-booking-system')),
		);
	}
	
	/**************************************************************************/
	
	function getTransferType($transferType=null)
	{
		if(is_null($transferType)) return($this->transferType);
		else return($this->transferType[$transferType]);
	}
	
	/**************************************************************************/
	
	function isTransferType($transferType)
	{
		return(array_key_exists($transferType,$this->transferType));
	}
	
	/**************************************************************************/
	
	function getTransferTypeName($transferType)
	{
		return($this->transferType[$transferType][0]);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/