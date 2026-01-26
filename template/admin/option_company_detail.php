
		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Name','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Name.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('company_detail_name'); ?>" id="<?php CHBSHelper::getFormName('company_detail_name'); ?>" value="<?php echo esc_attr($this->data['option']['company_detail_name']); ?>"/>
				</div>
			</li> 
			<li>
				<h5><?php esc_html_e('Tax number','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Tax number.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('company_detail_tax_number'); ?>" id="<?php CHBSHelper::getFormName('company_detail_tax_number'); ?>" value="<?php echo esc_attr($this->data['option']['company_detail_tax_number']); ?>"/>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('Street','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Street.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('company_detail_street_name'); ?>" id="<?php CHBSHelper::getFormName('company_detail_street_name'); ?>" value="<?php echo esc_attr($this->data['option']['company_detail_street_name']); ?>"/>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('Street number','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Street number.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('company_detail_street_number'); ?>" id="<?php CHBSHelper::getFormName('company_detail_street_number'); ?>" value="<?php echo esc_attr($this->data['option']['company_detail_street_number']); ?>"/>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('State','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('State.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('company_detail_state'); ?>" id="<?php CHBSHelper::getFormName('company_detail_state'); ?>" value="<?php echo esc_attr($this->data['option']['company_detail_state']); ?>"/>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('Postal code','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Postal code.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('company_detail_postal_code'); ?>" id="<?php CHBSHelper::getFormName('company_detail_postal_code'); ?>" value="<?php echo esc_attr($this->data['option']['company_detail_postal_code']); ?>"/>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('City','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('City.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('company_detail_city'); ?>" id="<?php CHBSHelper::getFormName('company_detail_city'); ?>" value="<?php echo esc_attr($this->data['option']['company_detail_city']); ?>"/>
				</div>
			</li>			
			<li>
				<h5><?php esc_html_e('Country','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Country.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<select name="<?php CHBSHelper::getFormName('company_detail_country'); ?>">
<?php
		foreach($this->data['dictionary']['country'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['option']['company_detail_country'],$index,false)).'>'.esc_html($value[0]).'</option>';
?>
					</select>  
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('Phone number','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Phone number.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('company_detail_phone_number'); ?>" id="<?php CHBSHelper::getFormName('company_detail_phone_number'); ?>" value="<?php echo esc_attr($this->data['option']['company_detail_phone_number']); ?>"/>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('E-mail address','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('E-mail address.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('company_detail_email_address'); ?>" id="<?php CHBSHelper::getFormName('company_detail_email_address'); ?>" value="<?php echo esc_attr($this->data['option']['company_detail_email_address']); ?>"/>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('Bank name','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Bank name.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('company_detail_bank_name'); ?>" id="<?php CHBSHelper::getFormName('company_detail_bank_name'); ?>" value="<?php echo esc_attr($this->data['option']['company_detail_bank_name']); ?>"/>
				</div>
			</li>			
			<li>
				<h5><?php esc_html_e('SWIFT','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('SWIFT.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('company_detail_bank_swift'); ?>" id="<?php CHBSHelper::getFormName('company_detail_bank_swift'); ?>" value="<?php echo esc_attr($this->data['option']['company_detail_bank_swift']); ?>"/>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('IBAN','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('IBAN.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('company_detail_bank_iban'); ?>" id="<?php CHBSHelper::getFormName('company_detail_bank_iban'); ?>" value="<?php echo esc_attr($this->data['option']['company_detail_bank_iban']); ?>"/>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('Account number','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Account number.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('company_detail_bank_account_number'); ?>" id="<?php CHBSHelper::getFormName('company_detail_bank_account_number'); ?>" value="<?php echo esc_attr($this->data['option']['company_detail_bank_account_number']); ?>"/>
				</div>
			</li>			
		</ul>