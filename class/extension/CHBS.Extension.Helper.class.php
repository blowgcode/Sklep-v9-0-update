<?php

/******************************************************************************/
/******************************************************************************/

class CHBSExtensionHelper
{
	/**************************************************************************/
	
	private $extensionPrefix;

	/**************************************************************************/
	
	function __construct($extensionPrefix)
	{
		$this->extensionPrefix=$extensionPrefix;	
	}
	
	/**************************************************************************/
	
	function getExtensionPrefix()
	{
		return($this->extensionPrefix);
	}
	
	/**************************************************************************/
	
	function getPostOption($prefix=null)
	{	
		return(CHBSHelper::getPostOption($prefix,$this->getExtensionPrefix()));
	}
	
	/**************************************************************************/
	
	function getFormName($name,$display=true)
	{
		return(CHBSHelper::getFormName($name,$display,$this->getExtensionPrefix()));
	}
	
	/**************************************************************************/
	
	function getGetValue($name,$prefix=true)
	{
		return(CHBSHelper::getGetValue($name,$prefix,$this->getExtensionPrefix()));
	}
	
	/**************************************************************************/
	
	function getPostValue($name,$prefix=true)
	{
		return(CHBSHelper::getPostValue($name,$prefix,$this->getExtensionPrefix()));
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/