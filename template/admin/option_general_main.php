
		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Logo','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Select company logo.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('logo'); ?>" id="<?php CHBSHelper::getFormName('logo'); ?>" class="to-float-left" value="<?php echo esc_attr($this->data['option']['logo']); ?>"/>
					<input type="button" name="<?php CHBSHelper::getFormName('logo_browse'); ?>" id="<?php CHBSHelper::getFormName('logo_browse'); ?>" class="to-button-browse to-button" value="<?php esc_attr_e('Browse','chauffeur-booking-system'); ?>"/>
				</div>
			</li> 
			<li>
				<h5><?php esc_html_e('Currency','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Select the base currency used in the plugin.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					<select name="<?php CHBSHelper::getFormName('currency'); ?>" id="<?php CHBSHelper::getFormName('currency'); ?>">
<?php
						foreach($this->data['dictionary']['currency'] as $index=>$value)
							echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['option']['currency'],$index,false)).'>'.esc_html($value['name'].' ('.$index.')').'</option>';
?>
					</select>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('Length unit','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Length unit.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<select name="<?php CHBSHelper::getFormName('length_unit'); ?>" id="<?php CHBSHelper::getFormName('length_unit'); ?>">
<?php
						foreach($this->data['dictionary']['length_unit'] as $index=>$value)
							echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['option']['length_unit'],$index,false)).'>'.esc_html($value[0].' ('.$value[1].')').'</option>';
