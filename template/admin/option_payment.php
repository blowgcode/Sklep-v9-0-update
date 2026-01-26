		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Booking status after successful payment','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Set the booking status after successful payment.','chauffeur-booking-system'); ?></span>
				<div class="to-radio-button">
					<input type="radio" value="-1" id="<?php CHBSHelper::getFormName('booking_status_payment_success_0'); ?>" name="<?php CHBSHelper::getFormName('booking_status_payment_success'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_status_payment_success'],-1); ?>/>
					<label for="<?php CHBSHelper::getFormName('booking_status_payment_success_0'); ?>"><?php esc_html_e('[No changes]','chauffeur-booking-system'); ?></label>
<?php
		foreach($this->data['dictionary']['booking_status'] as $index=>$value)
		{
?>
					<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('booking_status_payment_success_'.$index); ?>" name="<?php CHBSHelper::getFormName('booking_status_payment_success'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_status_payment_success'],$index); ?>/>
					<label for="<?php CHBSHelper::getFormName('booking_status_payment_success_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Set the same status if the booking sum is zero.','chauffeur-booking-system'); ?></span>
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('booking_status_sum_zero_1'); ?>" name="<?php CHBSHelper::getFormName('booking_status_sum_zero'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_status_sum_zero'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('booking_status_sum_zero_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('booking_status_sum_zero_0'); ?>" name="<?php CHBSHelper::getFormName('booking_status_sum_zero'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_status_sum_zero'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('booking_status_sum_zero_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
			</li> 
			<li>
				<h5><?php esc_html_e('Booking statuses synchronization','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Synchronize booking statuses between the plugin and WooCommerce.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-radio-button">
<?php
		foreach($this->data['dictionary']['booking_status_synchronization'] as $index=>$value)
		{
?>
					<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('booking_status_synchronization_'.$index); ?>" name="<?php CHBSHelper::getFormName('booking_status_synchronization'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['booking_status_synchronization'],$index); ?>/>
					<label for="<?php CHBSHelper::getFormName('booking_status_synchronization_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>
				</div>
			</li>  
		</ul>

