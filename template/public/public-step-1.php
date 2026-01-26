<?php
		if($this->data['widget_mode']!=1)
		{
?>
		<div class="chbs-notice chbs-hidden"></div>
<?php
		}
		
		$Validation=new CHBSValidation();
		
		/***/

		$class=array('chbs-layout-50x50');
		
		if(($this->data['widget_mode']==1) || ((int)$this->data['meta']['step_1_right_panel_visibility']===0)) 
			$class=array('chbs-layout-100');
		
		array_push($class,'chbs-clear-fix');
		
		/***/
		
		$fixedLocationClass=array();
		$fixedLocationEmptyItemHtml=null;
		
		if(((int)$this->data['meta']['location_fixed_list_item_empty_enable']===1) && ((int)$this->data['meta']['location_fixed_autocomplete_enable']===0))
			$fixedLocationEmptyItemHtml='<option value="-1">'.esc_html($this->data['meta']['location_fixed_list_item_empty_text']).'</option>';
		
		if((int)$this->data['meta']['location_fixed_autocomplete_enable']===1)
			$fixedLocationClass=array('chbs-selectmenu-disable','chbs-hidden'); 
		
		/***/
		
		$tabClass=array();
		
		if((count($this->data['meta']['service_type_id'])<=1) && ((int)$this->data['meta']['service_tab_enable']===0))
			array_push($tabClass,'chbs-hidden');
		
		/***/
?>
		<div<?php echo CHBSHelper::createCSSClassAttribute($class); ?>>

			<div class="chbs-layout-column-left">
			
				<div class="chbs-tab chbs-box-shadow">

					<ul<?php echo CHBSHelper::createCSSClassAttribute($tabClass); ?>>
<?php
		if(in_array(1,$this->data['meta']['service_type_id']))
		{
?>
						<li data-id="1"><a href="#panel-1"><?php esc_html_e('Distance','chauffeur-booking-system'); ?></a></li>
<?php
		}
		if(in_array(2,$this->data['meta']['service_type_id']))
		{		
?>
						<li data-id="2"><a href="#panel-2"><?php esc_html_e('Hourly','chauffeur-booking-system'); ?></a></li>
<?php
		}
		if(in_array(3,$this->data['meta']['service_type_id']))
		{
?>
						<li data-id="3"><a href="#panel-3"><?php esc_html_e('Flat rate','chauffeur-booking-system'); ?></a></li>
<?php
		}
		
		echo apply_filters(PLUGIN_CHBS_CONTEXT.'_public_tab_filter',null,'service_type');
?>
					</ul>