?>
					</select>
				</div>
			</li>   
			<li>
				<h5><?php esc_html_e('Date format','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php echo sprintf(esc_html__('Select the date format to be displayed. More info you can find here %s.','chauffeur-booking-system'),'<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">Formatting Date and Time</a>'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('date_format'); ?>" id="<?php CHBSHelper::getFormName('date_format'); ?>" value="<?php echo esc_attr($this->data['option']['date_format']); ?>"/>
				</div>
			</li>  
			<li>
				<h5><?php esc_html_e('First day','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Set the first day of the week in the calendars.','chauffeur-booking-system'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<select name="<?php CHBSHelper::getFormName('first_day_week'); ?>" id="<?php CHBSHelper::getFormName('first_day_week'); ?>">
<?php
		foreach($this->data['dictionary']['day'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['option']['first_day_week'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
					</select>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('Time format','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php echo sprintf(esc_html__('Select the time format to be displayed. More info you can find here %s.','chauffeur-booking-system'),'<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">Formatting Date and Time</a>'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('time_format'); ?>" id="<?php CHBSHelper::getFormName('time_format'); ?>" value="<?php echo esc_attr($this->data['option']['time_format']); ?>"/>
				</div>
			</li>		
			<li>
				<h5><?php esc_html_e('Address format','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Address format.','chauffeur-booking-system'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('address_format_type_1'); ?>" name="<?php CHBSHelper::getFormName('address_format_type'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['address_format_type'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('address_format_type_1'); ?>"><?php esc_html_e('Type 1','chauffeur-booking-system'); ?></label>
						<input type="radio" value="2" id="<?php CHBSHelper::getFormName('address_format_type_2'); ?>" name="<?php CHBSHelper::getFormName('address_format_type'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['address_format_type'],2); ?>/>
						<label for="<?php CHBSHelper::getFormName('address_format_type_2'); ?>"><?php esc_html_e('Type 2 (US)','chauffeur-booking-system'); ?></label>
					</div>
				</div>
			</li>   
			<li>
				<h5><?php esc_html_e('Geolocation server','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Select which servers should handle geolocation requests.','chauffeur-booking-system'); ?><br/>
					<?php esc_html_e('Some of them may require additional configuration.','chauffeur-booking-system'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Server.','chauffeur-booking-system'); ?></span>
					<div>
						<select name="<?php CHBSHelper::getFormName('geolocation_server_id'); ?>" id="<?php CHBSHelper::getFormName('geolocation_server_id'); ?>">
<?php
						echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['option']['geolocation_server_id'],-1,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
						foreach($this->data['dictionary']['geolocation_server'] as $index=>$value)
							echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['option']['geolocation_server_id'],$index,false)).'>'.esc_html($value['name']).'</option>';
?>
						</select>
					</div>
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('API key for ipstack server.','chauffeur-booking-system'); ?></span>
					<div>
						<input type="text" name="<?php CHBSHelper::getFormName('geolocation_server_id_3_api_key'); ?>" id="<?php CHBSHelper::getFormName('geolocation_server_id_3_api_key'); ?>" value="<?php echo esc_attr($this->data['option']['geolocation_server_id_3_api_key']); ?>"/>
					</div>
				</div>
			</li>			 
			<li>
				<h5><?php esc_html_e('Fixer.io API key','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php echo sprintf(__('Enter API key generated by <a href="%s" target="_blank">Fixer.io</a>.','chauffeur-booking-system'),'https://fixer.io/'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('fixer_io_api_key'); ?>" id="<?php CHBSHelper::getFormName('fixer_io_api_key'); ?>" value="<?php echo esc_attr($this->data['option']['fixer_io_api_key']); ?>"/>
				</div>
			</li>  
			<li>
				<h5><?php esc_html_e('Allowed files to upload','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php echo sprintf(__('List of allowed file extensions (separated by semicolon) for upload. If left empty, all file types can be uploaded. Note that upload permissions are also controlled by WordPress. More info <a href="%s" target="_blank">here</a>.','chauffeur-booking-system'),'https://codex.wordpress.org/Uploading_Files'); ?>
				</span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('file_extension_to_upload'); ?>" id="<?php CHBSHelper::getFormName('file_extension_to_upload'); ?>" value="<?php echo esc_attr($this->data['option']['file_extension_to_upload']); ?>"/>
				</div>
			</li> 			
			<li>
				<h5><?php esc_html_e('Duration/distance in pricing rules','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Select how distance and duration should be calculated for return/return (new ride) transfer types in pricing rules.','chauffeur-booking-system'); ?><br/>
					<?php esc_html_e('"Use sum" means one-way + return values will be used.','chauffeur-booking-system'); ?><br/>
					<?php esc_html_e('"Use one-way" means only one-way distance and duration will be applied.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('pricing_rule_return_use_type_1'); ?>" name="<?php CHBSHelper::getFormName('pricing_rule_return_use_type'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['pricing_rule_return_use_type'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('pricing_rule_return_use_type_1'); ?>"><?php esc_html_e('Use sum of distance/duration','chauffeur-booking-system'); ?></label>
						<input type="radio" value="2" id="<?php CHBSHelper::getFormName('pricing_rule_return_use_type_2'); ?>" name="<?php CHBSHelper::getFormName('pricing_rule_return_use_type'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['pricing_rule_return_use_type'],2); ?>/>
						<label for="<?php CHBSHelper::getFormName('pricing_rule_return_use_type_2'); ?>"><?php esc_html_e('Use one way distance/duration','chauffeur-booking-system'); ?></label>
					</div>
				</div>
			</li>   
			<li>
				<h5><?php esc_html_e('Non-blocking booking statuses','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Select statuses that don\'t block date/time when checking vehicle availability based on past bookings.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-checkbox-button">
<?php
		foreach($this->data['dictionary']['booking_status'] as $index=>$value)
		{
?>
					<input type="checkbox" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('booking_status_nonblocking_'.$index); ?>" name="<?php CHBSHelper::getFormName('booking_status_nonblocking[]'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_status_nonblocking'],$index); ?>/>
					<label for="<?php CHBSHelper::getFormName('booking_status_nonblocking_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>
				</div>
			</li> 
<?php
		if((int)$this->data['option']['license_verified']===1)
		{
?>
			<li>
				<h5><?php esc_html_e('Footer copyright','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Enable or disable QuanticaLabs copyright text in the footer.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('copyright_footer_enable_1'); ?>" name="<?php CHBSHelper::getFormName('copyright_footer_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['copyright_footer_enable'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('copyright_footer_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('copyright_footer_enable_0'); ?>" name="<?php CHBSHelper::getFormName('copyright_footer_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['copyright_footer_enable'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('copyright_footer_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
			</li>
<?php
		}
?>
		</ul>