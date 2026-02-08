		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Import demo','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
<?php
		$import=false;
		
		$License=new CHBSLicense();
		
		if((!CHBSPlugin::isAutoRideTheme()) && ($License->isVerified()))
		{
			$import=true;
		}
		
		if(CHBSPlugin::isAutoRideTheme())
		{
			esc_html_e('Import demo for the plugin is not available, because your are using "AutoRide" theme.','chauffeur-booking-system'); 
			echo '<br>';
			esc_html_e('You should use "Theme Demo Data Installer" to install entire dummy content.','chauffeur-booking-system'); 
		}
		else
		{
			if($License->isVerified())
			{
				esc_html_e('To import the demo content, click the button below.','chauffeur-booking-system'); 
				echo '<br>';
				esc_html_e('This function should be run only once — running it again will create duplicate content.','chauffeur-booking-system');
				echo '<br>';
				esc_html_e('The process may take a few minutes and cannot be undone.','chauffeur-booking-system'); 
			}
			else 
			{
				esc_html_e('To import the demo content, you must first verify your license in the "General / License" tab.','chauffeur-booking-system'); 				
			}
		}
?>
				</span>
<?php
		if($import)
		{
?>
				<input type="button" name="<?php CHBSHelper::getFormName('import_dummy_content'); ?>" id="<?php CHBSHelper::getFormName('import_dummy_content'); ?>" class="to-button to-margin-0" value="<?php esc_attr_e('Import','chauffeur-booking-system'); ?>"/>
<?php
		}
?>
			</li>
		</ul>
		<script type="text/javascript">
			jQuery(document).ready(function($) 
			{
				$('#<?php CHBSHelper::getFormName('import_dummy_content'); ?>').bind('click',function(e) 
				{
					e.preventDefault();
					if(confirm('<?php esc_html_e('Do you really want to import demo data?','chauffeur-booking-system'); ?>'))
					{
						$('#action').val('<?php echo PLUGIN_CHBS_CONTEXT.'_option_page_import_demo'; ?>');
						$('#to_form').submit();
						$('#action').val('<?php echo PLUGIN_CHBS_CONTEXT.'_option_page_save'; ?>');
					}
				});
			});
		</script>