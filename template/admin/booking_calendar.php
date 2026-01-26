
		<div class="to">
			
			<div class="to-booking-calendar to-chbs-booking-calendar">
				
				<div class="to-booking-calendar-header">
					<div><?php echo ($this->data['header_date']); ?> </div>
					<div>
						<div><?php echo ($this->data['header_booking_status']); ?> </div>
						<div><?php echo ($this->data['header_vehicle']); ?> </div>
					</div>
				</div>
				
				<div class="to-booking-calendar-table">
					<?php echo ($this->data['calendar']); ?>
				</div>
				
				<input type="hidden" name="<?php CHBSHelper::getFormName('booking_calendar_year_number'); ?>" value="<?php echo (int)$this->data['booking_calendar_year_number']; ?>"/>
				<input type="hidden" name="<?php CHBSHelper::getFormName('booking_calendar_month_number'); ?>" value="<?php echo (int)$this->data['booking_calendar_month_number']; ?>"/>
				
				<?php echo $this->data['nonce']; ?>

			</div>

		</div>
<?php
		CHBSHelper::addInlineScript('chbs-admin',
		'
			jQuery(document).ready(function($)
			{
				$(\'.to\').themeOptionElement({init:true});
								
				$(\'#'.CHBSHelper::getFormName('booking_calendar_booking_status_id',false).'\').on(\'change\',function(e)
				{
					e.preventDefault();

					var BookingCalendar=new CHBSBookingCalendar();
					BookingCalendar.sendRequest();
				});		
				
				$(\'#'.CHBSHelper::getFormName('booking_calendar_vehicle_id',false).'\').on(\'change\',function(e)
				{
					e.preventDefault();

					var BookingCalendar=new CHBSBookingCalendar();
					BookingCalendar.sendRequest();
				});	
				
				$(\'.to-booking-calendar-header>div:first>a:first\').on(\'click\',function(e)
				{
					var BookingCalendar=new CHBSBookingCalendar();
					
					BookingCalendar.increaseMonthNumber(-1);
					BookingCalendar.sendRequest();
				});
				
				$(\'.to-booking-calendar-header>div:first>a:first+a\').on(\'click\',function(e)
				{
					var BookingCalendar=new CHBSBookingCalendar();
					
					BookingCalendar.increaseMonthNumber(1);
					BookingCalendar.sendRequest();
				});
			});	
		');