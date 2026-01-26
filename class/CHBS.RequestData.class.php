<?php

/******************************************************************************/
/******************************************************************************/

class CHBSRequestData
{
	/**************************************************************************/
	
	static function getFromWidget($serviceTypeId,$name,$defaultValue='')
	{
		if((int)$serviceTypeId!==-1)
		{
			if(((int)self::get('service_type_id')!==(int)$serviceTypeId)) return;
		}
		
		return(self::get($name,$defaultValue));
	}
	
	/**************************************************************************/

	static function getCoordinateFromWidget($serviceTypeId,$name)
	{
		if((int)self::get('service_type_id')!==(int)$serviceTypeId) return;
	 
		$Validation=new CHBSValidation();
		
		if(($Validation->isEmpty(self::get($name.'_lng'))) || ($Validation->isEmpty(self::get($name.'_lat')))) return;
		
		$data=array('lat'=>self::get($name.'_lat'),'lng'=>self::get($name.'_lng'),'address'=>self::get($name.'_address'),'zip_code'=>self::get($name.'_zip_code'));
		
		return(json_encode($data,JSON_UNESCAPED_UNICODE));
	}
	
	/**************************************************************************/
	
	static function get($name,$defaultValue='')
	{
		if(array_key_exists($name,$_GET))
			return(CHBSHelper::stripslashes($_GET[$name]));
	
		return;
	}
	
	/**************************************************************************/
	
	static function post($name,$defaultValue='')
	{
		if(array_key_exists($name,$_POST))
			return(CHBSHelper::stripslashes($_POST[$name]));
	
		return;
	}	

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/