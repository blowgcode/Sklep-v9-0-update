
		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('WooCommerce order items','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enable or disable merging items with the same tax rate in the WooCommerce order.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('woocommerce_order_reduce_item_1'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_order_reduce_item'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['woocommerce_order_reduce_item'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('woocommerce_order_reduce_item_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('woocommerce_order_reduce_item_0'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_order_reduce_item'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['woocommerce_order_reduce_item'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('woocommerce_order_reduce_item_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
			</li>
			<li>
				<h5><?php esc_html_e('WooCommerce templates','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Use WooCommerce templates from the "Chauffeur Booking System for WordPress" plugin.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php CHBSHelper::getFormName('woocommerce_template_plugin_enable_1'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_template_plugin_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['woocommerce_template_plugin_enable'],1); ?>/>
						<label for="<?php CHBSHelper::getFormName('woocommerce_template_plugin_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
						<input type="radio" value="0" id="<?php CHBSHelper::getFormName('woocommerce_template_plugin_enable_0'); ?>" name="<?php CHBSHelper::getFormName('woocommerce_template_plugin_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['option']['woocommerce_template_plugin_enable'],0); ?>/>
						<label for="<?php CHBSHelper::getFormName('woocommerce_template_plugin_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>
					</div>
				</div>
			</li>
		</ul>