<?php

		if(in_array(1,$this->data['meta']['service_type_id']))
		{			
?>
					<div id="panel-1">
<?php
			if($this->data['widget_mode']!=1)
			{
?>
						<label class="chbs-form-label-group"><?php esc_html_e('Ride details','chauffeur-booking-system'); ?></label>
<?php
			}
			
			$b=array(false,false,false,false);
			
			$b[0]=CHBSBookingHelper::isPassengerEnable($this->data['meta'],1,'adult') && (!CHBSBookingHelper::isPassengerEnable($this->data['meta'],1,'children'));
			$b[1]=(int)$this->data['meta']['passenger_use_person_label']===1 ? true : false;
			$b[2]=(int)$this->data['meta']['passenger_number_dropdown_list_enable'] ? true : false;
			$b[3]=(int)$this->data['meta']['passenger_number_dropdown_list_display_type']===1 ? true : false;
			
			$passengerDisplay=in_array(false,$b,true) ? false : true;
			
			$pickupTimeReadOnly=false;
			$pickupTime=$this->data['booking_edit']->getFieldValue('pickup_time',array('meta','pickup_time'),1);
			
			if((int)$this->data['meta']['pickup_time_field_write_enable']!==1)
			{
				$pickupTime=null;
				$pickupTimeReadOnly=true;
			}
?>
						<div class="chbs-clear-fix chbs-form-field-pickup-date-time">

							<div class="chbs-form-field chbs-form-field-width-<?php echo esc_attr($passengerDisplay ? '33' : '50') ?>">
								<label class="chbs-form-field-label">
									<?php esc_html_e('Pickup date','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The date when your journey will start.','chauffeur-booking-system'); ?>"></span>
								</label>
								<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('pickup_date_service_type_1'); ?>" class="chbs-datepicker" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('pickup_date',array('meta','pickup_date'),1)); ?>"/>
							</div>

							<div class="chbs-form-field chbs-form-field-width-<?php echo esc_attr($passengerDisplay ? '33' : '50') ?>">
								<label>
									<?php esc_html_e('Pickup time','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The time when your journey will start.','chauffeur-booking-system'); ?>"></span>
								</label>
								<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('pickup_time_service_type_1'); ?>" <?php echo esc_attr($pickupTimeReadOnly ? 'readonly="readonly"' : '') ?> class="chbs-timepicker" value="<?php echo esc_attr($pickupTime); ?>"/>
							</div>
<?php
			if($passengerDisplay)
			{
				$passengerNumber=$this->data['booking_edit']->getFieldValue('passenger_adult',array('meta','passenger_adult_number'),1);
				if($Validation->isEmpty($passengerNumber)) $passengerNumber=$this->data['meta']['passenger_adult_default_number'];
?>
							<div class="chbs-form-field chbs-form-field-width-33">
								<label class="chbs-form-field-label">
									<?php esc_html_e('Persons','chauffeur-booking-system'); ?>
								</label>
								<select name="<?php CHBSHelper::getFormName('passenger_adult_service_type_1'); ?>">
<?php
				for($i=0;$i<=$this->data['vehicle_passenger_count_range']['max'];$i++)
				{
							echo '<option value="'.esc_attr($i).'"'.CHBSHelper::selectedIf($passengerNumber,$i,false).'>'.esc_html($i).'</option>';
				}
?>
								</select>
							</div>
<?php						
			}
?>
						</div>
<?php
			if($this->data['widget_mode']!=1)
			{
?>
						<div class="chbs-form-field chbs-form-field-location-autocomplete chbs-form-field-location-switch chbs-form-field-waypoint chbs-hidden">
							<label><?php esc_html_e('Waypoint','chauffeur-booking-system'); ?></label>
							<span class="chbs-meta-icon-2 chbs-meta-icon-2-location-1"></span>
							<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('waypoint_location_service_type_1[]'); ?>" placeholder="<?php esc_attr_e('Enter a location','chauffeur-booking-system'); ?>"/>
							<input type="hidden" name="<?php CHBSHelper::getFormName('waypoint_location_coordinate_service_type_1[]'); ?>"/>
							<span class="chbs-location-add chbs-meta-icon-plus"></span>
							<span class="chbs-location-remove chbs-meta-icon-minus"></span>
						</div>  
<?php
				if((int)$this->data['meta']['waypoint_duration_enable']===1)
				{
?>
						<div class="chbs-form-field chbs-form-field-waypoint-duration chbs-hidden">
							<label><?php esc_html_e('Waypoint duration (in minutes)','chauffeur-booking-system'); ?></label>
							<select name="<?php CHBSHelper::getFormName('waypoint_duration_service_type_1[]'); ?>" class="chbs-selectmenu-disable">
<?php
					for($i=$this->data['meta']['waypoint_duration_minimum_value'];$i<=$this->data['meta']['waypoint_duration_maximum_value'];$i+=$this->data['meta']['waypoint_duration_step_value'])
					{
?>
								<option value="<?php echo esc_attr($i); ?>"><?php echo sprintf(esc_html__('%d minute(s)','chauffeur-booking-system'),$i); ?></option>
<?php			  
					}
?>
							</select>
						</div> 					
<?php					
				}
			}
			
			if(count($this->data['meta']['location_fixed_pickup_service_type_1']))
			{
?>
						<div class="chbs-form-field chbs-form-field-location-fixed chbs-form-field-pickup-location">
							<label>
								<?php esc_html_e('Pickup location','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The address where your journey will start.','chauffeur-booking-system'); ?>"></span>
							</label>
<?php
				if((int)$this->data['meta']['location_fixed_autocomplete_enable']===1)
				{
?>
							<input name="<?php CHBSHelper::getFormName('fixed_location_pickup_service_type_1_autocomplete'); ?>" class="chbs-form-field-location-fixed-autocomplete" type="text" value="<?php echo esc_attr(CHBSRequestData::getFromWidget(1,'fixed_location_pickup_id')!==false ? '' : $this->data['meta']['location_fixed_pickup_service_type_1'][CHBSRequestData::getFromWidget(1,'fixed_location_pickup_id')]['address']); ?>"/>
<?php
				}
?>
							<select name="<?php CHBSHelper::getFormName('fixed_location_pickup_service_type_1'); ?>"<?php echo CHBSHelper::createCSSClassAttribute($fixedLocationClass); ?>>
<?php
				echo $fixedLocationEmptyItemHtml;
				foreach($this->data['meta']['location_fixed_pickup_service_type_1'] as $index=>$value)
				{
?>
								<option value="<?php echo esc_attr($index); ?>" data-location="<?php echo esc_attr(json_encode($value)); ?>" <?php CHBSHelper::selectedIf($this->data['booking_edit']->getFieldValue('fixed_location_pickup_id',array('meta','pickup_location_id'),1,$this->data['shortcode_attribute']['fixed_location_pickup_id']),$index); ?>><?php echo esc_html($value['name']); ?></option>
<?php			  
				}
?>							
							</select>
						</div>				   
<?php
			}
			else
			{
?>
						<div class="chbs-form-field chbs-form-field-location-autocomplete chbs-form-field-pickup-location chbs-form-field-location-switch" data-label-waypoint="<?php esc_attr_e('Waypoint'); ?>">
							<label>
								<?php esc_html_e('Pickup location','chauffeur-booking-system'); ?>
								<span class="chbs-my-location-link">&nbsp;&nbsp;-&nbsp;&nbsp;<a href="#"><?php esc_html_e('Use my location','chauffeur-booking-system'); ?></a></span>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The address where your journey will start.','chauffeur-booking-system'); ?>"></span>
							</label>
							<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('pickup_location_service_type_1'); ?>" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('pickup_location_text',array('_meta','pickup_location','address'),1)); ?>" placeholder="<?php esc_attr_e('Enter a location','chauffeur-booking-system'); ?>"/>
							<input type="hidden" name="<?php CHBSHelper::getFormName('pickup_location_coordinate_service_type_1'); ?>" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('pickup_location',array('_meta','pickup_location','coordinate'),1,null,'coordinate')); ?>"/>
<?php
				if(($this->data['widget_mode']!=1) && ($this->data['meta']['waypoint_enable']==1))
				{
?>
							<span class="chbs-location-add chbs-meta-icon-plus"></span>
<?php
			
				}
?>
						</div> 
<?php
			}

			if($this->data['widget_mode']!=1)
			{
				if($this->data['booking_edit']->isBookingEdit())
				{
					$waypointCount=0;
					
					if(array_key_exists('waypoint_location',$this->data['booking_edit']->booking['booking']['_meta']))
						$waypointCount=count($this->data['booking_edit']->booking['booking']['_meta']['waypoint_location']);
				
					for($i=0;$i<$waypointCount;$i++)
					{
						$address=null;
						$coordinate=null;

						$class=array('chbs-form-field','chbs-form-field-location-autocomplete','chbs-form-field-waypoint','chbs-form-field-location-switch');

						if($this->data['booking_edit']->isBookingEdit())
						{
							$address=$this->data['booking_edit']->booking['booking']['_meta']['waypoint_location'][$i]['address'];
							$coordinate=$this->data['booking_edit']->booking['booking']['_meta']['waypoint_location'][$i]['coordinate'];
						}
						else array_push($class,'chbs-hidden');
?>
						<div<?php echo CHBSHelper::createCSSClassAttribute($class); ?>>
							<label><?php esc_html_e('Waypoint','chauffeur-booking-system'); ?></label>
							<span class="chbs-meta-icon-2 chbs-meta-icon-2-location-1"></span>
							<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('waypoint_location_service_type_1[]'); ?>" value="<?php echo esc_attr($address); ?>" placeholder="<?php esc_attr_e('Enter a location','chauffeur-booking-system'); ?>"/>
							<input type="hidden" name="<?php CHBSHelper::getFormName('waypoint_location_coordinate_service_type_1[]'); ?>" value="<?php echo esc_attr($coordinate); ?>"/>
							<span class="chbs-location-add chbs-meta-icon-plus"></span>
							<span class="chbs-location-remove chbs-meta-icon-minus"></span>
						</div>  
<?php
					}
				}
			}  
			
			if(count($this->data['meta']['location_fixed_dropoff_service_type_1']))
			{
?>
						<div class="chbs-form-field chbs-form-field-location-fixed chbs-form-field-dropoff-location">
							<label>
								<?php esc_html_e('Drop-off location','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The address where your journey will end.','chauffeur-booking-system'); ?>"></span>
							</label>
<?php
				if((int)$this->data['meta']['location_fixed_autocomplete_enable']===1)
				{
?>
							<input name="<?php CHBSHelper::getFormName('fixed_location_dropoff_service_type_1_autocomplete'); ?>" class="chbs-form-field-location-fixed-autocomplete" type="text" value="<?php echo esc_attr(CHBSRequestData::getFromWidget(1,'fixed_location_dropoff_id')!==false ? '' : $this->data['meta']['location_fixed_dropoff_service_type_1'][CHBSRequestData::getFromWidget(1,'fixed_location_dropoff_id')]['address']); ?>"/>
<?php
				}
?>
							<select name="<?php CHBSHelper::getFormName('fixed_location_dropoff_service_type_1'); ?>"<?php echo CHBSHelper::createCSSClassAttribute($fixedLocationClass); ?>>
<?php
				echo $fixedLocationEmptyItemHtml;
				foreach($this->data['meta']['location_fixed_dropoff_service_type_1'] as $index=>$value)
				{	
?>
								<option value="<?php echo esc_attr($index); ?>" data-location="<?php echo esc_attr(json_encode($value)); ?>"<?php CHBSHelper::selectedIf($this->data['booking_edit']->getFieldValue('fixed_location_dropoff_id',array('meta','dropoff_location_id'),1,$this->data['shortcode_attribute']['fixed_location_dropoff_id']),$index); ?>><?php echo esc_html($value['name']); ?></option>
<?php			  
				}
?>							
							</select>
						</div>				   
<?php
			}
			else
			{
?>
						<div class="chbs-form-field chbs-form-field-location-autocomplete chbs-form-field-dropoff-location">
							<label>
								<?php esc_html_e('Drop-off location','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The address where your journey will end.','chauffeur-booking-system'); ?>"></span>						   
							</label>
							<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('dropoff_location_service_type_1'); ?>" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('dropoff_location_text',array('_meta','dropoff_location','address'),1,null)); ?>" placeholder="<?php esc_attr_e('Enter a location','chauffeur-booking-system'); ?>"/>
							<input type="hidden" name="<?php CHBSHelper::getFormName('dropoff_location_coordinate_service_type_1'); ?>" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('dropoff_location',array('_meta','dropoff_location','coordinate'),1,null,'coordinate')); ?>"/>
						</div>
