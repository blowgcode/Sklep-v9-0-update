<?php

/******************************************************************************/
/******************************************************************************/

class CHBSFixerIo
{
	/**************************************************************************/
	
	function __construct()
	{
		
	}
	
	/**************************************************************************/
	
	function getRate()
	{		
		$LogManager=new CHBSLogManager();
		
		$url='http://data.fixer.io/api/latest?access_key='.CHBSOption::getOption('fixer_io_api_key').'&base='.CHBSCurrency::getBaseCurrency();
		
		if(($content=file_get_contents($url))===false)
		{
			$LogManager->add('fixerio',1,$content);	
			return(false);
		}
		
		$data=json_decode($content);
		
		if($data->{'success'})
		{
			return($data->{'rates'});
		}
		
		$LogManager->add('fixerio',1,print_r($data,true));	
		
		return(false);
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/