<?php

/******************************************************************************/
/******************************************************************************/

class CHBSExtension
{
    /**************************************************************************/
    
	public $extension;
	
	/**************************************************************************/
	
    function __construct()
    {
        $this->extension=array
		(
			1=>array
			(
				'name'=>__('Invoices','chauffer-booking-system'),
				'prefix'=>'INV'
			),
			2=>array
			(
				'name'=>__('Import/Export','chauffer-booking-system'),
				'prefix'=>'IE'
			),
			3=>array
			(
				'name'=>__('Google Analytics Events','chauffer-booking-system'),
				'prefix'=>'GAE'
			),
			4=>array
			(
				'name'=>__('Custom Notifications','chauffer-booking-system'),
				'prefix'=>'CN',
				'cpt'=>array
				(
					array
					(
						'class'=>'CHBSECNCustomNotification',
						'name'=>__('Custom Notifications','chauffer-booking-system')
					)
				)
			),
			5=>array
			(
				'name'=>__('Mailchimp','chauffer-booking-system'),
				'prefix'=>'MC'
			)
		);
    }
	
	/**************************************************************************/
	
	function getExtension()
	{
		return($this->extension);
	}
	
	/**************************************************************************/
	
	function getExtensionClassName($index,$classPostfix=null)
	{
		$extension=$this->getExtension();
		
		$className='CHBSE'.$extension[$index].'Extension'.$classPostfix;
		
		if(class_exists($className)) return($className);
		
		return(false);
	}

	/**************************************************************************/
	
	function getExtensionCPT()
	{
		$extensionCPT=array();
		
		$extension=$this->getExtension();
		
		foreach($extension as $extensionData)
		{
			if((array_key_exists('cpt',$extensionData)) && (is_array($extensionData['cpt'])))
			{
				foreach($extensionData['cpt'] as $data)
				{
					if(class_exists($data['class']))
					{
						$extensionCPT[]=array
						(
							'name'=>$extensionData['name'],
							'cpt_name'=>$data['class']::getCPTName()
						);
					}
				}
			}
		}
		
		return($extensionCPT);
	}
	
    /**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/