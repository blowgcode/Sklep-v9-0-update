
		<div class="to to-to" style="display:none">

			<form name="to_form" id="to_form" method="POST" action="#">

				<?php echo $this->data['nonce']; ?>
				
				<div id="to_notice"></div> 

				<div class="to-header to-clear-fix">

					<div class="to-header-left">

						<div>
							<h3><?php esc_html_e('QuanticaLabs','chauffeur-booking-system'); ?></h3>
							<h6><?php esc_html_e('Plugin Options','chauffeur-booking-system'); ?></h6>
						</div>

					</div>

					<div class="to-header-right">

						<div>
							<h3>
								<?php esc_html_e('Chauffeur Taxi Booking System','chauffeur-booking-system'); ?>
							</h3>
							<h6>
								<?php echo sprintf(esc_html__('WordPress Plugin ver. %s','chauffeur-booking-system'),PLUGIN_CHBS_VERSION); ?>
							</h6>
							&nbsp;&nbsp;
							<a href="<?php echo esc_url('http://support.quanticalabs.com'); ?>" target="_blank"><?php esc_html_e('Support Forum','chauffeur-booking-system'); ?></a>&nbsp;&middot;
							<a href="<?php echo esc_url('https://1.envato.market/chauffeur-booking-system-for-wordpress'); ?>"><?php esc_html_e('Plugin site','chauffeur-booking-system'); ?></a>&nbsp;&middot;
							<a href="<?php echo esc_url('https://1.envato.market/c/296919/275988/4415?u=https://codecanyon.net/collections/11597341-add-ons-for-chauffeur-taxi-booking-system'); ?>"><?php esc_html_e('Add-ons','chauffeur-booking-system'); ?></a>
						</div>

						<a href="<?php echo esc_url('http://quanticalabs.com'); ?>" class="to-header-right-logo"></a>

					</div>

				</div>

				<div class="to-content to-clear-fix">

					<div class="to-content-left">

						<ul class="to-menu" id="to_menu">
							<li>
								<a href="#general"><?php esc_html_e('General','chauffeur-booking-system'); ?><span></span></a>
								<ul>
									<li><a href="#general_main"><?php esc_html_e('Main','chauffeur-booking-system'); ?></a></li>
									<li><a href="#general_system"><?php esc_html_e('System','chauffeur-booking-system'); ?></a></li>
									<li><a href="#general_license"><?php esc_html_e('License','chauffeur-booking-system'); ?></a></li>
									<li><a href="#general_google_map"><?php esc_html_e('Google Maps','chauffeur-booking-system'); ?></a></li>
									<li><a href="#general_google_recaptcha"><?php esc_html_e('Google reCAPTCHA','chauffeur-booking-system'); ?></a></li>
									<li><a href="#general_woocommerce"><?php esc_html_e('WooCommerce','chauffeur-booking-system'); ?></a></li>	
								</ul>
							</li>
							<li>
								<a href="#email"><?php esc_html_e('E-mail','chauffeur-booking-system'); ?><span></span></a>
							</li>
							<li>
								<a href="#import_demo"><?php esc_html_e('Import demo','chauffeur-booking-system'); ?><span></span></a>
							</li>
							<li>
								<a href="#payment"><?php esc_html_e('Payments','chauffeur-booking-system'); ?><span></span></a>
							</li>
							<li>
								<a href="#webhook"><?php esc_html_e('Webhooks','chauffeur-booking-system'); ?><span></span></a>
							</li>
							<li>
								<a href="#coupon_creator"><?php esc_html_e('Coupons creator','chauffeur-booking-system'); ?><span></span></a>
							</li>
							<li>
								<a href="#exchange_rate"><?php esc_html_e('Exchange rates','chauffeur-booking-system'); ?><span></span></a>
							</li>
							<li>
								<a href="#booking_acceptance"><?php esc_html_e('Booking acceptance','chauffeur-booking-system'); ?><span></span></a>
							</li>
							<li>
								<a href="#booking_cancel"><?php esc_html_e('Booking cancelling','chauffeur-booking-system'); ?><span></span></a>
							</li>
							<li>
								<a href="#company_detail"><?php esc_html_e('Company details','chauffeur-booking-system'); ?><span></span></a>
							</li>
							<li>
								<a href="#log_manager"><?php esc_html_e('Log manager','chauffeur-booking-system'); ?><span></span></a>
								<ul>
									<li><a href="#log_manager_mail"><?php esc_html_e('Mail','chauffeur-booking-system'); ?></a></li>
									<li><a href="#log_manager_stripe"><?php esc_html_e('Stripe','chauffeur-booking-system'); ?></a></li>
									<li><a href="#log_manager_paypal"><?php esc_html_e('PayPal','chauffeur-booking-system'); ?></a></li>
									<li><a href="#log_manager_twilio"><?php esc_html_e('Twilio','chauffeur-booking-system'); ?></a></li>
									<li><a href="#log_manager_nexmo"><?php esc_html_e('Vonage','chauffeur-booking-system'); ?></a></li>
									<li><a href="#log_manager_license"><?php esc_html_e('License','chauffeur-booking-system'); ?></a></li>
									<li><a href="#log_manager_fixerio"><?php esc_html_e('Fixer.io','chauffeur-booking-system'); ?></a></li>
									<li><a href="#log_manager_telegram"><?php esc_html_e('Telegram','chauffeur-booking-system'); ?></a></li>
									<li><a href="#log_manager_geolocation"><?php esc_html_e('Geolocation','chauffeur-booking-system'); ?></a></li>
									<li><a href="#log_manager_product_info"><?php esc_html_e('Product Info','chauffeur-booking-system'); ?></a></li>
									<li><a href="#log_manager_google_map"><?php esc_html_e('Google Maps','chauffeur-booking-system'); ?></a></li>
									<li><a href="#log_manager_google_calendar"><?php esc_html_e('Google Calendar','chauffeur-booking-system'); ?></a></li>
									<li><a href="#log_manager_google_recaptcha"><?php esc_html_e('Google reCAPTCHA','chauffeur-booking-system'); ?></a></li>
									<li><a href="#log_manager_booking_data_validation"><?php esc_html_e('Booking data validation','chauffeur-booking-system'); ?></a></li>
								</ul>		
							</li>
							<li>
								<a href="https://codecanyon.net/collections/11597341-add-ons-for-chauffeur-taxi-booking-system/" target="_blank"><?php esc_html_e('Add-ons','chauffeur-booking-system'); ?><span></span></a>
							</li>
						</ul>

					</div>

					<div class="to-content-right" id="to_panel">