<?php
			}
			if(count($this->data['meta']['transfer_type_enable_1']))
			{
?>
						<div class="chbs-form-field chbs-form-field-transfer-type">
							<label>
								<?php esc_html_e('Transfer type','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Transfer type of the journey.','chauffeur-booking-system'); ?>"></span>					 
							</label>
							<select name="<?php CHBSHelper::getFormName('transfer_type_service_type_1'); ?>">
<?php
				if((int)$this->data['meta']['transfer_type_list_item_empty_enable']===1)
					echo '<option value="-1">'.esc_html($this->data['meta']['transfer_type_list_item_empty_text']).'</option>';

				foreach($this->data['dictionary']['transfer_type'] as $index=>$value)
				{
					if(!in_array($index,$this->data['meta']['transfer_type_enable_1'])) continue;
?>
								<option value="<?php echo esc_attr($index); ?>" <?php CHBSHelper::selectedIf($this->data['booking_edit']->getFieldValue('transfer_type',array('meta','transfer_type_id'),1),$index); ?>><?php echo esc_html($value[0]); ?></option>
<?php			  
				}
?>							
							</select>
						</div>
<?php
				$class=array('chbs-clear-fix','chbs-form-field-return-date-time');
				if(!in_array(CHBSRequestData::getFromWidget(1,'transfer_type'),array(3,4)))
					array_push($class,'chbs-hidden');
?>
						<div<?php echo CHBSHelper::createCSSClassAttribute($class); ?>>

							<div class="chbs-form-field chbs-form-field-width-50">
								<label class="chbs-form-field-label"><?php esc_html_e('Return date','chauffeur-booking-system'); ?></label>
								<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('return_date_service_type_1'); ?>" class="chbs-datepicker" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('return_date',array('meta','return_date'),1)); ?>"/>
							</div>

							<div class="chbs-form-field chbs-form-field-width-50">
								<label><?php esc_html_e('Return time','chauffeur-booking-system'); ?></label>
								<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('return_time_service_type_1'); ?>" class="chbs-timepicker" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('return_time',array('meta','return_time'),1)); ?>"/>
							</div>

						</div>								  
<?php
			}
			
			if(((CHBSBookingHelper::isPassengerEnable($this->data['meta'],1,'adult')) || (CHBSBookingHelper::isPassengerEnable($this->data['meta'],1,'children'))) && (!$passengerDisplay)) 
			{
				$class=array(array('chbs-clear-fix','chbs-form-field-passenger'),array('chbs-form-field'));
				
				if(CHBSBookingHelper::isPassengerEnable($this->data['meta'],1,-1))
					 array_push($class[1],'chbs-form-field-width-50');
				
				if($this->data['widget_mode']!=1)
				{
?>
						<label class="chbs-form-label-group"><?php esc_html_e('Number of passengers','chauffeur-booking-system'); ?></label>
<?php
				}
?>
						<div<?php echo CHBSHelper::createCSSClassAttribute($class[0]); ?>>
<?php
				if(CHBSBookingHelper::isPassengerEnable($this->data['meta'],1,'adult'))
				{
					$passengerNumber=$this->data['booking_edit']->getFieldValue('passenger_adult',array('meta','passenger_adult_number'),1);
					if($Validation->isEmpty($passengerNumber)) $passengerNumber=$this->data['meta']['passenger_adult_default_number'];
?>
							<div<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
								<label class="chbs-form-field-label">
<?php
					if((int)$this->data['meta']['passenger_use_person_label']===1)
					{
?>
									<?php esc_html_e('Persons','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Number of passengers.','chauffeur-booking-system'); ?>"></span>
<?php
					}
					else
					{
?>								
									<?php esc_html_e('Adults','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Number of adults passengers.','chauffeur-booking-system'); ?>"></span>
<?php
					}
?>
								</label>
<?php
					if((int)$this->data['meta']['passenger_number_dropdown_list_enable']===1)
					{
?>
								<select name="<?php CHBSHelper::getFormName('passenger_adult_service_type_1'); ?>">
<?php
						for($i=0;$i<=$this->data['vehicle_passenger_count_range']['max'];$i++)
						{
							echo '<option value="'.esc_attr($i).'"'.CHBSHelper::selectedIf($passengerNumber,$i,false).'>'.esc_html($i).'</option>';
						}
?>
								</select>
<?php
					}
					else
					{
?>
								<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('passenger_adult_service_type_1'); ?>" value="<?php echo esc_attr($passengerNumber);  ?>"/>

<?php
					}
?>
							</div>					   
<?php
				}
				if(CHBSBookingHelper::isPassengerEnable($this->data['meta'],1,'children'))
				{
					$passengerNumber=$this->data['booking_edit']->getFieldValue('passenger_children',array('meta','passenger_children_number'),1);
					if($Validation->isEmpty($passengerNumber)) $passengerNumber=$this->data['meta']['passenger_children_default_number'];
?>
							<div<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
								<label class="chbs-form-field-label">
									<?php esc_html_e('Children','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Number of children.','chauffeur-booking-system'); ?>"></span>
								</label>
<?php
					if((int)$this->data['meta']['passenger_number_dropdown_list_enable']===1)
					{
?>
								<select name="<?php CHBSHelper::getFormName('passenger_children_service_type_1'); ?>">
<?php
						for($i=0;$i<=$this->data['vehicle_passenger_count_range']['max'];$i++)
						{
							echo '<option value="'.esc_attr($i).'"'.CHBSHelper::selectedIf($passengerNumber,$i,false).'>'.esc_html($i).'</option>';
						}
?>
								</select>
<?php
					}
					else
					{
?>
								<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('passenger_children_service_type_1'); ?>" value="<?php echo esc_attr($passengerNumber); ?>"/>
<?php
					}
?>
								
							</div>			 
<?php				  
				}				
?>	 
						</div>
<?php	
			}

			if($this->data['meta']['extra_time_enable']==1)
			{
				if($this->data['widget_mode']!=1)
				{
?>
						<label class="chbs-form-label-group"><?php esc_html_e('Extra options','chauffeur-booking-system'); ?></label>
<?php
				}
?>
						<div class="chbs-form-field chbs-form-field-extra-time">
							<label>
								<?php esc_html_e('Extra time','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Extra time included to the journey.','chauffeur-booking-system'); ?>"></span>
							</label>
							<select name="<?php CHBSHelper::getFormName('extra_time_service_type_1'); ?>">
<?php
				for($i=$this->data['meta']['extra_time_range_min'];$i<=$this->data['meta']['extra_time_range_max'];$i+=$this->data['meta']['extra_time_step'])
				{	
?>
								<option value="<?php echo esc_attr($i); ?>" <?php CHBSHelper::selectedIf($this->data['booking_edit']->getFieldValue('extra_time',array('_meta','extra_time_value'),1),$i); ?>><?php echo sprintf(($this->data['meta']['extra_time_unit']==1 ? esc_html__('%d minute(s)','chauffeur-booking-system') : esc_html__('%d hour(s)','chauffeur-booking-system')),$i); ?></option>
<?php			  
				}
?>
							</select>
						</div>	
<?php
			}
