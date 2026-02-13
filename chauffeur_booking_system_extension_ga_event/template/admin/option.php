<?php
		$ExtensionHelper=new CHBSExtensionHelper(PLUGIN_CHBSEGAE_PREFIX);
?>
		<div class="to to-to" style="display:none">

			<form name="to_form" id="to_form" method="POST" action="#">

				<?php echo $this->data['nonce']; ?>
				
				<div id="to_notice"></div> 

				<div class="to-header to-clear-fix">

					<div class="to-header-left">

						<div>
							<h3><?php esc_html_e('QuanticaLabs','chauffeur-booking-system-extension-ga-event'); ?></h3>
							<h6><?php esc_html_e('Add-on Options','chauffeur-booking-system-extension-ga-event'); ?></h6>
						</div>

					</div>

					<div class="to-header-right">

						<div>
							<h3>
								<?php esc_html_e('Chauffeur Taxi Booking System Add-on: GA Events','chauffeur-booking-system-extension-ga-event'); ?>
							</h3>
							<h6>
								<?php echo sprintf(esc_html__('WordPress Plugin Add-on ver. %s','chauffeur-booking-system-extension-ga-event'),PLUGIN_CHBSEGAE_VERSION); ?>
							</h6>
							&nbsp;&nbsp;
							<a href="<?php echo esc_url('http://support.quanticalabs.com'); ?>" target="_blank"><?php esc_html_e('Support Forum','chauffeur-booking-system-extension-ga-event'); ?></a>
							<a href="<?php echo esc_url('https://codecanyon.net/collections/11597341-add-ons-for-chauffeur-taxi-booking-system'); ?>"><?php esc_html_e('Plugin site','chauffeur-booking-system-extension-ga-event'); ?></a>
						</div>

						<a href="<?php echo esc_url('http://quanticalabs.com'); ?>" class="to-header-right-logo"></a>

					</div>

				</div>

				<div class="to-content to-clear-fix">

					<div class="to-content-left">

						<ul class="to-menu" id="to_menu">
							<li>
								<a href="#google_analytics"><?php esc_html_e('Google Analytics','chauffeur-booking-system-extension-ga-event'); ?><span></span></a>
							</li>
							<li>
								<a href="#event"><?php esc_html_e('Events','chauffeur-booking-system-extension-ga-event'); ?><span></span></a>
								<ul>
									<li><a href="#event_main"><?php esc_html_e('Main','chauffeur-booking-system-extension-ga-event'); ?></a></li>
									<li><a href="#event_event"><?php esc_html_e('Events','chauffeur-booking-system-extension-ga-event'); ?></a></li>
								</ul>
							</li>
						</ul>

					</div>

					<div class="to-content-right" id="to_panel">
<?php
		$content=array
		(
			'google_analytics',
			'event_main',
			'event_event'
		);
		
		foreach($content as $value)
		{
?>
						<div id="<?php echo esc_attr($value); ?>">
<?php
			echo CHBSTemplate::outputS($this->data,PLUGIN_CHBSEGAE_TEMPLATE_PATH.'admin/option_'.$value.'.php');
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
							<li><a href="<?php echo esc_url('http://themeforest.net/user/QuanticaLabs?ref=quanticalabs'); ?>" class="to-social-list-envato" title="<?php esc_attr_e('Envato','chauffeur-booking-system-extension-ga-event'); ?>"></a></li>
							<li><a href="<?php echo esc_url('http://www.facebook.com/QuanticaLabs'); ?>" class="to-social-list-facebook" title="<?php esc_attr_e('Facebook','chauffeur-booking-system-extension-ga-event'); ?>"></a></li>
							<li><a href="<?php echo esc_url('https://twitter.com/quanticalabs'); ?>" class="to-social-list-twitter" title="<?php esc_attr_e('Twitter','chauffeur-booking-system-extension-ga-event'); ?>"></a></li>
							<li><a href="<?php echo esc_url('http://quanticalabs.tumblr.com/'); ?>" class="to-social-list-tumblr" title="<?php esc_attr_e('Tumblr','chauffeur-booking-system-extension-ga-event'); ?>"></a></li>
						</ul>

					</div>
					
					<div class="to-footer-right">
						<input type="submit" value="<?php esc_attr_e('Save changes','chauffeur-booking-system-extension-ga-event'); ?>" name="Submit" id="Submit" class="to-button"/>
					</div>			
				
				</div>
				
				<input type="hidden" name="action" id="action" value="<?php echo esc_attr(PLUGIN_CHBSEGAE_CONTEXT.'_option_page_save'); ?>" />
<?php
		CHBSHelper::addInlineScript('chbs-admin',
		'
			jQuery(document).ready(function($)
			{
				chbsegaeRunThemeOption();
			});
		');
?>
			</form>
			
		</div>