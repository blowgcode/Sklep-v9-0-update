<?php

/******************************************************************************/
/******************************************************************************/

class CHBSGoogleMapAPI
{
	/**************************************************************************/

	function __construct()
	{
	
	}

	/**************************************************************************/

	public function computeRoutes($bookingForm,$data,$type=1)
	{
		return($this->request('computeRoutes',$bookingForm,$data,$type));
	}

	/**************************************************************************/

	public function request($name,$bookingForm,$data,$type=1)
	{		
		$Validation=new CHBSValidation();
		
		/***/
		
		$postData=array();
		
		$coordinate=CHBSBookingHelper::getCoordinate($bookingForm,$data,$pickupLocationId,$dropoffLocationId);
		$coordinateCount=count($coordinate);
		
		$transferTypeId=1;
		$serviceTypeId=(int)$data['service_type_id'];
		
		if($type===1)
		{
			$i=0;
			$j=0;

			foreach($coordinate as $value)
			{
				if($i===0) $postData['origin']=$this->transformCoordinate($value);
				else if($i==$coordinateCount-1) $postData['destination']=$this->transformCoordinate($value);
				else 
				{
					$postData['intermediates'][$j++]=$this->transformCoordinate($value);
				}
				$i++;
			}
		}
		else if(in_array($type,array(2,3)))
		{
			$baseLocation=array();
			
			if($data['vehicle_id']>0)
			{
				if(in_array($data['vehicle_id'],$bookingForm['dictionary']['vehicle']))
				{
					$vehicle=$bookingForm['dictionary']['vehicle'][$data['vehicle_id']];

					if(($Validation->isNotEmpty($vehicle['meta']['base_location_coordinate_lat'])) && ($Validation->isNotEmpty($vehicle['meta']['base_location_coordinate_lng'])))
					{
						$baseLocation=array
						(
							'lat'=>$vehicle['meta']['base_location_coordinate_lat'],
							'lng'=>$vehicle['meta']['base_location_coordinate_lng']
						);
					}
				}
			}
		
			if(!count($baseLocation))
			{
				if(($Validation->isNotEmpty($bookingForm['meta']['base_location_coordinate_lat'])) && ($Validation->isNotEmpty($bookingForm['meta']['base_location_coordinate_lng'])))
				{
					$baseLocation=array
					(
						'lat'=>$bookingForm['meta']['base_location_coordinate_lat'],
						'lng'=>$bookingForm['meta']['base_location_coordinate_lng']
					);
				}			
			}
			
			if(!count($baseLocation)) return(-2);
		
			if($type===2)
			{
				$postData['origin']=$this->transformCoordinate($baseLocation);	
				$postData['destination']=$this->transformCoordinate($coordinate[0]);	
			}
			else 
			{
				if(($serviceTypeId===2) && (!is_array($coordinate[1]))) return(-2);
	
				if(in_array($serviceTypeId,array(1,3)))
				{
					$transferTypeId=(int)$data['transfer_type_service_type_'.$serviceTypeId];
				}

				$postData['origin']=in_array($transferTypeId,array(1,3)) ? $this->transformCoordinate($coordinate[count($coordinate)-1]) : $this->transformCoordinate($coordinate[0]);
				$postData['destination']=$this->transformCoordinate($baseLocation);
			}
		}
		
		/***/
		
		$ch=curl_init();

		if($name==='computeRoutes')
		{
			curl_setopt($ch,CURLOPT_URL,'https://routes.googleapis.com/directions/v2:computeRoutes');
		}	
		
		curl_setopt($ch,CURLOPT_HTTPHEADER,
		[
			'Content-Type:application/json',
			'X-Goog-Api-Key:'.CHBSOption::getOption('google_map_api_key'),
			'X-Goog-FieldMask:routes.duration,routes.distanceMeters,routes.legs,routes.polyline.encodedPolyline'
		]);
		
		curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($postData));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

		/***/
	
		$response=json_decode(curl_exec($ch));
		
		if(!property_exists($response,'routes')) return(-1);
	