?>
					</div>
<?php
		}
		
		if(in_array(2,$this->data['meta']['service_type_id']))
		{
?>
					<div id="panel-2">
<?php
			if($this->data['widget_mode']!=1)
			{
?>						
						<label class="chbs-form-label-group"><?php esc_html_e('Ride details','chauffeur-booking-system'); ?></label>
<?php
			}
			
			$b=array(false,false,false);
			
			$b[0]=CHBSBookingHelper::isPassengerEnable($this->data['meta'],2,'adult') && (!CHBSBookingHelper::isPassengerEnable($this->data['meta'],2,'children'));
			$b[1]=(int)$this->data['meta']['passenger_use_person_label']===1 ? true : false;
			$b[2]=(int)$this->data['meta']['passenger_number_dropdown_list_enable'] ? true : false;
			
			$passengerDisplay=in_array(false,$b,true) ? false : true;
?>
						<div class="chbs-clear-fix chbs-form-field-pickup-date-time">

							<div class="chbs-form-field chbs-form-field-width-<?php echo esc_attr($passengerDisplay ? '33' : '50') ?>">
								<label class="chbs-form-field-label">
									<?php esc_html_e('Pickup date','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The date when your journey will start.','chauffeur-booking-system'); ?>"></span>
								</label>
								<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('pickup_date_service_type_2'); ?>" class="chbs-datepicker" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('pickup_date',array('meta','pickup_date'),2)); ?>"/>
							</div>

							<div class="chbs-form-field chbs-form-field-width-<?php echo esc_attr($passengerDisplay ? '33' : '50') ?>">
								<label>
									<?php esc_html_e('Pickup time','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The time when your journey will start.','chauffeur-booking-system'); ?>"></span>
								</label>
								<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('pickup_time_service_type_2'); ?>" class="chbs-timepicker" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('pickup_time',array('meta','pickup_time'),2)); ?>"/>
							</div>
<?php
			if($passengerDisplay)
			{
				$passengerNumber=$this->data['booking_edit']->getFieldValue('passenger_adult',array('meta','passenger_adult_number'),2);
				if($Validation->isEmpty($passengerNumber)) $passengerNumber=$this->data['meta']['passenger_adult_default_number'];
?>
							<div class="chbs-form-field chbs-form-field-width-33">
								<label class="chbs-form-field-label">
									<?php esc_html_e('Persons','chauffeur-booking-system'); ?>
								</label>
								<select name="<?php CHBSHelper::getFormName('passenger_adult_service_type_2'); ?>">
<?php
				for($i=0;$i<=$this->data['vehicle_passenger_count_range']['max'];$i++)
				{
							echo '<option value="'.esc_attr($i).'"'.CHBSHelper::selectedIf($passengerNumber,$i,false).'>'.esc_html($i).'</option>';
				}
?>
								</select>
							</div>
<?php						
			}
?>
						</div>
<?php
			if(count($this->data['meta']['location_fixed_pickup_service_type_2']))
			{
?>
						<div class="chbs-form-field chbs-form-field-location-fixed chbs-form-field-pickup-location">
							<label>
								<?php esc_html_e('Pickup location','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The address where your journey will start.','chauffeur-booking-system'); ?>"></span>
							</label>
<?php
				if((int)$this->data['meta']['location_fixed_autocomplete_enable']===1)
				{
?>
							<input name="<?php CHBSHelper::getFormName('fixed_location_pickup_service_type_2_autocomplete'); ?>" class="chbs-form-field-location-fixed-autocomplete" type="text" value="<?php echo esc_attr(is_null(CHBSRequestData::getFromWidget(2,'fixed_location_pickup_id')) ? '' : $this->data['meta']['location_fixed_pickup_service_type_1'][CHBSRequestData::getFromWidget(2,'fixed_location_pickup_id')]['address']); ?>"/>
<?php
				}
?>
							<select name="<?php CHBSHelper::getFormName('fixed_location_pickup_service_type_2'); ?>"<?php echo CHBSHelper::createCSSClassAttribute($fixedLocationClass); ?>>
<?php
				echo $fixedLocationEmptyItemHtml;
				foreach($this->data['meta']['location_fixed_pickup_service_type_2'] as $index=>$value)
				{
?>
								<option value="<?php echo esc_attr($index); ?>" data-location="<?php echo esc_attr(json_encode($value)); ?>"<?php CHBSHelper::selectedIf($this->data['booking_edit']->getFieldValue('fixed_location_pickup_id',array('meta','pickup_location_id'),2),$index); ?>><?php echo esc_html($value['name']); ?></option>
<?php			  
				}
?>								   
							</select>
						</div>				   
<?php
			}
			else
			{
?>
						<div class="chbs-form-field chbs-form-field-location-autocomplete chbs-form-field-pickup-location">
							<label>
								<?php esc_html_e('Pickup location','chauffeur-booking-system'); ?>
								<span class="chbs-my-location-link">&nbsp;&nbsp;-&nbsp;&nbsp;<a href="#"><?php esc_html_e('Use my location','chauffeur-booking-system'); ?></a></span>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The address where your journey will start.','chauffeur-booking-system'); ?>"></span>
							</label>
							<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('pickup_location_service_type_2'); ?>" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('pickup_location_text',array('_meta','pickup_location','address'),2)); ?>" placeholder="<?php esc_attr_e('Enter a location','chauffeur-booking-system'); ?>"/>
							<input type="hidden" name="<?php CHBSHelper::getFormName('pickup_location_coordinate_service_type_2'); ?>" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('pickup_location',array('_meta','pickup_location','coordinate'),2,null,'coordinate')); ?>"/>
						</div>   
<?php
			}
