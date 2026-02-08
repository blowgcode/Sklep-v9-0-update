<?php 
		echo $this->data['nonce']; 
?>	
		<div class="to">
			<div class="ui-tabs">
				<ul>
					<li><a href="#meta-box-vehicle-company-1"><?php esc_html_e('General','chauffeur-booking-system'); ?></a></li>
				</ul>
				<div id="meta-box-vehicle-company-1">
					<ul class="to-form-field-list">
						<?php echo CHBSHelper::createPostIdField(__('Vehicle company ID','chauffeur-booking-system')); ?>
						<li>
							<h5><?php esc_html_e('Contact details','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Contact details.','chauffeur-booking-system'); ?></span>
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('Phone number.','chauffeur-booking-system'); ?></span>
								<div><input type="text" name="<?php CHBSHelper::getFormName('contact_phone_number'); ?>" id="<?php CHBSHelper::getFormName('contact_phone_number'); ?>" value="<?php echo esc_attr($this->data['meta']['contact_phone_number']); ?>"/></div>
							</div>
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('E-mail address.','chauffeur-booking-system'); ?></span>
								<div><input type="text" name="<?php CHBSHelper::getFormName('contact_email_address'); ?>" id="<?php CHBSHelper::getFormName('contact_email_address'); ?>" value="<?php echo esc_attr($this->data['meta']['contact_email_address']); ?>"/></div>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Address details','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Address details.','chauffeur-booking-system'); ?></span>
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('Street name.','chauffeur-booking-system'); ?></span>
								<div><input type="text" name="<?php CHBSHelper::getFormName('address_street_name'); ?>" id="<?php CHBSHelper::getFormName('address_street_name'); ?>" value="<?php echo esc_attr($this->data['meta']['address_street_name']); ?>"/></div>
							</div>
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('Street number.','chauffeur-booking-system'); ?></span>
								<div><input type="text" name="<?php CHBSHelper::getFormName('address_street_number'); ?>" id="<?php CHBSHelper::getFormName('address_street_number'); ?>" value="<?php echo esc_attr($this->data['meta']['address_street_number']); ?>"/></div>
							</div>
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('City.','chauffeur-booking-system'); ?></span>
								<div><input type="text" name="<?php CHBSHelper::getFormName('address_city'); ?>" id="<?php CHBSHelper::getFormName('address_city'); ?>" value="<?php echo esc_attr($this->data['meta']['address_city']); ?>"/></div>
							</div>
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('State.','chauffeur-booking-system'); ?></span>
								<div><input type="text" name="<?php CHBSHelper::getFormName('address_state'); ?>" id="<?php CHBSHelper::getFormName('address_state'); ?>" value="<?php echo esc_attr($this->data['meta']['address_state']); ?>"/></div>
							</div>
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('Postal code.','chauffeur-booking-system'); ?></span>
								<div><input type="text" name="<?php CHBSHelper::getFormName('address_postal_code'); ?>" id="<?php CHBSHelper::getFormName('address_postal_code'); ?>" value="<?php echo esc_attr($this->data['meta']['address_postal_code']); ?>"/></div>
							</div>
							<div class="to-clear-fix">
								<span class="to-legend-field"><?php esc_html_e('Country.','chauffeur-booking-system'); ?></span>
								<div>
									<select name="<?php CHBSHelper::getFormName('address_country'); ?>" id="<?php CHBSHelper::getFormName('address_country'); ?>">
<?php
		foreach($this->data['dictionary']['country'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['address_country'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
									</select>  
								</div>
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