<?php
		$content=array
		(
			'general_main',
			'general_system',
			'general_license',
			'general_google_map',
			'general_google_recaptcha',
			'general_woocommerce',
			'email',
			'import_demo',
			'payment',
			'webhook',
			'coupon_creator',
			'exchange_rate',
			'booking_acceptance',
			'booking_cancel',
			'company_detail',
			'log_manager_mail',
			'log_manager_stripe',
			'log_manager_paypal',
			'log_manager_nexmo',
			'log_manager_license',
			'log_manager_fixerio',
			'log_manager_twilio',
			'log_manager_telegram',
			'log_manager_product_info',
			'log_manager_geolocation',
			'log_manager_google_map',
			'log_manager_google_calendar',
			'log_manager_google_recaptcha',
			'log_manager_booking_data_validation'
		);
		
		foreach($content as $value)
		{
?>
						<div id="<?php echo $value; ?>">
<?php
			$Template=new CHBSTemplate($this->data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/option_'.$value.'.php');
			echo $Template->output(false);
?>
						</div>
<?php
		}
?>
					</div>

				</div>

				<div class="to-footer to-clear-fix">

					<div class="to-footer-left">

						<ul class="to-social-list">
							<li><a href="<?php echo esc_url('http://themeforest.net/user/QuanticaLabs?ref=quanticalabs'); ?>" class="to-social-list-envato" title="<?php esc_attr_e('Envato','chauffeur-booking-system'); ?>"></a></li>
							<li><a href="<?php echo esc_url('http://www.facebook.com/QuanticaLabs'); ?>" class="to-social-list-facebook" title="<?php esc_attr_e('Facebook','chauffeur-booking-system'); ?>"></a></li>
							<li><a href="<?php echo esc_url('https://twitter.com/quanticalabs'); ?>" class="to-social-list-twitter" title="<?php esc_attr_e('Twitter','chauffeur-booking-system'); ?>"></a></li>
							<li><a href="<?php echo esc_url('http://quanticalabs.tumblr.com/'); ?>" class="to-social-list-tumblr" title="<?php esc_attr_e('Tumblr','chauffeur-booking-system'); ?>"></a></li>
						</ul>

					</div>
					
					<div class="to-footer-right">
						<input type="submit" value="<?php esc_attr_e('Save changes','chauffeur-booking-system'); ?>" name="Submit" id="Submit" class="to-button"/>
					</div>			
				
				</div>
				
				<input type="hidden" name="action" id="action" value="<?php echo esc_attr(PLUGIN_CHBS_CONTEXT.'_option_page_save'); ?>" />
				
				<script type="text/javascript">

					jQuery(document).ready(function($)
					{
						$('.to').themeOption({afterSubmit:function(response)
						{
							if(typeof(response.global.reload)!='undefined')
								location.reload();
					
							return(false);
						}});
						
						var element=$('.to').themeOptionElement({init:true});
						element.bindBrowseMedia('.to-button-browse');
					});

				</script>

			</form>
			
		</div>