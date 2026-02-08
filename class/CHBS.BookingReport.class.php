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
				<input type="text" id="'.CHBSHelper::getFormName('booking_report_form_date_from',false).'" placeholder="'.esc_html('Pickup/return date (from)','chauffeur-booking-system').'" name="'.CHBSHelper::getFormName('booking_report_form_date_from',false).'" class="to-datepicker-custom" value="'.esc_attr(CHBSHelper::getGetValue('booking_report_form_date_from')).'" placeholder="'.esc_html__('From:','chauffeur-booking-system').'"/>
				<input type="text" id="'.CHBSHelper::getFormName('booking_report_form_date_to',false).'" placeholder="'.esc_html('Pickup/return date (to)','chauffeur-booking-system').'" name="'.CHBSHelper::getFormName('booking_report_form_date_to',false).'" class="to-datepicker-custom" value="'.esc_attr(CHBSHelper::getGetValue('booking_report_form_date_to')).'" placeholder="'.esc_html__('To:','chauffeur-booking-system').'"/>
				<button class="to-booking-report-form-generate button">'.esc_html__('Generate','chauffeur-booking-system').'</button>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($)
				{
					toCreateCustomDateTimePicker();				

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
		
		if($Validation->isNotEmpty($dateFrom))
			$dateFrom=$Date->formatDateToStandard($dateFrom);
		else $dateFrom='00-00-0000';
		
		if($Validation->isNotEmpty($dateTo))
			$dateTo=$Date->formatDateToStandard($dateTo);
		else $dateTo='31-12-9999';
		
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
			
			$c=array(false,false);
			
			$c[0]=$Date->dateInRange($booking['meta']['pickup_date'],$dateFrom,$dateTo);
			
			if((in_array($booking['meta']['service_type_id'],array(1,3))) && ((int)$booking['meta']['transfer_type_id']===3))
			{
				$c[1]=$Date->dateInRange($booking['meta']['return_date'],$dateFrom,$dateTo);
			}
			
			if(!in_array(true,$c,true)) continue;
			
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
			
			foreach($data as $dataIndex=>$dataValue)
				$data[$dataIndex]=preg_replace('/\s+/',' ',$dataValue);
		
			$document.=implode(chr(9),$data)."\r\n";			
		}
		
		CHBSHelper::preservePost($post,$bPost,0);
		
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header('Cache-Control: public');
		header('Content-Type: text/csv');
		header('Content-Transfer-Encoding: Binary');
		header('Content-Disposition: attachment;filename=booking.csv');
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
			array
			(
				'key'=>PLUGIN_CHBS_CONTEXT.'_woocommerce_product_id',
				'value'=>array(0),
				'compare'=>'IN'
			)		
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