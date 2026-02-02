<?php
		$Validation=new CHBSValidation();
?>
		<div class="chbs-clear-fix chbs-booking-complete"> 
			<div class="chbs-notice chbs-hidden"></div>
			<div class="chbs-meta-icon-tick">
				<div></div>
				<div></div>
			</div>
			<h3><?php esc_html_e('Thank you for your order','chauffeur-booking-system'); ?></h3>
			<div class="chbs-booking-complete-payment">
				<div class="chbs-booking-complete-payment-paypal">
					<a href="#" class="chbs-button chbs-button-style-1"><?php esc_html_e('Pay via PayPal','chauffeur-booking-system'); ?></a>	
					<div><?php echo sprintf(__('You will be redirected to the payment page within <span>%s</span> second.','chauffeur-booking-system'),$this->data['meta']['payment_paypal_redirect_duration']); ?></div>
				</div>
				<div class="chbs-booking-complete-payment-stripe">
					<a href="#" class="chbs-button chbs-button-style-1"><?php esc_html_e('Pay via Credit Card','chauffeur-booking-system'); ?></a>
					<div><?php echo sprintf(__('You will be redirected to the payment page within <span>%s</span> second.','chauffeur-booking-system'),$this->data['meta']['payment_stripe_redirect_duration']); ?></div>
				</div>
				<div class="chbs-booking-complete-payment-tpay">
					<a href="#" class="chbs-button chbs-button-style-1"><?php esc_html_e('Pay via Tpay','chauffeur-booking-system'); ?></a>
					<div><?php echo sprintf(__('You will be redirected to the payment page within <span>%s</span> second.','chauffeur-booking-system'),$this->data['meta']['payment_tpay_redirect_duration']); ?></div>
				</div>
				<?php echo apply_filters('chbs_payment_public_step_5',null,$this->data['meta']); ?>
				<div class="chbs-booking-complete-payment-woocommerce">
					<a href="#" class="chbs-button chbs-button-style-1"><?php esc_html_e('Pay for order','chauffeur-booking-system'); ?></a>
				</div>				
				<div class="chbs-booking-complete-payment-cash">
					<a href="<?php echo ($Validation->isEmpty($this->data['meta']['thank_you_page_button_back_to_home_url_address']) ? the_permalink() : esc_url($this->data['meta']['thank_you_page_button_back_to_home_url_address'])); ?>" class="chbs-button chbs-button-style-1"><?php echo ($Validation->isEmpty($this->data['meta']['thank_you_page_button_back_to_home_label']) ? esc_html__('Back to home','chauffeur-booking-system') : $this->data['meta']['thank_you_page_button_back_to_home_label']); ?></a>
				</div>
				<div class="chbs-booking-complete-payment-wire-transfer">
					<a href="<?php echo ($Validation->isEmpty($this->data['meta']['thank_you_page_button_back_to_home_url_address']) ? the_permalink() : esc_url($this->data['meta']['thank_you_page_button_back_to_home_url_address'])); ?>" class="chbs-button chbs-button-style-1"><?php echo ($Validation->isEmpty($this->data['meta']['thank_you_page_button_back_to_home_label']) ? esc_html__('Back to home','chauffeur-booking-system') : $this->data['meta']['thank_you_page_button_back_to_home_label']); ?></a>
				</div>
				<div class="chbs-booking-complete-payment-credit-card-pickup">
					<a href="<?php echo ($Validation->isEmpty($this->data['meta']['thank_you_page_button_back_to_home_url_address']) ? the_permalink() : esc_url($this->data['meta']['thank_you_page_button_back_to_home_url_address'])); ?>" class="chbs-button chbs-button-style-1"><?php echo ($Validation->isEmpty($this->data['meta']['thank_you_page_button_back_to_home_label']) ? esc_html__('Back to home','chauffeur-booking-system') : $this->data['meta']['thank_you_page_button_back_to_home_label']); ?></a>
				</div>
				<div class="chbs-booking-complete-payment-disable">
					<a href="<?php echo ($Validation->isEmpty($this->data['meta']['thank_you_page_button_back_to_home_url_address']) ? the_permalink() : esc_url($this->data['meta']['thank_you_page_button_back_to_home_url_address'])); ?>" class="chbs-button chbs-button-style-1"><?php echo ($Validation->isEmpty($this->data['meta']['thank_you_page_button_back_to_home_label']) ? esc_html__('Back to home','chauffeur-booking-system') : $this->data['meta']['thank_you_page_button_back_to_home_label']); ?></a>
				</div>
			</div>
		</div>
