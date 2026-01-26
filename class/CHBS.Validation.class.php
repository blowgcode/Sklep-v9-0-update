<?php

/******************************************************************************/
/******************************************************************************/

class CHBSValidation
{
    public $Notice;

    public function __construct($Notice=null) 
    { 
        $this->Notice = $Notice;
    }

	/**************************************************************************/

	public function isNumber($value,$minValue,$maxValue,$empty=false)
	{
		if(($empty) && ($this->isEmpty($value))) return(true);
		if(!preg_match('/^(-)?[0-9]{1,}$/',$value,$result)) return(false);
		if(!(($value>=$minValue) && ($value<=$maxValue))) return(false);

		return(true);
	}
	
	/**************************************************************************/
	
	public function isFloat($value,$minValue,$maxValue,$empty=false,$decimalNumberCount=2)
	{
		$value=preg_replace('/,/','.',$value);
		
		if($this->isEmpty($value)) return($empty);
		if(floatval($value)!=$value) return(false);
		if(!preg_match('/^(-)?[0-9]+[,\.]*[0-9]{0,'.$decimalNumberCount.'}$/',$value)) return(false);
		if(!(($value>=$minValue) && ($value<=$maxValue))) return(false);
		
		return(true);
	}

	/**************************************************************************/

	public function isEmpty($value)
	{
		return(strlen(trim(strval($value))) ? false : true);
	}
	
	/**************************************************************************/

	public function isNotEmpty($value)
	{
		return(!$this->isEmpty($value));
	}
	
	/**************************************************************************/
	
	public function isEmailAddress($value,$empty=false)
	{
		$value=trim(strtolower($value));
		
		if(($empty) && ($this->isEmpty($value))) return(true);
		if(is_email($value)!==false) return(true);
		
		return(false);
	}
	
	/**************************************************************************/
	
	public function isColor($value,$empty=false)
	{
		if(($empty) && ($this->isEmpty($value))) return(true);

		if($value=='transparent') return(true);
		if(preg_match('/^[a-f0-9]{6,8}$/i',$value)) return(true);

		return(false);
	}
    
	/**************************************************************************/
	
	public function isName($value,$empty=false)
	{
		if(($empty) && ($this->isEmpty($value))) return(true);

		if(preg_match('/^[a-z]{1,}$/',$value)) return(true);

		return(false);
	}
	
	/**************************************************************************/
	
	public function isURL($value,$empty=false)
	{
		if(($empty) && ($this->isEmpty($value))) return(true);
		if(!preg_match('|^(http(s)?://)?[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i',$value)) return(false);
	
		return(true);
	}
	
	/**************************************************************************/
	
	public function isBool($value,$empty=false,$strict=false)
	{
		if(($empty) && ($this->isEmpty($value))) return(true);
		
		if($strict)
			return(in_array($value,array(false,true),true));
		else
			return(in_array($value,array(0,1),false));
	}
	
	/**************************************************************************/
	
public function isTime($value, $empty = false) {
    if ($this->isEmpty($value)) {
        return $empty;
    }

    // Dopasowanie pojedynczej godziny HH:MM
    $singleTimePattern = '/^(2[0-3]|[01][0-9]):([0-5][0-9])$/';

    // Dopasowanie przedziału czasowego HH:MM - HH:MM
    $timeRangePattern = '/^(2[0-3]|[01][0-9]):([0-5][0-9]) - (2[0-3]|[01][0-9]):([0-5][0-9])$/';

    if (preg_match($singleTimePattern, $value) || preg_match($timeRangePattern, $value)) {
        return true;
    }

    return false;
}
	
	/**************************************************************************/
	
	public function isDate($value,$empty=false)
	{
		if(($empty) && ($this->isEmpty($value))) return(true);
		
		$date=preg_split('/-/',$value);
	
		if(isset($date[0],$date[1],$date[2]))
			return(checkdate((int)$date[1],(int)$date[0],(int)$date[2]));
		
		return(false);
	}
    
    /**************************************************************************/
    
    public function isCoordinate($value,$empty=false)
    {
        if(($empty) && ($this->isEmpty($value))) return(true);
    
		$value=preg_replace('/,/','.',$value);
		
        if(!preg_match('/^(\-?\d+(\.\d+)?)\.\s*(\-?\d+(\.\d+)?)$/',$value)) return(false);
        
        return(true);
    }
    
    /**************************************************************************/
    
    public function isCoordinateGroup($coordinate)
    {
        $data=array('lat','lng');
        $coordinate=json_decode($coordinate,true);
 
        if(!is_array($coordinate)) return(false);
        
        foreach($data as $value)
        {
            if(!array_key_exists($value,$coordinate)) return(false);
            if(!$this->isCoordinate($coordinate[$value])) return(false);
        }
        
        return(true);
    }
    
    /**************************************************************************/
    
    public function isPrice($value,$empty=false)
    {
        return($this->isFloat($value,0.00,999999999.999999,$empty,6));
    }
	
	/**************************************************************************/
	
	public function isTimeDuration($value,$empty=false)
	{
		if(($empty) && ($this->isEmpty($value))) return(true);
		
		if(preg_match('/([0-9]{1,4}):([0-5][0-9])/',$value)) return(true);
	
		return(false);
	}
	
	/**************************************************************************/

	public function notice($functionName,$functionArgument,$noticeArgument)
	{
		$result=call_user_func_array(array($this,$functionName),$functionArgument);
		if(!$result) $this->Notice->addError($noticeArgument[0],$noticeArgument[1]);
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/