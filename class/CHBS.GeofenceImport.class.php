<?php

/******************************************************************************/
/******************************************************************************/

class CHBSGeofenceImport
{
	/**************************************************************************/
	
	function __construct()
	{
		
	}
	
	/**************************************************************************/
	
	function formatCoordinateToSave($coordinate)
	{
		$c=array();
		
		$Validation=new CHBSValidation();
		
		$coordinate=preg_replace('/0 /','',preg_split('/,/',$coordinate));

		$count=count($coordinate);

		for($i=0;$i<$count;$i+=2)
		{
			$lat=$coordinate[$i+1];
			$lng=$coordinate[$i];
			
			if(($Validation->isNotEmpty($lat)) && ($Validation->isNotEmpty($lng)))
				$c[]=(object)['lat'=>(float)$lat,'lng'=>(float)$lng];	
		}

		return($c);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/