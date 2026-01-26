<?php 
		echo $this->data['nonce']; 
?>	
		<div class="to">
			<div class="ui-tabs">
				<ul>
					<li><a href="#meta-box-geofence-1"><?php esc_html_e('General','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-geofence-2"><?php esc_html_e('Tax rate','chauffeur-booking-system'); ?></a></li>
					<li><a href="#meta-box-geofence-3"><?php esc_html_e('Import coordinates','chauffeur-booking-system'); ?></a></li>
				</ul>
				<div id="meta-box-geofence-1">
					<ul class="to-form-field-list">
						<?php echo CHBSHelper::createPostIdField(__('Geofence ID','chauffeur-booking-system')); ?>
						<li>
							<h5><?php esc_html_e('Geofence','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Draw an area using the tools located on or below the map.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('You can also type a location in the search field to easily find the area.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-clear-fix">
								<input type="text" class="to-width-full" name="<?php CHBSHelper::getFormName('google_map_autocomplete'); ?>" id="<?php CHBSHelper::getFormName('google_map_autocomplete'); ?>" value="" title="<?php esc_attr_e('Enter location.','chauffeur-booking-system'); ?>"/>
							</div>
							<div class="to-clear-fix">
								<div id="to-google-map"></div>
							</div>
							<div class="to-clear-fix to-float-right">
								<?php esc_html_e('Options:','chauffeur-booking-system'); ?>
								<a href="#" id="<?php CHBSHelper::getFormName('shape_remove'); ?>"><?php esc_html_e('Remove selected shape','chauffeur-booking-system'); ?></a>
							</div>
						</li>
					</ul>
				</div>
				<div id="meta-box-geofence-2">
					<ul class="to-form-field-list">
						<li>
							<h5><?php esc_html_e('Tax rate','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Select the tax rate to be applied to this area.','chauffeur-booking-system'); ?><br/>
								<?php esc_html_e('This feature requires additional setup when editing the booking form.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-clear-fix">
								<select name="<?php CHBSHelper::getFormName('tax_rate_id'); ?>" id="<?php CHBSHelper::getFormName('tax_rate_id'); ?>">
<?php
		echo '<option value="-1" '.(CHBSHelper::selectedIf($this->data['meta']['tax_rate_id'],-1,false)).'>'.esc_html__('- None -','chauffeur-booking-system').'</option>';
		foreach($this->data['dictionary']['tax_rate'] as $index=>$value)
			echo '<option value="'.esc_attr($index).'" '.(CHBSHelper::selectedIf($this->data['meta']['tax_rate_id'],$index,false)).'>'.esc_html($value['post']->post_title).'</option>';
?>
								</select>	
							</div>								
						</li>	
					</ul>
				</div>
				<div id="meta-box-geofence-3">
					<ul class="to-form-field-list">
						<li>
							<h5><?php esc_html_e('Import coordinates','chauffeur-booking-system'); ?></h5>
							<span class="to-legend">
								<?php esc_html_e('Import coordinates by entering list of them (longitude, latitude) separated by comma.','chauffeur-booking-system'); ?>
							</span>
							<div class="to-clear-fix">
								<textarea rows="1" cols="1" name="<?php CHBSHelper::getFormName('import_coordinate'); ?>"></textarea>
							</div>								
						</li>	
					</ul>
				</div>
			</div>
			<input type="hidden" value="<?php echo esc_attr(json_encode($this->data['meta']['shape_coordinate'])); ?>" name="<?php CHBSHelper::getFormName('shape_coordinate'); ?>" id="<?php CHBSHelper::getFormName('shape_coordinate'); ?>"/>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{	
				var element=$('.to').themeOptionElement({init:true});
				element.bindBrowseMedia('.to-button-browse');
				
				var geofence=$().chauffeurGeofenceAdmin(
				{
					coordinate:<?php echo json_encode($this->data['coordinate']); ?>
				});
				geofence.init();
			});
		</script>