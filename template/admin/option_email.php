
		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Default sender e-mail account','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Select the default email account.','chauffeur-booking-system'); ?><br/>
					<?php esc_html_e('It will be used to send notifications to clients about booking status changes and to drivers about assignment or unassignment.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					<select name="<?php CHBSHelper::getFormName('sender_default_email_account_id'); ?>" id="<?php CHBSHelper::getFormName('sender_default_email_account_id'); ?>">
<?php
						echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['option']['sender_default_email_account_id'],-1,false)).'>'.esc_html__('- Not set -','chauffeur-booking-system').'</option>';
						foreach($this->data['dictionary']['email_account'] as $index=>$value)
							echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['option']['sender_default_email_account_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
					</select>
				</div>
			</li> 
			<li>
				<h5><?php esc_html_e('Customer pre-arrival reminder','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enable this option to send customers an email reminder before the service.','chauffeur-booking-system'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('email_service_reminder_customer_enable_1'); ?>" name="<?php CHBSHelper::getFormName('email_service_reminder_customer_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['email_service_reminder_customer_enable'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('email_service_reminder_customer_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('email_service_reminder_customer_enable_0'); ?>" name="<?php CHBSHelper::getFormName('email_service_reminder_customer_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['email_service_reminder_customer_enable'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('email_service_reminder_customer_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Number of minutes (1–9999) before pickup date/time when the customer should receive the email reminder.','chauffeur-booking-system'); ?></span>
					<div>
						<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('email_service_reminder_customer_duration'); ?>" id="<?php CHBSHelper::getFormName('email_service_reminder_customer_duration'); ?>" value="<?php echo esc_attr($this->data['option']['email_service_reminder_customer_duration']); ?>"/>
					</div>
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Cron event.','chauffeur-booking-system'); ?></span>
					<div class="to-field-disabled to-width-100">
<?php
		$command='* * * * * wget "'.get_site_url().'/?chbs_cron_event=1&amp;chbs_run_code='.CHBSOption::getOption('run_code').'"';
		echo $command;
		echo '<a href="#" class="to-copy-to-clipboard to-float-right" data-clipboard-text="'.esc_attr($command).'" data-label-on-success="'.esc_attr__('Copied!','chauffeur-booking-system').'">'.esc_html__('Copy','chauffeur-booking-system').'</a>';
?>
					</div>						
				</div>	
			</li> 
			<li>
				<h5><?php esc_html_e('Driver pre-arrival reminder','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enable this option to send the driver an email reminder before the service.','chauffeur-booking-system'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('email_service_reminder_driver_enable_1'); ?>" name="<?php CHBSHelper::getFormName('email_service_reminder_driver_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['email_service_reminder_driver_enable'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('email_service_reminder_driver_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('email_service_reminder_driver_enable_0'); ?>" name="<?php CHBSHelper::getFormName('email_service_reminder_driver_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['email_service_reminder_driver_enable'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('email_service_reminder_driver_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Number of minutes (1–9999) before pickup date/time when the driver should receive the email reminder.','chauffeur-booking-system'); ?></span>
					<div>
						<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('email_service_reminder_driver_duration'); ?>" id="<?php CHBSHelper::getFormName('email_service_reminder_driver_duration'); ?>" value="<?php echo esc_attr($this->data['option']['email_service_reminder_driver_duration']); ?>"/>
					</div>
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Cron event.','chauffeur-booking-system'); ?></span>
					<div class="to-field-disabled to-width-100">
<?php
		$command='* * * * * wget "'.get_site_url().'/?chbs_cron_event=2&amp;chbs_run_code='.CHBSOption::getOption('run_code').'"';
		echo $command;
		echo '<a href="#" class="to-copy-to-clipboard to-float-right" data-clipboard-text="'.esc_attr($command).'" data-label-on-success="'.esc_attr__('Copied!','chauffeur-booking-system').'">'.esc_html__('Copy','chauffeur-booking-system').'</a>';
?>
					</div>						
				</div>	
			</li> 
			<li>
				<h5><?php esc_html_e('Customer post-arrival message','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enable this option to send the customer an email message after the service.','chauffeur-booking-system'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Status.','chauffeur-booking-system'); ?></span>
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_enable_1'); ?>" name="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['email_service_post_arrival_message_customer_enable'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_enable_0'); ?>" name="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['email_service_post_arrival_message_customer_enable'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Number of time units (1–999999) after service completion when the customer should receive the email message.','chauffeur-booking-system'); ?></span>
					<div>
						<input type="text" maxlength="6" name="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration'); ?>" id="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration'); ?>" value="<?php echo esc_attr($this->data['option']['email_service_post_arrival_message_customer_duration']); ?>"/>
					</div>
				</div>
				<div class="to-radio-button">
					<input type="radio" value="1" id="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration_unit_1'); ?>" name="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration_unit'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['email_service_post_arrival_message_customer_duration_unit'],1); ?>/>
					<label for="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration_unit_1'); ?>"><?php esc_html_e('Minutes','chauffeur-booking-system'); ?></label>
					<input type="radio" value="2" id="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration_unit_2'); ?>" name="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration_unit'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['email_service_post_arrival_message_customer_duration_unit'],2); ?>/>
					<label for="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration_unit_2'); ?>"><?php esc_html_e('Minutes','chauffeur-booking-system'); ?></label>
					<input type="radio" value="3" id="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration_unit_3'); ?>" name="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration_unit'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['email_service_post_arrival_message_customer_duration_unit'],3); ?>/>
					<label for="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration_unit_3'); ?>"><?php esc_html_e('Days','chauffeur-booking-system'); ?></label>
					<input type="radio" value="4" id="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration_unit_4'); ?>" name="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration_unit'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['email_service_post_arrival_message_customer_duration_unit'],4); ?>/>
					<label for="<?php CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration_unit_4'); ?>"><?php esc_html_e('Days (without hours/minutes)','chauffeur-booking-system'); ?></label>
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Cron event.','chauffeur-booking-system'); ?></span>
					<div class="to-field-disabled to-width-100">
<?php
		$command='* * * * * wget "'.get_site_url().'/?chbs_cron_event=3&amp;chbs_run_code='.CHBSOption::getOption('run_code').'"';
		echo $command;
		echo '<a href="#" class="to-copy-to-clipboard to-float-right" data-clipboard-text="'.esc_attr($command).'" data-label-on-success="'.esc_attr__('Copied!','chauffeur-booking-system').'">'.esc_html__('Copy','chauffeur-booking-system').'</a>';
?>
					</div>						
				</div>	
			</li>
		</ul>