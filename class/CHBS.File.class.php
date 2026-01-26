<?php

/******************************************************************************/
/******************************************************************************/

class CHBSFile
{
	/**************************************************************************/
	
	static function scanDir($dir)
	{
		if(!is_dir($dir)) return(false);
		
		$file=scandir($dir);
		
		unset($file[0],$file[1]);
		
		return($file);
	}
	
	/**************************************************************************/
	
	static function fileExist($path)
	{
		return(is_file($path) && file_exists($path));
	}
	
	/**************************************************************************/
	
	static function dirExist($path)
	{
		return(is_dir($path) && file_exists($path));
	}
	
	/**************************************************************************/
	
	static function getMultisiteBlog($type='path')
	{
		$prefix=$type==='path' ? PLUGIN_CHBS_MULTISITE_PATH : PLUGIN_CHBS_MULTISITE_URL;
		return($prefix.get_current_blog_id().'/');
	}
	
	/**************************************************************************/
	
	static function getMultisiteBlogCSS($type='path')
	{
		return(self::getMultisiteBlog($type).'style.css');
	}
	
	/**************************************************************************/
	
	static function getUploadPath()
	{
		return(ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir());
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/