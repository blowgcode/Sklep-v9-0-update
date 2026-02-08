		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Booking acceptance by drivers (stage 1)','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enable or disable option to accept/reject booking by the assigned driver.','chauffeur-booking-system'); ?><br/>
					<?php esc_html_e('Once this option is enabled email notification will be sent to the assigned driver.','chauffeur-booking-system'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_1_enable_1'); ?>" name="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_1_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_driver_acceptance_stage_1_enable'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_1_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_1_enable_0'); ?>" name="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_1_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_driver_acceptance_stage_1_enable'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_1_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
			</li>  	
			<li>
				<h5><?php esc_html_e('Booking acceptance by drivers (stage 2)','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enable or disable option to accept/reject booking by all drivers.','chauffeur-booking-system'); ?><br/>
					<?php esc_html_e('If the booking will not be accepted by the driver in the first stage (or the driver will not take any action in predefined time), then - if this option is enabled - plugin will send an e-mail message to all drivers. If none of the drivers accept the booking (or will not take any action in predefined time) booking will be canceled.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_2_enable_1'); ?>" name="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_2_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_driver_acceptance_stage_2_enable'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_2_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_2_enable_0'); ?>" name="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_2_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_driver_acceptance_stage_2_enable'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_2_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Cron events.','chauffeur-booking-system'); ?></span>
					<div class="to-field-disabled to-width-100">
<?php
		$command='* * * * * wget "'.get_site_url().'/?chbs_cron_event=4&amp;chbs_run_code='.CHBSOption::getOption('run_code').'"';
		echo $command;
		echo '<a href="#" class="to-copy-to-clipboard to-float-right" data-clipboard-text="'.esc_attr($command).'" data-label-on-success="'.esc_attr__('Copied!','chauffeur-booking-system').'">'.esc_html__('Copy','chauffeur-booking-system').'</a>';
?>
					</div>	
					<div class="to-field-disabled to-width-100">
<?php
		$command='* * * * * wget "'.get_site_url().'/?chbs_cron_event=5&amp;chbs_run_code='.CHBSOption::getOption('run_code').'"';
		echo $command;
		echo '<a href="#" class="to-copy-to-clipboard to-float-right" data-clipboard-text="'.esc_attr($command).'" data-label-on-success="'.esc_attr__('Copied!','chauffeur-booking-system').'">'.esc_html__('Copy','chauffeur-booking-system').'</a>';
?>
					</div>					
				</div>	
			</li>  
			<li>
				<h5><?php esc_html_e('Confirmation page for drivers','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enter page/post ID with booking confirmation form for drivers.','chauffeur-booking-system'); ?><br/>
					<?php echo sprintf(esc_html__('Please note that this page has to contain shortcode %s.','chauffeur-booking-system'),'['.PLUGIN_CHBS_CONTEXT.'_booking_driver_acceptance_confirmation]'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<input type="text" maxlength="6" name="<?php CHBSHelper::getFormName('booking_driver_acceptance_confirmation_page'); ?>" id="<?php CHBSHelper::getFormName('booking_driver_acceptance_confirmation_page'); ?>" value="<?php echo esc_attr($this->data['option']['booking_driver_acceptance_confirmation_page']); ?>"/>
				</div>
			</li>  
			<li>
				<h5><?php esc_html_e('Recipient e-mail addresses','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('List of recipient e-mail addresses separated by semicolon on which ones plugin sends notification about accepting/rejecting booking by driver.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('booking_driver_acceptance_email_recipient'); ?>" id="<?php CHBSHelper::getFormName('booking_driver_acceptance_email_recipient'); ?>" value="<?php echo esc_attr($this->data['option']['booking_driver_acceptance_email_recipient']); ?>"/>
				</div>
			</li>	
			<li>
				<h5><?php esc_html_e('Interval between stage 1 and 2','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Time duration (in minutes) between sending an email in stage 1 and stage 2 - if the assigned driver reject the booking nor take any action (in stage 1).','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					<input type="text" maxlength="6" name="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_1_interval'); ?>" id="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_1_interval'); ?>" value="<?php echo esc_attr($this->data['option']['booking_driver_acceptance_stage_1_interval']); ?>"/>
				</div>
			</li>	
			<li>
				<h5><?php esc_html_e('Interval between stage 2 and cancel the booking','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Time duration (in minutes) between sending an email in stage 2 and cancel the booking - if none of the drivers accept the booking nor take any action (in stage 2).','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					<input type="text" maxlength="6" name="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_2_interval'); ?>" id="<?php CHBSHelper::getFormName('booking_driver_acceptance_stage_2_interval'); ?>" value="<?php echo esc_attr($this->data['option']['booking_driver_acceptance_stage_2_interval']); ?>"/>
				</div>
			</li>	
			<li>
				<h5><?php esc_html_e('Driver notification booking status','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Define a booking statuses for which notifications to the drivers have to be sent.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-checkbox-button">
<?php
		foreach($this->data['dictionary']['booking_status'] as $index=>$value)
		{
?>
					<input type="checkbox" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('booking_driver_acceptance_notificaction_booking_status_id_'.$index); ?>" name="<?php CHBSHelper::getFormName('booking_driver_acceptance_notificaction_booking_status_id[]'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_driver_acceptance_notificaction_booking_status_id'],$index); ?>/>
					<label for="<?php CHBSHelper::getFormName('booking_driver_acceptance_notificaction_booking_status_id_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>
				</div>					
			</li>		
			<li>
				<h5><?php esc_html_e('New status for accepted booking','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Select new status for bookings which have been accepted by drivers.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-radio-button">
					<input type="radio" value="0" id="<?php CHBSHelper::getFormName('booking_driver_acceptance_status_after_accept_0'); ?>" name="<?php CHBSHelper::getFormName('booking_driver_acceptance_status_after_accept'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_driver_acceptance_status_after_accept'],-1); ?>/>
					<label for="<?php CHBSHelper::getFormName('booking_driver_acceptance_status_after_accept_0'); ?>"><?php esc_html_e('- No changes - ','chauffeur-booking-system'); ?></label>
<?php
		foreach($this->data['dictionary']['booking_status'] as $index=>$value)
		{
?>
					<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('booking_driver_acceptance_status_after_accept_'.$index); ?>" name="<?php CHBSHelper::getFormName('booking_driver_acceptance_status_after_accept'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_driver_acceptance_status_after_accept'],$index); ?>/>
					<label for="<?php CHBSHelper::getFormName('booking_driver_acceptance_status_after_accept_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>
				</div>
			</li>	
			<li>
				<h5><?php esc_html_e('New status for rejected booking','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Select new status for bookings which have been rejected by drivers.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-radio-button">
					<input type="radio" value="0" id="<?php CHBSHelper::getFormName('booking_driver_acceptance_status_after_reject_0'); ?>" name="<?php CHBSHelper::getFormName('booking_driver_acceptance_status_after_reject'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_driver_acceptance_status_after_reject'],-1); ?>/>
					<label for="<?php CHBSHelper::getFormName('booking_driver_acceptance_status_after_reject_0'); ?>"><?php esc_html_e('- No changes - ','chauffeur-booking-system'); ?></label>
<?php
		foreach($this->data['dictionary']['booking_status'] as $index=>$value)
		{
?>
					<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('booking_driver_acceptance_status_after_reject_'.$index); ?>" name="<?php CHBSHelper::getFormName('booking_driver_acceptance_status_after_reject'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_driver_acceptance_status_after_reject'],$index); ?>/>
					<label for="<?php CHBSHelper::getFormName('booking_driver_acceptance_status_after_reject_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>
				</div>
			</li>			 
		</ul>