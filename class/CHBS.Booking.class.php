<?php

/*******************************************************************************/
/*******************************************************************************/

class CHBSBooking
{
	//************************************************************************* */

	function __construct()
	{
		
	}

	/**************************************************************************/

	public function init()
	{
		$this->registerCPT();
	}

	/**************************************************************************/

	public static function getCPTName()
	{
		return(PLUGIN_CHBS_CONTEXT.'_booking');
	}

	/**************************************************************************/

	private function registerCPT()
	{
		register_post_type
		(
			self::getCPTName(),array
			(
				'labels'=>array
				(
					'name'=>__('Bookings','chauffeur-booking-system'),
					'singular_name'=>__('Booking','chauffeur-booking-system'),
					'edit_item'=>__('Edit Booking','chauffeur-booking-system'),
					'all_items'=>__('Bookings','chauffeur-booking-system'),
					'view_item'=>__('View Booking','chauffeur-booking-system'),
					'search_items'=>__('Search Bookings','chauffeur-booking-system'),
					'not_found'=>__('No Bookings Found','chauffeur-booking-system'),
					'not_found_in_trash'=>__('No Bookings Found in Trash','chauffeur-booking-system'),
					'parent_item_colon'=>'',
					'menu_name'=>__('Chauffeur Booking System','chauffeur-booking-system')
				),
				'public'=>false,
				'menu_icon'=>'dashicons-calendar-alt',
				'show_ui'=>true,
				'capability_type'=>'post',
				'capabilities'=>array
				(
					'create_posts'=>'do_not_allow',
				),
				'map_meta_cap'=>true,
				'menu_position'=>100,
				'hierarchical'=>false,
				'rewrite'=>false,
				'supports'=>array('title','page-attributes')
			)
		);

		add_action('save_post',array($this,'savePost'));

		add_action('add_meta_boxes_'.self::getCPTName(),array($this,'addMetaBox'));
		add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_booking',array($this,'adminCreateMetaBoxClass'));

		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns'));
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));

		add_action('restrict_manage_posts',array($this,'restrictManagePosts'));
		add_filter('parse_query',array($this,'parseQuery'));
	}

	/**************************************************************************/

	function addMetaBox()
	{
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_booking',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_booking_woocommerce',__('WooCommerce','chauffeur-booking-system'),array($this,'addMetaBoxWooCommerce'),self::getCPTName(),'side','low');
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_booking_edit',__('Booking edit','chauffeur-booking-system'),array($this,'addMetaBoxBookingEdit'),self::getCPTName(),'side','low');
	}

	/**************************************************************************/

	function addMetaBoxMain()
	{
		global $post;

		$data=$this->getBooking($post->ID);

		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_booking');

		$data['billing']=$this->createBilling($post->ID);
		
		$data['mode']='booking';

		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_booking.php');
		echo $Template->output();
	}

	/**************************************************************************/

	function addMetaBoxWooCommerce()
	{
		global $post;

		$booking=$this->getBooking($post->ID);

		if((int)$booking['meta']['woocommerce_booking_id']>0)
		{
			echo
			'
				<div>
					<div>'.esc_html__('This booking has corresponding wooCommerce order. Click on button below to see its details in new window.','chauffeur-booking-system').'</div>
					<br/>
					<a class="button button-primary" href="'.esc_url(CHBSHelper::editPostURLAddress($booking['meta']['woocommerce_booking_id'])).'" target="_blank">'.esc_html__('Open booking','chauffeur-booking-system').'</a>
				</div>
			';
		} 
		else
		{
			echo
			'
				<div>
					<div>'.esc_html__('This booking hasn\'t corresponding wooCommerce order.','chauffeur-booking-system').'</div>
				</div>
			';
		}
	}

	/**************************************************************************/

	function addMetaBoxBookingEdit()
	{
		global $post;

		$error=false;
		
		if(($booking=$this->getBooking($post->ID))===false) $error=true;
		
		if(!$error)
		{
			$BookingForm=new CHBSBookingForm();
			
			$dictionary=$BookingForm->getDictionary();
			
			$bookingFormId=$booking['meta']['booking_form_id'];
			
			if(!array_key_exists($bookingFormId,$dictionary)) $error=true;
		}
			
		if(!$error)
		{
			$bookingFormPostId=(int)$dictionary[$bookingFormId]['meta']['booking_form_post_id'];
			
			if($bookingFormPostId<=0) $error=true;			
		}
		
		if(!$error)
		{
			if(($url=get_the_permalink($bookingFormPostId))===false) $error=true;
		}
					
		if(!$error)
		{
			$BookingEdit=new CHBSBookingEdit();
			
			$url.='?'.CHBSHelper::getFormName('booking_edit_id',false).'='.$BookingEdit->getBookingIdHash((int)$post->ID);

			echo
			'
				<div>
					<div>'.esc_html__('Click on below button to start editing this booking.','chauffeur-booking-system').'</div>
					<br/>
					<a class="button button-primary" href="'.esc_url($url).'" target="_blank">'.esc_html__('Edit booking','chauffeur-booking-system').'</a>
				</div>
			';			
		}
		else
		{
			echo
			'
				<div>
					<div>'.esc_html__('This booking cannot be modified, because it cannot be found or booking form cannot be found or post/page ID on which booking form has been placed cannot be found.','chauffeur-booking-system').'</div>
				</div>
			';					
		}
	}
	
	/**************************************************************************/

	function getBookingPaymentName($meta)
	{
		if($meta['woocommerce_enable'])
			return($meta['payment_name']);
		else
		{
			$Payment=new CHBSPayment();
			return($Payment->getPaymentName($meta['payment_id']));
		}
	}

	/**************************************************************************/

	function getBooking($bookingId)
	{
		$post=get_post($bookingId);
		
		if(is_null($post))
			return(false);

		$booking=array();

		$Length=new CHBSLength();
		$Driver=new CHBSDriver();
		$Country=new CHBSCountry();
		$WooCommerce=new CHBSWooCommerce();
		$ServiceType=new CHBSServiceType();
		$BookingForm=new CHBSBookingForm();
		$TransferType=new CHBSTransferType();
		$BookingStatus=new CHBSBookingStatus();

		$booking['post']=$post;
		$booking['meta']=CHBSPostMeta::getPostMeta($post);

		$serviceType=$ServiceType->getServiceType($booking['meta']['service_type_id']);
		$booking['service_type_name']=$serviceType[0];

		if(!$TransferType->isTransferType($booking['meta']['transfer_type_id']))
			$booking['meta']['transfer_type_id']=1;
		$transferType=$TransferType->getTransferType($booking['meta']['transfer_type_id']);
		$booking['transfer_type_name']=$transferType[0];

		if($booking['meta']['client_billing_detail_enable']==1)
		{
			$country=$Country->getCountry($booking['meta']['client_billing_detail_country_code']);
			$booking['client_billing_detail_country_name']=$country[0];
		}

		if(!empty($booking['meta']['payment_id']))
			$booking['payment_name']=$this->getBookingPaymentName($booking['meta']);

		if($BookingStatus->isBookingStatus($booking['meta']['booking_status_id']))
		{
			$bookingStatus=$BookingStatus->getBookingStatus($booking['meta']['booking_status_id']);
			$booking['booking_status_name']=$bookingStatus[0];
		}

		$booking['dictionary']['driver']=$Driver->getDictionary();
		$booking['dictionary']['booking_status']=$BookingStatus->getBookingStatus();

		$booking['woocommerce_payment_url']=null;

		$dictionary=$BookingForm->getDictionary(array('booking_form_id'=>$booking['meta']['booking_form_id']));
		if((count($dictionary)===1) && (array_key_exists($booking['meta']['booking_form_id'],$dictionary)))
		{
			if($booking['meta']['woocommerce_enable']==1)
			{
				if($WooCommerce->isEnable($dictionary[$booking['meta']['booking_form_id']]['meta']))
					$booking['woocommerce_payment_url']=$WooCommerce->getPaymentURLAddress($bookingId+1);
			}

			$booking['booking_form_name']=$dictionary[$booking['meta']['booking_form_id']]['post']->post_title;
		}

		$driverId=$booking['meta']['driver_id'];
		if($driverId>0)
		{
			if(array_key_exists($driverId,$booking['dictionary']['driver']))
			{
				$booking['driver_first_name']=$booking['dictionary']['driver'][$driverId]['meta']['first_name'];
				$booking['driver_last_name']=$booking['dictionary']['driver'][$driverId]['meta']['second_name'];
				
				$booking['driver_full_name']=$booking['dictionary']['driver'][$driverId]['meta']['first_name'].' '.$booking['dictionary']['driver'][$driverId]['meta']['second_name'];
			}
		}

		$booking['vehicle_bag_count']=null;
		$booking['vehicle_passenger_count']=null;

		$vehicleMeta=CHBSPostMeta::getPostMeta($booking['meta']['vehicle_id']);

		if(count($vehicleMeta))
		{
			$booking['vehicle_bag_count']=$vehicleMeta['bag_count'];
			$booking['vehicle_passenger_count']=$vehicleMeta['passenger_count'];
		}
		
		$booking['calculation_method_name']=$BookingForm->calculationMethod[$booking['meta']['calculation_method']][0];

		/***/
		
		$booking['distance_included_format']=null;
		
		$distanceIncluded=CHBSBookingHelper::getDistanceIncluded(null,null,$booking,false);
		
		if($distanceIncluded!==-1)
			$booking['distance_included_format']=$Length->format($distanceIncluded,$booking['meta']['length_unit']);
		
		/***/
		
		return($booking);
	}

	/**************************************************************************/

	function adminCreateMetaBoxClass($class)
	{
		array_push($class,'to-postbox-1');
		return($class);
	}

	/**************************************************************************/

	function sendBooking($data,$bookingForm)
	{
		/***/
		
		$WPML=new CHBSWPML();
		$User=new CHBSUser();
		$Length=new CHBSLength();
		$TaxRate=new CHBSTaxRate();
		$Vehicle=new CHBSVehicle();
		$Validation=new CHBSValidation();
		$WooCommerce=new CHBSWooCommerce();
		$TransferType=new CHBSTransferType();
		$BookingStatus=new CHBSBookingStatus();
		$BookingGratuity=new CHBSBookingGratuity();
		$BookingFormElement=new CHBSBookingFormElement();		
		
		/***/
			
		if(!$WooCommerce->isAddToCartEnable($bookingForm))
		{
			if($bookingForm['booking_edit']->isBookingEdit())
			{
				$bookingOld=$this->getBooking($bookingForm['booking_edit']->bookingEditId);

				/***/

				CHBSPostMeta::updatePostMeta($bookingForm['booking_edit']->bookingEditId,'booking_status_id',3); 

				/***/

				$bookingNew=$this->getBooking($bookingForm['booking_edit']->bookingEditId);

				$emailSend=false;

				$WooCommerce=new CHBSWooCommerce();
				$WooCommerce->changeStatus(-1,$bookingForm['booking_edit']->bookingEditId,$emailSend);

				if(!$emailSend)
					$this->sendEmailBookingChangeStatus($bookingOld,$bookingNew);
			}
		}
		
		/***/
		
		$bookingId=wp_insert_post(array
		(
			'post_type'=>self::getCPTName(),
			'post_status'=>'publish'
		));

		if($bookingId===0) return(false);

		/***/
		
		$bookingTitle=sprintf($bookingForm['meta']['booking_title'],$bookingId);

		wp_update_post(array
		(
			'ID'=>$bookingId,
			'post_title'=>$bookingTitle
		));
		
		$vehicle=$bookingForm['dictionary']['vehicle'][$data['vehicle_id']];
		
		$taxRateDictionary=$TaxRate->getDictionary();

		$passenger=array('enable'=>0,'adult'=>0,'children'=>0);

		if((CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult')) || (CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'children')))
		{
			$passenger['enable']=1;

			if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'adult'))
				$passenger['adult']=$data['passenger_adult_service_type_'.$data['service_type_id']];

			if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id'],'children'))
				$passenger['children']=$data['passenger_children_service_type_'.$data['service_type_id']];
		}

		$userId=$User->isSignIn() ? $userId=$User->getUserId() : 0;
		
		CHBSPostMeta::updatePostMeta($bookingId,'user_id',$userId);
		CHBSPostMeta::updatePostMeta($bookingId,'woocommerce_product_id',0);

		CHBSPostMeta::updatePostMeta($bookingId,'passenger_enable',$passenger['enable']);
		CHBSPostMeta::updatePostMeta($bookingId,'passenger_adult_number',$passenger['adult']);
		CHBSPostMeta::updatePostMeta($bookingId,'passenger_children_number',$passenger['children']);

		CHBSPostMeta::updatePostMeta($bookingId,'hide_fee',$bookingForm['meta']['hide_fee']);
		CHBSPostMeta::updatePostMeta($bookingId,'price_hide',$bookingForm['meta']['price_hide']);
		CHBSPostMeta::updatePostMeta($bookingId,'total_time_display_enable',$bookingForm['meta']['total_time_display_enable']);
		
		CHBSPostMeta::updatePostMeta($bookingId,'passenger_use_person_label',$bookingForm['meta']['passenger_use_person_label']);

		CHBSPostMeta::updatePostMeta($bookingId,'woocommerce_enable',$WooCommerce->isEnable($bookingForm['meta']));

		CHBSPostMeta::updatePostMeta($bookingId,'booking_status_id',$bookingForm['meta']['booking_status_default_id']);

		CHBSPostMeta::updatePostMeta($bookingId,'booking_form_id',$data['booking_form_id']);

		CHBSPostMeta::updatePostMeta($bookingId,'currency_id',CHBSCurrency::getFormCurrency());
		CHBSPostMeta::updatePostMeta($bookingId,'length_unit',CHBSOption::getOption('length_unit'));

		CHBSPostMeta::updatePostMeta($bookingId,'service_type_id',$data['service_type_id']);

		CHBSPostMeta::updatePostMeta($bookingId,'pickup_time_range',$this->getPickupTimeRange($bookingForm));
		
		CHBSPostMeta::updatePostMeta($bookingId,'pickup_time',$data['pickup_time_service_type_'.$data['service_type_id']]);
		CHBSPostMeta::updatePostMeta($bookingId,'pickup_date',$data['pickup_date_service_type_'.$data['service_type_id']]);
		CHBSPostMeta::updatePostMeta($bookingId,'pickup_datetime',CHBSDate::formatDateTimeToMySQL($data['pickup_date_service_type_'.$data['service_type_id']],$data['pickup_time_service_type_'.$data['service_type_id']]));
		
		CHBSPostMeta::updatePostMeta($bookingId,'return_time','00:00');
		CHBSPostMeta::updatePostMeta($bookingId,'return_date','00-00-0000');
		CHBSPostMeta::updatePostMeta($bookingId,'return_datetime',CHBSDate::formatDateTimeToMySQL('00-00-0000','00:00'));

		/***/
		
		$driverId=$vehicle['meta']['driver_id'];
		if(!array_key_exists($driverId,$bookingForm['dictionary']['driver']))
			$driverId=-1;
		
		if($driverId===-1)
		{
			$driverId=$bookingForm['meta']['driver_default_id'];
			if(!array_key_exists($driverId,$bookingForm['dictionary']['driver']))
				$driverId=-1;				
		}
		
		CHBSPostMeta::updatePostMeta($bookingId,'driver_id',$driverId);
		
		/***/
		
		if($data['service_type_id']===2)
			CHBSPostMeta::updatePostMeta($bookingId,'calculation_method',1);
		else
			CHBSPostMeta::updatePostMeta($bookingId,'calculation_method',$bookingForm['meta']['calculation_method_service_type_'.$data['service_type_id']]);

		if(in_array($data['service_type_id'],array(1,3)))
		{
			if($data['transfer_type_service_type_'.(int)$data['service_type_id']]==3)
			{
				CHBSPostMeta::updatePostMeta($bookingId,'return_time',$data['return_time_service_type_'.$data['service_type_id']]);
				CHBSPostMeta::updatePostMeta($bookingId,'return_date',$data['return_date_service_type_'.$data['service_type_id']]);
				CHBSPostMeta::updatePostMeta($bookingId,'return_datetime',CHBSDate::formatDateTimeToMySQL($data['return_date_service_type_'.$data['service_type_id']],$data['return_time_service_type_'.$data['service_type_id']]));
			}
		}

		if(in_array($data['service_type_id'],array(1,3)))
		{
			CHBSPostMeta::updatePostMeta($bookingId,'extra_time_enable',$bookingForm['meta']['extra_time_enable']);

			if($bookingForm['meta']['extra_time_enable']==1)
				CHBSPostMeta::updatePostMeta($bookingId,'extra_time_value',$data['extra_time_service_type_'.$data['service_type_id']]*((int)$bookingForm['meta']['extra_time_unit']===1 ? 1 : 60));

			CHBSPostMeta::updatePostMeta($bookingId,'distance',$data['distance_map']);
			CHBSPostMeta::updatePostMeta($bookingId,'duration',$data['duration_map']);

			$transferTypeId=(int)$data['transfer_type_service_type_'.$data['service_type_id']];
			if(!$TransferType->isTransferType($transferTypeId))
				$transferTypeId=1;

			CHBSPostMeta::updatePostMeta($bookingId,'transfer_type_id',$data['transfer_type_service_type_'.$data['service_type_id']]);
		}
		
		$distanceIncluded=CHBSBookingHelper::getDistanceIncluded($data,$bookingForm,null,false);
		if(($distanceIncluded!==-1) && ((int)CHBSOption::getOption('length_unit')===2))
			$distanceIncluded=$Length->convertUnit($distanceIncluded,2,1);
		
		CHBSPostMeta::updatePostMeta($bookingId,'distance_included',$distanceIncluded);

		if(in_array($data['service_type_id'],array(2)))
		{
			CHBSPostMeta::updatePostMeta($bookingId,'duration',$data['duration_service_type_'.$data['service_type_id']]*60);
		}

		if(in_array($data['service_type_id'],array(3)))
		{
			$routeDictionary=$bookingForm['dictionary']['route'][$data['route_service_type_3']];

			CHBSPostMeta::updatePostMeta($bookingId,'route_id',$data['route_service_type_3']);
			CHBSPostMeta::updatePostMeta($bookingId,'route_name',$routeDictionary['post']->post_title);
		}

		/***/

		$coordinate=array();

		if(in_array($data['service_type_id'],array(1,2)))
		{
			if(count($bookingForm['meta']['location_fixed_pickup_service_type_'.$data['service_type_id']]))
			{
				$pickupLocationId=$data['fixed_location_pickup_service_type_'.$data['service_type_id']];
				array_push($coordinate,$bookingForm['meta']['location_fixed_pickup_service_type_'.$data['service_type_id']][$pickupLocationId]);

				CHBSPostMeta::updatePostMeta($bookingId,'pickup_location_id',$pickupLocationId);
			} 
			else
			{
				array_push($coordinate,json_decode($data['pickup_location_coordinate_service_type_'.$data['service_type_id']]));

				if(($data['service_type_id']==1) && ($bookingForm['meta']['waypoint_enable']==1))
				{
					if(is_array($data['waypoint_location_coordinate_service_type_1']))
					{
						foreach($data['waypoint_location_coordinate_service_type_1'] as $index=>$value)
						{
							$value=json_decode($value);
							
							if((int)$bookingForm['meta']['waypoint_duration_enable']===1)
							{
								$value->{'duration'}=(int)$data['waypoint_duration_service_type_1'][$index];
							}
							
							array_push($coordinate,$value);
						}
					}
				}
			}

			if(count($bookingForm['meta']['location_fixed_dropoff_service_type_'.$data['service_type_id']]))
			{
				$dropoffLocationId=$data['fixed_location_dropoff_service_type_'.$data['service_type_id']];
				array_push($coordinate,$bookingForm['meta']['location_fixed_dropoff_service_type_'.$data['service_type_id']][$dropoffLocationId]);

				CHBSPostMeta::updatePostMeta($bookingId,'dropoff_location_id',$dropoffLocationId);
			} 
			else
			{
				array_push($coordinate,json_decode($data['dropoff_location_coordinate_service_type_'.$data['service_type_id']]));
			}
		} 
		else
		{
			$routeDictionary=$bookingForm['dictionary']['route'][$data['route_service_type_3']];
			$coordinate=$routeDictionary['meta']['coordinate'];
			
			if((int)$bookingForm['meta']['route_pickup_location_field_enable']===1)
			{
				if($Validation->isNotEmpty($data['pickup_location_coordinate_service_type_3']))
				{
					array_unshift($coordinate,json_decode($data['pickup_location_coordinate_service_type_3']));
				}
			}
			
			if((int)$bookingForm['meta']['route_dropoff_location_field_enable']===1)
			{
				if($Validation->isNotEmpty($data['dropoff_location_coordinate_service_type_3']))
				{
					$coordinate[]=json_decode($data['dropoff_location_coordinate_service_type_3']);
				}
			}
		}

		$coordinate=json_decode(json_encode($coordinate),true);
		
		CHBSPostMeta::updatePostMeta($bookingId,'coordinate',$coordinate);

        /***/
        
        $waypointCount=CHBSBookingHelper::getWaypointCount($data,$bookingForm,$data['service_type_id'],$data['transfer_type_service_type_'.$data['service_type_id']]);
        $waypointDuration=CHBSBookingHelper::getWaypointDuration($data,$bookingForm,$data['service_type_id'],$data['transfer_type_service_type_'.$data['service_type_id']]);
		
        CHBSPostMeta::updatePostMeta($bookingId,'waypoint_count',$waypointCount);
        CHBSPostMeta::updatePostMeta($bookingId,'waypoint_duration',$waypointDuration);
		
        /***/

		// *rule
		$argument=array
		(
			'booking_form_id'=>$data['booking_form_id'],
			'service_type_id'=>$data['service_type_id'],
			'transfer_type_id'=>$data['transfer_type_service_type_'.$data['service_type_id']],
			'pickup_location_coordinate'=>$data['pickup_location_coordinate_service_type_'.$data['service_type_id']],
			'dropoff_location_coordinate'=>$data['dropoff_location_coordinate_service_type_'.$data['service_type_id']],
			'fixed_location_pickup'=>$data['fixed_location_pickup_service_type_'.$data['service_type_id']],
			'fixed_location_dropoff'=>$data['fixed_location_dropoff_service_type_'.$data['service_type_id']],
			'route_id'=>$data['route_service_type_3'],
			'vehicle_id'=>$data['vehicle_id'],
			'pickup_date'=>$data['pickup_date_service_type_'.$data['service_type_id']],
			'pickup_time'=>$data['pickup_time_service_type_'.$data['service_type_id']],
			'return_date'=>$data['return_date_service_type_'.$data['service_type_id']],
			'return_time'=>$data['return_time_service_type_'.$data['service_type_id']],
			'base_location_distance'=>CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id']),
			'base_location_return_distance'=>CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id'],true),
			'distance'=>$data['distance_map'],
			'distance_sum'=>$data['distance_sum'],
			'duration'=>in_array($data['service_type_id'],array(1,3)) ? 0 : $data['duration_service_type_2']*60,
			'duration_map'=>$data['duration_map'],
			'duration_sum'=>in_array($data['service_type_id'],array(1,3))?$data['duration_sum']:$data['duration_service_type_2']*60,
			'passenger_adult'=>$data['passenger_adult_service_type_'.$data['service_type_id']],
			'passenger_children'=>$data['passenger_children_service_type_'.$data['service_type_id']],
			'booking_form'=>$bookingForm,
            'waypoint_count'=>$waypointCount
		);
		
		$vehiclePrice=$Vehicle->calculatePrice($argument,true,true);
		
		CHBSPostMeta::updatePostMeta($bookingId,'vehicle_id',$WPML->translateID($data['vehicle_id']));
		CHBSPostMeta::updatePostMeta($bookingId,'vehicle_name',$vehicle['post']->post_title);
	
		$vehiclePriceBooking=array
		(
			'price_type'=>$vehiclePrice['price']['base']['price_type'],
			'price_fixed_value'=>$vehiclePrice['price']['base']['price_fixed_value'],
			'price_fixed_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_fixed_tax_rate_id'],$taxRateDictionary),
			'price_fixed_return_value'=>$vehiclePrice['price']['base']['price_fixed_return_value'],
			'price_fixed_return_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_fixed_return_tax_rate_id'],$taxRateDictionary),
			'price_fixed_return_new_ride_value'=>$vehiclePrice['price']['base']['price_fixed_return_new_ride_value'],
			'price_fixed_return_new_ride_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_fixed_return_new_ride_tax_rate_id'],$taxRateDictionary),
			'price_initial_value'=>$vehiclePrice['price']['base']['price_initial_value'],
			'price_initial_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_initial_tax_rate_id'],$taxRateDictionary),
			'price_initial_return_value'=>$vehiclePrice['price']['base']['price_initial_return_value'],
			'price_initial_return_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_initial_return_tax_rate_id'],$taxRateDictionary),
			'price_initial_return_new_ride_value'=>$vehiclePrice['price']['base']['price_initial_return_new_ride_value'],
			'price_initial_return_new_ride_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_initial_return_new_ride_tax_rate_id'],$taxRateDictionary),
			'price_delivery_value'=>$vehiclePrice['price']['base']['price_delivery_value'],
			'price_delivery_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_delivery_tax_rate_id'],$taxRateDictionary),
			'price_delivery_return_value'=>$vehiclePrice['price']['base']['price_delivery_return_value'],
			'price_delivery_return_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_delivery_return_tax_rate_id'],$taxRateDictionary),
			'price_distance_value'=>$vehiclePrice['price']['base']['price_distance_value'],
			'price_distance_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_distance_tax_rate_id'],$taxRateDictionary),
			'price_distance_return_value'=>$vehiclePrice['price']['base']['price_distance_return_value'],
			'price_distance_return_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_distance_return_tax_rate_id'],$taxRateDictionary),
			'price_distance_return_new_ride_value'=>$vehiclePrice['price']['base']['price_distance_return_new_ride_value'],
			'price_distance_return_new_ride_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_distance_return_new_ride_tax_rate_id'],$taxRateDictionary),
			'price_hour_value'=>$vehiclePrice['price']['base']['price_hour_value'],
			'price_hour_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_hour_tax_rate_id'],$taxRateDictionary),
			'price_hour_return_value'=>$vehiclePrice['price']['base']['price_hour_return_value'],
			'price_hour_return_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_hour_return_tax_rate_id'],$taxRateDictionary),
			'price_hour_return_new_ride_value'=>$vehiclePrice['price']['base']['price_hour_return_new_ride_value'],
			'price_hour_return_new_ride_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_hour_return_new_ride_tax_rate_id'],$taxRateDictionary),
			'price_extra_time_value'=>$vehiclePrice['price']['base']['price_extra_time_value'],
			'price_extra_time_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_extra_time_tax_rate_id'],$taxRateDictionary),
			'price_waypoint_value'=>$vehiclePrice['price']['base']['price_waypoint_value'],
			'price_waypoint_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_waypoint_tax_rate_id'],$taxRateDictionary),
			'price_waypoint_duration_value'=>$vehiclePrice['price']['base']['price_waypoint_duration_value'],
			'price_waypoint_duration_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_waypoint_duration_tax_rate_id'],$taxRateDictionary),
			'price_passenger_adult_value'=>$vehiclePrice['price']['base']['price_passenger_adult_value'],
			'price_passenger_adult_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_passenger_adult_tax_rate_id'],$taxRateDictionary),
			'price_passenger_children_value'=>$vehiclePrice['price']['base']['price_passenger_children_value'],
			'price_passenger_children_tax_rate_value'=>$TaxRate->getTaxRateValue($vehiclePrice['price']['base']['price_passenger_children_tax_rate_id'],$taxRateDictionary),
			'price_round_value'=>$vehiclePrice['price']['base']['round_value']
		);

		foreach($vehiclePriceBooking as $index=> $value)
			CHBSPostMeta::updatePostMeta($bookingId,$index,$value);
		
		/***/

		$Coupon=new CHBSCoupon();
		$code=$Coupon->checkCode();

		if($code===false)
		{
			CHBSPostMeta::updatePostMeta($bookingId,'coupon_code','');
			CHBSPostMeta::updatePostMeta($bookingId,'coupon_discount_percentage',0);
		} 
		else
		{
			CHBSPostMeta::updatePostMeta($bookingId,'coupon_code',$code['meta']['code']);
			CHBSPostMeta::updatePostMeta($bookingId,'coupon_discount_percentage',$code['meta']['discount_percentage']);
		}

		/***/

		$BookingFormElement->sendBookingField($bookingId,$bookingForm['meta'],$data);
		$BookingFormElement->sendBookingAgreement($bookingId,$bookingForm['meta'],$data);

		/***/

		$BookingExtra=new CHBSBookingExtra();
		$bookingExtra=$BookingExtra->validate($data,$bookingForm,$taxRateDictionary);

		CHBSPostMeta::updatePostMeta($bookingId,'booking_extra',$bookingExtra);

		/***/
		
		$data['client_contact_detail_email_address']=trim($data['client_contact_detail_email_address']);

		$field=array('first_name','last_name','email_address','phone_number');
		foreach($field as $value)
			CHBSPostMeta::updatePostMeta($bookingId,'client_contact_detail_'.$value,$data['client_contact_detail_'.$value]);

		if((int)$bookingForm['meta']['billing_detail_state']===3)
			$data['client_billing_detail_enable']=1;

		CHBSPostMeta::updatePostMeta($bookingId,'client_billing_detail_enable',(int)$data['client_billing_detail_enable']);

		if((int)$data['client_billing_detail_enable']===1)
		{
			$field=array('company_name','tax_number','street_name','street_number','city','state','postal_code','country_code');
			foreach($field as $value)
				CHBSPostMeta::updatePostMeta($bookingId,'client_billing_detail_'.$value,$data['client_billing_detail_'.$value]);
		}

		/***/

		CHBSPostMeta::updatePostMeta($bookingId,'comment',$data['comment']);

		/***/

		CHBSPostMeta::updatePostMeta($bookingId,'payment_id',$data['payment_id']);
		CHBSPostMeta::updatePostMeta($bookingId,'payment_name',CHBSBookingHelper::getPaymentName($data['payment_id'],-1,$bookingForm['meta']));

		$paymentDepositEnable=CHBSBookingHelper::isPaymentDepositEnable($bookingForm['meta']);

		CHBSPostMeta::updatePostMeta($bookingId,'payment_deposit_enable',$paymentDepositEnable);
		CHBSPostMeta::updatePostMeta($bookingId,'payment_deposit_value',($paymentDepositEnable==1 ? $bookingForm['meta']['payment_deposit_value'] : 0));

		/***/

		CHBSPostMeta::updatePostMeta($bookingId,'base_location_distance',$data['base_location_distance']);
		CHBSPostMeta::updatePostMeta($bookingId,'base_location_return_distance',$data['base_location_return_distance']);

		CHBSPostMeta::updatePostMeta($bookingId,'base_location_duration',$data['base_location_duration']);
		CHBSPostMeta::updatePostMeta($bookingId,'base_location_return_duration',$data['base_location_return_duration']);

		/***/

		$data2=$data;
		$data2['booking_form']=$bookingForm;

		$bookingPrice=$this->calculatePrice($data2,null,false,true);

		$gratuityValue=$BookingGratuity->calculateBookingGratuity($bookingForm,$bookingPrice['total']['sum']['net']['value']);

		CHBSPostMeta::updatePostMeta($bookingId,'gratuity_value',$gratuityValue);

		CHBSPostMeta::updatePostMeta($bookingId,'paypal_fee',$bookingPrice['paypal_fee']['value']);
		CHBSPostMeta::updatePostMeta($bookingId,'stripe_fee',$bookingPrice['stripe_fee']['value']);

		/***/
		
		global $chbsGlobalData;
		
		if((isset($chbsGlobalData['tax_rate_distance'])) && (is_array($chbsGlobalData['tax_rate_distance'])))
			CHBSPostMeta::updatePostMeta($bookingId,'tax_rate_distance',$chbsGlobalData['tax_rate_distance']);
		
		/***/

		if($User->isUserBusinessAccount($bookingForm,$bookingId,$data['pickup_date_service_type_'.$data['service_type_id']]))
		{
			CHBSPostMeta::updatePostMeta($bookingId,'booking_status_id',2);
			CHBSPostMeta::updatePostMeta($bookingId,'business_user_paid',1);

			$User->updateUserBusinessAccountTransaction($bookingId);
		}

		/***/
		
		if(!$WooCommerce->isAddToCartEnable($bookingForm))
		{
			if($WooCommerce->isEnable($bookingForm['meta']))
				$WooCommerce->sendBooking($bookingId,$bookingForm,$data);
		}
		
		/***/
		
		$booking=$this->getBooking($bookingId);
		$booking['billing']=$this->createBilling($bookingId);
		
		/***/
		
		if(((int)CHBSOption::getOption('booking_status_sum_zero')===1) && ($booking['billing']['summary']['value_gross']==0.00) && ($BookingStatus->isBookingStatus(CHBSOption::getOption('booking_status_payment_success'))))
		{
			CHBSPostMeta::updatePostMeta($bookingId,'booking_status_id',CHBSOption::getOption('booking_status_payment_success'));
		}
		
		/***/
		
		if(!$WooCommerce->isAddToCartEnable($bookingForm))
		{
			do_action(PLUGIN_CHBS_CONTEXT.'_after_booking_sent',$bookingId);
			
			$this->sendBookingNotification($bookingId);
			return($bookingId);
		}
		else
		{
			$WooCommerce->addToCart($booking,$bookingForm);
		}
		
		/***/
	}
	
	/**************************************************************************/
	
	function sendBookingNotification($bookingId)
	{
		global $chbs_logEvent;
		
		$Booking=new CHBSBooking();
		$BookingForm=new CHBSBookingForm();
		$BookingDriver=new CHBSBookingDriver();
		
		$Validation=new CHBSValidation();
		
		/***/
		
		if(($booking=$Booking->getBooking($bookingId))===false) return(false);
		
		$booking['billing']=$this->createBilling($bookingId);
		
		/***/
		
		$bookingFormDictionary=$BookingForm->getDictionary();
		if(!array_key_exists($booking['meta']['booking_form_id'],$bookingFormDictionary)) return(false);
		
		$bookingForm=$bookingFormDictionary[$booking['meta']['booking_form_id']];
		
		/***/
		
		$subject=sprintf(__('New booking "%s" has been received','chauffeur-booking-system'),$booking['post']->post_title);

		$b=array(false,false);

		$b[0]=((int)$bookingForm['meta']['email_notification_booking_new_client_enable']===1) && (((int)$bookingForm['meta']['email_notification_booking_new_client_payment_success_enable']===0) || (((int)$bookingForm['meta']['email_notification_booking_new_client_payment_success_enable']===1) && (!in_array($booking['meta']['payment_id'],array(2,3,6)))));
		$b[1]=((int)$bookingForm['meta']['email_notification_booking_new_admin_enable']===1) && (((int)$bookingForm['meta']['email_notification_booking_new_admin_payment_success_enable']===0) || (((int)$bookingForm['meta']['email_notification_booking_new_admin_payment_success_enable']===1) && (!in_array($booking['meta']['payment_id'],array(2,3,6)))));

		if($b[0])
		{
			$chbs_logEvent=1;
			$this->sendEmail($bookingId,$bookingForm['meta']['booking_new_sender_email_account_id'],'booking_new_client',array($booking['meta']['client_contact_detail_email_address']),$subject);
		}

		if($b[1])
		{
			$chbs_logEvent=2;
			$this->sendEmail($bookingId,$bookingForm['meta']['booking_new_sender_email_account_id'],'booking_new_admin',preg_split('/;/',$bookingForm['meta']['booking_new_recipient_email_address']),$subject);
		}

		if($bookingForm['meta']['nexmo_sms_enable']==1)
		{
			$Nexmo=new CHBSNexmo();
			$Nexmo->sendSMS($bookingForm['meta']['nexmo_sms_api_key'],$bookingForm['meta']['nexmo_sms_api_key_secret'],$bookingForm['meta']['nexmo_sms_sender_name'],$bookingForm['meta']['nexmo_sms_recipient_phone_number'],$bookingForm['meta']['nexmo_sms_message']);
		}

		if($bookingForm['meta']['twilio_sms_enable']==1)
		{
			$Twilio=new CHBSTwilio();
			$Twilio->sendSMS($bookingForm['meta']['twilio_sms_api_sid'],$bookingForm['meta']['twilio_sms_api_token'],$bookingForm['meta']['twilio_sms_sender_phone_number'],$bookingForm['meta']['twilio_sms_recipient_phone_number'],$bookingForm['meta']['twilio_sms_message']);
		}

		if(in_array($bookingForm['meta']['customer_sms_enable'],array(1,2)))
		{
			if(($Validation->isNotEmpty($booking['meta']['client_contact_detail_phone_number'])) && ($Validation->isNotEmpty($bookingForm['meta']['customer_sms_message'])))
			{
				$message=CHBSBookingHelper::createBookingText($booking,$bookingForm['meta']['customer_sms_message']);
	
				if(in_array($bookingForm['meta']['customer_sms_enable'],array(1)))
				{
					$Twilio=new CHBSTwilio();
					$Twilio->sendSMS($bookingForm['meta']['twilio_sms_api_sid'],$bookingForm['meta']['twilio_sms_api_token'],$bookingForm['meta']['twilio_sms_sender_phone_number'],$booking['meta']['client_contact_detail_phone_number'],$message);
				}
				if(in_array($bookingForm['meta']['customer_sms_enable'],array(2)))
				{
					$Nexmo=new CHBSNexmo();
					$Nexmo->sendSMS($bookingForm['meta']['nexmo_sms_api_key'],$bookingForm['meta']['nexmo_sms_api_key_secret'],$bookingForm['meta']['nexmo_sms_sender_name'],$booking['meta']['client_contact_detail_phone_number'],$message);
				}
			}
		}

		if($bookingForm['meta']['telegram_enable']==1)
		{
			$Telegram=new CHBSTelegram();
			$Telegram->sendMessage($bookingForm['meta']['telegram_token'],$bookingForm['meta']['telegram_group_id'],$bookingForm['meta']['telegram_message']);
		}

		/***/

		$GoogleCalendar=new CHBSGoogleCalendar();
		$GoogleCalendar->sendBooking($bookingId);

		/***/

		$BookingDriver->setBookingDriverAcceptanceData($bookingId,0,2,'0000-00-00','00:00');
		$BookingDriver->setBookingDriverAcceptanceData($bookingId,0,1,'0000-00-00','00:00');
		
		if((int)$booking['meta']['driver_id']>0)
		{
			$BookingDriver->setDriver($booking);
			$BookingDriver->sendEmail($booking);
		}

		/***/

		$Driver=new CHBSDriver();
		$Driver->sendTelegramBooking($bookingId);

		/***/

		$BookingWebHook=new CHBSBookingWebHook();
		$BookingWebHook->run($bookingId);

		/***/

		return($bookingId);
	}

	/**************************************************************************/

	function setPostMetaDefault(&$meta)
	{
		CHBSHelper::setDefault($meta,'business_user_paid',0);

		CHBSHelper::setDefault($meta,'gratuity_value',CHBSPrice::getDefaultPrice());

		CHBSHelper::setDefault($meta,'calculation_method',1);

		CHBSHelper::setDefault($meta,'user_id',0);

		CHBSHelper::setDefault($meta,'driver_id',-1);

		CHBSHelper::setDefault($meta,'coupon_code','');
		CHBSHelper::setDefault($meta,'coupon_discount_percentage',0);

		CHBSHelper::setDefault($meta,'passenger_enable',0);
		CHBSHelper::setDefault($meta,'passenger_adult_number',0);
		CHBSHelper::setDefault($meta,'passenger_children_number',0);

		CHBSHelper::setDefault($meta,'hide_fee',0);
		CHBSHelper::setDefault($meta,'price_hide',0);
		CHBSHelper::setDefault($meta,'total_time_display_enable',1);

		CHBSHelper::setDefault($meta,'passenger_use_person_label',0);
		
		CHBSHelper::setDefault($meta,'woocommerce_enable',0);
		CHBSHelper::setDefault($meta,'woocommerce_booking_id',0);

		CHBSHelper::setDefault($meta,'booking_status_id',1);
		CHBSHelper::setDefault($meta,'transfer_type_id',1);

		CHBSHelper::setDefault($meta,'base_location_distance',0);
		CHBSHelper::setDefault($meta,'base_location_return_distance',0);

		CHBSHelper::setDefault($meta,'price_delivery_return_value',CHBSPrice::getDefaultPrice());
		CHBSHelper::setDefault($meta,'price_delivery_return_tax_rate_id',0);

		CHBSHelper::setDefault($meta,'price_round_value',CHBSPrice::getDefaultPrice());

		CHBSHelper::setDefault($meta,'paypal_fee',0);
		CHBSHelper::setDefault($meta,'stripe_fee',0);

		CHBSHelper::setDefault($meta,'base_location_duration',0);
		CHBSHelper::setDefault($meta,'base_location_return_duration',0);
		
		CHBSHelper::setDefault($meta,'email_service_post_arrival_message_customer_send',0);

		CHBSHelper::setDefault($meta,'waypoint_count',0);
		CHBSHelper::setDefault($meta,'waypoint_duration',0);

		CHBSHelper::setDefault($meta,'woocommerce_product_id',0);
		
		CHBSHelper::setDefault($meta,'booking_acceptance_driver_status',0);
		CHBSHelper::setDefault($meta,'booking_acceptance_driver_stage_number',1);
		CHBSHelper::setDefault($meta,'booking_acceptance_driver_stage_1_email_send_date','0000-00-00');
		CHBSHelper::setDefault($meta,'booking_acceptance_driver_stage_1_email_send_time','00:00');
		CHBSHelper::setDefault($meta,'booking_acceptance_driver_stage_2_email_send_date','0000-00-00');
		CHBSHelper::setDefault($meta,'booking_acceptance_driver_stage_2_email_send_time','00:00');
		
		CHBSHelper::setDefault($meta,'distance_included',-1);
		
		$BookingDriver=new CHBSBookingDriver();
		$BookingDriver->setPostMetaDefault($meta);
	}

	/**************************************************************************/

	function savePost($postId)
	{
		if(!$_POST) return(false);

		if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_booking_noncename','savePost')===false) return(false);

		$Driver=new CHBSDriver();
		$BookingStatus=new CHBSBookingStatus();
		$BookingDriver=new CHBSBookingDriver();
		
		$bookingOld=$this->getBooking($postId);
		
		/***/
		
        if($BookingStatus->isBookingStatus(CHBSHelper::getPostValue('booking_status_id')))
           CHBSPostMeta::updatePostMeta($postId,'booking_status_id',CHBSHelper::getPostValue('booking_status_id')); 
          
		$dictionary=$Driver->getDictionary();
		if(array_key_exists(CHBSHelper::getPostValue('driver_id'),$dictionary))
			CHBSPostMeta::updatePostMeta($postId,'driver_id',CHBSHelper::getPostValue('driver_id'));
		else CHBSPostMeta::updatePostMeta($postId,'driver_id',-1);
		
		/***/
		
		$bookingNew=$this->getBooking($postId);
		
		$emailSend=false;
		
		$WooCommerce=new CHBSWooCommerce();
		$WooCommerce->changeStatus(-1,$postId,$emailSend);
		
		if(!$emailSend)
			$this->sendEmailBookingChangeStatus($bookingOld,$bookingNew);
		
		/***/

		$BookingDriver->setDriver($bookingNew,$bookingOld);
		
		/***/
		
		do_action('chbs_booking_status_change',$bookingNew,$bookingOld,CHBSHelper::getPostValue('driver_mail_message_resend'));
	}

	/**************************************************************************/

	function manageEditColumns($column)
	{
		$addColumn=array
		(
			'status'=>__('Booking status','chauffeur-booking-system'),
			'service_type'=>__('Service type','chauffeur-booking-system'),
			'pickup_return_date'=>__('Pickup/return date','chauffeur-booking-system'),
			'client'=>__('Client','chauffeur-booking-system'),
			'price'=>__('Price','chauffeur-booking-system'),
			'date'=>$column['date']
		);

		unset($column['date']);

		foreach($addColumn as $index=> $value)
			$column[$index]=$value;

		return($column);
	}

	/**************************************************************************/

	function managePostsCustomColumn($column)
	{
		global $post;

		$Date=new CHBSDate();
		$ServiceType=new CHBSServiceType();
		$BookingStatus=new CHBSBookingStatus();

		$meta=CHBSPostMeta::getPostMeta($post);

		$billing=$this->createBilling($post->ID);

		switch ($column)
		{
			case 'status':

				$bookingStatus=$BookingStatus->getBookingStatus($meta['booking_status_id']);
				echo '<div class="to-booking-status to-booking-status-'.(int)$meta['booking_status_id'].'">'.esc_html($bookingStatus[0]).'</div>';

			break;

			case 'service_type':

				$serviceType=$ServiceType->getServiceType($meta['service_type_id']);
				echo esc_html($serviceType[0]);

			break;

			case 'pickup_return_date':

				echo esc_html($Date->formatDateToDisplay($meta['pickup_date']).' '.$Date->formatTimeToDisplay($meta['pickup_time']));

				if(in_array($meta['service_type_id'],array(1,3)))
				{
					if(in_array($meta['transfer_type_id'],array(3)))
					{
						echo '<br>-<br>';
						echo esc_html($Date->formatDateToDisplay($meta['return_date']).' '.$Date->formatTimeToDisplay($meta['return_time']));
					}
				}

			break;

			case 'client':

				echo esc_html($meta['client_contact_detail_first_name'].' '.$meta['client_contact_detail_last_name']);

			break;

			case 'price':

				echo esc_html(CHBSPrice::format($billing['summary']['value_gross'],$meta['currency_id']));

			break;
		}
	}
	
	/**************************************************************************/

	function manageEditSortableColumns($column)
	{
		return($column);
	}

	/**************************************************************************/

	function restrictManagePosts()
	{
		if(!is_admin()) return;
		if(CHBSHelper::getGetValue('post_type',false)!==self::getCPTName()) return;

		$html=array(null,null);

		/***/
		
		$BookingStatus=new CHBSBookingStatus();
		$bookingStatusDirectory=$BookingStatus->getBookingStatus();
		
		$directory=array();
		foreach($bookingStatusDirectory as $index=>$value)
			$directory[$index]=$value[0];
		
		asort($directory,SORT_STRING);
		
		if(!array_key_exists('chbs_booking_status_id',$_GET))
			$_GET['chbs_booking_status_id']=-2;
		
 		foreach($directory as $index=>$value)
			$html[0].='<option value="'.(int)$index.'"'.CHBSHelper::selectedIf(CHBSHelper::getGetValue('booking_status_id'),$index,false).'>'.esc_html($value).'</option>';
		
		$html[0]=
		'
			<select name="'.CHBSHelper::getFormName('booking_status_id',false).'">
				<option value="-1"'.CHBSHelper::selectedIf(CHBSHelper::getGetValue('booking_status_id'),-1,false).'>'.esc_html__('- All statuses -','chauffeur-booking-system').'</option>
				<option value="-2"'.CHBSHelper::selectedIf(CHBSHelper::getGetValue('booking_status_id'),-2,false).'>'.esc_html__('- New & accepted -','chauffeur-booking-system').'</option>
				<option value="-3"'.CHBSHelper::selectedIf(CHBSHelper::getGetValue('booking_status_id'),-3,false).'>'.esc_html__('- Blocked -','chauffeur-booking-system').'</option>
				'.$html[0].'
			</select>
		';
		
		$html[1]=
		'
			<div>
				<input type="text" class="to-datepicker-custom" name="'.CHBSHelper::getFormName('pickup_date_from',false).'" placeholder="'.esc_html('Pickup/return date (from)','chauffeur-booking-system').'" value="'.esc_attr(CHBSHelper::getGetValue('pickup_date_from')).'"/>
			</div>
			<div>
				<input type="text" class="to-datepicker-custom" name="'.CHBSHelper::getFormName('pickup_date_to',false).'" placeholder="'.esc_html('Pickup/return date (to)','chauffeur-booking-system').'" value="'.esc_attr(CHBSHelper::getGetValue('pickup_date_to')).'"/>
			</div>
		';
		
		echo
		'	
			<div class="to-booking-search">
				'.$html[0].'
				'.$html[1].'
			</div>
		';
	}

	/**************************************************************************/

	function parseQuery($query)
	{
		if(!is_admin()) return;
		if(CHBSHelper::getGetValue('post_type',false)!==self::getCPTName()) return;
		if((int)CHBSHelper::getGetValue('booking_report_form_submit',true)===1) return;	
				
		if($query->query['post_type']!==self::getCPTName()) return;
		
		$Date=new CHBSDate();
		$Validation=new CHBSValidation();
		
		$metaQuery=array();
		
		/***/
		
		$bookingStatusId=CHBSHelper::getGetValue('booking_status_id',true);
		if($Validation->isEmpty($bookingStatusId)) $bookingStatusId=-2;

		$bookingStatusId=(int)$bookingStatusId;
		
		if($bookingStatusId!==-1)
		{
			$value=null;
			$compare='IN';
			
			switch($bookingStatusId)
			{
				case -3:
					
					$status=CHBSOption::getOption('booking_status_nonblocking');
					if(is_array($status))
					{
						$compare='NOT IN';
						$value=$status;
					}		

				break;

				case -2:
					
					$value=array(1,2);

				break;
	
				default:
					
					$value=array($bookingStatusId);
			}
			
			if(!is_null($value))
			{
				array_push($metaQuery,array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_booking_status_id',
					'value'=>$value,
					'compare'=>$compare
				));
			}
		}
		
		/***/
		
		$dateFrom=CHBSHelper::getGetValue('pickup_date_from',true);
		$dateTo=CHBSHelper::getGetValue('pickup_date_to',true);
		
		if(($Validation->isNotEmpty($dateFrom)) || ($Validation->isNotEmpty($dateTo)))
		{
			if($Validation->isEmpty($dateFrom)) $dateFrom=$dateTo;
			if($Validation->isEmpty($dateTo)) $dateTo=$dateFrom;
			
			$dateFrom=$Date->formatDateToStandard($dateFrom);
			$dateTo=$Date->formatDateToStandard($dateTo);
			
			array_push($metaQuery,array
			(	
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_pickup_datetime',
					'value'=>array($Date->reverseDate($dateFrom).' 00:00',$Date->reverseDate($dateTo).' 23:59'),
					'compare'=>'BETWEEN',
					'type'=>'DATE'					
				),
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_return_datetime',
					'value'=>array($Date->reverseDate($dateFrom).' 00:00',$Date->reverseDate($dateTo).' 23:59'),
					'compare'=>'BETWEEN',
					'type'=>'DATE'					
				),				
				'relation'=>'OR'
			));				
		}
		
		/***/
		
		array_push($metaQuery,array
		(
			array
			(
				'key'=>PLUGIN_CHBS_CONTEXT.'_woocommerce_product_id',
				'value'=>array(0),
				'compare'=>'IN'
			)		
		));

		/***/
		
		$order=CHBSHelper::getGetValue('order',false);
		$orderby=CHBSHelper::getGetValue('orderby',false);

		if($orderby=='title')
		{
			$query->set('orderby','title');
		} 
		elseif($orderby=='date')
		{
			$query->set('orderby','date');
		} 
		else
		{
			switch ($orderby)
			{
				default:

					$query->set('meta_key',PLUGIN_CHBS_CONTEXT.'_pickup_datetime');
					$query->set('meta_type','DATETIME');

					if($Validation->isEmpty($order)) $order='asc';
			}

			$query->set('orderby','meta_value');
		}

		$query->set('order',$order);

		if(count($metaQuery))
			$query->set('meta_query',$metaQuery);
	}

	/**************************************************************************/

	function calculatePrice($data,$vehiclePrice=null,$hideFee=false,$roundVehiclePrice=false,$useCoupon=true)
	{
		$Length=new CHBSLength();
		$TaxRate=new CHBSTaxRate();
		$Vehicle=new CHBSVehicle();
		$Payment=new CHBSPayment();
		$BookingExtra=new CHBSBookingExtra();
		$BookingGratuity=new CHBSBookingGratuity();

		$taxRateDictionary=$TaxRate->getDictionary();

		$component=array('initial','delivery','delivery_return','vehicle','extra_time','waypoint','booking_extra','total','pay');

		foreach($component as $value)
		{
			$price[$value]=array
			(
				'sum'=>array
				(
					'net'=>array
					(
						'value'=>0.00
					),
					'gross'=>array
					(
						'value'=>0.00,
						'format'=>0.00
					)
				)
			);
		}

		if(array_key_exists($data['vehicle_id'],$data['booking_form']['dictionary']['vehicle']))
		{
			$serviceTypeId=(int)$data['service_type_id'];
			$transferTypeKey='transfer_type_service_type_'.$serviceTypeId;
			$transferTypeId=array_key_exists($transferTypeKey,$data) ? (int)$data[$transferTypeKey] : 0;

			$pickupCoordinateKey='pickup_location_coordinate_service_type_'.$serviceTypeId;
			$dropoffCoordinateKey='dropoff_location_coordinate_service_type_'.$serviceTypeId;
			$fixedPickupKey='fixed_location_pickup_service_type_'.$serviceTypeId;
			$fixedDropoffKey='fixed_location_dropoff_service_type_'.$serviceTypeId;
			$routeKey='route_service_type_'.$serviceTypeId;

			$pickupCoordinate=array_key_exists($pickupCoordinateKey,$data) ? $data[$pickupCoordinateKey] : null;
			$dropoffCoordinate=array_key_exists($dropoffCoordinateKey,$data) ? $data[$dropoffCoordinateKey] : null;
			$fixedLocationPickup=array_key_exists($fixedPickupKey,$data) ? (int)$data[$fixedPickupKey] : 0;
			$fixedLocationDropoff=array_key_exists($fixedDropoffKey,$data) ? (int)$data[$fixedDropoffKey] : 0;
			$routeId=array_key_exists($routeKey,$data) ? (int)$data[$routeKey] : 0;
			
            $waypointCount=CHBSBookingHelper::getWaypointCount($data,$data['booking_form'],$serviceTypeId,$transferTypeId);
			$waypointDuration=CHBSBookingHelper::getWaypointDuration($data,$data['booking_form'],$serviceTypeId,$transferTypeId);

			// *rule
			$argument=array
			(
				'booking_form_id'=>$data['booking_form_id'],
				'service_type_id'=>$serviceTypeId,
				'transfer_type_id'=>$transferTypeId,
				'pickup_location_coordinate'=>$pickupCoordinate,
				'dropoff_location_coordinate'=>$dropoffCoordinate,
				'fixed_location_pickup'=>$fixedLocationPickup,
				'fixed_location_dropoff'=>$fixedLocationDropoff,
				'route_id'=>$routeId,
				'transfer_type_id'=>$transferTypeId,
				'vehicle_id'=>$data['vehicle_id'],
				'pickup_date'=>$data['pickup_date_service_type_'.$serviceTypeId],
				'pickup_time'=>$data['pickup_time_service_type_'.$serviceTypeId],
				'return_date'=>$data['return_date_service_type_'.$serviceTypeId],
				'return_time'=>$data['return_time_service_type_'.$serviceTypeId],
				'base_location_distance'=>CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id']),
				'base_location_return_distance'=>CHBSBookingHelper::getBaseLocationDistance($data['vehicle_id'],true),
				'distance'=>$data['distance_map'],
				'distance_sum'=>$data['distance_sum'],
				'duration'=>in_array($data['service_type_id'],array(1,3)) ? 0 : $data['duration_service_type_2']*60,
				'duration_map'=>$data['duration_map'],
				'duration_sum'=>in_array($data['service_type_id'],array(1,3)) ? $data['duration_sum'] : $data['duration_service_type_2']*60,
				'passenger_adult'=>$data['passenger_adult_service_type_'.$serviceTypeId],
				'passenger_children'=>$data['passenger_children_service_type_'.$serviceTypeId],
				'booking_form'=>$data['booking_form'],
                'waypoint_count'=>$waypointCount
			);

			if(is_null($vehiclePrice))
			{
				$vehiclePrice=$Vehicle->calculatePrice($argument,false,$roundVehiclePrice,array(),true,$useCoupon);
			}
			
			if(CHBSOption::getOption('length_unit')==2)
			{
				$data['distance_map']=$Length->convertUnit($data['distance_map']);
				$data['base_location_distance']=$Length->convertUnit($data['base_location_distance']);
				$data['base_location_return_distance']=$Length->convertUnit($data['base_location_return_distance']);
			}

			if(array_key_exists('other',$vehiclePrice['price']))
				$price['other']=$vehiclePrice['price']['other'];

			$price['vehicle']['sum']['net']['value']=$vehiclePrice['price']['sum']['net']['value'];
			$price['vehicle']['sum']['gross']['value']=$vehiclePrice['price']['sum']['gross']['value'];

			$price['vehicle_exclude_fee']['sum']['net']['value']=$price['vehicle']['sum']['net']['value'];
			$price['vehicle_exclude_fee']['sum']['gross']['value']=$price['vehicle']['sum']['gross']['value'];			
			
			$transferTypePrefix=null;
			if($transferTypeId===2) $transferTypePrefix='_return';
			if($transferTypeId===3) $transferTypePrefix='_return_new_ride';

			$price['initial']['sum']['net']['value']=$vehiclePrice['price']['base']['price_initial'.$transferTypePrefix.'_value'];
			$price['initial']['sum']['gross']['value']=CHBSPrice::calculateGross($price['initial']['sum']['net']['value'],$vehiclePrice['price']['base']['price_initial'.$transferTypePrefix.'_tax_rate_id']);

			if(in_array($serviceTypeId,array(1,3)))
			{
				if(is_array($data['booking_form']['meta']['transfer_type_enable_'.$serviceTypeId]))
				{
					if(in_array($transferTypeId,$data['booking_form']['meta']['transfer_type_enable_'.$serviceTypeId]))
					{
						if(in_array($transferTypeId,array(3)))
						{
							$data['base_location_distance']*=2;
							$data['base_location_return_distance']*=2;
						}
					}
				}
			}

			$price['delivery']['sum']['net']['value']=CHBSPrice::numberFormat($vehiclePrice['price']['base']['price_delivery_value']*$data['base_location_distance']);
			$price['delivery']['sum']['gross']['value']=CHBSPrice::calculateGross($price['delivery']['sum']['net']['value'],$vehiclePrice['price']['base']['price_delivery_tax_rate_id']);

			$price['delivery_return']['sum']['net']['value']=CHBSPrice::numberFormat($vehiclePrice['price']['base']['price_delivery_return_value']*$data['base_location_return_distance']);
			$price['delivery_return']['sum']['gross']['value']=CHBSPrice::calculateGross($price['delivery_return']['sum']['net']['value'],$vehiclePrice['price']['base']['price_delivery_return_tax_rate_id']);

			$price['extra_time']['sum']['net']['value']=0;
			$price['extra_time']['sum']['gross']['value']=0;

			if(in_array($serviceTypeId,array(1,3)))
			{
				$priceExtraTime=$vehiclePrice['price']['base']['price_extra_time_value'];

				$extraTimeKey='extra_time_service_type_'.$serviceTypeId;
				$time=array_key_exists($extraTimeKey,$data) ? $data[$extraTimeKey] : 0;

				if((int)$data['booking_form']['meta']['extra_time_unit']===1)
					$time/=60;

				$price['extra_time']['sum']['net']['value']=CHBSPrice::numberFormat($priceExtraTime*$time);
				$price['extra_time']['sum']['gross']['value']=CHBSPrice::calculateGross($price['extra_time']['sum']['net']['value'],$vehiclePrice['price']['base']['price_extra_time_tax_rate_id']);
			}
            
			$price['waypoint']['sum']['net']['value']=CHBSPrice::numberFormat($vehiclePrice['price']['base']['price_waypoint_value']*$waypointCount);
			$price['waypoint']['sum']['gross']['value']=CHBSPrice::calculateGross($price['waypoint']['sum']['net']['value'],$vehiclePrice['price']['base']['price_waypoint_tax_rate_id']);            

			$price['waypoint_duration']['sum']['net']['value']=CHBSPrice::numberFormat($vehiclePrice['price']['base']['price_waypoint_duration_value']*$waypointDuration);
			$price['waypoint_duration']['sum']['gross']['value']=CHBSPrice::calculateGross($price['waypoint_duration']['sum']['net']['value'],$vehiclePrice['price']['base']['price_waypoint_duration_tax_rate_id']);            
			
			if((int)$vehiclePrice['price']['base']['price_type']===2)
			{
				$price['initial']['sum']['net']['value']=0.00;
				$price['initial']['sum']['gross']['value']=0.00;

				$price['delivery']['sum']['net']['value']=0.00;
				$price['delivery']['sum']['gross']['value']=0.00;

				$price['delivery_return']['sum']['net']['value']=0.00;
				$price['delivery_return']['sum']['gross']['value']=0.00;
			}
			
			if(in_array((int)$hideFee,array(1,2)))
			{
				$price['vehicle']['sum']['net']['value']+=$price['initial']['sum']['net']['value']+$price['delivery']['sum']['net']['value']+$price['delivery_return']['sum']['net']['value']+$price['extra_time']['sum']['net']['value']+$price['waypoint']['sum']['net']['value']+$price['waypoint_duration']['sum']['net']['value'];
				$price['vehicle']['sum']['gross']['value']+=$price['initial']['sum']['gross']['value']+$price['delivery']['sum']['gross']['value']+$price['delivery_return']['sum']['gross']['value']+$price['extra_time']['sum']['gross']['value']+$price['waypoint']['sum']['gross']['value']+$price['waypoint_duration']['sum']['gross']['value'];;
			}
			
			$price['initial']['sum']['net']['format']=CHBSPrice::format($price['initial']['sum']['net']['value'],CHBSCurrency::getFormCurrency());
			$price['initial']['sum']['gross']['format']=CHBSPrice::format($price['initial']['sum']['gross']['value'],CHBSCurrency::getFormCurrency());

			$price['delivery']['sum']['net']['format']=CHBSPrice::format($price['delivery']['sum']['net']['value'],CHBSCurrency::getFormCurrency());
			$price['delivery']['sum']['gross']['format']=CHBSPrice::format($price['delivery']['sum']['gross']['value'],CHBSCurrency::getFormCurrency());

			$price['delivery_return']['sum']['net']['format']=CHBSPrice::format($price['delivery_return']['sum']['net']['value'],CHBSCurrency::getFormCurrency());
			$price['delivery_return']['sum']['gross']['format']=CHBSPrice::format($price['delivery_return']['sum']['gross']['value'],CHBSCurrency::getFormCurrency());

			if(in_array($serviceTypeId,array(1,3)))
			{
				$price['extra_time']['sum']['net']['format']=CHBSPrice::format($price['extra_time']['sum']['net']['value'],CHBSCurrency::getFormCurrency());
				$price['extra_time']['sum']['gross']['format']=CHBSPrice::format($price['extra_time']['sum']['gross']['value'],CHBSCurrency::getFormCurrency());
			}
            
            $price['waypoint']['sum']['net']['format']=CHBSPrice::format($price['waypoint']['sum']['net']['value'],CHBSCurrency::getFormCurrency());
			$price['waypoint']['sum']['gross']['format']=CHBSPrice::format($price['waypoint']['sum']['gross']['value'],CHBSCurrency::getFormCurrency());           

            $price['waypoint_duration']['sum']['net']['format']=CHBSPrice::format($price['waypoint_duration']['sum']['net']['value'],CHBSCurrency::getFormCurrency());
			$price['waypoint_duration']['sum']['gross']['format']=CHBSPrice::format($price['waypoint_duration']['sum']['gross']['value'],CHBSCurrency::getFormCurrency());  
			
			if($roundVehiclePrice)
			{
				$roundValue=CHBSBookingHelper::getRoundValue($data['booking_form'],$price['vehicle']['sum']['gross']['value']);
				$price['vehicle']['sum']['gross']['value']+=$roundValue;
			}

			$price['vehicle']['sum']['net']['format']=CHBSPrice::format($price['vehicle']['sum']['net']['value'],CHBSCurrency::getFormCurrency());
			$price['vehicle']['sum']['gross']['format']=CHBSPrice::format($price['vehicle']['sum']['gross']['value'],CHBSCurrency::getFormCurrency());

			$price['vehicle']['sum']['net']['formatHtml']=$Vehicle->getPriceFormatHtml($price['vehicle']['sum']['net']['value']);
			$price['vehicle']['sum']['gross']['formatHtml']=$Vehicle->getPriceFormatHtml($price['vehicle']['sum']['gross']['value']);
			
			$price['vehicle']['sum_one_way']['net']['format']=$vehiclePrice['price']['sum_one_way']['net']['format'];
			$price['vehicle']['sum_one_way']['gross']['format']=$vehiclePrice['price']['sum_one_way']['gross']['format'];

			$price['vehicle']['sum_return']['net']['format']=$vehiclePrice['price']['sum_return']['net']['format'];
			$price['vehicle']['sum_return']['gross']['format']=$vehiclePrice['price']['sum_return']['gross']['format'];
			
			$price['vehicle_exclude_fee']['sum']['net']['format']=CHBSPrice::format($price['vehicle_exclude_fee']['sum']['net']['value'],CHBSCurrency::getFormCurrency());
			$price['vehicle_exclude_fee']['sum']['gross']['format']=CHBSPrice::format($price['vehicle_exclude_fee']['sum']['gross']['value'],CHBSCurrency::getFormCurrency());

			$price['vehicle_exclude_fee']['sum']['net']['formatHtml']=$Vehicle->getPriceFormatHtml($price['vehicle_exclude_fee']['sum']['net']['value']);
			$price['vehicle_exclude_fee']['sum']['gross']['formatHtml']=$Vehicle->getPriceFormatHtml($price['vehicle_exclude_fee']['sum']['gross']['value']);	
		}

		$bookingExtra=$BookingExtra->validate($data,$data['booking_form'],$taxRateDictionary);
		foreach($bookingExtra as $value)
		{
			$valueItemNet=CHBSPrice::numberFormat($value['quantity']*$value['price']);
			
			$price['booking_extra']['sum']['net']['value']+=$valueItemNet;
			$price['booking_extra']['sum']['gross']['value']+=CHBSPrice::calculateGross($valueItemNet,0,$value['tax_rate_value']);
		}

		$price['booking_extra']['sum']['net']['format']=CHBSPrice::format($price['booking_extra']['sum']['net']['value'],CHBSCurrency::getFormCurrency());
		$price['booking_extra']['sum']['gross']['format']=CHBSPrice::format($price['booking_extra']['sum']['gross']['value'],CHBSCurrency::getFormCurrency());

		if(in_array($hideFee,array(1,2)))
		{
			$price['total']['sum']['net']['value']=$price['vehicle']['sum']['net']['value']+$price['booking_extra']['sum']['net']['value'];
		} 
		else
		{
			$price['total']['sum']['net']['value']=$price['initial']['sum']['net']['value']+$price['delivery']['sum']['net']['value']+$price['delivery_return']['sum']['net']['value']+$price['extra_time']['sum']['net']['value']+$price['waypoint']['sum']['net']['value']+$price['waypoint_duration']['sum']['net']['value']+$price['vehicle']['sum']['net']['value']+$price['booking_extra']['sum']['net']['value'];
		}

		$price['gratuity']['value']=$BookingGratuity->calculateBookingGratuity($data['booking_form'],$price['total']['sum']['net']['value']);
		$price['gratuity']['format']=CHBSPrice::format($price['gratuity']['value'],CHBSCurrency::getFormCurrency());

		if(in_array($hideFee,array(1,2)))
		{
			$price['total']['sum']['gross']['value']=$price['vehicle']['sum']['gross']['value']+$price['booking_extra']['sum']['gross']['value'];
		} 
		else
		{
			$price['total']['sum']['gross']['value']=$price['initial']['sum']['gross']['value']+$price['delivery']['sum']['gross']['value']+$price['delivery_return']['sum']['gross']['value']+$price['extra_time']['sum']['gross']['value']+$price['waypoint']['sum']['gross']['value']+$price['waypoint_duration']['sum']['gross']['value']+$price['vehicle']['sum']['gross']['value']+$price['booking_extra']['sum']['gross']['value'];
		}

		$price['tax']['sum']['value']=0.0;

		if($price['total']['sum']['gross']['value']>=$price['total']['sum']['net']['value'])
		{
			$price['tax']['sum']['value']=$price['total']['sum']['gross']['value']-$price['total']['sum']['net']['value'];
		}

		$price['tax']['sum']['format']=CHBSPrice::format($price['tax']['sum']['value'],CHBSCurrency::getFormCurrency());

		$price['total']['sum']['gross']['value']+=$price['gratuity']['value'];
		
		$price['paypal_fee']['value']=0.00;
		$price['stripe_fee']['value']=0.00;

		if(($Payment->isPayment($data['payment_id'])) && (in_array($data['payment_id'],array(2,3))))
		{
			$type=((int)$data['payment_id']===2) ? 'stripe' : 'paypal';

			if($vehiclePrice['price']['base']['price_payment_'.$type.'_percentage_value']!=0)
			{				
				$sum=CHBSPrice::numberFormat($price['total']['sum']['gross']['value']*(1+($vehiclePrice['price']['base']['price_payment_'.$type.'_percentage_value']/100)));
				$price[$type.'_fee']['value']+=$sum-$price['total']['sum']['gross']['value'];
			}
			
			if($vehiclePrice['price']['base']['price_payment_'.$type.'_fixed_value']!=0)
			{
				$price[$type.'_fee']['value']+=$vehiclePrice['price']['base']['price_payment_'.$type.'_fixed_value'];
			}
			
			$price['total']['sum']['gross']['value']+=$price[$type.'_fee']['value'];
		}

		$price['paypal_fee']['format']=CHBSPrice::format($price['paypal_fee']['value'],CHBSCurrency::getFormCurrency());
		$price['stripe_fee']['format']=CHBSPrice::format($price['stripe_fee']['value'],CHBSCurrency::getFormCurrency());

		$price['total']['sum']['net']['format']=CHBSPrice::format($price['total']['sum']['net']['value'],CHBSCurrency::getFormCurrency());
		$price['total']['sum']['gross']['format']=CHBSPrice::format($price['total']['sum']['gross']['value'],CHBSCurrency::getFormCurrency());
		
		$price['pay']=$price['total'];

		if(CHBSBookingHelper::isPaymentDepositEnable($data['booking_form']['meta']))
		{
			$price['pay']['sum']['gross']['value']=$price['pay']['sum']['gross']['value']*($data['booking_form']['meta']['payment_deposit_value']/100);
			$price['pay']['sum']['gross']['format']=CHBSPrice::format($price['pay']['sum']['gross']['value'],CHBSCurrency::getFormCurrency());
		}
		
		return($price);
	}

	/**************************************************************************/

	function createResponse($response)
	{
		echo json_encode($response);
		exit;
	}

	/**************************************************************************/

	static function createBillingTaxGroup(&$group,$taxValue,$valueNet,$valueGross)
	{
		if((!isset($group[$taxValue])) || (!is_array($group[$taxValue])))
			$group[$taxValue]=array('tax_value'=>$taxValue,'value'=>0.00,'value_net'=>0.00,'value_gross'=>0.00);

		$group[$taxValue]['value']+=$valueGross-$valueNet;
		
		$group[$taxValue]['value_net']+=$valueNet;
		$group[$taxValue]['value_gross']+=$valueGross;
		
		//if($group[$taxValue]['value']<=0)
		//	unset($group[$taxValue]);
	}

	/**************************************************************************/

	function createBilling($bookingId)
	{
		$billing=array('detail'=>array());

		if(($booking=$this->getBooking($bookingId))===false)
			return($billing);

		$Date=new CHBSDate();
		$Length=new CHBSLength();
		$TaxRate=new CHBSTaxRate();

		if($booking['meta']['price_type']==2)
		{
			$booking['meta']['price_initial_value']=0.00;
			$booking['meta']['price_initial_tax_rate_value']=0;
			$booking['meta']['price_initial_return_value']=0.00;
			$booking['meta']['price_initial_return_tax_rate_value']=0;
			$booking['meta']['price_initial_return_new_ride_value']=0.00;
			$booking['meta']['price_initial_return_new_ride_tax_rate_value']=0;
			
			$booking['meta']['price_delivery_value']=0.00;
			$booking['meta']['price_delivery_tax_rate_value']=0;

			$booking['meta']['price_delivery_return_value']=0.00;
			$booking['meta']['price_delivery_return_tax_rate_value']=0;
		}

		$returnFactorA=1;
		$returnFactorB=1;

		if(in_array($booking['meta']['service_type_id'],array(1,3)))
		{
			if(in_array($booking['meta']['transfer_type_id'],array(3)))
				$returnFactorA=2;

			if(in_array($booking['meta']['transfer_type_id'],array(2,3)))
				$returnFactorB=2;
		}
		
		if((int)$booking['meta']['price_type']===1)
		{
			if((int)$booking['meta']['transfer_type_id']===1)
			{
				$valueNet=$booking['meta']['price_initial_value'];

				$billing['detail'][]=array
				(
					'type'=>'initial_fee',
					'visible'=>($valueNet>0.00 ? true : false),
					'name'=>__('Initial fee','chauffeur-booking-system'),
					'unit'=>__('Item','chauffeur-booking-system'),
					'item'=>1,
					'duration'=>0,
					'duration_s1'=>0,
					'duration_s2'=>0,
					'distance'=>0,
					'distance_s1'=>0,
					'distance_s2'=>0,
					'passenger'=>0,
					'price_net'=>$booking['meta']['price_initial_value'],
					'value_net'=>$valueNet,
					'tax_value'=>$booking['meta']['price_initial_tax_rate_value'],
					'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_initial_tax_rate_value'])
				);
			}
		
			if((int)$booking['meta']['transfer_type_id']===2)
			{
				$valueNet=$booking['meta']['price_initial_return_value'];

				$billing['detail'][]=array
				(
					'type'=>'initial_fee_return',
					'visible'=>($valueNet>0.00 ? true : false),
					'name'=>__('Initial fee (return)','chauffeur-booking-system'),
					'unit'=>__('Item','chauffeur-booking-system'),
					'item'=>1,
					'duration'=>0,
					'duration_s1'=>0,
					'duration_s2'=>0,
					'distance'=>0,
					'distance_s1'=>0,
					'distance_s2'=>0,
					'passenger'=>0,
					'price_net'=>$booking['meta']['price_initial_return_value'],
					'value_net'=>$valueNet,
					'tax_value'=>$booking['meta']['price_initial_return_tax_rate_value'],
					'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_initial_return_tax_rate_value'])
				);
			}

			if((int)$booking['meta']['transfer_type_id']===3)
			{
				$valueNet=$booking['meta']['price_initial_return_new_ride_value'];

				$billing['detail'][]=array
				(
					'type'=>'initial_fee_return_new_ride',
					'visible'=>($valueNet>0.00 ? true : false),
					'name'=>__('Initial fee (return, new ride)','chauffeur-booking-system'),
					'unit'=>__('Item','chauffeur-booking-system'),
					'item'=>1,
					'duration'=>0,
					'duration_s1'=>0,
					'duration_s2'=>0,
					'distance'=>0,
					'distance_s1'=>0,
					'distance_s2'=>0,
					'passenger'=>0,
					'price_net'=>$booking['meta']['price_initial_value'],
					'value_net'=>$valueNet,
					'tax_value'=>$booking['meta']['price_initial_return_new_ride_tax_rate_value'],
					'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_initial_return_new_ride_tax_rate_value'])
				);
			}		
		}

		if((int)$booking['meta']['price_type']===1)
		{
			if($booking['meta']['length_unit']==2)
				$booking['meta']['base_location_distance']=$Length->convertUnit($booking['meta']['base_location_distance'],1,2);

			$baseLocationDistance=$booking['meta']['base_location_distance']*$returnFactorA;
			$baseLocationDuration=$booking['meta']['base_location_duration']*$returnFactorA;

			$valueNet=$baseLocationDistance*$booking['meta']['price_delivery_value'];

			$billing['detail'][]=array
			(
				'type'=>'delivery_fee',
				'visible'=>($valueNet>0.00 ? true: false),
				'name'=>__('Delivery fee','chauffeur-booking-system'),
				'unit'=>$Length->getUnitName($booking['meta']['length_unit']),
				'item'=>0,
				'duration'=>$baseLocationDuration,
				'duration_s1'=>$baseLocationDuration,
				'duration_s2'=>0,
				'distance'=>$baseLocationDistance,
				'distance_s1'=>$baseLocationDistance,
				'distance_s2'=>0,
				'passenger'=>0,
				'price_net'=>$booking['meta']['price_delivery_value'],
				'value_net'=>$valueNet,
				'tax_value'=>$booking['meta']['price_delivery_tax_rate_value'],
				'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_delivery_tax_rate_value'])
			);
		}

		if((int)$booking['meta']['price_type']===1)
		{
			if($booking['meta']['length_unit']==2)
				$booking['meta']['base_location_return_distance']=$Length->convertUnit($booking['meta']['base_location_return_distance'],1,2);

			$baseLocationReturnDistance=$booking['meta']['base_location_return_distance']*$returnFactorA;
			$baseLocationReturnDuration=$booking['meta']['base_location_return_duration']*$returnFactorA;

			$valueNet=$baseLocationReturnDistance*$booking['meta']['price_delivery_return_value'];

			$billing['detail'][]=array
			(
				'type'=>'delivery_return_fee',
				'visible'=>($valueNet>0.00 ? true: false),
				'name'=>__('Delivery fee (return)','chauffeur-booking-system'),
				'unit'=>$Length->getUnitName($booking['meta']['length_unit']),
				'item'=>0,
				'duration'=>$baseLocationReturnDuration,
				'duration_s1'=>$baseLocationReturnDuration,
				'duration_s2'=>0,
				'distance'=>$baseLocationReturnDistance,
				'distance_s1'=>$baseLocationReturnDistance,
				'distance_s2'=>0,
				'passenger'=>0,
				'price_net'=>$booking['meta']['price_delivery_return_value'],
				'value_net'=>$valueNet,
				'tax_value'=>$booking['meta']['price_delivery_return_tax_rate_value'],
				'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_delivery_return_tax_rate_value'])
			);
		}

		if($booking['meta']['length_unit']==2)
			$booking['meta']['distance']=$Length->convertUnit($booking['meta']['distance'],1,2);

		$duration=$booking['meta']['duration'];
		$distance=$booking['meta']['distance'];

		if((int)$booking['meta']['price_type']===2)
		{
			$billing['detail'][]=array
			(
				'type'=>'chauffeur_service',
				'visible'=>($booking['meta']['price_fixed_value']>0.00 ? true: false),
				'name'=>__('Chauffeur service','chauffeur-booking-system'),
				'unit'=>__('Item','chauffeur-booking-system'),
				'item'=>1,
				'duration'=>$duration,
				'duration_s1'=>$duration,
				'duration_s2'=>$duration,
				'distance'=>$distance,
				'distance_s1'=>$distance,
				'distance_s2'=>$distance,
				'passenger'=>0,
				'price_net'=>$booking['meta']['price_fixed_value'],
				'value_net'=>$booking['meta']['price_fixed_value'],
				'tax_value'=>$booking['meta']['price_fixed_tax_rate_value'],
				'value_gross'=>CHBSPrice::calculateGross($booking['meta']['price_fixed_value'],0,$booking['meta']['price_fixed_tax_rate_value'])
			);
		} 
		else
		{
			switch ($booking['meta']['calculation_method'])
			{
				case 1:
					
					$sum=0;
					$durationAdd=false;
					$distanceToCalculate=$distance;

					$priceNet=$booking['meta']['price_distance_value'];
	
					if((isset($booking['meta']['tax_rate_distance'])) && (is_array($booking['meta']['tax_rate_distance'])))
					{
						foreach($booking['meta']['tax_rate_distance'] as $index=>$value)
						{
							if(($TaxRate->isTaxRate($index)) && ($value['distance']>0))
							{
								$value['distance']=round($value['distance']/1000,1);

								$distanceToCalculate-=$value['distance'];
								
								$taxValue=$value['tax_rate_value'];

								$valueNet=$priceNet*$value['distance'];
								
								$billing['detail'][]=array
								(
									'type'=>'chauffeur_service_distance_'.$index,
									'visible'=>($valueNet>0.00 ? true: false),
									'name'=>sprintf(__('Chauffeur service (distance, %s, %s)','chauffeur-booking-system'),$value['geofence_name'],$taxValue.'%'),
									'unit'=>$Length->getUnitName($booking['meta']['length_unit']),
									'item'=>0,
									'duration'=>0,
									'duration_s1'=>($durationAdd ? 0 : $duration),
									'duration_s2'=>($durationAdd ? 0 : $duration),
									'distance'=>$value['distance'],
									'distance_s1'=>$value['distance'],
									'distance_s2'=>$value['distance'],
									'passenger'=>0,
									'price_net'=>$priceNet,
									'value_net'=>$valueNet,
									'tax_value'=>$taxValue,
									'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$taxValue)
								);
								
								$durationAdd=true;
							}
						}
					}
						
					if($distanceToCalculate>0)
					{
						$valueNet=$priceNet*$distanceToCalculate;

						$taxValue=$booking['meta']['price_distance_tax_rate_value'];

						$billing['detail'][]=array
						(
							'type'=>'chauffeur_service_distance',
							'visible'=>($valueNet>0.00 ? true: false),
							'name'=>__('Chauffeur service (distance)','chauffeur-booking-system'),
							'unit'=>$Length->getUnitName($booking['meta']['length_unit']),
							'item'=>0,
							'duration'=>0,
							'duration_s1'=>($durationAdd ? 0 : $duration),
							'duration_s2'=>($durationAdd ? 0 : $duration),
							'distance'=>$distanceToCalculate,
							'distance_s1'=>$distanceToCalculate,
							'distance_s2'=>$distanceToCalculate,
							'passenger'=>0,
							'price_net'=>$priceNet,
							'value_net'=>$valueNet,
							'tax_value'=>$taxValue,
							'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$taxValue)
						);							
					}

				break;

				case 2:

					$priceNetDuration=$booking['meta']['price_hour_value'];
					$taxValueDuration=$booking['meta']['price_hour_tax_rate_value'];
					$valueNetDuration=$priceNetDuration*($duration/60);

					$billing['detail'][]=array
					(
						'type'=>'chauffeur_service_duration',
						'visible'=>($valueNetDuration>0.00 ? true: false),
						'name'=>__('Chauffeur service (duration)','chauffeur-booking-system'),
						'unit'=>__('Hours','chauffeur-booking-system'),
						'item'=>0,
						'duration'=>$duration,
						'duration_s1'=>$duration,
						'duration_s2'=>$duration,
						'distance'=>0,
						'distance_s1'=>0,
						'distance_s2'=>0,
						'passenger'=>0,
						'price_net'=>$priceNetDuration,
						'value_net'=>$valueNetDuration,
						'tax_value'=>$taxValueDuration,
						'value_gross'=>CHBSPrice::calculateGross($valueNetDuration,0,$taxValueDuration)
					);
					
					$priceNetDistance=$booking['meta']['price_distance_value'];
					$taxValueDistance=$booking['meta']['price_distance_tax_rate_value'];
					$valueNetDistance=$priceNetDistance*$distance;

					$billing['detail'][]=array
					(
						'type'=>'chauffeur_service_distance',
						'visible'=>($valueNetDistance>0.00 ? true: false),
						'name'=>__('Chauffeur service (distance)','chauffeur-booking-system'),
						'unit'=>$Length->getUnitName($booking['meta']['length_unit']),
						'item'=>0,
						'duration'=>0,
						'duration_s1'=>0,
						'duration_s2'=>0,
						'distance'=>$distance,
						'distance_s1'=>$distance,
						'distance_s2'=>$distance,
						'passenger'=>0,
						'price_net'=>$priceNetDistance,
						'value_net'=>$valueNetDistance,
						'tax_value'=>$taxValueDistance,
						'value_gross'=>CHBSPrice::calculateGross($valueNetDistance,0,$taxValueDistance)
					);

				break;

				case 3: 

					$passengerNumber=$booking['meta']['passenger_adult_number']+$booking['meta']['passenger_children_number'];

					$priceNetDuration=$booking['meta']['price_hour_value'];
					$taxValueDuration=$booking['meta']['price_hour_tax_rate_value'];
					$valueNetDuration=$priceNetDuration*($duration/60)*$passengerNumber;

					$billing['detail'][]=array
					(
						'type'=>'chauffeur_service_duration_passenger',
						'visible'=>($valueNetDuration>0.00 ? true: false),
						'name'=>__('Chauffeur service (duration x no. passenger)','chauffeur-booking-system'),
						'unit'=>__('Hours x no. passenger','chauffeur-booking-system'),
						'item'=>0,
						'duration'=>$duration,
						'duration_s1'=>$duration,
						'duration_s2'=>$duration,
						'distance'=>0,
						'distance_s1'=>0,
						'distance_s2'=>0,
						'passenger'=>$passengerNumber,
						'price_net'=>$priceNetDuration,
						'value_net'=>$valueNetDuration,
						'tax_value'=>$taxValueDuration,
						'value_gross'=>CHBSPrice::calculateGross($valueNetDuration,0,$taxValueDuration)
					);

					$priceNetDistance=$booking['meta']['price_distance_value'];
					$taxValueDistance=$booking['meta']['price_distance_tax_rate_value'];
					$valueNetDistance=$priceNetDistance*$distance*$passengerNumber;

					$billing['detail'][]=array
					(
						'type'=>'chauffeur_service_distance_passenger',
						'visible'=>($valueNetDistance>0.00 ? true: false),
						'name'=>__('Chauffeur service (distance x no. passenger)','chauffeur-booking-system'),
						'unit'=>sprintf(__('%s x no. passengers','chauffeur-booking-system'),$Length->getUnitName($booking['meta']['length_unit'])),
						'item'=>0,
						'duration'=>0,
						'duration_s1'=>0,
						'duration_s2'=>0,
						'distance'=>$distance,
						'distance_s1'=>$distance,
						'distance_s2'=>$distance,
						'passenger'=>$passengerNumber,
						'price_net'=>$priceNetDistance,
						'value_net'=>$valueNetDistance,
						'tax_value'=>$taxValueDistance,
						'value_gross'=>CHBSPrice::calculateGross($valueNetDistance,0,$taxValueDistance)
					);

				break;

				case 4:

					$valueNet=$booking['meta']['passenger_adult_number']*$booking['meta']['price_passenger_adult_value'];

					$billing['detail'][]=array
					(
						'type'=>'chauffeur_passenger_adult',
						'visible'=>($valueNet>0.00 ? true: false),
						'name'=>__('Chauffeur service (no. adult passengers)','chauffeur-booking-system'),
						'unit'=>__('No. passengers','chauffeur-booking-system'),
						'item'=>0,
						'duration'=>$duration,
						'duration_s1'=>$duration,
						'duration_s2'=>$duration,
						'distance'=>$distance,
						'distance_s1'=>$distance,
						'distance_s2'=>$distance,
						'passenger'=>$booking['meta']['passenger_adult_number'],
						'price_net'=>$booking['meta']['price_passenger_adult_value'],
						'value_net'=>$valueNet,
						'tax_value'=>$booking['meta']['price_passenger_adult_tax_rate_value'],
						'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_passenger_adult_tax_rate_value'])
					);

					$valueNet=$booking['meta']['passenger_children_number']*$booking['meta']['price_passenger_children_value'];

					$billing['detail'][]=array
					(
						'type'=>'chauffeur_passenger_children',
						'visible'=>($valueNet>0.00 ? true: false),
						'name'=>__('Chauffeur service (no. children passengers)','chauffeur-booking-system'),
						'unit'=>__('No. passengers','chauffeur-booking-system'),
						'item'=>0,
						'duration'=>0,
						'duration_s1'=>0,
						'duration_s2'=>0,
						'distance'=>0,
						'distance_s1'=>0,
						'distance_s2'=>0,
						'passenger'=>$booking['meta']['passenger_children_number'],
						'price_net'=>$booking['meta']['price_passenger_children_value'],
						'value_net'=>$valueNet,
						'tax_value'=>$booking['meta']['price_passenger_children_tax_rate_value'],
						'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_passenger_children_tax_rate_value'])
					);

				break;

				case 5:

					$priceNet=$booking['meta']['price_hour_value']/60;
					$taxValue=$booking['meta']['price_hour_tax_rate_value'];

					$valueNet=$priceNet*$duration;

					$billing['detail'][]=array
					(
						'type'=>'chauffeur_service_duration',
						'visible'=>($valueNet>0.00 ? true: false),
						'name'=>__('Chauffeur service (duration)','chauffeur-booking-system'),
						'unit'=>__('Hours','chauffeur-booking-system'),
						'item'=>0,
						'duration'=>$duration,
						'duration_s1'=>$duration,
						'duration_s2'=>$duration,
						'distance'=>0,
						'distance_s1'=>0,
						'distance_s2'=>0,
						'passenger'=>0,
						'price_net'=>$booking['meta']['price_hour_value'],
						'value_net'=>$valueNet,
						'tax_value'=>$taxValue,
						'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$taxValue)
					);

				break;

				case 6:

					$passengerNumber=$booking['meta']['passenger_adult_number']+$booking['meta']['passenger_children_number'];

					$priceNet=$booking['meta']['price_hour_value']/60;
					$taxValue=$booking['meta']['price_hour_tax_rate_value'];

					$valueNet=$priceNet*$duration*$passengerNumber;

					$billing['detail'][]=array
					(
						'type'=>'chauffeur_service_duration_passenger',
						'visible'=>($valueNet>0.00 ? true: false),
						'name'=>__('Chauffeur service (duration x no. passengers)','chauffeur-booking-system'),
						'unit'=>__('Hours x no. passengers','chauffeur-booking-system'),
						'item'=>0,
						'duration'=>$duration,
						'duration_s1'=>$duration,
						'duration_s2'=>$duration,
						'distance'=>0,
						'distance_s1'=>0,
						'distance_s2'=>0,
						'passenger'=>$passengerNumber,
						'price_net'=>$booking['meta']['price_hour_value'],
						'value_net'=>$valueNet,
						'tax_value'=>$taxValue,
						'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$taxValue)
					);

				break;
			
				case 7:
					
					$priceNetDuration=$booking['meta']['price_hour_value'];
					$taxValueDuration=$booking['meta']['price_hour_tax_rate_value'];
					$valueNetDuration=$priceNetDuration*($duration/60);

					$billing['detail'][]=array
					(
						'type'=>'chauffeur_service_duration',
						'visible'=>($valueNetDuration>0.00 ? true: false),
						'name'=>__('Chauffeur service (duration)','chauffeur-booking-system'),
						'unit'=>__('Hours','chauffeur-booking-system'),
						'item'=>0,
						'duration'=>$duration,
						'duration_s1'=>$duration,
						'duration_s2'=>$duration,
						'distance'=>0,
						'distance_s1'=>0,
						'distance_s2'=>0,
						'passenger'=>0,
						'price_net'=>$priceNetDuration,
						'value_net'=>$valueNetDuration,
						'tax_value'=>$taxValueDuration,
						'value_gross'=>CHBSPrice::calculateGross($valueNetDuration,0,$taxValueDuration)
					);

					$priceNetDistance=$booking['meta']['price_distance_value'];
					$taxValueDistance=$booking['meta']['price_distance_tax_rate_value'];
					$valueNetDistance=$priceNetDistance*$distance;

					$billing['detail'][]=array
					(
						'type'=>'chauffeur_service_distance',
						'visible'=>($valueNetDistance>0.00 ? true: false),
						'name'=>__('Chauffeur service (distance)','chauffeur-booking-system'),
						'unit'=>$Length->getUnitName($booking['meta']['length_unit']),
						'item'=>0,
						'duration'=>0,
						'duration_s1'=>0,
						'duration_s2'=>0,
						'distance'=>$distance,
						'distance_s1'=>$distance,
						'distance_s2'=>$distance,
						'passenger'=>0,
						'price_net'=>$priceNetDistance,
						'value_net'=>$valueNetDistance,
						'tax_value'=>$taxValueDistance,
						'value_gross'=>CHBSPrice::calculateGross($valueNetDistance,0,$taxValueDistance)
					);
					
					$valueNet=$booking['meta']['passenger_adult_number']*$booking['meta']['price_passenger_adult_value'];

					$billing['detail'][]=array
					(
						'type'=>'chauffeur_passenger_adult',
						'visible'=>($valueNet>0.00 ? true: false),
						'name'=>__('Chauffeur service (no. adult passengers)','chauffeur-booking-system'),
						'unit'=>__('No. passengers','chauffeur-booking-system'),
						'item'=>0,
						'duration'=>0,
						'duration_s1'=>0,
						'duration_s2'=>0,
						'distance'=>0,
						'distance_s1'=>0,
						'distance_s2'=>0,
						'passenger'=>$booking['meta']['passenger_adult_number'],
						'price_net'=>$booking['meta']['price_passenger_adult_value'],
						'value_net'=>$valueNet,
						'tax_value'=>$booking['meta']['price_passenger_adult_tax_rate_value'],
						'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_passenger_adult_tax_rate_value'])
					);

					$valueNet=$booking['meta']['passenger_children_number']*$booking['meta']['price_passenger_children_value'];

					$billing['detail'][]=array
					(
						'type'=>'chauffeur_passenger_children',
						'visible'=>($valueNet>0.00 ? true: false),
						'name'=>__('Chauffeur service (no. children passengers)','chauffeur-booking-system'),
						'unit'=>__('No. passengers','chauffeur-booking-system'),
						'item'=>0,
						'duration'=>0,
						'duration_s1'=>0,
						'duration_s2'=>0,
						'distance'=>0,
						'distance_s1'=>0,
						'distance_s2'=>0,
						'passenger'=>$booking['meta']['passenger_children_number'],
						'price_net'=>$booking['meta']['price_passenger_children_value'],
						'value_net'=>$valueNet,
						'tax_value'=>$booking['meta']['price_passenger_children_tax_rate_value'],
						'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_passenger_children_tax_rate_value'])
					);
					
				break;
			}
		}

		if(in_array($booking['meta']['service_type_id'],array(1,3)))
		{
			if(in_array($booking['meta']['transfer_type_id'],array(2,3)))
			{
				if((int)$booking['meta']['price_type']===1)
				{
					$prefix=(int)$booking['meta']['transfer_type_id']===3 ? '_new_ride_' : '_';
					
					switch($booking['meta']['calculation_method'])
					{
						case 1:
							
							$priceNetDistance=$booking['meta']['price_distance_return'.$prefix.'value'];
							$taxValueDistance=$booking['meta']['price_distance_return'.$prefix.'tax_rate_value'];						

							$valueNetDistance=$priceNetDistance*$distance;
							
							$billing['detail'][]=array
							(
								'type'=>'chauffeur_service_distance_return',
								'visible'=>($valueNetDistance>0.00 ? true: false),
								'name'=>__('Chauffeur service (distance, return)','chauffeur-booking-system'),
								'unit'=>$Length->getUnitName($booking['meta']['length_unit']),
								'item'=>0,
								'duration'=>0,
								'duration_s1'=>$duration,
								'duration_s2'=>$duration,
								'distance'=>$distance,
								'distance_s1'=>$distance,
								'distance_s2'=>$distance,
								'passenger'=>0,
								'price_net'=>$priceNetDistance,
								'value_net'=>$valueNetDistance,
								'tax_value'=>$taxValueDistance,
								'value_gross'=>CHBSPrice::calculateGross($valueNetDistance,0,$taxValueDistance)
							);
							
						break;
						
						case 2:
							
							$priceNetDuration=$booking['meta']['price_hour_return'.$prefix.'value'];
							$taxValueDuration=$booking['meta']['price_hour_return'.$prefix.'tax_rate_value'];
							$valueNetDuration=($priceNetDuration/60)*$duration;							
			
							$billing['detail'][]=array
							(
								'type'=>'chauffeur_service_duration_return',
								'visible'=>($valueNetDuration>0.00 ? true: false),
								'name'=>__('Chauffeur service (duration, return)','chauffeur-booking-system'),
								'unit'=>__('Hours','chauffeur-booking-system'),
								'item'=>0,
								'duration'=>$duration,
								'duration_s1'=>$duration,
								'duration_s2'=>$duration,
								'distance'=>0,
								'distance_s1'=>0,
								'distance_s2'=>0,
								'passenger'=>0,
								'price_net'=>$priceNetDuration,
								'value_net'=>$valueNetDuration,
								'tax_value'=>$taxValueDuration,
								'value_gross'=>CHBSPrice::calculateGross($valueNetDuration,0,$taxValueDuration)
							);
							
							$priceNetDistance=$booking['meta']['price_distance_return'.$prefix.'value'];
							$taxValueDistance=$booking['meta']['price_distance_return'.$prefix.'tax_rate_value'];						
							$valueNetDistance=$priceNetDistance*$distance;
							
							$billing['detail'][]=array
							(
								'type'=>'chauffeur_service_distance_return',
								'visible'=>($valueNetDistance>0.00 ? true: false),
								'name'=>__('Chauffeur service (distance, return)','chauffeur-booking-system'),
								'unit'=>$Length->getUnitName($booking['meta']['length_unit']),
								'item'=>0,
								'duration'=>0,
								'duration_s1'=>0,
								'duration_s2'=>0,
								'distance'=>$distance,
								'distance_s1'=>$distance,
								'distance_s2'=>$distance,
								'passenger'=>0,
								'price_net'=>$priceNetDistance,
								'value_net'=>$valueNetDistance,
								'tax_value'=>$taxValueDistance,
								'value_gross'=>CHBSPrice::calculateGross($valueNetDistance,0,$taxValueDistance)
							);	
							
						break;
						
						case 3:
							
							$passengerNumber=$booking['meta']['passenger_adult_number']+$booking['meta']['passenger_children_number'];
							
							$priceNetDuration=$booking['meta']['price_hour_return'.$prefix.'value'];
							$taxValueDuration=$booking['meta']['price_hour_return'.$prefix.'tax_rate_value'];
							$valueNetDuration=($priceNetDuration/60)*$duration*$passengerNumber;							
					
							$billing['detail'][]=array
							(
								'type'=>'chauffeur_service_duration_passenger_return',
								'visible'=>($valueNetDuration>0.00 ? true: false),
								'name'=>__('Chauffeur service (return, duration x no. passenger)','chauffeur-booking-system'),
								'unit'=>__('Hours x no. passenger','chauffeur-booking-system'),
								'item'=>0,
								'duration'=>$duration,
								'duration_s1'=>$duration,
								'duration_s2'=>$duration,
								'distance'=>0,
								'distance_s1'=>0,
								'distance_s2'=>0,
								'passenger'=>$passengerNumber,
								'price_net'=>$priceNetDuration,
								'value_net'=>$valueNetDuration,
								'tax_value'=>$taxValueDuration,
								'value_gross'=>CHBSPrice::calculateGross($valueNetDuration,0,$taxValueDuration)
							);
						
							$priceNetDistance=$booking['meta']['price_distance_return'.$prefix.'value'];
							$taxValueDistance=$booking['meta']['price_distance_return'.$prefix.'tax_rate_value'];						
							$valueNetDistance=$priceNetDistance*$distance*$passengerNumber;
							
							$billing['detail'][]=array
							(
								'type'=>'chauffeur_service_distance_passenger_return',
								'visible'=>($valueNetDistance>0.00 ? true: false),
								'name'=>__('Chauffeur service (return, distance x no. passenger)','chauffeur-booking-system'),
								'unit'=>sprintf(__('%s x no. passengers','chauffeur-booking-system'),$Length->getUnitName($booking['meta']['length_unit'])),
								'item'=>0,
								'duration'=>0,
								'duration_s1'=>0,
								'duration_s2'=>0,
								'distance'=>$distance,
								'distance_s1'=>$distance,
								'distance_s2'=>$distance,
								'passenger'=>$passengerNumber,
								'price_net'=>$priceNetDistance,
								'value_net'=>$valueNetDistance,
								'tax_value'=>$taxValueDistance,
								'value_gross'=>CHBSPrice::calculateGross($valueNetDistance,0,$taxValueDistance)
							);	
							
						break;
					
						case 4:
							
							$valueNet=$booking['meta']['passenger_adult_number']*$booking['meta']['price_passenger_adult_value'];

							$billing['detail'][]=array
							(
								'type'=>'chauffeur_passenger_adult_return',
								'visible'=>($valueNet>0.00 ? true: false),
								'name'=>__('Chauffeur service (no. adult passengers, return)','chauffeur-booking-system'),
								'unit'=>__('No. passengers','chauffeur-booking-system'),
								'item'=>0,
								'duration'=>$duration,
								'duration_s1'=>$duration,
								'duration_s2'=>$duration,
								'distance'=>$distance,
								'distance_s1'=>$distance,
								'distance_s2'=>$distance,
								'passenger'=>$booking['meta']['passenger_adult_number'],
								'price_net'=>$booking['meta']['price_passenger_adult_value'],
								'value_net'=>$valueNet,
								'tax_value'=>$booking['meta']['price_passenger_adult_tax_rate_value'],
								'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_passenger_adult_tax_rate_value'])
							);

							$valueNet=$booking['meta']['passenger_children_number']*$booking['meta']['price_passenger_children_value'];

							$billing['detail'][]=array
							(
								'type'=>'chauffeur_passenger_children_return',
								'visible'=>($valueNet>0.00 ? true: false),
								'name'=>__('Chauffeur service (no. children passengers, return)','chauffeur-booking-system'),
								'unit'=>__('No. passengers','chauffeur-booking-system'),
								'item'=>0,
								'duration'=>0,
								'duration_s1'=>0,
								'duration_s2'=>0,
								'distance'=>0,
								'distance_s1'=>0,
								'distance_s2'=>0,
								'passenger'=>$booking['meta']['passenger_children_number'],
								'price_net'=>$booking['meta']['price_passenger_children_value'],
								'value_net'=>$valueNet,
								'tax_value'=>$booking['meta']['price_passenger_children_tax_rate_value'],
								'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_passenger_children_tax_rate_value'])
							);
							
						break;
					
						case 7:
							
							$priceNetDuration=$booking['meta']['price_hour_return'.$prefix.'value'];
							$taxValueDuration=$booking['meta']['price_hour_return'.$prefix.'tax_rate_value'];
							$valueNetDuration=$priceNetDuration*($duration/60);

							$billing['detail'][]=array
							(
								'type'=>'chauffeur_service_duration_return',
								'visible'=>($valueNetDuration>0.00 ? true: false),
								'name'=>__('Chauffeur service (duration,return)','chauffeur-booking-system'),
								'unit'=>__('Hours','chauffeur-booking-system'),
								'item'=>0,
								'duration'=>$duration,
								'duration_s1'=>$duration,
								'duration_s2'=>$duration,
								'distance'=>0,
								'distance_s1'=>0,
								'distance_s2'=>0,
								'passenger'=>0,
								'price_net'=>$priceNetDuration,
								'value_net'=>$valueNetDuration,
								'tax_value'=>$taxValueDuration,
								'value_gross'=>CHBSPrice::calculateGross($valueNetDuration,0,$taxValueDuration)
							);

							$priceNetDistance=$booking['meta']['price_distance_return'.$prefix.'value'];
							$taxValueDistance=$booking['meta']['price_distance_return'.$prefix.'tax_rate_value'];
							$valueNetDistance=$priceNetDistance*$distance;

							$billing['detail'][]=array
							(
								'type'=>'chauffeur_service_distance_return',
								'visible'=>($valueNetDistance>0.00 ? true: false),
								'name'=>__('Chauffeur service (distance,return)','chauffeur-booking-system'),
								'unit'=>$Length->getUnitName($booking['meta']['length_unit']),
								'item'=>0,
								'duration'=>0,
								'duration_s1'=>0,
								'duration_s2'=>0,
								'distance'=>$distance,
								'distance_s1'=>$distance,
								'distance_s2'=>$distance,
								'passenger'=>0,
								'price_net'=>$priceNetDistance,
								'value_net'=>$valueNetDistance,
								'tax_value'=>$taxValueDistance,
								'value_gross'=>CHBSPrice::calculateGross($valueNetDistance,0,$taxValueDistance)
							);

							$valueNet=$booking['meta']['passenger_adult_number']*$booking['meta']['price_passenger_adult_value'];

							$billing['detail'][]=array
							(
								'type'=>'chauffeur_passenger_adult_return',
								'visible'=>($valueNet>0.00 ? true: false),
								'name'=>__('Chauffeur service (no. adult passengers, return)','chauffeur-booking-system'),
								'unit'=>__('No. passengers','chauffeur-booking-system'),
								'item'=>0,
								'duration'=>0,
								'duration_s1'=>0,
								'duration_s2'=>0,
								'distance'=>0,
								'distance_s1'=>0,
								'distance_s2'=>0,
								'passenger'=>$booking['meta']['passenger_adult_number'],
								'price_net'=>$booking['meta']['price_passenger_adult_value'],
								'value_net'=>$valueNet,
								'tax_value'=>$booking['meta']['price_passenger_adult_tax_rate_value'],
								'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_passenger_adult_tax_rate_value'])
							);

							$valueNet=$booking['meta']['passenger_children_number']*$booking['meta']['price_passenger_children_value'];

							$billing['detail'][]=array
							(
								'type'=>'chauffeur_passenger_children_return',
								'visible'=>($valueNet>0.00 ? true: false),
								'name'=>__('Chauffeur service (no. children passengers, return)','chauffeur-booking-system'),
								'unit'=>__('No. passengers','chauffeur-booking-system'),
								'item'=>0,
								'duration'=>0,
								'duration_s1'=>0,
								'duration_s2'=>0,
								'distance'=>0,
								'distance_s1'=>0,
								'distance_s2'=>0,
								'passenger'=>$booking['meta']['passenger_children_number'],
								'price_net'=>$booking['meta']['price_passenger_children_value'],
								'value_net'=>$valueNet,
								'tax_value'=>$booking['meta']['price_passenger_children_tax_rate_value'],
								'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_passenger_children_tax_rate_value'])
							);
							
						break;
					}
				} 
				else
				{
					if((int)$booking['meta']['transfer_type_id']===3)
					{
						$priceNet=$booking['meta']['price_fixed_return_new_ride_value'];
						$taxValue=$booking['meta']['price_fixed_return_new_ride_tax_rate_value'];
					} 
					else
					{
						$priceNet=$booking['meta']['price_fixed_return_value'];
						$taxValue=$booking['meta']['price_fixed_return_tax_rate_value'];
					}

					$valueNet=$priceNet;

					$billing['detail'][]=array
					(
						'type'=>'chauffeur_service_return',
						'visible'=>($valueNet>0.00 ? true: false),
						'name'=>__('Chauffeur service (return)','chauffeur-booking-system'),
						'unit'=>__('Item','chauffeur-booking-system'),
						'item'=>1,
						'duration'=>$duration,
						'duration_s1'=>$duration,
						'duration_s2'=>$duration,
						'distance'=>$distance,
						'distance_s1'=>$distance,
						'distance_s2'=>$distance,
						'price_net'=>$priceNet,
						'value_net'=>$valueNet,
						'tax_value'=>$taxValue,
						'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$taxValue)
					);
				}
			}
		}

		if(in_array($booking['meta']['service_type_id'],array(1,3)))
		{
			if($booking['meta']['extra_time_enable']==1)
			{
				$priceNet=$booking['meta']['price_extra_time_value'];
				$valueNet=$priceNet*($booking['meta']['extra_time_value']/60);

				$billing['detail'][]=array
				(
					'type'=>'extra_time',
					'visible'=>($valueNet>0.00 ? true: false),
					'name'=>__('Extra time','chauffeur-booking-system'),
					'unit'=>__('Hours','chauffeur-booking-system'),
					'item'=>0,
					'duration'=>$booking['meta']['extra_time_value'],
					'duration_s1'=>$booking['meta']['extra_time_value'],
					'duration_s2'=>0,
					'distance'=>0,
					'distance_s1'=>0,
					'distance_s2'=>0,
					'passenger'=>0,
					'price_net'=>$priceNet,
					'value_net'=>$valueNet,
					'tax_value'=>$booking['meta']['price_extra_time_tax_rate_value'],
					'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_extra_time_tax_rate_value'])
				);
			}
            
 			if($booking['meta']['waypoint_count']>0)
			{
				$priceNet=$booking['meta']['price_waypoint_value'];
				$valueNet=$priceNet*$booking['meta']['waypoint_count'];

				$billing['detail'][]=array
				(
					'type'=>'waypoint',
					'visible'=>($valueNet>0.00 ? true: false),
					'name'=>__('Waypoints','chauffeur-booking-system'),
					'unit'=>__('Item','chauffeur-booking-system'),
					'item'=>$booking['meta']['waypoint_count'],
					'duration'=>0,
					'duration_s1'=>0,
					'duration_s2'=>0,
					'distance'=>0,
					'distance_s1'=>0,
					'distance_s2'=>0,
					'passenger'=>0,
					'price_net'=>$priceNet,
					'value_net'=>$valueNet,
					'tax_value'=>$booking['meta']['price_waypoint_tax_rate_value'],
					'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_waypoint_tax_rate_value'])
				);
			}   
			
 			if($booking['meta']['waypoint_duration']>0)
			{
				$priceNet=$booking['meta']['price_waypoint_duration_value'];
				$valueNet=$priceNet*$booking['meta']['waypoint_duration'];

				$billing['detail'][]=array
				(
					'type'=>'waypoint',
					'visible'=>($valueNet>0.00 ? true: false),
					'name'=>__('Waypoints duration','chauffeur-booking-system'),
					'unit'=>__('Minute','chauffeur-booking-system'),
					'item'=>0,
					'duration'=>$booking['meta']['waypoint_duration'],
					'duration_s1'=>$booking['meta']['waypoint_duration'],
					'duration_s2'=>$booking['meta']['waypoint_duration'],
					'distance'=>0,
					'distance_s1'=>0,
					'distance_s2'=>0,
					'passenger'=>0,
					'price_net'=>$priceNet,
					'value_net'=>$valueNet,
					'tax_value'=>$booking['meta']['price_waypoint_duration_tax_rate_value'],
					'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$booking['meta']['price_waypoint_duration_tax_rate_value'])
				);
			}   			
		}

		if(is_array($booking['meta']['booking_extra']))
		{
			foreach($booking['meta']['booking_extra'] as $value)
			{
				$priceNet=$value['price'];
				$valueNet=$priceNet*$value['quantity'];

				$billing['detail'][]=array
				(
					'type'=>'booking_extra',
					'visible'=>($valueNet>0.00 ? true: false),
					'name'=>$value['name'],
					'unit'=>__('Item','chauffeur-booking-system'),
					'item'=>$value['quantity'],
					'duration'=>0,
					'duration_s1'=>0,
					'duration_s2'=>0,
					'distance'=>0,
					'distance_s1'=>0,
					'distance_s2'=>0,
					'passenger'=>0,
					'price_net'=>$priceNet,
					'value_net'=>$valueNet,
					'tax_value'=>$value['tax_rate_value'],
					'value_gross'=>CHBSPrice::calculateGross($valueNet,0,$value['tax_rate_value'])
				);
			}
		}

		$billing['detail'][]=array
		(
			'type'=>'gratuity',
			'visible'=>($booking['meta']['gratuity_value']>0.00 ? true: false),
			'name'=>__('Gratuity','chauffeur-booking-system'),
			'unit'=>__('Item','chauffeur-booking-system'),
			'item'=>1,
			'duration'=>0,
			'duration_s1'=>0,
			'duration_s2'=>0,
			'distance'=>0,
			'distance_s1'=>0,
			'distance_s2'=>0,
			'passenger'=>0,
			'price_net'=>$booking['meta']['gratuity_value'],
			'value_net'=>$booking['meta']['gratuity_value'],
			'tax_value'=>0.00,
			'value_gross'=>$booking['meta']['gratuity_value']
		);

		$billing['detail'][]=array
		(
			'type'=>'paypal_fee',
			'visible'=>($booking['meta']['paypal_fee']>0.00 ? true: false),
			'name'=>__('PayPal fee','chauffeur-booking-system'),
			'unit'=>__('Item','chauffeur-booking-system'),
			'item'=>1,
			'duration'=>0,
			'duration_s1'=>0,
			'duration_s2'=>0,
			'distance'=>0,
			'distance_s1'=>0,
			'distance_s2'=>0,
			'passenger'=>0,
			'price_net'=>$booking['meta']['paypal_fee'],
			'value_net'=>$booking['meta']['paypal_fee'],
			'tax_value'=>0.00,
			'value_gross'=>$booking['meta']['paypal_fee']
		);

		$billing['detail'][]=array
		(
			'type'=>'stripe_fee',
			'visible'=>($booking['meta']['stripe_fee']!=0.00 ? true: false),
			'name'=>__('Stripe fee','chauffeur-booking-system'),
			'unit'=>__('Item','chauffeur-booking-system'),
			'item'=>1,
			'duration'=>0,
			'duration_s1'=>0,
			'duration_s2'=>0,
			'distance'=>0,
			'distance_s1'=>0,
			'distance_s2'=>0,
			'passenger'=>0,
			'price_net'=>$booking['meta']['stripe_fee'],
			'value_net'=>$booking['meta']['stripe_fee'],
			'tax_value'=>0.00,
			'value_gross'=>$booking['meta']['stripe_fee']
		);

		$billing['detail'][]=array
		(
			'type'=>'round_value',
			'visible'=>($booking['meta']['price_round_value']!=0.00 ? true : false),
			'name'=>__('Round value','chauffeur-booking-system'),
			'unit'=>__('Item','chauffeur-booking-system'),
			'item'=>1,
			'duration'=>0,
			'duration_s1'=>0,
			'duration_s2'=>0,
			'distance'=>0,
			'distance_s1'=>0,
			'distance_s2'=>0,
			'passenger'=>0,
			'price_net'=>$booking['meta']['price_round_value'],
			'value_net'=>$booking['meta']['price_round_value'],
			'tax_value'=>0.00,
			'value_gross'=>$booking['meta']['price_round_value']
		);
		
		
		/***/
		
		foreach($billing['detail'] as $index=>$value)
		{
			$billing['detail'][$index]['quantity_label']=$this->getBillingQuantityLabel($value,$booking['meta']['length_unit']);
		}
		
		/***/
	
		$rBilling=array();

		foreach($billing['detail'] as $value)
		{
			if($value['value_gross']==0)
				continue;

			$taxValue=$value['tax_value'];

			if(!isset($rBilling[$taxValue]))
			{
				$rBilling[$taxValue]=array
				(
					'price_net'=>0,
					'value_net'=>0,
					'value_gross'=>0
				);
			}

			$rBilling[$taxValue]=array
			(
				'type'=>'chauffeur_service_'.$value['tax_value'],
				'visible'=>1,
				'name'=>__('Chauffeur service','chauffeur-booking-system'),
				'unit'=>0,
				'item'=>0,
				'duration'=>0,
				'duration_s1'=>0,
				'duration_s2'=>0,
				'distance'=>0,
				'distance_s1'=>0,
				'distance_s2'=>0,
				'passenger'=>0,
				'price_net'=>$rBilling[$taxValue]['price_net']+$value['price_net'],
				'value_net'=>$rBilling[$taxValue]['value_net']+$value['value_net'],
				'tax_value'=>$taxValue,
				'value_gross'=>$rBilling[$taxValue]['value_gross']+$value['value_gross']
			);
		}
	
		if(count($rBilling)>1)
		{
			foreach($rBilling as $index=> $value)
			{
				$name=sprintf(__('Chauffeur service (tax %.2f%%)','chauffeur-booking-system'),$value['tax_value'].'%');
				$rBilling[$index]['name']=$name;
			}
		}

		$billing['reduced']=$rBilling;

		$billing['summary']['duration']=0;
		$billing['summary']['duration_s1']=0;
		$billing['summary']['duration_s2']=0;
		$billing['summary']['distance']=0;
		$billing['summary']['distance_s1']=0;
		$billing['summary']['distance_s2']=0;
		$billing['summary']['value_net']=0;
		$billing['summary']['value_gross']=0;
		$billing['summary']['tax_sum']=0;
		
		foreach($billing['detail'] as $value)
		{
			$billing['summary']['duration']+=$value['duration'];
			$billing['summary']['distance']+=$value['distance'];

			$billing['summary']['value_net']+=$value['value_net'];
			$billing['summary']['value_gross']+=$value['value_gross'];
			$billing['summary']['tax_sum']+=$value['value_gross']-$value['value_net'];
			
			$billing['summary']['duration_s1']+=$value['duration_s1'];
			$billing['summary']['duration_s2']+=$value['duration_s2'];

			$billing['summary']['distance_s1']+=$value['distance_s1'];
			$billing['summary']['distance_s2']+=$value['distance_s2'];
		}
		
		$billing['summary']['duration_minute']=$billing['summary']['duration'];
		$billing['summary']['duration_s1_minute']=$billing['summary']['duration_s1'];
		$billing['summary']['duration_s2_minute']=$billing['summary']['duration_s2'];

		$billing['summary']['duration']=$Date->formatMinuteToTime($billing['summary']['duration']);
		$billing['summary']['duration_s1']=$Date->formatMinuteToTime($billing['summary']['duration_s1']);
		$billing['summary']['duration_s2']=$Date->formatMinuteToTime($billing['summary']['duration_s2']);

		foreach($billing['summary'] as $aIndex=> $aValue)
		{
			if(in_array($aIndex,array('value_net','value_gross','tax_value')))
				$billing['summary'][$aIndex]=CHBSPrice::numberFormat($aValue,$booking['meta']['currency_id']);
		}

		if(CHBSBookingHelper::isPaymentDepositEnable($booking['meta'],$bookingId)==1)
			$billing['summary']['pay']=CHBSPrice::numberFormat($billing['summary']['value_gross']*($booking['meta']['payment_deposit_value']/100),$booking['meta']['currency_id']);
		else $billing['summary']['pay']=$billing['summary']['value_gross'];

		/***/
		
		$billing['summary']['vehicle']['one_way']['value_net']=0.00;
		$billing['summary']['vehicle']['one_way']['value_gross']=0.00;
		$billing['summary']['vehicle']['return']['value_net']=0.00;
		$billing['summary']['vehicle']['return']['value_gross']=0.00;
		
		$element=array
		(
			'one_way'=>array
			(
				'chauffeur_service_distance',
				'chauffeur_service_duration',
				'chauffeur_service_duration_passenger',
				'chauffeur_service_distance_passenger',
				'chauffeur_passenger_adult',
				'chauffeur_passenger_children',
				'chauffeur_service'
			),
			'return'=>array
			(
				'chauffeur_service_distance_return',
				'chauffeur_service_duration_return',
				'chauffeur_service_duration_passenger_return',
				'chauffeur_service_distance_passenger_return',
				'chauffeur_passenger_adult_return',
				'chauffeur_passenger_children_return',
				'chauffeur_service_return'
			)
		);
		
		foreach($billing['detail'] as $index=>$value)
		{
			if(in_array($value['type'],$element['one_way']))
			{
				$billing['summary']['vehicle']['one_way']['value_net']+=$value['value_net'];
				$billing['summary']['vehicle']['one_way']['value_gross']+=$value['value_gross'];				
			}
			elseif(in_array($value['type'],$element['return']))
			{
				$billing['summary']['vehicle']['return']['value_net']+=$value['value_net'];
				$billing['summary']['vehicle']['return']['value_gross']+=$value['value_gross'];				
			}
		}
		
		$billing['summary']['vehicle']['one_way']['value_net']=CHBSPrice::numberFormat($billing['summary']['vehicle']['one_way']['value_net'],$booking['meta']['currency_id']);
		$billing['summary']['vehicle']['one_way']['value_gross']=CHBSPrice::numberFormat($billing['summary']['vehicle']['one_way']['value_gross'],$booking['meta']['currency_id']);
		$billing['summary']['vehicle']['return']['value_net']=CHBSPrice::numberFormat($billing['summary']['vehicle']['return']['value_net'],$booking['meta']['currency_id']);
		$billing['summary']['vehicle']['return']['value_gross']=CHBSPrice::numberFormat($billing['summary']['vehicle']['return']['value_gross'],$booking['meta']['currency_id']);
		
		/***/
		
		$taxGroup=array();

		foreach($billing['detail'] as $value)
			self::createBillingTaxGroup($taxGroup,$value['tax_value'],$value['value_net'],$value['value_gross']);

		foreach($billing['detail'] as $aIndex=> $aValue)
		{
			foreach($aValue as $bIndex=>$bValue)
			{
				if(in_array($bIndex,array('price_net','value_net','tax_value','value_gross')))
					$billing['detail'][$aIndex][$bIndex]=CHBSPrice::numberFormat($bValue,$booking['meta']['currency_id']);
				elseif($bIndex=='duration')
					$billing['detail'][$aIndex][$bIndex]=$Date->formatMinuteToTime($bValue);
			}
		}

		$billing['tax_group']=$taxGroup;

		return($billing);
	}
	
	/**************************************************************************/
	
	function getBillingQuantityLabel($line,$lengthUnit)
	{
		$label=null;
		
		$Length=new CHBSLength();
		$Validation=new CHBSValidation();
		
		$field=array
		(
			'item'=>array(__('item','chauffeur-booking-system')),
			'distance'=>array(__($Length->label($lengthUnit,7),'chauffeur-booking-system')),
			'duration'=>array(__('minutes','chauffeur-booking-system')),
			'passenger'=>array(__('passengers','chauffeur-booking-system'))
		);
			
		foreach($field as $fieldIndex=>$fieldValue)
		{
			if($line[$fieldIndex]>0)
			{
				if($Validation->isNotEmpty($label)) $label.=', ';
				$label.=$line[$fieldIndex].' '.$fieldValue[0];
				
			}
		}
		
		return($label);
	}

	/**************************************************************************/

	function sendEmail($bookingId,$emailAccountId,$template,$recipient,$subject)
	{
		$Email=new CHBSEmail();
		$EmailAccount=new CHBSEmailAccount();
		$BookingStatus=new CHBSBookingStatus();

		if(($booking=$this->getBooking($bookingId))===false)
			return(false);

		if(($emailAccount=$EmailAccount->getDictionary(array('email_account_id'=>$emailAccountId)))===false)
			return(false);

		if(!isset($emailAccount[$emailAccountId]))
			return(false);

		$data=array();

		$emailAccount=$emailAccount[$emailAccountId];

		global $chbs_phpmailer;

		$chbs_phpmailer['sender_name']=$emailAccount['meta']['sender_name'];
		$chbs_phpmailer['sender_email_address']=$emailAccount['meta']['sender_email_address'];

		$chbs_phpmailer['smtp_auth_enable']=$emailAccount['meta']['smtp_auth_enable'];
		$chbs_phpmailer['smtp_auth_debug_enable']=$emailAccount['meta']['smtp_auth_debug_enable'];

		$chbs_phpmailer['smtp_auth_username']=$emailAccount['meta']['smtp_auth_username'];
		$chbs_phpmailer['smtp_auth_password']=$emailAccount['meta']['smtp_auth_password'];

		$chbs_phpmailer['smtp_auth_host']=$emailAccount['meta']['smtp_auth_host'];
		$chbs_phpmailer['smtp_auth_port']=$emailAccount['meta']['smtp_auth_port'];

		$chbs_phpmailer['smtp_auth_secure_connection_type']=$emailAccount['meta']['smtp_auth_secure_connection_type'];

		if(in_array($template,array('booking_new_admin')))
		{
			$chbs_phpmailer['reply_to_email_address']=$booking['meta']['client_contact_detail_email_address'];		
		}
		
		$booking['booking_title']=$booking['post']->post_title;

		if(in_array($template,array('booking_new_admin','booking_driver_accept','booking_driver_reject')))
			$booking['booking_title']='<a href="'.admin_url('post.php?post='.(int)$booking['post']->ID.'&action=edit').'">'.$booking['booking_title'].'</a>';
		else unset($booking['booking_form_name']);
		
		if(in_array($template,array('booking_new_admin','booking_new_client','booking_assign_driver','booking_change_status')))
		{
			$templateFile='email_booking.php';
		}

		if($template=='booking_change_status')
		{
			$bookingStatusId=(int)$booking['meta']['booking_status_id'];
			$bookingStatusName=$BookingStatus->mapBookingStatus($bookingStatusId);
			
			$fileName1='email_booking_status_'.$bookingStatusId.'.php';
			$fileName2='email_booking_status_'.$bookingStatusName.'.php';
			
			$filePath1=PLUGIN_CHBS_TEMPLATE_PATH.$fileName1;
			$filePath2=PLUGIN_CHBS_TEMPLATE_PATH.$fileName2;
			
			if(CHBSFile::fileExist($filePath1)) $templateFile=$fileName1;
			else if(CHBSFile::fileExist($filePath2)) $templateFile=$fileName2;
		}
	
		if(in_array($template,array('booking_unassign_driver')))
		{
			$templateFile='email_booking_unassign_driver.php';
		}
		
		if(in_array($template,array('booking_assign_driver')))
		{
			$BookingDriver=new CHBSBookingDriver();

			$link=$BookingDriver->generateLink($booking['post']->ID);

			if(is_array($link))
			{
				$data['booking_driver_accept_link']=$link['accept'];
				$data['booking_driver_reject_link']=$link['reject'];
			}
		}
		
		if(in_array($template,array('booking_new_admin','booking_new_client','booking_change_status')))
		{
			$BookingCancel=new CHBSBookingCancel();

			$link=$BookingCancel->generateBookingCancelURL($booking['post']->ID);

			if($link!==false) $data['booking_cancel_link']=$link;
		}
		
		if(in_array($template,array('booking_driver_accept')))
		{
			$templateFile='email_booking_driver_accept.php';
		}

		if(in_array($template,array('booking_driver_reject')))
		{
			$templateFile='email_booking_driver_reject.php';
		}
		
		if(in_array($template,array('booking_cancel_customer')))
		{
			$templateFile='email_booking_cancel_customer.php';
		}
		
		do_action('chbs_send_email_notification',$bookingId,$template);

		$data['style']=$Email->getEmailStyle();
		
		$data['template']=$template;
		
		$data['booking']=$booking;
		$data['booking']['billing']=$this->createBilling($bookingId);

		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.$templateFile);
		$body=$Template->output();
		
		$subject=apply_filters('chbs_email_template_subject',$subject,$data['booking'],$template);
		
		$Email->send($recipient,$subject,$body);
	}
	
	/**************************************************************************/
	
	function sendEmailBookingChangeStatus($bookingOld,$bookingNew)
	{
		if($bookingOld['meta']['booking_status_id']==$bookingNew['meta']['booking_status_id']) return;
		
		$BookingStatus=new CHBSBookingStatus();
        $bookingStatus=$BookingStatus->getBookingStatus($bookingNew['meta']['booking_status_id']);
            
        $recipient=array();
        $recipient[0]=array($bookingNew['meta']['client_contact_detail_email_address']);
       
        $subject=sprintf(__('Booking "%s" has changed status to "%s"','chauffeur-booking-system'),$bookingNew['post']->post_title,$bookingStatus[0]);
        
		global $chbs_logEvent;
        
		$chbs_logEvent=3;
        $this->sendEmail($bookingNew['post']->ID,CHBSOption::getOption('sender_default_email_account_id'),'booking_change_status',$recipient[0],$subject);           
	}

	/**************************************************************************/

	function getCouponCodeUsageCount($couponCode)
	{
		$argument=array
		(
			'post_type'=>self::getCPTName(),
			'post_status'=>'publish',
			'posts_per_page'=>-1,
			'meta_key'=>PLUGIN_CHBS_CONTEXT.'_coupon_code',
			'meta_value'=>$couponCode,
			'meta_compare'=>'='
		);

		$query=new WP_Query($argument);
		if($query===false)
			return(false);

		return($query->found_posts);
	}

	/**************************************************************************/

	function getUserBooking($userId,$argument)
	{
		$argument=array
		(
			'post_type'=>CHBSBooking::getCPTName(),
			'post_status'=>'publish',
			'posts_per_page'=>-1,
			'meta_query'=>array
			(
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_pickup_date',
					'value'=>$argument['date_from'],
					'compare'=>'>='
				),
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_pickup_date',
					'value'=>$argument['date_to'],
					'compare'=>'<=',
				),
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_user_id',
					'value'=>$userId
				),
				array
				(
					'key'=>PLUGIN_CHBS_CONTEXT.'_business_user_paid',
					'value'=>1
				)
			)
		);

		$query=new WP_Query($argument);

		return($query);
	}

	/**************************************************************************/

	function getSumBooking($query)
	{
		$sum=0.00;

		if($query===false)
			return($sum);

		global $post;

		CHBSHelper::preservePost($post,$bPost);

		while($query->have_posts())
		{
			$query->the_post();

			$billing=$this->createBilling($post->ID);

			$sum+=$billing['summary']['value_gross'];
		}

		CHBSHelper::preservePost($post,$bPost,0);

		return($sum);
	}
	
	/**************************************************************************/
	
	function getPickupTimeRange($bookingForm)
	{
		$label=null;
		
		if((int)$bookingForm['meta']['timepicker_hour_range_enable']!==1) return($label);
		
		$data=CHBSHelper::getPostOption();
		$label=$data['pickup_time_service_type_'.$data['service_type_id']];
		
		return($label);
	}
	
	/**************************************************************************/
	
	function getBookingDateStop($bookingMeta,$duration,&$dateStop,$type='pickup')
	{
		$dateStop=array();
				
		if(in_array($bookingMeta['service_type_id'],array(1,3)))
		{
			if(in_array($bookingMeta['transfer_type_id'],array(3)))
			{
				$duration=round($duration/2);
			}
		}
		
		$timestamp=strtotime($bookingMeta[$type.'_datetime']);
		
		$date=date_i18n('d-m-Y H:i',strtotime('+'.$duration.' minutes',$timestamp));
		
		$dateStop['date']=date_i18n('d-m-Y',strtotime($date));
		$dateStop['time']=date_i18n('H:i',strtotime($date));
		
		$dateStop['datetime']=date_i18n('Y-m-d H:i:00',strtotime($date));
	}

	/**************************************************************************/
	
	function changeStatus($bookingNew,$bookingOld,$sendEmail=false)
	{
		$BookingDriver=new CHBSBookingDriver();
		$BookingDriver->sendEmail($bookingNew,$bookingOld,$sendEmail);
		
		if((int)$bookingOld['meta']['booking_status_id']===(int)$bookingNew['meta']['booking_status_id']) return;
		
		$GoogleCalendar=new CHBSGoogleCalendar();
		$GoogleCalendar->sendBooking($bookingOld['post']->ID,false,'after_booking_status_change');
		
		$BookingWebHook=new CHBSBookingWebHook();
		$BookingWebHook->run($bookingOld['post']->ID,'after_booking_status_change');
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/
