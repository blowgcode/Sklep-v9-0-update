
		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Add JS/CSS files only to page with shortcode','chauffeur-booking-system'); ?></h5>
				<span class="to-legend"><?php esc_html_e('Add JavaScript/CSS scripts/stylesheets only to page which includes plugin shortcode.','chauffeur-booking-system'); ?></span>
				<div class="to-clear-fix">
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('system_library_on_shortcode_page_enable_1'); ?>" name="<?php CHBSHelper::getFormName('system_library_on_shortcode_page_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['system_library_on_shortcode_page_enable'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('system_library_on_shortcode_page_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('system_library_on_shortcode_page_enable_0'); ?>" name="<?php CHBSHelper::getFormName('system_library_on_shortcode_page_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['system_library_on_shortcode_page_enable'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('system_library_on_shortcode_page_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
			</li> 
		</ul>	
			