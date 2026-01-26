<?php 
		echo $this->data['nonce']; 
?>	
		<div class="to">
			<div class="ui-tabs">
				<ul>
					<li><a href="#meta-box-currency-1"><?php esc_html_e('General','chauffeur-booking-system'); ?></a></li>
				</ul>
				<div id="meta-box-currency-1">
					<div class="to-notice-small to-notice-small-error">
						<?php esc_html_e('Note that if the currency is already defined, it will not be saved again.') ?>
					</div>
					<ul class="to-form-field-list">
						<?php echo CHBSHelper::createPostIdField(__('Currency ID','chauffeur-booking-system')); ?>
						<li>
							<h5><?php esc_html_e('Currency','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Select currency. If the currency already exists, changes will not be saved.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-clear-fix">
								<select name="<?php CHBSHelper::getFormName('currency_code'); ?>" id="<?php CHBSHelper::getFormName('currency_code'); ?>">
									<option value="-1"><?php esc_html_e('- None -','chauffeur-booking-system'); ?></option>

<?php
		foreach($this->data['dictionary']['currency'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['currency_code'],$index,false)).'>'.esc_html($value['name'].' ('.$index.')').'</option>';
?>
								</select>												  
							</div>
						</li>
						<li>
							<h5><?php esc_html_e('Symbol','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Symbol of the currency e.g "$".','chauffeur-booking-system'); ?>
							</span>
							<div><input type="text" name="<?php CHBSHelper::getFormName('symbol'); ?>" value="<?php echo esc_attr($this->data['meta']['symbol']); ?>"/></div>								  
						</li>
						<li>
							<h5><?php esc_html_e('Symbol position','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Position of the symbol.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-radio-button">
<?php
		foreach($this->data['dictionary']['symbol_position'] as $index=>$value)
		{
?>
								<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('symbol_position_'.$index); ?>" name="<?php CHBSHelper::getFormName('symbol_position'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['symbol_position'],$index); ?>/>
								<label for="<?php CHBSHelper::getFormName('symbol_position_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>
<?php		
		}
?>
							</div>
						</li>						
						<li>
							<h5><?php esc_html_e('Decimal separator','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Decimal separator, e.g ".".','chauffeur-booking-system'); ?>
							</span>
							<div><input type="text" name="<?php CHBSHelper::getFormName('decimal_separator'); ?>" value="<?php echo esc_attr($this->data['meta']['decimal_separator']); ?>"/></div>								  
						</li>
						<li>
							<h5><?php esc_html_e('Decimal digits','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Number of decimal digits, e.g "2".','chauffeur-booking-system'); ?>
							</span>
							<div><input type="text" maxlength="1" name="<?php CHBSHelper::getFormName('decimal_digit_number'); ?>" value="<?php echo esc_attr($this->data['meta']['decimal_digit_number']); ?>"/></div>								  
						</li>						
						<li>
							<h5><?php esc_html_e('Thousands separator','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Thousands separator, e.g .','chauffeur-booking-system'); ?>
							</span>
							<div><input type="text" name="<?php CHBSHelper::getFormName('thousand_separator'); ?>" value="<?php echo esc_attr($this->data['meta']['thousand_separator']); ?>"/></div>								  
						</li>								
					</ul>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{	
				$('.to').themeOptionElement({init:true});
			});
		</script>