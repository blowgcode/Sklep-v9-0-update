<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingFormStyle
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->color=array
		(
			1=>array
			(
				'color'=>'FF700A',
				'header'=>'',
				'subheader'=>''
			),
			2=>array
			(
				'color'=>'F6F6F6',
				'header'=>'',
				'subheader'=>''
			),
			3=>array
			(
				'color'=>'FFFFFF',
				'header'=>'',
				'subheader'=>''
			),
			4=>array
			(
				'color'=>'778591',
				'header'=>'',
				'subheader'=>''
			),
			5=>array
			(
				'color'=>'EAECEE',
				'header'=>'',
				'subheader'=>''
			),
			6=>array
			(
				'color'=>'2C3E50',
				'header'=>'',
				'subheader'=>''
			),
			7=>array
			(
				'color'=>'CED3D9',
				'header'=>'',
				'subheader'=>''
			),
			8=>array
			(
				'color'=>'9EA8B2',
				'header'=>'',
				'subheader'=>''
			),
			9=>array
			(
				'color'=>'556677',
				'header'=>'',
				'subheader'=>''
			)	 
		);
		
		$Validation=new CHBSValidation();
		
		foreach($this->color as $index=>$value)
		{
			if($Validation->isEmpty($value['header']))
				$this->color[$index]['header']=sprintf(__('Color #%s','chauffeur-booking-system'),$index);
			if($Validation->isEmpty($value['subheader']))
				$this->color[$index]['subheader']=sprintf(__('Enter color (in HEX) for a elements in group #%s.','chauffeur-booking-system'),$index);
		}
	}
	
	/**************************************************************************/
	
	function isColor($color)
	{
		return(array_key_exists($color,$this->getColor()));
	}
	
	/**************************************************************************/
	
	function getColor()
	{
		return($this->color);
	}
	
	/**************************************************************************/
	
	function createCSSFile()
	{
		$path=array
		(
			CHBSFile::getMultisiteBlog()
		);
		
		foreach($path as $pathData)
		{
			if(!CHBSFile::dirExist($pathData)) @mkdir($pathData);			
			if(!CHBSFile::dirExist($pathData)) return(false);
		}
				
		/***/
		
		$content=null;
		
		$Validation=new CHBSValidation();
		$BookingForm=new CHBSBookingForm();
		
		$dictionary=$BookingForm->getDictionary(array('suppress_filters'=>true));
		
		foreach($dictionary as $dictionaryIndex=>$dictionaryValue)
		{
			$meta=$dictionaryValue['meta'];

			foreach($this->getColor() as $colorIndex=>$colorValue)
			{
				if((!isset($meta['style_color'][$colorIndex])) || (!$Validation->isColor($meta['style_color'][$colorIndex]))) 
					$meta['style_color'][$colorIndex]=$colorValue['color'];
			}
			
			$data=array();
		
			$data['color']=$meta['style_color'];
			$data['main_css_class']='.chbs-booking-form-id-'.$dictionaryIndex;

			$data['booking_form_id']=$dictionaryIndex;
			
			$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'public/style.php');
		
			$content.=$Template->output();
		}
		
		if($Validation->isNotEmpty($content))
			file_put_contents(CHBSFile::getMultisiteBlogCSS(),$content); 
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/