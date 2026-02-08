		
		<div class="chbs-layout-25x75 chbs-clear-fix">

			<div class="chbs-layout-column-left"></div>

			<div class="chbs-layout-column-right">
<?php
		if(count($this->data['meta']['vehicle_filter_enable']))
		{
			$columnName=(int)(100/count($this->data['meta']['vehicle_filter_enable']));
?>
				<div class="chbs-vehicle-filter chbs-box-shadow chbs-clear-fix">
					
					<label class="chbs-form-label-group"><?php esc_html_e('Vehicles filter','chauffeur-booking-system'); ?></label>
<?php
			if(in_array(1,$this->data['meta']['vehicle_filter_enable']))
			{
?>
					<div class="chbs-form-field chbs-form-field-width-<?php echo $columnName; ?>">
						<label class="chbs-form-field-label">
							<?php esc_html_e('Passengers','chauffeur-booking-system'); ?>
							<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Number of Passengers.','chauffeur-booking-system'); ?>"></span>
						</label>
						<select name="<?php CHBSHelper::getFormName('vehicle_passenger_count'); ?>">
<?php
				for($i=$this->data['vehicle_passenger_count_range']['min'];$i<=$this->data['vehicle_passenger_count_range']['max'];$i++)
				{
?>
							<option value="<?php echo esc_attr($i); ?>"<?php echo ($i==1 ? ' selected="selected"' : ''); ?>><?php echo esc_html($i); ?></option>
<?php
				}
?>
						</select>
					</div>
<?php
			}
			if(in_array(2,$this->data['meta']['vehicle_filter_enable']))
			{
?>
					<div class="chbs-form-field chbs-form-field-width-<?php echo $columnName; ?>">
						<label>
							<?php esc_html_e('Suitcases','chauffeur-booking-system'); ?>
							<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Number of Suitcases.','chauffeur-booking-system'); ?>"></span>
						</label>
						<select name="<?php CHBSHelper::getFormName('vehicle_bag_count'); ?>">
<?php
				for($i=$this->data['vehicle_bag_count_range']['min'];$i<=$this->data['vehicle_bag_count_range']['max'];$i++)
				{
?>
							<option value="<?php echo esc_attr($i); ?>"<?php echo ($i==1 ? ' selected="selected"' : ''); ?>><?php echo esc_html($i); ?></option>
<?php
				}
?>
						</select>
					</div>
<?php
			}
			if(in_array(3,$this->data['meta']['vehicle_filter_enable']))
			{
?>
					<div class="chbs-form-field chbs-form-field-width-<?php echo $columnName; ?>">
						<label>
							<?php esc_html_e('Standard','chauffeur-booking-system'); ?>
							<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Standard.','chauffeur-booking-system'); ?>"></span>
						</label>
						<select name="<?php CHBSHelper::getFormName('vehicle_standard'); ?>">
<?php
				for($i=1;$i<=4;$i++)
				{
?>
							<option value="<?php echo esc_attr($i); ?>"<?php echo ($i==1 ? ' selected="selected"' : ''); ?>><?php echo esc_html($i); ?></option>
<?php
				}
?>
						</select>
					</div>
<?php
			}			
			if(in_array(4,$this->data['meta']['vehicle_filter_enable']))
			{
?>
					<div class="chbs-form-field chbs-form-field-width-<?php echo $columnName; ?>">
						<label>
							<?php esc_html_e('Type','chauffeur-booking-system'); ?>
							<span class="chbs-tooltip chbs-meta-icon-question" title="<?php esc_html_e('Vehicle Type.','chauffeur-booking-system'); ?>"></span>
						</label>
						<select name="<?php CHBSHelper::getFormName('vehicle_category'); ?>">
							<option value="0"><?php esc_html_e('- All vehicles -','chauffeur-booking-system') ?></option>
<?php
				foreach($this->data['dictionary']['vehicle_category'] as $index=>$value)
				{
?>
							<option value="<?php echo esc_attr($index); ?>"><?php echo esc_html($value['name']); ?></option>
<?php
				}
?>
						</select>						
					</div>
<?php
			}
?>
				</div>
<?php		  
		}
?>
				<div class="chbs-notice chbs-hidden"></div>
				
				<div class="chbs-vehicle-list"></div>
			
				<div class="chbs-booking-extra"></div>
				
			</div>

		</div>

		<div class="chbs-clear-fix chbs-main-content-navigation-button">
			<a href="#" class="chbs-button chbs-button-style-2 chbs-button-step-prev">
				<span class="chbs-meta-icon-arrow-horizontal-large"></span>
				<?php echo esc_html($this->data['step']['dictionary'][2]['button']['prev']); ?>
			</a> 
			<a href="#" class="chbs-button chbs-button-style-1 chbs-button-step-next">
				<?php echo esc_html($this->data['step']['dictionary'][2]['button']['next']); ?>
				<span class="chbs-meta-icon-arrow-horizontal-large"></span>
			</a> 
		</div>