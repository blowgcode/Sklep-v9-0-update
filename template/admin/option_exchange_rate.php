
		<ul class="to-form-field-list">
			<li>
				<h5><?php esc_html_e('Update','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Select exchange rate provider.','chauffeur-booking-system'); ?>
				</span>
				<div class="to-clear-fix">
					<select name="<?php CHBSHelper::getFormName('exchange_rate_provider'); ?>" id="<?php CHBSHelper::getFormName('exchange_rate_provider'); ?>">
<?php
						foreach($this->data['dictionary']['exchange_rate_provider'] as $index=>$value)
							echo '<option value="'.esc_attr($index).'">'.$value[0].'</option>';
?>
					</select>
				</div>
			</li> 
			<li>
				<input type="button" name="<?php CHBSHelper::getFormName('import_exchange_rate'); ?>" id="<?php CHBSHelper::getFormName('import_exchange_rate'); ?>" class="to-button to-margin-0" value="<?php esc_attr_e('Import exchange rates','chauffeur-booking-system'); ?>"/>
			</li>			
			<li>
				<h5><?php esc_html_e('Rates table','chauffeur-booking-system'); ?></h5>
				<span class="to-legend">
					<?php esc_html_e('Enter an exchange rate for selected currencies in relation to base currency.','chauffeur-booking-system'); ?>
				</span>
				<div>
					<table class="to-table">
						<tr>
							<th style="width:50%">
								<div>
									<?php esc_html_e('Currency','chauffeur-booking-system'); ?>
									<span class="to-legend">
										<?php esc_html_e('Currency.','chauffeur-booking-system'); ?>
									</span>
								</div>
							</th>
							<th style="width:50%">
								<div>
									<?php esc_html_e('Exchange rate','chauffeur-booking-system'); ?>
									<span class="to-legend">
										<?php esc_html_e('Exchange rate.','chauffeur-booking-system'); ?>
									</span>
								</div>
							</th>
						</tr> 
<?php
		foreach($this->data['dictionary']['currency'] as $index=>$value)
		{
?>
						<tr>
							<td>
								<div>
									<?php echo esc_html__($value['name']).' <b>('.esc_html($index).')</b>'; ?>
								</div>
							</td>
							<td>
								<div>
									<input type="text" value="<?php echo esc_attr(array_key_exists($index,(array)$this->data['option']['currency_exchange_rate']) ? $this->data['option']['currency_exchange_rate'][$index] : ''); ?>" name="<?php CHBSHelper::getFormName('currency_exchange_rate['.$index.']'); ?>"/>
								</div>
							</td>
						</tr>
<?php		  
		}
?>
					</table>
				</div>
			</li> 
		</ul>
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{	
				$('#<?php CHBSHelper::getFormName('import_exchange_rate'); ?>').bind('click',function(e) 
				{
					e.preventDefault();
					$('#action').val('<?php echo PLUGIN_CHBS_CONTEXT.'_option_page_import_exchange_rate'; ?>');
					$('#to_form').submit();
					$('#action').val('<?php echo PLUGIN_CHBS_CONTEXT.'_option_page_save'; ?>');
				});
			});
		</script>