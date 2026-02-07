<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingHelper
{
	/**************************************************************************/
	
	public $newLineChar;
	public $notificationHtmlTagUse;
	
	/**************************************************************************/
	
	function __construct($notificationHtmlTagUse=true)
	{
		$this->notificationHtmlTagUse=$notificationHtmlTagUse;
	}
	
	/**************************************************************************/
	
	static function getPaymentName($paymentId,$wooCommerceEnable=-1,$meta=array())
	{
		$Payment=new CHBSPayment();
		$WooCommerce=new CHBSWooCommerce();
		
		if($wooCommerceEnable===-1)
			$wooCommerceEnable=$WooCommerce->isEnable($meta);
		
		if($wooCommerceEnable)
		{
			$paymentName=$WooCommerce->getPaymentName($paymentId);
		}
		else
		{
			$paymentName=$Payment->getPaymentName($paymentId);
		}
		
		return($paymentName);
	}
		
	/**************************************************************************/
	
	static function isPayment(&$paymentId,$meta,$step=-1)
	{
		$Payment=new CHBSPayment();
		$WooCommerce=new CHBSWooCommerce();
		
		if((int)$meta['price_hide']===1)
		{
			$paymentId=0;
			return(true);
		}
		
		if(($step===3) && ($WooCommerce->isEnable($meta)) && ((int)$meta['payment_woocommerce_step_3_enable']===0))
		{
			return(true);
		}
		
		if((int)$meta['payment_mandatory_enable']===0)
		{
			if($WooCommerce->isEnable($meta))
			{
				if((empty($paymentId)) || ((int)$paymentId===-1))
				{
					$paymentId=0;
					return(true);
				}
			}
			else
			{
				if($paymentId==-1)
				{
					$paymentId=0;
					return(true);
				}
			}
		}
		
		if($WooCommerce->isEnable($meta))
		{
			return($WooCommerce->isPayment($paymentId));
		}
		else
		{
			if(!$Payment->isPayment($paymentId)) return(false);
		}
		
		return(true);
	}
	
	/**************************************************************************/
	
	static function isPaymentDepositEnable($meta,$bookingId=-1)
	{
		if((int)$meta['price_hide']===1)
		{
			return(0);
		}
		
		if($bookingId==-1)
		{
			$WooCommerce=new CHBSWooCommerce();
			if($WooCommerce->isEnable($meta)) return(0);
		}
		
		return((int)$meta['payment_deposit_enable']);
	}

	/**************************************************************************/
	
	static function isPassengerEnable($meta,$serviceType=1,$passengerType='adult')
	{
		if((int)$passengerType===-1)
		{
			return($meta['passenger_adult_enable_service_type_'.$serviceType] && $meta['passenger_children_enable_service_type_'.$serviceType]);
		}
		
		return($meta['passenger_'.$passengerType.'_enable_service_type_'.$serviceType]);
	}
	
	/**************************************************************************/
	
	static function getPassenegerSum($meta,$data)
	{
		$sum=0;
		
		if(CHBSBookingHelper::isPassengerEnable($meta,$data['service_type_id'],'adult'))
			$sum+=(int)$data['passenger_adult_service_type_'.$data['service_type_id']];
			
		if(CHBSBookingHelper::isPassengerEnable($meta,$data['service_type_id'],'children'))
			$sum+=(int)$data['passenger_children_service_type_'.$data['service_type_id']];			
		
		return($sum);
	}
	
	/**************************************************************************/
	
	static function getPassengerLabel($numberAdult,$numberChildren,$type=1,$usePersonLabel=0)
	{
		$html=null;
		
		$Validation=new CHBSValidation();
		
		if($type===1)
		{
			if(($numberAdult>0) && ($numberChildren==0))
			{
				if((int)$usePersonLabel===1)
					$html=sprintf(__('%s persons','chauffeur-booking-system'),$numberAdult);
				else $html=sprintf(__('%s passengers','chauffeur-booking-system'),$numberAdult);
			
			}	
			else
			{
				if($numberAdult>0)
				{
					if((int)$usePersonLabel===1)
						$html=sprintf(__('%s persons','chauffeur-booking-system'),$numberAdult);
					else $html=sprintf(__('%s adults','chauffeur-booking-system'),$numberAdult);
				}
				
				if($numberChildren>0)
				{
					if($Validation->isNotEmpty($html)) $html.=', ';
					$html.=sprintf(__('%s children','chauffeur-booking-system'),$numberChildren);
				}
			}
		}
		
		return($html);
	}
	
	/**************************************************************************/
	
	static function getBaseLocationDistance($vehicleId,$return=false,$global=true)
	{
		$Validation=new CHBSValidation();
		
		$distance='';
		
		$option=CHBSHelper::getPostOption();
		
		$index=(!$return ? 'base_location_vehicle_distance' : 'base_location_vehicle_return_distance');
		
		if(array_key_exists($index,$option))
		{
			if(isset($option[$index][$vehicleId]))
				$distance=$option[$index][$vehicleId];
		}
	
		if($global)
		{
			if($Validation->isEmpty($distance))
			{
				$index=(!$return ? 'base_location_distance' : 'base_location_return_distance');
				return($option[$index]);
			}
		}
				
		return($distance);
	}
	
	/**************************************************************************/
	
	static function getPriceType($bookingForm,&$priceType,&$sumType,&$taxShow,$step)
	{
		$taxShow=true;
		$sumType='gross';
		$priceType='gross';
		
		/***/
		
		if((int)$bookingForm['meta']['show_net_price_hide_tax']===1)
		{
			if((int)$step!==4)
			{
				$taxShow=false;
				$sumType='net';
				$priceType='net';
			}
		}
		
		/***/
		
		if((int)$bookingForm['meta']['order_sum_split']===1)
		{
			$priceType='net';
		}
	}
	
	/**************************************************************************/
	
	static function getRoundValue($bookingForm,$price)
	{
		$roundValue=0.00;
		
		if($bookingForm['meta']['vehicle_price_round']>0.00)
		{
			$price=CHBSPrice::numberFormat($price);
			
			$roundPrice=ceil($price/$bookingForm['meta']['vehicle_price_round'])*$bookingForm['meta']['vehicle_price_round'];
			
			if($roundPrice>=$price) 
			{
				$roundValue=$roundPrice-$price;
				
				if($roundPrice-$bookingForm['meta']['vehicle_price_round']==$price)
				{
					$roundValue=0.00;
				}
			}
		}
		
		return($roundValue);
	}
	
	/**************************************************************************/
	
	static function isVehicleBidPriceEnable($bookingForm)
	{
		return(((int)$bookingForm['meta']['booking_summary_hide_fee']===1) && ($bookingForm['meta']['vehicle_price_round']==0.00) && ((int)$bookingForm['meta']['vehicle_bid_enable']===1));
	}
	
	/**************************************************************************/
	
	function createNotification($data,$newLineChar='<br/>')
	{
		$html=null;
		
		$this->newLineChar=$newLineChar;
		
		/***/
		
		$Date=new CHBSDate();
		$Length=new CHBSLength();
		$GoogleMap=new CHBSGoogleMap();
		$Validation=new CHBSValidation();
		$BookingFormElement=new CHBSBookingFormElement();
		
		/***/
		
		$html.=$this->addNotificationHeader(__('General','chauffeur-booking-system'),false);
		
		$html.=$this->addNotificationLine(__('Title','chauffeur-booking-system'),$data['booking']['post']->post_title);

		if(array_key_exists('booking_form_name',$data['booking']))
			$html.=$this->addNotificationLine(__('Booking form name','chauffeur-booking-system'),$data['booking']['booking_form_name']);
		
		$html.=$this->addNotificationLine(__('Status','chauffeur-booking-system'),$data['booking']['booking_status_name']);
		$html.=$this->addNotificationLine(__('Service type','chauffeur-booking-system'),$data['booking']['service_type_name']);
		$html.=$this->addNotificationLine(__('Transfer type','chauffeur-booking-system'),$data['booking']['transfer_type_name']);
		$html.=$this->addNotificationLine(__('Pickup date and time','chauffeur-booking-system'),$Date->formatDateToDisplay($data['booking']['meta']['pickup_date']).' '.$Date->formatTimeToDisplay($data['booking']['meta']['pickup_time']));
		
		if(in_array($data['booking']['meta']['service_type_id'],array(1,3)))
		{
			if((int)$data['booking']['meta']['transfer_type_id']===3)
				$html.=$this->addNotificationLine(__('Return date and time','chauffeur-booking-system'),$Date->formatDateToDisplay($data['booking']['meta']['return_date']).' '.$Date->formatTimeToDisplay($data['booking']['meta']['return_time']));
		}
	
		if((int)$data['booking']['meta']['price_hide']===0)
		{
			$html.=$this->addNotificationLine(__('Order total amount','chauffeur-booking-system'),html_entity_decode(CHBSPrice::format($data['booking']['billing']['summary']['value_gross'],$data['booking']['meta']['currency_id'])));
		
			$htmlTax=null;
			foreach($data['booking']['billing']['tax_group'] as $value)
			{
				if(!$Validation->isEmpty($htmlTax)) $htmlTax.=', ';
				$htmlTax.=html_entity_decode(CHBSPrice::format($value['value'],$data['booking']['meta']['currency_id'])).' ('.$value['tax_value'].'%)';
			}	
			$html.=$this->addNotificationLine(__('Taxes','chauffeur-booking-system'),$htmlTax,array(true,false));
			
			if($data['booking']['meta']['payment_deposit_enable']==1)
				$html.=$this->addNotificationLine(sprintf(esc_html__('To pay (deposit %s%%)','chauffeur-booking-system'),$data['booking']['meta']['payment_deposit_value']),html_entity_decode(CHBSPrice::format($data['booking']['billing']['summary']['pay'],$data['booking']['meta']['currency_id'])));
		}
		
		if(in_array($data['booking']['meta']['service_type_id'],array(1,3)))
			$html.=$this->addNotificationLine(__('Distance','chauffeur-booking-system'),$data['booking']['billing']['summary']['distance_s2'].$Length->getUnitShortName($data['booking']['meta']['length_unit']));
			
		if((int)$data['booking']['meta']['total_time_display_enable']===1)
			$html.=$this->addNotificationLine(__('Duration','chauffeur-booking-system'),$data['booking']['billing']['summary']['duration_s2']);	
			
		if($data['booking']['meta']['passenger_enable']==1)
			$html.=$this->addNotificationLine(__('Passengers','chauffeur-booking-system'),CHBSBookingHelper::getPassengerLabel($data['booking']['meta']['passenger_adult_number'],$data['booking']['meta']['passenger_children_number'],1,$data['booking']['meta']['passenger_use_person_label']));
			
		if($Validation->isNotEmpty($data['booking']['meta']['comment']))
			$html.=$this->addNotificationLine(__('Comments','chauffeur-booking-system'),$data['booking']['meta']['comment']);
			
		/***/
		
		if(((int)$data['booking']['meta']['service_type_id']===3) || (((int)$data['booking']['meta']['service_type_id']===3) && ((int)$data['booking']['meta']['extra_time_enable']===1)))
		{
			$html.=$this->addNotificationHeader(__('Route','chauffeur-booking-system'));
			
			$html.=$this->addNotificationLine(__('Route name','chauffeur-booking-system'),$data['booking']['meta']['route_name']);
			
			if(in_array($data['booking']['meta']['service_type_id'],array(1,3)))
			{
				if((int)$data['booking']['meta']['extra_time_enable']===1)
					$html.=$this->addNotificationLine(__('Extra time','chauffeur-booking-system'),$Date->formatMinuteToTime($data['booking']['meta']['extra_time_value']));
			}
		}
		
		/***/
		
		$i=0;
		$html.=$this->addNotificationHeader(__('Route locations','chauffeur-booking-system'));
		
		$url=$GoogleMap->getRouteURLAddress($data['booking']['meta']['coordinate']);
		
		foreach($data['booking']['meta']['coordinate'] as $value)
		{	
			$address=CHBSHelper::getAddress($value);
			if($Validation->isNotEmpty($address))
			{
				if((array_key_exists('duration',$value)) && ((int)$value['duration']>0))
					$address.=sprintf(esc_html__(' (%s minutes)','chauffeur-booking-system'),$value['duration']);
				
				$html.=$this->addNotificationLine((++$i),'<a href="'.esc_url($url).'" target="_blank">'.esc_html($address).'</a>',array(true,false));
			}
		}
		
		/***/
		
		$html.=$this->addNotificationHeader(__('Vehicle','chauffeur-booking-system'));
		
		$html.=$this->addNotificationLine(__('Vehicle name','chauffeur-booking-system'),$data['booking']['meta']['vehicle_name']);
		
		if(array_key_exists('vehicle_bag_count',$data['booking']))
			$html.=$this->addNotificationLine(__('Bag count','chauffeur-booking-system'),$data['booking']['vehicle_bag_count']);
		
		if(array_key_exists('vehicle_passenger_count',$data['booking']))
			$html.=$this->addNotificationLine(__('Passengers count','chauffeur-booking-system'),$data['booking']['vehicle_passenger_count']);
		
		/***/
		
		if(count($data['booking']['meta']['booking_extra']))
		{
			$i=0;
			$html.=$this->addNotificationHeader(__('Booking extras','chauffeur-booking-system'));
			
			foreach($data['booking']['meta']['booking_extra'] as $value)
			{
				$htmlPrice=null;
				if((int)$data['booking']['meta']['price_hide']===0)
					$htmlPrice=' - '.CHBSPrice::format(CHBSPrice::calculateGross($value['price'],0,$value['tax_rate_value'])*$value['quantity'],$data['booking']['meta']['currency_id']);
				
				$html.=$this->addNotificationLine((++$i),esc_html($value['quantity']).esc_html(' x ','chauffeur-booking-system').esc_html($value['name']).$htmlPrice,array(true,false));
			}
		}
		
		/***/
		
		$html.=$this->addNotificationHeader(__('Client details','chauffeur-booking-system'));
		$html.=$this->addNotificationLine(__('First name','chauffeur-booking-system'),$data['booking']['meta']['client_contact_detail_first_name']);
		$html.=$this->addNotificationLine(__('Last name','chauffeur-booking-system'),$data['booking']['meta']['client_contact_detail_last_name']);
		$html.=$this->addNotificationLine(__('E-mail address','chauffeur-booking-system'),$data['booking']['meta']['client_contact_detail_email_address']);
		$html.=$this->addNotificationLine(__('Phone number','chauffeur-booking-system'),$data['booking']['meta']['client_contact_detail_phone_number']);
		$html.=$BookingFormElement->displayField(1,$data['booking']['meta'],4,array(),$newLineChar,false);
		
		/***/
		
		if((int)$data['booking']['meta']['client_billing_detail_enable']===1)
		{
			$html.=$this->addNotificationHeader(__('Billing address','chauffeur-booking-system'));
			$html.=$this->addNotificationLine(__('Company name','chauffeur-booking-system'),$data['booking']['meta']['client_billing_detail_company_name']);
			$html.=$this->addNotificationLine(__('Tax number','chauffeur-booking-system'),$data['booking']['meta']['client_billing_detail_tax_number']);
			$html.=$this->addNotificationLine(__('Street name','chauffeur-booking-system'),$data['booking']['meta']['client_billing_detail_street_name']);
			$html.=$this->addNotificationLine(__('Street number','chauffeur-booking-system'),$data['booking']['meta']['client_billing_detail_street_number']);
			$html.=$this->addNotificationLine(__('City','chauffeur-booking-system'),$data['booking']['meta']['client_billing_detail_city']);
			$html.=$this->addNotificationLine(__('State','chauffeur-booking-system'),$data['booking']['meta']['client_billing_detail_state']);
			$html.=$this->addNotificationLine(__('Postal code','chauffeur-booking-system'),$data['booking']['meta']['client_billing_detail_postal_code']);
			$html.=$this->addNotificationLine(__('Country','chauffeur-booking-system'),$data['booking']['meta']['client_billing_detail_country_name']);
			$html.=$BookingFormElement->displayField(2,$data['booking']['meta'],4,array(),$newLineChar,false);
		}
		
		/***/

		$panel=$BookingFormElement->getPanel($data['booking']['meta']);
	
		foreach($panel as $panelValue)
		{
			if(in_array($panelValue['id'],array(1,2))) continue;
			
			$htmlField=$BookingFormElement->displayField($panelValue['id'],$data['booking']['meta'],4,array(),$newLineChar,false);

			if($Validation->isEmpty($htmlField)) continue;
			
			$html.=$this->addNotificationHeader($panelValue['label']);
			$html.=$htmlField;
		}
	
		/***/
	
		if((array_key_exists('form_element_agreement',$data['booking']['meta'])) && (is_array($data['booking']['meta']['form_element_agreement'])) && (count($data['booking']['meta']['form_element_agreement'])))
		{
			$i=0;
			
			$html.=$this->addNotificationHeader(__('Agreements','chauffeur-booking-system'));
			
			foreach($data['booking']['meta']['form_element_agreement'] as $value)
				$html.=$this->addNotificationLine((++$i),((int)$value['value']===1 ? __('[YES]','chauffeur-booking-system') : __('[NO]','chauffeur-booking-system')).' '.$value['text'],array(true,false));
		}
	
		/***/

		if(!empty($data['booking']['meta']['payment_id']))
		{
			$html.=$this->addNotificationHeader(__('Payment','chauffeur-booking-system'));
			$html.=$this->addNotificationLine(__('Name','chauffeur-booking-system'),$data['booking']['payment_name']);
		}
		
		if((array_key_exists('booking_driver_accept_link',$data)) && (array_key_exists('booking_driver_reject_link',$data)))
		{
			$html.=$this->addNotificationHeader(__('Accept booking','chauffeur-booking-system'));
			$html.=$this->addNotificationLine('Accept','<a href="'.esc_url($data['booking_driver_accept_link']).'" target="_blank">'.esc_html__('Click to accept this booking','chauffeur-booking-system').'</a>',array(true,false));			
	
			$html.=$this->addNotificationHeader(__('Reject booking','chauffeur-booking-system'));
			$html.=$this->addNotificationLine('Reject','<a href="'.esc_url($data['booking_driver_reject_link']).'" target="_blank">'.esc_html__('Click to reject this booking','chauffeur-booking-system').'</a>',array(true,false));			
	
			/***/
			
			$DateTime=DateTime::createFromFormat('d-m-Y H:i',$data['booking']['meta']['pickup_date'].' '.$data['booking']['meta']['pickup_time']);

			$dateQueryString=$DateTime->format('Ymd').'T'.$DateTime->format('His');
			
			$DateTime->modify('+'.(int)$data['booking']['billing']['summary']['duration_s2_minute'].' minutes');
			
			$dateQueryString.='Z/'.$DateTime->format('Ymd').'T'.$DateTime->format('His').'Z';
			
			$htmlGoogleCalendarLink='https://calendar.google.com/calendar/render?action=TEMPLATE&text='.urlencode($data['booking']['post']->post_title).'&details='.urlencode($html).'&dates='.$dateQueryString;

			$html.=$this->addNotificationHeader(__('Add to Google Calendar','chauffeur-booking-system'));
			$html.=$this->addNotificationLine('Add','<a href="'.$htmlGoogleCalendarLink.'" target="_blank">'.esc_html__('Add to Google Calendar','chauffeur-booking-system').'</a>',array(true,false));			
		}
	
		/***/
		
		return($html);	
	}
	
	/**************************************************************************/
	
	function addNotificationLine($label,$value,$format=array(true,true))
	{
		$html=($format[0] ? esc_html($label) : $label).': ';
		
		if($this->notificationHtmlTagUse) $html='<b>'.$html.'</b>';
		
		$html.=($format[1] ? esc_html($value) : $value).$this->newLineChar;
		
		return($html);
	}
	
	/**************************************************************************/
	
	function addNotificationHeader($header,$addNotificationLineBefore=true)
	{
		$header=$this->notificationHtmlTagUse ? '<u>'.esc_html($header).'</u>' : esc_html($header);
		
		$html=$addNotificationLineBefore ? $this->newLineChar : '';
		$html.=$header.$this->newLineChar;
		
		return($html);
	}
	
	/**************************************************************************/
	
	function setTaxRateDistance($bookingForm)
	{
		global $chbsGlobalData; 
		
		$Date=new CHBSDate();
		$ServiceType=new CHBSServiceType();
		
		$data=CHBSHelper::getPostOption();
	
		if((!array_key_exists('service_type_id',$data)) || (!$ServiceType->isServiceType($data['service_type_id']))) return;
			
		if((array_key_exists('tax_rate_distance',$chbsGlobalData))) return;
		
		$data['pickup_date_service_type_'.$data['service_type_id']]=$Date->formatDateToStandard($data['pickup_date_service_type_'.$data['service_type_id']]);
		$data['pickup_time_service_type_'.$data['service_type_id']]=$Date->formatTimeToStandard($data['pickup_time_service_type_'.$data['service_type_id']]);	
		
		if((in_array($data['service_type_id'],array(1,3))) && ((int)$bookingForm['meta']['calculation_method_service_type_1']===1))
		{
			if((int)$bookingForm['meta']['tax_rate_geofence_enable']===1)
			{
				$taxRate=array();
				$geofence=array();

				$TaxRate=new CHBSTaxRate();
				
				$dictionaryTaxRate=$TaxRate->getDictionary();

				foreach($bookingForm['dictionary']['geofence'] as $index=>$value)
				{
					if($TaxRate->isTaxRate($value['meta']['tax_rate_id']))
						$geofence[$index]=array('tax_rate_id'=>$value['meta']['tax_rate_id'],'geofence_name'=>$value['post']->post_title);
				}

				if(count($geofence))
				{
					$GeofenceChecker=new CHBSGeofenceChecker();

					$routeData=json_decode($data['route_data']);
                    
                    if(is_array($routeData))
                    {
                        foreach($routeData as $routeDataIndex=>$routeDataValue)
                        {
                            foreach($geofence as $geofenceIndex=>$geofenceValue)
                            {
                                $taxRateId=$geofenceValue['tax_rate_id'];

                                if(!is_array($taxRate[$taxRateId]))
                                {
                                    $taxRate[$taxRateId]['distance']=0;
                                }

                                $result1=(int)$GeofenceChecker->locationInGeofence(array($geofenceIndex),$bookingForm['dictionary']['geofence'],json_encode($routeDataValue[0]));
                                $result2=(int)$GeofenceChecker->locationInGeofence(array($geofenceIndex),$bookingForm['dictionary']['geofence'],json_encode($routeDataValue[1]));

                                if($result1+$result2===1)
                                {
                                    $point=array();

                                    $coordinate=$routeDataValue[3];

                                    foreach($coordinate as $coordinateValue)
                                    {
                                        $result3=(int)$GeofenceChecker->locationInGeofence(array($geofenceIndex),$bookingForm['dictionary']['geofence'],json_encode($coordinateValue));
                                        if($result3===1) $point[]=$coordinateValue;
                                    }

                                    if(count($point)>2)
                                    {
                                        $length=count($point);

                                        $c[0][0]=$point[0]->lat;
                                        $c[0][1]=$point[0]->lng;

                                        $c[1][0]=$point[$length-1]->lat;
                                        $c[1][1]=$point[$length-1]->lng;											

                                        $GoogleMap=new CHBSGoogleMap();
                                        $d=$GoogleMap->getDistance($c);

                                        $taxRate[$taxRateId]['distance']+=(int)$d;
                                        $taxRate[$taxRateId]['geofence_name']=$geofenceValue['geofence_name'];
                                        $taxRate[$taxRateId]['tax_rate_value']=$dictionaryTaxRate[$taxRateId]['meta']['tax_rate_value'];
                                    }
                                }
                                elseif($result1+$result2===2)
                                {
                                    $taxRate[$taxRateId]['distance']+=$routeDataValue[2];
                                    $taxRate[$taxRateId]['geofence_name']=$geofenceValue['geofence_name'];
                                    $taxRate[$taxRateId]['tax_rate_value']=$dictionaryTaxRate[$taxRateId]['meta']['tax_rate_value'];
                                }
                            }
                        }
                    }

					CHBSGlobalData::setGlobalData('tax_rate_distance',$taxRate);
				}
			}
		}
	}
	
	/**************************************************************************/
	
	function calculateTaxRateDistance($calculationMethod,$serviceTypeId,$priceNet,$taxRateId,$distance,$passengerCount=1)
	{
		$TaxRate=new CHBSTaxRate();
		
		$taxRateDicionary=$TaxRate->getDictionary();
		
		$sum=0;
		$distanceToCalculate=$distance;
		
		if(((int)$calculationMethod===1) && (in_array($serviceTypeId,array(1,3))))
		{
			global $chbsGlobalData;

			if((isset($chbsGlobalData['tax_rate_distance'])) && (is_array($chbsGlobalData['tax_rate_distance'])))
			{			
				foreach($chbsGlobalData['tax_rate_distance'] as $index=>$value)
				{
					if(($TaxRate->isTaxRate($index)) && ($value['distance']>0))
					{
						$value['distance']=round($value['distance']/1000,1);

						$distanceToCalculate-=$value['distance'];

						$sum+=CHBSPrice::numberFormat(CHBSPrice::numberFormat($priceNet*$value['distance']*$passengerCount)*(1+$TaxRate->getTaxRateValue($index,$taxRateDicionary)/100));
					}
				}
			}	
		}
		
		$sum+=CHBSPrice::numberFormat(CHBSPrice::numberFormat($priceNet*$distanceToCalculate*$passengerCount)*(1+$TaxRate->getTaxRateValue($taxRateId,$taxRateDicionary)/100));
		
		return($sum);
	}
    
    /**************************************************************************/
    
    static function getWaypointCount($data,$bookingForm,$serviceTypeId,$transferTypeId)
    {
        $count=0;
        
        $Validation=new CHBSValidation();
        
        if((int)$serviceTypeId!==1) return($duration);
        if((int)$bookingForm['meta']['waypoint_enable']!==1) return($duration);
        
        if(is_array($data['waypoint_location_coordinate_service_type_1']))
        {
            foreach($data['waypoint_location_coordinate_service_type_1'] as $value)
            {
                if($Validation->isNotEmpty($value)) $count++;			   
            }           
        }
        
        $count*=in_array($transferTypeId,array(2,3)) ? 2 : 1;

        return($count);
    }
	
    /**************************************************************************/
    
    static function getWaypointDuration($data,$bookingForm,$serviceTypeId,$transferTypeId)
    {
        $duration=0;
        
        $Validation=new CHBSValidation();
        
        if((int)$serviceTypeId!==1) return($duration);
        if((int)$bookingForm['meta']['waypoint_enable']!==1) return($duration);
        
        if(is_array($data['waypoint_location_coordinate_service_type_1']))
        {
            foreach($data['waypoint_location_coordinate_service_type_1'] as $index=>$value)
            {
                if($Validation->isNotEmpty($value))
				{
					$duration+=(int)$data['waypoint_duration_service_type_1'][$index];
				}
            }           
        }
		
        $duration*=in_array($transferTypeId,array(2,3)) ? 2 : 1;

        return($duration);
    }
	
	/**************************************************************************/
	
	static function getBookingLocation($booking)
	{
		$location=array('pickup'=>null,'waypoint'=>array(),'dropoff'=>null);
		
		$i=0;
		$coordinateCount=count($booking['meta']['coordinate']);
		
		foreach($booking['meta']['coordinate'] as $value)
		{
			if($i===0) $location['pickup']=CHBSHelper::getAddress($value);
			elseif($i>0)
			{
				if($i===$coordinateCount-1) $location['dropoff']=CHBSHelper::getAddress($value);
				else 
				{
					$address=CHBSHelper::getAddress($value);
					
					if($value['duration']>0)
						$address.=sprintf(esc_html__(' (%s minutes)','chauffeur-booking-system'),$value['duration']);				
					
					$location['waypoint'][]=$address;
				}
			}
			
			$i++;
		}
		
		return($location);
	}
	
	/**************************************************************************/
	
	static function checkMaximumBookingNumberAll($data,$bookingForm)
	{
		$tVehicleUnset=array();
		
		CHBSBookingHelper::checkMaximumBookingNumber($data,$bookingForm,'pickup','pickup',$tVehicleUnset,true);
		
		CHBSBookingHelper::checkMaximumBookingNumber($data,$bookingForm,'pickup','return',$tVehicleUnset,true);
		
		CHBSBookingHelper::checkMaximumBookingNumber($data,$bookingForm,'return','return',$tVehicleUnset,true);
		
		CHBSBookingHelper::checkMaximumBookingNumber($data,$bookingForm,'return','pickup',$tVehicleUnset,true);
		
		return($tVehicleUnset);
	}
	
	/**************************************************************************/
	
	static function checkMaximumBookingNumber($data,$bookingForm,$type1='pickup',$type2='pickup',&$tVehicleUnset=array(),$checkVehicle=false)
	{
		$response=array('error'=>false);
		
		$serviceTypeId=(int)$data['service_type_id'];
		
		if((!is_array($bookingForm['meta']['maximum_booking_number'])) || (!count($bookingForm['meta']['maximum_booking_number']))) return($response);
		
		/***/
		
		if(($type1=='return') && ((int)$data['transfer_type_service_type_'.$serviceTypeId]!==3)) return($response);
		
		/***/
		
		$found=false;
		
		$Date=new CHBSDate();
		
		$metaQuery=array();
		
		$time=$Date->formatTimeToStandard($data[$type1.'_time_service_type_'.$serviceTypeId]);
		$date=$Date->formatDateToStandard($data[$type1.'_date_service_type_'.$serviceTypeId]);
		
		/***/
		
		$maximumBookingNumberValue=null;

		foreach($bookingForm['meta']['maximum_booking_number'] as $index=>$value)
		{
			$maximumBookingNumberValue=$value;
			
			switch($value['time_unit'])
			{
				case 1:
					
					if($Date->timeInRange($time,$value['time_start'],$value['time_stop']))
					{
						$found=true;
						
						array_push($metaQuery,array
						(
							'key'=>PLUGIN_CHBS_CONTEXT.'_'.$type2.'_time',
							'value'=>$value['time_start'],
							'compare'=>'>='
						));		
						
						array_push($metaQuery,array
						(
							'key'=>PLUGIN_CHBS_CONTEXT.'_'.$type2.'_time',
							'value'=>$value['time_stop'],
							'compare'=>'<='
						));	
						
						$maximumBookingNumber=$value['number'];
						break 2;
					}
					
				break;
			
				case 2:
					
					if($Date->dateInRange($date,$value['date_start'],$value['date_stop']))
					{
						$found=true;
						
						array_push($metaQuery,array
						(
							'key'=>PLUGIN_CHBS_CONTEXT.'_'.$type2.'_datetime',
							'value'=>$Date->reverseDate($value['date_start']),
							'compare'=>'>=',
							'type'=>'DATE'
						));		
						
						array_push($metaQuery,array
						(
							'key'=>PLUGIN_CHBS_CONTEXT.'_'.$type2.'_datetime',
							'value'=>$Date->reverseDate($value['date_stop']),
							'compare'=>'<=',
							'type'=>'DATE'
						));	

						$maximumBookingNumber=$value['number'];
						break 2;						
					}
					
				break;
			
				case 3:
					
					if($Date->dateInRange($date.' '.$time,$value['date_start'].' '.$value['time_start'],$value['date_stop'].' '.$value['time_stop']))
					{
						$found=true;
						
						array_push($metaQuery,array
						(
							'key'=>PLUGIN_CHBS_CONTEXT.'_'.$type2.'_datetime',
							'value'=>$Date->reverseDate($value['date_start']).' '.$value['time_start'].':00',
							'compare'=>'>=',
							'type'=>'DATETIME'
						));		
						
						array_push($metaQuery,array
						(
							'key'=>PLUGIN_CHBS_CONTEXT.'_'.$type2.'_datetime',
							'value'=>$Date->reverseDate($value['date_stop']).' '.$value['time_stop'].':00',
							'compare'=>'<=',
							'type'=>'DATETIME'
						));	

						$maximumBookingNumber=$value['number'];
						break 2;						
					}
					
				break;
			
				case 4:
					
					$dayOfWeek=$Date->getDayNumberOfWeek($date);
					
					if((int)$dayOfWeek===(int)$value['week_day_number'])
					{
						$found=true;
						
						CHBSGlobalData::setGlobalData('maximum_booking_number_week_day_number',$dayOfWeek);
						CHBSGlobalData::setGlobalData('maximum_booking_number_type_2',$type2);
						
						add_filter('posts_join',array('CHBSBookingHelper','checkMaximumBookingNumberFilterPostsJoin'),10,2);
						add_filter('posts_where',array('CHBSBookingHelper','checkMaximumBookingNumberFilterPostsWhere'),10,2);
						
						$maximumBookingNumber=$value['number'];
						break 2;
					}
					
				break;
				
				case 5:
				
					if($Date->timeInRange($time,$value['time_start'],$value['time_stop']))
					{
						$found=true;
						
						array_push($metaQuery,array
						(
							'key'=>PLUGIN_CHBS_CONTEXT.'_'.$type2.'_datetime',
							'value'=>$Date->reverseDate($date).' '.$value['time_start'].':00',
							'compare'=>'>=',
							'type'=>'DATETIME'
						));		
						
						array_push($metaQuery,array
						(
							'key'=>PLUGIN_CHBS_CONTEXT.'_'.$type2.'_datetime',
							'value'=>$Date->reverseDate($date).' '.$value['time_stop'].':00',
							'compare'=>'<=',
							'type'=>'DATETIME'
						));	
			
						$maximumBookingNumber=$value['number'];
						break 2;
					}
					
				break;
			}
		}
		
		if(!$found) return($response);
			
		/***/
		
		$argument=array
		(
			'post_type'=>CHBSBooking::getCPTName(),
			'post_status'=>'publish',
			'posts_per_page'=>-1,
		);
		
		array_push($metaQuery,array
		(
			'key'=>PLUGIN_CHBS_CONTEXT.'_woocommerce_product_id',
			'value'=>array(0),
			'compare'=>'IN'
		));	
		
		global $wpdb;
		
		$argument['meta_query']=$metaQuery;

		$query=new WP_Query($argument);
		
		remove_filter('posts_join',array('CHBSBookingHelper','checkMaximumBookingNumberFilterPostsJoin'),10,2);
		remove_filter('posts_where',array('CHBSBookingHelper','checkMaximumBookingNumberFilterPostsWhere'),10,2);
			
		if($maximumBookingNumber>-1)
		{
			if($query->found_posts>=$maximumBookingNumber)
			{
				$response['error']=true;
				$response['message']=__('There is no free slots to fulfill this booking. Please select a different date/time.','chauffeur-booking-system');
			}
		}
		else if($checkVehicle)
		{
			if(is_array($maximumBookingNumberValue['vehicle']) && (count($maximumBookingNumberValue['vehicle'])))
			{
				global $post;

				$tVehicle=array();
			
				while($query->have_posts())
				{
					$query->the_post();

					$meta=CHBSPostMeta::getPostMeta($post);

					if(!array_key_exists($meta['vehicle_id'],$tVehicle))
						$tVehicle[$meta['vehicle_id']]=0;

					$tVehicle[$meta['vehicle_id']]++;
				}

				foreach($maximumBookingNumberValue['vehicle'] as $vehicleIndex=>$vehicleValue)
				{
					if($vehicleValue<=-1) continue;
					
					if($vehicleValue==0) $tVehicleUnset[]=$vehicleIndex;
					
					if(!array_key_exists($vehicleIndex,$tVehicle)) continue;
					
					if($tVehicle[$vehicleIndex]>$vehicleValue) $tVehicleUnset[]=$vehicleIndex;
				}
			}
		}
		
		return($response);
	}
		
	/**************************************************************************/
	
	static function checkMaximumBookingNumberFilterPostsJoin($join)
	{
		global $wpdb;
		$join.=' INNER JOIN '.$wpdb->prefix.'postmeta as '.PLUGIN_CHBS_CONTEXT.'_table ON ('.$wpdb->prefix.'posts.ID=chbs_table.post_id)';
		return($join);			
	}
	
	/**************************************************************************/

	static function checkMaximumBookingNumberFilterPostsWhere($where)
	{
		global $wpdb;
		$where.=' and ('.PLUGIN_CHBS_CONTEXT.'_table.meta_key=\''.PLUGIN_CHBS_CONTEXT.'_'.CHBSGlobalData::getGlobalData('maximum_booking_number_type_2').'_date\' and weekday(STR_TO_DATE('.PLUGIN_CHBS_CONTEXT.'_table.meta_value,\'%d-%m-%Y\'))='.((int)CHBSGlobalData::getGlobalData('maximum_booking_number_week_day_number')-1).')';
		return($where);			
	}
	
	/**************************************************************************/
	
	static function getLocationFromBookingForm($data,$locationType='pickup')
	{
		$Location=new CHBSLocation();
		
		$locationData=array('lat'=>'','lng'=>'');
		
		if(array_key_exists('fixed_location_'.$locationType.'_service_type_'.$data['service_type_id'],$data))
		{
			$locationDictionary=$Location->getDictionary();
			
			$locationId=(int)$data['fixed_location_'.$locationType.'_service_type_'.$data['service_type_id']];
		
			if(array_key_exists($locationId,$locationDictionary))
			{
				$locationData['lat']=$locationDictionary[$locationId]['meta']['location_name_coordinate_lat'];
				$locationData['lng']=$locationDictionary[$locationId]['meta']['location_name_coordinate_lng'];
			}
			
			$locationData=json_encode($locationData);
		}
		else $locationData=$data[$locationType.'_location_coordinate_service_type_'.$data['service_type_id']];				
				
		return($locationData);
	}
	
	/**************************************************************************/
	
	static function fillBookingFormSampleData($data)
	{
		$Validation=new CHBSValidation();
		$ServiceType=new CHBSServiceType();
		$TransferType=new CHBSTransferType();
		
		/***/
		
		if(!$ServiceType->isServiceType($data['service_type_id']))
			$data['service_type_id']=1;
		
		if(!$TransferType->isTransferType($data['transfer_type_service_type_'.$data['service_type_id']]))
			$data['transfer_type_service_type_'.$data['service_type_id']]=1;		
		
		$data['service_type_id']=(int)$data['service_type_id'];
		
		/***/
		
		$dateTimePickup=date_i18n('d-m-Y H:i',strtotime('+10 minutes'));
			
		if($Validation->isEmpty($data['pickup_date_service_type_'.$data['service_type_id']]))
			$data['pickup_date_service_type_'.$data['service_type_id']]=date_i18n('d-m-Y',strtotime($dateTimePickup));
		
		if($Validation->isEmpty($data['pickup_time_service_type_'.$data['service_type_id']]))
			$data['pickup_time_service_type_'.$data['service_type_id']]=date_i18n('H:i',strtotime($dateTimePickup));		
		
		if($data['service_type_id']===1)
		{
			if($data['transfer_type_service_type_1']===3)
			{
				$dateTimeReturn=date_i18n('d-m-Y H:i',strtotime($dateTimePickup.'+10 minutes'));
				
				if($Validation->isEmpty($data['return_date_service_type_1']))
					$data['return_date_service_type_1']=date_i18n('d-m-Y',strtotime($dateTimeReturn));

				if($Validation->isEmpty($data['return_time_service_type_1']))
					$data['return_time_service_type_1']=date_i18n('H:i',strtotime($dateTimeReturn));				
			}
		}
		
		/***/
		
		if(array_key_exists('passenger_adult_service_type_'.$data['service_type_id'],$data))
		{
			if((int)$data['passenger_adult_service_type_'.$data['service_type_id']]<=0)
				$data['passenger_adult_service_type_'.$data['service_type_id']]=1;
		}
		
		if(array_key_exists('passenger_children_service_type_'.$data['service_type_id'],$data))
		{
			if($Validation->isEmpty($data['passenger_children_service_type_'.$data['service_type_id']]))
			{
				if($Validation->isNumber($data['passenger_adult_service_type_'.$data['service_type_id']],1,99))
					$data['passenger_children_service_type_'.$data['service_type_id']]=0;
				else $data['passenger_children_service_type_'.$data['service_type_id']]=1;
			}
		}	
		
		return($data);
	}
	
	/**************************************************************************/
	
	static function isPriceBeforeDiscountEnable($bookingForm,$priceBeforeDiscount=0,$priceAfterDiscount=0)
	{
		if((int)$bookingForm['meta']['show_price_before_discount']===0) return(false);
		
		if((int)$bookingForm['meta']['show_price_per_single_passenger']===1) return(false);
		
		if((int)$bookingForm['meta']['vehicle_bid_enable']===1) return(false);
		
		if(($priceBeforeDiscount!==0) && ($priceAfterDiscount!==0))
		{
			if($priceBeforeDiscount<=$priceAfterDiscount) return(false);
		}
		
		return(true);
	}
	
	/**************************************************************************/
	
	static function calculateCouponDiscount($price,$priceWithoutCoupon=null)
	{
		if(!is_array($priceWithoutCoupon)) return(false);
		
		$discount=$priceWithoutCoupon['total']['sum']['gross']['value']-$price['total']['sum']['gross']['value'];
		
		if($discount>0)
		{
			return(CHBSPrice::format($discount,CHBSCurrency::getFormCurrency()));
		}
		
		return(false);
	}
	
	/**************************************************************************/
	
	static function createBookingText($booking,$message)
	{
		$Date=new CHBSDate();

		$pattern=array();
		$replacement=array();

		$location=self::getBookingLocation($booking);
		
		$pattern=self::getBookingTextShortcodes(true);
		
		$replacement[0]=$location['pickup'];
		$replacement[1]=$location['dropoff'];
		$replacement[2]=$Date->formatDateToDisplay($booking['meta']['pickup_date']);
		$replacement[3]=$Date->formatTimeToDisplay($booking['meta']['pickup_time']);
		$replacement[4]=$booking['meta']['vehicle_name'];
		$replacement[5]=CHBSPrice::format($booking['billing']['summary']['value_gross'],$booking['meta']['currency_id'],false);
		$replacement[6]=$booking['meta']['client_contact_detail_first_name'];
		$replacement[7]=$booking['meta']['client_contact_detail_last_name'];
		$replacement[8]=$booking['meta']['client_contact_detail_email_address'];
		$replacement[9]=$booking['meta']['client_contact_detail_phone_number'];
		$replacement[10]=$booking['meta']['comment'];
		
		$message=preg_replace($pattern,$replacement,$message);		
		
		return($message);
	}
	
	/**************************************************************************/
	
	static function getBookingTextShortcodes($usePattern=false,&$label=null)
	{
		$shortcode=array();
		
		$Validation=new CHBSValidation();
		
		$shortcode[0]='pickup_location';
		$shortcode[1]='dropoff_location';
		$shortcode[2]='pickup_date';
		$shortcode[3]='pickup_time';
		$shortcode[4]='vehicle_name';
		$shortcode[5]='booking_sum_gross';
		$shortcode[6]='client_first_name';
		$shortcode[7]='client_last_name';
		$shortcode[8]='client_email_address';	
		$shortcode[9]='client_phone_number';	
		$shortcode[10]='comment';	
		
		foreach($shortcode as $shortcodeIndex=>$shortcodeValue)
			$shortcode[$shortcodeIndex]=PLUGIN_CHBS_CONTEXT.'_'.$shortcodeValue;
		
		$label=null;
		foreach($shortcode as $shortcodeValue)
		{
			if($Validation->isNotEmpty($label)) $label.=', ';
			$label.='['.$shortcodeValue.']';
		}
		
		if($usePattern)
		{
			foreach($shortcode as $shortcodeIndex=>$shortcodeValue)
				$shortcode[$shortcodeIndex]='/\['.$shortcodeValue.'\]/';
		}
		
		return($shortcode);
	}
	
	/**************************************************************************/
	
	static function getDistanceIncluded($data,$bookingForm,$booking=null,$distanceFormat=true)
	{
		$distance=-1;
		
		$Length=new CHBSLength();
		
		if(!is_null($booking))
		{
			if((int)$booking['meta']['service_type_id']===2)
			{
				$distance=(int)$booking['meta']['distance_included'];
			}
		}
		else
		{
			if((int)$data['service_type_id']===2)
			{
				if((int)$bookingForm['meta']['duration_distance_included_enable']===1)
					$distance=$bookingForm['duration_distance'][(int)$data['duration_service_type_2']]['distance'];
			}
		}
		
		if(($distanceFormat) && ($distance!==-1)) $distance=$Length->format($distance);
		
		return($distance);
	}
	
	/**************************************************************************/
	
	static function getCoordinate($bookingForm,$data,&$pickupLocationId=-1,&$dropoffLocationId=-1)
	{
		$coordinate=array();
		
		$Validation=new CHBSValidation();

		if(in_array($data['service_type_id'],array(1,2)))
		{
			if(count($bookingForm['meta']['location_fixed_pickup_service_type_'.$data['service_type_id']]))
			{
				$pickupLocationId=$data['fixed_location_pickup_service_type_'.$data['service_type_id']];
				array_push($coordinate,$bookingForm['meta']['location_fixed_pickup_service_type_'.$data['service_type_id']][$pickupLocationId]);
			} 
			else
			{
				array_push($coordinate,json_decode($data['pickup_location_coordinate_service_type_'.$data['service_type_id']]));

				if(((int)$data['service_type_id']===1) && ((int)$bookingForm['meta']['waypoint_enable']===1))
				{
					if(is_array($data['waypoint_location_coordinate_service_type_1']))
					{
						foreach($data['waypoint_location_coordinate_service_type_1'] as $index=>$value)
						{
							$value=json_decode($value);
							
							if(is_null($value)) continue;
							
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
			} 
			else
			{
				array_push($coordinate,json_decode($data['dropoff_location_coordinate_service_type_'.$data['service_type_id']]));
			}
		} 
		else
		{
			$coordinate=$bookingForm['dictionary']['route'][$data['route_service_type_3']]['meta']['coordinate'];
			
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
		
		return($coordinate);
	}
	
	/**************************************************************************/
	
	static function paymentSuccess($bookingId)
	{
		$Booking=new CHBSBooking();
		$BookingForm=new CHBSBookingForm();
		
		/***/
		
		$emailAdminSend=false;
		$emailClientSend=false;
		
		$bookingFormDictionary=$BookingForm->getDictionary();
		
		/***/
		
		$bookingOld=$Booking->getBooking($bookingId);

		CHBSPostMeta::updatePostMeta($bookingId,'booking_status_id',CHBSOption::getOption('booking_status_payment_success'));

		$bookingNew=$Booking->getBooking($bookingId);
		
		/***/

		if(array_key_exists($bookingNew['meta']['booking_form_id'],$bookingFormDictionary))
		{
			$bookingForm=$bookingFormDictionary[$bookingNew['meta']['booking_form_id']];

			$subject=sprintf(__('New booking "%s" has been received','chauffeur-booking-system'),$bookingNew['post']->post_title);

			if(((int)$bookingForm['meta']['email_notification_booking_new_client_enable']===1) && ((int)$bookingForm['meta']['email_notification_booking_new_client_payment_success_enable']===1))
			{
				$chbs_logEvent=1;
				$emailClientSend=true;
				$Booking->sendEmail($bookingId,$bookingForm['meta']['booking_new_sender_email_account_id'],'booking_new_client',array($bookingNew['meta']['client_contact_detail_email_address']),$subject);
			}

			if((int)$bookingForm['meta']['email_notification_booking_new_admin_enable']===1)
			{
				$chbs_logEvent=2;
				$emailAdminSend=true;
				$Booking->sendEmail($bookingId,$bookingForm['meta']['booking_new_sender_email_account_id'],'booking_new_admin',preg_split('/;/',$bookingForm['meta']['booking_new_recipient_email_address']),$subject);
			}
		}

		if(!$emailClientSend)
		{
			$emailSend=false;

			$WooCommerce=new CHBSWooCommerce();
			$WooCommerce->changeStatus(-1,$bookingId,$emailSend);									

			if(!$emailSend)
				$Booking->sendEmailBookingChangeStatus($bookingOld,$bookingNew);
		}

		do_action('chbs_booking_status_change',$bookingNew,$bookingOld);		
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/
