<?php

/******************************************************************************/
/******************************************************************************/

class CHBSDate
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->day=array();
		
		for($i=1;$i<8;$i++)
		{
			$this->day[$i]=array(date_i18n('l',CHBSDate::strtotime('0'.$i.'-04-2013')));
		}		
	}
	
	/**************************************************************************/
	
	function getDayName($number)
	{
		return($this->day[$number][0]);
	}
	
	/**************************************************************************/
	
	function compareTime($time1,$time2)
	{
		$time1=array_map('intval',preg_split('/:/',$time1));
		$time2=array_map('intval',preg_split('/:/',$time2));

		if($time1[0]>$time2[0]) return(1);

		if($time1[0]==$time2[0])
		{
			if($time1[1]>$time2[1]) return(1);
			if($time1[1]==$time2[1]) return(0);
		}
		
		return(2);
	}
	
	/**************************************************************************/
	
	function compareDate($date1,$date2)
	{
		$date1=CHBSDate::strtotime($date1);
		$date2=CHBSDate::strtotime($date2);
		
		if($date1-$date2==0) return(0);
		if($date1-$date2>0) return(1);
		if($date1-$date2<0) return(2);
	}

	/**************************************************************************/
	
	function reverseDate($date)
	{
		$Validation=new CHBSValidation();
		
		if($Validation->isEmpty($date)) return('');
		
		$date=preg_split('/-/',$date);
		return($date[2].'-'.$date[1].'-'.$date[0]);
	}
	
	/**************************************************************************/
	
	function dateInRange($date1,$date2,$date3)
	{
	   return((in_array($this->compareDate($date1,$date2),array(0,1))) && (in_array($this->compareDate($date1,$date3),array(0,2))));
	}
	
	/**************************************************************************/
	
	function timeInRange($time1,$time2,$time3)
	{
	   return((in_array($this->compareTime($time1,$time2),array(0,1))) && (in_array($this->compareTime($time1,$time3),array(0,2))));
	}
  
	/**************************************************************************/

	function getDayNumberOfWeek($date)
	{
		return(date_i18n('N',CHBSDate::strtotime($date)));
	}
	
	/**************************************************************************/
	
	function formatTime($time)
	{
		return(number_format($time,2,':',''));
	}
	
	/**************************************************************************/
	
	function formatMinuteToTime($minute)
	{
		$hour=floor($minute/60);
		$minute=($minute%60);
		
		if(strlen($hour)==1) $hour='0'.$hour;
		if(strlen($minute)==1) $minute='0'.$minute;
		
		return($hour.':'.$minute);
	}
	
	/**************************************************************************/
	
	function formatDateToStandard($date)
	{
		$Validation=new CHBSValidation();
		if($Validation->isEmpty($date)) return('');
        
        $date=date_create_from_format(CHBSOption::getOption('date_format'),$date);
        if($date===false) return('');
		
		return(date_format($date,'d-m-Y'));
	}
	
	/**************************************************************************/
	
	function formatDateToDisplay($date,$sourceFormat='d-m-Y')
	{
		$Validation=new CHBSValidation();
		if($Validation->isEmpty($date)) return('');
		
		if(($date=='00-00-0000') && ($sourceFormat=='d-m-Y')) return($date);
		if(($date=='0000-00-00') && ($sourceFormat=='Y-m-d')) return($date);
		
        $date=date_create_from_format($sourceFormat,$date);
		
        if($date===false) return('');
        
		return(date_format($date,CHBSOption::getOption('date_format')));
	}
	
	/**************************************************************************/
	
	function formatTimeToStandard($time)
	{
		$Validation=new CHBSValidation();
		if($Validation->isEmpty($time)) return('');
		
		if(($index=strpos($time,' - '))!==false)
			$time=substr($time,0,$index);
		
		if($Validation->isTime($time)) return($time);
        
        $time=date_create_from_format(CHBSOption::getOption('time_format'),$time);
        if($time===false) return('');
        
		return(date_format($time,'H:i'));
	}
	
	/**************************************************************************/
	
	function formatTimeToDisplay($time,$sourceFormat='H:i')
	{
		$Validation=new CHBSValidation();
		if($Validation->isEmpty($time)) return('');
		
		$time=date_create_from_format($sourceFormat,$time);
		if($time===false) return('');
		
		return(date_format($time,CHBSOption::getOption('time_format')));
	}
	
	/**************************************************************************/
	
	static function formatDateTimeToMySQL($date,$time)
	{
		$date=explode('-',$date);
		return($date[2].'-'.$date[1].'-'.$date[0].' '.$time.':00');
	}
	
	/**************************************************************************/
	
	static function getNow()
	{
		return(strtotime(date_i18n('d-m-Y H:i')));
	}
	
	/**************************************************************************/
	
	static function strtotime($time)
	{
		return(strtotime($time,self::getNow()));
	}
	
	/**************************************************************************/
	
	static function setExcludeTime($time)
	{
		$excludeTime=array();

		for($i=1;$i<=7;$i++)
		{
			if(is_array($time[$i]['hour']))
			{
				$excludeTime[$i][]=array(strtotime('01-01-1970 0:00'),null);
				
				foreach($time[$i]['hour'] as $index=>$value)
				{
					$excludeTime[$i][]=array(strtotime('01-01-1970 '.$value),null);
				}
				
				$excludeTime[$i][]=array(strtotime('02-01-1970 00:00'),null);

				sort($excludeTime[$i]);
			}
		}

		for($i=1;$i<=7;$i++)
		{
			if(!isset($excludeTime[$i])) continue;
			
			foreach($excludeTime[$i] as $index=>$value)
			{
				if($index===0)
				{
					$st=$excludeTime[1][$index][0];
					$sp=$excludeTime[$i][$index+1][0]-60;
					
					$excludeTime[$i][$index]=array($st,$sp);
				}
				elseif($index<count($excludeTime[$i])-1)
				{
					$st=$excludeTime[$i][$index][0]+60;
					$sp=$excludeTime[$i][$index+1][0]-60;	
					
					$excludeTime[$i][$index]=array($st,$sp);
				}
				else
				{
					$st=-1;
					$sp=-1;
					
					unset($excludeTime[$i][$index]);
				}
			}	
		}
		
		for($i=1;$i<=7;$i++)
		{
			if(!isset($excludeTime[$i])) continue;		
			
			foreach($excludeTime[$i] as $index=>$value)
			{
				if($value[0]>=$value[1])
				{
					unset($excludeTime[$i][$index]);
					continue;
				}
				
				$excludeTime[$i][$index]=array(date_i18n('g:ia',$value[0]),date_i18n('g:ia',$value[1]+60));
			}
			
			$excludeTime[$i]=array_values($excludeTime[$i]);
		}
		
		return($excludeTime);
	}
	
	/**************************************************************************/
	
	function getDateDifferenceInDay($date1,$date2)
	{
		$difference=(int)(strtotime($date1)-strtotime($date2))/60/60/24;
		return($difference);
	}
	
	/**************************************************************************/
	
	static function fillTime($time,$zeroCount=2)
	{
		$Validation=new CHBSValidation();
		
		if($Validation->isEmpty($time)) return;
		
		if(!preg_match('/\:/',$time)) $time.=':';
		
		$tTime=array_map('intval',preg_split('/:/',$time));
		
		$tTime[0]=str_pad($tTime[0],$zeroCount,'0',STR_PAD_LEFT); 
		$tTime[1]=str_pad($tTime[1],2,'0',STR_PAD_LEFT); 
		
		return($tTime[0].':'.$tTime[1]);
	}
	
	/**************************************************************************/
	
	static function convertTimeDurationToMinute($timeDuration)
	{
		$timeDuration=self::fillTime($timeDuration);
		
		$timeDuration=preg_split('/:/',$timeDuration);
		
		return(($timeDuration[0]*60)+$timeDuration[1]);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/