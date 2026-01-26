<?php 
		global $post;
		echo $this->data['nonce']; 
?>	
		<div class="to">
			<div class="ui-tabs">
				<ul>
					<li><a href="#meta-box-email-account-1"><?php esc_html_e('Sender','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-email-account-2"><?php esc_html_e('SMTP authentication','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-email-account-3"><?php esc_html_e('E-mail testing','chauffeur-booking-system'); ?></a></li>
				</ul>
				<div id="meta-box-email-account-1">
					<ul class="to-form-field-list">
						<?php echo CHBSHelper::createPostIdField(__('E-mail account ID','chauffeur-booking-system')); ?>
						<li>
							<h5><?php esc_html_e('Sender name','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Sender name.','chauffeur-booking-system'); ?></span>
							<div>
								<input type="text" name="<?php CHBSHelper::getFormName('sender_name'); ?>" id="<?php CHBSHelper::getFormName('sender_name'); ?>" value="<?php echo esc_attr($this->data['meta']['sender_name']); ?>"/>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Sender e-mail address','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Sender e-mail address.','chauffeur-booking-system'); ?></span>
							<div>
								<input type="text" name="<?php CHBSHelper::getFormName('sender_email_address'); ?>" id="<?php CHBSHelper::getFormName('sender_email_address'); ?>" value="<?php echo esc_attr($this->data['meta']['sender_email_address']); ?>"/>
							</div>
						</li>
					</ul>
				</div>
				<div id="meta-box-email-account-2">
					<div class="to-notice-small to-notice-small-error">
						<?php esc_html_e('Note that entering the username, password, host name, and port number is required if the "SMTP authentication" option is enabled.','chauffeur-booking-system'); ?>
					</div>
					<ul class="to-form-field-list">
						<li>
							<h5><?php esc_html_e('SMTP authentication','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Enable or disable SMTP authentication.','chauffeur-booking-system'); ?></span>
							<div class="to-radio-button">
								<input type="radio" value="1" id="<?php CHBSHelper::getFormName('smtp_auth_enable_1'); ?>" name="<?php CHBSHelper::getFormName('smtp_auth_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['smtp_auth_enable'],1); ?>/>
								<label for="<?php CHBSHelper::getFormName('smtp_auth_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>
								<input type="radio" value="0" id="<?php CHBSHelper::getFormName('smtp_auth_enable_0'); ?>" name="<?php CHBSHelper::getFormName('smtp_auth_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['smtp_auth_enable'],0); ?>/>
								<label for="<?php CHBSHelper::getFormName('smtp_auth_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>							
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Username','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Username.','chauffeur-booking-system'); ?></span>
							<div>
								<input type="text" name="<?php CHBSHelper::getFormName('smtp_auth_username'); ?>" id="<?php CHBSHelper::getFormName('smtp_auth_username'); ?>" value="<?php echo esc_attr($this->data['meta']['smtp_auth_username']); ?>"/>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Password','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Password.','chauffeur-booking-system'); ?></span>
							<div>
								<input type="password" name="<?php CHBSHelper::getFormName('smtp_auth_password'); ?>" id="<?php CHBSHelper::getFormName('smtp_auth_password'); ?>" value="<?php echo esc_attr($this->data['meta']['smtp_auth_password']); ?>"/>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Host','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Host.','chauffeur-booking-system'); ?></span>
							<div>
								<input type="text" name="<?php CHBSHelper::getFormName('smtp_auth_host'); ?>" id="<?php CHBSHelper::getFormName('smtp_auth_host'); ?>" value="<?php echo esc_attr($this->data['meta']['smtp_auth_host']); ?>"/>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Port number','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Port number.','chauffeur-booking-system'); ?></span>
							<div>
								<input type="text" name="<?php CHBSHelper::getFormName('smtp_auth_port'); ?>" id="<?php CHBSHelper::getFormName('smtp_auth_port'); ?>" value="<?php echo esc_attr($this->data['meta']['smtp_auth_port']); ?>"/>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Secure connection type','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Secure connection type.','chauffeur-booking-system'); ?></span>
							<div class="to-radio-button">
<?php
		foreach($this->data['dictionary']['secure_connection_type'] as $secureConnectionTypeIndex=>$secureConnectionTypeData)
		{
?>
								<input type="radio" value="<?php echo esc_attr($secureConnectionTypeIndex); ?>" id="<?php CHBSHelper::getFormName('smtp_auth_secure_connection_type_'.$secureConnectionTypeIndex); ?>" name="<?php CHBSHelper::getFormName('smtp_auth_secure_connection_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['smtp_auth_secure_connection_type'],$secureConnectionTypeIndex); ?>/>
								<label for="<?php CHBSHelper::getFormName('smtp_auth_secure_connection_type_'.$secureConnectionTypeIndex); ?>"><?php echo esc_html($secureConnectionTypeData[0]); ?></label>							
<?php		
		}
?>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Debug','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Enable or disable debugging.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('You can check result of debugging in Chrome/Firebug console (after submit form).','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('Please note that this option should be used for debugging only. Once it will be enabled, proper processing booking in last step could not be possible.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-radio-button">
								<input type="radio" value="1" id="<?php CHBSHelper::getFormName('smtp_auth_debug_enable_1'); ?>" name="<?php CHBSHelper::getFormName('smtp_auth_debug_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['smtp_auth_debug_enable'],1); ?>/>
								<label for="<?php CHBSHelper::getFormName('smtp_auth_debug_enable_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system'); ?></label>							
								<input type="radio" value="0" id="<?php CHBSHelper::getFormName('smtp_auth_debug_enable_0'); ?>" name="<?php CHBSHelper::getFormName('smtp_auth_debug_enable'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['smtp_auth_debug_enable'],0); ?>/>
								<label for="<?php CHBSHelper::getFormName('smtp_auth_debug_enable_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system'); ?></label>							
							</div>
						</li>
					</ul>
				</div>
				<div id="meta-box-email-account-3">
					<div class="to-notice-small to-notice-small-error">
						<?php esc_html_e('Enter receiver e-mail address and click on below button to send testing e-mail message using details of this account.') ?>
					</div>
					<ul class="to-form-field-list">
						<li>
							<h5><?php esc_html_e('Receiver e-mail address','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Receiver e-mail address.','chauffeur-booking-system'); ?></span>
							<div>
								<input type="text" name="<?php CHBSHelper::getFormName('test_email_receiver_email_address'); ?>" id="<?php CHBSHelper::getFormName('test_email_receiver_email_address'); ?>" value=""/>
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Response','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Response from the server.','chauffeur-booking-system'); ?></span>						
							<div class="to-clear-fix">
								<pre class="to-preformatted-text" id="to_email_response"><?php esc_html_e('No reply from the server.','chauffeur-booking-system'); ?></pre>
								<input type="submit" value="<?php esc_attr_e('Send a message','chauffeur-booking-system'); ?>" name="<?php CHBSHelper::getFormName('test_email_send'); ?>" class="to-button to-margin-right-0"/>
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
				
				/***/
				
				$('input[name="<?php CHBSHelper::getFormName('test_email_send'); ?>"]').on('click',function(e)
				{
					e.preventDefault();
					
					var data={};
					
					var helper=new CHBSHelper();
					
					data.action='<?php echo PLUGIN_CHBS_CONTEXT.'_test_email_send'; ?>';
					data.email_account_id=<?php echo $post->ID; ?>;
					data.receiver_email_address=$('input[name="<?php CHBSHelper::getFormName('test_email_receiver_email_address'); ?>"]').val();

					if(helper.isEmpty(data.receiver_email_address))
					{
						alert('<?php esc_html_e('Please enter receiver e-mail address.','chauffeur-booking-system'); ?>');
						$('input[name="<?php CHBSHelper::getFormName('test_email_receiver_email_address'); ?>"]').focus();
					}
					else
					{
						$('.to').block({message:false,overlayCSS:{opacity:'0.3'}});
						
						$('#to_email_response').html('<?php esc_html_e('No reply from the server.','chauffeur-booking-system'); ?>');
						
						$.post(ajaxurl,data,function(response) 
						{		
							$('.to').unblock({onUnblock:function()
							{ 
								if(parseInt(response.error,10)===1)
								{
									alert(response.error_message);
								}
								else
								{
									if(new String(response.email_response)!=='undefined')
										$('#to_email_response').html(response.email_response);
								}
							}});

						},'json');
					}
				});
			});
		</script>