<?php 
		echo $this->data['nonce']; 
?>	
		<div class="to">
			<div class="ui-tabs">
				<ul>
					<li><a href="#meta-box-location-1"><?php esc_html_e('General','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-location-2"><?php esc_html_e('Routes','chauffeur-booking-system'); ?></a></li>
				</ul>
				<div id="meta-box-location-1">
					<ul class="to-form-field-list">
						<?php echo CHBSHelper::createPostIdField(__('Location ID','chauffeur-booking-system')); ?>
						<li>
							<h5><?php esc_html_e('Address','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Enter address of the location.','chauffeur-booking-system'); ?></span>
							<div>
								<input type="text" name="<?php CHBSHelper::getFormName('location_name'); ?>" id="<?php CHBSHelper::getFormName('location_name'); ?>" value="<?php echo esc_attr($this->data['meta']['location_name']); ?>"/>
								<input type="hidden" name="<?php CHBSHelper::getFormName('location_name_coordinate_lat'); ?>" value="<?php echo esc_attr($this->data['meta']['location_name_coordinate_lat']); ?>"/>
								<input type="hidden" name="<?php CHBSHelper::getFormName('location_name_coordinate_lng'); ?>" value="<?php echo esc_attr($this->data['meta']['location_name_coordinate_lng']); ?>"/>							
							</div>
						</li> 
						<li>
							<h5><?php esc_html_e('Discount for vehicle price','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Maximum percentage discount for the vehicle if "Bid vehicle price" option is enabled in a booking form.','chauffeur-booking-system'); ?><br/>
							</span>
							<div>
								<input type="text" maxlength="5" name="<?php CHBSHelper::getFormName('vehicle_bid_max_percentage_discount'); ?>" value="<?php echo esc_attr($this->data['meta']['vehicle_bid_max_percentage_discount']); ?>"/>
							</div>									
						</li>
						
					</ul>
				</div>
				<div id="meta-box-location-2">
					<ul class="to-form-field-list">
						<li>
							<h5><?php esc_html_e('Routes','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Choose drop-off locations not available for current one (pick up location).','chauffeur-booking-system'); ?></span>
<?php
		if(count($this->data['dictionary']['booking_form']))
		{
?>
							<div>
								<table class="to-table">
									<tr>
										<th style="width:20%">
											<div>
												<?php esc_html_e('Booking form','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Booking form name.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:40%">
											<div>
												<?php esc_html_e('Service type','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Service type name.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:40%">
											<div>
												<?php esc_html_e('Locations','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Disabled drop-off locations.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
									</tr>
<?php
			foreach($this->data['dictionary']['booking_form'] as $bookingFormIndex=>$bookingFormData)
			{
				if(!isset($this->data['meta']['location_dropoff_disable_service_type_1'][$bookingFormIndex]))
					$this->data['meta']['location_dropoff_disable_service_type_1'][$bookingFormIndex]=array(-1);
				if(!isset($this->data['meta']['location_dropoff_disable_service_type_2'][$bookingFormIndex]))
					$this->data['meta']['location_dropoff_disable_service_type_2'][$bookingFormIndex]=array(-1);
?>
									<tr>
										<td rowspan="2">
											<div class="to-clear-fix">
												<?php echo esc_html($bookingFormData['post']->post_title); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Distance','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('location_dropoff_disable_service_type_1['.$bookingFormIndex.'][]'); ?>">
													<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['location_dropoff_disable_service_type_1'][$bookingFormIndex],-1); ?>><?php esc_html_e('- All enabled -','chauffeur-booking-system'); ?></option>
<?php
				foreach($this->data['dictionary']['location'] as $locationIndex=>$locationData)
				{
					echo '<option value="'.esc_attr($locationIndex).'" '.(CHBSHelper::selectedIf($this->data['meta']['location_dropoff_disable_service_type_1'][$bookingFormIndex],$locationIndex,false)).'>'.esc_html($locationData['post']->post_title).'</option>';
				}
?>
												</select>		   
											</div>
										</td>
									</tr> 
									<tr>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Hourly','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('location_dropoff_disable_service_type_2['.$bookingFormIndex.'][]'); ?>">
													<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['location_dropoff_disable_service_type_2'][$bookingFormIndex],-1); ?>><?php esc_html_e('- All enabled -','chauffeur-booking-system'); ?></option>
<?php
				foreach($this->data['dictionary']['location'] as $locationIndex=>$locationData)
				{
					echo '<option value="'.esc_attr($locationIndex).'" '.(CHBSHelper::selectedIf($this->data['meta']['location_dropoff_disable_service_type_2'][$bookingFormIndex],$locationIndex,false)).'>'.esc_html($locationData['post']->post_title).'</option>';
				}
?>
												</select>		   
											</div>	   
										</td>
									</tr>								   
<?php
			}
?>
								</table>
							 </div>
<?php
		}
?>
						</li>  
					</ul>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			
			jQuery(document).ready(function($)
			{	
				$('.to').themeOptionElement({init:true});
				
				/***/
				
				var helper=new CHBSHelper();
				helper.getMessageFromConsole();
				
				/***/
				
				toCreateAutocomplete('input[name="<?php CHBSHelper::getFormName('location_name'); ?>"]');
			});
			
		</script>