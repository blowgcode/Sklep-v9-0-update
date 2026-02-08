<?php

/******************************************************************************/
/******************************************************************************/

class CHBSExtensionPostMeta 
{
	/**************************************************************************/
	
	protected $extensionPrefix;

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
	
	function prepareGetPostMeta($post)
	{
		return(CHBSPostMeta::prepareGetPostMeta($post,$this->getExtensionPrefix()));
	}
	
	/**************************************************************************/
	
	function unserialize(&$data,$unserializeIndex)
	{
		return(CHBSPostMeta::unserialize($data,$unserializeIndex));
	}
	
	/**************************************************************************/

	function updatePostMeta($post,$name,$value)
	{
		return(CHBSPostMeta::updatePostMeta($post,$name,$value,$this->getExtensionPrefix()));
	}
	
	/**************************************************************************/
	
	function removePostMeta($post,$name)
	{
		return(CHBSPostMeta::removePostMeta($post,$name,$this->getExtensionPrefix()));
	}
	
	/**************************************************************************/
	
	function createArray(&$array,$index)
	{
		return(CHBSPostMeta::createArray($array,$index));
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/