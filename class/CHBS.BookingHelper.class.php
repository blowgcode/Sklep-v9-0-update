<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingHelper
{
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
				if($value['duration']>0)
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
			$html.=$this->addNotificationLine('Accept','<a href="'.urlencode($data['booking_driver_accept_link']).'" target="_blank">'.esc_html__('Click to accept this booking','chauffeur-booking-system').'</a>',array(true,false));			
	
			$html.=$this->addNotificationHeader(__('Reject booking','chauffeur-booking-system'));
			$html.=$this->addNotificationLine('Reject','<a href="'.urlencode($data['booking_driver_reject_link']).'" target="_blank">'.esc_html__('Click to reject this booking','chauffeur-booking-system').'</a>',array(true,false));			
		}
	
		/***/
		
		return($html);	
	}
	
	/**************************************************************************/
	
	function addNotificationLine($label,$value,$format=array(true,true))
	{
		$html='<b>'.($format[0] ? esc_html($label) : $label).':</b> '.($format[1] ? esc_html($value) : $value).$this->newLineChar;
		return($html);
	}
	
	/**************************************************************************/
	
	function addNotificationHeader($header,$addNotificationLineBefore=true)
	{
		$html.=$addNotificationLineBefore ? $this->newLineChar : '';
		$html.='<u>'.esc_html($header).'</u>'.$this->newLineChar;
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
        
        if((int)$serviceTypeId!==1) return($count);
        if((int)$bookingForm['meta']['waypoint_enable']!==1) return($count);
        
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
        
        if((int)$serviceTypeId!==1) return($count);
        if((int)$bookingForm['meta']['waypoint_enable']!==1) return($count);
        
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
				else $location['waypoint'][]=CHBSHelper::getAddress($value);
			}
			
			$i++;
		}
		
		return($location);
	}
	
	/**************************************************************************/
	
	static function checkMaximumBookingNumber($data,$bookingForm,$type1='pickup',$type2='pickup')
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
		
		$time=$data[$type1.'_time_service_type_'.$serviceTypeId];
		$date=$data[$type1.'_date_service_type_'.$serviceTypeId];
		
		/***/

		foreach($bookingForm['meta']['maximum_booking_number'] as $index=>$value)
		{
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
		
		$argument['meta_query']=$metaQuery;

		$query=new WP_Query($argument);
		
		remove_filter('posts_join',array('CHBSBookingHelper','checkMaximumBookingNumberFilterPostsJoin'),10,2);
		remove_filter('posts_where',array('CHBSBookingHelper','checkMaximumBookingNumberFilterPostsWhere'),10,2);
			
		if($query->found_posts>=$maximumBookingNumber)
		{
			$response['error']=true;
			$response['message']=__('There is no free slots to fulfill this booking. Please select a different date/time.','chauffeur-booking-system');
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
		$where.=' and ('.PLUGIN_CHBS_CONTEXT.'_table.meta_key=\''.PLUGIN_CHBS_CONTEXT.'_'.$type2.'_date\' and weekday(STR_TO_DATE('.PLUGIN_CHBS_CONTEXT.'_table.meta_value,\'%d-%m-%Y\'))='.((int)CHBSGlobalData::getGlobalData('maximum_booking_number_week_day_number')-1).')';
		return($where);			
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/