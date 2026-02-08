		
		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Status','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enable or disable Google reCAPTCHA.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_recaptcha_enable_1'); ?>" name="<?php CHBSHelper::getFormName('google_recaptcha_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['google_recaptcha_enable'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('google_recaptcha_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('google_recaptcha_enable_0'); ?>" name="<?php CHBSHelper::getFormName('google_recaptcha_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['google_recaptcha_enable'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('google_recaptcha_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>					
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('API type','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php echo sprintf(__('Select API type. You can find more info <a href="%s" target="_blank">here</a>.','chauffeur-booking-system'),'https://cloud.google.com/recaptcha-enterprise/docs/compare-versions'); ?>
				</span>
				<div class="to-clear-fix">
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_recaptcha_api_type_1'); ?>" name="<?php CHBSHelper::getFormName('google_recaptcha_api_type'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['google_recaptcha_api_type'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('google_recaptcha_api_type_1'); ?>"><?php esc_html_e('reCAPTCHA v3','chauffeur-booking-system'); ?></label>
						<input type="radio" value="2" id="<?php CHBSHelper::getFormName('google_recaptcha_api_type_0'); ?>" name="<?php CHBSHelper::getFormName('google_recaptcha_api_type'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['google_recaptcha_api_type'],2); ?>/>
						<label for="<?php CHBSHelper::getFormName('google_recaptcha_api_type_0'); ?>"><?php esc_html_e('reCAPTCHA v3 Enterprise','chauffeur-booking-system'); ?></label>
					</div>
				</div>		
			</li>			
			<li>
				<h5><?php esc_html_e('Site API key','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php echo sprintf(__('Enter Site API key. You can generate your own key <a href="%s" target="_blank">here</a>.','chauffeur-booking-system'),'https://cloud.google.com/recaptcha-enterprise/docs/create-key'); ?>
				</span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('google_recaptcha_site_key'); ?>" id="<?php CHBSHelper::getFormName('google_recaptcha_site_key'); ?>" value="<?php echo esc_attr($this->data['option']['google_recaptcha_site_key']); ?>"/>
				</div>
			</li>	
			<li>
				<h5><?php esc_html_e('Secret key','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php echo sprintf(__('Enter secret key. You can generate your own key <a href="%s" target="_blank">here</a>.','chauffeur-booking-system'),'https://cloud.google.com/recaptcha-enterprise/docs/create-key'); ?>
				</span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('google_recaptcha_secret_key'); ?>" id="<?php CHBSHelper::getFormName('google_recaptcha_secret_key'); ?>" value="<?php echo esc_attr($this->data['option']['google_recaptcha_secret_key']); ?>"/>
				</div>
			</li>	
			<li>
				<h5><?php esc_html_e('Score','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enter score - a value from 0.0 to 1.0.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('google_recaptcha_score'); ?>" id="<?php CHBSHelper::getFormName('google_recaptcha_score'); ?>" value="<?php echo esc_attr($this->data['option']['google_recaptcha_score']); ?>"/>
				</div>
			</li>	
			<li>
				<h5><?php esc_html_e('Badge status','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Show/hide Google reCAPTCHA badge.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_recaptcha_badge_enable_1'); ?>" name="<?php CHBSHelper::getFormName('google_recaptcha_badge_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['google_recaptcha_badge_enable'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('google_recaptcha_badge_enable_1'); ?>"><?php esc_html_e('Show','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('google_recaptcha_badge_enable_0'); ?>" name="<?php CHBSHelper::getFormName('google_recaptcha_badge_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['google_recaptcha_badge_enable'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('google_recaptcha_badge_enable_0'); ?>"><?php esc_html_e('Hide','chauffeur-booking-system'); ?></label>
					</div>					
				</div>
			</li>	
		</ul>