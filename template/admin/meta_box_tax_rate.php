<?php 
		echo $this->data['nonce']; 
?>	
		<div class="to">
			<div class="ui-tabs">
				<ul>
					<li><a href="#meta-box-tax-rate-1"><?php esc_html_e('General','chauffeur-booking-system'); ?></a></li>
				</ul>
				<div id="meta-box-tax-rate-1">
					<ul class="to-form-field-list">
						<?php echo CHBSHelper::createPostIdField(__('Tax rate ID','chauffeur-booking-system')); ?>
						<li>
							<h5><?php esc_html_e('Value','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Percentage value of tax rate. Floating point values are allowed, up to two decimal places in the range 0-100.','chauffeur-booking-system'); ?></span>
							<div>
								<input type="text" maxlength="6" name="<?php CHBSHelper::getFormName('tax_rate_value'); ?>" id="<?php CHBSHelper::getFormName('tax_rate_value'); ?>" value="<?php echo esc_attr($this->data['meta']['tax_rate_value']); ?>"/>
							</div>
						</li>  
						<li>
							<h5><?php esc_html_e('Default tax rate','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Mark this tax rate as default.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('The default rate will be pre-selected when adding new items (e.g. vehicles, routes) in the dashboard.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('Only one default tax rate can be set at a time. Selecting another rate as default will override the previous one upon saving.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-radio-button">
								<input type="radio" value="1" id="<?php CHBSHelper::getFormName('tax_rate_default_1'); ?>" name="<?php CHBSHelper::getFormName('tax_rate_default'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['tax_rate_default'],1); ?>/>
								<label for="<?php CHBSHelper::getFormName('tax_rate_default_1'); ?>"><?php esc_html_e('Yes','chauffeur-booking-system'); ?></label>
								<input type="radio" value="0" id="<?php CHBSHelper::getFormName('tax_rate_default_0'); ?>" name="<?php CHBSHelper::getFormName('tax_rate_default'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['tax_rate_default'],0); ?>/>
								<label for="<?php CHBSHelper::getFormName('tax_rate_default_0'); ?>"><?php esc_html_e('No','chauffeur-booking-system'); ?></label>
							</div>
						</li>	
					</ul>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{	
				$('.to').themeOptionElement({init:true});
			});
		</script>