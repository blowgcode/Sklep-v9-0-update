		<h2><?php _e('User business account','chauffeur-booking-system'); ?></h2>
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<label for="<?php CHBSHelper::getFormName('business_user_account_enable'); ?>">
							<?php esc_html_e('Business account','chauffeur-booking-system'); ?>
							<span class="description"><?php esc_html_e('(required)','chauffeur-booking-system'); ?></span>
						</label>
					</th>
					<td>
						<label for="<?php CHBSHelper::getFormName('business_user_account_enable'); ?>">
							<input value="1" type="checkbox" name="<?php CHBSHelper::getFormName('business_user_account_enable'); ?>" id="<?php CHBSHelper::getFormName('business_user_account_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['business_user_account_enable'],1) ?>>
							<?php esc_html_e('Enable business account for this user','chauffeur-booking-system'); ?>		
						</label>
					</td>
				</tr>
				<tr>
					<th>
						<label for="<?php CHBSHelper::getFormName('business_user_account_date_from'); ?>">
							<?php esc_html_e('Date from (DD-MM-YYYY)','chauffeur-booking-system'); ?>
							<span class="description"><?php esc_html_e('(required)','chauffeur-booking-system'); ?></span>
						</label>
					</th>
					<td>
						<input class="regular-text to-datepicker-custom" type="text" name="<?php CHBSHelper::getFormName('business_user_account_date_from'); ?>" id="<?php CHBSHelper::getFormName('business_user_account_date_from'); ?>" value="<?php echo esc_attr($this->data['meta']['business_user_account_date_from']); ?>">
					</td>
				</tr>
				<tr>
					<th>
						<label for="<?php CHBSHelper::getFormName('business_user_account_date_to'); ?>">
							<?php esc_html_e('Date to (DD-MM-YYYY)','chauffeur-booking-system'); ?>
							<span class="description"><?php esc_html_e('(required)','chauffeur-booking-system'); ?></span>
						</label>
					</th>
					<td>
						<input class="regular-text to-datepicker-custom" type="text" name="<?php CHBSHelper::getFormName('business_user_account_date_to'); ?>" id="<?php CHBSHelper::getFormName('business_user_account_date_to'); ?>" value="<?php echo esc_attr($this->data['meta']['business_user_account_date_to']); ?>">
					</td>
				</tr>
				<tr>
					<th>
						<label for="<?php CHBSHelper::getFormName('business_user_account_amount'); ?>">
							<?php esc_html_e('Amount to use id this period','chauffeur-booking-system'); ?>
							<span class="description"><?php esc_html_e('(required)','chauffeur-booking-system'); ?></span>
						</label>
					</th>
					<td>
						<input class="regular-text" type="text" name="<?php CHBSHelper::getFormName('business_user_account_amount'); ?>" id="<?php CHBSHelper::getFormName('business_user_account_amount'); ?>" value="<?php echo esc_attr($this->data['meta']['business_user_account_amount']); ?>">
					</td>
				</tr>
			</tbody>
		</table>
<?php
		if(is_array($this->data['meta']['business_user_account_transaction']))
		{
			if(count($this->data['meta']['business_user_account_transaction']))
			{
?>
		<h2><?php _e('Business account transactions','chauffeur-booking-system'); ?></h2>
		<div class="to">
			<div class="to-bussiness-account-transaction">
				<table class="to-table">
					<tr>
						<th><div><?php esc_html_e('Booking ID','chauffeur-booking-system'); ?></div></th>
						<th><div><?php esc_html_e('Date and time','chauffeur-booking-system'); ?></div></th>
						<th><div><?php esc_html_e('Amount before booking','chauffeur-booking-system'); ?></div></th>
						<th><div><?php esc_html_e('Booking sum','chauffeur-booking-system'); ?></div></th>
						<th><div><?php esc_html_e('Amount after booking','chauffeur-booking-system'); ?></div></th>
					</tr>
<?php
				foreach($this->data['meta']['business_user_account_transaction'] as $index=>$value)
				{
?>
					<tr>
						<td><div><?php esc_html_e($value['booking_id']); ?></div></td>
						<td><div><?php esc_html_e($value['booking_date'].' '.$value['booking_time']); ?></div></td>
						<td><div><?php esc_html_e($value['amount_before_booking']); ?></div></td>
						<td><div><?php esc_html_e($value['booking_sum']); ?></div></td>
						<td><div><?php esc_html_e($value['amount_after_booking']); ?></div></td>
					</tr>
<?php
				}
?>
				</table>
			</div>
		</div>
		<br/>
<?php
			}
		}
?>
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{	
				$('#profile-page').on('focusin','.to-datepicker-custom',function()
				{
					$(this).datepicker(
					{ 
						inline													:	true,
						dateFormat                                              :	'<?php echo CHBSJQueryUIDatePicker::convertDateFormat(CHBSOption::getOption('date_format')); ?>',
						prevText												:   '',
						nextText                                                :   ''
					});
				});   
			});
		</script>