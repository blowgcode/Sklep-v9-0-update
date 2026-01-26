<?php

/******************************************************************************/
/******************************************************************************/

class CHBSExtensionOption
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
	
	function createOption($refresh=false)
	{
		return(CHBSOption::createOption($refresh,$this->getExtensionPrefix()));
	}
		
	/**************************************************************************/
	
	function createOptionObject()
	{	
		return(CHBSOption::createOptionObject($this->getExtensionPrefix()));
	}
	
	/**************************************************************************/
	
	function refreshOption()
	{
		return(CHBSOption::refreshOption($this->getExtensionPrefix()));
	}
	
	/**************************************************************************/
	
	function getOption($name)
	{
		return(CHBSOption::getOption($name,$this->getExtensionPrefix()));
	}

	/**************************************************************************/
	
	function getOptionObject()
	{
		return(CHBSOption::getOptionObject($this->getExtensionPrefix()));
	}
	
	/**************************************************************************/
	
	function updateOption($option)
	{
		return(CHBSOption::updateOption($option,$this->getExtensionPrefix()));
	}
	
	/**************************************************************************/
	
	function resetOption()
	{
		return(CHBSOption::resetOption($this->getExtensionPrefix()));
	}
	
	/**************************************************************************/
	
	function getSalt()
	{
		return(CHBSOption::getSalt($this->getExtensionPrefix()));
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/