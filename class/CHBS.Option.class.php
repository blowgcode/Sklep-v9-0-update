<?php

/******************************************************************************/
/******************************************************************************/

class CHBSOption
{
	/**************************************************************************/
	
	static function createOption($refresh=false)
	{
		return(CHBSGlobalData::setGlobalData(PLUGIN_CHBS_CONTEXT,array('CHBSOption','createOptionObject'),$refresh));				
	}
		
	/**************************************************************************/
	
	static function createOptionObject()
	{	
		return((array)get_option(PLUGIN_CHBS_OPTION_PREFIX.'_option'));
	}
	
	/**************************************************************************/
	
	static function refreshOption()
	{
		return(self::createOption(true));
	}
	
	/**************************************************************************/
	
	static function getOption($name)
	{
		global $chbsGlobalData;

		self::createOption();

		if(!array_key_exists($name,$chbsGlobalData[PLUGIN_CHBS_CONTEXT])) return(null);
		return($chbsGlobalData[PLUGIN_CHBS_CONTEXT][$name]);		
	}

	/**************************************************************************/
	
	static function getOptionObject()
	{
		global $chbsGlobalData;
		return($chbsGlobalData[PLUGIN_CHBS_CONTEXT]);
	}
	
	/**************************************************************************/
	
	static function updateOption($option)
	{
		$nOption=array();
		foreach($option as $index=>$value) $nOption[$index]=$value;
		
		$oOption=self::refreshOption();

		update_option(PLUGIN_CHBS_OPTION_PREFIX.'_option',array_merge($oOption,$nOption));
		
		self::refreshOption();
	}
	
	/**************************************************************************/
	
	static function resetOption()
	{
		update_option(PLUGIN_CHBS_OPTION_PREFIX.'_option',array());
		self::refreshOption();		
	}
	
	/**************************************************************************/
	
	static function getSalt()
	{
		$Validation=new CHBSValidation();
		
		$salt=self::getOption('salt');
		
		if($Validation->isEmpty($salt))
		{
			$option['salt']=CHBSHelper::createSalt();
			
			self::updateOption($option);
			
			$salt=$option['salt'];
		}
		
		return($salt);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/