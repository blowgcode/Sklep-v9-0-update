
/******************************************************************************/
/******************************************************************************/

function CHBSBookingCalendar()
{
	/**************************************************************************/

	this.sendRequest=function()
	{
		var data={};

		data.action='chbs_booking_calendar_ajax';
					
		data.chbs_booking_calendar_year_number=jQuery('input[name="chbs_booking_calendar_year_number"]').val();
		data.chbs_booking_calendar_month_number=jQuery('input[name="chbs_booking_calendar_month_number"]').val();
		
		data.chbs_booking_calendar_booking_status_id=jQuery('select[name="chbs_booking_calendar_booking_status_id"]').val();
		
		data.chbs_booking_calendar_vehicle_id=jQuery('select[name="chbs_booking_calendar_vehicle_id"]').val();
		
		jQuery('.to').block({message:false,overlayCSS:{opacity:'0.3'}});

		jQuery.post(ajaxurl,data,function(response) 
		{		
			jQuery('.to').unblock({onUnblock:function()
			{ 
				jQuery('.to-booking-calendar-table').html(response.calendar);
				jQuery('.to-booking-calendar-header>div>h1').html(response.booking_calendar_header);
			}});

		},'json');
	};
	
	/**************************************************************************/
	
	this.increaseMonthNumber=function(step)
	{
		var month=parseInt(jQuery('input[name="chbs_booking_calendar_month_number"]').val(),10);
		
		month+=step;
		
		jQuery('input[name="chbs_booking_calendar_month_number"]').val(month);
	};

    /**************************************************************************/
};

/******************************************************************************/
/******************************************************************************/