?>
						<div class="chbs-form-field chbs-form-field-duration">
							<label>
								<?php esc_html_e('Duration (in hours)','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Duration of the journey.','chauffeur-booking-system'); ?>"></span>
							</label>
							<select name="<?php CHBSHelper::getFormName('duration_service_type_2'); ?>">
<?php
			foreach($this->data['duration_distance'] as $index=>$value)
			{
?>
								<option value="<?php echo esc_attr($index); ?>" <?php CHBSHelper::selectedIf($this->data['booking_edit']->getFieldValue('duration',array('meta','duration'),2),$index); ?>>
									<?php echo $value['label']; ?>
								</option>
<?php			  
			}
?>
							</select>
						</div> 
<?php
			if(((CHBSBookingHelper::isPassengerEnable($this->data['meta'],2,'adult')) || (CHBSBookingHelper::isPassengerEnable($this->data['meta'],2,'children'))) && (!$passengerDisplay))
			{
				$class=array(array('chbs-clear-fix','chbs-form-field-passenger'),array('chbs-form-field'));
				
				if(CHBSBookingHelper::isPassengerEnable($this->data['meta'],2,-1))
					 array_push($class[1],'chbs-form-field-width-50');
				
				if($this->data['widget_mode']!=1)
				{
?>
						<label class="chbs-form-label-group"><?php esc_html_e('Number of passengers','chauffeur-booking-system'); ?></label>
<?php
				}
?>
						<div<?php echo CHBSHelper::createCSSClassAttribute($class[0]); ?>>
<?php
				if(CHBSBookingHelper::isPassengerEnable($this->data['meta'],2,'adult'))
				{
					$passengerNumber=$this->data['booking_edit']->getFieldValue('passenger_adult',array('meta','passenger_adult_number'),2);
					if($Validation->isEmpty($passengerNumber)) $passengerNumber=$this->data['meta']['passenger_adult_default_number'];
?>
							<div<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
								<label class="chbs-form-field-label">
<?php
					if((int)$this->data['meta']['passenger_use_person_label']===1)
					{
?>
									<?php esc_html_e('Persons','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Number of passengers.','chauffeur-booking-system'); ?>"></span>
<?php
					}
					else
					{
?>
									<?php esc_html_e('Adults','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Number of adults passengers.','chauffeur-booking-system'); ?>"></span>
<?php
					}
?>
								</label>
<?php
					if((int)$this->data['meta']['passenger_number_dropdown_list_enable']===1)
					{
?>
								<select name="<?php CHBSHelper::getFormName('passenger_adult_service_type_2'); ?>">
<?php
						for($i=0;$i<=$this->data['vehicle_passenger_count_range']['max'];$i++)
						{
							echo '<option value="'.esc_attr($i).'"'.CHBSHelper::selectedIf($passengerNumber,$i,false).'>'.esc_html($i).'</option>';
						}
?>
								</select>
<?php
					}
					else
					{
?>
								<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('passenger_adult_service_type_2'); ?>" value="<?php echo esc_attr($passengerNumber); ?>"/>
<?php
					}
?>
							</div>					   
<?php
				}
				if(CHBSBookingHelper::isPassengerEnable($this->data['meta'],2,'children'))
				{
					$passengerNumber=$this->data['booking_edit']->getFieldValue('passenger_children',array('meta','passenger_children_number'),2);
					if($Validation->isEmpty($passengerNumber)) $passengerNumber=$this->data['meta']['passenger_children_default_number'];
?>
							<div<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
								<label class="chbs-form-field-label">
									<?php esc_html_e('Children','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Number of children.','chauffeur-booking-system'); ?>"></span>								
								</label>
<?php
					if((int)$this->data['meta']['passenger_number_dropdown_list_enable']===1)
					{
?>
								<select name="<?php CHBSHelper::getFormName('passenger_children_service_type_2'); ?>">
<?php
						for($i=0;$i<=$this->data['vehicle_passenger_count_range']['max'];$i++)
						{
							echo '<option value="'.esc_attr($i).'"'.CHBSHelper::selectedIf($passengerNumber,$i,false).'>'.esc_html($i).'</option>';
						}
?>
								</select>
<?php
					}
					else
					{
?>
								<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('passenger_children_service_type_2'); ?>" value="<?php echo esc_attr($passengerNumber); ?>"/>
<?php
					}
?>
							</div>			 
<?php				  
				}				
?>	 
						</div>
<?php	
			}

			if((int)$this->data['meta']['dropoff_location_field_enable']===1)
			{
				if($this->data['widget_mode']!=1)
				{
?>
						<label class="chbs-form-label-group"><?php esc_html_e('Extra options','chauffeur-booking-system'); ?></label>
<?php
				}

				if(count($this->data['meta']['location_fixed_dropoff_service_type_2']))
				{
?>
						<div class="chbs-form-field chbs-form-field-location-fixed chbs-form-field-dropoff-location">
							<label>
								<?php esc_html_e('Drop-off location','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The address where your journey will end.','chauffeur-booking-system'); ?>"></span>
							</label>
<?php
					if((int)$this->data['meta']['location_fixed_autocomplete_enable']===1)
					{
?>
							<input name="<?php CHBSHelper::getFormName('fixed_location_dropoff_service_type_2_autocomplete'); ?>" class="chbs-form-field-location-fixed-autocomplete" type="text" value="<?php echo esc_attr(is_null(CHBSRequestData::getFromWidget(2,'fixed_location_dropoff_id')) ? '' : $this->data['meta']['location_fixed_dropoff_service_type_2'][CHBSRequestData::getFromWidget(2,'fixed_location_dropoff_id')]['address']); ?>"/>
 <?php
					}
 ?>
							<select name="<?php CHBSHelper::getFormName('fixed_location_dropoff_service_type_2'); ?>"<?php echo CHBSHelper::createCSSClassAttribute($fixedLocationClass); ?>>
<?php
					echo $fixedLocationEmptyItemHtml;
					foreach($this->data['meta']['location_fixed_dropoff_service_type_2'] as $index=>$value)
					{
?>
								<option value="<?php echo esc_attr($index); ?>" data-location="<?php echo esc_attr(json_encode($value)); ?>"<?php CHBSHelper::selectedIf($this->data['booking_edit']->getFieldValue('fixed_location_dropoff_id',array('meta','dropoff_location_id'),2),$index); ?>><?php echo esc_html($value['name']); ?></option>
<?php			  
					}
?>									
							</select>
						</div>				   
<?php
				}
				else
				{
?>
						<div class="chbs-form-field chbs-form-field-location-autocomplete chbs-form-field-dropoff-location">
							<label>
								<?php esc_html_e('Drop-off location','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The address where your journey will end.','chauffeur-booking-system'); ?>"></span>
							</label>
							<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('dropoff_location_service_type_2'); ?>" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('dropoff_location_text',array('_meta','dropoff_location','address'),2)); ?>" placeholder="<?php esc_attr_e('Enter a location','chauffeur-booking-system'); ?>"/>
							<input type="hidden" name="<?php CHBSHelper::getFormName('dropoff_location_coordinate_service_type_2'); ?>" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('dropoff_location',array('_meta','dropoff_location','coordinate'),1,null,'coordinate')); ?>"/>
						</div>
<?php
				}
			}
