<?php 
		echo $this->data['nonce']; 
?>	
		<div class="to">
			<div class="ui-tabs">
				<ul>
					<li><a href="#meta-box-driver-1"><?php esc_html_e('General','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-driver-2"><?php esc_html_e('Contact details','chauffeur-booking-system'); ?></a></li>
				</ul>
				<div id="meta-box-driver-1">
					<ul class="to-form-field-list">
						<?php echo CHBSHelper::createPostIdField(__('Driver ID','chauffeur-booking-system')); ?>
						<li>
							<h5><?php esc_html_e('Name','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('First and second name of the driver.','chauffeur-booking-system'); ?></span>
							<div>
								<span class="to-legend-field"><?php esc_html_e('First name.','chauffeur-booking-system'); ?></span>
								<input type="text" name="<?php CHBSHelper::getFormName('first_name'); ?>" id="<?php CHBSHelper::getFormName('first_name'); ?>" value="<?php echo esc_attr($this->data['meta']['first_name']); ?>"/>
							</div>
							<div>
								<span class="to-legend-field"><?php esc_html_e('Second name.','chauffeur-booking-system'); ?></span>
								<input type="text" name="<?php CHBSHelper::getFormName('second_name'); ?>" id="<?php CHBSHelper::getFormName('second_name'); ?>" value="<?php echo esc_attr($this->data['meta']['second_name']); ?>"/>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Position','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Position.','chauffeur-booking-system'); ?></span>
							<div>
								<input type="text" name="<?php CHBSHelper::getFormName('position'); ?>" id="<?php CHBSHelper::getFormName('position'); ?>" value="<?php echo esc_attr($this->data['meta']['position']); ?>"/>
							</div>
						</li>  
						<li>
							<h5><?php esc_html_e('Notifications','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Send notifications about assign/unassign driver to the booking via selected channels.','chauffeur-booking-system'); ?></span>
							<div class="to-checkbox-button">
								<input type="checkbox" value="-1" id="<?php CHBSHelper::getFormName('notification_type_1'); ?>" name="<?php CHBSHelper::getFormName('notification_type[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['notification_type'],-1); ?>/>
								<label for="<?php CHBSHelper::getFormName('notification_type_1'); ?>"><?php esc_html_e('- None -','chauffeur-booking-system'); ?></label>
								<input type="checkbox" value="1" id="<?php CHBSHelper::getFormName('notification_type_2'); ?>" name="<?php CHBSHelper::getFormName('notification_type[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['notification_type'],1); ?>/>
								<label for="<?php CHBSHelper::getFormName('notification_type_2'); ?>"><?php esc_html_e('E-mail','chauffeur-booking-system'); ?></label>
								<input type="checkbox" value="2" id="<?php CHBSHelper::getFormName('notification_type_3'); ?>" name="<?php CHBSHelper::getFormName('notification_type[]'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['notification_type'],2); ?>/>
								<label for="<?php CHBSHelper::getFormName('notification_type_3'); ?>"><?php esc_html_e('Telegram','chauffeur-booking-system'); ?></label>
							</div>
						</li>				   
					</ul>
				</div>
				<div id="meta-box-driver-2">
					<ul class="to-form-field-list">
						<li>
							<h5><?php esc_html_e('Phone number','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Phone number.','chauffeur-booking-system'); ?></span>
							<div>
								<input type="text" name="<?php CHBSHelper::getFormName('contact_phone_number'); ?>" id="<?php CHBSHelper::getFormName('contact_phone_number'); ?>" value="<?php echo esc_attr($this->data['meta']['contact_phone_number']); ?>"/>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('E-mail address','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('E-mail address.','chauffeur-booking-system'); ?></span>
							<div>
								<input type="text" name="<?php CHBSHelper::getFormName('contact_email_address'); ?>" id="<?php CHBSHelper::getFormName('contact_email_address'); ?>" value="<?php echo esc_attr($this->data['meta']['contact_email_address']); ?>"/>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Telegram','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php _e('Enter <a href="https://telegram.org/" target="_blank">Telegram Messenger</a> details.','chauffeur-booking-system'); ?>
							</span> 
							<div>
								<span class="to-legend-field"><?php esc_html_e('Token.','chauffeur-booking-system'); ?></span>
								<input type="text" name="<?php CHBSHelper::getFormName('contact_telegram_token'); ?>" value="<?php echo esc_attr($this->data['meta']['contact_telegram_token']); ?>"/>
							</div>
							<div>
								<span class="to-legend-field"><?php esc_html_e('Group ID.','chauffeur-booking-system'); ?></span>
								<input type="text" name="<?php CHBSHelper::getFormName('contact_telegram_group_id'); ?>" value="<?php echo esc_attr($this->data['meta']['contact_telegram_group_id']); ?>"/>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Social profiles','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Define URL addresses of available social profiles.','chauffeur-booking-system'); ?></span>
							<div>
								<table class="to-table" id="to-table-social-profile">
									<tr>
										<th style="width:40%">
											<div>
												<?php esc_html_e('Social profile name','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Social profile name.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:40%">
											<div>
												<?php esc_html_e('URL address','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Full URL address of profile.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:20%">
											<div>
												<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Remove this entry.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>											
									</tr>
									<tr class="to-hidden">
										<td>
											<div class="to-clear-fix">
												<select name="<?php CHBSHelper::getFormName('social_profile[profile][]'); ?>" class="to-dropkick-disable">
<?php
		foreach($this->data['dictionary']['social_profile'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'">'.esc_html($value[0]).'</option>';
?>
												</select>												   
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<input type="text" class="to-datepicker-custom" name="<?php CHBSHelper::getFormName('social_profile[url_address][]'); ?>"/>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
											</div>
										</td>
									</tr>						 
<?php
		if(isset($this->data['meta']['social_profile']))
		{
			if(is_array($this->data['meta']['social_profile']))
			{
				foreach($this->data['meta']['social_profile'] as $index=>$value)
				{
?>
									<tr>
										<td>
											<div class="to-clear-fix">
												<select id="<?php CHBSHelper::getFormName('social_profile_id_'.CHBSHelper::createId()); ?>" name="<?php CHBSHelper::getFormName('social_profile[profile][]'); ?>">
<?php
					foreach($this->data['dictionary']['social_profile'] as $socialProfileIndex=>$socialProfileValue)
						echo '<option value="'.esc_attr($socialProfileIndex).'" '.(CHBSHelper::selectedIf($value['profile'],$socialProfileIndex,false)).'>'.esc_html($socialProfileValue[0]).'</option>';
?>
												</select>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<input type="text" name="<?php CHBSHelper::getFormName('social_profile[url_address][]'); ?>" value="<?php echo esc_attr($value['url_address']); ?>"/>
											</div>									
										</td>
										<td>
											<div class="to-clear-fix">
												<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
											</div>
										</td>
									</tr>	 
<?php				  
				}
			}
		}
?>
								</table>
								<div> 
									<a href="#" class="to-table-button-add"><?php esc_html_e('Add','chauffeur-booking-system'); ?></a>
								</div>
							</div>
						</li>			
					</ul>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{	
				$('.to').themeOptionElement({init:true});
				
				$('#to-table-social-profile').table();
				
				$('input[name="<?php CHBSHelper::getFormName('notification_type'); ?>[]"]').on('change',function()
				{
					var checkbox=$(this).parents('li:first').find('input');
					
					var value=parseInt($(this).val());
					if(value===-1)
					{
						checkbox.prop('checked',false);
						checkbox.first().prop('checked',true);
					}
					else checkbox.first().prop('checked',false);
					
					var checked=[];
					checkbox.each(function()
					{
						if($(this).is(':checked'))
							checked.push(parseInt($(this).val(),10));
					});
					
					if(checked.length===0)
					{
						checkbox.prop('checked',false);
						checkbox.first().prop('checked',true);
					}
					
					checkbox.button('refresh');
				});
			});
		</script>