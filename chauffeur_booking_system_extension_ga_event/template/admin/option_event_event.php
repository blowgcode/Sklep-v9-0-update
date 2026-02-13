<?php
		if(count($this->data['dictionary']['event_group']))
		{
			$Validation=new CHBSValidation();
			$ExtensionHelper=new CHBSExtensionHelper(PLUGIN_CHBSEGAE_PREFIX);
?>
		<ul class="to-form-field-list">
<?php
			foreach($this->data['dictionary']['event_group'] as $eventGroupId=>$eventGroupData)
			{
?>
			<li>
				<h5><?php echo esc_html__($eventGroupData['label']); ?></h5>
				<span class="to-legend">
					<?php echo (array_key_exists('description',$eventGroupData) ? esc_html__($eventGroupData['description']) : esc_html__($eventGroupData['label'])); ?>.
				</span>
<?php
				foreach($this->data['dictionary']['event'] as $eventId=>$eventData)
				{
					if((int)$eventData['group_id']!==(int)$eventGroupId) continue;
					
					$id='event_'.$eventId;
					$name='event['.$eventId.']';
					
					$value=0;
					
					if(array_key_exists($eventId,$this->data['option']['event']))
					{
						$value=$this->data['option']['event'][$eventId];
					}
?>
				<div class="to-clear-fix">
					<span class="to-legend-field">
						<?php echo (array_key_exists('description',$eventData) ? esc_html__($eventData['description']) : esc_html__($eventData['label'])); ?>.
					</span>
					<div class="to-radio-button">
						<input type="radio" value="1" id="<?php $ExtensionHelper->getFormName($id.'_1'); ?>" name="<?php $ExtensionHelper->getFormName($name); ?>" <?php CHBSHelper::checkedIf($value,1); ?>/>
						<label for="<?php $ExtensionHelper->getFormName($id.'_1'); ?>"><?php esc_html_e('Enable','chauffeur-booking-system-extension-ga-event'); ?></label>
						<input type="radio" value="0" id="<?php $ExtensionHelper->getFormName($id.'_0'); ?>" name="<?php $ExtensionHelper->getFormName($name); ?>" <?php CHBSHelper::checkedIf($value,0); ?>/>
						<label for="<?php $ExtensionHelper->getFormName($id.'_0'); ?>"><?php esc_html_e('Disable','chauffeur-booking-system-extension-ga-event'); ?></label>
					</div>
				</div>
<?php					
				}
?>
			</li>
<?php			
			}
		}