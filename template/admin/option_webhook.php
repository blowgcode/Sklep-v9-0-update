
		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Booking webhook','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enable this webhook to send full order details to the defined URL after the defined event.','chauffeur-booking-system'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('webhook_booking_enable_1'); ?>" name="<?php CHBSHelper::getFormName('webhook_booking_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['webhook_booking_enable'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('webhook_booking_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('webhook_booking_enable_0'); ?>" name="<?php CHBSHelper::getFormName('webhook_booking_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['webhook_booking_enable'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('webhook_booking_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('URL address.','chauffeur-booking-system'); ?></span>
					<div>
						<input type="text" name="<?php CHBSHelper::getFormName('webhook_booking_url_address'); ?>" id="<?php CHBSHelper::getFormName('webhook_booking_url_address'); ?>" value="<?php echo esc_attr($this->data['option']['webhook_booking_url_address']); ?>"/>
					</div>
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field">
						<?php esc_html_e('Specify when the webhook has to be running.','chauffeur-booking-system'); ?>
					</span> 
					<div>
						<select name="<?php CHBSHelper::getFormName('webhook_booking_run_event'); ?>" id="<?php CHBSHelper::getFormName('webhook_booking_run_event'); ?>">
<?php
		foreach($this->data['dictionary']['webhook_booking_run_event'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['option']['webhook_booking_run_event'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
						</select>	
					</div>  
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Define how many times webhook can be triggered. Value "0" means that this number is unlimited.','chauffeur-booking-system'); ?></span>
					<div>
						<input type="text" maxlength="6" name="<?php CHBSHelper::getFormName('webhook_booking_run_number'); ?>" id="<?php CHBSHelper::getFormName('webhook_booking_run_number'); ?>" value="<?php echo esc_attr($this->data['option']['webhook_booking_run_number']); ?>"/>
					</div>
				</div>				
			</li>   			
		</ul>