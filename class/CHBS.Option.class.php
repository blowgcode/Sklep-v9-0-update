<?php

/******************************************************************************/
/******************************************************************************/

class CHBSOption
{
	/**************************************************************************/
	
	static function createOption($refresh=false,$prefix='chbs')
	{
		$constant=CHBSConstant::get('context',false,$prefix);
		
		$instance=$prefix=='chbs' ? 'CHBSOption' : new CHBSExtensionOption($prefix);
		
		return(CHBSGlobalData::setGlobalData($constant,array($instance,'createOptionObject'),$refresh));	
	}
		
	/**************************************************************************/
	
	static function createOptionObject($prefix='chbs')
	{	
		$constant=CHBSConstant::get('option_prefix',true,$prefix);	
		
		return((array)get_option($constant.'_option'));
	}
	
	/**************************************************************************/
	
	static function refreshOption($prefix='chbs')
	{
		return(self::createOption(true,$prefix));
	}
	
	/**************************************************************************/
	
	static function getOption($name,$prefix='chbs')
	{
		global $chbsGlobalData;

		self::createOption(false,$prefix);
		
		$constant=CHBSConstant::get('context',false,$prefix);

		if(!array_key_exists($name,$chbsGlobalData[$constant])) return(null);
		return($chbsGlobalData[$constant][$name]);		
	}

	/**************************************************************************/
	
	static function getOptionObject($prefix='chbs')
	{
		global $chbsGlobalData;
		
		$constant=CHBSConstant::get('context',false,$prefix);
		
		if(array_key_exists($constant,$chbsGlobalData))
			return($chbsGlobalData[$constant]);
		else return(array());
	}
	
	/**************************************************************************/
	
	static function updateOption($option,$prefix='chbs')
	{
		$nOption=array();
		foreach($option as $index=>$value) $nOption[$index]=$value;
		
		$oOption=self::refreshOption($prefix);
		
		$constant=CHBSConstant::get('option_prefix',true,$prefix);
		
		update_option($constant.'_option',array_merge($oOption,$nOption));
		
		self::refreshOption($prefix);
	}
	
	/**************************************************************************/
	
	static function resetOption($prefix='chbs')
	{
		$constant=CHBSConstant::get('option_prefix',true,$prefix);
		
		update_option($constant.'_option',array());
		self::refreshOption($prefix);		
	}
	
	/**************************************************************************/
	
	static function getSalt($prefix='chbs',$index=1)
	{
		$Validation=new CHBSValidation();
		
		$key='salt_'.$index;
		
		$salt=self::getOption($key,$prefix);
		
		if($Validation->isEmpty($salt))
		{
			$option[$key]=CHBSHelper::createSalt();
			
			self::updateOption($option,$prefix);
			
			$salt=$option[$key];
		}
		
		return($salt);
	}
	
	/**************************************************************************/
	
	static function changeOptionName($name,$option)
	{
		foreach($name as $nameOld=>$nameNew)
		{
			if(array_key_exists($nameOld,$option))
			{
				$option[$nameNew]=$option[$nameOld];
				unset($option[$nameOld]);
			}
		}
		
		return($option);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/