?>
					</div>
<?php
		}
		
		if(in_array(3,$this->data['meta']['service_type_id']))
		{
?>
					<div id="panel-3">
<?php
			if($this->data['widget_mode']!=1)
			{
?>						
						<label class="chbs-form-label-group"><?php esc_html_e('Ride details','chauffeur-booking-system'); ?></label>
<?php
			}
			
			$b=array(false,false,false);
			
			$b[0]=CHBSBookingHelper::isPassengerEnable($this->data['meta'],3,'adult') && (!CHBSBookingHelper::isPassengerEnable($this->data['meta'],3,'children'));
			$b[1]=(int)$this->data['meta']['passenger_use_person_label']===1 ? true : false;
			$b[2]=(int)$this->data['meta']['passenger_number_dropdown_list_enable'] ? true : false;
			
			$passengerDisplay=in_array(false,$b,true) ? false : true;
?>
						<div class="chbs-clear-fix chbs-form-field-pickup-date-time">

							<div class="chbs-form-field chbs-form-field-width-<?php echo esc_attr($passengerDisplay ? '33' : '50') ?>">
								<label class="chbs-form-field-label">
									<?php esc_html_e('Pickup date','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The date when your journey will start.','chauffeur-booking-system'); ?>"></span>
								</label>
								<input type="text" name="<?php CHBSHelper::getFormName('pickup_date_service_type_3'); ?>" class="chbs-datepicker" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('pickup_date',array('meta','pickup_date'),3)); ?>"/>
							</div>

							<div class="chbs-form-field chbs-form-field-width-<?php echo esc_attr($passengerDisplay ? '33' : '50') ?>">
								<label>
									<?php esc_html_e('Pickup time','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The time when your journey will start.','chauffeur-booking-system'); ?>"></span>
								</label>
								<input type="text" name="<?php CHBSHelper::getFormName('pickup_time_service_type_3'); ?>" class="chbs-timepicker" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('pickup_time',array('meta','pickup_time'),3)); ?>"/>
							</div>
<?php
			if($passengerDisplay)
			{
				$passengerNumber=$this->data['booking_edit']->getFieldValue('passenger_adult',array('meta','passenger_adult_number'),3);
				if($Validation->isEmpty($passengerNumber)) $passengerNumber=$this->data['meta']['passenger_adult_default_number'];
?>
							<div class="chbs-form-field chbs-form-field-width-33">
								<label class="chbs-form-field-label">
									<?php esc_html_e('Persons','chauffeur-booking-system'); ?>
								</label>
								<select name="<?php CHBSHelper::getFormName('passenger_adult_service_type_3'); ?>">
<?php
				for($i=0;$i<=$this->data['vehicle_passenger_count_range']['max'];$i++)
				{
							echo '<option value="'.esc_attr($i).'"'.CHBSHelper::selectedIf($passengerNumber,$i,false).'>'.esc_html($i).'</option>';
				}
?>
								</select>
							</div>
<?php						
			}
?>
						</div>
