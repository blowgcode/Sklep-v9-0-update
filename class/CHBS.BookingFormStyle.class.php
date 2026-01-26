<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingFormStyle
{
	/**************************************************************************/
	
	public $color;
	
	/**************************************************************************/
	
	function __construct()
	{
		$this->color=array
		(
			1=>array
			(
				'color'=>'FF700A',
				'header'=>'',
				'subheader'=>'',
				'description'=>__('Main color, tab background, active form step background, next step button background, active button background, icons (total distance, total time, colored form field icons), vehicle prices.','chauffeur-booking-system')
			),
			2=>array
			(
				'color'=>'F6F6F6',
				'header'=>'',
				'subheader'=>'',
				'description'=>__('Light gray backgrounds – e.g., summary box background.','chauffeur-booking-system')
			),
			3=>array
			(
				'color'=>'FFFFFF',
				'header'=>'',
				'subheader'=>'',
				'description'=>__('White backgrounds.','chauffeur-booking-system')
			),
			4=>array
			(
				'color'=>'778591',
				'header'=>'',
				'subheader'=>'',
				'description'=>__('Light texts, field labels, form step labels, back button labels, inactive button labels, small icons.','chauffeur-booking-system')
			),
			5=>array
			(
				'color'=>'EAECEE',
				'header'=>'',
				'subheader'=>'',
				'description'=>__('Light bar backgrounds (e.g., Ride Details, Extra Options, Vehicle Filter), inactive form step background, light borders and dividers, back button background, inactive button background.','chauffeur-booking-system')
			),
			6=>array
			(
				'color'=>'2C3E50',
				'header'=>'',
				'subheader'=>'',
				'description'=>__('Dark texts, form field values, headers, payment method labels.','chauffeur-booking-system')
			),
			7=>array
			(
				'color'=>'CED3D9',
				'header'=>'',
				'subheader'=>'',
				'description'=>__('Dark dividers, icons (passengers, suitcases in the vehicle list), inactive days in the calendar, \'Edit\' button borders.','chauffeur-booking-system')
			),
			8=>array
			(
				'color'=>'9EA8B2',
				'header'=>'',
				'subheader'=>'',
				'description'=>__('Very light texts, deposit label.','chauffeur-booking-system')
			),
			9=>array
			(
				'color'=>'556677',
				'header'=>'',
				'subheader'=>'',
				'description'=>__('Error message backgrounds.','chauffeur-booking-system')
			),
			10=>array
			(
				'color'=>'FFFFFF',
				'header'=>'',
				'subheader'=>'',
				'description'=>__('White labels.','chauffeur-booking-system')
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