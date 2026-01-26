<?php

/******************************************************************************/
/******************************************************************************/

class CHBSGlobalData
{
	/**************************************************************************/
	
	static function setGlobalData($name,$functionCallback,$refresh=false)
	{
		global $chbsGlobalData;
		
		if(isset($chbsGlobalData[$name]) && (!$refresh)) return($chbsGlobalData[$name]);
		
		if(is_callable($functionCallback)) $chbsGlobalData[$name]=call_user_func($functionCallback);
		else $chbsGlobalData[$name]=$functionCallback;
		
		return($chbsGlobalData[$name]);
	}
	
	/**************************************************************************/
	
	static function isSetGlobalData($name)
	{
		global $chbsGlobalData;
		return(isset($chbsGlobalData[$name]));
	}
	
	/**************************************************************************/
	
	static function getGlobalData($name)
	{
		global $chbsGlobalData;
		
		if((is_array($chbsGlobalData)) && (isset($chbsGlobalData[$name]))) return($chbsGlobalData[$name]);
		
		return(null);
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/