<?php
			if((int)$this->data['meta']['route_pickup_location_field_enable']===1)
			{
?>
						<div class="chbs-form-field chbs-form-field-location-autocomplete chbs-form-field-pickup-location">
							<label>
								<?php esc_html_e('Pickup location','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The address where your journey will start.','chauffeur-booking-system'); ?>"></span>						   
							</label>
							<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('pickup_location_service_type_3'); ?>" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('pickup_location_text',array('_meta','pickup_location','address'),3,null)); ?>" placeholder="<?php esc_attr_e('Enter a location','chauffeur-booking-system'); ?>"/>
							<input type="hidden" name="<?php CHBSHelper::getFormName('pickup_location_coordinate_service_type_3'); ?>" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('pickup_location',array('_meta','pickup_location','coordinate'),3,null,'coordinate')); ?>"/>
						</div>			
<?php				
			}
?>
						<div class="chbs-form-field chbs-form-field-route">
							<label>
								<?php esc_html_e('Route','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Route.','chauffeur-booking-system'); ?>"></span>			   
							</label>
<?php
			$routeClass=array(); 
			
			if((int)$this->data['meta']['route_list_autocomplete_enable']===1)
			{
				$routeClass=array('chbs-selectmenu-disable','chbs-hidden'); 
?>
							<input name="<?php CHBSHelper::getFormName('route_service_type_3_autocomplete'); ?>" class="chbs-form-field-route-autocomplete" type="text" value=""/>
<?php
			}
?>
							<select name="<?php CHBSHelper::getFormName('route_service_type_3'); ?>" <?php echo CHBSHelper::createCSSClassAttribute($routeClass); ?>>
<?php   
			if(((int)$this->data['meta']['route_list_autocomplete_enable']===0) && ((int)$this->data['meta']['route_list_item_empty_enable']===1))
				echo '<option value="-1" data-coordinate="">'.esc_html($this->data['meta']['route_list_item_empty_text']).'</option>';

			$Route=new CHBSRoute();
			
			foreach($this->data['dictionary']['route'] as $index=>$value)
			{
				$value['meta']['pickup_hour']=$Route->formatTimeToDisplayRoute($value['meta']['pickup_hour']);				
?>
								<option value="<?php echo esc_attr($index); ?>" data-coordinate="<?php echo esc_attr(json_encode($value['meta']['coordinate'])); ?>" data-pickup_hour="<?php echo esc_attr(json_encode($value['meta']['pickup_hour'])); ?>" <?php CHBSHelper::selectedIf($this->data['booking_edit']->getFieldValue('route_id',array('meta','route_id'),3),$index); ?>><?php echo get_the_title($index); ?></option>
<?php
			}
?>
							</select>
							<input type="hidden" name="<?php CHBSHelper::getFormName('route_coordinate_service_type_3'); ?>"/>
						</div>	
<?php
			if((int)$this->data['meta']['route_dropoff_location_field_enable']===1)
			{
?>
						<div class="chbs-form-field chbs-form-field-location-autocomplete chbs-form-field-dropoff-location">
							<label>
								<?php esc_html_e('Drop-off location','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('The address where your journey will end.','chauffeur-booking-system'); ?>"></span>						   
							</label>
							<input type="text" autocomplete="off" name="<?php CHBSHelper::getFormName('dropoff_location_service_type_3'); ?>" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('dropoff_location_text',array('_meta','dropoff_location','address'),3,null)); ?>" placeholder="<?php esc_attr_e('Enter a location','chauffeur-booking-system'); ?>"/>
							<input type="hidden" name="<?php CHBSHelper::getFormName('dropoff_location_coordinate_service_type_3'); ?>" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('dropoff_location',array('_meta','dropoff_location','coordinate'),3,null,'coordinate')); ?>"/>
						</div>			
<?php				
			}
			
			if(count($this->data['meta']['transfer_type_enable_3']))
			{
?>
						<div class="chbs-form-field chbs-form-field-transfer-type">
							<label>
								<?php esc_html_e('Transfer type','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Transfer type of the journey.','chauffeur-booking-system'); ?>"></span>				  
							</label>
							<select name="<?php CHBSHelper::getFormName('transfer_type_service_type_3'); ?>">
<?php
				if((int)$this->data['meta']['transfer_type_list_item_empty_enable']===1)
					echo '<option value="-1">'.esc_html($this->data['meta']['transfer_type_list_item_empty_text']).'</option>';

				foreach($this->data['dictionary']['transfer_type'] as $index=>$value)
				{
					if(!in_array($index,$this->data['meta']['transfer_type_enable_3'])) continue;
?>
								<option value="<?php echo esc_attr($index); ?>" <?php CHBSHelper::selectedIf($this->data['booking_edit']->getFieldValue('transfer_type',array('meta','transfer_type_id'),3),$index); ?>><?php echo esc_html($value[0]); ?></option>
<?php			  
				}
?>							
							</select>
						</div>
<?php
				$class=array('chbs-clear-fix','chbs-form-field-return-date-time');
				if(!in_array(CHBSRequestData::getFromWidget(3,'transfer_type'),array(3,4)))
					array_push($class,'chbs-hidden');
?>
						<div<?php echo CHBSHelper::createCSSClassAttribute($class); ?>>

							<div class="chbs-form-field chbs-form-field-width-50">
								<label class="chbs-form-field-label"><?php esc_html_e('Return date','chauffeur-booking-system'); ?></label>
								<input type="text" name="<?php CHBSHelper::getFormName('return_date_service_type_3'); ?>" class="chbs-datepicker" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('return_date',array('meta','return_date'),3)); ?>"/>
							</div>

							<div class="chbs-form-field chbs-form-field-width-50">
								<label><?php esc_html_e('Return time','chauffeur-booking-system'); ?></label>
								<input type="text" name="<?php CHBSHelper::getFormName('return_time_service_type_3'); ?>" class="chbs-timepicker" value="<?php echo esc_attr($this->data['booking_edit']->getFieldValue('return_time',array('meta','return_time'),3)); ?>"/>
							</div>

						</div>								  
