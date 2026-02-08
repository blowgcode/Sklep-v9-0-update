		
		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Google Maps API key','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enter your Google Maps API key.','chauffeur-booking-system'); ?><br/>
					<?php esc_html_e('You can create and manage API keys in the Google Cloud Console.','chauffeur-booking-system'); ?><br/>
					<?php echo sprintf(__('More info: <a href="%s" target="_blank">Generating Google Maps API Key and Map ID</a>.','chauffeur-booking-system'),'https://quanticalabs.com/docs/chauffeur-booking-system/knowledge-base/generating-google-maps-api-key-and-map-id/'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('google_map_api_key'); ?>" id="<?php CHBSHelper::getFormName('google_map_api_key'); ?>" value="<?php echo esc_attr($this->data['option']['google_map_api_key']); ?>"/>
				</div>
			</li>		
			<li>
				<h5><?php esc_html_e('Google Map ID','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enter your Google Map ID.','chauffeur-booking-system'); ?><br/>
					<?php esc_html_e('You can create and manage Map IDs in the Google Cloud Console.','chauffeur-booking-system'); ?><br/>
					<?php echo sprintf(__('More info: <a href="%s" target="_blank">Generating Google Maps API Key and Map ID</a>.','chauffeur-booking-system'),'https://quanticalabs.com/docs/chauffeur-booking-system/knowledge-base/generating-google-maps-api-key-and-map-id/'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<input type="text" name="<?php CHBSHelper::getFormName('google_map_map_id'); ?>" id="<?php CHBSHelper::getFormName('google_map_map_id'); ?>" value="<?php echo esc_attr($this->data['option']['google_map_map_id']); ?>"/>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('Google Maps usage notification','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Displays a prompt asking for user consent to load the Google Maps library.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					 <div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_map_ask_load_enable_1'); ?>" name="<?php CHBSHelper::getFormName('google_map_ask_load_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['google_map_ask_load_enable'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('google_map_ask_load_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('google_map_ask_load_enable_0'); ?>" name="<?php CHBSHelper::getFormName('google_map_ask_load_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['google_map_ask_load_enable'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('google_map_ask_load_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
			</li>	
			<li>
				<h5><?php esc_html_e('Google Maps server data validation','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enable or disable Google Maps booking data validation on the server side.','chauffeur-booking-system'); ?><br/>
					<?php esc_html_e('This option has to be enabled in the production mode.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					 <div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_map_server_data_validation_enable_1'); ?>" name="<?php CHBSHelper::getFormName('google_map_server_data_validation_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['google_map_server_data_validation_enable'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('google_map_server_data_validation_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('google_map_server_data_validation_enable_0'); ?>" name="<?php CHBSHelper::getFormName('google_map_server_data_validation_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['google_map_server_data_validation_enable'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('google_map_server_data_validation_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('Remove duplicated Google Maps scripts','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enable this option to remove Google Maps script from theme and other, included plugins.','chauffeur-booking-system'); ?><br/>
					<?php esc_html_e('This option allows to prevent errors related with including the same script more than once.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					 <div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('google_map_duplicate_script_remove_1'); ?>" name="<?php CHBSHelper::getFormName('google_map_duplicate_script_remove'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['google_map_duplicate_script_remove'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('google_map_duplicate_script_remove_1'); ?>"><?php esc_html_e('Enable (remove)','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('google_map_duplicate_script_remove_0'); ?>" name="<?php CHBSHelper::getFormName('google_map_duplicate_script_remove'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['google_map_duplicate_script_remove'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('google_map_duplicate_script_remove_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
			</li>	
			<li>
				<h5><?php esc_html_e('Google Maps API key restrictions','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php echo sprintf(__('To avoid unauthorized access to the API key by external applications, set the following domain and server IP address in the "Application restrictions" section, which is available during the editing key in the <a href="%s" target="_blank">Google Cloud Console</a>.','chauffeur-booking-system'),'https://console.cloud.google.com/google/maps-apis/credentials'); ?>
				</span>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Domain.','chauffeur-booking-system'); ?></span>
					<div class="to-field-disabled">
						<?php echo esc_html($this->data['option']['domain']); ?>
						<a href="#" class="to-copy-to-clipboard to-float-right" data-clipboard-text="<?php echo esc_attr($this->data['option']['domain']); ?>" data-label-on-success="<?php echo esc_attr__('Copied!','chauffeur-booking-system'); ?>"><?php esc_html_e('Copy','chauffeur-booking-system'); ?></a>
					</div>
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Server IP address.','chauffeur-booking-system'); ?></span>
					<div class="to-field-disabled">
						<?php echo esc_html($this->data['option']['server_ip_address']); ?>
						<a href="#" class="to-copy-to-clipboard to-float-right" data-clipboard-text="<?php echo esc_attr($this->data['option']['server_ip_address']); ?>" data-label-on-success="<?php echo esc_attr__('Copied!','chauffeur-booking-system'); ?>"><?php esc_html_e('Copy','chauffeur-booking-system'); ?></a>
					</div>
				</div>					
			</li>	
		</ul>