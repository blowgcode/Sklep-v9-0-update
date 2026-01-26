<?php
		$Date=new CHBSDate();
?>
		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Count','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Number of coupons to generate.','chauffeur-booking-system'); ?><br/>
					<?php esc_html_e('Allowed are integer numbers from 1 to 999.','chauffeur-booking-system'); ?>
				</span>
				<div>
					<input type="text" maxlength="3" name="<?php CHBSHelper::getFormName('coupon_generate_count'); ?>" id="<?php CHBSHelper::getFormName('coupon_generate_count'); ?>" value="<?php echo esc_attr($this->data['option']['coupon_generate_count']); ?>"/>
				</div>
			</li> 
			<li>
				<h5><?php esc_html_e('Usage limit','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Current usage count of the code. Allowed are integer values from range 1-9999. Leave blank for unlimited.','chauffeur-booking-system'); ?></span>
				<div>
					<input type="text" maxlength="4" name="<?php CHBSHelper::getFormName('coupon_generate_usage_limit'); ?>" id="<?php CHBSHelper::getFormName('coupon_generate_usage_limit'); ?>" value="<?php echo esc_attr($this->data['option']['coupon_generate_usage_limit']); ?>"/>
				</div>
			</li>							 
			<li>
				<h5><?php esc_html_e('Percentage discount','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Percentage discount. Allowed are integer numbers from 0 to 99.','chauffeur-booking-system'); ?></span>
				<div>
					<input type="text" maxlength="2" name="<?php CHBSHelper::getFormName('coupon_generate_discount_percentage'); ?>" id="<?php CHBSHelper::getFormName('coupon_generate_discount_percentage'); ?>" value="<?php echo esc_attr($this->data['option']['coupon_generate_discount_percentage']); ?>"/>
				</div>
			</li>	 
			<li>
				<h5><?php esc_html_e('Active from','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Start date. Leave blank for no start date.','chauffeur-booking-system'); ?></span>
				<div>
					<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('coupon_generate_active_date_start'); ?>" id="<?php CHBSHelper::getFormName('coupon_generate_active_date_start'); ?>" value="<?php echo esc_attr($this->data['option']['coupon_generate_active_date_start']); ?>"/>
				</div>
			</li>  
			<li>
				<h5><?php esc_html_e('Active to','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Stop date. Leave blank for no start date.','chauffeur-booking-system'); ?></span>
				<div>
					<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('coupon_generate_active_date_stop'); ?>" id="<?php CHBSHelper::getFormName('coupon_generate_active_date_stop'); ?>" value="<?php echo esc_attr($this->data['option']['coupon_generate_active_date_stop']); ?>"/>
				</div>
			</li>
			<li>
				<input type="button" name="<?php CHBSHelper::getFormName('create_coupon_code'); ?>" id="<?php CHBSHelper::getFormName('create_coupon_code'); ?>" class="to-button to-margin-0" value="<?php esc_attr_e('Create coupons','chauffeur-booking-system'); ?>"/>
			</li>
		</ul>
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{	
				toCreateCustomDateTimePicker();
				
				$('#<?php CHBSHelper::getFormName('create_coupon_code'); ?>').bind('click',function(e) 
				{
					e.preventDefault();
					$('#action').val('<?php echo PLUGIN_CHBS_CONTEXT.'_option_page_create_coupon_code'; ?>');
					$('#to_form').submit();
					$('#action').val('<?php echo PLUGIN_CHBS_CONTEXT.'_option_page_save'; ?>');
				});
			});
		</script>