<?php
			}

			if(((CHBSBookingHelper::isPassengerEnable($this->data['meta'],3,'adult')) || (CHBSBookingHelper::isPassengerEnable($this->data['meta'],3,'children'))) && (!$passengerDisplay))
			{
				$class=array(array('chbs-clear-fix','chbs-form-field-passennger'),array('chbs-form-field'));
				
				if(CHBSBookingHelper::isPassengerEnable($this->data['meta'],3,-1))
					 array_push($class[1],'chbs-form-field-width-50');
				
				if($this->data['widget_mode']!=1)
				{
?>
						<label class="chbs-form-label-group"><?php esc_html_e('Number of passengers','chauffeur-booking-system'); ?></label>
<?php
				}
?>
						<div<?php echo CHBSHelper::createCSSClassAttribute($class[0]); ?>>
<?php
				if(CHBSBookingHelper::isPassengerEnable($this->data['meta'],3,'adult'))
				{
					$passengerNumber=$this->data['booking_edit']->getFieldValue('passenger_adult',array('meta','passenger_adult_number'),3);
					if($Validation->isEmpty($passengerNumber)) $passengerNumber=$this->data['meta']['passenger_adult_default_number'];
?>
							<div<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
								<label class="chbs-form-field-label">
<?php
					if((int)$this->data['meta']['passenger_use_person_label']===1)
					{
?>
									<?php esc_html_e('Persons','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Number of passengers.','chauffeur-booking-system'); ?>"></span>
<?php
					}
					else
					{
?>
									<?php esc_html_e('Adults','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Number of adults passengers.','chauffeur-booking-system'); ?>"></span>
<?php
					}
?>
								</label>
<?php
					if((int)$this->data['meta']['passenger_number_dropdown_list_enable']===1)
					{
?>
								<select name="<?php CHBSHelper::getFormName('passenger_adult_service_type_3'); ?>">
<?php
						for($i=0;$i<=$this->data['vehicle_passenger_count_range']['max'];$i++)
						{
							echo '<option value="'.esc_attr($i).'"'.CHBSHelper::selectedIf($passengerNumber,$i,false).'>'.esc_html($i).'</option>';
						}
?>
								</select>
<?php
					}
					else
					{
?>
								<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('passenger_adult_service_type_3'); ?>" value="<?php echo esc_attr($passengerNumber); ?>"/>
<?php
					}
?>
							</div>					   
<?php
				}
				if(CHBSBookingHelper::isPassengerEnable($this->data['meta'],3,'children'))
				{
					$passengerNumber=$this->data['booking_edit']->getFieldValue('passenger_children',array('meta','passenger_children_number'),3);
					if($Validation->isEmpty($passengerNumber)) $passengerNumber=$this->data['meta']['passenger_children_default_number'];
?>
							<div<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
								<label class="chbs-form-field-label">
									<?php esc_html_e('Children','chauffeur-booking-system'); ?>
									<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Number of children.','chauffeur-booking-system'); ?>"></span>
								</label>
<?php
					if((int)$this->data['meta']['passenger_number_dropdown_list_enable']===1)
					{
?>
								<select name="<?php CHBSHelper::getFormName('passenger_children_service_type_3'); ?>">
<?php
						for($i=0;$i<=$this->data['vehicle_passenger_count_range']['max'];$i++)
						{
							echo '<option value="'.esc_attr($i).'"'.CHBSHelper::selectedIf($passengerNumber,$i,false).'>'.esc_html($i).'</option>';
						}
?>
								</select>
<?php
					}
					else
					{
?>
								<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('passenger_children_service_type_3'); ?>" value="<?php echo esc_attr($passengerNumber); ?>"/>
<?php
					}
?>
							</div>			 
<?php				  
				}				
?>	 
						</div>
<?php	
			}
			
			if($this->data['meta']['extra_time_enable']==1)
			{
				if($this->data['widget_mode']!=1)
				{
?>
						<label class="chbs-form-label-group"><?php esc_html_e('Extra options','chauffeur-booking-system'); ?></label>
<?php
				}
?>
						<div class="chbs-form-field chbs-form-field-extra-time">
							<label>
								<?php esc_html_e('Extra time','chauffeur-booking-system'); ?>
								<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Extra time included to the journey.','chauffeur-booking-system'); ?>"></span>	   
							</label>
							<select name="<?php CHBSHelper::getFormName('extra_time_service_type_3'); ?>">
<?php
				for($i=$this->data['meta']['extra_time_range_min'];$i<=$this->data['meta']['extra_time_range_max'];$i+=$this->data['meta']['extra_time_step'])
				{
?>
								<option value="<?php echo esc_attr($i); ?>" <?php CHBSHelper::selectedIf($this->data['booking_edit']->getFieldValue('extra_time',array('_meta','extra_time_value'),3),$i); ?>><?php echo sprintf(($this->data['meta']['extra_time_unit']==1 ? esc_html__('%d minute(s)','chauffeur-booking-system') : esc_html__('%d hour(s)','chauffeur-booking-system')),$i); ?></option>
<?php			  
				}
?>
							</select>
						</div>						
<?php
			}
?>	  
					</div>
<?php
		}
?>		  
				</div>	

			</div>
<?php
		if($this->data['widget_mode']!=1)
		{
			$class=array('chbs-layout-column-right');
					
			if((int)$this->data['meta']['step_1_right_panel_visibility']===0)
				array_push($class,'chbs-hidden');
?>
			<div<?php echo CHBSHelper::createCSSClassAttribute($class); ?>>
				<div class="chbs-google-map">
					<div id="chbs_google_map"></div>
				</div>
				<div class="chbs-ride-info chbs-box-shadow">
					<div>
						<span class="chbs-meta-icon-route"></span>
						<span><?php esc_html_e('Total distance','chauffeur-booking-system'); ?></span>
						<span>
							<span>0</span>
							<span><?php echo esc_html($this->data['length_unit'][1]); ?></span>
						</span>
					</div>
<?php
			if((int)$this->data['meta']['total_time_display_enable']===1)
			{
?>
					<div>
						<span class="chbs-meta-icon-clock"></span>
						<span><?php esc_html_e('Total time','chauffeur-booking-system'); ?></span>
						<span>
							<span>0</span>
							<span><?php esc_html_e('h','chauffeur-booking-system'); ?></span>
							<span>0</span>
							<span><?php esc_html_e('m','chauffeur-booking-system'); ?></span>
						</span>
					</div>		
<?php
			}
?>
				</div>
			</div>
<?php
		}
?>
		</div>
<?php
		if($this->data['widget_mode']==1)
		{
?>
		<div class="chbs-clear-fix">
			<a href="#" class="chbs-button chbs-button-style-1 chbs-button-widget-submit">
				<?php esc_html_e('Book now','chauffeur-booking-system'); ?>
			</a>
		</div>
<?php
		}
		else
		{
?>
		<div class="chbs-clear-fix chbs-main-content-navigation-button">
			<a href="#" class="chbs-button chbs-button-style-1 chbs-button-step-next">
				<?php echo esc_html($this->data['step']['dictionary'][1]['button']['next']); ?>
				<span class="chbs-meta-icon-arrow-horizontal-large"></span>
			</a> 
		</div>
<?php
		}