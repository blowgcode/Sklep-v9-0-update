<?php 
		echo $this->data['nonce'];
		
		$Date=new CHBSDate();
		$Length=new CHBSLength();
?>	
		<div class="to">
			<div class="ui-tabs">
				<ul>
					<li><a href="#meta-box-price-rule-1"><?php esc_html_e('General','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-price-rule-2"><?php esc_html_e('Conditions','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-price-rule-3"><?php esc_html_e('Prices','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-price-rule-4"><?php esc_html_e('Options','chauffeur-booking-system'); ?></a></li>
				</ul>
				<div id="meta-box-price-rule-1">
					<ul class="to-form-field-list">
						<?php echo CHBSHelper::createPostIdField(__('Pricing rule ID','chauffeur-booking-system')); ?>
					</ul>
				</div>
				<div id="meta-box-price-rule-2">
					<div class="ui-tabs">
						<ul>
							<li><a href="#meta-box-price-rule-2-1"><?php esc_html_e('General','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-price-rule-2-2"><?php esc_html_e('Vehicles','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-price-rule-2-3"><?php esc_html_e('Locations','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-price-rule-2-4"><?php esc_html_e('Date','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-price-rule-2-5"><?php esc_html_e('Time','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-price-rule-2-6"><?php esc_html_e('Week','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-price-rule-2-7"><?php esc_html_e('Distance','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-price-rule-2-8"><?php esc_html_e('Duration','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-price-rule-2-9"><?php esc_html_e('Passengers','chauffeur-booking-system'); ?></a></li>
						</ul>	
						<div id="meta-box-price-rule-2-1">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Service types','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Select service type(s).','chauffeur-booking-system'); ?></span>
									<div class="to-checkbox-button">
										<input type="checkbox" value="-1" id="<?php CHBSHelper::getFormName('service_type_id_0'); ?>" name="<?php CHBSHelper::getFormName('service_type_id[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['service_type_id'],-1); ?>/>
										<label for="<?php CHBSHelper::getFormName('service_type_id_0'); ?>"><?php esc_html_e('- None -','chauffeur-booking-system') ?></label>
<?php
		foreach($this->data['dictionary']['service_type'] as $index=>$value)
		{
?>
										<input type="checkbox" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('service_type_id_'.$index); ?>" name="<?php CHBSHelper::getFormName('service_type_id[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['service_type_id'],$index); ?>/>
										<label for="<?php CHBSHelper::getFormName('service_type_id_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>
									</div>
								</li>  
								<li>
									<h5><?php esc_html_e('Transfer types','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Select transfer type(s).','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('This option works only for a "Distance" and "Flat rate" service types.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-checkbox-button">
										<input type="checkbox" value="-1" id="<?php CHBSHelper::getFormName('transfer_type_id_0'); ?>" name="<?php CHBSHelper::getFormName('transfer_type_id[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['transfer_type_id'],-1); ?>/>
										<label for="<?php CHBSHelper::getFormName('transfer_type_id_0'); ?>"><?php esc_html_e('- None -','chauffeur-booking-system') ?></label>
<?php
		foreach($this->data['dictionary']['transfer_type'] as $index=>$value)
		{
?>
										<input type="checkbox" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('transfer_type_id_'.$index); ?>" name="<?php CHBSHelper::getFormName('transfer_type_id[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['transfer_type_id'],$index); ?>/>
										<label for="<?php CHBSHelper::getFormName('transfer_type_id_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>
									</div>
								</li> 
								<li>
									<h5><?php esc_html_e('Booking forms','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Select booking form(s).','chauffeur-booking-system'); ?></span>
									<div class="to-clear-fix">
										<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('booking_form_id[]'); ?>">
											<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['booking_form_id'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['booking_form'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['booking_form_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
										</select>  
									</div>
								</li>
								<li>
									<h5><?php esc_html_e('Routes','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Select route(s).','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('This option works only for the "Flat rate" service type.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('route_id[]'); ?>">
											<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['route_id'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['route'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['route_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
										</select>  
									</div>									
								</li>
							</ul>
						</div>
						<div id="meta-box-price-rule-2-2">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Vehicles','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Select vehicle(s).','chauffeur-booking-system'); ?></span>
									<div class="to-clear-fix">
										<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('vehicle_id[]'); ?>">
											<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['vehicle_id'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['vehicle'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['vehicle_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
										</select>  
									</div>
								</li>
								<li>
									<h5><?php esc_html_e('Vehicle companies','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Select vehicle companies.','chauffeur-booking-system'); ?></span>
									<div class="to-clear-fix">
										<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('vehicle_company_id[]'); ?>">
											<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['vehicle_company_id'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['vehicle_company'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['vehicle_company_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
										</select>  
									</div>									
								</li>
							</ul>
						</div>
						<div id="meta-box-price-rule-2-3">
							<div class="to-notice-small to-notice-small-error">
								<?php esc_html_e('These conditions work only for a "Distance" and "Hourly" service types.') ?><br/>
								<?php esc_html_e('Please use "Geofence", "ZIP codes" and "Country" options separately. Using them together may produce unexpected results.') ?>
							</div>
							<div class="ui-tabs">
								<ul>
									<li><a href="#meta-box-price-rule-2-3-1"><?php esc_html_e('Fixed','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-price-rule-2-3-2"><?php esc_html_e('Geofence','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-price-rule-2-3-3"><?php esc_html_e('ZIP Code','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-price-rule-2-3-4"><?php esc_html_e('Country','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-price-rule-2-3-5"><?php esc_html_e('Other','chauffeur-booking-system'); ?></a></li>
								</ul>	
								<div id="meta-box-price-rule-2-3-1">
									<div class="to-notice-small to-notice-small-error">
										<?php esc_html_e('These conditions work only if the fixed locations are enabled in the booking form.') ?>
									</div>									
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('Fixed pickup locations','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Select fixed pickup location(s).','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('location_fixed_pickup[]'); ?>">
													<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['location_fixed_pickup'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['location'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['location_fixed_pickup'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
												</select>  
											</div>
										</li>
										<li>
											<h5><?php esc_html_e('Fixed drop-off locations','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Select fixed drop-off location(s).','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('location_fixed_dropoff[]'); ?>">
													<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['location_fixed_dropoff'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['location'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['location_fixed_dropoff'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
												</select>  
											</div>
										</li> 
									</ul>
								</div>
								<div id="meta-box-price-rule-2-3-2">	
									<div class="to-notice-small to-notice-small-error">
										<?php esc_html_e('These conditions work only for the non-fixed locations.') ?>
									</div>	
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('Pickup geofence','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Select geofence areas of pickup location.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('location_geofence_pickup[]'); ?>">
													<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['location_geofence_pickup'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['geofence'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['location_geofence_pickup'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
												</select>  
											</div>
										</li>
										<li>
											<h5><?php esc_html_e('Drop-off geofence','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Select geofence areas of drop-off location.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('location_geofence_dropoff[]'); ?>">
													<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['location_geofence_dropoff'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['geofence'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['location_geofence_dropoff'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
												</select>  
											</div>
										</li>										
									</ul>									
								</div>
								<div id="meta-box-price-rule-2-3-3">	
									<div class="to-notice-small to-notice-small-error">
										<?php esc_html_e('These conditions work only for the non-fixed locations.') ?>
									</div>										
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('Pickup ZIP codes','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php echo __('Enter ZIP code(s) of pickup locations separated by semicolon.','chauffeur-booking-system'); ?></span>			   
											<div>
												<div>
													<input type="text" name="<?php CHBSHelper::getFormName('location_zip_code_pickup'); ?>" value="<?php echo esc_attr($this->data['meta']['location_zip_code_pickup']); ?>"/>
												</div>
											</div>							  
										</li> 						
										<li>
											<h5><?php esc_html_e('Drop-off ZIP codes','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php echo __('Enter ZIP code(s) of drop-off locations separated by semicolon.','chauffeur-booking-system'); ?></span>			   
											<div>
												<div>
													<input type="text" name="<?php CHBSHelper::getFormName('location_zip_code_dropoff'); ?>" value="<?php echo esc_attr($this->data['meta']['location_zip_code_dropoff']); ?>"/>
												</div>
											</div>							  
										</li>										
									</ul>									
								</div>								
								<div id="meta-box-price-rule-2-3-4">
									<div class="to-notice-small to-notice-small-error">
										<?php esc_html_e('These conditions work only for the non-fixed locations.') ?>
									</div>	
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('Pickup countries location','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Select pickup location countries.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('location_country_pickup[]'); ?>">
													<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['location_country_pickup'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['country'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['location_country_pickup'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>  
											</div>
										</li> 
										<li>
											<h5><?php esc_html_e('Drop-off countries location','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Select countries for drop-off location.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('location_country_dropoff[]'); ?>">
													<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['location_country_dropoff'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['country'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['location_country_dropoff'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>  
											</div>
										</li> 										
									</ul>									
								</div>	
								<div id="meta-box-price-rule-2-3-5">
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('Swap locations','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Swap pickup and drop-off locations in the location rules.','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('This allows the rule to work for both directions, e.g. from location A to B and from B to A.','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('Works for all types of locations in the "Conditions / Locations" tab.','chauffeur-booking-system'); ?>
											</span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('location_swap_enable_1'); ?>" name="<?php CHBSHelper::getFormName('location_swap_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['location_swap_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('location_swap_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('location_swap_enable_0'); ?>" name="<?php CHBSHelper::getFormName('location_swap_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['location_swap_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('location_swap_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>  
										</li>										
									</ul>
								</div>
							</div>
						</div>
						<div id="meta-box-price-rule-2-4">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Pickup dates','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Enter pickup dates.','chauffeur-booking-system'); ?></span>
									<div>
										<table class="to-table" id="to-table-pickup-date">
											<tr>
												<th style="width:40%">
													<div>
														<?php esc_html_e('From','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('From.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:40%">
													<div>
														<?php esc_html_e('To','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('To.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												</th>
												<th style="width:20%">
													<div>
														<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Remove this entry.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>											
											</tr>
											<tr class="to-hidden">
												<td>
													<div>
														<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('pickup_date[start][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('pickup_date[stop][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>
											</tr>						 
<?php
		if(isset($this->data['meta']['pickup_date']))
		{
			if(is_array($this->data['meta']['pickup_date']))
			{
				foreach($this->data['meta']['pickup_date'] as $index=>$value)
				{
?>
											<tr>
												<td>
													<div>
														<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('pickup_date[start][]'); ?>" value="<?php echo esc_attr($Date->formatDateToDisplay($value['start'])); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('pickup_date[stop][]'); ?>" value="<?php echo esc_attr($Date->formatDateToDisplay($value['stop'])); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>
											</tr>	 
<?php				  
				}
			}
		}
?>
										</table>
										<div> 
											<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
										</div>
									</div>
								</li>	
								<li>
									<h5><?php esc_html_e('Return dates','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Enter return dates. This feature works only if the "Return (new ride)" transfer type is selected by the customer.','chauffeur-booking-system'); ?></span>
									<div>
										<table class="to-table" id="to-table-return-date">
											<tr>
												<th style="width:40%">
													<div>
														<?php esc_html_e('From','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('From.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:40%">
													<div>
														<?php esc_html_e('To','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('To.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												</th>
												<th style="width:20%">
													<div>
														<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Remove this entry.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>											
											</tr>
											<tr class="to-hidden">
												<td>
													<div>
														<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('return_date[start][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('return_date[stop][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>
											</tr>						 
<?php
		if(isset($this->data['meta']['return_date']))
		{
			if(is_array($this->data['meta']['return_date']))
			{
				foreach($this->data['meta']['return_date'] as $index=>$value)
				{
?>
											<tr>
												<td>
													<div>
														<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('return_date[start][]'); ?>" value="<?php echo esc_attr($Date->formatDateToDisplay($value['start'])); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('return_date[stop][]'); ?>" value="<?php echo esc_attr($Date->formatDateToDisplay($value['stop'])); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>
											</tr>	 
<?php				  
				}
			}
		}
?>
										</table>
										<div> 
											<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
										</div>
									</div>
								</li>		
								<li>
									<h5><?php esc_html_e('Return/pickup date difference','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Difference (in full days, time ignored) between return and pickup dates.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Value 0 means both dates must be the same. Value 1 means return date is the next day after pickup.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Allowed integer values: 0 to 999999999. Leave empty to disable this option.','chauffeur-booking-system'); ?>
									</span>
									<div><input type="text" maxlength="9" name="<?php CHBSHelper::getFormName('pickup_return_date_difference'); ?>" value="<?php echo esc_attr($this->data['meta']['pickup_return_date_difference']); ?>"/></div>								  
								</li>									
							</ul>
						</div>			
						<div id="meta-box-price-rule-2-5">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Pickup hours','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Enter pickup hours.','chauffeur-booking-system'); ?></span>
									<div>
										<table class="to-table" id="to-table-pickup-time">
											<tr>
												<th style="width:40%">
													<div>
														<?php esc_html_e('From','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('From.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:40%">
													<div>
														<?php esc_html_e('To','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('To.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:20%">
													<div>
														<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Remove this entry.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>											
											</tr>
											<tr class="to-hidden">
												<td>
													<div>
														<input type="text" class="to-timepicker-custom" name="<?php CHBSHelper::getFormName('pickup_time[start][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" class="to-timepicker-custom" name="<?php CHBSHelper::getFormName('pickup_time[stop][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>
											</tr>  
<?php
		if(isset($this->data['meta']['pickup_time']))
		{
			if(is_array($this->data['meta']['pickup_time']))
			{
				foreach($this->data['meta']['pickup_time'] as $index=>$value)
				{
?>
											<tr>
												<td>
													<div>
														<input type="text" class="to-timepicker-custom" name="<?php CHBSHelper::getFormName('pickup_time[start][]'); ?>" value="<?php echo esc_attr($Date->formatTimeToDisplay($value['start'])); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" class="to-timepicker-custom" name="<?php CHBSHelper::getFormName('pickup_time[stop][]'); ?>" value="<?php echo esc_attr($Date->formatTimeToDisplay($value['stop'])); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>
											</tr>	 
<?php				  
				}
			}
		}
?>
										</table>
										<div> 
											<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
										</div>
									</div>
								</li>	
								<li>
									<h5><?php esc_html_e('Return hours','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Enter return hours. This feature works only if the "Return (new ride)" transfer type is selected by the customer.','chauffeur-booking-system'); ?></span>
									<div>
										<table class="to-table" id="to-table-return-time">
											<tr>
												<th style="width:40%">
													<div>
														<?php esc_html_e('From','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('From.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:40%">
													<div>
														<?php esc_html_e('To','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('To.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:20%">
													<div>
														<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Remove this entry.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>											
											</tr>
											<tr class="to-hidden">
												<td>
													<div>
														<input type="text" class="to-timepicker-custom" name="<?php CHBSHelper::getFormName('return_time[start][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" class="to-timepicker-custom" name="<?php CHBSHelper::getFormName('return_time[stop][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>
											</tr>  
<?php
		if(isset($this->data['meta']['return_time']))
		{
			if(is_array($this->data['meta']['return_time']))
			{
				foreach($this->data['meta']['return_time'] as $index=>$value)
				{
?>
											<tr>
												<td>
													<div>
														<input type="text" class="to-timepicker-custom" name="<?php CHBSHelper::getFormName('return_time[start][]'); ?>" value="<?php echo esc_attr($Date->formatTimeToDisplay($value['start'])); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" class="to-timepicker-custom" name="<?php CHBSHelper::getFormName('return_time[stop][]'); ?>" value="<?php echo esc_attr($Date->formatTimeToDisplay($value['stop'])); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>
											</tr>	 
<?php				  
				}
			}
		}
?>
										</table>
										<div> 
											<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
										</div>
									</div>
								</li>									
							</ul>	
						</div>
						<div id="meta-box-price-rule-2-6">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Days of the week','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Select pickup days of the week.','chauffeur-booking-system'); ?></span>
									<div class="to-checkbox-button">
										<input type="checkbox" value="-1" id="<?php CHBSHelper::getFormName('pickup_day_number_0'); ?>" name="<?php CHBSHelper::getFormName('pickup_day_number[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['pickup_day_number'],-1); ?>/>
										<label for="<?php CHBSHelper::getFormName('pickup_day_number_0'); ?>"><?php esc_html_e('- All days -','chauffeur-booking-system') ?></label>
<?php
		for($i=1;$i<=7;$i++)
		{
?>
										<input type="checkbox" value="<?php echo esc_attr($i); ?>" id="<?php CHBSHelper::getFormName('pickup_day_number_'.$i); ?>" name="<?php CHBSHelper::getFormName('pickup_day_number[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['pickup_day_number'],$i); ?>/>
										<label for="<?php CHBSHelper::getFormName('pickup_day_number_'.$i); ?>"><?php echo esc_html(date_i18n('l',strtotime('Sunday +'.$i.' days'))); ?></label>
<?php
		}
?>								
									</div>						
								</li>
							</ul>
						</div>
						<div id="meta-box-price-rule-2-7">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Distance','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enter ride distance (from - to) between customer pickup and drop-off location.','chauffeur-booking-system'); ?><br/>
										<?php echo sprintf(esc_html__('If the "Price source type" (from "Prices" tab) option is set to "Calculation based on distance" plugin uses prices defined in this table as prices "%s", "%s", "%s".','chauffeur-booking-system'),$Length->label(-1,3),$Length->label(-1,4),$Length->label(-1,5)); ?>
										<?php esc_html_e('Otherwise plugin checks whether distance is defined in this table and use prices from "Prices" tab.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('This condition works only for "Distance" and "Flat rate" service type only. Minimum step is set to 0.1.','chauffeur-booking-system'); ?>
									</span>
									<div>
										<table class="to-table" id="to-table-distance">
											<tr>
												<th style="width:20%">
													<div>
														<?php esc_html_e('From','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('From.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:20%">
													<div>
														<?php esc_html_e('To','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('To.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:20%">
													<div>
														<?php esc_html_e('Price alter type','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Price alter type.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>		
												<th style="width:20%">
													<div>
														<?php esc_html_e('Price','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php echo sprintf(esc_html__('Price (%s).','chauffeur-booking-system'),$Length->label(CHBSOption::getOption('length_unit'),8)); ?>
														</span>
													</div>
												</th>				
												<th style="width:20%">
													<div>
														<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Remove this entry.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>											
											</tr>
											<tr class="to-hidden">
												<td>
													<div class="to-clear-fix">
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('distance[start][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div class="to-clear-fix">
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('distance[stop][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div class="to-clear-fix">
														<select class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('distance[price_alter_type_id][]'); ?>" id="distance_price_alter_type_id">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf(2,$index,false)).'>'.esc_html($value[0]).'</option>';
		}
?>
														</select>												  
													</div>
												</td>
												<td>
													<div class="to-clear-fix">
														<input type="text" name="<?php CHBSHelper::getFormName('distance[price][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div class="to-clear-fix">
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>
											</tr>   
<?php
		if(isset($this->data['meta']['distance']))
		{
			if(is_array($this->data['meta']['distance']))
			{
				foreach($this->data['meta']['distance'] as $index=>$value)
				{
					if(CHBSOption::getOption('length_unit')==2)
					{
						$value['start']=round($Length->convertUnit($value['start'],1,2),1);
						$value['stop']=round($Length->convertUnit($value['stop'],1,2),1); 
					}
?>
											<tr>
												<td>
													<div class="to-clear-fix">
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('distance[start][]'); ?>" value="<?php echo esc_attr($value['start']); ?>"/>
													</div>									
												</td>
												<td>
													<div class="to-clear-fix">
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('distance[stop][]'); ?>" value="<?php echo esc_attr($value['stop']); ?>"/>
													</div>									
												</td>
												<td>
													<div class="to-clear-fix">
														<select name="<?php CHBSHelper::getFormName('distance[price_alter_type_id][]'); ?>" id="<?php CHBSHelper::getFormName('distance_price_alter_type_id_'.$index); ?>">
<?php
				foreach($this->data['dictionary']['alter_type'] as $alterTypeIndex=>$alterTypeValue)
				{
					echo '<option value="'.esc_attr($alterTypeIndex).'" '.(CHBSHelper::selectedIf($value['price_alter_type_id'],$alterTypeIndex,false)).'>'.esc_html($alterTypeValue[0]).'</option>';
				}
?>
														</select>												  
													</div>
												</td>
												<td>
													<div class="to-clear-fix">
														<input type="text" name="<?php CHBSHelper::getFormName('distance[price][]'); ?>" value="<?php echo esc_attr($value['price']); ?>"/>
													</div>									
												</td>
												<td>
													<div class="to-clear-fix">
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>										
											</tr>	 
<?php				  
				}
			}
		}
?>
										</table>
										<div> 
											<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
										</div>
									</div>
								</li>
								<li>
									<h5><?php esc_html_e('Distance between base and pickup location','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enter ride distance (from - to) between base and customer pickup location.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('If the "Price source type" (from "Prices" tab) option is set to "Calculation based on distance between base and pickup location" plugin uses prices defined in this table as "Delivery" price.','chauffeur-booking-system'); ?>
										<?php esc_html_e('Otherwise plugin checks whether distance is defined in this table and use prices from "Prices" tab.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('This condition work only if the base location is set up. Minimum step is set to 0.1.','chauffeur-booking-system'); ?><br/>
									</span>
									<div>
										<table class="to-table" id="to-table-distance-base-to-pickup">
											<tr>
												<th style="width:25%">
													<div>
														<?php esc_html_e('From','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('From.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:25%">
													<div>
														<?php esc_html_e('To','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('To.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:30%">
													<div>
														<?php esc_html_e('Price','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Price.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:20%">
													<div>
														<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Remove this entry.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>											
											</tr>
											<tr class="to-hidden">
												<td>
													<div>
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('distance_base_to_pickup[start][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('distance_base_to_pickup[stop][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" name="<?php CHBSHelper::getFormName('distance_base_to_pickup[price][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>
											</tr>   
<?php
		if(isset($this->data['meta']['distance_base_to_pickup']))
		{
			if(is_array($this->data['meta']['distance_base_to_pickup']))
			{
				foreach($this->data['meta']['distance_base_to_pickup'] as $index=>$value)
				{
					if(CHBSOption::getOption('length_unit')==2)
					{
						$value['start']=round($Length->convertUnit($value['start'],1,2),1);
						$value['stop']=round($Length->convertUnit($value['stop'],1,2),1); 
					}
?>
											<tr>
												<td>
													<div>
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('distance_base_to_pickup[start][]'); ?>" value="<?php echo esc_attr($value['start']); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('distance_base_to_pickup[stop][]'); ?>" value="<?php echo esc_attr($value['stop']); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" name="<?php CHBSHelper::getFormName('distance_base_to_pickup[price][]'); ?>" value="<?php echo esc_attr($value['price']); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>										
											</tr>	 
<?php				  
				}
			}
		}
?>
										</table>
										<div> 
											<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
										</div>
									</div>
								</li>
								<li>
									<h5><?php esc_html_e('Distance between drop-off and base location','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enter ride distance (from - to) between customer drop-off and base location.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('If the "Price source type" (from "Prices" tab) option is set to "Calculation based on distance between drop-off and base location" plugin uses prices defined in this table as "Delivery (return)" price.','chauffeur-booking-system'); ?>
										<?php esc_html_e('Otherwise plugin checks whether distance is defined in this table and use prices from "Prices" tab.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('This condition work only if the base location is set up. Minimum step is set to 0.1.','chauffeur-booking-system'); ?><br/>
									</span>
									<div>
										<table class="to-table" id="to-table-distance-drop-off-to-base">
											<tr>
												<th style="width:25%">
													<div>
														<?php esc_html_e('From','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('From.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:25%">
													<div>
														<?php esc_html_e('To','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('To.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:30%">
													<div>
														<?php esc_html_e('Price','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Price.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:20%">
													<div>
														<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Remove this entry.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>											
											</tr>
											<tr class="to-hidden">
												<td>
													<div>
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('distance_drop_off_to_base[start][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('distance_drop_off_to_base[stop][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" name="<?php CHBSHelper::getFormName('distance_drop_off_to_base[price][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>
											</tr>   
<?php
		if(isset($this->data['meta']['distance_drop_off_to_base']))
		{
			if(is_array($this->data['meta']['distance_drop_off_to_base']))
			{
				foreach($this->data['meta']['distance_drop_off_to_base'] as $index=>$value)
				{
					if(CHBSOption::getOption('length_unit')==2)
					{
						$value['start']=round($Length->convertUnit($value['start'],1,2),1);
						$value['stop']=round($Length->convertUnit($value['stop'],1,2),1); 
					}
?>
											<tr>
												<td>
													<div>
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('distance_drop_off_to_base[start][]'); ?>" value="<?php echo esc_attr($value['start']); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('distance_drop_off_to_base[stop][]'); ?>" value="<?php echo esc_attr($value['stop']); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" name="<?php CHBSHelper::getFormName('distance_drop_off_to_base[price][]'); ?>" value="<?php echo esc_attr($value['price']); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>										
											</tr>	 
<?php				  
				}
			}
		}
?>
										</table>
										<div> 
											<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
										</div>
									</div>
								</li>								
								
							</ul>
						</div>
						<div id="meta-box-price-rule-2-8">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Ride duration','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enter range of ride duration in format HHHH:MM.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('If the "Price source type" (from "Prices" tab) option is set to "Calculation based on duration" plugin uses prices defined in this table as prices "Per hour", "Per hour (return)", "Per hour (return, new ride)". Otherwise plugin checks whether duration is defined in this table and use prices from "Prices" tab.','chauffeur-booking-system'); ?>
									</span>
									<div>
										<table class="to-table" id="to-table-duration">
											<tr>
												<th style="width:25%">
													<div>
														<?php esc_html_e('From','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('From.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:25%">
													<div>
														<?php esc_html_e('To','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('To.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:30%">
													<div>
														<?php esc_html_e('Price','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Price.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>												
												<th style="width:20%">
													<div>
														<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Remove this entry.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>											
											</tr>
											<tr class="to-hidden">
												<td>
													<div>
														<input type="text" maxlength="7" name="<?php CHBSHelper::getFormName('duration[start][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" maxlength="7" name="<?php CHBSHelper::getFormName('duration[stop][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" name="<?php CHBSHelper::getFormName('duration[price][]'); ?>"/>
													</div>									
												</td>												
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>
											</tr>   
<?php
		if(isset($this->data['meta']['duration']))
		{
			if(is_array($this->data['meta']['duration']))
			{
				foreach($this->data['meta']['duration'] as $index=>$value)
				{
?>
											<tr>
												<td>
													<div>
														<input type="text" maxlength="7" name="<?php CHBSHelper::getFormName('duration[start][]'); ?>" value="<?php echo esc_attr(CHBSDate::fillTime($value['start'])); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" maxlength="7" name="<?php CHBSHelper::getFormName('duration[stop][]'); ?>" value="<?php echo esc_attr(CHBSDate::fillTime($value['stop'])); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" name="<?php CHBSHelper::getFormName('duration[price][]'); ?>" value="<?php echo esc_attr($value['price']); ?>"/>
													</div>									
												</td>												
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>										
											</tr>	 
<?php				  
				}
			}
		}
?>
										</table>
										<div> 
											<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
										</div>
									</div>
								</li>
							</ul>
						</div>
						<div id="meta-box-price-rule-2-9">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Passengers number','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enter passengers number.','chauffeur-booking-system'); ?><br/>
										<?php echo esc_html('This condition works only if the passengers mode is enabled in booking form for particular service types.','chauffeur-booking-system'); ?><br/>
									</span>
									<div>
										<table class="to-table" id="to-table-passenger">
											<tr>
												<th style="width:40%">
													<div>
														<?php esc_html_e('From','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('From.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:40%">
													<div>
														<?php esc_html_e('To','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('To.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:20%">
													<div>
														<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Remove this entry.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>											
											</tr>
											<tr class="to-hidden">
												<td>
													<div>
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('passenger[start][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('passenger[stop][]'); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>
											</tr>   
<?php
		if(isset($this->data['meta']['passenger']))
		{
			if(is_array($this->data['meta']['passenger']))
			{
				foreach($this->data['meta']['passenger'] as $index=>$value)
				{
?>
											<tr>
												<td>
													<div>
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('passenger[start][]'); ?>" value="<?php echo esc_attr($value['start']); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<input type="text" maxlength="12" name="<?php CHBSHelper::getFormName('passenger[stop][]'); ?>" value="<?php echo esc_attr($value['stop']); ?>"/>
													</div>									
												</td>
												<td>
													<div>
														<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
													</div>
												</td>										
											</tr>	 
<?php				  
				}
			}
		}
?>
										</table>
										<div> 
											<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
										</div>
									</div>
								</li>						
							</ul>
						</div>
					</div>		
				</div>
				<div id="meta-box-price-rule-3">
					<ul class="to-form-field-list">
						<li>
							<h5><?php esc_html_e('Price source type','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Selecting the "Set directly in the "Prices" tab" option means that the rule uses prices only from this tab.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('The "Calculation based on" options mean that the rule takes prices from the "Conditions/Distance" or "Conditions/Duration" tab and ignores prices from this tab.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('The "all ranges" option for distance means that the rule calculates the average price per kilometer (or mile) taking into account all "From"-"To" ranges from the "Conditions/Distance" tab.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('The "all ranges" option for duration means that the rule calculates the average price per hour taking into account all "From"-"To" ranges from the "Conditions/Duration" tab.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('The "exact range" option for distance means that the rule checks whether there is a matching range "From"-"To" from the "Conditions/Distance" tab and if found, applies the price per kilometer (or mile) from the "Price" field next to that range.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('The "exact range" option for duration means that the rule checks whether there is a matching range "From"-"To" from the "Conditions/Duration" tab and if found, applies the price per hour from the "Price" field next to that range.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-clear-fix">
								<select name="<?php CHBSHelper::getFormName('price_source_type[]'); ?>" multiple="multiple" class="to-dropkick-disable">
<?php
		foreach($this->data['dictionary']['price_source_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_source_type'],$index,false)).' title="'.esc_attr($value[0]).'">'.esc_html($value[0]).'</option>';
?>
								</select>
							</div>
						</li>	
						<li>
							<h5><?php esc_html_e('Booking sum type','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Select the type of booking sum.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('For the "Variable" option, the final price depends on factors such as distance, time, number of passengers, etc.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('For the "Fixed" option, the final price is independent of any factors.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-radio-button">
<?php
		foreach($this->data['dictionary']['price_type'] as $index=>$value)
		{
?>
								<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('price_type_'.$index); ?>" name="<?php CHBSHelper::getFormName('price_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['price_type'],$index); ?>/>
								<label for="<?php CHBSHelper::getFormName('price_type_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>
							</div>
						</li>  
<?php
		$class=array(1=>array('to-price-type-1'),2=>array('to-price-type-2'));
		array_push($class[$this->data['meta']['price_type']==1 ? 2 : 1],'to-state-disabled');
?>				  
						<li>
							<h5><?php esc_html_e('Prices','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Prices.','chauffeur-booking-system'); ?></span>
							<div>
								<table class="to-table to-table-price">
									<tr>
										<th style="width:20%">
											<div>
												<?php esc_html_e('Name','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Name.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:8%">
											<div>
												<?php esc_html_e('Type','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Type.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>										
										<th style="width:35%">
											<div>
												<?php esc_html_e('Description','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Description.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:17%">
											<div>
												<?php esc_html_e('Price alter','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Price alter type.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>										
										<th style="width:10%">
											<div>
												<?php esc_html_e('Value','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Value.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>										
										<th style="width:10%">
											<div>
												<?php esc_html_e('Tax','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Tax.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>										  
									</tr> 
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[2]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Fixed','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Fixed','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Fixed price for a ride.','chauffeur-booking-system'); ?>
											</div>
										</td>		  
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_fixed_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_fixed_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
		}
?>
												</select>												  
											</div>
										</td> 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_fixed_value'); ?>" id="<?php CHBSHelper::getFormName('price_fixed_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_fixed_value']); ?>"/>
											</div>
										</td>												
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_fixed_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_fixed_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_fixed_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_fixed_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										
									</tr>
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[2]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Fixed (return)','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Fixed','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Fixed price for a return ride.','chauffeur-booking-system'); ?>
											</div>
										</td>	
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_fixed_return_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_fixed_return_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
		}
?>
												</select>												  
											</div>
										</td> 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_fixed_return_value'); ?>" id="<?php CHBSHelper::getFormName('price_fixed_return_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_fixed_return_value']); ?>"/>
											</div>
										</td>	
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_fixed_return_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_fixed_return_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_fixed_return_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_fixed_return_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										
									</tr> 
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[2]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Fixed (return, new ride)','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Fixed','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Fixed price for a return, new ride.','chauffeur-booking-system'); ?>
											</div>
										</td>	 
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_fixed_return_new_ride_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_fixed_return_new_ride_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_fixed_return_new_ride_value'); ?>" id="<?php CHBSHelper::getFormName('price_fixed_return_new_ride_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_fixed_return_new_ride_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_fixed_return_new_ride_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_fixed_return_new_ride_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_fixed_return_new_ride_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_fixed_return_new_ride_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										
									</tr>									 
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Initial','chauffeur-booking-system'); ?>
											</div>
										</td>
										 <td>
											<div class="to-clear-fix">
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Fixed value which is added to the order sum.','chauffeur-booking-system'); ?>
											</div>
										</td>	
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_initial_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_initial_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_initial_value'); ?>" id="<?php CHBSHelper::getFormName('price_initial_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_initial_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_initial_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_initial_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_initial_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_initial_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										
									</tr>
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Initial (return)','chauffeur-booking-system'); ?>
											</div>
										</td>
										 <td>
											<div class="to-clear-fix">
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Fixed value which is added to the order sum in case of "Return" transfer type.','chauffeur-booking-system'); ?>
											</div>
										</td>	
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_initial_return_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_initial_return_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_initial_return_value'); ?>" id="<?php CHBSHelper::getFormName('price_initial_return_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_initial_return_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_initial_return_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_initial_return_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_initial_return_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_initial_return_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										
									</tr>	
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Initial (return, new ride)','chauffeur-booking-system'); ?>
											</div>
										</td>
										 <td>
											<div class="to-clear-fix">
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Fixed value which is added to the order sum in case of "Return (new ride)" transfer type.','chauffeur-booking-system'); ?>
											</div>
										</td>	
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_initial_return_new_ride_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_initial_return_new_ride_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_initial_return_new_ride_value'); ?>" id="<?php CHBSHelper::getFormName('price_initial_return_new_ride_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_initial_return_new_ride_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_initial_return_new_ride_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_initial_return_new_ride_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_initial_return_new_ride_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_initial_return_new_ride_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										
									</tr>
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Delivery','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php echo sprintf(esc_html__('%s of ride from base to customer pickup location.','chauffeur-booking-system'),$Length->label(-1,1)); ?>
											</div>
										</td>	  
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_delivery_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_delivery_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_delivery_value'); ?>" id="<?php CHBSHelper::getFormName('price_delivery_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_delivery_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_delivery_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_delivery_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_delivery_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_delivery_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										
									</tr>  
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Delivery (return)','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php echo sprintf(esc_html__('%s of ride from customer drop-off location to base.','chauffeur-booking-system'),$Length->label(-1,1)); ?>
											</div>
										</td> 
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_delivery_return_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_delivery_return_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_delivery_return_value'); ?>" id="<?php CHBSHelper::getFormName('price_delivery_return_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_delivery_return_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_delivery_return_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_delivery_return_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_delivery_return_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_delivery_return_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										
									</tr>											 
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php echo $Length->label(-1,3); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Price per distance.','chauffeur-booking-system'); ?>
											</div>
										</td>	   
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_distance_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_distance_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
		}
?>
												</select>												  
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_distance_value'); ?>" id="<?php CHBSHelper::getFormName('price_distance_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_distance_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_distance_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_distance_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_distance_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_distance_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										
									</tr> 
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php echo $Length->label(-1,3); ?><?php esc_html_e(' (return)'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Price per distance for return ride.','chauffeur-booking-system'); ?>
											</div>
										</td>   
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_distance_return_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_distance_return_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
		}
?>
												</select>												  
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_distance_return_value'); ?>" id="<?php CHBSHelper::getFormName('price_distance_return_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_distance_return_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_distance_return_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_distance_return_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_distance_return_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_distance_return_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										
									</tr>  
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php echo $Length->label(-1,3); ?><?php esc_html_e(' (return, new ride)'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Price per distance for return, new ride.','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_distance_return_new_ride_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_distance_return_new_ride_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										  
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_distance_return_new_ride_value'); ?>" id="<?php CHBSHelper::getFormName('price_distance_return_new_ride_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_distance_return_new_ride_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_distance_return_new_ride_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_distance_return_new_ride_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_distance_return_new_ride_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_distance_return_new_ride_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										
									</tr>  
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Per hour','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Price per hour.','chauffeur-booking-system'); ?>
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_hour_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_hour_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										  
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_hour_value'); ?>" id="<?php CHBSHelper::getFormName('price_hour_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_hour_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_hour_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_hour_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_hour_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_hour_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										
									</tr>
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Per hour (return)','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Price per hour for return ride.','chauffeur-booking-system'); ?>
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_hour_return_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_hour_return_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>												  
											</div>
										</td>										  
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_hour_return_value'); ?>" id="<?php CHBSHelper::getFormName('price_hour_return_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_hour_return_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_hour_return_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_hour_return_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_hour_return_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
		{
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_hour_return_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
		}
?>
												</select>												  
											</div>
										</td>										
									</tr>									
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Per hour (return, new ride)','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Price per hour for return, new ride.','chauffeur-booking-system'); ?>
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_hour_return_new_ride_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_hour_return_new_ride_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>												  
											</div>
										</td>										  
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_hour_return_new_ride_value'); ?>" id="<?php CHBSHelper::getFormName('price_hour_return_new_ride_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_hour_return_new_ride_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_hour_return_new_ride_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_hour_return_new_ride_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_hour_return_new_ride_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_hour_return_new_ride_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
												</select>												  
											</div>
										</td>										
									</tr>									
									<tr>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Per extra time (hour)','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Fixed','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Price per hour for extra time.','chauffeur-booking-system'); ?>
											</div>
										</td>	 
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_extra_time_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_extra_time_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>												  
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_extra_time_value'); ?>" id="<?php CHBSHelper::getFormName('price_extra_time_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_extra_time_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_extra_time_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_extra_time_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_extra_time_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_extra_time_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
												</select>												  
											</div>
										</td>										
									</tr>
									<tr>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Per waypoint','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Fixed','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Fixed value added for each waypoint in "Distance" service.','chauffeur-booking-system'); ?>
											</div>
										</td>	 
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_waypoint_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_waypoint_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>												  
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_waypoint_value'); ?>" id="<?php CHBSHelper::getFormName('price_waypoint_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_waypoint_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_waypoint_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_waypoint_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_waypoint_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_waypoint_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
												</select>												  
											</div>
										</td>										
									</tr>  
									<tr>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Per waypoint duration','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Fixed','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Price per minute of the waypoint duration.','chauffeur-booking-system'); ?>
											</div>
										</td>	 
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_waypoint_duration_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_waypoint_duration_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>												  
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_waypoint_duration_value'); ?>" id="<?php CHBSHelper::getFormName('price_waypoint_duration_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_waypoint_duration_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_waypoint_duration_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_waypoint_duration_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_waypoint_duration_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_waypoint_duration_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
												</select>												  
											</div>
										</td>										
									</tr>    
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Per adult','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Price per adult.','chauffeur-booking-system'); ?>
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_passenger_adult_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
					echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_passenger_adult_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>												  
											</div>
										</td>										 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_passenger_adult_value'); ?>" id="<?php CHBSHelper::getFormName('price_passenger_adult_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_passenger_adult_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_passenger_adult_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_passenger_adult_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_passenger_adult_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_passenger_adult_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
												</select>												  
											</div>
										</td>										
									</tr>
									<tr<?php echo CHBSHelper::createCSSClassAttribute($class[1]); ?>>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Per child','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Price per child.','chauffeur-booking-system'); ?>
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_passenger_children_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_passenger_children_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>												  
											</div>
										</td>										 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_passenger_children_value'); ?>" id="<?php CHBSHelper::getFormName('price_passenger_children_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_passenger_children_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_passenger_children_tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['price_passenger_children_tax_rate_id'],-1,false)).'>'.esc_html__('- Inherited -','chauffeur-booking-system').'</option>';
		echo '<option value="0" '.(CHBSHelper::selectedIf($this->data['meta']['price_passenger_children_tax_rate_id'],0,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_passenger_children_tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
												</select>												  
											</div>
										</td>										
									</tr>
									<tr>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('PayPal flat fee','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Fixed','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Flat fee added to the sum of booking once customer selects PayPal payment.','chauffeur-booking-system'); ?>
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_payment_paypal_fixed_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_payment_paypal_fixed_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>												  
											</div>
										</td>										 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_payment_paypal_fixed_value'); ?>" id="<?php CHBSHelper::getFormName('price_payment_paypal_fixed_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_payment_paypal_fixed_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix"></div>
										</td>										
									</tr>									
									<tr>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('PayPal percentage fee','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Fixed','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Percentage fee (calculate based on booking sum) added to the sum of booking once customer selects PayPal payment.','chauffeur-booking-system'); ?>
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_payment_paypal_percentage_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_payment_paypal_percentage_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>												  
											</div>
										</td>										 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_payment_paypal_percentage_value'); ?>" id="<?php CHBSHelper::getFormName('price_payment_paypal_percentage_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_payment_paypal_percentage_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix"></div>
										</td>										
									</tr>										
									<tr>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Stripe flat fee','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Fixed','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Flat fee added to the sum of booking once customer selects Stripe payment.','chauffeur-booking-system'); ?>
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_payment_stripe_fixed_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_payment_stripe_fixed_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>												  
											</div>
										</td>										 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_payment_stripe_fixed_value'); ?>" id="<?php CHBSHelper::getFormName('price_payment_stripe_fixed_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_payment_stripe_fixed_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix"></div>
										</td>										 
									</tr>									
									<tr>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Stripe percentage fee','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Fixed','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('Variable','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<?php _e('Percentage fee (calculate based on booking sum) added to the sum of booking once customer selects Stripe payment.','chauffeur-booking-system'); ?>
											</div>
										</td>  
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('price_payment_stripe_percentage_alter_type_id'); ?>">
<?php
		foreach($this->data['dictionary']['alter_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['price_payment_stripe_percentage_alter_type_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>												  
											</div>
										</td>										 
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-price-field-value" name="<?php CHBSHelper::getFormName('price_payment_stripe_percentage_value'); ?>" id="<?php CHBSHelper::getFormName('price_payment_stripe_percentage_value'); ?>" value="<?php echo esc_attr($this->data['meta']['price_payment_stripe_percentage_value']); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix"></div>
										</td>									   
									</tr>										
								</table>
							</div>
						</li>
					</ul>
				</div>
				<div id="meta-box-price-rule-4">
					<ul class="to-form-field-list">
						<li>
							<h5><?php esc_html_e('Next rule processing','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php echo __('Defines whether the plugin should stop pricing after this rule or continue processing the next rule based on order within the same rule level.','chauffeur-booking-system'); ?>
							</span>			   
							<div>
								<div class="to-radio-button">
									<input type="radio" value="1" id="<?php CHBSHelper::getFormName('process_next_rule_enable_1'); ?>" name="<?php CHBSHelper::getFormName('process_next_rule_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['process_next_rule_enable'],1); ?>/>
									<label for="<?php CHBSHelper::getFormName('process_next_rule_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
									<input type="radio" value="0" id="<?php CHBSHelper::getFormName('process_next_rule_enable_0'); ?>" name="<?php CHBSHelper::getFormName('process_next_rule_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['process_next_rule_enable'],0); ?>/>
									<label for="<?php CHBSHelper::getFormName('process_next_rule_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
								</div>  
							</div>							  
						</li>
						<li>
							<h5><?php esc_html_e('Rule level','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php echo __('Enter the rule level (integer from -9999 to 9999). If empty, the rule is treated as the highest level.','chauffeur-booking-system'); ?><br/>
                                <?php echo __('Rules are processed in descending order by level and order. When the level changes during rule processing, the "Next Rule Processing" setting is ignored and the next rule from the new level is applied.','chauffeur-booking-system'); ?>
							</span>			   
							<div>
								<div>
									<input type="text" maxlength="5" name="<?php CHBSHelper::getFormName('rule_level'); ?>" value="<?php echo esc_attr($this->data['meta']['rule_level']); ?>"/>
								</div>
							</div>								
						</li>
						<li>
							<h5><?php esc_html_e('Minimum order value','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php echo __('Define the minimum (net) order value.','chauffeur-booking-system'); ?><br/>
								<?php echo __('If the total is lower than the defined minimum, the plugin adds the difference to the initial fee.','chauffeur-booking-system'); ?><br/>
								<?php echo __('This option is available for variable prices only.','chauffeur-booking-system'); ?>
							</span>			   
							<div>
								<span class="to-legend-field"><?php echo __('Minimum order net value.','chauffeur-booking-system'); ?></span>
								<div>
									<input type="text" name="<?php CHBSHelper::getFormName('minimum_order_value'); ?>" value="<?php echo esc_attr($this->data['meta']['minimum_order_value']); ?>"/>
								</div>
							</div>	
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php echo __('Include (net) value of booking extras to the minimum order value.','chauffeur-booking-system'); ?></span>
								<div class="to-radio-button">
									<input type="radio" value="1" id="<?php CHBSHelper::getFormName('minimum_order_booking_extra_include_1'); ?>" name="<?php CHBSHelper::getFormName('minimum_order_booking_extra_include'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['minimum_order_booking_extra_include'],1); ?>/>
									<label for="<?php CHBSHelper::getFormName('minimum_order_booking_extra_include_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
									<input type="radio" value="0" id="<?php CHBSHelper::getFormName('minimum_order_booking_extra_include_0'); ?>" name="<?php CHBSHelper::getFormName('minimum_order_booking_extra_include'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['minimum_order_booking_extra_include'],0); ?>/>
									<label for="<?php CHBSHelper::getFormName('minimum_order_booking_extra_include_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
								</div>  
							</div>
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php echo __('Include (net) value of extra time to the minimum order value.','chauffeur-booking-system'); ?></span>
								<div class="to-radio-button">
									<input type="radio" value="1" id="<?php CHBSHelper::getFormName('minimum_order_extra_time_include_1'); ?>" name="<?php CHBSHelper::getFormName('minimum_order_extra_time_include'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['minimum_order_extra_time_include'],1); ?>/>
									<label for="<?php CHBSHelper::getFormName('minimum_order_extra_time_include_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
									<input type="radio" value="0" id="<?php CHBSHelper::getFormName('minimum_order_extra_time_include_0'); ?>" name="<?php CHBSHelper::getFormName('minimum_order_extra_time_include'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['minimum_order_extra_time_include'],0); ?>/>
									<label for="<?php CHBSHelper::getFormName('minimum_order_extra_time_include_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
								</div>  
							</div>							
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php echo __('Include (net) value of waypoint duration to the minimum order value.','chauffeur-booking-system'); ?></span>
								<div class="to-radio-button">
									<input type="radio" value="1" id="<?php CHBSHelper::getFormName('minimum_order_waypoint_duration_include_1'); ?>" name="<?php CHBSHelper::getFormName('minimum_order_waypoint_duration_include'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['minimum_order_waypoint_duration_include'],1); ?>/>
									<label for="<?php CHBSHelper::getFormName('minimum_order_waypoint_duration_include_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
									<input type="radio" value="0" id="<?php CHBSHelper::getFormName('minimum_order_waypoint_duration_include_0'); ?>" name="<?php CHBSHelper::getFormName('minimum_order_waypoint_duration_include'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['minimum_order_waypoint_duration_include'],0); ?>/>
									<label for="<?php CHBSHelper::getFormName('minimum_order_waypoint_duration_include_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
								</div>  
							</div>								
						</li>
						<li>
							<h5><?php esc_html_e('Custom vehicle selection','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php echo __('This option allows to customize "Select" button located on the vehicle list in second step of booking form.','chauffeur-booking-system'); ?>
							</span>		
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php echo __('Status.','chauffeur-booking-system'); ?></span>
								<div class="to-radio-button">
									<input type="radio" value="1" id="<?php CHBSHelper::getFormName('custom_vehicle_selection_enable_1'); ?>" name="<?php CHBSHelper::getFormName('custom_vehicle_selection_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['custom_vehicle_selection_enable'],1); ?>/>
									<label for="<?php CHBSHelper::getFormName('custom_vehicle_selection_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
									<input type="radio" value="0" id="<?php CHBSHelper::getFormName('custom_vehicle_selection_enable_0'); ?>" name="<?php CHBSHelper::getFormName('custom_vehicle_selection_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['custom_vehicle_selection_enable'],0); ?>/>
									<label for="<?php CHBSHelper::getFormName('custom_vehicle_selection_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
								</div>  
							</div>	
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('Button URL address.','chauffeur-booking-system'); ?></span>
								<div>
									<input type="text" name="<?php CHBSHelper::getFormName('custom_vehicle_selection_button_url_address'); ?>" value="<?php echo esc_attr($this->data['meta']['custom_vehicle_selection_button_url_address']); ?>"/>
								</div>
							</div>	
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('Button label.','chauffeur-booking-system'); ?></span>
								<div>
									<input type="text" name="<?php CHBSHelper::getFormName('custom_vehicle_selection_button_label'); ?>" value="<?php echo esc_attr($this->data['meta']['custom_vehicle_selection_button_label']); ?>"/>
								</div>
							</div>
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php echo __('URL address target.','chauffeur-booking-system'); ?></span>
								<div class="to-radio-button">
									<input type="radio" value="1" id="<?php CHBSHelper::getFormName('custom_vehicle_selection_button_url_target_1'); ?>" name="<?php CHBSHelper::getFormName('custom_vehicle_selection_button_url_target'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['custom_vehicle_selection_button_url_target'],1); ?>/>
									<label for="<?php CHBSHelper::getFormName('custom_vehicle_selection_button_url_target_1'); ?>"><?php esc_html_e('The same window','chauffeur-booking-system'); ?></label>
									<input type="radio" value="2" id="<?php CHBSHelper::getFormName('custom_vehicle_selection_button_url_target_2'); ?>" name="<?php CHBSHelper::getFormName('custom_vehicle_selection_button_url_target'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['custom_vehicle_selection_button_url_target'],2); ?>/>
									<label for="<?php CHBSHelper::getFormName('custom_vehicle_selection_button_url_target_2'); ?>"><?php esc_html_e('New window','chauffeur-booking-system'); ?></label>
								</div>  
							</div>	
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php echo __('Redirect directly after first step.','chauffeur-booking-system'); ?></span>
								<div class="to-radio-button">
									<input type="radio" value="1" id="<?php CHBSHelper::getFormName('custom_vehicle_selection_first_step_redirect_1'); ?>" name="<?php CHBSHelper::getFormName('custom_vehicle_selection_first_step_redirect'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['custom_vehicle_selection_first_step_redirect'],1); ?>/>
									<label for="<?php CHBSHelper::getFormName('custom_vehicle_selection_first_step_redirect_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
									<input type="radio" value="0" id="<?php CHBSHelper::getFormName('custom_vehicle_selection_first_step_redirect_0'); ?>" name="<?php CHBSHelper::getFormName('custom_vehicle_selection_first_step_redirect'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['custom_vehicle_selection_first_step_redirect'],0); ?>/>
									<label for="<?php CHBSHelper::getFormName('custom_vehicle_selection_first_step_redirect_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
								</div>  
							</div>	
							<div>
								<span class="to-legend-field"><?php echo __('Hide price.','chauffeur-booking-system'); ?></span>
								<div class="to-radio-button">
									<input type="radio" value="1" id="<?php CHBSHelper::getFormName('custom_vehicle_selection_hide_price_1'); ?>" name="<?php CHBSHelper::getFormName('custom_vehicle_selection_hide_price'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['custom_vehicle_selection_hide_price'],1); ?>/>
									<label for="<?php CHBSHelper::getFormName('custom_vehicle_selection_hide_price_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
									<input type="radio" value="0" id="<?php CHBSHelper::getFormName('custom_vehicle_selection_hide_price_0'); ?>" name="<?php CHBSHelper::getFormName('custom_vehicle_selection_hide_price'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['custom_vehicle_selection_hide_price'],0); ?>/>
									<label for="<?php CHBSHelper::getFormName('custom_vehicle_selection_hide_price_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
								</div>  
							</div>							  
						</li>							
					</ul>
				</div>	
			</div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{	
				/***/
				
				$('.to').themeOptionElement({init:true});
				
				/***/
				
				toPreventCheckbox($('input[name="<?php CHBSHelper::getFormName('service_type_id'); ?>[]"]'));
				toPreventCheckbox($('input[name="<?php CHBSHelper::getFormName('transfer_type_id'); ?>[]"]'));
				toPreventCheckbox($('input[name="<?php CHBSHelper::getFormName('pickup_day_number'); ?>[]"]'));
				
				/***/
				
				$('#to-table-pickup-date').table();
				$('#to-table-return-date').table();
				$('#to-table-pickup-time').table();
				$('#to-table-return-time').table();
				$('#to-table-distance').table();
				$('#to-table-distance-base-to-pickup').table();
				$('#to-table-distance-drop-off-to-base').table();
				$('#to-table-passenger').table();
				$('#to-table-duration').table();
				
				/***/
	
				toCreateCustomDateTimePicker();
				
				/***/
				
				toTogglePriceType('.to input[name="<?php CHBSHelper::getFormName('price_type'); ?>"]','.to .to-table-price');
				
				/***/
				
				$('.to .to-price-field-value').on('change',function(e) 
				{
					var alterTypeField=$(this).parents('td:first').prev('td:first').find('select');
					
					if(parseInt(alterTypeField.val(),10)===1)
					{
						alterTypeField.val(2);
						alterTypeField.trigger('change');
					}
				});
				
				/***/
				
				var sourceTypeOptionCheck=[['2','3'],['4','5'],['6','7'],['8','9']];
				var sourceTypeOptionPrevious=[];
				
				$('select[name="chbs_price_source_type[]"]').on('change',function()
				{
					var option=$(this).val();
		
					if((index=$.inArray('1',option))>=0)
					{
						if($.inArray(1,sourceTypeOptionPrevious)>=0) 
						{	
							delete option[index];
						}
						else
						{
							option=[1];
						}
					}
					else
					{
						for(var i in sourceTypeOptionCheck)
						{
							if(((index1=$.inArray(sourceTypeOptionCheck[i][0],option))>=0) && ((index2=$.inArray(sourceTypeOptionCheck[i][1],option))>=0))
							{
								if($.inArray(sourceTypeOptionCheck[i][0],sourceTypeOptionPrevious)>=0) delete option[index1];
								else delete option[index2];
							}
						}
					}
					
					$(this).val(option);
					
					sourceTypeOptionPrevious=option;
				});
				
				/***/
			});
		</script>