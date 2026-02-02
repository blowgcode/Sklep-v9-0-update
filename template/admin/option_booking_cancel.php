<?php
			$option=(isset($this->data['option']) && is_array($this->data['option'])) ? $this->data['option'] : array();
			$bookingCancelEnable=array_key_exists('booking_cancel_enable',$option) ? $option['booking_cancel_enable'] : 0;
			$bookingCancelConfirmationPage=array_key_exists('booking_cancel_confirmation_page',$option) ? $option['booking_cancel_confirmation_page'] : '';
			$bookingCancelEmailRecipient=array_key_exists('booking_cancel_email_recipient',$option) ? $option['booking_cancel_email_recipient'] : '';
			$bookingCancelCancellableStatuses=array_key_exists('booking_cancel_cancellable_booking_status_id',$option) ? $option['booking_cancel_cancellable_booking_status_id'] : array();
?>
			<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Booking cancelling','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enable or disable option to cancel bookings by customer.','chauffeur-booking-system'); ?><br/>
					<?php esc_html_e('Once this option is enabled email notification (sent to customer) will include a link to cancel the booking.','chauffeur-booking-system'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('booking_cancel_enable_1'); ?>" name="<?php CHBSHelper::getFormName('booking_cancel_enable'); ?>" <?php CHBSHelper::checkedIf($bookingCancelEnable,1); ?>/>
						<label for="<?php CHBSHelper::getFormName('booking_cancel_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('booking_cancel_enable_0'); ?>" name="<?php CHBSHelper::getFormName('booking_cancel_enable'); ?>" <?php CHBSHelper::checkedIf($bookingCancelEnable,0); ?>/>
						<label for="<?php CHBSHelper::getFormName('booking_cancel_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
			</li>
			
			<li>
				<h5><?php esc_html_e('Cancelling confirmation page','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enter page/post ID to which customer will be redirected after clicking on cancel booking link in the notification.','chauffeur-booking-system'); ?>
					<?php echo sprintf(esc_html__('Please note that this page has to contain shortcode %s.','chauffeur-booking-system'),'['.PLUGIN_CHBS_CONTEXT.'_booking_cancel_confirmation]'); ?><br/>
				</span>
					<div class="to-clear-fix">
						<input type="text" maxlength="6" name="<?php CHBSHelper::getFormName('booking_cancel_confirmation_page'); ?>" id="<?php CHBSHelper::getFormName('booking_cancel_confirmation_page'); ?>" value="<?php echo esc_attr($bookingCancelConfirmationPage); ?>"/>
				</div>
			</li>  			
			<li>
				<h5><?php esc_html_e('Recipient e-mail addresses','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('List of recipient e-mail addresses separated by semicolon on which ones plugin sends notification about canceling booking by the customer.','chauffeur-booking-system'); ?>
				</span>
					<div class="to-clear-fix">
						<input type="text" name="<?php CHBSHelper::getFormName('booking_cancel_email_recipient'); ?>" id="<?php CHBSHelper::getFormName('booking_cancel_email_recipient'); ?>" value="<?php echo esc_attr($bookingCancelEmailRecipient); ?>"/>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('Cancellable booking statuses','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Select booking statuses which could be cancelled.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-checkbox-button">
<?php
		foreach($this->data['dictionary']['booking_status'] as $index=>$value)
		{
?>
						<input type="checkbox" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('booking_cancel_cancellable_booking_status_id_'.$index); ?>" name="<?php CHBSHelper::getFormName('booking_cancel_cancellable_booking_status_id[]'); ?>" <?php CHBSHelper::checkedIf($bookingCancelCancellableStatuses,$index); ?>/>
					<label for="<?php CHBSHelper::getFormName('booking_cancel_cancellable_booking_status_id_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>
				</div>
			</li>				
		</ul>
