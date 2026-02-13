<?php
		$ExtensionHelper=new CHBSExtensionHelper(PLUGIN_CHBSEGAE_PREFIX);
?>		
		<div class="to-notice-small to-notice-small-info">
			<?php esc_html_e('If you already have Google Analytics code included to your site, you can ignore settings in this tab.','chauffeur-booking-system-extension-ga-event'); ?>
		</div>

		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Google Analytics code','chauffeur-booking-system-extension-ga-event'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Google Analytics code.','chauffeur-booking-system-extension-ga-event'); ?>
				</span>
				<div class="to-clear-fix">
					<textarea name="<?php $ExtensionHelper->getFormName('ga_code'); ?>" id="<?php $ExtensionHelper->getFormName('ga_code'); ?>" rows="1" cels="1"><?php echo esc_html($this->data['option']['ga_code']); ?></textarea>
				</div>
			</li> 
		</ul>
