<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingCalendar
{
	/**************************************************************************/
	   
	function __construct()
	{
		
	}
	
	/**************************************************************************/
	
	function createHeaderDate($year,$monthName)
	{
		$html=
		'
			<h1>'.esc_html($monthName.' '.$year).'</h1>
			<a href="#">&lt;</a>
			<a href="#">&gt;</a>
		';
		
		return($html);
	}
	
	/**************************************************************************/
	
	function createHeaderBookingStatus()
	{
		$html=null;
		
		/***/
		
		$BookingStatus=new CHBSBookingStatus();
		
		/***/
		
		$dictionary=$BookingStatus->getBookingStatus();
		
		if($dictionary!==false)
		{
			foreach($dictionary as $index=>$value)
				$html.='<option value="'.(int)$index.'">'.esc_html($value[0]).'</option>';
		}
		
		$html=
		'
			<select id="'.CHBSHelper::getFormName('booking_calendar_booking_status_id',false).'" name="'.CHBSHelper::getFormName('booking_calendar_booking_status_id',false).'">
				<option value="-1">'.esc_html__('- All statuses -','chauffeur-booking-system').'</option>
				<option value="-2">'.esc_html__('- New & accepted -','chauffeur-booking-system').'</option>
				<option value="-3">'.esc_html__('- Blocked -','chauffeur-booking-system').'</option>
				'.$html.'
			</select>
		';		

		return($html);
	}
	
	/**************************************************************************/
	
	function createHeaderVehicle()
	{
		$html=null;
		
		/***/
		
		$Vehicle=new CHBSVehicle();
		
		/***/
		
		$dictionary=$Vehicle->getDictionary();
		
		if($dictionary!==false)
		{
			foreach($dictionary as $index=>$value)
				$html.='<option value="'.(int)$index.'">'.esc_html($value['post']->post_title).'</option>';
		}
		
		$html=
		'
			<select id="'.CHBSHelper::getFormName('booking_calendar_vehicle_id',false).'" name="'.CHBSHelper::getFormName('booking_calendar_vehicle_id',false).'">
				<option value="-1">'.esc_html__('- All vehicles -','chauffeur-booking-system').'</option>
				'.$html.'
			</select>
		';		

		return($html);
	}
	
	/**************************************************************************/
	
	function getDate()
	{
		$DateTime=new DateTime();
		$Validation=new CHBSValidation();
		
		/***/
		
		$year=null;
		$month=null;
	
		/***/
		
		$data=CHBSHelper::getPostOption();
		
		if(array_key_exists('booking_calendar_year_number',$data))
			$year=$data['booking_calendar_year_number'];	
		if(array_key_exists('booking_calendar_month_number',$data))
			$month=$data['booking_calendar_month_number'];		
		
		/***/
		
		if(($Validation->isNotEmpty($year)) && ($Validation->isNotEmpty($month)))
			$DateTime->setDate($year,$month,1);
		
		/***/
		
		$response=array();
		
		$response['booking_calendar_year_number']=$DateTime->format('Y');
		$response['booking_calendar_month_number']=$DateTime->format('m');
		$response['booking_calendar_month_name']=$DateTime->format('F');
		
		return($response);
	}

	/**************************************************************************/
	
	function createBookingCalendar($year,$month,$bookingStatusId=-1,$vehicleId=-1)
	{
		$html=null;
		
		$Date=new CHBSDate();
		$Validation=new CHBSValidation();
		
		/***/
		
		$booking=$this->getBooking($year,$month,$bookingStatusId,$vehicleId);
			
		/***/
		
		$headerCounterStart=CHBSOption::getOption('first_day_week');
		$headerCounterStop=$headerCounterStart+7;
		
		$tableHeadHtml=null;
		for($i=$headerCounterStart;$i<$headerCounterStop;$i++)
		{
			$counter=$i<8 ? $i : $i-7;
			$tableHeadHtml.='<th><div>'.esc_html($Date->day[$counter][0]).'</div></th>';
		}
		
		/***/
		
		$cellCounter=0;
		
		$tableCellHtml=null;
		$tableBodyHtml=null;
		
		$DateTime=new DateTime('01-'.$month.'-'.$year);
		$DateCurrent=new DateTime();
		
		
		for($i=1;$i<=31;$i++)
		{
			$dayNumber=(int)$DateTime->format('d');
			$dayOfWeekNumber=$DateTime->format('N');
			
			if($i===1)
			{
				$cellCounterStop=($dayOfWeekNumber-CHBSOption::getOption('first_day_week')+7)%7;
				
				for($j=1;$j<=$cellCounterStop;$j++) 
					$tableCellHtml.='<td><div></div></td>';
				
				$cellCounter+=$j-1;
			}
			
			$value=array(0,0,0);
			
			if(isset($booking[$dayNumber]['pickup']['count']))
				$value[0]=(int)$booking[$dayNumber]['pickup']['count'];
			
			if(isset($booking[$dayNumber]['return']['count']))
				$value[0]+=(int)$booking[$dayNumber]['return']['count'];

			/***/
			
			$timeHtml=null;
			$moreLinkHtml=null;
			
			$timeSlotSum=0;
			$timeSlotLimit=5;
			$timeSlotCount=0;
			
			$dayURL=admin_url('edit.php?post_type='.CHBSBooking::getCPTName().'&chbs_booking_status_id='.(int)$bookingStatusId.'&chbs_pickup_date_from='.esc_attr($Date->formatDateToDisplay($DateTime->format('d-m-Y'))).'&chbs_pickup_date_to='.esc_attr($Date->formatDateToDisplay($DateTime->format('d-m-Y'))));
			
			if(isset($booking[$dayNumber]['pickup']['count']))
			{
				$timeSlotSum+=$booking[$dayNumber]['pickup']['count'];
				
				foreach($booking[$dayNumber]['pickup']['detail'] as $detailIndex=>$detailValue)
				{
					if($timeSlotCount===$timeSlotLimit) break;
					$timeSlotCount++;
					
					$timeHtml.='<li><a href="'.esc_url(CHBSHelper::editPostURLAddress($detailValue['post_id'])).'" title="'.esc_attr($detailValue['vehicle_name']).'" target="_blank">'.esc_html($Date->formatTimeToDisplay($detailValue['time']).': '.$detailValue['vehicle_name']).'</a></li>';
				}
			}
			
			if(isset($booking[$dayNumber]['return']['count']))
			{
				$timeSlotSum+=$booking[$dayNumber]['return']['count'];
				
				foreach($booking[$dayNumber]['return']['detail'] as $detailIndex=>$detailValue)
				{
					if($timeSlotCount===$timeSlotLimit) break;
					$timeSlotCount++;
					
					$timeHtml.='<li><a href="'.esc_url(CHBSHelper::editPostURLAddress($detailValue['post_id'])).'" title="'.esc_attr($detailValue['vehicle_name']).'" target="_blank">'.esc_html($Date->formatTimeToDisplay($detailValue['time']).': '.$detailValue['vehicle_name']).'</a></li>';
				}
			}				
			
			if($Validation->isNotEmpty($timeHtml))
			{
				$timeHtml='<div><ul>'.$timeHtml.'</ul></div>';
				
				$d=$timeSlotSum-$timeSlotCount;
				
				if($d>0)
				{
					$moreLinkHtml='<div><a href="'.esc_url($dayURL).'" target="_blank">'.sprintf(esc_html__('+ %s more','chauffeur-booking-system'),($timeSlotSum-$timeSlotCount)).'</a></div>';
				}
			}
			
			/***/

			$class=array();
			
			if($DateTime->format('d-m-Y')==$DateCurrent->format('d-m-Y'))
				array_push($class,'to-booking-calendar-today');
	
			$tableCellHtml.=
			'
				<td>
					<div>
						<div><a href="'.esc_url($dayURL).'" target="_blank"><span'.CHBSHelper::createCSSClassAttribute($class).'>'.$DateTime->format('d').'</span></a></div>
						'.$timeHtml.'
						'.$moreLinkHtml.'
					</div>
				</td>
			';
			
			$cellCounter++;
			
			$DateTime->modify('+1 day');
			
			$break=false;
			
			if((int)$DateTime->format('n')!==(int)$month)
			{
				$break=true;
				
				if($cellCounter%7!==0)
				{
					$maxCellCounter=(floor($cellCounter/7)+1)*7;
				
					for($j=$cellCounter;$j<$maxCellCounter;$j++) $tableCellHtml.='<td><div></div></td>';
				
					$cellCounter=$maxCellCounter;
				}
			}
			
			if($cellCounter%7===0)
			{
				$tableBodyHtml.='<tr>'.$tableCellHtml.'</tr>';
				$tableCellHtml=null;
			}
			
			if($break) break;
		}
		
		/***/
		
		$html.=
		'
			<table cellspacing="0px" cellpadding="0px">
				<thead>
					'.$tableHeadHtml.'
				</thead>
				<tbody>
					'.$tableBodyHtml.'
				</tbody>
			</table>
		';
			
		/***/
		
		return($html);
	}
	
	/**************************************************************************/
	
	function getBooking($year,$month,$bookingStatusId=-1,$vehicleId)
	{
		global $post;
		
		$data=array();
		
		$DateTime=new DateTime('01-'.$month.'-'.$year);
		$DateTime->modify('+1 month');
		
		/***/
		
		$metaQuery=array();
		
		$argument=array
		(
			'post_type'=>CHBSBooking::getCPTName(),
			'post_status'=>'publish',
			'posts_per_page'=>-1,
			'suppress_filters'=>true,
			'meta_key'=>PLUGIN_CHBS_CONTEXT.'_pickup_datetime',
			'meta_type'=>'DATETIME',
			'orderby'=>'meta_value',
			'order'=>'asc'
		);
		
		array_push($metaQuery,array
		(
			array
			(
				'key'=>PLUGIN_CHBS_CONTEXT.'_woocommerce_product_id',
				'value'=>array(0),
				'compare'=>'IN'
			)		
		));
		
		$argument['meta_query']=$metaQuery;
		
		/***/
		
		$query=new WP_Query($argument);	
		if($query===false) return(false);
		
		while($query->have_posts())
		{
			$query->the_post();
			
			$meta=CHBSPostMeta::getPostMeta($post);
			
			if($bookingStatusId!=-1)
			{
				switch($bookingStatusId)
				{
					case -3:
						
						$status=CHBSOption::getOption('booking_status_nonblocking');
						if(is_array($status))
						{
							if(in_array($meta['booking_status_id'],$status)) continue 2;
						}						
						
					break;
				
					case -2:
						
						if(!in_array($meta['booking_status_id'],array(1,2))) continue 2;
						
					break;
								
					default:
						
						if((int)$meta['booking_status_id']!==(int)$bookingStatusId) continue 2;
				}
			}
			
			if($vehicleId!=-1)
			{
				if((int)$meta['vehicle_id']!==(int)$vehicleId) continue;
			}
			
			$DatePickup=new DateTime($meta['pickup_datetime']);
			$DateReturn=new DateTime($meta['return_datetime']);
			
			$key=null;
			
			$b=((int)$month===(int)$DatePickup->format('n')) && ((int)$year===(int)$DatePickup->format('Y'));
			if($b) $key='pickup';
	
			$b=((int)$month===(int)$DateReturn->format('n')) && ((int)$year===(int)$DateReturn->format('Y'));
			if($b) $key='return';
			
			$data[$DatePickup->format('j')][$key]['detail'][]=array('post_id'=>$post->ID,'time'=>$meta['pickup_time'],'vehicle_id'=>$meta['vehicle_id'],'vehicle_name'=>$meta['vehicle_name']);
		}	
	
		for($i=1;$i<=31;$i++)
		{
			if(!isset($data[$i])) continue;
			
			if(isset($data[$i]['pickup'])) 
				$data[$i]['pickup']['count']=count($data[$i]['pickup']['detail']);
			if(isset($data[$i]['return'])) 
				$data[$i]['return']['count']=count($data[$i]['return']['detail']);
		}

		return($data);
	}
	
	/**************************************************************************/
	
	function ajax()
	{
		if(CHBSHelper::verifyNonce('booking_calendar')===false) return(false);
		
		$response=array();
	
		$data=CHBSHelper::getPostOption();
		
		$date=$this->getDate();
			
		$response['calendar']=$this->createBookingCalendar($date['booking_calendar_year_number'],$date['booking_calendar_month_number'],$data['booking_calendar_booking_status_id'],$data['booking_calendar_vehicle_id']);
		
		$response['booking_calendar_header']=$date['booking_calendar_month_name'].' '.$date['booking_calendar_year_number'];
		
		CHBSHelper::createJSONResponse($response);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/