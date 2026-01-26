<?php 
		echo $this->data['nonce']; 
?>	
		<div class="to">
			<div class="ui-tabs">
				<ul>
					<li><a href="#meta-box-vehicle-attribute-1"><?php esc_html_e('General','chauffeur-booking-system'); ?></a></li>
				</ul>
				<div id="meta-box-vehicle-attribute-1">
					<ul class="to-form-field-list">
						<?php echo CHBSHelper::createPostIdField(__('Vehicle attribute ID','chauffeur-booking-system')); ?>
						<li>
							<h5><?php esc_html_e('Type','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Select type of the attribute (it cannot be changed after saving).','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('"Text Value" is used for any custom text. For example, you can define an attribute named "color" and then specify it by entering it in a single vehicle settings.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('"Single Choice" is used for one value. For example, you can define an attribute named "transmission" with two values "automatic" and "manual" and then select one of them in a single vehicle settings.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('"Multi Choice" is used for multiple values. For example, you can define an attribute named "extras" with two values "bluetooth" and "radio" and then select all of them in a single vehicle settings.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-radio-button">
<?php
		foreach($this->data['dictionary']['attribute_type'] as $index=>$value)
		{
?>
								<input type="radio" value="<?php echo esc_attr($index); ?>" id="<?php CHBSHelper::getFormName('attribute_type_'.$index); ?>" name="<?php CHBSHelper::getFormName('attribute_type'); ?>" <?php CHBSHelper::checkedIf($this->data['meta']['attribute_type'],$index); ?>/>
								<label for="<?php CHBSHelper::getFormName('attribute_type_'.$index); ?>"><?php echo esc_html($value[0]); ?></label>							
<?php		
		}
?>
							</div>	
						</li>
<?php
		$class=array();
		if($this->data['meta']['attribute_type']==1)
			$class[]='to-hidden';
?>
						<li<?php echo CHBSHelper::createCSSClassAttribute($class); ?>>
							<h5><?php esc_html_e('Values','chauffeur-booking-system'); ?></h5>
							<span class="to-legend"><?php esc_html_e('Specify possible values for this attribute.','chauffeur-booking-system'); ?></span>					
							<div>
								<table class="to-table" id="to-table-vehicle-attribute">
									<tr>
										<th style="width:80%">
											<div>
												<?php esc_html_e('Value','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Value.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>
										<th style="width:20%">
											<div>
												<?php esc_html_e('Remove','chauffeur-booking-system'); ?>
												<span class="to-legend">
													<?php esc_html_e('Remove this line.','chauffeur-booking-system'); ?>
												</span>
											</div>
										</th>										
									</tr>
									<tr class="to-hidden">
										<td>
											<div>
												<input type="text" name="<?php CHBSHelper::getFormName('attribute_value[]'); ?>" title="<?php esc_attr_e('Enter value of attribute.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>
										<td>
											<div>
												<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
											</div>
										</td>
									</tr>
<?php
		if((isset($this->data['meta']['attribute_value'])) && (is_array($this->data['meta']['attribute_value'])))
		{
			foreach($this->data['meta']['attribute_value'] as $value)
			{
?>
									<tr>
										<td>
											<div>
												<input type="text" name="<?php CHBSHelper::getFormName('attribute_value['.(int)$value['id'].']'); ?>" value="<?php echo esc_attr($value['value']); ?>" title="<?php esc_attr_e('Enter value of attribute.','chauffeur-booking-system'); ?>"/>
											</div>									
										</td>
										<td>
											<div>
												<a href="#" class="to-table-button-remove"><?php esc_html_e('Remove','chauffeur-booking-system'); ?></a>
											</div>
										</td>
									</tr>  
<?php
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
					<input type="hidden" value="<?php echo (int)CHBSHelper::isEditMode(); ?>" name="<?php CHBSHelper::getFormName('is_edit_mode'); ?>" id="<?php CHBSHelper::getFormName('is_edit_mode'); ?>"/>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{	
<?php
		if(CHBSHelper::isEditMode())
			echo '$(\'input[name="'.CHBSHelper::getFormName('attribute_type',false).'"]\').attr(\'disabled\',\'disabled\');';
?>				
				$('input[name="<?php CHBSHelper::getFormName('attribute_type'); ?>"]').on('change',function()
				{
					var line=$(this).parents('li:first').nextAll('li');
					if(parseInt($(this).val())===1) line.addClass('to-hidden');
					else line.removeClass('to-hidden')
				});
		
				$('.to').themeOptionElement({init:true});
				$('#to-table-vehicle-attribute').table();
			});
		</script>