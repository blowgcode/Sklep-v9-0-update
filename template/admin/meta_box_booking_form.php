<?php 
		echo $this->data['nonce']; 
		
		global $post;
		
		$Date=new CHBSDate();
		$Length=new CHBSLength();
		$Validation=new CHBSValidation();
?>	
		<div class="to">
			<div class="ui-tabs">
				<ul>
					<li><a href="#meta-box-booking-form-1"><?php esc_html_e('General','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-booking-form-2"><?php esc_html_e('Availability','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-booking-form-3"><?php esc_html_e('Payments','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-booking-form-4"><?php esc_html_e('Driving zone','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-booking-form-5"><?php esc_html_e('Form elements','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-booking-form-6"><?php esc_html_e('Notifications','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-booking-form-7"><?php esc_html_e('Google Maps','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-booking-form-8"><?php esc_html_e('Google Calendar','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-booking-form-9"><?php esc_html_e('Styles','chauffeur-booking-system'); ?></a></li>
				</ul>
				<div id="meta-box-booking-form-1">
					<div class="ui-tabs">
						<ul>
							<li><a href="#meta-box-booking-form-1-1"><?php esc_html_e('Main','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-1-2"><?php esc_html_e('Shortcodes','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-1-3"><?php esc_html_e('Services & transfers','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-1-4"><?php esc_html_e('Locations','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-1-5"><?php esc_html_e('Routes','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-1-6"><?php esc_html_e('Passengers','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-1-7"><?php esc_html_e('Vehicles','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-1-8"><?php esc_html_e('Booking extras','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-1-9"><?php esc_html_e('Prices','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-1-10"><?php esc_html_e('WooCommerce','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-1-11"><?php esc_html_e('Look & feel','chauffeur-booking-system'); ?></a></li>
						</ul>	 
						<div id="meta-box-booking-form-1-1">
							<ul class="to-form-field-list">
								<?php echo CHBSHelper::createPostIdField(__('Booking form ID','chauffeur-booking-system')); ?>
								<li>
									<h5><?php esc_html_e('Booking sending period','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Set range (in days, hours or minutes) during which customer can send a booking.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('E.g. range 1-14 days means that customer can send a booking from tomorrow during next two weeks.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Allowed are integer values from range 0-9999. Empty values means that booking sending period is not limited.','chauffeur-booking-system'); ?><br/>
									</span>
									<div>
										<span class="to-legend-field"><?php esc_html_e('From (number of days/hours/minutes from now when booking becomes available).','chauffeur-booking-system'); ?></span>
										<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('booking_period_from'); ?>" value="<?php echo esc_attr($this->data['meta']['booking_period_from']); ?>"/>
									</div>   
									<div>
										<span class="to-legend-field"><?php esc_html_e('To (number of days/hours/minutes from now when booking is no longer allowed).','chauffeur-booking-system'); ?></span>
										<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('booking_period_to'); ?>" value="<?php echo esc_attr($this->data['meta']['booking_period_to']); ?>"/>
									</div>  
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('booking_period_type_1'); ?>" name="<?php CHBSHelper::getFormName('booking_period_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_period_type'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('booking_period_type_1'); ?>"><?php esc_html_e('Days','chauffeur-booking-system'); ?></label>
										<input type="radio" value="2" id="<?php CHBSHelper::getFormName('booking_period_type_2'); ?>" name="<?php CHBSHelper::getFormName('booking_period_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_period_type'],2); ?>/>
										<label for="<?php CHBSHelper::getFormName('booking_period_type_2'); ?>"><?php esc_html_e('Hours','chauffeur-booking-system'); ?></label>
										<input type="radio" value="3" id="<?php CHBSHelper::getFormName('booking_period_type_3'); ?>" name="<?php CHBSHelper::getFormName('booking_period_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_period_type'],3); ?>/>
										<label for="<?php CHBSHelper::getFormName('booking_period_type_3'); ?>"><?php esc_html_e('Minutes','chauffeur-booking-system'); ?></label>
									</div>							
								</li> 
								<li>
									<h5><?php esc_html_e('Bookings interval','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Set interval (in minutes) between bookings for the same vehicle.','chauffeur-booking-system'); ?></span>
									<div>
										<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('booking_vehicle_interval'); ?>" value="<?php echo esc_attr($this->data['meta']['booking_vehicle_interval']); ?>"/>
									</div>   
								</li>	
								<li>
									<h5><?php esc_html_e('Default booking status','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Default status of a new booking.','chauffeur-booking-system'); ?></span>
									<div class="to-radio-button">
<?php
		foreach($this->data['dictionary']['booking_status'] as $index=>$value)
		{
?>
										<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('booking_status_default_id_'.$index); ?>" name="<?php CHBSHelper::getFormName('booking_status_default_id'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_status_default_id'],$index); ?>/>
										<label for="<?php CHBSHelper::getFormName('booking_status_default_id_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>								
									</div>
								</li>
								<li>
									<h5><?php esc_html_e('Booking title','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Booking title. Use "%s" to enter booking ID.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										<input type="text" name="<?php CHBSHelper::getFormName('booking_title'); ?>" id="<?php CHBSHelper::getFormName('booking_title'); ?>" value="<?php echo esc_attr($this->data['meta']['booking_title']); ?>"/>
									</div> 
								</li>   
								<li>
									<h5><?php esc_html_e('Booking form page ID','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enter ID of the page/post where this booking form is placed.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Required to enable booking editing.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										<input type="text" name="<?php CHBSHelper::getFormName('booking_form_post_id'); ?>" id="<?php CHBSHelper::getFormName('booking_form_post_id'); ?>" value="<?php echo esc_attr($this->data['meta']['booking_form_post_id']); ?>"/>
									</div> 
								</li> 								
								<li>
									<h5><?php esc_html_e('Minimum/maximum distance','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php echo sprintf(esc_html__('Minimum/maximum distance (in kilometers) required to make a booking.','chauffeur-booking-system'),(CHBSOption::getOption('length_unit')==2) ? esc_html__('in miles','chauffeur-booking-system') : esc_html__('in kilometers','chauffeur-booking-system')); ?><br/>
										<?php esc_html_e('Use integer values from 0 to 99999. Available for "Distance" service type only.','chauffeur-booking-system'); ?>
									</span>
<?php
		$distanceMinimum=$this->data['meta']['distance_minimum'];
		if(CHBSOption::getOption('length_unit')==2) $distanceMinimum=round($Length->convertUnit($distanceMinimum,1,2),1);
		$distanceMaximum=$this->data['meta']['distance_maximum'];
		if(CHBSOption::getOption('length_unit')==2) $distanceMaximum=round($Length->convertUnit($distanceMaximum,1,2),1);
?>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Minimum.','chauffeur-booking-system'); ?></span>
										<input type="text" maxlength="5" name="<?php CHBSHelper::getFormName('distance_minimum'); ?>" value="<?php echo esc_attr($distanceMinimum); ?>"/>								  
									</div>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Maximum.','chauffeur-booking-system'); ?></span>
										<input type="text" maxlength="5" name="<?php CHBSHelper::getFormName('distance_maximum'); ?>" value="<?php echo esc_attr($distanceMaximum); ?>"/>								  
									</div>									
								</li>
								<li>
									<h5><?php esc_html_e('Minimum/maximum duration','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Minimum/maximum duration (in minutes) required to make a booking.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Use integer values from 0 to 999999999. Available for "Distance" and "Hourly" service types only.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Minimum.','chauffeur-booking-system'); ?></span>
										<input type="text" maxlength="9" name="<?php CHBSHelper::getFormName('duration_minimum'); ?>" value="<?php echo esc_attr($this->data['meta']['duration_minimum']); ?>"/>						  
									</div>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Maximum.','chauffeur-booking-system'); ?></span>
										<input type="text" maxlength="9" name="<?php CHBSHelper::getFormName('duration_maximum'); ?>" value="<?php echo esc_attr($this->data['meta']['duration_maximum']); ?>"/>							  
									</div>								
								</li>								   
								<li>
									<h5><?php esc_html_e('Minimum/maximum booking value','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Specify minimum/maximum gross value of the booking.','chauffeur-booking-system'); ?>
									</span>
									<div>
										<span class="to-legend-field"><?php esc_html_e('Minimum.','chauffeur-booking-system'); ?></span>
										<input type="text" name="<?php CHBSHelper::getFormName('order_value_minimum'); ?>" value="<?php echo esc_attr($this->data['meta']['order_value_minimum']); ?>"/>
									</div>		
									<div>
										<span class="to-legend-field"><?php esc_html_e('Maximum.','chauffeur-booking-system'); ?></span>
										<input type="text" name="<?php CHBSHelper::getFormName('order_value_maximum'); ?>" value="<?php echo esc_attr($this->data['meta']['order_value_maximum']); ?>"/>
									</div>											
								</li>						 
								<li>
									<h5><?php esc_html_e('Default driver','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Driver assigned to the new bookings.','chauffeur-booking-system'); ?></span>
									<div class="to-radio-button">
										<input type="radio" value="-1" id="<?php CHBSHelper::getFormName('driver_default_id_0'); ?>" name="<?php CHBSHelper::getFormName('driver_default_id'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['driver_default_id'],-1); ?>/>
										<label for="<?php CHBSHelper::getFormName('driver_default_id_0'); ?>"><?php esc_html_e('- None - ','chauffeur-booking-system'); ?></label>							
<?php
		foreach($this->data['dictionary']['driver'] as $index=>$value)
		{
?>
										<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('driver_default_id_'.$index); ?>" name="<?php CHBSHelper::getFormName('driver_default_id'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['driver_default_id'],$index); ?>/>
										<label for="<?php CHBSHelper::getFormName('driver_default_id_'.$index); ?>"><?php echo esc_html($value['post']->post_title); ?></label>
<?php		
		}
?>								
									</div>
								</li>  
								<li>
									<h5><?php esc_html_e('Default country','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Select default country for step #3 ("Billing details") of booking form.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('If "Based on customer geolocation" is enabled, country will be set by customer IP.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										<select name="<?php CHBSHelper::getFormName('country_default'); ?>" id="<?php CHBSHelper::getFormName('country_default'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['country_default'],-1,false)).'>'.esc_html__('- Based on customer geolocation -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['country'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['country_default'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
										</select>												  
									</div>
								</li> 		
								<li>
									<h5><?php esc_html_e('States','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('List of available states separated by semicolon which will be available to choose in billing details.','chauffeur-booking-system'); ?>
									</span>
									<div><input type="text"  name="<?php CHBSHelper::getFormName('billing_detail_list_state'); ?>" value="<?php echo esc_attr($this->data['meta']['billing_detail_list_state']); ?>"/></div>								  
								</li>									

								<li>
									<h5><?php esc_html_e('Server side geolocation','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable server side geolocation.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('You can change settings of geolocation server in "Plugin Options".','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('geolocation_server_side_enable_1'); ?>" name="<?php CHBSHelper::getFormName('geolocation_server_side_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['geolocation_server_side_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('geolocation_server_side_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('geolocation_server_side_enable_0'); ?>" name="<?php CHBSHelper::getFormName('geolocation_server_side_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['geolocation_server_side_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('geolocation_server_side_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li> 
								<li>
									<h5><?php esc_html_e('Ride time multiplier','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enter value (multiplier) for ride time.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Allowed are float numbers from range 0-99.99.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										 <input maxlength="5" type="text" name="<?php CHBSHelper::getFormName('ride_time_multiplier'); ?>" value="<?php echo esc_attr($this->data['meta']['ride_time_multiplier']); ?>"/>
								   </div>								  
								</li>
								<li>
									<h5><?php esc_html_e('Ride time rounding','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enter value (rounding) for ride time.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Allowed are integer numbers from range 1-60.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										 <input maxlength="2" type="text" name="<?php CHBSHelper::getFormName('ride_time_rounding'); ?>" value="<?php echo esc_attr($this->data['meta']['ride_time_rounding']); ?>"/>
								   </div>								  
								</li>
	
								<li>
									<h5><?php esc_html_e('Recaptcha','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable reCAPTCHA v3 feature in booking form.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('This option requires additional settings in the "Plugin Options".','chauffeur-booking-system'); ?><br/>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_recaptcha_enable_1'); ?>" name="<?php CHBSHelper::getFormName('google_recaptcha_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_recaptcha_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('google_recaptcha_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('google_recaptcha_enable_0'); ?>" name="<?php CHBSHelper::getFormName('google_recaptcha_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_recaptcha_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('google_recaptcha_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li>								
							</ul>					
						</div>		
						<div id="meta-box-booking-form-1-2">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Booking form','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Copy and paste the shortcode on a page.','chauffeur-booking-system'); ?></span>
									<div class="to-field-disabled to-field-disabled-full-width">
<?php 
		$shortcode=CHBSShortcode::create(array('booking_form_id'=>$post->ID)); 
		echo $shortcode;
?>
										<a href="#" class="to-copy-to-clipboard to-float-right" data-clipboard-text="<?php echo esc_attr($shortcode); ?>" data-label-on-success="<?php esc_attr_e('Copied!','chauffeur-booking-system') ?>"><?php esc_html_e('Copy','chauffeur-booking-system'); ?></a>
									</div>
								</li>
								<li>
									<h5><?php esc_html_e('Widget','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Copy and paste the shortcode on a page.','chauffeur-booking-system'); ?></span>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Style 1 (vertical, selected fields).','chauffeur-booking-system'); ?></span>
										<div class="to-field-disabled to-field-disabled-full-width">
<?php 
		$shortcode=CHBSShortcode::create(array('booking_form_id'=>$post->ID,'widget_mode'=>1,'widget_style'=>1,'widget_booking_form_url'=>null)); 
		echo $shortcode;
?>
											<a href="#" class="to-copy-to-clipboard to-float-right" data-clipboard-text="<?php echo esc_attr($shortcode); ?>" data-label-on-success="<?php esc_attr_e('Copied!','chauffeur-booking-system') ?>"><?php esc_html_e('Copy','chauffeur-booking-system'); ?></a>
										</div>
									</div>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Style 2 (horizontal, single line, selected fields).','chauffeur-booking-system'); ?></span>
										<div class="to-field-disabled to-field-disabled-full-width">
<?php 
		$shortcode=CHBSShortcode::create(array('booking_form_id'=>$post->ID,'widget_mode'=>1,'widget_style'=>2,'widget_booking_form_url'=>null)); 
		echo $shortcode;
?>
											<a href="#" class="to-copy-to-clipboard to-float-right" data-clipboard-text="<?php echo esc_attr($shortcode); ?>" data-label-on-success="<?php esc_attr_e('Copied!','chauffeur-booking-system') ?>"><?php esc_html_e('Copy','chauffeur-booking-system'); ?></a>
										</div>
									</div>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Style 3 (horizontal, multiple lines, all fields).','chauffeur-booking-system'); ?></span>
										<div class="to-field-disabled to-field-disabled-full-width">
<?php 
		$shortcode=CHBSShortcode::create(array('booking_form_id'=>$post->ID,'widget_mode'=>1,'widget_style'=>3,'widget_booking_form_url'=>null)); 
		echo $shortcode;
?>
											<a href="#" class="to-copy-to-clipboard to-float-right" data-clipboard-text="<?php echo esc_attr($shortcode); ?>" data-label-on-success="<?php esc_attr_e('Copied!','chauffeur-booking-system') ?>"><?php esc_html_e('Copy','chauffeur-booking-system'); ?></a>
										</div>
									</div>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Style 4 (horizontal, single line, selected fields).','chauffeur-booking-system'); ?></span>
										<div class="to-field-disabled to-field-disabled-full-width">
<?php 
		$shortcode=CHBSShortcode::create(array('booking_form_id'=>$post->ID,'widget_mode'=>1,'widget_style'=>4,'widget_booking_form_url'=>null)); 
		echo $shortcode;
?>
											<a href="#" class="to-copy-to-clipboard to-float-right" data-clipboard-text="<?php echo esc_attr($shortcode); ?>" data-label-on-success="<?php esc_attr_e('Copied!','chauffeur-booking-system') ?>"><?php esc_html_e('Copy','chauffeur-booking-system'); ?></a>
										</div>
									</div>									
								</li>
							</ul>
						</div>
						<div id="meta-box-booking-form-1-3">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Service type offered','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Select at least one available type of service: distance (point-to-point), hourly, flat-rate for defined routes.','chauffeur-booking-system'); ?></span>
									<div class="to-checkbox-button">
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
									<h5><?php esc_html_e('Default service type','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Select default service type. It will be preselected on the booking form.','chauffeur-booking-system'); ?></span>
									<div class="to-radio-button">
<?php
		foreach($this->data['dictionary']['service_type'] as $index=>$value)
		{
?>
										<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('service_type_id_default_'.$index); ?>" name="<?php CHBSHelper::getFormName('service_type_id_default'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['service_type_id_default'],$index); ?>/>
										<label for="<?php CHBSHelper::getFormName('service_type_id_default_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>
									</div>
								</li>						 
								<li>
									<h5><?php esc_html_e('Transfer type','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Enable or disable transfer types (one way, return, return as new ride) for selected services.','chauffeur-booking-system'); ?></span>
									<div>
										<table class="to-table">
											<tr>
												<th style="width:30%">
													<div>
														<?php esc_html_e('Service','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Service type offered.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:70%">
													<div>
														<?php esc_html_e('Transfer type','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Transfer type','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
											</tr>
											<tr>
												<td>
													<div><?php esc_html_e('Distance','chauffeur-booking-system'); ?></div>
												</td>
												<td>
													<div class="to-clear-fix">
														<div class="to-checkbox-button">
															<input type="checkbox" value="1" id="<?php CHBSHelper::getFormName('transfer_type_enable_1_1'); ?>" name="<?php CHBSHelper::getFormName('transfer_type_enable_1[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['transfer_type_enable_1'],1); ?>/>
															<label for="<?php CHBSHelper::getFormName('transfer_type_enable_1_1'); ?>"><?php esc_html_e('One way','chauffeur-booking-system'); ?></label>
															<input type="checkbox" value="2" id="<?php CHBSHelper::getFormName('transfer_type_enable_1_2'); ?>" name="<?php CHBSHelper::getFormName('transfer_type_enable_1[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['transfer_type_enable_1'],2); ?>/>
															<label for="<?php CHBSHelper::getFormName('transfer_type_enable_1_2'); ?>"><?php esc_html_e('Return','chauffeur-booking-system'); ?></label>
															<input type="checkbox" value="3" id="<?php CHBSHelper::getFormName('transfer_type_enable_1_3'); ?>" name="<?php CHBSHelper::getFormName('transfer_type_enable_1[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['transfer_type_enable_1'],3); ?>/>
															<label for="<?php CHBSHelper::getFormName('transfer_type_enable_1_3'); ?>"><?php esc_html_e('Return (new ride)','chauffeur-booking-system'); ?></label>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<td>
													<div><?php esc_html_e('Flat rate','chauffeur-booking-system'); ?></div>
												</td>
												<td>
													<div class="to-clear-fix">
														<div class="to-checkbox-button">
															<input type="checkbox" value="1" id="<?php CHBSHelper::getFormName('transfer_type_enable_3_1'); ?>" name="<?php CHBSHelper::getFormName('transfer_type_enable_3[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['transfer_type_enable_3'],1); ?>/>
															<label for="<?php CHBSHelper::getFormName('transfer_type_enable_3_1'); ?>"><?php esc_html_e('One way','chauffeur-booking-system'); ?></label>
															<input type="checkbox" value="2" id="<?php CHBSHelper::getFormName('transfer_type_enable_3_2'); ?>" name="<?php CHBSHelper::getFormName('transfer_type_enable_3[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['transfer_type_enable_3'],2); ?>/>
															<label for="<?php CHBSHelper::getFormName('transfer_type_enable_3_2'); ?>"><?php esc_html_e('Return','chauffeur-booking-system'); ?></label>
															<input type="checkbox" value="3" id="<?php CHBSHelper::getFormName('transfer_type_enable_3_3'); ?>" name="<?php CHBSHelper::getFormName('transfer_type_enable_3[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['transfer_type_enable_3'],3); ?>/>
															<label for="<?php CHBSHelper::getFormName('transfer_type_enable_3_3'); ?>"><?php esc_html_e('Return (new ride)','chauffeur-booking-system'); ?></label>
														 </div>
													</div>
												</td>
											</tr>									
										</table>
									</div>
								</li> 
								<li>
									<h5><?php esc_html_e('Transfer type label','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable empty (not selected) item on transfer type list.','chauffeur-booking-system'); ?><br/>
									</span>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('transfer_type_list_item_empty_enable_1'); ?>" name="<?php CHBSHelper::getFormName('transfer_type_list_item_empty_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['transfer_type_list_item_empty_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('transfer_type_list_item_empty_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('transfer_type_list_item_empty_enable_0'); ?>" name="<?php CHBSHelper::getFormName('transfer_type_list_item_empty_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['transfer_type_list_item_empty_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('transfer_type_list_item_empty_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>	
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Label.','chauffeur-booking-system'); ?></span>
										<input type="text" name="<?php CHBSHelper::getFormName('transfer_type_list_item_empty_text'); ?>" value="<?php echo esc_attr($this->data['meta']['transfer_type_list_item_empty_text']); ?>"/>
									</div>									
								</li>								
							</ul>
						</div>
						<div id="meta-box-booking-form-1-4">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Base location','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Company base location. Available for "Distance" and "Hourly" service types only.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('If set, plugin can calculate delivery fee (base to pickup) and delivery return fee (drop-off to base).','chauffeur-booking-system'); ?>
									</span>
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('base_location'); ?>" value="<?php echo esc_attr($this->data['meta']['base_location']); ?>"/>
										<input type="hidden" name="<?php CHBSHelper::getFormName('base_location_coordinate_lat'); ?>" value="<?php echo esc_attr($this->data['meta']['base_location_coordinate_lat']); ?>"/>
										<input type="hidden" name="<?php CHBSHelper::getFormName('base_location_coordinate_lng'); ?>" value="<?php echo esc_attr($this->data['meta']['base_location_coordinate_lng']); ?>"/>
									</div>								  
								</li>												 
								<li>
									<h5><?php esc_html_e('Fixed locations','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enter fixed pickup/drop-off location(s) for selected service(s).','chauffeur-booking-system'); ?><br/>
									</span>
									<div>
										<table class="to-table">
											<tr>
												<th style="width:20%">
													<div>
														<?php esc_html_e('Service','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Service type offered.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:40%">
													<div>
														<?php esc_html_e('Pickup location','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Pickup location.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:40%">
													<div>
														<?php esc_html_e('Drop-off location','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Drop-off location.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
											</tr>
											<tr>
												<td>
													<div><?php esc_html_e('Distance','chauffeur-booking-system'); ?></div>
												</td>
												<td>
													<div class="to-clear-fix">
														<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('location_fixed_pickup_service_type_1[]'); ?>">
															<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['location_fixed_pickup_service_type_1'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['location'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['location_fixed_pickup_service_type_1'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
														</select>												
													 </div>
												</td>
												<td>
													<div class="to-clear-fix">
														<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('location_fixed_dropoff_service_type_1[]'); ?>">
															<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['location_fixed_dropoff_service_type_1'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['location'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['location_fixed_dropoff_service_type_1'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
														</select>												
													 </div>
												</td>
											</tr>									
											<tr>
												<td>
													<div><?php esc_html_e('Hourly','chauffeur-booking-system'); ?></div>
												</td>
												<td>
													<div class="to-clear-fix">
														 <select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('location_fixed_pickup_service_type_2[]'); ?>">
															 <option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['location_fixed_pickup_service_type_2'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['location'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['location_fixed_pickup_service_type_2'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
														</select>
													</div>
												</td>
												<td>
													<div class="to-clear-fix">
														 <select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('location_fixed_dropoff_service_type_2[]'); ?>">
															 <option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['location_fixed_dropoff_service_type_2'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['location'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['location_fixed_dropoff_service_type_2'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
														</select>
													</div>
												</td>
											</tr>										
										</table>
									</div>
								</li>  
								<li>
									<h5><?php esc_html_e('Fixed locations label','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable empty (not selected) item on fixed location list. This option works only if the autocomplete feature is disabled.','chauffeur-booking-system'); ?><br/>
									</span>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('location_fixed_list_item_empty_enable_1'); ?>" name="<?php CHBSHelper::getFormName('location_fixed_list_item_empty_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['location_fixed_list_item_empty_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('location_fixed_list_item_empty_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('location_fixed_list_item_empty_enable_0'); ?>" name="<?php CHBSHelper::getFormName('location_fixed_list_item_empty_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['location_fixed_list_item_empty_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('location_fixed_list_item_empty_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Label.','chauffeur-booking-system'); ?></span>
										<input type="text" name="<?php CHBSHelper::getFormName('location_fixed_list_item_empty_text'); ?>" value="<?php echo esc_attr($this->data['meta']['location_fixed_list_item_empty_text']); ?>"/>
									</div>
								</li>	  
								<li>
									<h5><?php esc_html_e('Fixed locations autocomplete','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable autocomplete feature on fixed location lists.','chauffeur-booking-system'); ?><br/>
									</span>
									<div class="to-clear-fix">
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('location_fixed_autocomplete_enable_1'); ?>" name="<?php CHBSHelper::getFormName('location_fixed_autocomplete_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['location_fixed_autocomplete_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('location_fixed_autocomplete_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('location_fixed_autocomplete_enable_0'); ?>" name="<?php CHBSHelper::getFormName('location_fixed_autocomplete_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['location_fixed_autocomplete_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('location_fixed_autocomplete_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>
								</li>	
								<li>
									<h5><?php esc_html_e('Waypoints','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Allow customer to add waypoints.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Works only in "Distance" mode without fixed pickup/drop-off locations.','chauffeur-booking-system'); ?><br/>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('waypoint_enable_1'); ?>" name="<?php CHBSHelper::getFormName('waypoint_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['waypoint_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('waypoint_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('waypoint_enable_0'); ?>" name="<?php CHBSHelper::getFormName('waypoint_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['waypoint_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('waypoint_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li> 
								
							</ul>
						</div>						
						<div id="meta-box-booking-form-1-5">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Routes','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Select routes that are available to book.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('This option is available for "Flat rate" service type only.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-checkbox-button">
										<input type="checkbox" value="-1" id="<?php CHBSHelper::getFormName('route_id_0'); ?>" name="<?php CHBSHelper::getFormName('route_id[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['route_id'],-1); ?>/>
										<label for="<?php CHBSHelper::getFormName('route_id_0'); ?>"><?php esc_html_e('- All routes -','chauffeur-booking-system') ?></label>
<?php
		foreach($this->data['dictionary']['route'] as $index=>$value)
		{
?>
										<input type="checkbox" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('route_id_'.$index); ?>" name="<?php CHBSHelper::getFormName('route_id[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['route_id'],$index); ?>/>
										<label for="<?php CHBSHelper::getFormName('route_id_'.$index); ?>"><?php echo esc_html($value['post']->post_title); ?></label>
<?php		
		}
?>
									</div>
								</li>  
								<li>
									<h5><?php esc_html_e('Routes label','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable empty (not selected) item on routes list. This option works only if the autocomplete feature is disabled.','chauffeur-booking-system'); ?><br/>
									</span>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('route_list_item_empty_enable_1'); ?>" name="<?php CHBSHelper::getFormName('route_list_item_empty_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['route_list_item_empty_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('route_list_item_empty_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('route_list_item_empty_enable_0'); ?>" name="<?php CHBSHelper::getFormName('route_list_item_empty_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['route_list_item_empty_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('route_list_item_empty_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>	
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Label.','chauffeur-booking-system'); ?></span>
										<input type="text" name="<?php CHBSHelper::getFormName('route_list_item_empty_text'); ?>" value="<?php echo esc_attr($this->data['meta']['route_list_item_empty_text']); ?>"/>
									</div>									
								</li>
								<li>
									<h5><?php esc_html_e('Route field autocomplete','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable autocomplete feature on route field.','chauffeur-booking-system'); ?><br/>
									</span>
									<div class="to-clear-fix">
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('route_list_autocomplete_enable_1'); ?>" name="<?php CHBSHelper::getFormName('route_list_autocomplete_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['route_list_autocomplete_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('route_list_autocomplete_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('route_list_autocomplete_enable_0'); ?>" name="<?php CHBSHelper::getFormName('route_list_autocomplete_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['route_list_autocomplete_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('route_list_autocomplete_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>
								</li>	
								<li>
									<h5><?php esc_html_e('Pickup location field','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable pickup location field.','chauffeur-booking-system'); ?><br/>
									</span>
									<div class="to-clear-fix">
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('route_pickup_location_field_enable_1'); ?>" name="<?php CHBSHelper::getFormName('route_pickup_location_field_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['route_pickup_location_field_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('route_pickup_location_field_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('route_pickup_location_field_enable_0'); ?>" name="<?php CHBSHelper::getFormName('route_pickup_location_field_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['route_pickup_location_field_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('route_pickup_location_field_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>
								</li>
								<li>
									<h5><?php esc_html_e('Drop-off location field','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable drop-off location field.','chauffeur-booking-system'); ?><br/>
									</span>
									<div class="to-clear-fix">
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('route_dropoff_location_field_enable_1'); ?>" name="<?php CHBSHelper::getFormName('route_dropoff_location_field_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['route_dropoff_location_field_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('route_dropoff_location_field_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('route_dropoff_location_field_enable_0'); ?>" name="<?php CHBSHelper::getFormName('route_dropoff_location_field_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['route_dropoff_location_field_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('route_dropoff_location_field_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>
								</li>
							</ul>
						</div>
						<div id="meta-box-booking-form-1-6">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Passengers','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable option to set number of passengers (adults, children) for selected service types.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Works correctly only with "Variable" price type. You must define price per adult and child.','chauffeur-booking-system'); ?><br/>
									</span>
									<div>
										<table class="to-table">
											<tr>
												<th style="width:20%">
													<div>
														<?php esc_html_e('Service','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Service type offered.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:40%">
													<div>
														<?php esc_html_e('Adults','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Adults.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:40%">
													<div>
														<?php esc_html_e('Children','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Children.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
											</tr>
											<tr>
												<td>
													<div><?php esc_html_e('Distance','chauffeur-booking-system'); ?></div>
												</td>
												<td>
													<div class="to-clear-fix">
														<div class="to-radio-button">
															<input type="radio" value="1" id="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_1_1'); ?>" name="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_1'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_adult_enable_service_type_1'],1); ?>/>
															<label for="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_1_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
															<input type="radio" value="0" id="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_1_0'); ?>" name="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_1'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_adult_enable_service_type_1'],0); ?>/>
															<label for="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_1_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
														</div>
													</div>
												</td>
												<td>
													<div class="to-clear-fix">
														<div class="to-radio-button">
															<input type="radio" value="1" id="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_1_1'); ?>" name="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_1'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_children_enable_service_type_1'],1); ?>/>
															<label for="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_1_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
															<input type="radio" value="0" id="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_1_0'); ?>" name="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_1'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_children_enable_service_type_1'],0); ?>/>
															<label for="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_1_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
														</div>
													</div>
												</td>
											</tr>									
											<tr>
												<td>
													<div><?php esc_html_e('Hourly','chauffeur-booking-system'); ?></div>
												</td>
												<td>
													<div class="to-clear-fix">
														<div class="to-radio-button">
															<input type="radio" value="1" id="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_2_1'); ?>" name="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_2'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_adult_enable_service_type_2'],1); ?>/>
															<label for="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_2_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
															<input type="radio" value="0" id="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_2_0'); ?>" name="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_2'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_adult_enable_service_type_2'],0); ?>/>
															<label for="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_2_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
														</div>
													</div>
												</td>
												<td>
													<div class="to-clear-fix">
														<div class="to-radio-button">
															<input type="radio" value="1" id="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_2_1'); ?>" name="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_2'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_children_enable_service_type_2'],1); ?>/>
															<label for="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_2_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
															<input type="radio" value="0" id="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_2_0'); ?>" name="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_2'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_children_enable_service_type_2'],0); ?>/>
															<label for="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_2_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
														</div>
													</div>
												</td>
											</tr>  
											<tr>
												<td>
													<div><?php esc_html_e('Flat rate','chauffeur-booking-system'); ?></div>
												</td>
												<td>
													<div class="to-clear-fix">
														<div class="to-radio-button">
															<input type="radio" value="1" id="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_3_1'); ?>" name="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_3'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_adult_enable_service_type_3'],1); ?>/>
															<label for="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_3_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
															<input type="radio" value="0" id="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_3_0'); ?>" name="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_3'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_adult_enable_service_type_3'],0); ?>/>
															<label for="<?php CHBSHelper::getFormName('passenger_adult_enable_service_type_3_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
														</div>
													</div>
												</td>
												<td>
													<div class="to-clear-fix">
														<div class="to-radio-button">
															<input type="radio" value="1" id="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_3_1'); ?>" name="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_3'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_children_enable_service_type_3'],1); ?>/>
															<label for="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_3_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
															<input type="radio" value="0" id="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_3_0'); ?>" name="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_3'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_children_enable_service_type_3'],0); ?>/>
															<label for="<?php CHBSHelper::getFormName('passenger_children_enable_service_type_3_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
														</div>
													</div>
												</td>
											</tr>  
										</table>
									</div>
								</li> 
								<li>
									<h5><?php esc_html_e('Default number of passengers','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enter default number of passengers, or leave empty.','chauffeur-booking-system'); ?>
									</span>
									<div>
										<span class="to-legend-field"><?php esc_html_e('Adults.','chauffeur-booking-system'); ?></span>
										<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('passenger_adult_default_number'); ?>" value="<?php echo esc_attr($this->data['meta']['passenger_adult_default_number']); ?>"/>
									</div> 
									<div>
										<span class="to-legend-field"><?php esc_html_e('Children.','chauffeur-booking-system'); ?></span>
										<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('passenger_children_default_number'); ?>" value="<?php echo esc_attr($this->data['meta']['passenger_children_default_number']); ?>"/>
									</div> 
								</li>						
								<li>
									<h5><?php esc_html_e('Show price per passengers','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Show price per single passenger next to vehicle in second step.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('show_price_per_single_passenger_1'); ?>" name="<?php CHBSHelper::getFormName('show_price_per_single_passenger'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['show_price_per_single_passenger'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('show_price_per_single_passenger_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('show_price_per_single_passenger_0'); ?>" name="<?php CHBSHelper::getFormName('show_price_per_single_passenger'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['show_price_per_single_passenger'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('show_price_per_single_passenger_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li>	
								<li>
									<h5><?php esc_html_e('"Person" label','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Use "Person" instead of "Adult" label in the booking form.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('passenger_use_person_label_1'); ?>" name="<?php CHBSHelper::getFormName('passenger_use_person_label'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_use_person_label'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('passenger_use_person_label_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('passenger_use_person_label_0'); ?>" name="<?php CHBSHelper::getFormName('passenger_use_person_label'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_use_person_label'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('passenger_use_person_label_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li>									
								<li>
									<h5><?php esc_html_e('Passenger dropdown list','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable dropdown list for number of passengers instead of text field in step #1 of booking form.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('passenger_number_dropdown_list_enable_1'); ?>" name="<?php CHBSHelper::getFormName('passenger_number_dropdown_list_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_number_dropdown_list_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('passenger_number_dropdown_list_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('passenger_number_dropdown_list_enable_0'); ?>" name="<?php CHBSHelper::getFormName('passenger_number_dropdown_list_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_number_dropdown_list_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('passenger_number_dropdown_list_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li>	
								<li>
									<h5><?php esc_html_e('Location of passenger dropdown list','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Display passenger dropdown next to pickup date/time.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Works only if the following options are enabled: adult passenger, person label, and passenger dropdown list.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('passenger_number_dropdown_list_display_type_1'); ?>" name="<?php CHBSHelper::getFormName('passenger_number_dropdown_list_display_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_number_dropdown_list_display_type'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('passenger_number_dropdown_list_display_type_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('passenger_number_dropdown_list_display_type_0'); ?>" name="<?php CHBSHelper::getFormName('passenger_number_dropdown_list_display_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_number_dropdown_list_display_type'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('passenger_number_dropdown_list_display_type_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li>	
							</ul>
						</div>						
						<div id="meta-box-booking-form-1-7">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Default vehicle','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Select default vehicle to be preselected on the booking form.','chauffeur-booking-system'); ?></span>
									<div class="to-clear-fix">
										<select name="<?php CHBSHelper::getFormName('vehicle_id_default'); ?>">
											<option value="-1"><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['vehicle'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['vehicle_id_default'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
										</select>
									</div>
								</li>  
								<li>
									<h5><?php esc_html_e('Vehicle selecting','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable vehicle selection in step #2.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('If disabled, a default vehicle must be set.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Vehicle availability settings are ignored in this case.','chauffeur-booking-system'); ?><br/>
									</span>
									<div class="to-clear-fix">
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('vehicle_select_enable_1'); ?>" name="<?php CHBSHelper::getFormName('vehicle_select_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_select_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('vehicle_select_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('vehicle_select_enable_2'); ?>" name="<?php CHBSHelper::getFormName('vehicle_select_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_select_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('vehicle_select_enable_2'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										 </div>
									</div>
								</li>  
								<li>
									<h5><?php esc_html_e('Vehicles availability','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Enable to prevent submitting orders with vehicles already assigned to other bookings at the same date and time.','chauffeur-booking-system'); ?></span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('prevent_double_vehicle_booking_enable_1'); ?>" name="<?php CHBSHelper::getFormName('prevent_double_vehicle_booking_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['prevent_double_vehicle_booking_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('prevent_double_vehicle_booking_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('prevent_double_vehicle_booking_enable_0'); ?>" name="<?php CHBSHelper::getFormName('prevent_double_vehicle_booking_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['prevent_double_vehicle_booking_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('prevent_double_vehicle_booking_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li> 
								<li>
									<h5><?php esc_html_e('Vehicles availability for the same orders','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable to display vehicles assigned to other orders with same date/time and available seats.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Works only for fixed locations.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('vehicle_in_the_same_booking_passenger_sum_enable_1'); ?>" name="<?php CHBSHelper::getFormName('vehicle_in_the_same_booking_passenger_sum_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_in_the_same_booking_passenger_sum_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('vehicle_in_the_same_booking_passenger_sum_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('vehicle_in_the_same_booking_passenger_sum_enable_0'); ?>" name="<?php CHBSHelper::getFormName('vehicle_in_the_same_booking_passenger_sum_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_in_the_same_booking_passenger_sum_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('vehicle_in_the_same_booking_passenger_sum_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li> 
								<li>
									<h5><?php esc_html_e('Vehicle categories','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Select vehicle categories available for booking.','chauffeur-booking-system'); ?></span>
									<div class="to-checkbox-button">
										<input type="checkbox" value="-1" id="<?php CHBSHelper::getFormName('vehicle_category_id_0'); ?>" name="<?php CHBSHelper::getFormName('vehicle_category_id[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_category_id'],-1); ?>/>
										<label for="<?php CHBSHelper::getFormName('vehicle_category_id_0'); ?>"><?php esc_html_e('- All categories -','chauffeur-booking-system') ?></label>
<?php
		foreach($this->data['dictionary']['vehicle_category'] as $index=>$value)
		{
?>
									<input type="checkbox" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('vehicle_category_id_'.$index); ?>" name="<?php CHBSHelper::getFormName('vehicle_category_id[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_category_id'],$index); ?>/>
									<label for="<?php CHBSHelper::getFormName('vehicle_category_id_'.$index); ?>"><?php echo esc_html($value['name']); ?></label>
<?php		
		}
?>
									</div>
								</li>
								<li>
									<h5><?php esc_html_e('Vehicle filter','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable selected filters in filter bar.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										<div class="to-checkbox-button">
											<input type="checkbox" value="1" id="<?php CHBSHelper::getFormName('vehicle_filter_enable_1'); ?>" name="<?php CHBSHelper::getFormName('vehicle_filter_enable[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_filter_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('vehicle_filter_enable_1'); ?>"><?php esc_html_e('Passengers','chauffeur-booking-system'); ?></label>
											<input type="checkbox" value="2" id="<?php CHBSHelper::getFormName('vehicle_filter_enable_2'); ?>" name="<?php CHBSHelper::getFormName('vehicle_filter_enable[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_filter_enable'],2); ?>/>
											<label for="<?php CHBSHelper::getFormName('vehicle_filter_enable_2'); ?>"><?php esc_html_e('Suitcases','chauffeur-booking-system'); ?></label>
											<input type="checkbox" value="3" id="<?php CHBSHelper::getFormName('vehicle_filter_enable_3'); ?>" name="<?php CHBSHelper::getFormName('vehicle_filter_enable[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_filter_enable'],3); ?>/>
											<label for="<?php CHBSHelper::getFormName('vehicle_filter_enable_3'); ?>"><?php esc_html_e('Standard','chauffeur-booking-system'); ?></label>
											<input type="checkbox" value="4" id="<?php CHBSHelper::getFormName('vehicle_filter_enable_4'); ?>" name="<?php CHBSHelper::getFormName('vehicle_filter_enable[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_filter_enable'],4); ?>/>
											<label for="<?php CHBSHelper::getFormName('vehicle_filter_enable_4'); ?>"><?php esc_html_e('Type','chauffeur-booking-system'); ?></label>
										</div>
									</div>
								</li>  
								<li>
									<h5><?php esc_html_e('Vehicles sorting','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Select sorting options of vehicles in booking form.','chauffeur-booking-system'); ?></span>
									<div class="to-clear-fix">
										<select name="<?php CHBSHelper::getFormName('vehicle_sorting_type'); ?>">
 <?php
		foreach($this->data['dictionary']['vehicle_sorting_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['vehicle_sorting_type'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
										</select>
									</div>
								</li>	
								<li>
									<h5><?php esc_html_e('Vehicle pagination','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enter number of vehicles displayed on single page.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Value 0 means that all vehicles will be displayed on single page and pagination won\'t be displayed.','chauffeur-booking-system'); ?><br/>
									</span>
									<div>
										<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('vehicle_pagination_vehicle_per_page'); ?>" value="<?php echo esc_attr($this->data['meta']['vehicle_pagination_vehicle_per_page']); ?>"/>
									</div> 
								</li>
								<li>
									<h5><?php esc_html_e('Limit number of vehicles','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Limit number of displayed vehicles.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Value 0 means that all vehicles will be displayed on single page.','chauffeur-booking-system'); ?><br/>
									</span>
									<div>
										<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('vehicle_limit'); ?>" value="<?php echo esc_attr($this->data['meta']['vehicle_limit']); ?>"/>
									</div> 
								</li>  
							</ul>
						</div>
						<div id="meta-box-booking-form-1-8">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Booking extras','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Select categories, from which add-ons are available to book.','chauffeur-booking-system'); ?></span>
									<div class="to-checkbox-button">
										<input type="checkbox" value="-1" id="<?php CHBSHelper::getFormName('booking_extra_category_id__1'); ?>" name="<?php CHBSHelper::getFormName('booking_extra_category_id[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_extra_category_id'],-1); ?>/>
										<label for="<?php CHBSHelper::getFormName('booking_extra_category_id__1'); ?>"><?php esc_html_e('- All extras -','chauffeur-booking-system') ?></label>
										<input type="checkbox" value="-2" id="<?php CHBSHelper::getFormName('booking_extra_category_id__2'); ?>" name="<?php CHBSHelper::getFormName('booking_extra_category_id[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_extra_category_id'],-2); ?>/>
										<label for="<?php CHBSHelper::getFormName('booking_extra_category_id__2'); ?>"><?php esc_html_e('- None -','chauffeur-booking-system') ?></label>
<?php
		foreach($this->data['dictionary']['booking_extra_category'] as $index=>$value)
		{
?>
										<input type="checkbox" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('booking_extra_category_id_'.$index); ?>" name="<?php CHBSHelper::getFormName('booking_extra_category_id[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_extra_category_id'],$index); ?>/>
										<label for="<?php CHBSHelper::getFormName('booking_extra_category_id_'.$index); ?>"><?php echo esc_html($value['name']); ?></label>
<?php		
		}
?>
									</div>
								</li>	  
								<li>
									<h5><?php esc_html_e('Show booking extras categories','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable showing add-ons grouped in categories.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Please note that add-on has to be assigned to at least one category, otherwise it won\'t be displayed (when this option is enabled).','chauffeur-booking-system'); ?><br/>
									</span>
									<div class="to-clear-fix">
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('booking_extra_category_display_enable_1'); ?>" name="<?php CHBSHelper::getFormName('booking_extra_category_display_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_extra_category_display_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('booking_extra_category_display_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('booking_extra_category_display_enable_0'); ?>" name="<?php CHBSHelper::getFormName('booking_extra_category_display_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_extra_category_display_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('booking_extra_category_display_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>
								</li>
								<li>
									<h5><?php esc_html_e('Booking extra notes','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable showing notes under each add-on in step #3 of booking form.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('booking_extra_note_display_enable_1'); ?>" name="<?php CHBSHelper::getFormName('booking_extra_note_display_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_extra_note_display_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('booking_extra_note_display_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('booking_extra_note_display_enable_0'); ?>" name="<?php CHBSHelper::getFormName('booking_extra_note_display_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_extra_note_display_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('booking_extra_note_display_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Mandatory.','chauffeur-booking-system'); ?></span>
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('booking_extra_note_mandatory_enable_1'); ?>" name="<?php CHBSHelper::getFormName('booking_extra_note_mandatory_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_extra_note_mandatory_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('booking_extra_note_mandatory_enable_1'); ?>"><?php esc_html_e('Yes','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('booking_extra_note_mandatory_enable_0'); ?>" name="<?php CHBSHelper::getFormName('booking_extra_note_mandatory_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_extra_note_mandatory_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('booking_extra_note_mandatory_enable_0'); ?>"><?php esc_html_e('No','chauffeur-booking-system'); ?></label>
										</div>
									</div>
								</li>	
								<li>
									<h5><?php esc_html_e('Prices modification','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enter percentage of the extra’s net price for "Return" and "Return (new ride)" transfer types.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('100 means same price as for "One way" ride, 50 means half the price, etc.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Return transfer type.','chauffeur-booking-system'); ?></span>
										<input type="text" maxlength="7" name="<?php CHBSHelper::getFormName('booking_extra_price_change_return_percentage'); ?>" value="<?php echo esc_attr($this->data['meta']['booking_extra_price_change_return_percentage']); ?>"/>
									</div> 
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Return (new ride) transfer type.','chauffeur-booking-system'); ?></span>
										<input type="text" maxlength="7" name="<?php CHBSHelper::getFormName('booking_extra_price_change_return_new_ride_percentage'); ?>" value="<?php echo esc_attr($this->data['meta']['booking_extra_price_change_return_new_ride_percentage']); ?>"/>
									</div> 
								</li>  
							</ul>
						</div>
						<div id="meta-box-booking-form-1-9">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Currencies','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Select available currencies.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('You can set exchange rates for each selected currency in plugin options.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('You can run booking form with particular currency by adding parameter "currency=CODE" to the query string of page on which booking form is located.','chauffeur-booking-system'); ?>
									</span>						
									<div class="to-clear-fix">
										<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('currency[]'); ?>">
											<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['currency'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['currency'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['currency'],$index,false)).'>'.esc_html($value['name'].' ('.$index.')').'</option>';
?>
										</select>												
									</div>
								</li>
								<li>
									<h5><?php esc_html_e('Calculation method','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Select one of the available price calculation methods for services.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('This option is available only for "Variable" booking sum type.','chauffeur-booking-system'); ?>
									</span>
									<div>
										<table class="to-table">
											<tr>
												<th style="width:20%">
													<div>
														<?php esc_html_e('Service','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Service type offered.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
												<th style="width:80%">
													<div>
														<?php esc_html_e('Method','chauffeur-booking-system'); ?>
														<span class="to-legend">
															<?php esc_html_e('Calculation method.','chauffeur-booking-system'); ?>
														</span>
													</div>
												</th>
											</tr>
											<tr>
												<td>
													<div><?php esc_html_e('Distance','chauffeur-booking-system'); ?></div>
												</td>
												<td>
													<div class="to-clear-fix">
														<div class="to-radio-button">
<?php
		foreach($this->data['dictionary']['calculation_method'] as $index=>$value)
		{
			if(!in_array(1,$value[1])) continue;
?>
															<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('calculation_method_service_type_1_'.$index); ?>" name="<?php CHBSHelper::getFormName('calculation_method_service_type_1'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['calculation_method_service_type_1'],$index); ?>/>
															<label for="<?php CHBSHelper::getFormName('calculation_method_service_type_1_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>								
														</div>
													</div>
												</td>
											</tr>									
											<tr>
												<td>
													<div><?php esc_html_e('Hourly','chauffeur-booking-system'); ?></div>
												</td>
												<td>
													<div class="to-clear-fix">
														<div class="to-radio-button">
<?php
		foreach($this->data['dictionary']['calculation_method'] as $index=>$value)
		{
			if(!in_array(2,$value[1])) continue;
?>
															<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('calculation_method_service_type_2_'.$index); ?>" name="<?php CHBSHelper::getFormName('calculation_method_service_type_2'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['calculation_method_service_type_2'],$index); ?>/>
															<label for="<?php CHBSHelper::getFormName('calculation_method_service_type_2_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>								
														</div>
													</div>													
												</td>
											</tr>  
											<tr>
												<td>
													<div><?php esc_html_e('Flat rate','chauffeur-booking-system'); ?></div>
												</td>
												<td>
													<div class="to-clear-fix">
														<div class="to-radio-button">
<?php
		foreach($this->data['dictionary']['calculation_method'] as $index=>$value)
		{
			if(!in_array(3,$value[1])) continue;
?>
															<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('calculation_method_service_type_3_'.$index); ?>" name="<?php CHBSHelper::getFormName('calculation_method_service_type_3'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['calculation_method_service_type_3'],$index); ?>/>
															<label for="<?php CHBSHelper::getFormName('calculation_method_service_type_3_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>								
														</div>
													</div>	
												</td>
											</tr>  
										</table>
									</div>
								</li>  
								<li>
									<h5><?php esc_html_e('Hide fees','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Hide/show all additional fees (initial, delivery) in booking summary and include/exclude them to/from the price of the vehicle.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										<select name="<?php CHBSHelper::getFormName('booking_summary_hide_fee'); ?>">
											<option value="0" <?php CHBSHelper::selectedIf($this->data['meta']['booking_summary_hide_fee'],0); ?>><?php esc_html_e('Show in booking summary and exclude from vehicle price','chauffeur-booking-system') ?></option>
											<option value="2" <?php CHBSHelper::selectedIf($this->data['meta']['booking_summary_hide_fee'],2); ?>><?php esc_html_e('Show in booking summary and include in vehicle price','chauffeur-booking-system') ?></option>
											<option value="1" <?php CHBSHelper::selectedIf($this->data['meta']['booking_summary_hide_fee'],1); ?>><?php esc_html_e('Hide in booking summary and include in vehicle price','chauffeur-booking-system') ?></option>
										</select>
									</div>
								</li>  
								<li>
									<h5><?php esc_html_e('Hide prices','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Hide all prices and summary.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('If enabled, customers won’t see prices or payment methods. WooCommerce support will also be disabled.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('price_hide_1'); ?>" name="<?php CHBSHelper::getFormName('price_hide'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['price_hide'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('price_hide_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('price_hide_0'); ?>" name="<?php CHBSHelper::getFormName('price_hide'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['price_hide'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('price_hide_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li>  
								<li>
									<h5><?php esc_html_e('Split order sum','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Split order sum to net and tax value in summary section.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('order_sum_split_1'); ?>" name="<?php CHBSHelper::getFormName('order_sum_split'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['order_sum_split'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('order_sum_split_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('order_sum_split_0'); ?>" name="<?php CHBSHelper::getFormName('order_sum_split'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['order_sum_split'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('order_sum_split_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li> 
								<li>
									<h5><?php esc_html_e('Show vehicle price details','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Split vehicle price to one way and return sum in the booking summary for a selected transfer types.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-checkbox-button">
										<input type="checkbox" value="-1" id="<?php CHBSHelper::getFormName('vehicle_sum_split_1'); ?>" name="<?php CHBSHelper::getFormName('vehicle_sum_split[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_sum_split'],-1); ?>/>
										<label for="<?php CHBSHelper::getFormName('vehicle_sum_split_1'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										<input type="checkbox" value="2" id="<?php CHBSHelper::getFormName('vehicle_sum_split_2'); ?>" name="<?php CHBSHelper::getFormName('vehicle_sum_split[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_sum_split'],2); ?>/>
										<label for="<?php CHBSHelper::getFormName('vehicle_sum_split_2'); ?>"><?php esc_html_e('Return','chauffeur-booking-system'); ?></label>
										<input type="checkbox" value="3" id="<?php CHBSHelper::getFormName('vehicle_sum_split_3'); ?>" name="<?php CHBSHelper::getFormName('vehicle_sum_split[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_sum_split'],3); ?>/>
										<label for="<?php CHBSHelper::getFormName('vehicle_sum_split_3'); ?>"><?php esc_html_e('Return (new ride)','chauffeur-booking-system'); ?></label>
									</div>
								</li>								
								<li>
									<h5><?php esc_html_e('Show net prices and hide tax','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Show net prices and hide tax - tax value will be displayed in last step only.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('show_net_price_hide_tax_1'); ?>" name="<?php CHBSHelper::getFormName('show_net_price_hide_tax'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['show_net_price_hide_tax'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('show_net_price_hide_tax_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('show_net_price_hide_tax_0'); ?>" name="<?php CHBSHelper::getFormName('show_net_price_hide_tax'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['show_net_price_hide_tax'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('show_net_price_hide_tax_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li> 
								<li>
									<h5><?php esc_html_e('Show price before discount','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Show original (higher) price before applying "Pricing Rules".','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('If the final price is lower than the base price from "Vehicles" or "Routes", the higher one will be shown with strikethrough.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Works only if "Show price per passengers" and "Bid vehicle price" are disabled.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('show_price_before_discount_1'); ?>" name="<?php CHBSHelper::getFormName('show_price_before_discount'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['show_price_before_discount'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('show_price_before_discount_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('show_price_before_discount_0'); ?>" name="<?php CHBSHelper::getFormName('show_price_before_discount'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['show_price_before_discount'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('show_price_before_discount_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li> 
								<li>
									<h5><?php esc_html_e('Apply tax based on geofence','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Apply tax to the distance prices based on geofence.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('This option works for "Distance" service type and "Distance" calculation method only.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('tax_rate_geofence_enable_1'); ?>" name="<?php CHBSHelper::getFormName('tax_rate_geofence_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['tax_rate_geofence_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('tax_rate_geofence_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('tax_rate_geofence_enable_0'); ?>" name="<?php CHBSHelper::getFormName('tax_rate_geofence_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['tax_rate_geofence_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('tax_rate_geofence_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li> 
								
									<li>
									<h5><?php esc_html_e('Coupons','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Enter setting of coupons.','chauffeur-booking-system'); ?></span>
									<div>
										<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('coupon_enable_1'); ?>" name="<?php CHBSHelper::getFormName('coupon_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['coupon_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('coupon_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('coupon_enable_0'); ?>" name="<?php CHBSHelper::getFormName('coupon_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['coupon_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('coupon_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>
									<div>
										<span class="to-legend-field"><?php esc_html_e('Show value of the discount in the booking summary.','chauffeur-booking-system'); ?></span>
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('coupon_discount_value_show_1'); ?>" name="<?php CHBSHelper::getFormName('coupon_discount_value_show'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['coupon_discount_value_show'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('coupon_discount_value_show_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('coupon_discount_value_show_0'); ?>" name="<?php CHBSHelper::getFormName('coupon_discount_value_show'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['coupon_discount_value_show'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('coupon_discount_value_show_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>
								</li> 
								<li>
									<h5><?php esc_html_e('Gratuity','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enter setting of gratuity.','chauffeur-booking-system'); ?>
									</span> 
									<div>
										<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('gratuity_enable_1'); ?>" name="<?php CHBSHelper::getFormName('gratuity_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['gratuity_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('gratuity_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('gratuity_enable_0'); ?>" name="<?php CHBSHelper::getFormName('gratuity_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['gratuity_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('gratuity_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>
									<div>
										<span class="to-legend-field"><?php esc_html_e('Gratuity type.','chauffeur-booking-system'); ?></span>
										<div class="to-radio-button">
<?php
		foreach($this->data['dictionary']['gratuity_type'] as $index=>$value)
		{
?>
											<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('gratuity_admin_type_'.$index); ?>" name="<?php CHBSHelper::getFormName('gratuity_admin_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['gratuity_admin_type'],$index); ?>/>
											<label for="<?php CHBSHelper::getFormName('gratuity_admin_type_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>								
										</div>
									</div> 
									<div>
										<span class="to-legend-field"><?php esc_html_e('Value of gratuity (fixed or percentage).','chauffeur-booking-system'); ?></span>
										<input type="text" name="<?php CHBSHelper::getFormName('gratuity_admin_value'); ?>" id="<?php CHBSHelper::getFormName('gratuity_admin_value'); ?>" value="<?php echo esc_attr($this->data['meta']['gratuity_admin_value']); ?>"/>
									</div>									
									<div>
										<span class="to-legend-field"><?php esc_html_e('Enable possibility of changing gratuity by customer.','chauffeur-booking-system'); ?></span>
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('gratuity_customer_enable_1'); ?>" name="<?php CHBSHelper::getFormName('gratuity_customer_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['gratuity_customer_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('gratuity_customer_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="2" id="<?php CHBSHelper::getFormName('gratuity_customer_enable_0'); ?>" name="<?php CHBSHelper::getFormName('gratuity_customer_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['gratuity_customer_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('gratuity_customer_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>   
									<div>
										<span class="to-legend-field"><?php esc_html_e('Customer gratuity type (fixed or percentage).','chauffeur-booking-system'); ?></span>
										<div class="to-checkbox-button">
<?php
		foreach($this->data['dictionary']['gratuity_type'] as $index=>$value)
		{
?>
											<input type="checkbox" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('gratuity_customer_type_'.$index); ?>" name="<?php CHBSHelper::getFormName('gratuity_customer_type[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['gratuity_customer_type'],$index); ?>/>
											<label for="<?php CHBSHelper::getFormName('gratuity_customer_type_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>								
										</div>											
									</div>  
								</li>
								<li>
									<h5><?php esc_html_e('Vehicle price rounding','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Vehicle price rounding.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('A value from range 0.01-999999.99. If empty, price will not be rounded.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('This option works for vehicle gross price only.','chauffeur-booking-system'); ?>
									</span>
									<div>
										<input type="text" maxlength="9" name="<?php CHBSHelper::getFormName('vehicle_price_round'); ?>" value="<?php echo esc_attr($this->data['meta']['vehicle_price_round']); ?>"/>
									</div>
								</li>   
								<li>
									<h5><?php esc_html_e('Bid vehicle price','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Allows customer to enter their own price for a vehicle.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Price cannot be lower than a defined percentage of the base price.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('For fixed locations, this limit can be overridden by a value set in the location settings.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Works only if "Hide fees" is enabled and "Vehicle price rounding" is set to 0.00.','chauffeur-booking-system'); ?>
									</span>
									<div>
										<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
										<div>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('vehicle_bid_enable_1'); ?>" name="<?php CHBSHelper::getFormName('vehicle_bid_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_bid_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('vehicle_bid_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('vehicle_bid_enable_0'); ?>" name="<?php CHBSHelper::getFormName('vehicle_bid_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_bid_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('vehicle_bid_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</div>					 
									</div>
									<div>
										<span class="to-legend-field"><?php esc_html_e('Maximum percentage discount.','chauffeur-booking-system'); ?></span>
										<div>
											<input type="text" maxlength="5" name="<?php CHBSHelper::getFormName('vehicle_bid_max_percentage_discount'); ?>" value="<?php echo esc_attr($this->data['meta']['vehicle_bid_max_percentage_discount']); ?>"/>
										</div>					 
									</div>									
								</li>
							</ul>
						</div>
						<div id="meta-box-booking-form-1-10">
							<div class="to-notice-small to-notice-small-info">
								<?php echo sprintf(__('To use these features, make sure WooCommerce is installed and the "Checkout" page is set up in <a href="%s" target="_blank">WooCommerce settings</a>.','chauffeur-booking-system'),admin_url('admin.php?page=wc-settings&tab=advanced')); ?>
							</div>
							<ul class="to-form-field-list">					
								<li>
									<h5><?php esc_html_e('WooCommerce support','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable managing bookings and payments via the WooCommerce plugin.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Requires "Hide prices" and "Add to WooCommerce cart" options to be disabled.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('woocommerce_enable_1'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['woocommerce_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('woocommerce_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('woocommerce_enable_0'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['woocommerce_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('woocommerce_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li>
								<li>
									<h5><?php esc_html_e('WooCommerce account','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable account creation and login via WooCommerce.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('"Disable" means the login and registration forms will not be displayed.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('"Enable as option" means both forms will be available, but account creation and login are optional.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('"Enable as mandatory" means the user must be registered and logged in before sending a booking.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('woocommerce_account_enable_type_1'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_account_enable_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['woocommerce_account_enable_type'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('woocommerce_account_enable_type_1'); ?>"><?php esc_html_e('Enable as option','chauffeur-booking-system'); ?></label>
										<input type="radio" value="2" id="<?php CHBSHelper::getFormName('woocommerce_account_enable_type_2'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_account_enable_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['woocommerce_account_enable_type'],2); ?>/>
										<label for="<?php CHBSHelper::getFormName('woocommerce_account_enable_type_2'); ?>"><?php esc_html_e('Enable as mandatory','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('woocommerce_account_enable_type_0'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_account_enable_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['woocommerce_account_enable_type'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('woocommerce_account_enable_type_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li>
								<li>
									<h5><?php esc_html_e('Add to wooCommerce cart','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable adding bookings to the WooCommerce cart as products.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Requires "Hide prices" and "WooCommerce support" to be disabled.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_enable_1'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['woocommerce_add_to_cart_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_enable_0'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['woocommerce_add_to_cart_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>
									<div class="to-clear-fix">
										<span class="to-legend-field">
											<?php esc_html_e('Process booking after sending via the wooCommerce.','chauffeur-booking-system'); ?>
										</span>
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_process_booking_enable_1'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_process_booking_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['woocommerce_add_to_cart_process_booking_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_process_booking_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_process_booking_enable_0'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_process_booking_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['woocommerce_add_to_cart_process_booking_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_process_booking_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>		
									<div class="to-clear-fix">
										<span class="to-legend-field">
											<?php esc_html_e('Update booking client details using data from wooCommerce checkout.','chauffeur-booking-system'); ?>
										</span>
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_update_client_detail_enable_1'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_update_client_detail_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['woocommerce_add_to_cart_update_client_detail_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_update_client_detail_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_update_client_detail_enable_0'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_update_client_detail_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['woocommerce_add_to_cart_update_client_detail_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_update_client_detail_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>											
									<div class="to-clear-fix">
										<span class="to-legend-field">
											<?php esc_html_e('Update booking client billing details using data from wooCommerce checkout.','chauffeur-booking-system'); ?>
										</span>
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_update_client_billing_details_enable_1'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_update_client_billing_details_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['woocommerce_add_to_cart_update_client_billing_details_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_update_client_billing_details_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_update_client_billing_details_enable_0'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_update_client_billing_details_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['woocommerce_add_to_cart_update_client_billing_details_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_update_client_billing_details_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>
									</div>
									<div class="to-clear-fix">
										<span class="to-legend-field">
											<?php esc_html_e('Assign created product to the category.','chauffeur-booking-system'); ?>
										</span>
										<div class="to-clear-fix">
											<select name="<?php CHBSHelper::getFormName('woocommerce_add_to_cart_product_category'); ?>">
												<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['woocommerce_add_to_cart_product_category'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
												<option value="-2" <?php CHBSHelper::selectedIf($this->data['meta']['woocommerce_add_to_cart_product_category'],-2); ?>><?php esc_html_e('- Vehicle category -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['woocommerce_product_category'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['woocommerce_add_to_cart_product_category'],$index,false)).'>'.esc_html($value['name_hierarchical']).'</option>';
?>
											</select>												
										</div>
									</div>
								</li>  								
							</ul>
						</div>
						<div id="meta-box-booking-form-1-11">
							<div class="ui-tabs">
								<ul>
									<li><a href="#meta-box-booking-form-1-11-1"><?php esc_html_e('Main','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-booking-form-1-11-2"><?php esc_html_e('Step #1','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-booking-form-1-11-3"><?php esc_html_e('Step #2','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-booking-form-1-11-4"><?php esc_html_e('Step #3','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-booking-form-1-11-5"><?php esc_html_e('Step #4','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-booking-form-1-11-6"><?php esc_html_e('Step #5','chauffeur-booking-system'); ?></a></li>
								</ul>	 
								<div id="meta-box-booking-form-1-11-1">		
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('Form preloader','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Enter properties of preloader.','chauffeur-booking-system'); ?>
											</span>
											<div>
												<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('form_preloader_enable_1'); ?>" name="<?php CHBSHelper::getFormName('form_preloader_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['form_preloader_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('form_preloader_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('form_preloader_enable_0'); ?>" name="<?php CHBSHelper::getFormName('form_preloader_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['form_preloader_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('form_preloader_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>
											</div>
											<div>
												<span class="to-legend-field"><?php esc_html_e('Image.','chauffeur-booking-system'); ?></span>
												<input type="text" name="<?php CHBSHelper::getFormName('form_preloader_image_src'); ?>" id="<?php CHBSHelper::getFormName('form_preloader_image_src'); ?>" class="to-float-left" value="<?php echo esc_attr($this->data['meta']['form_preloader_image_src']); ?>"/>
												<input type="button" name="<?php CHBSHelper::getFormName('form_preloader_image_src_browse'); ?>" id="<?php CHBSHelper::getFormName('form_preloader_image_src_browse'); ?>" class="to-button-browse to-button" value="<?php esc_attr_e('Browse','chauffeur-booking-system'); ?>"/>
											</div>  
											<div>
												<span class="to-legend-field"><?php esc_html_e('Background opacity.','chauffeur-booking-system'); ?></span>
												<div id="<?php CHBSHelper::getFormName('form_preloader_background_opacity'); ?>"></div>
												<input type="text" name="<?php CHBSHelper::getFormName('form_preloader_background_opacity'); ?>" id="<?php CHBSHelper::getFormName('form_preloader_background_opacity'); ?>" class="to-slider-range" readonly/>
											</div>		 
											<div>
												<span class="to-legend-field"><?php esc_html_e('Background color.','chauffeur-booking-system'); ?></span>
												<div class="to-clear-fix">	
													<input type="text" class="to-color-picker" id="<?php CHBSHelper::getFormName('form_preloader_background_color'); ?>" name="<?php CHBSHelper::getFormName('form_preloader_background_color'); ?>" value="<?php echo esc_attr($this->data['meta']['form_preloader_background_color']); ?>"/>
												</div>
											</div>									
										</li>  
										<li>
											<h5><?php esc_html_e('Total time','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Enable or disable showing total time in the booking.','chauffeur-booking-system'); ?></span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('total_time_display_enable_1'); ?>" name="<?php CHBSHelper::getFormName('total_time_display_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['total_time_display_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('total_time_display_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('total_time_display_enable_0'); ?>" name="<?php CHBSHelper::getFormName('total_time_display_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['total_time_display_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('total_time_display_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li> 
										<li>
											<h5><?php esc_html_e('Sticky summary sidebar','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Enable or disable sticky option for summary sidebar.','chauffeur-booking-system'); ?></span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('summary_sidebar_sticky_enable_1'); ?>" name="<?php CHBSHelper::getFormName('summary_sidebar_sticky_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['summary_sidebar_sticky_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('summary_sidebar_sticky_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('summary_sidebar_sticky_enable_0'); ?>" name="<?php CHBSHelper::getFormName('summary_sidebar_sticky_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['summary_sidebar_sticky_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('summary_sidebar_sticky_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li> 
										<li>
											<h5><?php esc_html_e('Top navigation','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php  esc_html_e('Enable or disable top navigation.','chauffeur-booking-system'); ?>
											</span>
											<div class="to-clear-fix">
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('navigation_top_enable_1'); ?>" name="<?php CHBSHelper::getFormName('navigation_top_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['navigation_top_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('navigation_top_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('navigation_top_enable_0'); ?>" name="<?php CHBSHelper::getFormName('navigation_top_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['navigation_top_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('navigation_top_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>								
											</div>
										</li>
									</ul>
								</div>
								<div id="meta-box-booking-form-1-11-2">		
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('Extra time','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Choose whether you want to offer the option of extra time (in hours).','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('This option is available for "Distance" and "Flat rate" services only.','chauffeur-booking-system'); ?>
											</span>
											<div>
												<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('extra_time_enable_1'); ?>" name="<?php CHBSHelper::getFormName('extra_time_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['extra_time_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('extra_time_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('extra_time_enable_0'); ?>" name="<?php CHBSHelper::getFormName('extra_time_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['extra_time_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('extra_time_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>
											</div>
											<div>
												<span class="to-legend-field"><?php esc_html_e('Specify the minimum (integer value from 0 to 9999) and maximum (integer value from 1 to 9999) extra time in selected time unit.','chauffeur-booking-system'); ?></span>
												<div>
													<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('extra_time_range_min'); ?>" value="<?php echo esc_attr($this->data['meta']['extra_time_range_min']); ?>"/>
												</div>
												<div>
													<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('extra_time_range_max'); ?>" value="<?php echo esc_attr($this->data['meta']['extra_time_range_max']); ?>"/>
												</div>
											</div>						  
											<div>
												<span class="to-legend-field"><?php esc_html_e('Step (integer value from 1 to 9999).','chauffeur-booking-system'); ?></span>
												<div>
													<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('extra_time_step'); ?>" value="<?php echo esc_attr($this->data['meta']['extra_time_step']); ?>"/>
												</div>
											</div> 
											<div>
												<span class="to-legend-field"><?php esc_html_e('Unit.','chauffeur-booking-system'); ?></span>
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('extra_time_unit_1'); ?>" name="<?php CHBSHelper::getFormName('extra_time_unit'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['extra_time_unit'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('extra_time_unit_1'); ?>"><?php esc_html_e('Minutes','chauffeur-booking-system'); ?></label>
													<input type="radio" value="2" id="<?php CHBSHelper::getFormName('extra_time_unit_2'); ?>" name="<?php CHBSHelper::getFormName('extra_time_unit'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['extra_time_unit'],2); ?>/>
													<label for="<?php CHBSHelper::getFormName('extra_time_unit_2'); ?>"><?php esc_html_e('Hours','chauffeur-booking-system'); ?></label>
												</div>
											</div>
											<div>
												<span class="to-legend-field"><?php esc_html_e('Mandatory.','chauffeur-booking-system'); ?></span>
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('extra_time_mandatory_1'); ?>" name="<?php CHBSHelper::getFormName('extra_time_mandatory'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['extra_time_mandatory'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('extra_time_mandatory_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('extra_time_mandatory_2'); ?>" name="<?php CHBSHelper::getFormName('extra_time_mandatory'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['extra_time_mandatory'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('extra_time_mandatory_2'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>
											</div>									
										</li>
										<li>
											<h5><?php esc_html_e('Duration','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Rental time of the vehicle (in hours).','chauffeur-booking-system'); ?><br/>
											</span>
											<div>
												<span class="to-legend-field"><?php esc_html_e('Specify the minimum (integer value from 1 to 9999) and maximum (integer value from 1 to 9999) rental time of the vehicle.','chauffeur-booking-system'); ?></span>
												<div>
													<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('duration_min'); ?>" value="<?php echo esc_attr($this->data['meta']['duration_min']); ?>"/>
												</div>
												<div>
													<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('duration_max'); ?>" value="<?php echo esc_attr($this->data['meta']['duration_max']); ?>"/>
												</div>
											</div>						  
											<div>
												<span class="to-legend-field"><?php esc_html_e('Step (integer value from 1 to 9999).','chauffeur-booking-system'); ?></span>
												<div>
													<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('duration_step'); ?>" value="<?php echo esc_attr($this->data['meta']['duration_step']); ?>"/>
												</div>
											</div>								  
										</li>
<?php
		if((int)CHBSOption::getOption('length_unit')===2)
		{
			$durationDistanceMin=round($Length->convertUnit($this->data['meta']['duration_distance_min'],1,2),1);
			$durationDistanceMax=round($Length->convertUnit($this->data['meta']['duration_distance_max'],1,2),1);
			$durationDistanceStep=round($Length->convertUnit($this->data['meta']['duration_distance_step'],1,2),1);
		
			$durationDistanceListArray=array();
			$durationDistanceList=preg_split('/;/',$this->data['meta']['duration_distance_list']);
		
			foreach($durationDistanceList as $index=>$value)
			{
				if($Validation->isNotEmpty($value))
				{
					$durationDistanceListArray[]=round($Length->convertUnit($value,1,2),1);	
				}
			}
			
			$durationDistanceList=join(';',$durationDistanceListArray);
		}
		else
		{
			$durationDistanceMin=$this->data['meta']['duration_distance_min'];
			$durationDistanceMax=$this->data['meta']['duration_distance_max'];
			$durationDistanceStep=$this->data['meta']['duration_distance_step'];
			$durationDistanceList=$this->data['meta']['duration_distance_list'];
		}
?>
										
										<li>
											<h5><?php esc_html_e('Distance limit','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php echo sprintf(esc_html__('Distance limit (%s) per hour. These values will be listed together with the hours in the "Duration" field.','chauffeur-booking-system'),$Length->label(CHBSOption::getOption('length_unit'),6)); ?><br/>
											</span>
											<div>
												<span class="to-legend-field"><?php esc_html_e('Specify the minimum (integer value from 1 to 9999) and maximum (integer value from 1 to 9999) distance limit per hour.','chauffeur-booking-system'); ?></span>
												<div>
													<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('duration_distance_min'); ?>" value="<?php echo esc_attr($durationDistanceMin); ?>"/>
												</div>
												<div>
													<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('duration_distance_max'); ?>" value="<?php echo esc_attr($durationDistanceMax); ?>"/>
												</div>
											</div>						  
											<div>
												<span class="to-legend-field"><?php esc_html_e('Step (integer value from 1 to 9999).','chauffeur-booking-system'); ?></span>
												<div>
													<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('duration_distance_step'); ?>" value="<?php echo esc_attr($durationDistanceStep); ?>"/>
												</div>
											</div>	
											<div>
												<span class="to-legend-field">
													<?php echo sprintf(esc_html__('Enter list of %s (separated by semicolon) for each hour.','chauffeur-booking-system'),$Length->label(CHBSOption::getOption('length_unit'),7)); ?><br/>
													<?php echo sprintf(esc_html__('If the list will be specified, then values located in the min, max and step fields will be ignored.','chauffeur-booking-system'),$Length->label(CHBSOption::getOption('length_unit'),7)); ?>
												</span>
												<div>
													<input type="text" name="<?php CHBSHelper::getFormName('duration_distance_list'); ?>" value="<?php echo esc_attr($durationDistanceList); ?>"/>
												</div>
											</div>	
											<div>
												<span class="to-legend-field">
													<?php esc_html_e('Show selected distance limit (related to the duration) as a "Included distance".','chauffeur-booking-system'); ?><br/>
												</span>
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('duration_distance_included_enable_1'); ?>" name="<?php CHBSHelper::getFormName('duration_distance_included_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['duration_distance_included_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('duration_distance_included_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('duration_distance_included_enable_0'); ?>" name="<?php CHBSHelper::getFormName('duration_distance_included_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['duration_distance_included_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('duration_distance_included_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>					
											</div>
										</li>										
										<li>
											<h5><?php esc_html_e('Waypoints duration','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Duration of each waypoint in the minutes.','chauffeur-booking-system'); ?><br/>
											</span>
											<div>
												<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('waypoint_duration_enable_1'); ?>" name="<?php CHBSHelper::getFormName('waypoint_duration_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['waypoint_duration_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('waypoint_duration_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('waypoint_duration_enable_0'); ?>" name="<?php CHBSHelper::getFormName('waypoint_duration_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['waypoint_duration_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('waypoint_duration_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>													
											</div>
											<div>
												<span class="to-legend-field"><?php esc_html_e('Minimum value in minutes (integer value from 0 to 9999).','chauffeur-booking-system'); ?></span>
												<div>
													<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('waypoint_duration_minimum_value'); ?>" value="<?php echo esc_attr($this->data['meta']['waypoint_duration_minimum_value']); ?>"/>
												</div>
											</div>	
											<div>
												<span class="to-legend-field"><?php esc_html_e('Maximum value in minutes (integer value from 0 to 9999).','chauffeur-booking-system'); ?></span>
												<div>
													<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('waypoint_duration_maximum_value'); ?>" value="<?php echo esc_attr($this->data['meta']['waypoint_duration_maximum_value']); ?>"/>
												</div>
											</div>	
											<div>
												<span class="to-legend-field"><?php esc_html_e('Step value in minutes (integer value from 1 to 9999).','chauffeur-booking-system'); ?></span>
												<div>
													<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('waypoint_duration_step_value'); ?>" value="<?php echo esc_attr($this->data['meta']['waypoint_duration_step_value']); ?>"/>
												</div>
											</div>								  
										</li>										
										<li>
											<h5><?php esc_html_e('Timepicker','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Timepicker settings: dropdown list status and interval.','chauffeur-booking-system'); ?><br/>
											</span>
											<div class="to-clear-fix">
												<span class="to-legend-field"><?php esc_html_e('Dropdown list status.','chauffeur-booking-system'); ?></span>
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('timepicker_dropdown_list_enable_1'); ?>" name="<?php CHBSHelper::getFormName('timepicker_dropdown_list_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['timepicker_dropdown_list_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('timepicker_dropdown_list_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('timepicker_dropdown_list_enable_0'); ?>" name="<?php CHBSHelper::getFormName('timepicker_dropdown_list_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['timepicker_dropdown_list_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('timepicker_dropdown_list_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>
											</div>
											<div class="to-clear-fix">
												<span class="to-legend-field">
													<?php esc_html_e('Interval - the amount of time, in minutes, between each item in the dropdown.','chauffeur-booking-system'); ?>
													<?php esc_html_e('Allowed are integer values from 1 to 9999.','chauffeur-booking-system'); ?>
												</span>
												<div>
													<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('timepicker_step'); ?>" value="<?php echo esc_attr($this->data['meta']['timepicker_step']); ?>"/>							   
												</div>
											</div>
											<div>
												<span class="to-legend-field">
													<?php esc_html_e('Start time for a current date.','chauffeur-booking-system'); ?>
												</span>		
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('timepicker_today_start_time_type_1'); ?>" name="<?php CHBSHelper::getFormName('timepicker_today_start_time_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['timepicker_today_start_time_type'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('timepicker_today_start_time_type_1'); ?>"><?php esc_html_e('Timepicker starts based on current time','chauffeur-booking-system'); ?></label>
													<input type="radio" value="2" id="<?php CHBSHelper::getFormName('timepicker_today_start_time_type_2'); ?>" name="<?php CHBSHelper::getFormName('timepicker_today_start_time_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['timepicker_today_start_time_type'],2); ?>/>
													<label for="<?php CHBSHelper::getFormName('timepicker_today_start_time_type_2'); ?>"><?php esc_html_e('Timepicker starts based on interval','chauffeur-booking-system'); ?></label>
												</div>		
											</div>
											<div>
												<span class="to-legend-field">
													<?php esc_html_e('Show hours as a range between them.','chauffeur-booking-system'); ?>
												</span>		
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('timepicker_hour_range_enable_1'); ?>" name="<?php CHBSHelper::getFormName('timepicker_hour_range_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['timepicker_hour_range_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('timepicker_hour_range_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('timepicker_hour_range_enable_0'); ?>" name="<?php CHBSHelper::getFormName('timepicker_hour_range_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['timepicker_hour_range_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('timepicker_hour_range_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>		
											</div>	
											<div>
												<span class="to-legend-field">
													<?php esc_html_e('Set field as readonly (customer will not be able to enter own time).','chauffeur-booking-system'); ?>
												</span>		
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('timepicker_field_readonly_1'); ?>" name="<?php CHBSHelper::getFormName('timepicker_field_readonly'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['timepicker_field_readonly'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('timepicker_field_readonly_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('timepicker_field_readonly_0'); ?>" name="<?php CHBSHelper::getFormName('timepicker_field_readonly'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['timepicker_field_readonly'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('timepicker_field_readonly_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>		
											</div>										
										</li>
										<li>
											<h5><?php esc_html_e('Services tab','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Enable or disable services tab on step #1 of booking form if only one service is active.','chauffeur-booking-system'); ?>
											</span>
											<div class="to-clear-fix">
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('service_tab_enable_1'); ?>" name="<?php CHBSHelper::getFormName('service_tab_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['service_tab_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('service_tab_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('service_tab_enable_0'); ?>" name="<?php CHBSHelper::getFormName('service_tab_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['service_tab_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('service_tab_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>								
											</div>
										</li>								
										<li>
											<h5><?php esc_html_e('Icons in fields','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Enable or disable showing icons in fields of booking form.','chauffeur-booking-system'); ?><br/>
											</span>
											<div class="to-clear-fix">
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('icon_field_enable_1'); ?>" name="<?php CHBSHelper::getFormName('icon_field_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['icon_field_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('icon_field_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('icon_field_enable_0'); ?>" name="<?php CHBSHelper::getFormName('icon_field_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['icon_field_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('icon_field_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>
											</div>
										</li>								 
										<li>
											<h5><?php esc_html_e('Visibility of right panel in step #1','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php echo __('Google Maps and ride info visibility.','chauffeur-booking-system'); ?><br/>
												<?php echo __('Please note that this option doesn\'t disable map. It hides map only.','chauffeur-booking-system'); ?>
											</span>
											<div class="to-clear-fix">
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('step_1_right_panel_visibility_1'); ?>" name="<?php CHBSHelper::getFormName('step_1_right_panel_visibility'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['step_1_right_panel_visibility'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('step_1_right_panel_visibility_1'); ?>"><?php esc_html_e('Show','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('step_1_right_panel_visibility_0'); ?>" name="<?php CHBSHelper::getFormName('step_1_right_panel_visibility'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['step_1_right_panel_visibility'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('step_1_right_panel_visibility_0'); ?>"><?php esc_html_e('Hide','chauffeur-booking-system'); ?></label>
												</div>								
											</div>
										</li>	
										<li>
											<h5><?php esc_html_e('Drop-off location in "Hourly" service','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Enable or disable "Drop-off location" field in "Hourly" service type offered.','chauffeur-booking-system'); ?></span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('dropoff_location_field_enable_1'); ?>" name="<?php CHBSHelper::getFormName('dropoff_location_field_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['dropoff_location_field_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('dropoff_location_field_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('dropoff_location_field_enable_0'); ?>" name="<?php CHBSHelper::getFormName('dropoff_location_field_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['dropoff_location_field_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('dropoff_location_field_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li>
										<li>
											<h5><?php esc_html_e('"Use my location" link for pickup location fields','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Enable or disable the visibility of the "Use my location" link for pickup fields in "Distance" and "Hourly" services.','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('This works only if the customer\'s browser has geolocation enabled, otherwise, the link won’t be shown.','chauffeur-booking-system'); ?>
											</span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('use_my_location_link_enable_1'); ?>" name="<?php CHBSHelper::getFormName('use_my_location_link_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['use_my_location_link_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('use_my_location_link_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('use_my_location_link_enable_0'); ?>" name="<?php CHBSHelper::getFormName('use_my_location_link_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['use_my_location_link_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('use_my_location_link_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li> 
										<li>
											<h5><?php esc_html_e('Pickup time field','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Enable or disable possibility of changing time in "Pickup time" field.','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('If it is disabled, customer will not be able to change field value and providing a default time in the business hours is needed.','chauffeur-booking-system'); ?>
											</span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('pickup_time_field_write_enable_1'); ?>" name="<?php CHBSHelper::getFormName('pickup_time_field_write_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['pickup_time_field_write_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('pickup_time_field_write_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('pickup_time_field_write_enable_0'); ?>" name="<?php CHBSHelper::getFormName('pickup_time_field_write_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['pickup_time_field_write_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('pickup_time_field_write_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>	
										</li>
										<li>
											<h5><?php esc_html_e('Vehicles price range','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Enable or disable option to show vehicles price range (after filling the ride details) in the first step.','chauffeur-booking-system'); ?>
											</span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('vehicle_price_calculation_first_step_enable_1'); ?>" name="<?php CHBSHelper::getFormName('vehicle_price_calculation_first_step_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_price_calculation_first_step_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('vehicle_price_calculation_first_step_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('vehicle_price_calculation_first_step_enable_0'); ?>" name="<?php CHBSHelper::getFormName('vehicle_price_calculation_first_step_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_price_calculation_first_step_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('vehicle_price_calculation_first_step_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li> 
									</ul>								
								</div>
								<div id="meta-box-booking-form-1-11-3">		
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('"Choose a Vehicle" step','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Enable or disable the second step ("Choose a Vehicle") in the booking form.','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('Available only if a single vehicle is defined. Vehicle availability settings are ignored in this case.','chauffeur-booking-system'); ?>
											</span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('step_second_enable_1'); ?>" name="<?php CHBSHelper::getFormName('step_second_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['step_second_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('step_second_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('step_second_enable_0'); ?>" name="<?php CHBSHelper::getFormName('step_second_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['step_second_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('step_second_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li> 
										<li>
											<h5><?php esc_html_e('Vehicles description','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Show or hide vehicles description (info) by default in step #2 of booking form.','chauffeur-booking-system'); ?></span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('vehicle_more_info_default_show_1'); ?>" name="<?php CHBSHelper::getFormName('vehicle_more_info_default_show'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_more_info_default_show'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('vehicle_more_info_default_show_1'); ?>"><?php esc_html_e('Show','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('vehicle_more_info_default_show_0'); ?>" name="<?php CHBSHelper::getFormName('vehicle_more_info_default_show'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['vehicle_more_info_default_show'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('vehicle_more_info_default_show_0'); ?>"><?php esc_html_e('Hide','chauffeur-booking-system'); ?></label>
											</div>
										</li>  
										<li>
											<h5><?php esc_html_e('Scroll after selecting a vehicle','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Scroll user to booking add-ons section after selecting a vehicle. If the add-ons are not available, plugin will scroll to the navigation buttons.','chauffeur-booking-system'); ?></span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('scroll_to_booking_extra_after_select_vehicle_enable_1'); ?>" name="<?php CHBSHelper::getFormName('scroll_to_booking_extra_after_select_vehicle_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['scroll_to_booking_extra_after_select_vehicle_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('scroll_to_booking_extra_after_select_vehicle_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('scroll_to_booking_extra_after_select_vehicle_enable_0'); ?>" name="<?php CHBSHelper::getFormName('scroll_to_booking_extra_after_select_vehicle_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['scroll_to_booking_extra_after_select_vehicle_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('scroll_to_booking_extra_after_select_vehicle_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li>  
										<li>
											<h5><?php esc_html_e('Number of passengers on vehicle list','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Enable or disable visibility of passenger number of vehicle on list in step #2.','chauffeur-booking-system'); ?></span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('passenger_number_vehicle_list_enable_1'); ?>" name="<?php CHBSHelper::getFormName('passenger_number_vehicle_list_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_number_vehicle_list_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('passenger_number_vehicle_list_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('passenger_number_vehicle_list_enable_0'); ?>" name="<?php CHBSHelper::getFormName('passenger_number_vehicle_list_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['passenger_number_vehicle_list_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('passenger_number_vehicle_list_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li>  
										<li>
											<h5><?php esc_html_e('Number of suitcases on vehicle list','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Enable or disable visibility of suitcases number of vehicle on list in step #2.','chauffeur-booking-system'); ?></span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('suitcase_number_vehicle_list_enable_1'); ?>" name="<?php CHBSHelper::getFormName('suitcase_number_vehicle_list_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['suitcase_number_vehicle_list_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('suitcase_number_vehicle_list_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('suitcase_number_vehicle_list_enable_0'); ?>" name="<?php CHBSHelper::getFormName('suitcase_number_vehicle_list_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['suitcase_number_vehicle_list_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('suitcase_number_vehicle_list_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li> 
										<li>
											<h5><?php esc_html_e('Booking extras toggle visibility button','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Enable or disable button which allows customers  to toggle booking extras visibility.','chauffeur-booking-system'); ?></span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('booking_extra_button_toggle_visibility_enable_1'); ?>" name="<?php CHBSHelper::getFormName('booking_extra_button_toggle_visibility_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_extra_button_toggle_visibility_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('booking_extra_button_toggle_visibility_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('booking_extra_button_toggle_visibility_enable_0'); ?>" name="<?php CHBSHelper::getFormName('booking_extra_button_toggle_visibility_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_extra_button_toggle_visibility_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('booking_extra_button_toggle_visibility_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li> 
										<li>
											<h5><?php esc_html_e('Booking extras visibility status','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Set default status of booking extras visibility.','chauffeur-booking-system'); ?></br>
												<?php esc_html_e('This option works only if the "Booking extras toggle visibility button" is enabled.','chauffeur-booking-system'); ?>
											</span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('booking_extra_visibility_status_1'); ?>" name="<?php CHBSHelper::getFormName('booking_extra_visibility_status'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_extra_visibility_status'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('booking_extra_visibility_status_1'); ?>"><?php esc_html_e('Show','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('booking_extra_visibility_status_0'); ?>" name="<?php CHBSHelper::getFormName('booking_extra_visibility_status'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['booking_extra_visibility_status'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('booking_extra_visibility_status_0'); ?>"><?php esc_html_e('Hide','chauffeur-booking-system'); ?></label>
											</div>
										</li> 										
									</ul>								
								</div>							
								<div id="meta-box-booking-form-1-11-4">		
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('"Enter contact details" step','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Enable or disable the third step ("Enter contact details") in the booking form.','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('This step can be disabled only if the "Add to WooCommerce cart" option is enabled.','chauffeur-booking-system'); ?>
											</span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('step_third_enable_1'); ?>" name="<?php CHBSHelper::getFormName('step_third_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['step_third_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('step_third_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('step_third_enable_0'); ?>" name="<?php CHBSHelper::getFormName('step_third_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['step_third_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('step_third_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li> 
										<li>
											<h5><?php esc_html_e('Phone number','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Enable or disable showing phone number format and code with flag in the "Phone number" field.','chauffeur-booking-system'); ?>
											</span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('phone_number_iti_library_enable_1'); ?>" name="<?php CHBSHelper::getFormName('phone_number_iti_library_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['phone_number_iti_library_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('phone_number_iti_library_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('phone_number_iti_library_enable_0'); ?>" name="<?php CHBSHelper::getFormName('phone_number_iti_library_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['phone_number_iti_library_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('phone_number_iti_library_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li> 										
										<li>
											<h5><?php esc_html_e('Billing details','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Select default state of billing details section.','chauffeur-booking-system'); ?></span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('billing_detail_state_1'); ?>" name="<?php CHBSHelper::getFormName('billing_detail_state'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['billing_detail_state'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('billing_detail_state_1'); ?>"><?php esc_html_e('Unchecked','chauffeur-booking-system'); ?></label>
												<input type="radio" value="2" id="<?php CHBSHelper::getFormName('billing_detail_state_2'); ?>" name="<?php CHBSHelper::getFormName('billing_detail_state'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['billing_detail_state'],2); ?>/>
												<label for="<?php CHBSHelper::getFormName('billing_detail_state_2'); ?>"><?php esc_html_e('Checked','chauffeur-booking-system'); ?></label>
												<input type="radio" value="3" id="<?php CHBSHelper::getFormName('billing_detail_state_3'); ?>" name="<?php CHBSHelper::getFormName('billing_detail_state'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['billing_detail_state'],3); ?>/>
												<label for="<?php CHBSHelper::getFormName('billing_detail_state_3'); ?>"><?php esc_html_e('Mandatory','chauffeur-booking-system'); ?></label>
												<input type="radio" value="4" id="<?php CHBSHelper::getFormName('billing_detail_state_4'); ?>" name="<?php CHBSHelper::getFormName('billing_detail_state'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['billing_detail_state'],4); ?>/>
												<label for="<?php CHBSHelper::getFormName('billing_detail_state_4'); ?>"><?php esc_html_e('Hidden','chauffeur-booking-system'); ?></label>
											</div>
										</li>
										<li>
											<h5><?php esc_html_e('Fields mandatory','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Select which fields should be marked as mandatory.','chauffeur-booking-system'); ?></span>
											<div class="to-checkbox-button">
<?php
		foreach($this->data['dictionary']['field_mandatory'] as $index=>$value)
		{
?>
												<input type="checkbox" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('field_mandatory_'.$index); ?>" name="<?php CHBSHelper::getFormName('field_mandatory['.$index.']'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['field_mandatory'],$index); ?>/>
												<label for="<?php CHBSHelper::getFormName('field_mandatory_'.$index); ?>"><?php echo esc_html($value['label']); ?></label>
<?php		
		}
?>								
											</div>
										</li> 
									</ul>								
								</div>							
								<div id="meta-box-booking-form-1-11-5">		
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('"Booking summary" step','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Enable or disable the fourth step ("Booking summary") in the booking form.','chauffeur-booking-system'); ?>
											</span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('step_fourth_enable_1'); ?>" name="<?php CHBSHelper::getFormName('step_fourth_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['step_fourth_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('step_fourth_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('step_fourth_enable_0'); ?>" name="<?php CHBSHelper::getFormName('step_fourth_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['step_fourth_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('step_fourth_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li> 									
									</ul>								
								</div>					
								<div id="meta-box-booking-form-1-11-6">		
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('"Thank You" page','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Enable or disable the "Thank You" page in the booking form.','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('This page can be disabled only if WooCommerce support is enabled.','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('In that case, the customer is redirected to the checkout page without confirmation that the order was sent.','chauffeur-booking-system'); ?>
											</span>
											<div class="to-radio-button">
												<input type="radio" value="1" id="<?php CHBSHelper::getFormName('thank_you_page_enable_1'); ?>" name="<?php CHBSHelper::getFormName('thank_you_page_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['thank_you_page_enable'],1); ?>/>
												<label for="<?php CHBSHelper::getFormName('thank_you_page_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
												<input type="radio" value="0" id="<?php CHBSHelper::getFormName('thank_you_page_enable_0'); ?>" name="<?php CHBSHelper::getFormName('thank_you_page_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['thank_you_page_enable'],0); ?>/>
												<label for="<?php CHBSHelper::getFormName('thank_you_page_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
											</div>
										</li> 
										<li>
											<h5><?php esc_html_e('"Back to home" button on "Thank you" page','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Enter URL address and label for this button.','chauffeur-booking-system'); ?>
											</span>
											<div>
												<span class="to-legend-field"><?php esc_html_e('Label.','chauffeur-booking-system'); ?></span>
												<div>
													<input type="text" name="<?php CHBSHelper::getFormName('thank_you_page_button_back_to_home_label'); ?>" value="<?php echo esc_attr($this->data['meta']['thank_you_page_button_back_to_home_label']); ?>"/>
												</div>					 
											</div>
											<div>
												<span class="to-legend-field"><?php esc_html_e('URL address.','chauffeur-booking-system'); ?></span>
												<div>
													<input type="text" name="<?php CHBSHelper::getFormName('thank_you_page_button_back_to_home_url_address'); ?>" value="<?php echo esc_attr($this->data['meta']['thank_you_page_button_back_to_home_url_address']); ?>"/>
												</div>					 
											</div>
										</li>
										<li>
											<h5><?php esc_html_e('After submitting booking redirect','chauffeur-booking-system'); ?></h5>
											<span class="to-legend">
												<?php esc_html_e('Enter URL address of the page on which customer has to be redirected.','chauffeur-booking-system'); ?><br/>
												<?php esc_html_e('This option works only if no payment has been selected/enabled.','chauffeur-booking-system'); ?>
											</span>
											<div>
												<input type="text" name="<?php CHBSHelper::getFormName('payment_disable_success_url_address'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_disable_success_url_address']); ?>"/>
											</div>
										</li>										
									</ul>								
								</div>	
							</div>
						</div>
					</div>
				</div>
				<div id="meta-box-booking-form-2">
					<ul class="to-form-field-list">
						<li>
							<h5><?php esc_html_e('Business hours','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Specify working days/hours.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('Leave all fields empty if booking is not available for selected day.','chauffeur-booking-system'); ?>
							</span> 
							<div class="to-clear-fix">
								<table class="to-table">
									<tr>
										<th style="width:25%">
											<div>
												<?php esc_html_e('Weekday','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Day of the week.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:25%">
											<div>
												<?php esc_html_e('Start Time','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Start time.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:25%">
											<div>
												<?php esc_html_e('End Time','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('End time.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:25%">
											<div>
												<?php esc_html_e('Default Time','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Default time.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
									</tr>
<?php
		for($i=1;$i<8;$i++)
		{
?>
									<tr>
										<td>
											<div><?php echo $Date->getDayName($i); ?></div>
										</td>
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-timepicker-custom" name="<?php CHBSHelper::getFormName('business_hour['.$i.'][0]'); ?>" value="<?php echo esc_attr($Date->formatTimeToDisplay($this->data['meta']['business_hour'][$i]['start'])); ?>" title="<?php esc_attr_e('Enter start time.','chauffeur-booking-system'); ?>"/>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">							
												<input type="text" class="to-timepicker-custom" name="<?php CHBSHelper::getFormName('business_hour['.$i.'][1]'); ?>" value="<?php echo esc_attr($Date->formatTimeToDisplay($this->data['meta']['business_hour'][$i]['stop'])); ?>" title="<?php esc_attr_e('Enter end time.','chauffeur-booking-system'); ?>"/>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">								
												<input type="text" class="to-timepicker-custom" name="<?php CHBSHelper::getFormName('business_hour['.$i.'][2]'); ?>" value="<?php echo esc_attr($Date->formatTimeToDisplay($this->data['meta']['business_hour'][$i]['default'])); ?>" title="<?php esc_attr_e('Enter default time.','chauffeur-booking-system'); ?>"/>
											</div>
										</td>
									</tr>
<?php
		}
?>
								</table>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Exclude dates','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Specify dates not available for booking. Past (or invalid date ranges) will be removed during saving.','chauffeur-booking-system'); ?></span>
							<div>	
								<table class="to-table" id="to-table-availability-exclude-date">
									<tr>
										<th style="width:40%">
											<div>
												<?php esc_html_e('Start Date','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Start date.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:40%">
											<div>
												<?php esc_html_e('End Date','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('End date.','chauffeur-booking-system'); ?>
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
												<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('date_exclude_start[]'); ?>" title="<?php esc_attr_e('Enter start date.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>
										<td>
											<div>
												<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('date_exclude_stop[]'); ?>" title="<?php esc_attr_e('Enter start date.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>	
										<td>
											<div>
												<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
											</div>
										</td>
									</tr>
<?php
		if(count($this->data['meta']['date_exclude']))
		{
			foreach($this->data['meta']['date_exclude'] as $dateExcludeIndex=>$dateExcludeValue)
			{
?>
									<tr>
										<td>
											<div>
												<input type="text" class="to-datepicker-custom" value="<?php echo esc_attr($Date->formatDateToDisplay($dateExcludeValue['start'])); ?>" name="<?php CHBSHelper::getFormName('date_exclude_start[]'); ?>" title="<?php esc_attr_e('Enter start date.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>
										<td>
											<div>
												<input type="text" class="to-datepicker-custom" value="<?php echo esc_attr($Date->formatDateToDisplay($dateExcludeValue['stop'])); ?>" name="<?php CHBSHelper::getFormName('date_exclude_stop[]'); ?>" title="<?php esc_attr_e('Enter start date.','chauffeur-booking-system'); ?>"/>
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
?>
								</table>
								<div> 
									<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
								</div>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Maximum number of bookings','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Specify maximum number of bookings for a defined: range of date/time, date, time, day of week.','chauffeur-booking-system'); ?></span>
							<div class="to-table-maximum-booking-number">	
								<table class="to-table" id="to-table-maximum-booking-number">
									<tr>
										<th style="width:10%">
											<div>
												<?php esc_html_e('Time unit','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Time unit.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:10%">
											<div>
												<?php esc_html_e('Start date','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Start date.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:10%">
											<div>
												<?php esc_html_e('Start time','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Start time.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:10%">
											<div>
												<?php esc_html_e('Stop date','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Stop date.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:10%">
											<div>
												<?php esc_html_e('Stop time','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Stop time.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:10%">
											<div>
												<?php esc_html_e('Day of week','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Day of week.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>	
										<th style="width:20%">
											<div>
												<?php esc_html_e('Number of the bookings for vehicles','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Number of the bookings for vehicles.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>	
										<th style="width:10%">
											<div>
												<?php esc_html_e('Number of the bookings','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Number of the bookings.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>	
										<th style="width:10%">
											<div>
												<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Remove.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
									</tr>
									<tr class="to-hidden">									
										<td>
											<div class="to-clear-fix">
												<select class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('maximum_booking_number[time_unit][]'); ?>" id="maximum_booking_number_time_unit">
<?php
		foreach($this->data['dictionary']['maximum_booking_number_time_unit'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'">'.esc_html($value[0]).'</option>';
?>
												</select>  
											</div>									
										</td>										
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('maximum_booking_number[date_start][]'); ?>" title="<?php esc_attr_e('Enter start date.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>	
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-timepicker-custom" name="<?php CHBSHelper::getFormName('maximum_booking_number[time_start][]'); ?>" title="<?php esc_attr_e('Enter start time.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>										
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('maximum_booking_number[date_stop][]'); ?>" title="<?php esc_attr_e('Enter stop date.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>	
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-timepicker-custom" name="<?php CHBSHelper::getFormName('maximum_booking_number[time_stop][]'); ?>" title="<?php esc_attr_e('Enter stop time.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>										
										<td>
											<div class="to-clear-fix">
												<select class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('maximum_booking_number[week_day_number][]'); ?>" id="maximum_booking_number_week_day_number">
													<option value="0"><?php esc_html_e('[None]','chauffeur-booking-system'); ?></option>
<?php
		for($i=1;$i<8;$i++)
			echo '<option value="'.esc_attr($i).'">'.esc_html($Date->getDayName($i)).'</option>';
?>
												</select>  												
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
<?php
		if(count($this->data['dictionary']['vehicle']))
		{
?>
												<table class="to-table">
													<tr>
														<th style="width:70%">
															<div class="to-clear-fix">
																<?php esc_html_e('Vehicle','chauffeur-booking-system') ?>
															</div>
														</th>
														<th style="width:70%">
															<div class="to-clear-fix">
																<?php esc_html_e('Number','chauffeur-booking-system') ?>
															</div>
														</th>														
													</tr>
<?php
			foreach($this->data['dictionary']['vehicle'] as $vehicleIndex=>$vehicleValue)
			{
?>				
													<tr>
														<td>
															<div class="to-clear-fix">
																<?php echo esc_html($vehicleValue['post']->post_title); ?>
															</div>
														</td>
														<td>
															<div class="to-clear-fix">
																<input type="text" value="-1" name="<?php CHBSHelper::getFormName('maximum_booking_number[vehicle]['.$vehicleIndex.'][number][]'); ?>" maxlength="9"/>
															</div>
														</td>														
													</tr>
<?php				
			}
?>
												</table>
<?php
		}
?>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('maximum_booking_number[number][]'); ?>"  maxlength="9"/>
											</div>									
										</td>	
										<td>
											<div class="to-clear-fix">
												<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
											</div>
										</td>
									</tr>
<?php
		if(count($this->data['meta']['maximum_booking_number']))
		{
			foreach($this->data['meta']['maximum_booking_number'] as $maximumBookingNumberIndex=>$maximumBookingNumberValue)
			{
?>
									<tr>
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('maximum_booking_number[time_unit][]'); ?>" id="maximum_booking_number_time_unit_<?php echo esc_attr($maximumBookingNumberIndex); ?>">
<?php
				foreach($this->data['dictionary']['maximum_booking_number_time_unit'] as $index=>$value)
					echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($maximumBookingNumberValue['time_unit'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>  												
											</div>									
										</td>	
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-datepicker-custom" value="<?php echo esc_attr($Date->formatDateToDisplay($maximumBookingNumberValue['date_start'])); ?>" name="<?php CHBSHelper::getFormName('maximum_booking_number[date_start][]'); ?>" title="<?php esc_attr_e('Enter start date.','chauffeur-booking-system'); ?>"/>												
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-timepicker-custom" value="<?php echo esc_attr($Date->formatTimeToDisplay($maximumBookingNumberValue['time_start'])); ?>" name="<?php CHBSHelper::getFormName('maximum_booking_number[time_start][]'); ?>" title="<?php esc_attr_e('Enter start time.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-datepicker-custom" value="<?php echo esc_attr($Date->formatDateToDisplay($maximumBookingNumberValue['date_stop'])); ?>" name="<?php CHBSHelper::getFormName('maximum_booking_number[date_stop][]'); ?>" title="<?php esc_attr_e('Enter stop date.','chauffeur-booking-system'); ?>"/>												
											</div>									
										</td>										
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-timepicker-custom" value="<?php echo esc_attr($Date->formatTimeToDisplay($maximumBookingNumberValue['time_stop'])); ?>" name="<?php CHBSHelper::getFormName('maximum_booking_number[time_stop][]'); ?>" title="<?php esc_attr_e('Enter stop time.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('maximum_booking_number[week_day_number][]'); ?>" id="maximum_booking_number_week_day_number_<?php echo esc_attr($maximumBookingNumberIndex); ?>">
													<option value="0" <?php echo CHBSHelper::selectedIf($maximumBookingNumberValue['week_day_number'],0,false); ?>><?php esc_html_e('[None]','chauffeur-booking-system'); ?></option>
<?php
				for($i=1;$i<8;$i++)
					echo '<option value="'.esc_attr($i).'"'.(CHBSHelper::selectedIf($maximumBookingNumberValue['week_day_number'],$i,false)).'>'.esc_html($Date->getDayName($i)).'</option>';
?>
												</select>  													
											</div>									
										</td>	
										<td>
											<div class="to-clear-fix">
<?php
				if(count($this->data['dictionary']['vehicle']))
				{
?>
												<table class="to-table">
													<tr>
														<th style="width:70%">
															<div class="to-clear-fix">
																<?php esc_html_e('Vehicle','chauffeur-booking-system') ?>
															</div>
														</th>
														<th style="width:70%">
															<div class="to-clear-fix">
																<?php esc_html_e('Number','chauffeur-booking-system') ?>
															</div>
														</th>														
													</tr>
<?php
					foreach($this->data['dictionary']['vehicle'] as $vehcicleIndex=>$vehicleValue)
					{
?>				
													<tr>
														<td>
															<div class="to-clear-fix">
																<?php echo esc_html($vehicleValue['post']->post_title); ?>
															</div>
														</td>
														<td>
															<div class="to-clear-fix">
																<input type="text" name="<?php CHBSHelper::getFormName('maximum_booking_number[vehicle]['.$vehcicleIndex.'][number][]'); ?>" value="<?php echo esc_attr($maximumBookingNumberValue['vehicle'][$vehcicleIndex]);  ?>" maxlength="9"/>
															</div>
														</td>														
													</tr>
<?php				
			}
?>
												</table>
<?php
		}
?>
											</div>									
										</td>										
										<td>
											<div class="to-clear-fix">
												<input type="text" value="<?php echo esc_attr($maximumBookingNumberValue['number']); ?>" name="<?php CHBSHelper::getFormName('maximum_booking_number[number][]'); ?>" maxlength="9"/>
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
?>
								</table>
								<div> 
									<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
								</div>
							</div>
						</li>
					</ul>
				</div>	
				<div id="meta-box-booking-form-3">
					<div class="ui-tabs">
						<ul>
							<li><a href="#meta-box-booking-form-3-1"><?php esc_html_e('General','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-3-2"><?php esc_html_e('Deposit','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-3-3"><?php esc_html_e('Payments','chauffeur-booking-system'); ?></a></li>
						</ul>
						<div id="meta-box-booking-form-3-1">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Payment','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Select one or more built-in payment methods for this booking form. Some methods may require additional configuration.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('These methods are not available when WooCommerce support is enabled.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-checkbox-button">
<?php
		foreach($this->data['dictionary']['payment'] as $index=>$value)
		{
?>
										<input type="checkbox" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('payment_id_'.$index); ?>" name="<?php CHBSHelper::getFormName('payment_id[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_id'],$index); ?>/>
										<label for="<?php CHBSHelper::getFormName('payment_id_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>							
<?php		
		}
?>
									</div>	
								</li>
								<li>
									<h5><?php esc_html_e('Default payment','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Select default payment method.','chauffeur-booking-system'); ?>
									</span>
									<div>
										<select name="<?php CHBSHelper::getFormName('payment_default_id'); ?>">
											<option value="-1" <?php CHBSHelper::selectedIf($this->data['meta']['payment_default_id'],-1); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['payment'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['payment_default_id'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
										</select>  
									</div>	
								</li>
								<li>
									<h5><?php esc_html_e('Payment selection','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Require customer to select a payment method.','chauffeur-booking-system'); ?></span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('payment_mandatory_enable_1'); ?>" name="<?php CHBSHelper::getFormName('payment_mandatory_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_mandatory_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('payment_mandatory_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('payment_mandatory_enable_0'); ?>" name="<?php CHBSHelper::getFormName('payment_mandatory_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_mandatory_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('payment_mandatory_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li>
								<li>
									<h5><?php esc_html_e('Payment processing','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable payment via the booking form.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('If disabled, the customer can select a payment method but won\'t be able to complete the payment.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('payment_processing_enable_1'); ?>" name="<?php CHBSHelper::getFormName('payment_processing_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_processing_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('payment_processing_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('payment_processing_enable_0'); ?>" name="<?php CHBSHelper::getFormName('payment_processing_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_processing_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('payment_processing_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li>	 
								<li>
									<h5><?php esc_html_e('WooCommerce payments on step #3','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable the ability to choose a WooCommerce payment method in step #3.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Available only if WooCommerce support is enabled.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('payment_woocommerce_step_3_enable_1'); ?>" name="<?php CHBSHelper::getFormName('payment_woocommerce_step_3_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_woocommerce_step_3_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('payment_woocommerce_step_3_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('payment_woocommerce_step_3_enable_0'); ?>" name="<?php CHBSHelper::getFormName('payment_woocommerce_step_3_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_woocommerce_step_3_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('payment_woocommerce_step_3_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>
								</li> 	
							</ul>
						</div>
						<div id="meta-box-booking-form-3-2">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Deposit','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Enable or disable deposit payment.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Not available if WooCommerce support is enabled.','chauffeur-booking-system'); ?><br/>
									</span>
									<div class="to-clear-fix">
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('payment_deposit_enable_1'); ?>" name="<?php CHBSHelper::getFormName('payment_deposit_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_deposit_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('payment_deposit_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('payment_deposit_enable_0'); ?>" name="<?php CHBSHelper::getFormName('payment_deposit_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_deposit_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('payment_deposit_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>									
									</div>							
								</li>
								<li>
									<h5><?php esc_html_e('Percentage value of the deposit','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Percentage value of the deposit.','chauffeur-booking-system'); ?></span>
									<div class="to-clear-fix">
										<div id="<?php CHBSHelper::getFormName('payment_deposit_value'); ?>"></div>
										<input type="text" name="<?php CHBSHelper::getFormName('payment_deposit_value'); ?>" id="<?php CHBSHelper::getFormName('payment_deposit_value'); ?>" class="to-slider-range" readonly/>
									</div>								
								</li>
							</ul>
						</div>
						<div id="meta-box-booking-form-3-3">
							<div class="ui-tabs">
								<ul>
									<li><a href="#meta-box-booking-form-3-3-1"><?php esc_html_e('Stripe','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-booking-form-3-3-2"><?php esc_html_e('PayPal','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-booking-form-3-3-6"><?php esc_html_e('Tpay','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-booking-form-3-3-3"><?php esc_html_e('Cash','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-booking-form-3-3-4"><?php esc_html_e('Wire transfer','chauffeur-booking-system'); ?></a></li>
									<li><a href="#meta-box-booking-form-3-3-5"><?php esc_html_e('Credit card on pickup','chauffeur-booking-system'); ?></a></li>
									<?php echo apply_filters(PLUGIN_CHBS_CONTEXT.'_admin_tab_filter',null,$post,'payment_tab'); ?>
								</ul>
								<div id="meta-box-booking-form-3-3-1">
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('Secret API key','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php echo sprintf(__('You can find more about keys <a href="%s" target="_blank">here</a>.','chauffeur-booking-system'),'https://stripe.com/docs/keys'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_stripe_api_key_secret'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_stripe_api_key_secret']); ?>"/>
											</div>											
										</li>
										<li>
											<h5><?php esc_html_e('Publishable API key','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php echo sprintf(__('You can find more about keys <a href="%s" target="_blank">here</a>.','chauffeur-booking-system'),'https://stripe.com/docs/keys'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_stripe_api_key_publishable'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_stripe_api_key_publishable']); ?>"/>
											</div>											
										</li>										
										<li>
											<h5><?php esc_html_e('Payment methods','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('You can set up each of them in your "Stripe" dashboard under "Settings / Payment methods".','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<div class="to-checkbox-button">
<?php
		foreach($this->data['dictionary']['payment_stripe_method'] as $index=>$value)
		{
?>
												<input type="checkbox" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('payment_stripe_method_'.$index); ?>" name="<?php CHBSHelper::getFormName('payment_stripe_method[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_stripe_method'],$index); ?>/>
												<label for="<?php CHBSHelper::getFormName('payment_stripe_method_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>							
<?php		
		}
?>
												</div>	
											</div>											
										</li>												
										<li>
											<h5><?php esc_html_e('Product ID','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Product ID.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_stripe_product_id'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_stripe_product_id']); ?>"/>
											</div>											
										</li>											
										<li>
											<h5><?php esc_html_e('Redirection delay','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Duration of redirection delay (in seconds) to the Stripe gateway.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('payment_stripe_redirect_duration'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_stripe_redirect_duration']); ?>"/>
											</div>												
										</li>											
										<li>
											<h5><?php esc_html_e('"Success" URL address','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('"Success" URL address.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_stripe_success_url_address'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_stripe_success_url_address']); ?>"/>
											</div>											
										</li>	
										<li>
											<h5><?php esc_html_e('"Cancel" URL address','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('"Cancel" URL address.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_stripe_cancel_url_address'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_stripe_cancel_url_address']); ?>"/>
											</div>											
										</li>	
										<li>
											<h5><?php esc_html_e('Logo','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Logo.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_stripe_logo_src'); ?>" id="<?php CHBSHelper::getFormName('payment_stripe_logo_src'); ?>" class="to-float-left" value="<?php echo esc_attr($this->data['meta']['payment_stripe_logo_src']); ?>"/>
												<input type="button" name="<?php CHBSHelper::getFormName('payment_stripe_logo_src_browse'); ?>" id="<?php CHBSHelper::getFormName('payment_stripe_logo_src_browse'); ?>" class="to-button-browse to-button" value="<?php esc_attr_e('Browse','chauffeur-booking-system'); ?>"/>
											</div>											
										</li>	
										<li>
											<h5><?php esc_html_e('Information for customer','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Additional information for customer.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<textarea rows="1" cols="1" name="<?php CHBSHelper::getFormName('payment_stripe_info'); ?>"><?php echo esc_html($this->data['meta']['payment_stripe_info']); ?></textarea>
											</div>											
										</li>	
									</ul>
								</div>
								<div id="meta-box-booking-form-3-3-2">
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('E-mail address','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('E-mail address.','chauffeur-booking-system'); ?></span>			
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_paypal_email_address'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_paypal_email_address']); ?>"/>
											</div>											
										</li>									
										<li>
											<h5><?php esc_html_e('Sandbox mode','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Sandbox mode.','chauffeur-booking-system'); ?></span>										
											<div class="to-clear-fix">
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('payment_paypal_sandbox_mode_enable_1'); ?>" name="<?php CHBSHelper::getFormName('payment_paypal_sandbox_mode_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_paypal_sandbox_mode_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('payment_paypal_sandbox_mode_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('payment_paypal_sandbox_mode_enable_0'); ?>" name="<?php CHBSHelper::getFormName('payment_paypal_sandbox_mode_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_paypal_sandbox_mode_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('payment_paypal_sandbox_mode_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>
											</div>											
										</li>										
										<li>
											<h5><?php esc_html_e('Redirection delay','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Duration of redirection delay (in seconds) to the PayPal gateway.','chauffeur-booking-system'); ?></span>													
											<div class="to-clear-fix">
												<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('payment_paypal_redirect_duration'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_paypal_redirect_duration']); ?>"/>
											</div>												
										</li>										
										<li>
											<h5><?php esc_html_e('"Success" URL address','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('"Success" URL address.','chauffeur-booking-system'); ?></span>													
											<div class="to-clear-fix">
												 <input type="text" name="<?php CHBSHelper::getFormName('payment_paypal_success_url_address'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_paypal_success_url_address']); ?>"/>
											 </div>											
										</li>	
										<li>
											<h5><?php esc_html_e('"Cancel" URL address','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('"Cancel" URL address.','chauffeur-booking-system'); ?></span>													
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_paypal_cancel_url_address'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_paypal_cancel_url_address']); ?>"/>
											</div>												
										</li>	
										<li>
											<h5><?php esc_html_e('Logo','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Logo.','chauffeur-booking-system'); ?></span>													
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_paypal_logo_src'); ?>" id="<?php CHBSHelper::getFormName('payment_paypal_logo_src'); ?>" class="to-float-left" value="<?php echo esc_attr($this->data['meta']['payment_paypal_logo_src']); ?>"/>
												<input type="button" name="<?php CHBSHelper::getFormName('payment_paypal_logo_src_browse'); ?>" id="<?php CHBSHelper::getFormName('payment_paypal_logo_src_browse'); ?>" class="to-button-browse to-button" value="<?php esc_attr_e('Browse','chauffeur-booking-system'); ?>"/>
											</div>											
										</li>	
										<li>
											<h5><?php esc_html_e('Information for customer','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Additional information for customer.','chauffeur-booking-system'); ?></span>													
											<div class="to-clear-fix">
												<textarea rows="1" cols="1" name="<?php CHBSHelper::getFormName('payment_paypal_info'); ?>"><?php echo esc_html($this->data['meta']['payment_paypal_info']); ?></textarea>
											</div>											
										</li>	
									</ul>
								</div>
								<div id="meta-box-booking-form-3-3-6">
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('Client ID','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('OAuth client ID from the Tpay panel.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_tpay_client_id'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_tpay_client_id']); ?>"/>
											</div>
										</li>
										<li>
											<h5><?php esc_html_e('Client secret','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('OAuth client secret from the Tpay panel.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_tpay_client_secret'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_tpay_client_secret']); ?>"/>
											</div>
										</li>
										<li>
											<h5><?php esc_html_e('Notification secret','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Secret for validating Tpay notifications (optional).','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_tpay_notification_secret'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_tpay_notification_secret']); ?>"/>
											</div>
										</li>
										<li>
											<h5><?php esc_html_e('Sandbox mode','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Sandbox mode.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('payment_tpay_sandbox_mode_enable_1'); ?>" name="<?php CHBSHelper::getFormName('payment_tpay_sandbox_mode_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_tpay_sandbox_mode_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('payment_tpay_sandbox_mode_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('payment_tpay_sandbox_mode_enable_0'); ?>" name="<?php CHBSHelper::getFormName('payment_tpay_sandbox_mode_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['payment_tpay_sandbox_mode_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('payment_tpay_sandbox_mode_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>
											</div>
										</li>
										<li>
											<h5><?php esc_html_e('Redirection delay','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Duration of redirection delay (in seconds) to the Tpay gateway.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('payment_tpay_redirect_duration'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_tpay_redirect_duration']); ?>"/>
											</div>
										</li>
										<li>
											<h5><?php esc_html_e('"Success" URL address','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('"Success" URL address.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_tpay_success_url_address'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_tpay_success_url_address']); ?>"/>
											</div>
										</li>
										<li>
											<h5><?php esc_html_e('"Cancel" URL address','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('"Cancel" URL address.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_tpay_cancel_url_address'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_tpay_cancel_url_address']); ?>"/>
											</div>
										</li>
										<li>
											<h5><?php esc_html_e('Logo','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Logo.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_tpay_logo_src'); ?>" id="<?php CHBSHelper::getFormName('payment_tpay_logo_src'); ?>" class="to-float-left" value="<?php echo esc_attr($this->data['meta']['payment_tpay_logo_src']); ?>"/>
												<input type="button" name="<?php CHBSHelper::getFormName('payment_tpay_logo_src_browse'); ?>" id="<?php CHBSHelper::getFormName('payment_tpay_logo_src_browse'); ?>" class="to-button-browse to-button" value="<?php esc_attr_e('Browse','chauffeur-booking-system'); ?>"/>
											</div>
										</li>
										<li>
											<h5><?php esc_html_e('Information for customer','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Additional information for customer.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<textarea rows="1" cols="1" name="<?php CHBSHelper::getFormName('payment_tpay_info'); ?>"><?php echo esc_html($this->data['meta']['payment_tpay_info']); ?></textarea>
											</div>
										</li>
									</ul>
								</div>
								<div id="meta-box-booking-form-3-3-3">
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('"Success" URL address','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('"Success" URL address.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_cash_success_url_address'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_cash_success_url_address']); ?>"/>
											</div>											
										</li>	
										<li>
											<h5><?php esc_html_e('Logo','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Logo.','chauffeur-booking-system'); ?></span>													
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_cash_logo_src'); ?>" id="<?php CHBSHelper::getFormName('payment_cash_logo_src'); ?>" class="to-float-left" value="<?php echo esc_attr($this->data['meta']['payment_cash_logo_src']); ?>"/>
												<input type="button" name="<?php CHBSHelper::getFormName('payment_cash_logo_src_browse'); ?>" id="<?php CHBSHelper::getFormName('payment_cash_logo_src_browse'); ?>" class="to-button-browse to-button" value="<?php esc_attr_e('Browse','chauffeur-booking-system'); ?>"/>
											</div>											
										</li>									
										<li>
											<h5><?php esc_html_e('Information for customer','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Additional information for customer.','chauffeur-booking-system'); ?></span>													
											<div class="to-clear-fix">
												<textarea rows="1" cols="1" name="<?php CHBSHelper::getFormName('payment_cash_info'); ?>"><?php echo esc_html($this->data['meta']['payment_cash_info']); ?></textarea>
											</div>											
										</li>										
									</ul>									
								</div>
								<div id="meta-box-booking-form-3-3-4">
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('"Success" URL address','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('"Success" URL address.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_wire_transfer_success_url_address'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_wire_transfer_success_url_address']); ?>"/>
											</div>											
										</li>	
										<li>
											<h5><?php esc_html_e('Logo','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Logo.','chauffeur-booking-system'); ?></span>													
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_wire_transfer_logo_src'); ?>" id="<?php CHBSHelper::getFormName('payment_wire_transfer_logo_src'); ?>" class="to-float-left" value="<?php echo esc_attr($this->data['meta']['payment_wire_transfer_logo_src']); ?>"/>
												<input type="button" name="<?php CHBSHelper::getFormName('payment_wire_transfer_logo_src_browse'); ?>" id="<?php CHBSHelper::getFormName('payment_wire_transfer_logo_src_browse'); ?>" class="to-button-browse to-button" value="<?php esc_attr_e('Browse','chauffeur-booking-system'); ?>"/>
											</div>											
										</li>									
										<li>
											<h5><?php esc_html_e('Information for customer','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Additional information for customer.','chauffeur-booking-system'); ?></span>													
											<div class="to-clear-fix">
												<textarea rows="1" cols="1" name="<?php CHBSHelper::getFormName('payment_wire_transfer_info'); ?>"><?php echo esc_html($this->data['meta']['payment_wire_transfer_info']); ?></textarea>
											</div>											
										</li>										
									</ul>									
								</div>	
								<div id="meta-box-booking-form-3-3-5">
									<ul class="to-form-field-list">
										<li>
											<h5><?php esc_html_e('"Success" URL address','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('"Success" URL address.','chauffeur-booking-system'); ?></span>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_credit_card_pickup_success_url_address'); ?>" value="<?php echo esc_attr($this->data['meta']['payment_credit_card_pickup_success_url_address']); ?>"/>
											</div>											
										</li>	
										<li>
											<h5><?php esc_html_e('Logo','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Logo.','chauffeur-booking-system'); ?></span>													
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('payment_credit_card_pickup_logo_src'); ?>" id="<?php CHBSHelper::getFormName('payment_credit_card_pickup_logo_src'); ?>" class="to-float-left" value="<?php echo esc_attr($this->data['meta']['payment_credit_card_pickup_logo_src']); ?>"/>
												<input type="button" name="<?php CHBSHelper::getFormName('payment_credit_card_pickup_logo_src_browse'); ?>" id="<?php CHBSHelper::getFormName('payment_credit_card_pickup_logo_src_browse'); ?>" class="to-button-browse to-button" value="<?php esc_attr_e('Browse','chauffeur-booking-system'); ?>"/>
											</div>											
										</li>									
										<li>
											<h5><?php esc_html_e('Information for customer','chauffeur-booking-system'); ?></h5>
											<span class="to-legend"><?php esc_html_e('Additional information for customer.','chauffeur-booking-system'); ?></span>													
											<div class="to-clear-fix">
												<textarea rows="1" cols="1" name="<?php CHBSHelper::getFormName('payment_credit_card_pickup_info'); ?>"><?php echo esc_html($this->data['meta']['payment_credit_card_pickup_info']); ?></textarea>
											</div>											
										</li>										
									</ul>									
								</div>
								<?php echo apply_filters(PLUGIN_CHBS_CONTEXT.'_admin_tab_panel_filter',null,$post,'payment_tab_panel'); ?>
							</div>
						</div>
					</div>
				</div>
				<div id="meta-box-booking-form-4">
				   <ul class="to-form-field-list"> 
						<li>
							<h5><?php esc_html_e('Driving zone','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Enable or disable driving zone restriction to selected areas.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('Available for "Distance" and "Hourly" service types only.','chauffeur-booking-system'); ?><br/>
							</span>   
							<div class="to-clear-fix">
								<table class="to-table">
									<tr>
										<th style="width:25%">
											<div></div>
										</th>
										<th style="width:25%">
											<div>
												<?php esc_html_e('Pickup location','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Settings for pickup location.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:25%">
											<div>
												<?php esc_html_e('Waypoints','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Settings for waypoints.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:25%">
											<div>
												<?php esc_html_e('Drop-off location','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Settings for return location.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
									</tr>								
									<tr>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Status','chauffeur-booking-system'); ?>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_enable_1'); ?>" name="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['driving_zone_restriction_pickup_location_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_enable_0'); ?>" name="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['driving_zone_restriction_pickup_location_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>												
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_enable_1'); ?>" name="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['driving_zone_restriction_waypoint_location_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_enable_0'); ?>" name="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['driving_zone_restriction_waypoint_location_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>												
											</div>
										</td>											
										<td>
											<div class="to-clear-fix">
												<div class="to-radio-button">
													<input type="radio" value="1" id="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_enable_1'); ?>" name="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['driving_zone_restriction_dropoff_location_enable'],1); ?>/>
													<label for="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
													<input type="radio" value="0" id="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_enable_0'); ?>" name="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['driving_zone_restriction_dropoff_location_enable'],0); ?>/>
													<label for="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
												</div>												
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Restriction to country','chauffeur-booking-system'); ?><br>
												<span class="to-legend-field"><?php esc_html_e('Select (max. 5) countries.','chauffeur-booking-system'); ?></span>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_country[]'); ?>" id="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_country'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['driving_zone_restriction_pickup_location_country'],-1,false)).'>'.esc_html__(' - Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['country'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['driving_zone_restriction_pickup_location_country'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>  
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_country[]'); ?>" id="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_country'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['driving_zone_restriction_waypoint_location_country'],-1,false)).'>'.esc_html__(' - Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['country'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['driving_zone_restriction_waypoint_location_country'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>  
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<select multiple="multiple" class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_country[]'); ?>" id="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_country'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['driving_zone_restriction_dropoff_location_country'],-1,false)).'>'.esc_html__(' - Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['country'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['driving_zone_restriction_dropoff_location_country'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>												  
											</div>
										</td>
									</tr>									  
									<tr>
										<td>
											<div class="to-clear-fix">
												<?php esc_html_e('Restriction to area','chauffeur-booking-system'); ?><br>
												<span class="to-legend-field"><?php echo esc_html(sprintf(__('Address and radius in %s.','chauffeur-booking-system'),$Length->label(-1,7))); ?></span>
											</div>
										</td>
<?php
		$radius=$this->data['meta']['driving_zone_restriction_pickup_location_area_radius'];
		if(CHBSOption::getOption('length_unit')==2) $radius=$Length->convertUnit($radius,1,2);
?>
										<td>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_area'); ?>" id="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_area'); ?>" value="<?php echo esc_attr($this->data['meta']['driving_zone_restriction_pickup_location_area']); ?>"/>
												<input type="text" name="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_area_radius'); ?>" value="<?php echo esc_attr($radius); ?>" maxlength="8" class="to-margin-top-10"/>
												<input type="hidden" name="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_area_coordinate_lat'); ?>" id="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_area_coordinate_lat'); ?>" value="<?php echo esc_attr($this->data['meta']['driving_zone_restriction_pickup_location_area_coordinate_lat']); ?>" class="to-coordinate-lat"/>
												<input type="hidden" name="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_area_coordinate_lng'); ?>" id="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_area_coordinate_lng'); ?>" value="<?php echo esc_attr($this->data['meta']['driving_zone_restriction_pickup_location_area_coordinate_lng']); ?>" class="to-coordinate-lng"/>
											</div>
										</td>	
<?php
		$radius=$this->data['meta']['driving_zone_restriction_waypoint_location_area_radius'];
		if(CHBSOption::getOption('length_unit')==2) $radius=$Length->convertUnit($radius,1,2);
?>
										<td>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_area'); ?>" id="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_area'); ?>" value="<?php echo esc_attr($this->data['meta']['driving_zone_restriction_waypoint_location_area']); ?>"/>
												<input type="text" name="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_area_radius'); ?>" value="<?php echo esc_attr($radius); ?>" maxlength="8" class="to-margin-top-10"/>
												<input type="hidden" name="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_area_coordinate_lat'); ?>" id="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_area_coordinate_lat'); ?>" value="<?php echo esc_attr($this->data['meta']['driving_zone_restriction_waypoint_location_area_coordinate_lat']); ?>" class="to-coordinate-lat"/>
												<input type="hidden" name="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_area_coordinate_lng'); ?>" id="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_area_coordinate_lng'); ?>" value="<?php echo esc_attr($this->data['meta']['driving_zone_restriction_waypoint_location_area_coordinate_lng']); ?>" class="to-coordinate-lng"/>
											</div>
										</td>	
<?php
		$radius=$this->data['meta']['driving_zone_restriction_dropoff_location_area_radius'];
		if(CHBSOption::getOption('length_unit')==2) $radius=$Length->convertUnit($radius,1,2);
?>
										<td>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_area'); ?>" id="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_area'); ?>" value="<?php echo esc_attr($this->data['meta']['driving_zone_restriction_dropoff_location_area']); ?>"/>
												<input type="text" name="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_area_radius'); ?>" value="<?php echo esc_attr($radius); ?>" maxlength="8" class="to-margin-top-10"/>
												<input type="hidden" name="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_area_coordinate_lat'); ?>" id="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_area_coordinate_lat'); ?>" value="<?php echo esc_attr($this->data['meta']['driving_zone_restriction_dropoff_location_area_coordinate_lat']); ?>" class="to-coordinate-lat"/>
												<input type="hidden" name="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_area_coordinate_lng'); ?>" id="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_area_coordinate_lng'); ?>" value="<?php echo esc_attr($this->data['meta']['driving_zone_restriction_dropoff_location_area_coordinate_lng']); ?>" class="to-coordinate-lng"/>
											</div>
										</td>
									</tr>									  
								</table>
							</div>							
						</li>  
						<li>
							<h5><?php esc_html_e('Pickup and drop-off relation','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Define the relation between pickup and drop-off location.','chauffeur-booking-system'); ?><br/>
							</span>
							<div class="to-clear-fix">
								<div class="to-radio-button">
									<input type="radio" value="1" id="<?php CHBSHelper::getFormName('driving_zone_restriction_location_field_relation_type_1'); ?>" name="<?php CHBSHelper::getFormName('driving_zone_restriction_location_field_relation_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['driving_zone_restriction_location_field_relation_type'],1); ?>/>
									<label for="<?php CHBSHelper::getFormName('driving_zone_restriction_location_field_relation_type_1'); ?>"><?php esc_html_e('Restrictions for both fields (drop-off AND pickup) have to be fulfilled','chauffeur-booking-system'); ?></label>
									<input type="radio" value="2" id="<?php CHBSHelper::getFormName('driving_zone_restriction_location_field_relation_type_2'); ?>" name="<?php CHBSHelper::getFormName('driving_zone_restriction_location_field_relation_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['driving_zone_restriction_location_field_relation_type'],2); ?>/>
									<label for="<?php CHBSHelper::getFormName('driving_zone_restriction_location_field_relation_type_2'); ?>"><?php esc_html_e('Restrictions for at least one field (drop-off OR pickup) have to be fulfilled','chauffeur-booking-system'); ?></label>
								</div>								
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Replace location','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Use this option to replace all locations inside the geofence area with a coordinates of new location.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-clear-fix">
								<table class="to-table" id="to-table-location-replace">
									<tr>
										<th style="width:40%">
											<div>
												<?php esc_html_e('Geofence','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Geofence.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:40%">
											<div>
												<?php esc_html_e('New location','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('New location.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:40%">
											<div>
												<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Remove.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
									</tr>
									<tr class="to-hidden">
										<td>
											<div class="to-clear-fix">											
												<select name="<?php CHBSHelper::getFormName('location_replace[geofence][]'); ?>" class="to-dropkick-disable" id="location_replace_geofence">
<?php
		foreach($this->data['dictionary']['geofence'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'">'.esc_html($value['post']->post_title).'</option>';
?>
												</select>
											</div>										
										</td>
										<td>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('location_replace[location][address][name][]'); ?>"/>
												<input type="hidden" name="<?php CHBSHelper::getFormName('location_replace[location][address][coordinate_lat][]'); ?>" class="to-coordinate-lat"/>
												<input type="hidden" name="<?php CHBSHelper::getFormName('location_replace[location][address][coordinate_lng][]'); ?>" class="to-coordinate-lng"/>
											</div>
										</td>										
										<td>
											<div>
												<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
											</div>
										</td>										
									</tr>
<?php
		if((array_key_exists('location_replace',$this->data['meta'])) && (is_array($this->data['meta']['location_replace'])) && (count($this->data['meta']['location_replace'])))
		{
			foreach($this->data['meta']['location_replace'] as $index=>$value)
			{
?>
									<tr>
										<td>
											<div class="to-clear-fix">											
												<select name="<?php CHBSHelper::getFormName('location_replace[geofence][]'); ?>" id="location_replace_geofence_<?php echo CHBSHelper::createId(); ?>">
<?php
		foreach($this->data['dictionary']['geofence'] as $geofenceIndex=>$geofenceValue)
			echo '<option value="'.esc_attr($geofenceIndex).'" '.CHBSHelper::selectedIf($geofenceIndex,$value['geofence_id'],false).'>'.esc_html($geofenceValue['post']->post_title).'</option>';
?>
												</select>
											</div>										
										</td>
										<td>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('location_replace[location][address][name][]'); ?>" value="<?php echo esc_attr($value['location']['name']); ?>"/>
												<input type="hidden" name="<?php CHBSHelper::getFormName('location_replace[location][address][coordinate_lat][]'); ?>" class="to-coordinate-lat" value="<?php echo esc_attr($value['location']['coordinate']['lat']); ?>"/>
												<input type="hidden" name="<?php CHBSHelper::getFormName('location_replace[location][address][coordinate_lng][]'); ?>" class="to-coordinate-lng" value="<?php echo esc_attr($value['location']['coordinate']['lng']); ?>"/>
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
?>
								</table>
								<div> 
									<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
								</div>
							</div>
						</li>						
						<li>
							<h5><?php esc_html_e('Pickup time by geofence (service type 1)','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Assign pickup time ranges for pickup addresses inside selected geofence areas.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-clear-fix">
								<table class="to-table" id="to-table-pickup-time-geofence">
									<tr>
										<th style="width:40%">
											<div>
												<?php esc_html_e('Geofence','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Geofence.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:40%">
											<div>
												<?php esc_html_e('Pickup time','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Pickup time range that will be assigned when the pickup address is inside the geofence.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:40%">
											<div>
												<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Remove.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
									</tr>
									<tr class="to-hidden">
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('pickup_time_geofence[geofence][]'); ?>" class="to-dropkick-disable" id="pickup_time_geofence_geofence">
<?php
		foreach($this->data['dictionary']['geofence'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'">'.esc_html($value['post']->post_title).'</option>';
?>
												</select>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('pickup_time_geofence[time][]'); ?>" value=""/>
											</div>
										</td>
										<td>
											<div>
												<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
											</div>
										</td>
									</tr>
<?php
		if((array_key_exists('pickup_time_geofence',$this->data['meta'])) && (is_array($this->data['meta']['pickup_time_geofence'])) && (count($this->data['meta']['pickup_time_geofence'])))
		{
			foreach($this->data['meta']['pickup_time_geofence'] as $index=>$value)
			{
?>
									<tr>
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('pickup_time_geofence[geofence][]'); ?>" id="pickup_time_geofence_geofence_<?php echo CHBSHelper::createId(); ?>">
<?php
		foreach($this->data['dictionary']['geofence'] as $geofenceIndex=>$geofenceValue)
			echo '<option value="'.esc_attr($geofenceIndex).'" '.CHBSHelper::selectedIf($geofenceIndex,$value['geofence_id'],false).'>'.esc_html($geofenceValue['post']->post_title).'</option>';
?>
												</select>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('pickup_time_geofence[time][]'); ?>" value="<?php echo esc_attr($value['time']); ?>"/>
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
?>
								</table>
								<div>
									<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
								</div>
							</div>
						</li>
				   </ul>
				</div>
				<div id="meta-box-booking-form-5">
					<ul class="to-form-field-list">
						<li>
							<h5><?php esc_html_e('Panels','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('This table shows a list of user-defined panels (groups of fields) used in the client form.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('The default tabs "Contact details" and "Billing address" cannot be modified.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-clear-fix">
								<table class="to-table" id="to-table-form-element-panel">
									<tr>
										<th style="width:60%">
											<div>
												<?php esc_html_e('Label','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Label of the panel.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:25%">
											<div>
												<?php esc_html_e('Open/close','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Enable open/close panel option.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>										
										<th style="width:15%">
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
												<input type="hidden" name="<?php CHBSHelper::getFormName('form_element_panel[id][]'); ?>"/>
												<input type="text" name="<?php CHBSHelper::getFormName('form_element_panel[label][]'); ?>" title="<?php esc_attr_e('Enter label.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<select class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('form_element_panel[toggle_visibility_enable][]'); ?>" id="form_element_panel_toggle_visibility_enable">
													<option value="1"><?php esc_html_e('Yes','chauffeur-booking-system'); ?></option>
													<option value="0"><?php esc_html_e('No','chauffeur-booking-system'); ?></option>
												</select>
											</div>
										</td>										
										<td>
											<div>
												<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
											</div>
										</td>										
									</tr>
<?php
		if(isset($this->data['meta']['form_element_panel']))
		{
			foreach($this->data['meta']['form_element_panel'] as $panelValue)
			{
				if(!array_key_exists('toggle_visibility_enable',$panelValue)) 
					$panelValue['toggle_visibility_enable']=0;
?>
									<tr>
										<td>
											<div>
												<input type="hidden" value="<?php echo esc_attr($panelValue['id']); ?>" name="<?php CHBSHelper::getFormName('form_element_panel[id][]'); ?>"/>
												<input type="text" value="<?php echo esc_attr($panelValue['label']); ?>" name="<?php CHBSHelper::getFormName('form_element_panel[label][]'); ?>" title="<?php esc_attr_e('Enter label.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<select id="<?php CHBSHelper::getFormName('form_element_panel_toggle_visibility_enable_'.$panelValue['id']); ?>" name="<?php CHBSHelper::getFormName('form_element_panel[toggle_visibility_enable][]'); ?>">
													<option value="1" <?php CHBSHelper::selectedIf($panelValue['toggle_visibility_enable'],1); ?>><?php esc_html_e('Yes','chauffeur-booking-system'); ?></option>
													<option value="0" <?php CHBSHelper::selectedIf($panelValue['toggle_visibility_enable'],0); ?>><?php esc_html_e('No','chauffeur-booking-system'); ?></option>
												</select>
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
?>
								</table>
								<div> 
									<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
								</div>
							</div>				
						</li>
						<li>
							<h5><?php esc_html_e('Fields','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('This table shows a list of user-defined fields used in the client form.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('Default fields in the "Contact details" and "Billing address" tabs cannot be modified. The "Label" column is required for each field.','chauffeur-booking-system'); ?>
								<?php esc_html_e('If a field is marked as "Mandatory", the "Error message" must also be filled in.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-clear-fix to-table-form-element-field">
								<table class="to-table" id="to-table-form-element-field">
									<tr>
										<th style="width:10%">
											<div>
												<?php esc_html_e('Label','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Label of the field.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:6%">
											<div>
												<?php esc_html_e('Type','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Field type.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>	
										<th style="width:10%">
											<div>
												<?php esc_html_e('Values','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('List of possible values to choose separated by semicolon.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th> 
										<th style="width:5%">
											<div>
												<?php esc_html_e('Mandatory','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Mandatory.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>	
										<th style="width:10%">
											<div>
												<?php esc_html_e('Error message','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Error message displayed in tooltip when field is empty.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>	
										<th style="width:8%">
											<div>
												<?php esc_html_e('Layout','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Layout of the fields displayed in the booking form.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>  
										<th style="width:10%">
											<div>
												<?php esc_html_e('Panel','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Panel in which field has to be located.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:10%">
											<div>
												<?php esc_html_e('Service types','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Types to which field has to be assigned.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:10%">
											<div>
												<?php esc_html_e('Transfer types','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Types to which field has to be assigned.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>	
										<th style="width:8%">
											<div>
												<?php esc_html_e('Pickup geofence','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Pickup geofence.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>											
										<th style="width:8%">
											<div>
												<?php esc_html_e('Drop-off geofence','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Drop-off geofence.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>										
										<th style="width:5%">
											<div>
												<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Remove.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
									</tr>
									<tr class="to-hidden">
										<td>
											<div class="to-clear-fix">
												<input type="hidden" name="<?php CHBSHelper::getFormName('form_element_field[id][]'); ?>"/>
												<input type="text" name="<?php CHBSHelper::getFormName('form_element_field[label][]'); ?>" title="<?php esc_attr_e('Enter label.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<select class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('form_element_field[field_type][]'); ?>" id="form_element_field_field_type">
<?php
		foreach($this->data['dictionary']['form_element_field_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'">'.esc_html($value[0]).'</option>';
?>
												</select>
											</div>									
										</td>	
										<td>
											<div class="to-clear-fix">												
												<input type="text" name="<?php CHBSHelper::getFormName('form_element_field[dictionary][]'); ?>" title="<?php esc_attr_e('Enter values of list separated by semicolon.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>  										
										<td>
											<div class="to-clear-fix">
												<select class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('form_element_field[mandatory][]'); ?>" id="form_element_field_mandatory">
													<option value="1"><?php esc_html_e('Yes','chauffeur-booking-system'); ?></option>
													<option value="0" selected="selected"><?php esc_html_e('No','chauffeur-booking-system'); ?></option>
												</select>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">												
												<input type="text" name="<?php CHBSHelper::getFormName('form_element_field[message_error][]'); ?>" title="<?php esc_attr_e('Enter error message.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>	
										<td>
											<div class="to-clear-fix">
												<select class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('form_element_field[field_layout][]'); ?>" id="form_element_field_layout">
<?php
		foreach($this->data['dictionary']['form_element_field_layout'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'">'.esc_html($value[0]).'</option>';
?>
												</select>
											</div>									
										</td>										
										<td>
											<div class="to-clear-fix">
												<select class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('form_element_field[panel_id][]'); ?>" id="form_element_field_panel_id">
<?php
		foreach($this->data['dictionary']['form_element_panel'] as $index=>$value)
			echo '<option value="'.esc_attr($value['id']).'">'.esc_html($value['label']).'</option>';
?>
												</select>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">											
												<select name="<?php CHBSHelper::getFormName('form_element_field[service_type_id_enable][]'); ?>" id="form_element_field_service_type_id_enable" class="to-dropkick-disable chbs-service-type-id-enable" multiple="multiple" size="<?php echo (int)count($this->data['dictionary']['service_type']); ?>">
<?php
		foreach($this->data['dictionary']['service_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" selected="selected">'.esc_html($value[0]).'</option>';
?>
												</select>
												<input type="hidden" value="" name="<?php CHBSHelper::getFormName('form_element_field[service_type_id_enable_hidden][]'); ?>"/>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">											
												<select name="<?php CHBSHelper::getFormName('form_element_field[transfer_type_id_enable][]'); ?>" id="form_element_field_transfer_type_id_enable" class="to-dropkick-disable chbs-transfer-type-id-enable" multiple="multiple" size="<?php echo (int)count($this->data['dictionary']['transfer_type']); ?>">
<?php
		foreach($this->data['dictionary']['transfer_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" selected="selected">'.esc_html($value[0]).'</option>';
?>
												</select>
												<input type="hidden" value="" name="<?php CHBSHelper::getFormName('form_element_field[transfer_type_id_enable_hidden][]'); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">											
												<select name="<?php CHBSHelper::getFormName('form_element_field[geofence_pickup][]'); ?>" id="form_element_field_geofence_pickup" class="to-dropkick-disable chbs-geofence-pickup" multiple="multiple" size="3">
													<option value="-1" selected="selected"><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['geofence'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'">'.esc_html($value['post']->post_title).'</option>';
?>
												</select>
												<input type="hidden" value="" name="<?php CHBSHelper::getFormName('form_element_field[geofence_pickup_hidden][]'); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">											
												<select name="<?php CHBSHelper::getFormName('form_element_field[geofence_dropoff][]'); ?>" id="form_element_field_geofence_dropoff" class="to-dropkick-disable chbs-geofence-dropoff" multiple="multiple" size="3">
													<option value="-1" selected="selected"><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
		foreach($this->data['dictionary']['geofence'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'">'.esc_html($value['post']->post_title).'</option>';
?>
												</select>
												<input type="hidden" value="" name="<?php CHBSHelper::getFormName('form_element_field[geofence_dropoff_hidden][]'); ?>"/>
											</div>
										</td>
										<td>
											<div>
												<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
											</div>
										</td>										
									</tr>
<?php
		if(isset($this->data['meta']['form_element_field']))
		{
			foreach($this->data['meta']['form_element_field'] as $fieldValue)
			{
?>			   
									<tr>
										<td>
											<div class="to-clear-fix">
												<input type="hidden" value="<?php echo esc_attr($fieldValue['id']); ?>" name="<?php CHBSHelper::getFormName('form_element_field[id][]'); ?>"/>
												<input type="text" value="<?php echo esc_attr($fieldValue['label']); ?>" name="<?php CHBSHelper::getFormName('form_element_field[label][]'); ?>" title="<?php esc_attr_e('Enter label.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<select id="<?php CHBSHelper::getFormName('form_element_field_field_type_'.$fieldValue['id']); ?>" name="<?php CHBSHelper::getFormName('form_element_field[field_type][]'); ?>">
<?php
				foreach($this->data['dictionary']['form_element_field_type'] as $index=>$value)
					echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($fieldValue['field_type'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>
											</div>									
										</td>	
										<td>
											<div class="to-clear-fix">												
												<input type="text" value="<?php echo esc_attr($fieldValue['dictionary']); ?>" name="<?php CHBSHelper::getFormName('form_element_field[dictionary][]'); ?>" title="<?php esc_attr_e('Enter values of list separated by semicolon.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<select id="<?php CHBSHelper::getFormName('form_element_field_mandatory_'.$fieldValue['id']); ?>" name="<?php CHBSHelper::getFormName('form_element_field[mandatory][]'); ?>">
													<option value="1" <?php CHBSHelper::selectedIf($fieldValue['mandatory'],1); ?>><?php esc_html_e('Yes','chauffeur-booking-system'); ?></option>
													<option value="0" <?php CHBSHelper::selectedIf($fieldValue['mandatory'],0); ?>><?php esc_html_e('No','chauffeur-booking-system'); ?></option>
												</select>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">												
												<input type="text" value="<?php echo esc_attr($fieldValue['message_error']); ?>" name="<?php CHBSHelper::getFormName('form_element_field[message_error][]'); ?>" title="<?php esc_attr_e('Enter error message.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>	
										<td>
											<div class="to-clear-fix">
												<select id="<?php CHBSHelper::getFormName('form_element_field_field_layout_'.$fieldValue['id']); ?>" name="<?php CHBSHelper::getFormName('form_element_field[field_layout][]'); ?>">
<?php
				foreach($this->data['dictionary']['form_element_field_layout'] as $index=>$value)
					echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($fieldValue['field_layout'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
												</select>
											</div>									
										</td>											
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('form_element_field[panel_id][]'); ?>" id="<?php CHBSHelper::getFormName('form_element_field_panel_id_'.$fieldValue['id']); ?>" >
<?php
				foreach($this->data['dictionary']['form_element_panel'] as $index=>$value)
					echo '<option value="'.esc_attr($value['id']).'" '.(CHBSHelper::selectedIf($fieldValue['panel_id'],$value['id'],false)).'>'.esc_html($value['label']).'</option>';
?>
												</select>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">											
												<select name="<?php CHBSHelper::getFormName('form_element_field[service_type_id_enable][]'); ?>" id="<?php CHBSHelper::getFormName('form_element_field_service_type_id_enable_'.$fieldValue['id']); ?>"  class="to-dropkick-disable chbs-service-type-id-enable" multiple="multiple" size="<?php echo (int)count($this->data['dictionary']['service_type']); ?>">
<?php
				foreach($this->data['dictionary']['service_type'] as $index=>$value)
				{
					$selected=false;
					
					if(!array_key_exists('service_type_id_enable',$fieldValue) || (!count($fieldValue['service_type_id_enable'])))
						$selected=true;
					else
					{
						if(in_array($index,$fieldValue['service_type_id_enable']))
							$selected=true;
					} 
					
					echo '<option value="'.esc_attr($index).'" '.($selected ? ' selected="selected"' : '').'>'.esc_html($value[0]).'</option>';
				}
?>
												</select>
												<input type="hidden" value="" name="<?php CHBSHelper::getFormName('form_element_field[service_type_id_enable_hidden][]'); ?>"/>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">											
												<select name="<?php CHBSHelper::getFormName('form_element_field[transfer_type_id_enable][]'); ?>" id="<?php CHBSHelper::getFormName('form_element_field_transfer_type_id_enable_'.$fieldValue['id']); ?>"  class="to-dropkick-disable chbs-service-type-id-enable" multiple="multiple" size="<?php echo (int)count($this->data['dictionary']['transfer_type']); ?>">
<?php
				foreach($this->data['dictionary']['transfer_type'] as $index=>$value)
				{
					$selected=false;
					
					if(!array_key_exists('transfer_type_id_enable',$fieldValue) || (!count($fieldValue['transfer_type_id_enable'])))
						$selected=true;
					else
					{
						if(in_array($index,$fieldValue['transfer_type_id_enable']))
							$selected=true;
					} 
					
					echo '<option value="'.esc_attr($index).'" '.($selected ? ' selected="selected"' : '').'>'.esc_html($value[0]).'</option>';
				}
?>
												</select>
												<input type="hidden" value="" name="<?php CHBSHelper::getFormName('form_element_field[transfer_type_id_enable_hidden][]'); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">											
												<select name="<?php CHBSHelper::getFormName('form_element_field[geofence_pickup][]'); ?>" id="<?php CHBSHelper::getFormName('form_element_field_geofence_pickup_'.$fieldValue['id']); ?>"  class="to-dropkick-disable chbs-geofence-pickup" multiple="multiple" size="3">
													<option value="-1" <?php echo ((is_array($fieldValue['geofence_pickup'])) && (in_array(-1,$fieldValue['geofence_pickup'])) ? ' selected="selected"' : ''); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
				foreach($this->data['dictionary']['geofence'] as $index=>$value)
					echo '<option value="'.esc_attr($index).'" '.((is_array($fieldValue['geofence_pickup'])) && (in_array($index,$fieldValue['geofence_pickup'])) ? ' selected="selected"' : '').'>'.esc_html($value['post']->post_title).'</option>';
?>
												</select>
												<input type="hidden" value="" name="<?php CHBSHelper::getFormName('form_element_field[geofence_pickup_hidden][]'); ?>"/>
											</div>
										</td>										
										<td>
											<div class="to-clear-fix">											
												<select name="<?php CHBSHelper::getFormName('form_element_field[geofence_dropoff][]'); ?>" id="<?php CHBSHelper::getFormName('form_element_field_geofence_dropoff_'.$fieldValue['id']); ?>"  class="to-dropkick-disable chbs-geofence-dropoff" multiple="multiple" size="3">
													<option value="-1" <?php echo ((is_array($fieldValue['geofence_dropoff'])) && (in_array(-1,$fieldValue['geofence_dropoff'])) ? ' selected="selected"' : ''); ?>><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>
<?php
				foreach($this->data['dictionary']['geofence'] as $index=>$value)
					echo '<option value="'.esc_attr($index).'" '.((is_array($fieldValue['geofence_dropoff'])) && (in_array($index,$fieldValue['geofence_dropoff'])) ? ' selected="selected"' : '').'>'.esc_html($value['post']->post_title).'</option>';
?>
												</select>
												<input type="hidden" value="" name="<?php CHBSHelper::getFormName('form_element_field[geofence_dropoff_hidden][]'); ?>"/>
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
?>									
								</table>
								<div> 
									<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
								</div>
							</div>				
						</li>
						<li>
							<h5><?php esc_html_e('Agreements','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('This table shows a list of agreements that must be accepted by the customer before submitting the booking.','chauffeur-booking-system'); ?><br/>
								<?php echo _e('Each agreement includes a checkbox and agreement text.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-clear-fix">
								<table class="to-table" id="to-table-form-element-agreement">
									<tr>
										<th style="width:45%">
											<div>
												<?php esc_html_e('Text','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Text of the agreement.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:20%">
											<div>
												<?php esc_html_e('Mandatory','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Mandatory.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>	
										<th style="width:20%">
											<div>
												<?php esc_html_e('Service type','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Service type.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>											
										<th style="width:15%">
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
												<input type="hidden" name="<?php CHBSHelper::getFormName('form_element_agreement[id][]'); ?>"/>
												<input type="text" name="<?php CHBSHelper::getFormName('form_element_agreement[text][]'); ?>" title="<?php esc_attr_e('Enter text.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('form_element_agreement[mandatory][]'); ?>" class="to-dropkick-disable" id="form_element_agreement_mandatory">
													<option value="1"><?php esc_html_e('Yes','chauffeur-booking-system'); ?></option>
													<option value="0"><?php esc_html_e('No','chauffeur-booking-system'); ?></option>
												</select>
											</div>
										</td>	
										<td>
											<div class="to-clear-fix">											
												<select class="to-dropkick-disable" name="<?php CHBSHelper::getFormName('form_element_agreement[service_type_id_enable][]'); ?>" id="form_element_agreement_service_type_id_enable" class="to-dropkick-disable chbs-service-type-id-enable" multiple="multiple" size="<?php echo (int)count($this->data['dictionary']['service_type']); ?>">
<?php
		foreach($this->data['dictionary']['service_type'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" selected="selected">'.esc_html($value[0]).'</option>';
?>
												</select>
												<input type="hidden" value="" name="<?php CHBSHelper::getFormName('form_element_agreement[service_type_id_enable_hidden][]'); ?>"/>
											</div>
										</td>
										<td>
											<div>
												<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
											</div>
										</td>										
									</tr>
<?php
		if(isset($this->data['meta']['form_element_agreement']))
		{
			foreach($this->data['meta']['form_element_agreement'] as $agreementValue)
			{
?>
									<tr>
										<td>
											<div>
												<input type="hidden" value="<?php echo esc_attr($agreementValue['id']); ?>" name="<?php CHBSHelper::getFormName('form_element_agreement[id][]'); ?>"/>
												<input type="text" value="<?php echo esc_attr($agreementValue['text']); ?>" name="<?php CHBSHelper::getFormName('form_element_agreement[text][]'); ?>" title="<?php esc_attr_e('Enter text.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<select id="<?php CHBSHelper::getFormName('form_element_agreement_mandatory_'.$agreementValue['id']); ?>" name="<?php CHBSHelper::getFormName('form_element_agreement[mandatory][]'); ?>">
													<option value="1" <?php CHBSHelper::selectedIf((isset($agreementValue['mandatory']) ? $agreementValue['mandatory'] : 1),1); ?>><?php esc_html_e('Yes','chauffeur-booking-system'); ?></option>
													<option value="0" <?php CHBSHelper::selectedIf((isset($agreementValue['mandatory']) ? $agreementValue['mandatory'] : 1),0); ?>><?php esc_html_e('No','chauffeur-booking-system'); ?></option>
												</select>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">											
												<select name="<?php CHBSHelper::getFormName('form_element_agreement[service_type_id_enable][]'); ?>" id="<?php CHBSHelper::getFormName('form_element_agreement_service_type_id_enable_'.$fieldValue['id']); ?>"  class="to-dropkick-disable chbs-service-type-id-enable" multiple="multiple" size="<?php echo (int)count($this->data['dictionary']['service_type']); ?>">
<?php
				foreach($this->data['dictionary']['service_type'] as $index=>$value)
				{
					$selected=false;
					
					if(!array_key_exists('service_type_id_enable',$agreementValue) || (!count($agreementValue['service_type_id_enable'])))
						$selected=true;
					else
					{
						if(in_array($index,$agreementValue['service_type_id_enable']))
							$selected=true;
					} 
					
					echo '<option value="'.esc_attr($index).'" '.($selected ? ' selected="selected"' : '').'>'.esc_html($value[0]).'</option>';
				}
?>
												</select>
												<input type="hidden" value="" name="<?php CHBSHelper::getFormName('form_element_agreement[service_type_id_enable_hidden][]'); ?>"/>
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
?>
								</table>
								<div> 
									<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
								</div>
							</div>				
						</li>
					</ul>
				</div>
				<div id="meta-box-booking-form-6">
					<div class="ui-tabs">
						<ul>
							<li><a href="#meta-box-booking-form-6-1"><?php esc_html_e('E-mail','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-6-2"><?php esc_html_e('Vonage SMS','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-6-3"><?php esc_html_e('Twilio SMS','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-6-4"><?php esc_html_e('Vonage/Twilio SMS (to customer)','chauffeur-booking-system'); ?></a></li>
							<li><a href="#meta-box-booking-form-6-5"><?php esc_html_e('Telegram','chauffeur-booking-system'); ?></a></li>
						</ul>
						<div id="meta-box-booking-form-6-1">
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Sender account','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Select the email account that will be used to send messages about new bookings to clients and defined recipients.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										<select name="<?php CHBSHelper::getFormName('booking_new_sender_email_account_id'); ?>" id="<?php CHBSHelper::getFormName('booking_new_sender_email_account_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['booking_new_sender_email_account_id'],-1,false)).'>'.esc_html__(' - Not set -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['email_account'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['booking_new_sender_email_account_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
										</select>
									</div>									
								</li>
								<li>
									<h5><?php esc_html_e('Recipients e-mail addresses','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('List of recipients\' email addresses (separated by semicolon).','chauffeur-booking-system'); ?></span>
									<div class="to-clear-fix">
										<input type="text" name="<?php CHBSHelper::getFormName('booking_new_recipient_email_address'); ?>" value="<?php echo esc_attr($this->data['meta']['booking_new_recipient_email_address']); ?>"/>
									</div>									
								</li>
								<li>
									<h5><?php esc_html_e('New booking notifications sending to customers','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Send an email message about a new booking to customers.','chauffeur-booking-system'); ?></span>
									<div class="to-clear-fix">
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('email_notification_booking_new_client_enable_1'); ?>" name="<?php CHBSHelper::getFormName('email_notification_booking_new_client_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['email_notification_booking_new_client_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('email_notification_booking_new_client_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('email_notification_booking_new_client_enable_0'); ?>" name="<?php CHBSHelper::getFormName('email_notification_booking_new_client_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['email_notification_booking_new_client_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('email_notification_booking_new_client_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>								
									</div>										
								</li>
								<li>
									<h5><?php esc_html_e('New booking notifications sending to customers in case of successful payment only','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Send an email message about a new booking to customers only if the booking has been paid.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Works only with built-in online payment methods like Stripe and PayPal.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Requires "New booking notifications sending to customers" to be enabled.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('email_notification_booking_new_client_payment_success_enable_1'); ?>" name="<?php CHBSHelper::getFormName('email_notification_booking_new_client_payment_success_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['email_notification_booking_new_client_payment_success_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('email_notification_booking_new_client_payment_success_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('email_notification_booking_new_client_payment_success_enable_0'); ?>" name="<?php CHBSHelper::getFormName('email_notification_booking_new_client_payment_success_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['email_notification_booking_new_client_payment_success_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('email_notification_booking_new_client_payment_success_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>								
									</div>										
								</li>	
								<li>
									<h5><?php esc_html_e('New booking notifications sending to defined addresses','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Send an email message about a new booking to the addresses defined in the recipient list.','chauffeur-booking-system'); ?></span>
									<div class="to-clear-fix">
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('email_notification_booking_new_admin_enable_1'); ?>" name="<?php CHBSHelper::getFormName('email_notification_booking_new_admin_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['email_notification_booking_new_admin_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('email_notification_booking_new_admin_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('email_notification_booking_new_admin_enable_0'); ?>" name="<?php CHBSHelper::getFormName('email_notification_booking_new_admin_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['email_notification_booking_new_admin_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('email_notification_booking_new_admin_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>								
									</div>										
								</li>	
								<li>
									<h5><?php esc_html_e('New booking notifications sending to defined addresses in case of successful payment only','chauffeur-booking-system'); ?></h5>
									<span class="to-legend">
										<?php esc_html_e('Send an email message about a new booking to the addresses defined in the recipient list only if the booking has been paid.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Works only with built-in online payment methods like Stripe and PayPal.','chauffeur-booking-system'); ?><br/>
										<?php esc_html_e('Requires "New booking notifications sending to defined addresses" to be enabled.','chauffeur-booking-system'); ?>
									</span>
									<div class="to-clear-fix">
										<div class="to-radio-button">
											<input type="radio" value="1" id="<?php CHBSHelper::getFormName('email_notification_booking_new_admin_payment_success_enable_1'); ?>" name="<?php CHBSHelper::getFormName('email_notification_booking_new_admin_payment_success_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['email_notification_booking_new_admin_payment_success_enable'],1); ?>/>
											<label for="<?php CHBSHelper::getFormName('email_notification_booking_new_admin_payment_success_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
											<input type="radio" value="0" id="<?php CHBSHelper::getFormName('email_notification_booking_new_admin_payment_success_enable_0'); ?>" name="<?php CHBSHelper::getFormName('email_notification_booking_new_admin_payment_success_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['email_notification_booking_new_admin_payment_success_enable'],0); ?>/>
											<label for="<?php CHBSHelper::getFormName('email_notification_booking_new_admin_payment_success_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
										</div>								
									</div>										
								</li>									
							</ul>
						</div>
						<div id="meta-box-booking-form-6-2">
							<div class="to-notice-small to-notice-small-error">
								<?php esc_html_e('Using these options you can set up sending notifications about new booking to the defined number.','chauffeur-booking-system'); ?>
								<?php echo sprintf(__('You can find more information about Vonage <a href="%s" target="_blank">here</a>.','chauffeur-booking-system'),'https://www.vonage.com/'); ?>
							</div>
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Status','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>		
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('nexmo_sms_enable_1'); ?>" name="<?php CHBSHelper::getFormName('nexmo_sms_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['nexmo_sms_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('nexmo_sms_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('nexmo_sms_enable_0'); ?>" name="<?php CHBSHelper::getFormName('nexmo_sms_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['nexmo_sms_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('nexmo_sms_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>								
								</li>
								<li>
									<h5><?php esc_html_e('API key','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('API key.','chauffeur-booking-system'); ?></span>		
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('nexmo_sms_api_key'); ?>" value="<?php echo esc_attr($this->data['meta']['nexmo_sms_api_key']); ?>"/>
									</div>										
								</li>
								<li>
									<h5><?php esc_html_e('Secret API key','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Secret API key.','chauffeur-booking-system'); ?></span>	
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('nexmo_sms_api_key_secret'); ?>" value="<?php echo esc_attr($this->data['meta']['nexmo_sms_api_key_secret']); ?>"/>
									</div>										
								</li>
								<li>
									<h5><?php esc_html_e('Sender name','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Sender name.','chauffeur-booking-system'); ?></span>	
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('nexmo_sms_sender_name'); ?>" value="<?php echo esc_attr($this->data['meta']['nexmo_sms_sender_name']); ?>"/>
									</div>	
								</li>
								<li>
									<h5><?php esc_html_e('Recipient phone number','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Recipient phone number.','chauffeur-booking-system'); ?></span>		
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('nexmo_sms_recipient_phone_number'); ?>" value="<?php echo esc_attr($this->data['meta']['nexmo_sms_recipient_phone_number']); ?>"/>
									</div>										
								</li>
								<li>
									<h5><?php esc_html_e('Message','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Message.','chauffeur-booking-system'); ?></span>		
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('nexmo_sms_message'); ?>" value="<?php echo esc_attr($this->data['meta']['nexmo_sms_message']); ?>"/>
									</div>	
								</li>	
							</ul>
						</div>		
						<div id="meta-box-booking-form-6-3">
							<div class="to-notice-small to-notice-small-error">
								<?php esc_html_e('Using these options you can set up sending notifications about new booking to the defined number.','chauffeur-booking-system'); ?>
								<?php echo sprintf(__('You can find more information about Twilio <a href="%s" target="_blank">here</a>.','chauffeur-booking-system'),'https://www.twilio.com/'); ?>
							</div>
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Status','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>		
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('twilio_sms_enable_1'); ?>" name="<?php CHBSHelper::getFormName('twilio_sms_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['twilio_sms_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('twilio_sms_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('twilio_sms_enable_0'); ?>" name="<?php CHBSHelper::getFormName('twilio_sms_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['twilio_sms_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('twilio_sms_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>								
								</li>
								<li>
									<h5><?php esc_html_e('API SID','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('API SID.','chauffeur-booking-system'); ?></span>	
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('twilio_sms_api_sid'); ?>" value="<?php echo esc_attr($this->data['meta']['twilio_sms_api_sid']); ?>"/>
									</div>										
								</li>
								<li>
									<h5><?php esc_html_e('API token','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('API token.','chauffeur-booking-system'); ?></span>	
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('twilio_sms_api_token'); ?>" value="<?php echo esc_attr($this->data['meta']['twilio_sms_api_token']); ?>"/>
									</div>	
								</li>
								<li>
									<h5><?php esc_html_e('Sender phone number','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Sender phone number.','chauffeur-booking-system'); ?></span>	
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('twilio_sms_sender_phone_number'); ?>" value="<?php echo esc_attr($this->data['meta']['twilio_sms_sender_phone_number']); ?>"/>
									</div>	
								</li>	
								<li>
									<h5><?php esc_html_e('Recipient phone number','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Recipient phone number.','chauffeur-booking-system'); ?></span>	
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('twilio_sms_recipient_phone_number'); ?>" value="<?php echo esc_attr($this->data['meta']['twilio_sms_recipient_phone_number']); ?>"/>
									</div>	
								</li>	
								<li>
									<h5><?php esc_html_e('Message','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Message.','chauffeur-booking-system'); ?></span>		
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('twilio_sms_message'); ?>" value="<?php echo esc_attr($this->data['meta']['twilio_sms_message']); ?>"/>
									</div>
								</li>									
							</ul>
						</div>
						<div id="meta-box-booking-form-6-4">
							<div class="to-notice-small to-notice-small-error">
								<?php esc_html_e('Using these options you can set up sending notifications about new booking to the customer.','chauffeur-booking-system'); ?><br/>
								<?php echo __('Note that the "Status" option in the Twilio and Vonage settings has no effect when sending notifications to the customer.','chauffeur-booking-system'); ?><br/>
								<?php echo sprintf(__('You can use shortcodes like: %s in the message.','chauffeur-booking-system'),$this->data['message_shortcode']); ?>
							</div>
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Status','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('customer_sms_enable_1'); ?>" name="<?php CHBSHelper::getFormName('customer_sms_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['customer_sms_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('customer_sms_enable_1'); ?>"><?php esc_html_e('Enable via Twilio','chauffeur-booking-system'); ?></label>
										<input type="radio" value="2" id="<?php CHBSHelper::getFormName('customer_sms_enable_2'); ?>" name="<?php CHBSHelper::getFormName('customer_sms_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['customer_sms_enable'],2); ?>/>
										<label for="<?php CHBSHelper::getFormName('customer_sms_enable_2'); ?>"><?php esc_html_e('Enable via Vonage','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('customer_sms_enable_0'); ?>" name="<?php CHBSHelper::getFormName('customer_sms_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['customer_sms_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('customer_sms_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>								
								</li>
								<li>
									<h5><?php esc_html_e('Text message','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Text message.','chauffeur-booking-system'); ?></span>
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('customer_sms_message'); ?>" value="<?php echo esc_attr($this->data['meta']['customer_sms_message']); ?>"/>
									</div>										
								</li>
							</ul>
						</div>
						<div id="meta-box-booking-form-6-5">
							<div class="to-notice-small to-notice-small-error">
								<?php esc_html_e('Using these options you can set up sending notifications about new booking to the defined number.','chauffeur-booking-system'); ?>
								<?php echo sprintf(__('You can find more information about Telegram configuration <a href="%s" target="_blank">here</a>.','chauffeur-booking-system'),'https://core.telegram.org/bots'); ?>
							</div>
							<ul class="to-form-field-list">
								<li>
									<h5><?php esc_html_e('Status','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
									<div class="to-radio-button">
										<input type="radio" value="1" id="<?php CHBSHelper::getFormName('telegram_enable_1'); ?>" name="<?php CHBSHelper::getFormName('telegram_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['telegram_enable'],1); ?>/>
										<label for="<?php CHBSHelper::getFormName('telegram_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
										<input type="radio" value="0" id="<?php CHBSHelper::getFormName('telegram_enable_0'); ?>" name="<?php CHBSHelper::getFormName('telegram_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['telegram_enable'],0); ?>/>
										<label for="<?php CHBSHelper::getFormName('telegram_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
									</div>								
								</li>
								<li>
									<h5><?php esc_html_e('Token','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Token.','chauffeur-booking-system'); ?></span>
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('telegram_token'); ?>" value="<?php echo esc_attr($this->data['meta']['telegram_token']); ?>"/>
									</div>									
								</li>
								<li>
									<h5><?php esc_html_e('Group ID','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Group ID.','chauffeur-booking-system'); ?></span>
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('telegram_group_id'); ?>" value="<?php echo esc_attr($this->data['meta']['telegram_group_id']); ?>"/>
									</div>									
								</li>
								<li>
									<h5><?php esc_html_e('Message','chauffeur-booking-system'); ?></h5>
									<span class="to-legend"><?php esc_html_e('Message.','chauffeur-booking-system'); ?></span>
									<div>
										<input type="text" name="<?php CHBSHelper::getFormName('telegram_message'); ?>" value="<?php echo esc_attr($this->data['meta']['telegram_message']); ?>"/>
									</div>									
								</li>	
							</ul>
						</div>
					</div>
				</div>
				<div id="meta-box-booking-form-7">
					<ul class="to-form-field-list">
						<li>
							<h5><?php esc_html_e('Google Map ID','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php echo __('Google Map ID.','chauffeur-booking-system'); ?></span> 
							<div class="to-clear-fix">
								<input type="text" name="<?php CHBSHelper::getFormName('google_map_map_id'); ?>" value="<?php echo esc_attr($this->data['meta']['google_map_map_id']); ?>"/>								 
							</div>						 
						</li>
						<li>
							<h5><?php esc_html_e('Default location','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Select how the default location on the map should be determined.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('If you choose "Browser geolocation" (requires SSL), the customer will be asked for permission to use their current location.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('In all other cases, the location from the "Fixed location" text field will be used.','chauffeur-booking-system'); ?>
							</span>
							<div>
								<span class="to-legend-field"><?php esc_html_e('Type.','chauffeur-booking-system'); ?></span>
								<div class="to-radio-button">
									<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_map_default_location_type_1'); ?>" name="<?php CHBSHelper::getFormName('google_map_default_location_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_default_location_type'],1); ?>/>
									<label for="<?php CHBSHelper::getFormName('google_map_default_location_type_1'); ?>"><?php esc_html_e('Browser geolocation','chauffeur-booking-system'); ?></label>
									<input type="radio" value="2" id="<?php CHBSHelper::getFormName('google_map_default_location_type_2'); ?>" name="<?php CHBSHelper::getFormName('google_map_default_location_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_default_location_type'],2); ?>/>
									<label for="<?php CHBSHelper::getFormName('google_map_default_location_type_2'); ?>"><?php esc_html_e('Fixed location','chauffeur-booking-system'); ?></label>
								</div>
							</div>
							<div>
								<span class="to-legend-field"><?php esc_html_e('Fixed location.','chauffeur-booking-system'); ?></span>
								<input type="text" name="<?php CHBSHelper::getFormName('google_map_default_location_fixed'); ?>" value="<?php echo esc_attr($this->data['meta']['google_map_default_location_fixed']); ?>"/>
								<input type="hidden" name="<?php CHBSHelper::getFormName('google_map_default_location_fixed_coordinate_lat'); ?>" value="<?php echo esc_attr($this->data['meta']['google_map_default_location_fixed_coordinate_lat']); ?>"/>
								<input type="hidden" name="<?php CHBSHelper::getFormName('google_map_default_location_fixed_coordinate_lng'); ?>" value="<?php echo esc_attr($this->data['meta']['google_map_default_location_fixed_coordinate_lng']); ?>"/>
							</div>								  
						</li>  
						<li>
							<h5><?php esc_html_e('Route type','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php echo __('Define which type of the route should be displayed on the map.','chauffeur-booking-system'); ?></span> 
							<div class="to-radio-button">
								<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_map_route_type_1'); ?>" name="<?php CHBSHelper::getFormName('google_map_route_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_route_type'],1); ?>/>
								<label for="<?php CHBSHelper::getFormName('google_map_route_type_1'); ?>"><?php esc_html_e('Fastest','chauffeur-booking-system'); ?></label>
								<input type="radio" value="2" id="<?php CHBSHelper::getFormName('google_map_route_type_2'); ?>" name="<?php CHBSHelper::getFormName('google_map_route_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_route_type'],2); ?>/>
								<label for="<?php CHBSHelper::getFormName('google_map_route_type_2'); ?>"><?php esc_html_e('Shortest','chauffeur-booking-system'); ?></label>
							</div>							
						</li>						
						<li>
							<h5><?php esc_html_e('Avoid','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php echo __('Indicates that the calculated route(s) should avoid the indicated features.','chauffeur-booking-system'); ?></span> 
							<div class="to-checkbox-button">
								<input type="checkbox" value="-1" id="<?php CHBSHelper::getFormName('google_map_route_avoid_0'); ?>" name="<?php CHBSHelper::getFormName('google_map_route_avoid[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_route_avoid'],-1); ?>/>
								<label for="<?php CHBSHelper::getFormName('google_map_route_avoid_0'); ?>"><?php esc_html_e('- None - ','chauffeur-booking-system'); ?></label>							
<?php
		foreach($this->data['dictionary']['google_map']['route_avoid'] as $index=>$value)
		{
?>
								<input type="checkbox" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('google_map_route_avoid_'.$index); ?>" name="<?php CHBSHelper::getFormName('google_map_route_avoid[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_route_avoid'],$index); ?>/>
								<label for="<?php CHBSHelper::getFormName('google_map_route_avoid_'.$index); ?>"><?php echo esc_html($value); ?></label>		<?php		
		}
?>
							</div>	
					   </li>					   
					   <li>
							<h5><?php esc_html_e('Traffic layer','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php echo __('Enable or disable traffic layer on the map.','chauffeur-booking-system'); ?></span> 
							<div class="to-radio-button">
								<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_map_traffic_layer_enable_1'); ?>" name="<?php CHBSHelper::getFormName('google_map_traffic_layer_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_traffic_layer_enable'],1); ?>/>
								<label for="<?php CHBSHelper::getFormName('google_map_traffic_layer_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
								<input type="radio" value="0" id="<?php CHBSHelper::getFormName('google_map_traffic_layer_enable_0'); ?>" name="<?php CHBSHelper::getFormName('google_map_traffic_layer_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_traffic_layer_enable'],0); ?>/>
								<label for="<?php CHBSHelper::getFormName('google_map_traffic_layer_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
							</div>							
					   </li>
					   <li>
							<h5><?php esc_html_e('Draggable map','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php echo __('Enable or disable map dragging.','chauffeur-booking-system'); ?></span> 
							<div class="to-radio-button">
								<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_map_draggable_enable_1'); ?>" name="<?php CHBSHelper::getFormName('google_map_draggable_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_draggable_enable'],1); ?>/>
								<label for="<?php CHBSHelper::getFormName('google_map_draggable_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
								<input type="radio" value="0" id="<?php CHBSHelper::getFormName('google_map_draggable_enable_0'); ?>" name="<?php CHBSHelper::getFormName('google_map_draggable_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_draggable_enable'],0); ?>/>
								<label for="<?php CHBSHelper::getFormName('google_map_draggable_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
							</div>							
					   </li>
						<li>
							<h5><?php esc_html_e('Map type control','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Enter settings for a map type.','chauffeur-booking-system'); ?>
							</span> 
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
								<div class="to-radio-button">
									<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_map_map_type_control_enable_1'); ?>" name="<?php CHBSHelper::getFormName('google_map_map_type_control_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_map_type_control_enable'],1); ?>/>
									<label for="<?php CHBSHelper::getFormName('google_map_map_type_control_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
									<input type="radio" value="0" id="<?php CHBSHelper::getFormName('google_map_map_type_control_enable_0'); ?>" name="<?php CHBSHelper::getFormName('google_map_map_type_control_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_map_type_control_enable'],0); ?>/>
									<label for="<?php CHBSHelper::getFormName('google_map_map_type_control_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
								</div>								
							</div>   
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('Type.','chauffeur-booking-system'); ?></span>
								<select name="<?php CHBSHelper::getFormName('google_map_map_type_control_id'); ?>" id="<?php CHBSHelper::getFormName('google_map_map_type_control_id'); ?>">
<?php
		foreach($this->data['dictionary']['google_map']['map_type_control_id'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['google_map_map_type_control_id'],$index,false)).'>'.esc_html($value).'</option>';
?>
								</select>								
							</div>  
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('Style.','chauffeur-booking-system'); ?></span>
								<select name="<?php CHBSHelper::getFormName('google_map_map_type_control_style'); ?>" id="<?php CHBSHelper::getFormName('google_map_map_type_control_style'); ?>">
<?php
		foreach($this->data['dictionary']['google_map']['map_type_control_style'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['google_map_map_type_control_style'],$index,false)).'>'.esc_html($value).'</option>';
?>
								</select>								
							</div>							  
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('Position.','chauffeur-booking-system'); ?></span>
								<select name="<?php CHBSHelper::getFormName('google_map_map_type_control_position'); ?>" id="<?php CHBSHelper::getFormName('google_map_map_type_control_position'); ?>">
<?php
		foreach($this->data['dictionary']['google_map']['position'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['google_map_map_type_control_position'],$index,false)).'>'.esc_html($value).'</option>';
?>
								</select>								
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Zoom','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Enter settings for a zoom.','chauffeur-booking-system'); ?>
							</span> 
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
								<div class="to-radio-button">
									<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_map_zoom_control_enable_1'); ?>" name="<?php CHBSHelper::getFormName('google_map_zoom_control_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_zoom_control_enable'],1); ?>/>
									<label for="<?php CHBSHelper::getFormName('google_map_zoom_control_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
									<input type="radio" value="0" id="<?php CHBSHelper::getFormName('google_map_zoom_control_enable_0'); ?>" name="<?php CHBSHelper::getFormName('google_map_zoom_control_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_map_zoom_control_enable'],0); ?>/>
									<label for="<?php CHBSHelper::getFormName('google_map_zoom_control_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
								</div>								
							</div>  
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('Position.','chauffeur-booking-system'); ?></span>
								<select name="<?php CHBSHelper::getFormName('google_map_zoom_control_position'); ?>" id="<?php CHBSHelper::getFormName('google_map_zoom_control_position'); ?>">
<?php
		foreach($this->data['dictionary']['google_map']['position'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['google_map_zoom_control_position'],$index,false)).'>'.esc_html($value).'</option>';
?>
								</select>								
							</div>						  
						</li>						
					</ul> 
				</div>
				<div id="meta-box-booking-form-8">
					<ul class="to-form-field-list">
					   <li>
							<h5><?php esc_html_e('Google Calendar','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php echo __('Enable or disable integration with Google Calendar.','chauffeur-booking-system'); ?></span> 
							<div class="to-radio-button">
								<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_calendar_enable_1'); ?>" name="<?php CHBSHelper::getFormName('google_calendar_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_calendar_enable'],1); ?>/>
								<label for="<?php CHBSHelper::getFormName('google_calendar_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
								<input type="radio" value="0" id="<?php CHBSHelper::getFormName('google_calendar_enable_0'); ?>" name="<?php CHBSHelper::getFormName('google_calendar_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_calendar_enable'],0); ?>/>
								<label for="<?php CHBSHelper::getFormName('google_calendar_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
							</div>							
						</li>	   
						<li>
							<h5><?php esc_html_e('ID','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php echo __('Google Calendar ID.','chauffeur-booking-system'); ?></span> 
							<div class="to-clear-fix">
								<input type="text" name="<?php CHBSHelper::getFormName('google_calendar_id'); ?>" value="<?php echo esc_attr($this->data['meta']['google_calendar_id']); ?>"/>								 
							</div>						 
						</li>
						<li>
							<h5><?php esc_html_e('Settings','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php echo __('Copy/paste the contents of downloaded *.json file.','chauffeur-booking-system'); ?></span> 
							<div class="to-clear-fix">
								<textarea rows="1" cols="1" name="<?php CHBSHelper::getFormName('google_calendar_settings'); ?>" id="<?php CHBSHelper::getFormName('google_calendar_settings'); ?>"><?php echo esc_html($this->data['meta']['google_calendar_settings']); ?></textarea>
							</div>						 
						</li>
						<li>
							<h5><?php esc_html_e('Regenerate token','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php echo __('Regenerate (create a new one) OAuth2 token each time when the new event is added.','chauffeur-booking-system'); ?><br/>
								<?php echo __('You can disable this option if the Google Calendar works without any issues and events are created.','chauffeur-booking-system'); ?>
							</span> 
							<div class="to-radio-button">
								<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_calendar_regenerate_token_enable_1'); ?>" name="<?php CHBSHelper::getFormName('google_calendar_regenerate_token_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_calendar_regenerate_token_enable'],1); ?>/>
								<label for="<?php CHBSHelper::getFormName('google_calendar_regenerate_token_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
								<input type="radio" value="0" id="<?php CHBSHelper::getFormName('google_calendar_regenerate_token_enable_0'); ?>" name="<?php CHBSHelper::getFormName('google_calendar_regenerate_token_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['google_calendar_regenerate_token_enable'],0); ?>/>
								<label for="<?php CHBSHelper::getFormName('google_calendar_regenerate_token_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
							</div>							
						</li>
						<li>
							<h5><?php esc_html_e('Adding event','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Specify when the booking has to be added to the calendar.','chauffeur-booking-system'); ?>
							</span> 
							<div class="to-clear-fix">
								<select name="<?php CHBSHelper::getFormName('google_calendar_add_event_action'); ?>" id="<?php CHBSHelper::getFormName('google_calendar_add_event_action'); ?>">
<?php
		foreach($this->data['dictionary']['google_calendar_add_event_action'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['google_calendar_add_event_action'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
								</select>	
							</div>  
						</li>
						<li>
							<h5><?php esc_html_e('Event title','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php echo esc_html__('Title of the Google Calendar event. If this field will be empty, plugin uses default one.','chauffeur-booking-system'); ?><br/>
								<?php echo sprintf(__('You can use shortcodes like: %s in the title.','chauffeur-booking-system'),$this->data['message_shortcode']); ?>
							</span> 
							<div class="to-clear-fix">
								<input type="text" name="<?php CHBSHelper::getFormName('google_calendar_event_title'); ?>" value="<?php echo esc_attr($this->data['meta']['google_calendar_event_title']); ?>"/>								 
							</div>						 
						</li>	
						<li>
							<h5><?php esc_html_e('Event description','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php echo esc_html__('Description of the Google Calendar event. If this field will be empty, plugin uses default one.','chauffeur-booking-system'); ?><br/>
								<?php echo sprintf(__('You can use shortcodes like: %s in the description.','chauffeur-booking-system'),$this->data['message_shortcode']); ?>
							</span>
							<div class="to-clear-fix">
								<textarea rows="1" cols="1" name="<?php CHBSHelper::getFormName('google_calendar_event_description'); ?>"><?php echo esc_html($this->data['meta']['google_calendar_event_description']); ?></textarea>
							</div>											
						</li>	
					</ul>
				</div>
				<div id="meta-box-booking-form-9">
					<ul class="to-form-field-list">
						<li>
							<h5><?php esc_html_e('Colors','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Specify color for each group of elements.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('Please note that in some cases "resetting browser cache" will be required.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('If you use server cache control plugins in your WordPress, you need to clear its own cache as well.','chauffeur-booking-system'); ?>
							</span> 
							<div class="to-clear-fix">
								<table class="to-table">
									<tr>
										<th style="width:10%">
											<div>
												<?php esc_html_e('Group number','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Group number.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:50%">
											<div>
												<?php esc_html_e('Description.','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Description.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:20%">
											<div>
												<?php esc_html_e('Default color','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Default value of the color.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:20%">
											<div>
												<?php esc_html_e('Color','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('New value (in HEX) of the color.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
									</tr>
<?php
		foreach($this->data['dictionary']['color'] as $index=>$value)
		{
?>
									<tr>
										<td>
											<div><?php echo $index; ?>.</div>
										</td>
										<td>
											<div><?php echo esc_html($value['description']); ?></div>
										</td>
										<td>
											<div class="to-clear-fix">
												<span class="to-color-picker-sample to-color-picker-sample-style-1" style="background-color:#<?php echo esc_attr($value['color']); ?>"></span>
												<span><?php echo '#'.esc_html($value['color']); ?></span>
											</div>
										</td>
										<td>
											<div class="to-clear-fix">	
												 <input type="text" class="to-color-picker" id="<?php CHBSHelper::getFormName('style_color_'.$index); ?>" name="<?php CHBSHelper::getFormName('style_color['.$index.']'); ?>" value="<?php echo esc_attr($this->data['meta']['style_color'][$index]); ?>"/>
											</div>
										</td>
									</tr>
<?php
		}
?>
								</table>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
<?php
		$GeoLocation=new CHBSGeoLocation();
		
		if((int)$this->data['meta']['geolocation_server_side_enable']===1)
			$userDefaultCoordinate=$GeoLocation->getCoordinate();
		else $userDefaultCoordinate=array('lat'=>0,'lng'=>0);
?>
		<script type="text/javascript">

			jQuery(document).ready(function($)
			{	
				/***/
				
				var helper=new CHBSHelper();
				helper.getMessageFromConsole();
				
				/***/
				
				var element=$('.to').themeOptionElement({init:true});
				element.createSlider('#<?php CHBSHelper::getFormName('payment_deposit_value'); ?>',0,100,<?php echo (int)$this->data['meta']['payment_deposit_value']; ?>);
				element.createSlider('#<?php CHBSHelper::getFormName('form_preloader_background_opacity'); ?>',0,100,<?php echo (int)$this->data['meta']['form_preloader_background_opacity']; ?>);
				
				/***/
				
				toPreventCheckbox($('input[name="<?php CHBSHelper::getFormName('route_id'); ?>[]"]'));
				toPreventCheckbox($('input[name="<?php CHBSHelper::getFormName('vehicle_category_id'); ?>[]"]'));
				toPreventCheckbox($('input[name="<?php CHBSHelper::getFormName('google_map_route_avoid'); ?>[]"]'));			
				toPreventCheckbox($('input[name="<?php CHBSHelper::getFormName('booking_extra_category_id'); ?>[]"]'));
				toPreventCheckbox($('input[name="<?php CHBSHelper::getFormName('vehicle_sum_split'); ?>[]"]'));
				
				/***/
	
				toCreateCustomDateTimePicker();
				
				/***/
				
				$('#to-table-form-element-panel').table();
				$('#to-table-form-element-field').table();
				$('#to-table-form-element-agreement').table();
				$('#to-table-availability-exclude-date').table();
				$('#to-table-maximum-booking-number').table();
				
				$('#to-table-location-replace').table(
				{
					afterAddLine		:   function(line)
					{
						toCreateAutocomplete(line.find('input[type="text"]'),true);
					}
				});
				$('#to-table-pickup-time-geofence').table();

				/***/
				
				toPreventCheckbox($('input[name="<?php CHBSHelper::getFormName('service_type_id[]'); ?>"]'),2);
				toPreventCheckbox($('input[name="<?php CHBSHelper::getFormName('transfer_type_enable_1[]'); ?>"]'),2);
				toPreventCheckbox($('input[name="<?php CHBSHelper::getFormName('transfer_type_enable_3[]'); ?>"]'),2);
				
				/***/
				
				$('#post').on('submit',function()
				{
					$('select.chbs-service-type-id-enable,select.chbs-transfer-type-id-enable,select.chbs-geofence-pickup,select.chbs-geofence-dropoff').each(function()
					{
						var option=[];
						$(this).children('option:selected').each(function() { option.push($(this).val()); });
						$(this).next('input').val(option.join('.'));
					});
				});			   
				
				/***/
				
				element.bindBrowseMedia('.to-button-browse');
				
				/***/
				
				toCreateAutocomplete('input[name="<?php CHBSHelper::getFormName('base_location'); ?>"]');
				toCreateAutocomplete('input[name="<?php CHBSHelper::getFormName('google_map_default_location_fixed'); ?>"]');

				toCreateAutocomplete('input[name="<?php CHBSHelper::getFormName('driving_zone_restriction_pickup_location_area'); ?>"]');
				toCreateAutocomplete('input[name="<?php CHBSHelper::getFormName('driving_zone_restriction_waypoint_location_area'); ?>"]');
				toCreateAutocomplete('input[name="<?php CHBSHelper::getFormName('driving_zone_restriction_dropoff_location_area'); ?>"]'); 
				
				toCreateAutocomplete('input[name="<?php CHBSHelper::getFormName('location_replace[location][address][name][]'); ?>"]',true); 
				
				/***/
			});
			
		</script>
