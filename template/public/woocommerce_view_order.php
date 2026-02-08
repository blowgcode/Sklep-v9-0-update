<?php
		$Date=new CHBSDate();
		$Length=new CHBSLength();
		$Validation=new CHBSValidation();
		$BookingFormElement=new CHBSBookingFormElement();
?>		
		<div class="chbs-wc-order-view">
			
			<div class="chbs-wc-order-view-section">
				
				<h3><?php esc_html_e('General','chauffeur-booking-system'); ?></h3>

				<div>
				
					<div>
						<div><?php esc_html_e('Status','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html($this->data['booking']['booking_status_name']); ?></div>
					</div>
					<div>
						<div><?php esc_html_e('Service type','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html($this->data['booking']['service_type_name']); ?></div>
					</div>	
					<div>
						<div><?php esc_html_e('Transfer type','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html($this->data['booking']['transfer_type_name']); ?></div>
					</div>
					<div>
						<div><?php esc_html_e('Pickup date and time','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html($Date->formatDateToDisplay($this->data['booking']['meta']['pickup_date']).' '.$Date->formatTimeToDisplay($this->data['booking']['meta']['pickup_time'])); ?></div>
					</div>	
<?php
		if(in_array($this->data['booking']['meta']['service_type_id'],array(1,3)))
		{
			if((int)$this->data['booking']['meta']['transfer_type_id']===3)
			{
?>
					<div>
						<div><?php esc_html_e('Return date and time','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html($Date->formatDateToDisplay($this->data['booking']['meta']['return_date']).' '.$Date->formatTimeToDisplay($this->data['booking']['meta']['return_time'])); ?></div>
					</div>											
<?php
			}
		}
		
		if((int)$this->data['booking']['meta']['price_hide']===0)
		{
		
?>											
					<div>
						<div><?php esc_html_e('Order total amount','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html(CHBSPrice::format($this->data['booking']['billing']['summary']['value_gross'],$this->data['booking']['meta']['currency_id'])); ?></div>
					</div>
<?php
			$taxHtml=null;
			
			foreach($this->data['booking']['billing']['tax_group'] as $value)
			{
				if(!$Validation->isEmpty($taxHtml)) $taxHtml.=', ';
				$taxHtml.=CHBSPrice::format($value['value'],$this->data['booking']['meta']['currency_id']).' ('.$value['tax_value'].'%)';
			}	
			
			if($Validation->isNotEmpty($taxHtml))
			{
?>
					<div>
						<div><?php esc_html_e('Taxes','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html($taxHtml); ?></div>
					</div>
<?php
			}
			if($this->data['booking']['meta']['payment_deposit_enable']==1)
			{
?>											
					<div>
						<div><?php echo sprintf(esc_html('To pay (deposit %s%%)','chauffeur-booking-system'),$this->data['booking']['meta']['payment_deposit_value']); ?></div>
						<div><?php echo esc_html(CHBSPrice::format($this->data['booking']['billing']['summary']['pay'],$this->data['booking']['meta']['currency_id'])); ?></div>
					</div>												
<?php		  
			}
		}
		
		if(in_array($this->data['booking']['meta']['service_type_id'],array(1,3)))
		{
?>
					<div>
						<div><?php esc_html_e('Distance','chauffeur-booking-system'); ?></div>
						<div>
							<?php echo $this->data['booking']['billing']['summary']['distance']; ?>
							<?php echo $Length->getUnitShortName($this->data['booking']['meta']['length_unit']); ?>
						</div>
					</div>
<?php
		}
		else if($Validation->isNotEmpty($this->data['booking']['distance_included_format']))
		{
?>		
					<div>
						<div><?php esc_html_e('Incl. distance','chauffeur-booking-system'); ?></div>
						<div>
							<?php echo esc_html($this->data['booking']['distance_included_format']); ?>
						</div>
					</div>
<?php			
		}
?>
					<div>
						<div><?php esc_html_e('Duration','chauffeur-booking-system'); ?></div>
						<div>
							<?php echo esc_html($this->data['booking']['billing']['summary']['duration']);  ?>
						</div>
					</div>	
<?php
		if($this->data['booking']['meta']['passenger_enable']==1)
		{
?> 
					<div>
						<div><?php esc_html_e('Passengers','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html(CHBSBookingHelper::getPassengerLabel($this->data['booking']['meta']['passenger_adult_number'],$this->data['booking']['meta']['passenger_children_number'],1,$this->data['booking']['meta']['passenger_use_person_label'])); ?></div>
					</div>		
<?php		  
		}
		if($Validation->isNotEmpty($this->data['booking']['meta']['comment']))
		{
?>											
					<div>
						<div><?php esc_html_e('Comment','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html($this->data['booking']['meta']['comment']); ?></div>
					</div>   
<?php
		}
?>
					
				</div>
					
			</div>			
<?php
		/***/

		if(((int)$this->data['booking']['meta']['service_type_id']===3) || (((int)$this->data['booking']['meta']['service_type_id']===3) && ((int)$this->data['booking']['meta']['extra_time_enable']===1)))
		{
?>		   
			<div class="chbs-wc-order-view-section">
				
				<h3><?php esc_html_e('Route','chauffeur-booking-system'); ?></h3>
				
				<div>
<?php
			if((int)$this->data['booking']['meta']['service_type_id']===3)
			{
?>
					<div>
						<div><?php esc_html_e('Route name','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html($this->data['booking']['meta']['route_name']); ?></div>
					</div>
<?php
			}

			if(in_array($this->data['booking']['meta']['service_type_id'],array(1,3)))
			{
				if((int)$this->data['booking']['meta']['extra_time_enable']===1)
				{
?>
					<div>
						<div><?php esc_html_e('Extra time','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html($Date->formatMinuteToTime($this->data['booking']['meta']['extra_time_value'])); ?></div>
					</div>											
<?php
				}
			}
?>
				</div>
					
			</div>
<?php
		}
?>
			<div class="chbs-wc-order-view-section">
				
				<h3><?php esc_html_e('Route locations','chauffeur-booking-system'); ?></h3>
				
				<div>
					<div>
						<ol>
<?php
		foreach($this->data['booking']['meta']['coordinate'] as $index=>$value)
		{
?>
						<li><a href="https://www.google.com/maps/?q=<?php echo esc_attr($value['lat']).','.esc_attr($value['lng']); ?>" target="_blank"><?php echo esc_html(CHBSHelper::getAddress($value)); ?></a></li>
<?php
		}
?>
						</ol>
					</div>
				</div>
				
			</div>
			
			<div class="chbs-wc-order-view-section">
				
				<h3><?php esc_html_e('Vehicle','chauffeur-booking-system'); ?></h3>
				
				<div>
				
					<div>
						<div><?php esc_html_e('Vehicle name','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html($this->data['booking']['meta']['vehicle_name']); ?></div>
					</div>
<?php
		if(array_key_exists('vehicle_bag_count',$this->data['booking']))
		{
?>
					<div>
						<div><?php esc_html_e('Bag count','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html($this->data['booking']['vehicle_bag_count']); ?></div>
					</div>
<?php
		}
		if(array_key_exists('vehicle_passenger_count',$this->data['booking']))
		{		
?>
					<div>
						<div><?php esc_html_e('Passengers count','chauffeur-booking-system'); ?></div>
						<div><?php echo esc_html($this->data['booking']['vehicle_passenger_count']); ?></div>
					</div>
<?php
		}
?>
				</div>
				
			</div>
<?php
		if(count($this->data['booking']['meta']['booking_extra']))
		{
?>		
			<div class="chbs-wc-order-view-section">
				
				<h3><?php esc_html_e('Extra','chauffeur-booking-system'); ?></h3>

				<div>
					<div>
						<ol>
<?php
			foreach($this->data['booking']['meta']['booking_extra'] as $index=>$value)
			{
?>
							<li>
								<?php echo esc_html($value['quantity']); ?>
								<?php esc_html_e('x','chauffeur-booking-system'); ?>
								<?php echo esc_html($value['name']); ?>
<?php
				if((int)$this->data['booking']['meta']['price_hide']===0)
				{
					echo ' - '.CHBSPrice::format(CHBSPrice::calculateGross($value['price'],0,$value['tax_rate_value'])*$value['quantity'],$this->data['booking']['meta']['currency_id']);
				}
?>
							</li> 
<?php
			}
?>
						</ol>
					</div>
				</div>
				
			</div>
<?php
		}
		
		$panel=$BookingFormElement->displayField(1,$this->data['booking']['meta'],3);
		if($Validation->isNotEmpty($panel))
		{
?>
			<div class="chbs-wc-order-view-section">
				
				<h3><?php esc_html_e('Client details','chauffeur-booking-system'); ?></h3>
				<div><?php echo $panel; ?></div>
				
			</div>
<?php
		}

		$panel=$BookingFormElement->displayField(2,$this->data['booking']['meta'],3);
		if(($Validation->isNotEmpty($panel)) && ((int)$this->data['booking']['meta']['client_billing_detail_enable']===1))
		{
?>	
			<div class="chbs-wc-order-view-section">
				
				<h3><?php esc_html_e('Billing address','chauffeur-booking-system'); ?></h3>
				<div><?php echo $panel; ?></div>
				
			</div>
<?php
		}

		$panel=$BookingFormElement->getPanel($this->data['booking']['meta']);
		foreach($panel as $panelIndex=>$panelValue)
		{
			if(in_array($panelValue['id'],array(1,2))) continue;
?>
			<div class="chbs-wc-order-view-section">
				
				<h3><?php echo esc_html($panelValue['label']); ?></h3>
				<div>
					<?php echo $BookingFormElement->displayField($panelValue['id'],$this->data['booking']['meta'],3); ?>   
				</div>
					
			</div>
<?php
		}
		
		if((array_key_exists('form_element_agreement',$this->data['booking']['meta'])) && (is_array($this->data['booking']['meta']['form_element_agreement'])) && (count($this->data['booking']['meta']['form_element_agreement'])))
		{
?>	
			<div class="chbs-wc-order-view-section">
				
				<h3><?php esc_html_e('Agreements','chauffeur-booking-system'); ?></h3>
			
				<div>
					<div>
						<ol>
<?php
			foreach($this->data['booking']['meta']['form_element_agreement'] as $index=>$value)
			{
?>
							<li>
								<?php echo ((int)$value['value']===1 ? __('[YES]','chauffeur-booking-system') : __('[NO]','chauffeur-booking-system')).' '.$value['text']; ?>
							</li> 
<?php
			}
?>
						</ol>
					</div>
				</div>
				
			</div>
<?php		  
		}

		if(!empty($this->data['booking']['meta']['payment_id']))
		{
?>
			<div class="chbs-wc-order-view-section">
				
				<h3><?php esc_html_e('Payment','chauffeur-booking-system'); ?></h3>
			
				<div>
				
					<div>
						<div><?php esc_html_e('Payment','chauffeur-booking-system'); ?></div>
						<div>
<?php 
			echo esc_html($this->data['booking']['payment_name']); 
			if($Validation->isNotEmpty($this->data['booking']['woocommerce_payment_url']))
				echo '<br><a href="'.esc_url($this->data['booking']['woocommerce_payment_url']).'" target="_blank">'.__('Click to pay for this order','chauffeur-booking-system').'</a>';
?>
						</div>
					</div>   
				
				</div>
				
			</div>
<?php	
		}
?>	
		</div>