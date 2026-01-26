<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingReport
{
	/**************************************************************************/
	
	function __construct()
	{
		
	}
	
	/**************************************************************************/
	
	function init()
	{
		add_action('wp_loaded',array($this,'generate'));
		add_action('manage_posts_extra_tablenav',array($this,'createForm'));
	}
	
	/**************************************************************************/
	
	function createForm()
	{
		if(!is_admin()) return;
		if(CHBSHelper::getGetValue('post_type',false)!==PLUGIN_CHBS_CONTEXT.'_booking') return;
		
		$output=
		'
			<div id="to-booking-report-form" class="alignleft actions">
				<span>'.esc_html__('Pickup/return date:','chauffeur-booking-system').'</span>
				<input type="text" id="'.CHBSHelper::getFormName('booking_report_form_date_from',false).'" name="'.CHBSHelper::getFormName('booking_report_form_date_from',false).'" class="to-datepicker-custom" value="'.esc_attr(CHBSHelper::getGetValue('booking_report_form_date_from')).'" placeholder="'.esc_html__('From:','chauffeur-booking-system').'"/>
				<input type="text" id="'.CHBSHelper::getFormName('booking_report_form_date_to',false).'" name="'.CHBSHelper::getFormName('booking_report_form_date_to',false).'" class="to-datepicker-custom" value="'.esc_attr(CHBSHelper::getGetValue('booking_report_form_date_to')).'" placeholder="'.esc_html__('To:','chauffeur-booking-system').'"/>
				<button class="to-booking-report-form-generate button">'.esc_html__('Generate','chauffeur-booking-system').'</button>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($)
				{
					var timeFormat=\''.CHBSOption::getOption('time_format').'\';
					var dateFormat=\''.CHBSJQueryUIDatePicker::convertDateFormat(CHBSOption::getOption('date_format')).'\';
				
					toCreateCustomDateTimePicker(dateFormat,timeFormat);				

					$(\'.to-booking-report-form-generate\').on(\'click\',function()
					{
						var dateFrom=$(\'#'.CHBSHelper::getFormName('booking_report_form_date_from',false).'\').val();
						var dateTo=$(\'#'.CHBSHelper::getFormName('booking_report_form_date_to',false).'\').val();
						window.location.href="'.admin_url('edit.php?post_type='.CHBSBooking::getCPTName()).'&chbs_booking_report_form_submit=1&chbs_booking_report_form_date_from="+dateFrom+"&chbs_booking_report_form_date_to="+dateTo;
						return(false);
					});			
				});
			</script>
		';
		
		echo $output;
	}
	
	/**************************************************************************/
	
	function generate()
	{
		if(!is_admin()) return;
		
		$bookingReport=CHBSHelper::getGetValue('booking_report_form_submit');
		if((int)$bookingReport!==1) return;
		
		$Date=new CHBSDate();
		$Booking=new CHBSBooking();
		$Validation=new CHBSValidation();
		
		$dateFrom=CHBSHelper::getGetValue('booking_report_form_date_from');
		$dateTo=CHBSHelper::getGetValue('booking_report_form_date_to');
		
		if(($Validation->isEmpty($dateFrom)) || ($Validation->isEmpty($dateTo))) return;
		
		$dateFrom=$Date->formatDateToStandard($dateFrom);
		$dateTo=$Date->formatDateToStandard($dateTo);

		if((!$Validation->isDate($dateFrom)) || (!$Validation->isDate($dateTo))) return;
		
		$query=$this->getBooking($dateFrom,$dateTo);
		
		if($query===false) return;
		
		global $post;

		CHBSHelper::preservePost($post,$bPost);

		$data=array();
		$document=null;
	
		$data[]=__('ID','chauffeur-booking-system');
		$data[]=__('Status','chauffeur-booking-system');
		$data[]=__('Service type','chauffeur-booking-system');
		$data[]=__('Transfer type','chauffeur-booking-system');
		$data[]=__('Pickup date','chauffeur-booking-system');
		$data[]=__('Pickup time','chauffeur-booking-system');
		$data[]=__('Return date','chauffeur-booking-system');
		$data[]=__('Return time','chauffeur-booking-system');
		$data[]=__('Order total amount','chauffeur-booking-system');
		$data[]=__('Currency','chauffeur-booking-system');
		$data[]=__('Distance','chauffeur-booking-system');
		$data[]=__('Duration','chauffeur-booking-system');
		$data[]=__('Pickup location','chauffeur-booking-system');
		$data[]=__('Drop-off location','chauffeur-booking-system');
		$data[]=__('Route name','chauffeur-booking-system');
		$data[]=__('Extra time','chauffeur-booking-system');
		$data[]=__('Vehicle','chauffeur-booking-system');
		$data[]=__('Driver','chauffeur-booking-system');
		$data[]=__('Client first name','chauffeur-booking-system');
		$data[]=__('Client last name','chauffeur-booking-system');
		$data[]=__('Client e-mail address','chauffeur-booking-system');
		$data[]=__('Client phone number','chauffeur-booking-system');
		$data[]=__('Payment name','chauffeur-booking-system');
		
		$document.=implode(chr(9),$data)."\r\n";
		
		while($query->have_posts())
		{
			$query->the_post();
			
			/***/
			
			$data=array();
			
			$booking=$Booking->getBooking($post->ID);
			$bookingBilling=$Booking->createBilling($post->ID);
			
			CHBSHelper::removeUIndex($booking,'booking_status_name','service_type_name','transfer_type_name','extra_time_value','payment_name');
			
			/***/
			
			if(!$Date->dateInRange($booking['meta']['pickup_date'],$dateFrom,$dateTo)) continue;
			
			/***/
			
			$i=0;
			$pickupLocation=null;
			$dropoffLocation=null;
			
			foreach($booking['meta']['coordinate'] as $value)
			{
				if($i===0) $pickupLocation=$value['address'];
				$dropoffLocation=$value['address'];
				$i++;
			}
			
			/***/
			
			$data[]=$post->ID;
			$data[]=$booking['booking_status_name'];
			$data[]=$booking['service_type_name'];
			$data[]=$booking['transfer_type_name'];
			$data[]=$Date->formatDateToDisplay($booking['meta']['pickup_date']);
			$data[]=$Date->formatTimeToDisplay($booking['meta']['pickup_time']);
			
			if((in_array($booking['meta']['service_type_id'],array(1,3))) && ((int)$booking['meta']['transfer_type_id']===3))
			{
				$data[]=$Date->formatDateToDisplay($booking['meta']['return_date']);
				$data[]=$Date->formatTimeToDisplay($booking['meta']['return_time']);
			}
			else
			{
				$data[]='';
				$data[]='';		
			}
			
			$data[]=$bookingBilling['summary']['value_gross'];
			$data[]=$booking['meta']['currency_id'];
			
			$data[]=$bookingBilling['summary']['distance_s1'];
			$data[]=$bookingBilling['summary']['duration_s1'];
			
			$data[]=$pickupLocation;
			$data[]=$dropoffLocation;
			
			if($booking['meta']['service_type_id']==3)
				$data[]=$booking['meta']['route_name'];
			else $data[]='';
			
			if((in_array($booking['meta']['service_type_id'],array(1,3))) && ($booking['meta']['extra_time_enable']==1))
				$data[]=$Date->formatMinuteToTime($booking['meta']['extra_time_value']);
			else $data[]='';
			
			$data[]=$booking['meta']['vehicle_name'];
			$data[]=$booking['driver_full_name'];
			$data[]=$booking['meta']['client_contact_detail_first_name'];
			$data[]=$booking['meta']['client_contact_detail_last_name'];
			$data[]=$booking['meta']['client_contact_detail_email_address'];
			$data[]=$booking['meta']['client_contact_detail_phone_number'];
			$data[]=$booking['payment_name'];
			
			array_walk($data,function(&$value,&$key)
			{
				$value=preg_replace('/\s+/',' ',$value);
			});
				
			$document.=implode(chr(9),$data)."\r\n";			
		}
		
		CHBSHelper::preservePost($post,$bPost,0);
		
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header('Cache-Control: public');
		header('Content-Type: text/csv');
		header('Content-Transfer-Encoding: Binary');
		header('Content-Disposition: attachment;filename=booking['.$dateFrom.'-'.$dateTo.'].csv');
		echo $document;
		die();
	}
	
	/**************************************************************************/
	
	function getBooking($dateFrom,$dateTo)
	{
		$metaQuery=array();
		
		$argument=array
		(
			'post_type'=>CHBSBooking::getCPTName(),
			'post_status'=>'publish',
			'posts_per_page'=>-1,
			'meta_key'=>PLUGIN_CHBS_CONTEXT.'_pickup_datetime',
			'meta_type'=>'DATETIME',
			'orderby'=>'meta_value'
		);

		array_push($metaQuery,array
		(
			'key'=>PLUGIN_CHBS_CONTEXT.'_woocommerce_product_id',
			'value'=>array(0),
			'compare'=>'IN'
		));	
		
		$argument['meta_query']=$metaQuery;
		
		$query=new WP_Query($argument);
		
		return($query);		
	}
	
	/**************************************************************************/
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/