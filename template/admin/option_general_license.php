<?php
		$License=new CHBSLicense();
?>
		<ul class="to-form-field-list">
			<li>
<?php
		if($License->isVerified())
		{
?>
				<div class="to-notice-small to-notice-small-success">
					<?php echo __('<b>Thank you!</b> Your license is verified.','chauffeur-booking-system'); ?>
				</div>			
<?php
		}
		else
		{
?>
				<div class="to-notice-small to-notice-small-error">
					<?php esc_html_e('Your license is not verified. Please enter the purchase code and click on the "Verify purchase" button.','chauffeur-booking-system'); ?>
				</div>							
<?php
		}
?>
				<div class="to-notice-small to-notice-small-info">
					<?php echo sprintf(__('If you purchased the standalone "Chauffeur Booking System for WordPress" plugin version from the <a href="%s">CodeCanyon</a>, you need to enter purchase code from the plugin license file. If you purchased this plugin together with the "AutoRide - Chauffeur Limousine Booking WordPress Theme" theme from the <a href="%s">ThemeForest</a>, you need to enter purchase code from theme license file.','chauffeur-booking-system'),'https://1.envato.market/chauffeur-booking-system-for-wordpress','https://1.envato.market/autoride-chauffeur-booking-wordpress-theme'); ?>
				</div>
				<h5><?php esc_html_e('License details','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php echo sprintf(__('Enter the license details. You can find all required information in the <a href="%s" target="_blank">License Certificate</a> PDF document.','chauffeur-booking-system'),'https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code'); ?><br/>
				</span>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('License ID.','chauffeur-booking-system'); ?></span>
					<div class="to-field-disabled" id="to-license-id">
						<?php echo esc_attr($this->data['option']['license_license_id']); ?>
					</div>
				</div>				
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Product ID.','chauffeur-booking-system'); ?></span>
					<div class="to-field-disabled">
						<?php echo esc_attr($this->data['option']['license_product_id']); ?>
					</div>
				</div>
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Supported until.','chauffeur-booking-system'); ?></span>
					<div class="to-field-disabled" id="to-license-support-period">
						<?php echo esc_attr($this->data['option']['license_support_datetime']); ?>
						<div class="to-float-right">
							<a href="<?php echo CHBSPlugin::getProductEnvatoURL(); ?>" target="_blank"><?php esc_html_e('Renew the support','chauffeur-booking-system'); ?></a>
						</div>
					</div>
				</div>	
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Last checked.','chauffeur-booking-system'); ?></span>
					<div class="to-field-disabled" id="to-license-last-check-datetime">
						<?php echo esc_attr($this->data['option']['license_last_check_datetime']); ?>
					</div>
				</div>	
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Domain.','chauffeur-booking-system'); ?></span>
					<div class="to-field-disabled">
						<?php echo esc_attr($this->data['option']['license_domain']); ?>
					</div>
				</div>	
				<div class="to-clear-fix">
					<span class="to-legend-field"><?php esc_html_e('Purchase code.','chauffeur-booking-system'); ?></span>
					<input type="text" name="<?php CHBSHelper::getFormName('license_purchase_code'); ?>" id="<?php CHBSHelper::getFormName('license_purchase_code'); ?>" value="<?php echo esc_attr($this->data['option']['license_purchase_code']); ?>"/>
				</div>
				<div class="to-clear-fix">
					<input type="button" name="<?php CHBSHelper::getFormName('license_verify'); ?>" id="<?php CHBSHelper::getFormName('license_verify'); ?>" class="to-button to-margin-right-0" value="<?php esc_attr_e('Verify purchase','chauffeur-booking-system'); ?>"/>
				</div>
			</li> 
		</ul>
		<script type="text/javascript">
			jQuery(document).ready(function($) 
			{
				$('#<?php CHBSHelper::getFormName('license_verify'); ?>').bind('click',function(e) 
				{
					e.preventDefault();
				
					var Helper=new CHBSHelper();
				
					var purchaseCode=$('#<?php CHBSHelper::getFormName('license_purchase_code'); ?>').val();
					
					if(Helper.isEmpty(purchaseCode))
					{
						alert('<?php esc_html_e('Please enter your purchase code.','chauffeur-booking-system') ?>');
						$('#<?php CHBSHelper::getFormName('license_purchase_code'); ?>').focus();
					}
					else
					{
						window.location.href='<?php echo CHBSLicense::generateVerifyURLAddress(); ?>&purchase_code='+purchaseCode;
					}
				});
			});
		</script>
