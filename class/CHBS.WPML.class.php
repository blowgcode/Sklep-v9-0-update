<?php

/******************************************************************************/
/******************************************************************************/

class CHBSWPML
{
	/**************************************************************************/
	
	function __construct()
	{		
		
	}
	
	/**************************************************************************/
	
	function isEnable()
	{
		return(function_exists('icl_object_id'));
	}
	
	/**************************************************************************/
	
	function translateID($data)
	{
		global $sitepress;
		
		if(!$this->isEnable()) return($data);
		if(!is_object($sitepress)) return($data); 
		
		return(apply_filters('wpml_object_id',$data,'chbs_vehicle',true,$sitepress->get_default_language()));
	}
	
	 /**************************************************************************/
	
	function translateDictionary($data)
	{
		global $sitepress;
		$temp=array();
		
		foreach($data as $index=>$value)
		{
			$temp[$this->translateID($index)]=$value;
		}
		return($temp);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/