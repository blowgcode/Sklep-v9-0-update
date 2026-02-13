<?php
		$ExtensionHelper=new CHBSExtensionHelper(PLUGIN_CHBSEGAE_PREFIX);
?>		
		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Events prefix','chauffeur-booking-system-extension-ga-event'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Prefix for all events, e.g: prefix_.','chauffeur-booking-system-extension-ga-event'); ?>
				</span>
				<div class="to-clear-fix">
					<input type="text" name="<?php $ExtensionHelper->getFormName('event_prefix'); ?>" id="<?php $ExtensionHelper->getFormName('event_prefix'); ?>" value="<?php echo esc_attr($this->data['option']['event_prefix']); ?>"/>
				</div>
			</li> 
			<li>
				<h5><?php esc_html_e('Browser console','chauffeur-booking-system-extension-ga-event'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enable or disable showing events with parameters in the browser console.','chauffeur-booking-system-extension-ga-event'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php $ExtensionHelper->getFormName('event_console_log_enable_1'); ?>" name="<?php $ExtensionHelper->getFormName('event_console_log_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['event_console_log_enable'],1); ?>/>
						<label for="<?php $ExtensionHelper->getFormName('event_console_log_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system-extension-ga-event'); ?></label>
						<input type="radio" value="0" id="<?php $ExtensionHelper->getFormName('event_console_log_enable_2'); ?>" name="<?php $ExtensionHelper->getFormName('event_console_log_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['event_console_log_enable'],0); ?>/>
						<label for="<?php $ExtensionHelper->getFormName('event_console_log_enable_2'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system-extension-ga-event'); ?></label>
					</div>
				</div>
			</li>  
			<li>
				<h5><?php esc_html_e('DebugView','chauffeur-booking-system-extension-ga-event'); ?></h5>
				<span class="to-legend">
					<?php echo sprintf(__('Enable or disable <a href="%s" target="_blank">Google Analytics DebugView</a> mode for all events.','chauffeur-booking-system-extension-ga-event'),'https://support.google.com/analytics/answer/7201382'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php $ExtensionHelper->getFormName('event_debug_view_enable_1'); ?>" name="<?php $ExtensionHelper->getFormName('event_debug_view_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['event_debug_view_enable'],1); ?>/>
						<label for="<?php $ExtensionHelper->getFormName('event_debug_view_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system-extension-ga-event'); ?></label>
						<input type="radio" value="0" id="<?php $ExtensionHelper->getFormName('event_debug_view_enable_2'); ?>" name="<?php $ExtensionHelper->getFormName('event_debug_view_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['event_debug_view_enable'],0); ?>/>
						<label for="<?php $ExtensionHelper->getFormName('event_debug_view_enable_2'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system-extension-ga-event'); ?></label>
					</div>
				</div>
			</li> 
		</ul>