		return($response);
	}
	
	/**************************************************************************/
	
	function transformCoordinate($coordinate)
	{
		$data=array
		(
			'location'=>array
			(
				'latLng'=>array
				(
					'latitude'=>$coordinate['lat'],
					'longitude'=>$coordinate['lng'],
				)
			)
		);

		return($data);
	}
	
	/**************************************************************************/
	
	function validateBookingFormData($bookingForm,$data)
	{
		$result=array
		(
			'distance_map'=>0,
			'duration_map'=>0,
			'distance_sum'=>0,
			'duration_sum'=>0,
			'base_location_duration'=>0,
			'base_location_distance'=>0,
			'base_location_return_distance'=>0,
			'base_location_return_duration'=>0
		);
		
		$Validation=new CHBSValidation();
		
		/***/
		
		$coordinate=CHBSBookingHelper::getCoordinate($bookingForm,$data,$pickupLocationId,$dropoffLocationId);
				
		$coordinateCount=count($coordinate);
	
		/***/
		
		if(in_array($data['service_type_id'],array(1,3)))
		{
			$response=$this->computeRoutes($bookingForm,$data,1);
			
			if($response===-1) return(-1);
		
			$result['distance_map']=round((int)$response->routes[0]->distanceMeters/1000,1);
			$result['duration_map']=ceil((int)$response->routes[0]->duration/60);
		}
		
		if($bookingForm['meta']['ride_time_rounding']>0.00)
		{
			$result['duration_map']=ceil($result['duration_map']/$bookingForm['meta']['ride_time_rounding'])*$bookingForm['meta']['ride_time_rounding'];
		}
		
		$result['duration_map']*=$bookingForm['meta']['ride_time_multiplier'];
		
		$distance=$result['distance_map'];
		
		$duration=0;
		$durationWaypoint=0;
	
		switch($data['service_type_id'])
		{
			case 1:
				
				if($Validation->isNumber($data['extra_time_service_type_1'],0,999999999))
					$duration+=(int)$data['extra_time_service_type_1'];
				
				$duration*=(int)$bookingForm['meta']['extra_time_unit']===2 ? 60 : 1;
				
				/***/
				
				$i=0;
				
				foreach($coordinate as $value)
				{		
					if(($i!==0) && ($i!==$coordinateCount-1))
					{
						if(!array_key_exists('duration',$value)) continue;
						
						if(!$Validation->isNumber($value['duration'],0,999999999)) continue;
						
						$durationWaypoint+=(int)$value['duration'];
					}
						
					$i++;
				}		
				
			break;
		
			case 2:
				
				if($Validation->isNumber($data['duration_service_type_2'],0,999999999))
					$duration=(int)$data['duration_service_type_2'];	
				
				$duration*=60;
				
			break;
		
			case 3:
				
				if($Validation->isNumber($data['extra_time_service_type_3'],0,999999999))
					$duration+=(int)$data['extra_time_service_type_3'];
				
				$duration*=(int)$bookingForm['meta']['extra_time_unit']===2 ? 60 : 1;
				
			break;
		}
		
		if(in_array($data['service_type_id'],array(1,3)))
		{
			$transferTypeId=(int)$data['transfer_type_service_type_'.$data['service_type_id']];
			$transferTypeValue=in_array($transferTypeId,array(2,3)) ? 2 : 1;
				
			$duration+=($result['duration_map']*$transferTypeValue)+($durationWaypoint*$transferTypeValue);
				
			$distance*=$transferTypeValue;
		}
		
		$result['distance_sum']=$distance;
		$result['duration_sum']=$duration;
		
		/***/
	
		$response=$this->computeRoutes($bookingForm,$data,2);
				
		if($response===-1) return(-1);
		
		if($response!==-2)
		{
			$result['base_location_distance']=round((int)$response->routes[0]->distanceMeters/1000,1);
			$result['base_location_duration']=ceil((int)$response->routes[0]->duration/60);	
			
			$response=$this->computeRoutes($bookingForm,$data,3);
			
			if($response===-1) return(-1);
			
			if($response!==-2)
			{
				$result['base_location_return_distance']=round((int)$response->routes[0]->distanceMeters/1000,1);
				$result['base_location_return_duration']=ceil((int)$response->routes[0]->duration/60);					
			}
		}
		
		/****/
		
		$resultData=array();
		
		foreach($result as $resultIndex=>$resultValue)
			$resultData[$resultIndex]=$data[$resultIndex];
		
		/****/
		
		foreach($result as $resultIndex=>$resultValue)
		{
			if((float)$resultValue!==(float)$data[$resultIndex])
			{
				$LogManager=new CHBSLogManager();
				$LogManager->add('booking_data_validation',1,__('Result: ','chauffeur-booking-system').print_r($result,true).__('Data: ','chauffeur-booking-system').print_r($resultData,true)); 
				
				return(-2);
			}			
		}
		
		return(1);
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/