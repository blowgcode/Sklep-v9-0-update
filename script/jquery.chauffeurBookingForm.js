/******************************************************************************/
/******************************************************************************/

;(function($,doc,win)
{
	"use strict";
	
	var ChauffeurBookingForm=function(object,option)
	{
		/**********************************************************************/

		var $this=$(object);
	
		var $optionDefault;
		var $option=$.extend($optionDefault,option);
		
		var $marker=[];

		var $googleMap;

		var $routePolyline=[];

		var $routeIndex=0;
		
		var $startLocation;
		
		var $googleMapHeightInterval;

		var $self=this;

		var $iti;
		
		var $sidebar;
		
		var $autocomplete=[];
		
		var $GoogleMapAPI;

		var $pickupCountryCode='';

		var $dropoffCountryCode='';

		var $tpaySelectionBound=false;
		
		/**********************************************************************/
		
		this.setup=function()
		{	 
			$GoogleMapAPI=new CHBSGoogleMapAPI($option.google_map_option.api_key);
			
			var helper=new CHBSHelper();
			helper.getMessageFromConsole();
			
			$self.e('select,input[type="hidden"]').each(function()
			{
				if($(this)[0].hasAttribute('data-value'))
					$(this).val($(this).attr('data-value'));
			});
			
			/***/
			
			if(parseInt($option.google_map_option.ask_load_enable,10)===1)
			{
				if(isNaN(parseInt($self.e('[name="chbs_google_maps_enable"]').val(),10)))
				{
					var url=window.location.href;

					if(url.indexOf('?')===-1) url+='?';
					else url+='&';

					if(confirm($option.message.google_map_ask_load_confirm)) 
						window.location.href=url+'chbs_google_maps_enable=1';
					else window.location.href=url+'chbs_google_maps_enable=-1';
					
					return;
				}
			}
			
			/***/
			
			$self.init();
			$self.resetPickupTimeOnLoad();
			
			if(parseInt($option.icon_field_enable,10)===1)
			{
				for(var i=1;i<=3;i++)
				{
					$self.e('input[name="chbs_pickup_date_service_type_'+i+'"]').before('<span class="chbs-meta-icon-2 chbs-meta-icon-2-date-1"></span>');
					$self.e('input[name="chbs_pickup_time_service_type_'+i+'"]').before('<span class="chbs-meta-icon-2 chbs-meta-icon-2-time-1"></span>');
					$self.e('input[name="chbs_return_date_service_type_'+i+'"]').before('<span class="chbs-meta-icon-2 chbs-meta-icon-2-date-1"></span>');
					$self.e('input[name="chbs_return_time_service_type_'+i+'"]').before('<span class="chbs-meta-icon-2 chbs-meta-icon-2-time-1"></span>');
				}

				for(var i=1;i<=2;i++)
				{
					$self.e('input[name="chbs_pickup_location_service_type_'+i+'"]').before('<span class="chbs-meta-icon-2 chbs-meta-icon-2-location-1"></span>');
					$self.e('input[name="chbs_dropoff_location_service_type_'+i+'"]').before('<span class="chbs-meta-icon-2 chbs-meta-icon-2-location-1"></span>');				
				}

				$self.e('input[name="chbs_passenger_adult_service_type_1"]').before('<span class="chbs-meta-icon-2 chbs-meta-icon-2-passengers-1"></span>');
				$self.e('input[name="chbs_passenger_adult_service_type_3"]').before('<span class="chbs-meta-icon-2 chbs-meta-icon-2-passengers-1"></span>');

				$self.e('input[name="chbs_passenger_children_service_type_1"]').before('<span class="chbs-meta-icon-2 chbs-meta-icon-2-passengers-1"></span>');
				$self.e('input[name="chbs_passenger_children_service_type_3"]').before('<span class="chbs-meta-icon-2 chbs-meta-icon-2-passengers-1"></span>');
			}
		};
			
		/**********************************************************************/
		
		this.init=function()
		{		   
			var helper=new CHBSHelper();
			
			if(helper.isMobile())
			{
				$self.e('input[name="chbs_pickup_date_service_type_1"]').prop('readonly',true);
				$self.e('input[name="chbs_pickup_date_service_type_2"]').prop('readonly',true);
				$self.e('input[name="chbs_pickup_date_service_type_3"]').prop('readonly',true);
				$self.e('input[name="chbs_return_date_service_type_1"]').prop('readonly',true);
				$self.e('input[name="chbs_return_date_service_type_3"]').prop('readonly',true);
			}
			
			$self.createButtonRadio('.chbs-booking-extra');
			$self.bindTpaySelection();
			
			/***/
			
			$(window).resize(function() 
			{
				try
				{
					$self.e('select').selectmenu('close');
				}
				catch(e) {}
				
				try
				{
					$self.e('.chbs-datepicker').datepicker('hide');
				}
				catch(e) {}
				
				try
				{
					$self.e('.chbs-timepicker').timepicker('hide');
				}
				catch(e) {}
				
				try
				{
					$self.e('.ui-timepicker-wrapper').css({opacity:0});
				}
				catch(e) {}
				
				try
				{
					var currCenter=$googleMap.getCenter();
					google.maps.event.trigger($googleMap,'resize');
					$googleMap.setCenter(currCenter);
				}
				catch(e) {}
			});
			
			$self.setWidthClass();
			
			/***/
			
			if(parseInt($option.use_my_location_link_enable,10)===1)
			{
				if(navigator.geolocation)
				{
					$self.e('.chbs-my-location-link').css('display','inline');		
					navigator.geolocation.getCurrentPosition($self.myLocationSuccess,$self.myLocationError);	
				}
			}
			
			/***/
			
			var active=1;
			var panel=$self.e('.chbs-tab>ul').children('li[data-id="'+parseInt($self.e('[name="chbs_service_type_id"]').val())+'"]',10);
			
			if(panel.length===1) active=panel.index();
			
			$self.e('.chbs-tab').tabs(
			{
				activate:function(event,ui)
				{
					$('.qtip').remove();
					
					$self.googleMapReInit();
					
					var serviceTypeId=$self.getServiceTypeId();
					$self.setServiceTypeId(serviceTypeId);
					
					$self.showRideInfo();
									
					$self.googleMapCreate();
					$self.googleMapCreateRoute();
					
					$self.addGAEvent('service_type_select',{'service_type_id':serviceTypeId});
				},
				active:active
			});
						  
			/***/
			
			$self.e('.chbs-main-navigation-default a').on('click',function(e)
			{
				e.preventDefault();
				
				var navigation=parseInt($(this).parent('li').data('step'),10);
				var step=parseInt($self.e('input[name="chbs_step"]').val(),10);
				
				$self.addGAEvent('navigation_top_select',{'step_current':step,'step_target':navigation});

				if(navigation-step===0) return;
				
				$self.goToStep(navigation-step);
			});
			
			$self.e('.chbs-button-step-next').on('click',function(e)
			{
				e.preventDefault();
				
				var step=parseInt($self.e('input[name="chbs_step"]').val(),10);
		
				$self.addGAEvent('navigation_bottom_click',{'step_current':step,'step_target':step+1});
				
				$self.goToStep(1);
			});
			
			$self.e('.chbs-button-step-prev').on('click',function(e)
			{
				e.preventDefault();
				
				var step=parseInt($self.e('input[name="chbs_step"]').val(),10);
		
				$self.addGAEvent('navigation_bottom_click',{'step_current':step,'step_target':step-1});
				
				$self.goToStep(-1);
			});
			
			/***/
			
			$self.e('.chbs-main-content').on('click','.chbs-form-field',function(e)
			{
				if($(this).find(':input[type="file"]').length) return;
				
				$('.qtip').remove();
				
				e.preventDefault();
				if(($(e.target).hasClass('chbs-location-add')) || ($(e.target).hasClass('chbs-location-remove'))) return;
				$(this).find(':input').focus(); 
				
				var select=$(this).find('select:not(.chbs-selectmenu-disable)');
				
				if(select.length)
					select.selectmenu('open');
			});
			
			/***/
			
			$self.e('.chbs-location-add').on('click',function(e)
			{
				e.preventDefault();

				var field=$(this).parent('.chbs-form-field:first');
				var fieldNext=field.next('.chbs-form-field-waypoint-duration');
				
				if(parseInt(fieldNext.length,10)===1) field=fieldNext;
				
				/***/
				
				var newField=$self.e('.chbs-form-field-location-autocomplete.chbs-hidden').clone(true,true);

				newField.insertAfter(field);
				newField.removeClass('chbs-hidden');
				
				newField.find(':input').focus();
				
				/***/

				var waypointDuration=$self.e('.chbs-form-field-waypoint-duration.chbs-hidden');
				if(parseInt(waypointDuration.length,10)===1)
				{
					var newFieldWaypointDuration=waypointDuration.clone(true,true);

					newFieldWaypointDuration.insertAfter(newField);
					newFieldWaypointDuration.removeClass('chbs-hidden');
					
					newFieldWaypointDuration.find('select').removeClass('chbs-selectmenu-disable');
					
					$self.createSelectMenu();
				}
				
				/***/
				
				$self.googleMapAutocompleteCreate(newField.find('input[type="text"]'));
				
				$self.reCalculateRoute();
				
				$self.createLabelTooltip();
			});

			$self.e('.chbs-location-remove').on('click',function(e)
			{
				e.preventDefault();
				
				var field=$(this).parent('.chbs-form-field:first');
				var fieldNext=field.next('.chbs-form-field-waypoint-duration');
						
				fieldNext.find('select').selectmenu('destroy');
						
				field.remove();
				fieldNext.remove();

				$self.googleMapCreate();
				$self.googleMapCreateRoute();
			});	   

			$self.e('.chbs-form-field-location-autocomplete input[type="text"]').each(function()
			{
				$self.googleMapAutocompleteCreate($(this));
			});
					   
			/***/
			
			$self.e('.chbs-payment-form').on('click','.chbs-payment>li>a',function(e)
			{
				e.preventDefault();
				
				$(this).parents('.chbs-payment').find('li>a').removeClass('chbs-state-selected');
				$(this).addClass('chbs-state-selected');
				
				$self.getGlobalNotice().addClass('chbs-hidden');
				
				var paymentId=$(this).attr('data-payment-id');
				var paymentName=$(this).attr('data-payment-name');
				
				$self.e('input[name="chbs_payment_id"]').val(paymentId);
				$self.toggleTpaySelection(paymentId);
				
				$self.addGAEvent('payment_select',{'payment_id':paymentId,'payment_name':paymentName});
				
				$self.createSummaryPriceElement();
			});
			
			$self.e('>*').on('click','.chbs-form-checkbox',function(e)
			{
				var text=$(this).next('input[type="hidden"]');
				var value=parseInt(text.val(),10)===1 ? 0 : 1;
				
				if(value===1) $(this).addClass('chbs-state-selected');
				else $(this).removeClass('chbs-state-selected');
				
				$(this).next('input[type="hidden"]').on('change',function(e)
				{ 
					var value=parseInt($(this).val(),10)===1 ? 1 : 0;
					var section=$(this).parents('.chbs-clear-fix:first').nextAll('.chbs-panel:first');

					if(value===0) section.addClass('chbs-hidden');
					else section.removeClass('chbs-hidden');

					$(window).scroll();
				});
				
				text.val(value).trigger('change');
			});
			
			/***/
			
			$self.e('.chbs-booking-extra').on('click','.chbs-booking-extra-list .chbs-button.chbs-button-style-2',function(e)
			{
				e.preventDefault();
				
				var extraNote=$(this).parents('li:first').find('.chbs-booking-form-extra-note');
				
				var bookingExtraId=$(this).parents('li:first').attr('data-booking_extra_id');
				var bookingExtraName=$(this).parents('li:first').attr('data-booking_extra_name');
				
				var buttonRadio=false;
				
				/****/
				
				if(!$(this).parent('.chbs-button-radio').length)
				{
					$(this).toggleClass('chbs-state-selected'); 
					
					if($(this).hasClass('chbs-state-selected'))
						extraNote.removeClass('chbs-hidden');
					else extraNote.addClass('chbs-hidden');
				}
				else
				{
					buttonRadio=true;
					
					if(parseInt($(this).attr('data-value'),10)!==0)
						extraNote.removeClass('chbs-hidden');
					else extraNote.addClass('chbs-hidden');
				}
				
				/****/
				
				$self.getSelectedBookingExtra();
			
				/****/
				
				if(buttonRadio)
				{
					$self.addGAEvent((parseInt($(this).attr('data-value'),10)!==0 ? 'booking_extra_select' : 'booking_extra_deselect'),{'booking_extra_id':bookingExtraId,'booking_extra_name':bookingExtraName});
				}
				else
				{
					$self.addGAEvent(($(this).hasClass('chbs-state-selected') ? 'booking_extra_select' : 'booking_extra_deselect'),{'booking_extra_id':bookingExtraId,'booking_extra_name':bookingExtraName});
				}
				
				/****/
				
				$self.createSummaryPriceElement();
			});
			
			/***/
			
			$self.e('.chbs-booking-extra').on('blur','.chbs-booking-extra-list input[type="text"]',function(e)
			{
			   if(isNaN($(this).val())) $(this).val(1);
			   $self.createSummaryPriceElement();
			});
			
			$self.e('.chbs-booking-extra').on('click','.chbs-booking-extra-list .chbs-column-2',function()
			{
				$(this).find('input[type="text"]').select();
			});
			
			/***/
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-vehicle-list .chbs-button.chbs-button-style-2:not(.chbs-button-on-request)',function(e)
			{
				e.preventDefault();
				
				if($(this).hasClass('chbs-state-selected')) return;

				$(this).addClass('chbs-state-selected');

				$self.e('.chbs-vehicle-list .chbs-button.chbs-button-style-2').not($(this)).removeClass('chbs-state-selected');

				var vehicleId=parseInt($(this).parents('.chbs-vehicle').attr('data-id'),10);
				var vehicleName=$(this).parents('.chbs-vehicle').attr('data-name');
				
				$self.addGAEvent('vehicle_select',{'vehicle_id':vehicleId,'vehicle_name':vehicleName});
				
				$self.e('input[name="chbs_vehicle_id"]').val(vehicleId);

				$self.e('.chbs-vehicle-content-price-bid>div').addClass('chbs-hidden');
				$self.e('.chbs-vehicle-list [name="chbs_vehicle_bid_price[]"]').val('');
				$(this).parents('.chbs-vehicle').find('.chbs-vehicle-content-price-bid>div:first-child').removeClass('chbs-hidden');

				$self.getGlobalNotice().addClass('chbs-hidden');
				
				$self.getSelectedBookingExtra(true);
				
				$self.calculateBaseLocationDistance(function()
				{
					$self.createSummaryPriceElement();

					if(parseInt($option.scroll_to_booking_extra_after_select_vehicle_enable,10)===1)
					{
						var header=$self.e('.chbs-booking-extra-header');
						if(parseInt(header.length,10)===1)
						{
							$.scrollTo(header,200,{offset:-50});
						}
						else
						{
							var navigationButton=$self.e('.chbs-main-content-step-2 .chbs-main-content-navigation-button');
							$.scrollTo(navigationButton,200,{offset:-1*parseInt($(window).height(),10)+110});
						}
					}
					
					$self.manageBookingExtra();
				});
			});
			
			/***/
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-vehicle .chbs-vehicle-content .chbs-show-more-button a',function(e)
			{
				e.preventDefault();
				
				$(this).toggleClass('chbs-state-selected');
				
				var section=$(this).parents('.chbs-vehicle:first').find('.chbs-vehicle-content-description');
				
				var height=parseInt(section.children('div').actual('outerHeight',{includeMargin:true}),10);
				
				if(section.hasClass('chbs-state-open'))
				{
					section.animate({height:0},150,function()
					{
						section.removeClass('chbs-state-open');
						$(window).scroll();
					});					  
				}
				else
				{
					section.animate({height:height},150,function()
					{
						section.css('height','auto');
						section.addClass('chbs-state-open');
						$(window).scroll();
					});						
				}
			});
			
			/***/
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-booking-extra .chbs-booking-extra-header .chbs-show-more-button a',function(e)
			{
				e.preventDefault();
								
				$(this).toggleClass('chbs-state-selected');
				
				var section=$(this).parents('.chbs-booking-extra:first').children('.chbs-booking-extra-header+div');
				
				var height=parseInt(section.children('div').actual('outerHeight',{includeMargin:true}),10);
				
				if(section.hasClass('chbs-state-open'))
				{
					section.animate({height:0},150,function()
					{
						section.removeClass('chbs-state-open');
						$(window).scroll();
					});					  
				}
				else
				{
					section.animate({height:height},150,function()
					{
						section.css('height','auto');
						section.addClass('chbs-state-open');
						$(window).scroll();
					});						
				}
			});
			
			/***/
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-summary .chbs-summary-header a',function(e)
			{
				e.preventDefault();
				$self.goToStep(parseInt($(this).attr('data-step'),10)-2);
			});
			
			/***/
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-coupon-code-section a',function(e)
			{
				e.preventDefault();
				
				$self.addGAEvent('coupon_apply',{'code':$self.e('.chbs-coupon-code-section [name="chbs_coupon_code"]').val()});
				
				$self.setAction('coupon_code_check');
	   
				$self.post($self.e('form[name="chbs-form"]').serialize(),function(response)
				{
					$self.e('.chbs-summary-price-element').replaceWith(response.html);
					
					var object=$self.e('.chbs-coupon-code-section');
					
					object.qtip(
					{
						show:
						{ 
							target:$(this) 
						},
						style:
						{ 
							classes:(response.error===1 ? 'chbs-qtip chbs-qtip-error' : 'chbs-qtip chbs-qtip-success')
						},
						content:
						{ 
							text:response.message 
						},
						position:
						{ 
							my:($option.is_rtl ? 'bottom right' : 'bottom left'),
							at:($option.is_rtl ? 'top right' : 'top left'),
							container:object.parent()
						}
					}).qtip('show');	
				});
			});
			
			/***/
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-gratuity-section a',function(e)
			{
				e.preventDefault();
				
				$self.setAction('gratuity_customer_set');
				
				$self.addGAEvent('gratuity_apply');
	   
				$self.post($self.e('form[name="chbs-form"]').serialize(),function(response)
				{  
					$self.e('.chbs-summary-price-element').replaceWith(response.html);
					
					var object=$self.e('.chbs-gratuity-section');
					
					object.qtip(
					{
						show:
						{ 
							target:$(this) 
						},
						style:
						{ 
							classes:(response.error===1 ? 'chbs-qtip chbs-qtip-error' : 'chbs-qtip chbs-qtip-success')
						},
						content:
						{ 
							text:response.message 
						},
						position:
						{ 
							my:($option.is_rtl ? 'bottom right' : 'bottom left'),
							at:($option.is_rtl ? 'top right' : 'top left'),
							container:object.parent()
						}
					}).qtip('show');
					
					object.find('[name="chbs_gratuity_customer_value"]').val(response.gratuity);
				});
			});			
			
			/***/
			
			$self.e('.chbs-datepicker:not(.chbs-datepicker-form-element)').datepicker(
			{
				autoSize:true,
				firstDay:$option.first_day_week,
				minDate:$option.datetime_min_format,
				maxDate:$option.datetime_max_format,
				dateFormat:$option.date_format_js,
				beforeShow:function(date,instance)
				{
					var helper=new CHBSHelper();
					var value=helper.getValueFromClass($(instance.dpDiv),'chbs-booking-form-id-');
					
					if(value!==false) $(instance.dpDiv).removeClass('chbs-booking-form-id-'+value);
					
					$(instance.dpDiv).addClass('chbs-booking-form-id-'+$option.booking_form_id).addClass('chbs-datepicker');
					
					if($(date).attr('name')==='chbs_return_date_service_type_'+$self.getServiceTypeId())
					{
						try
						{
							var datePickup=$self.e('[name="chbs_pickup_date_service_type_'+$self.getServiceTypeId()+'"]').val();
							var dateParse=$.datepicker.parseDate($option.date_format_js,datePickup);
							
							if(dateParse!==null)
							{
								$(this).datepicker('option','minDate',datePickup); 
							}
						}
						catch(e)
						{
							
						}
					}	
				},
				beforeShowDay:function(date)
				{
					var helper=new CHBSHelper();
					
					var dayWeek=parseInt(date.getDay(),10);
					if(dayWeek===0) dayWeek=7;
					
					date=$.datepicker.formatDate('dd-mm-yy',date);
					
					for(var i in $option.date_exclude)
					{
						var r=helper.compareDate([date,$option.date_exclude[i].start,$option.date_exclude[i].stop]);
						if(r) return([false,'','']);
					}
					
					/***/
					
					var sDate=date.split('-');
					var date=new Date(sDate[2],sDate[1]-1,sDate[0]);
										
					if((!$option.business_hour[dayWeek].start) || (!$option.business_hour[dayWeek].stop)) 
						return([false,'','']);
					
					/***/
					
					return([true,'','']);
				},
				onSelect:function(date,object)
				{
					$self.createTimePicker($(this),object);
				}
			});
			
			$('.ui-datepicker').addClass('notranslate');
			
			if(parseInt($option.timepicker_dropdown_list_enable,10)===1)
			{
				$this.on('focusin','.chbs-timepicker:not(.chbs-timepicker-form-element)',function()
				{
					var helper=new CHBSHelper();

					var prefix=$(this).attr('name').indexOf('pickup')>-1 ? 'pickup' : 'return';

					var field=$self.e('input[name="chbs_'+prefix+'_date_service_type_'+$self.getServiceTypeId()+'"]');

					if(helper.isEmpty(field.val()))
					{
						$(this).timepicker('remove');
						field.click();
						return;
					}
					else
					{
						if(helper.isEmpty($(this).val()))
						{
							if($(this).hasClass('ui-timepicker-input'))
							{
								$(this).timepicker('show');
							}
							else
							{
								$self.createTimePicker(field,field.datepicker());
							}
						}
					}
				});
			}
			
			/***/
			
			$self.createSelectMenu();
			
			$self.createAutocomplete('.chbs-form-field-location-fixed-autocomplete');
			$self.createAutocomplete('.chbs-form-field-route-autocomplete');
			
			/***/
			
			$self.e('.chbs-booking-extra').on('blur','.chbs-booking-extra-list input[type="text"]',function()
			{
				if(!$(this)[0].hasAttribute('data-quantity-max')) return;
				
				var value=$(this).val();
				
				if(isNaN(value)) value=1;
				
				value=parseInt(value,10);
				
				if(value>parseInt($(this).attr('data-quantity-max'),10))
					$(this).val($(this).attr('data-quantity-max'));
				
				$self.createSummaryPriceElement();
			});
			
			$self.e('.chbs-form-field').has('select').css({cursor:'pointer'});
			
			/***/
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-button-sign-up',function(e)
			{
				e.preventDefault();
				
				$self.addGAEvent('user_sign_up');
				
				$self.e('.chbs-client-form-sign-up').removeClass('chbs-hidden');
				$self.e('input[name="chbs_client_account"]').val(1);
			});
			
			/***/
			
			$self.e('.chbs-main-content').on('click','.chbs-button-widget-submit',function(e)
			{
				e.preventDefault();
			   
				var helper=new CHBSHelper();
				
				var data={};
				
				data.service_type_id=$self.getServiceTypeId();
				
				data.pickup_date=$self.e('[name="chbs_pickup_date_service_type_'+data.service_type_id+'"]').val();
				data.pickup_time=$self.e('[name="chbs_pickup_time_service_type_'+data.service_type_id+'"]').val();
				
				if($.inArray($self.getServiceTypeId(),[1,2,3])>-1)
				{
					var coordinate=$self.e('[name="chbs_pickup_location_coordinate_service_type_'+data.service_type_id+'"]').val();
					if(!helper.isEmpty(coordinate))
					{
						var json=JSON.parse(coordinate);
						data.pickup_location_lat=json.lat;
						data.pickup_location_lng=json.lng;
						data.pickup_location_zip_code=json.zip_code;
						data.pickup_location_locality=json.locality;
						data.pickup_location_address=json.address;
						data.pickup_location_text=$self.e('[name="chbs_pickup_location_service_type_'+data.service_type_id+'"]').val();  
					}
					
					var coordinate=$self.e('[name="chbs_dropoff_location_coordinate_service_type_'+data.service_type_id+'"]').val();
					if(!helper.isEmpty(coordinate))
					{
						var json=JSON.parse(coordinate);
						data.dropoff_location_lat=json.lat;
						data.dropoff_location_lng=json.lng;
						data.dropoff_location_zip_code=json.zip_code;
						data.dropoff_location_locality=json.locality;
						data.dropoff_location_address=json.address;
						data.dropoff_location_text=$self.e('[name="chbs_dropoff_location_service_type_'+data.service_type_id+'"]').val();
					}	
					
					if($.inArray($self.getServiceTypeId(),[1,2])>-1)
					{
						var pickupLocationId=$self.e('[name="chbs_fixed_location_pickup_service_type_'+data.service_type_id+'"]').val();
						if(parseInt(pickupLocationId,10)>0)
							data.fixed_location_pickup_id=pickupLocationId;

						var dropoffLocationId=$self.e('[name="chbs_fixed_location_dropoff_service_type_'+data.service_type_id+'"]').val();
						if(parseInt(dropoffLocationId,10)>0)
							data.fixed_location_dropoff_id=dropoffLocationId;	
					}
				}
				
				if($.inArray($self.getServiceTypeId(),[3])>-1)
				{
					data.route_id=$self.e('[name="chbs_route_service_type_'+data.service_type_id+'"]').val();	
				}
				
				if($.inArray($self.getServiceTypeId(),[1,3])>-1)
				{
					data.extra_time=$self.e('[name="chbs_extra_time_service_type_'+data.service_type_id+'"]').val();
					data.transfer_type=$self.e('[name="chbs_transfer_type_service_type_'+data.service_type_id+'"]').val(); 
			  
					if($.inArray(data.transfer_type,[3]))
					{
						data.duration=$self.e('[name="chbs_duration_service_type_'+data.service_type_id+'"]').val();  
						
						data.return_date=$self.e('[name="chbs_return_date_service_type_'+data.service_type_id+'"]').val();  
						data.return_time=$self.e('[name="chbs_return_time_service_type_'+data.service_type_id+'"]').val();  
					}
				}
				
				if($.inArray($self.getServiceTypeId(),[2])>-1)
				{
					data.duration=$self.e('[name="chbs_duration_service_type_'+data.service_type_id+'"]').val();  
				}
				
				var passengerAdult=$self.e('[name="chbs_passenger_adult_service_type_'+data.service_type_id+'"]');
				if(passengerAdult.length===1) data.passenger_adult=passengerAdult.val();
				
				var passengerChildren=$self.e('[name="chbs_passenger_children_service_type_'+data.service_type_id+'"]');
				if(passengerChildren.length===1) data.passenger_children=passengerChildren.val();
				
				data.currency=$self.e('[name="chbs_currency"]').val();
				
				data.widget_submit=1;
                data.widget_second_step=parseInt($self.e('[name="chbs_widget_second_step"]').val(),10);  

				/***/
				
				var url=$option.widget.booking_form_url;
				
				if(url.indexOf('?')===-1) url+='?';
				else url+='&';
				
				url+=decodeURI($.param(data));
				
				var form=$self.e('form[name="chbs-form"]');
				
				form.attr('action',url).submit();
			});
			
			/***/
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-button-sign-in',function(e)
			{
				e.preventDefault();
				
				$self.getGlobalNotice().addClass('chbs-hidden');
				
				$self.preloader(true);
				
				$self.addGAEvent('user_sign_in');
			
				$self.setAction('user_sign_in');
	   
				$self.post($self.e('form[name="chbs-form"]').serialize(),function(response)
				{
					if(parseInt(response.user_sign_in,10)===1)
					{
						$self.e('.chbs-main-content-step-2 .chbs-client-form').html('');
				 
						if(typeof(response.client_form_sign_up)!=='undefined')
							$self.e('.chbs-main-content-step-2 .chbs-client-form').append(response.client_form_sign_up);  
	   
						if(typeof(response.summary)!=='undefined')
							$self.e('.chbs-main-content-step-2>.chbs-layout-25x75 .chbs-layout-column-left:first').html(response.summary[0]);						
						
						$self.createSelectMenu();
						
						$self.createAutocomplete('.chbs-form-field-location-fixed-autocomplete');;
						$self.createAutocomplete('.chbs-form-field-route-autocomplete');
					}
					else
					{
						if(typeof(response.error.global[0])!=='undefined')
							$self.getGlobalNotice().removeClass('chbs-hidden').html(response.error.global[0].message);
					}
					
					$self.preloader(false);
				});
			});
			
			/***/
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-sign-up-password-generate',function(e)
			{
				e.preventDefault();
				
				var helper=new CHBSHelper();
				var password=helper.generatePassword(8);
				
				$self.e('input[name="chbs_client_sign_up_password"],input[name="chbs_client_sign_up_password_retype"]').val(password);
			});
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-sign-up-password-show',function(e)
			{
				e.preventDefault();
				
				var password=$self.e('input[name="chbs_client_sign_up_password"]');
				password.attr('type',(password.attr('type')==='password' ? 'text' : 'password'));
			});
			
			/***/
			
			if(parseInt($option.vehicle_price_calculation_first_step_enable,10)===1) 
			{	
				$self.e('.chbs-main-content-step-1').on('change',':input',function(e)
				{
					$self.doVehiclePriceCalculationStepFirst();
				});
			}
		
			/***/
			
			$self.e('.chbs-notice-fixed a').on('click',function(e) 
			{
				e.preventDefault();
				$(this).parents('.chbs-notice-fixed').addClass('chbs-hidden');
			});
		
			/***/
			
			$(document).bind('keypress',function(e) 
			{
				if(parseInt(e.which,10)===13) 
				{
					switch($(e.target).attr('name'))
					{
						case 'chbs_client_sign_in_login':
						case 'chbs_client_sign_in_password':
						
							$self.e('.chbs-main-content-step-2 .chbs-button-sign-in').trigger('click');
						
						break;
					}
				}				   
			});
			
			$(document).unbind('keydown').bind('keydown',function(e) 
			{
				switch($(e.target).attr('name'))
				{
					case 'chbs_passenger_adult_service_type_1':
					case 'chbs_passenger_adult_service_type_2':
					case 'chbs_passenger_adult_service_type_3':
					case 'chbs_passenger_children_service_type_1':
					case 'chbs_passenger_children_service_type_2':
					case 'chbs_passenger_children_service_type_3':	

						if($.inArray(parseInt(e.keyCode,10),[38,40])>-1)
						{
							var value=parseInt($(e.target).val(),10);
							if(isNaN(value)) value=0;

							if(parseInt(e.keyCode,10)===38)
								value=(value+1)>99 ? 99 : value+1;
							else if(parseInt(e.keyCode,10)===40)
								value=(value-1)<0 ? 0 : value-1;

							$(e.target).val(parseInt(value));
						}
					
					break;
				} 
			});
			
			/***/
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-quantity-section .chbs-quantity-section-button',function(e)
			{			   
				var textField=$(this).parent().children('input[type="text"]');
				if(parseInt(textField.length,10)!==1) return;

				var step=parseInt($(this).attr('data-step'),10);
				var value=parseInt(textField.val(),10);
				
				var minValue=1;
				var maxValue=parseInt(textField.attr('data-quantity-max'),10);
				
				value+=step;
				
				if((value<minValue) || (value>maxValue)) return;
				
				textField.val(value);
				
				$(this).parents('li:first').find('.chbs-column-3 .chbs-button').removeClass('chbs-state-selected');
				$(this).parents('li:first').find('.chbs-column-3 .chbs-button:first').trigger('click');
				
			});
			
			/***/
			
			$self.e('.chbs-main-content-step-2 .chbs-vehicle-list').on('click','.chbs-pagination a',function(e)
			{
				e.preventDefault();
				
				var i=0;
				
				var vehiclePerPage=parseInt($(this).parent('.chbs-pagination').attr('data-vehicle_per_page'),10);
				
				var vehicleCount=$self.e('.chbs-vehicle-list>ul>li').length;
				
				/***/
				
				var vehicleFirst=0;
				$self.e('.chbs-vehicle-list>ul>li').each(function()
				{
					i++;
					if((!$(this).hasClass('chbs-hidden')) && (vehicleFirst===0)) vehicleFirst=i;
				});
				
				/***/
	   
				var step=parseInt($(this).attr('href').substr(1),10);
				
				var range1=vehicleFirst+(step*vehiclePerPage);
				var range2=range1+vehiclePerPage;
				
				if(range1>vehicleCount)
				{
					return;
				}
				
				if(range1<=0)
				{
					return;
				}
				
				/***/
				
				i=0;
				
				$self.e('.chbs-vehicle-list>ul>li').each(function()
				{
					i++;
					$(this).addClass('chbs-hidden');
					
					if((i>=range1) && (i<range2))
						$(this).removeClass('chbs-hidden');
				});
			});
					
			/***/

			$self.createLabelTooltip();
			
			/***/
			
			$self.googleMapCreate();
			$self.googleMapInit();
			
			var firstOption=$self.e('[name="chbs_route_service_type_3"]>option:first');
			
			if(parseInt(firstOption.val(),10)===-1)
			{
				if(typeof($startLocation)!=='undefined')
				{
					var data=[{lat:$startLocation.lat(),lng:$startLocation.lng()}];
					firstOption.attr('data-coordinate',JSON.stringify(data));
				}
			}
		   
			$self.googleMapCreateRoute(function()
			{
				if((parseInt(helper.urlParam('widget_submit'),10)===1) && (parseInt(helper.urlParam('widget_second_step'),10)===1))	
				{
					$self.goToStep(1,function()
					{
						$this.removeClass('chbs-hidden');
						$self.createStickySidebar();
						$(window).scroll();
					});
				}
				else $this.removeClass('chbs-hidden');	
				$self.googleMapStartCustomizeHeight();  
			});
			
			$self.moveSubmitButton();
			$self.setBidPriceVehicle();
			
			$self.showRideInfo();			
		};
		
		/**********************************************************************/
		
		this.createTimePicker=function(dateField,object)
		{
			var helper=new CHBSHelper();

			var dateSelected=[object.selectedDay,object.selectedMonth+1,object.selectedYear];

			for(var i in dateSelected)
			{
				if(new String(dateSelected[i]).length===1) dateSelected[i]='0'+dateSelected[i];
			}

			dateSelected=dateSelected[0]+'-'+dateSelected[1]+'-'+dateSelected[2];

			if(helper.isEmpty(dateField.datepicker('getDate'))) return;

			var dayWeek=parseInt(dateField.datepicker('getDate').getDay(),10);
			if(dayWeek===0) dayWeek=7;					

			var prefix=dateField.attr('name').indexOf('pickup')>-1 ? 'pickup' : 'return';
			var timeField=$self.e('input[name="chbs_'+prefix+'_time_service_type_'+$self.getServiceTypeId()+'"]');
			var allowDefaultTime=true;

			if(prefix==='pickup' && !$self.hasValidPickupLocation($self.getServiceTypeId()))
				allowDefaultTime=false;

			if(parseInt($option.pickup_time_field_write_enable,10)===1 || prefix==='return')
			{	
				var minTime='';
				var maxTime='';

				/***/

				if((helper.isEmpty(minTime)) || (helper.isEmpty(maxTime)))
				{
					if(new String(typeof($option.business_hour[dayWeek]))!=='undefined')
					{
						minTime=$option.business_hour[dayWeek].start;
						maxTime=$option.business_hour[dayWeek].stop;
					}
				}

				/***/

				var t=$option.datetime_min.split(' ');

				if(dateSelected===t[0])
				{
					if(Date.parse('01/01/1970 '+t[1])>Date.parse('01/01/1970 '+minTime))
						minTime=t[1];
				}

				/***/

				if(!helper.isEmpty($option.datetime_max))
				{
					var t=$option.datetime_max.split(' ');

					if(dateSelected===t[0])
					{
						if(Date.parse('01/01/1970 '+t[1])<Date.parse('01/01/1970 '+maxTime))
							maxTime=t[1];
					}					
				}

				/***/

				var option=
				{
					appendTo:$this,
					showOn:[],
					timeFormat:$option.time_format,
					step:$option.timepicker_step,
					disableTouchKeyboard:true,
					minTime:minTime,
					maxTime:maxTime,
					disableTextInput:parseInt($option.timepicker_field_readonly,10)===1 ? true : false
				};

				/***/

				if(parseInt($option.timepicker_dropdown_list_enable,10)===1)
				{	
					try
					{
						timeField.timepicker('remove');
					}
					catch(e) {}

					/***/

					if(allowDefaultTime && typeof($option.business_hour[dayWeek].default_format)!='undefined')
					{
						option.scrollDefault=$option.business_hour[dayWeek].default_format;
					}

					/***/

					timeField.timepicker(option);

					timeField.val('').timepicker('show');
					timeField.blur();				

					$self.setTimepicker(timeField);

					/***/			

					if(parseInt($self.getServiceTypeId(),10)===3)
					{
						var routeField=$self.e('select[name="chbs_route_service_type_3"]>option:selected');

						try
						{
							var pickupHour=JSON.parse(routeField.attr('data-pickup_hour'));

							if((pickupHour[dayWeek]!=undefined) && (pickupHour[dayWeek].hour.length>0))
							{
								$('.ui-timepicker-list>li').addClass('ui-timepicker-disabled').each(function()
								{
									if($.inArray(dateField.text(),pickupHour[dayWeek].hour)>-1)
									{
										dateField.removeClass('ui-timepicker-disabled');
									}
								});
							}
						}
						catch(e) {}
					}

					/***/

					if(parseInt($option.timepicker_hour_range_enable,10)===1)
					{
						$('.ui-timepicker-list>li').each(function()
						{
							var node=[dateField,dateField.next()];

							if(node[1].length===1)
								node[0].html(node[0].text()+' - '+node[1].text());
							else node[0].remove();
						});

						timeField.on('selectTime',function(obj1,obj2) 
						{
							var element=$('.ui-timepicker-list>.ui-timepicker-selected');

							if(element.length===1)
							{
								dateField.val(element.text());
							}
						});
					}

					/***/

					if(allowDefaultTime && typeof($option.business_hour[dayWeek].default_format)!='undefined')
					{
						$('.ui-timepicker-list>li:contains("'+$option.business_hour[dayWeek].default_format+'"):last').addClass('ui-timepicker-selected');
					}
				}
			}
			else
			{	
				if(allowDefaultTime && new String(typeof($option.business_hour[dayWeek]))!=='undefined')
				{
					timeField.val($option.business_hour[dayWeek].default_format);
				}
				else
				{
					timeField.val('');
				}
			}			
		};
		
		/**********************************************************************/
		
		this.setVehiclePriceCalculationStepFirst=function(state)
		{
			$self.e('input[name="chbs_vehicle_price_calculation_first_step_action"]').val(state);
		};
		
		/**********************************************************************/

		this.getVehiclePriceCalculationStepFirst=function()
		{
			return(parseInt($self.e('input[name="chbs_vehicle_price_calculation_first_step_action"]').val(),10));
		};
		
		/**********************************************************************/
		
		this.increaseVehiclePriceCalculationStepFirst=function(step)
		{
			var value=parseInt($self.getVehiclePriceCalculationStepFirst(),10);
			$self.setVehiclePriceCalculationStepFirst(value+step);
		};
		
		/**********************************************************************/
		
		this.doVehiclePriceCalculationStepFirst=function()
		{
			if(parseInt($option.vehicle_price_calculation_first_step_enable,10)===1) 
			{	
				var distance=$self.e('[name="chbs_distance_sum"]').val();
				var duration=$self.e('[name="chbs_duration_sum"]').val();	
				
				if(distance>0 || duration>0)
				{
					$self.increaseVehiclePriceCalculationStepFirst(1);

					$self.goToStep(1,function() 
					{
						$self.increaseVehiclePriceCalculationStepFirst(-1);
					});
				}
			}
		};
		
		/**********************************************************************/
		
		this.showRideInfo=function()
		{
			var serviceTypeId=$self.getServiceTypeId();
					
			if(parseInt(serviceTypeId,10)===2)
				$self.e('.chbs-ride-info>div:first').addClass('chbs-hidden');
			else $self.e('.chbs-ride-info>div:first').removeClass('chbs-hidden');
		};
		
		/**********************************************************************/
		
		this.myLocationSuccess=function(position)
		{
			$self.e('.chbs-my-location-link').on('click',function()
			{
				var coordinate= 
				{
					lat: position.coords.latitude,
					lng: position.coords.longitude
				};
			
				var field=$self.e('input[name="chbs_pickup_location_coordinate_service_type_'+$self.getServiceTypeId()+'"]');
				
				field.val(JSON.stringify(coordinate));

				$self.googleMapSetAddress(field,function()
				{
					$self.googleMapCreate();
					$self.googleMapCreateRoute();   
				},true);
			});
		};
		
		/**********************************************************************/
		
		this.myLocationError=function(error)
		{
			console.log(error);
		};
		
		/**********************************************************************/
		
		this.getVehicleSelectedId=function()
		{
			var vehicleId=parseInt($self.e('.chbs-vehicle .chbs-vehicle-content-header .chbs-button.chbs-state-selected').parents('.chbs-vehicle').attr('data-id'),10);
			return(vehicleId);
		}
		
		/**********************************************************************/
		
		this.setBidPriceVehicle=function()
		{
			$self.e('.chbs-main-content-step-2')
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-vehicle-content-price-bid>div:first-child>a',function(e)
			{
				e.preventDefault();
				
				$(this).parent('div').addClass('chbs-hidden');
				$(this).parent('div').next('div').removeClass('chbs-hidden');
			});
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-vehicle-content-price-bid>div+div>input+a',function(e)
			{
				var input=$(this).prev('input');
				
				e.preventDefault();
				
				$self.setAction('vehicle_bid_price_check');
	   
				$self.preloader(true);
				$self.post($self.e('form[name="chbs-form"]').serialize(),function(response)				
				{
					$self.preloader(false);
					if(typeof(response.html)!=='undefined')
					{
						if(typeof(response.bid_vehicle_min_value)!='undefined')
						{
							if(confirm(response.bid_question))
							{
								input.val(response.bid_vehicle_min_value);
								
								$self.preloader(true);
								$self.post($self.e('form[name="chbs-form"]').serialize(),function(response)				
								{
									$self.preloader(false);
									if(typeof(response.html)!=='undefined')
									{
										$self.e('.chbs-summary-price-element').replaceWith(response.html);
									}
								});
							}
							else
							{
								$self.e('.chbs-summary-price-element').replaceWith(response.html);
								if(typeof(response.bid_notice)!='undefined') alert(response.bid_notice);
							}
						}
						else
						{
							$self.e('.chbs-summary-price-element').replaceWith(response.html);
							if(typeof(response.bid_notice)!='undefined') alert(response.bid_notice);
						}
					}
				});
			});
			
			$self.e('.chbs-main-content-step-2').on('click','.chbs-vehicle-content-price-bid>div+div>input+a+a',function(e)
			{
				e.preventDefault();
				
				$(this).parent('div').addClass('chbs-hidden');
				$(this).parent('div').prev('div').removeClass('chbs-hidden');
				
				$(this).parent('div').find('input[type="text"]').val('');
				
				$self.setAction('vehicle_bid_price_check');
	   
				$self.post($self.e('form[name="chbs-form"]').serialize(),function(response)				
				{
					if(typeof(response.html)!=='undefined')
						$self.e('.chbs-summary-price-element').replaceWith(response.html);
				});
			});		
		};
		
		/**********************************************************************/
		
		this.setPayment=function()
		{
			var paymentId=parseInt($self.e('input[name="chbs_payment_id"]').val(),10);
			if(paymentId>0) $self.e('.chbs-payment>li>a[data-payment-id="'+paymentId+'"]').addClass('chbs-state-selected');
			$self.toggleTpaySelection(paymentId);
			$self.restoreTpaySelection();
		};

		/**********************************************************************/
		
		this.toggleTpaySelection=function(paymentId)
		{
			var tpaySelection=$self.e('.chbs-payment-tpay-selection');
			
			if(!tpaySelection.length) return;
			
			if(parseInt(paymentId,10)===parseInt(tpaySelection.data('payment-id'),10))
				tpaySelection.removeClass('chbs-hidden');
			else tpaySelection.addClass('chbs-hidden');
		};
		
		/**********************************************************************/
		
		this.setTpayGroupId=function(groupId)
		{
			if(isNaN(parseInt(groupId,10))) return;
			
			$self.e('input[name="chbs_payment_tpay_group_id"]').val(groupId);
			$self.e('input[name="payment_tpay_group_id"]').val(groupId);
		};
		
		/**********************************************************************/
		
		this.restoreTpaySelection=function()
		{
			var groupId=$self.e('input[name="chbs_payment_tpay_group_id"]').val();
			if(isNaN(parseInt(groupId,10))) groupId=$self.e('input[name="payment_tpay_group_id"]').val();
			if(isNaN(parseInt(groupId,10))) return;
			
			var selection=$self.e('.chbs-payment-tpay-selection');
			if(!selection.length) return;
			
			selection.find('.tpay-group-holder').removeClass('tpay-active');
			selection.find('.tpay-group-holder[data-tpay-group-id="'+groupId+'"]').addClass('tpay-active');
		};
		
		/**********************************************************************/
		
		this.bindTpaySelection=function()
		{
			if($tpaySelectionBound) return;
			
			$tpaySelectionBound=true;
			
			var form=$self.e('form[name="chbs-form"]');
			
			form.on('click','.chbs-payment-tpay-selection .tpay-group-holder',function()
			{
				var groupId=$(this).attr('data-tpay-group-id');
				if(isNaN(parseInt(groupId,10))) return;
				
				$(this).closest('.chbs-payment-tpay-selection').find('.tpay-group-holder').removeClass('tpay-active');
				$(this).addClass('tpay-active');
				$self.setTpayGroupId(groupId);
			});
			
			form.on('change','.chbs-payment-tpay-selection input[name="groupId"], .chbs-payment-tpay-selection select[name="bank_list"]',function()
			{
				var groupId=$(this).val();
				$self.setTpayGroupId(groupId);
			});
		};
		
		/**********************************************************************/
		
		this.moveSubmitButton=function()
		{
			if(($this.hasClass('chbs-widget')) && ($this.hasClass('chbs-widget')) && ($this.hasClass('chbs-widget-style-2')))
			{
				var button=$self.e('.chbs-main-content-step-1 .chbs-button-widget-submit').parent();

				button.clone().appendTo($self.e('#panel-1'));
				button.clone().appendTo($self.e('#panel-2'));
				button.clone().appendTo($self.e('#panel-3'));
				
				button.remove();
			}
		};
		
		/**********************************************************************/
		
		this.convertTimeToMinute=function(time)
		{
			time=time.split(':');
			return(time[0]*60+time[1]);
		};
		
		/**********************************************************************/
		
		this.createLabelTooltip=function()
		{
			$self.e('.chbs-tooltip').qtip(
			{
				style:
				{ 
					classes:'chbs-qtip chbs-qtip-success'
				},
				position:
				{ 
					my:'bottom left',
					at:'top left',
					container:$this
				}
			});
		};
		
		/**********************************************************************/
		
		this.setTimepicker=function(field)
		{
			if((parseInt($option.timepicker_dropdown_list_enable,10)===1) || (field.hasClass('chbs-timepicker-form-element')))
				$('.ui-timepicker-wrapper').css({opacity:1,'width':field.parent('div').outerWidth()+1});
		};
		
        /**********************************************************************/
        
		this.createFormElementFieldDatePicker=function()
		{
			var option=
			{
				autoSize:true,
				firstDay:$option.first_day_week,
				dateFormat:$option.date_format_js,
				beforeShow:function(date,instance)
				{
					var helper=new CHBSHelper();
					var value=helper.getValueFromClass($(instance.dpDiv),'chbs-booking-form-id-');
					
					if(value!==false) $(instance.dpDiv).removeClass('chbs-booking-form-id-'+value);
					
					$(instance.dpDiv).addClass('chbs-booking-form-id-'+$option.booking_form_id).addClass('chbs-datepicker');
				}
			};
			
			$self.e('.chbs-datepicker.chbs-datepicker-form-element').datepicker(option);
		};
		
        /**********************************************************************/
        
		this.createFormElementFieldTimePicker=function()
		{
			$self.e('.chbs-main-content').on('click','.chbs-timepicker.chbs-timepicker-form-element',function(e)
			{
				var option=
				{
					showOn:[],
					appendTo:$this,
					showOnFocus:false,
					timeFormat:$option.time_format,
				};				
				
				$(this).timepicker(option);

				$(this).val('').timepicker('show');
				$(this).blur();

				$self.setTimepicker($(this));				
			});
		};
		
		/**********************************************************************/
		
		this.createFormElementFieldAutocomplete=function()
		{
			var helper=new CHBSHelper();
			
			$self.e('.chbs-autocomplete-form-element').each(function()
			{
				var id='chbs_autocomplete_form_element_'+helper.getRandomString(16);
				
				$(this).attr('id',id);
				
				var field=$(this);
				
				var autocomplete=new google.maps.places.Autocomplete(document.getElementById(id),option);
				
				autocomplete.addListener('place_changed',function(id)
				{	
					var place=autocomplete.getPlace();
					
					if(!place.geometry)
					{
						alert($option.message.place_geometry_error);
						field.val('');
						return(false);
					}

					var placeData=
					{
						lat:place.geometry.location.lat(),
						lng:place.geometry.location.lng(),
						address:$self.removeDoubleQuote(field.val()),
						zip_code:$self.getElementFromPlace(place,'postal_code'),
						country_code:$self.getElementFromPlace(place,'country','short_name')
					};	
					
					$(field).siblings('input[type="hidden"]').val(JSON.stringify(placeData));
				});
			});
		};
		
		/**********************************************************************/
		
        this.createFileField=function()
        {
			$self.e('input[type="file"]').each(function()
			{
				var field=$(this);
				
				$(this).fileupload(
				{
					url:$option.ajax_url,
					dataType:'json',
					formData:{'action':'chbs_file_upload'},
					done:function(e,data) 
					{
						$self.setFileUploadField(field,true,data.result);
					}
				});  

				$(this).parent('.chbs-file-upload').next('.chbs-file-remove').children('span:last-child').on('click',function(e)
				{
					e.preventDefault();
					$self.setFileUploadField(field,false,[]);
				});
			});
        };
		
		/**********************************************************************/
		
		this.setFileUploadField=function(object,upload,data)
		{
			var name=object.attr('name');
			var field=$self.e('input[name="'+name+'"]');
			
			if(parseInt(data.error,10)===1)
			{
				alert(data.message);
				return;
			}
			
			if(upload)
			{
				field.parent('.chbs-file-upload').addClass('chbs-hidden');
				field.parent('.chbs-file-upload').next('.chbs-file-remove').removeClass('chbs-hidden').find('span>span').html(data.name);
			}
			else
			{
				field.parent('.chbs-file-upload').removeClass('chbs-hidden');
				field.parent('.chbs-file-upload').next('.chbs-file-remove').addClass('chbs-hidden').find('span>span').html('');

				data={name:'',type:'',tmp_name:''};
			}

			$self.e('input[name="'+name+'_name"]').val(data.name);
			$self.e('input[name="'+name+'_type"]').val(data.type);
			$self.e('input[name="'+name+'_tmp_name"]').val(data.tmp_name);
		};
		
		/**********************************************************************/
		
		this.triggerOnSelectDatePicker=function(selector)
		{
			var trigger=true;
			
			if(selector==='[name="chbs_pickup_date_service_type_3"]')
			{
				trigger=false;
				var routeField=$self.e('select[name="chbs_route_service_type_3"]>option:selected');
			
				try
				{
			
					var pickupHour=JSON.parse(routeField.attr('data-pickup_hour'));
				}
				catch(e) {}
					
				var pickupTime=$self.e('[name="chbs_pickup_time_service_type_3"]').val();
					
				var dateField=$self.e('[name="chbs_pickup_date_service_type_3"]');
								
				try
				{
					var dayWeek=parseInt(dateField.datepicker('getDate').getDay(),10);

					$('.ui-timepicker-list>li').removeClass('ui-timepicker-disabled');

					if((pickupHour[dayWeek]!=undefined) && (pickupHour[dayWeek].hour!=undefined))
					{
						$('.ui-timepicker-list>li').addClass('ui-timepicker-disabled').each(function()
						{
							if($.inArray($(this).text(),pickupHour[dayWeek].hour)>-1)
							{
								$(this).removeClass('ui-timepicker-disabled');
							}
						});			

						if($.inArray(pickupTime,pickupHour[dayWeek].hour)==-1) trigger=true;
					}
				}
				catch(e) {}
			}
			
			if(trigger)
			{
				var inst=$.datepicker._getInst($(selector)[0]);
				$.datepicker._get(inst,'onSelect').apply(inst.input[0],[$(selector).datepicker('getDate'),inst]);
			}
		};
		
		/**********************************************************************/
		
		this.createSelectMenu=function(useDisable=true)
		{
			var selector='select';
			if(useDisable) selector+=':not(.chbs-selectmenu-disable)';
			
			$self.e(selector).selectmenu(
			{
				appendTo:$this,
				open:function(event,ui)
				{
					var select=$(this);
					var selectmenu=$('#'+select.attr('id')+'-menu').parent('div');
					
					var field=select.parents('.chbs-form-field:first');
					
					var left=parseInt(selectmenu.css('left'),10)-1;
					
					var borderWidth=parseInt(field.css('border-left-width'),10)+parseInt(field.css('border-right-width'),10);
					
					var width=field[0].getBoundingClientRect().width-borderWidth;
					
					selectmenu.css({width:width+2,left:left});
				},
				change:function(event,ui)
				{
					var name=$(this).attr('name');
										
					if(name==='chbs_waypoint_duration_service_type_1[]')
					{
						$self.reCalculateRoute();
					}
					
					if(name==='chbs_route_service_type_3')
					{
						$self.e('[name="chbs_pickup_date_service_type_3"]').val('');
						$self.e('[name="chbs_pickup_time_service_type_3"]').val('');

						$self.triggerOnSelectDatePicker('[name="chbs_pickup_date_service_type_3"]');
						
						$self.googleMapCreate();
						$self.googleMapCreateRoute();
					}
					
					if($.inArray(name,['chbs_transfer_type_service_type_1','chbs_transfer_type_service_type_3'])>-1)
					{
						var section=$self.e('[name="chbs_return_date_service_type_'+$self.getServiceTypeId()+'"]').parent('div').parent('div');
						
						if(parseInt($(this).val(),10)===3) section.removeClass('chbs-hidden');
						else section.addClass('chbs-hidden');
					}
					
					if($.inArray(name,['chbs_extra_time_service_type_1','chbs_transfer_type_service_type_1','chbs_duration_service_type_2','chbs_extra_time_service_type_3','chbs_transfer_type_service_type_3'])>-1)
					{
						$self.reCalculateRoute();
					}1
					
					if(name==='chbs_navigation_responsive')
					{
						var navigation=parseInt($(this).val(),10);
						
						var step=parseInt($self.e('input[name="chbs_step"]').val(),10);	
						
						$self.addGAEvent('navigation_top_select',{'step_current':step,'step_target':navigation});
				
						if(navigation-step===0) return;

						$self.goToStep(navigation-step);
					}
					
					if($.inArray(name,['chbs_vehicle_passenger_count','chbs_vehicle_bag_count','chbs_vehicle_standard','chbs_vehicle_category'])>-1)
					{
						$self.setAction('vehicle_filter');
						
						$self.e('.chbs-vehicle-list').children().addClass('chbs-hidden');
						$self.e('.chbs-vehicle-list').addClass('chbs-preloader-1');
						
						$self.post($self.e('form[name="chbs-form"]').serialize(),function(response)
						{	   
							$self.getGlobalNotice().addClass('chbs-hidden');
							
							var vehicleList=$self.e('.chbs-vehicle-list');
							
							vehicleList.html('').removeClass('chbs-preloader-1');
							
							if((typeof(response.error)!=='undefined') && (typeof(response.error.global)!=='undefined'))
							{
								$self.getGlobalNotice().removeClass('chbs-hidden').html(response.error.global[0].message);
							}
							else
							{
								vehicleList.html(response.html);
								$self.recalculateVehiclePrice(response,1);
							}
							
							$self.e('.chbs-vehicle-list').find('.chbs-button.chbs-button-style-2').removeClass('chbs-state-selected');
							
							$self.preloadVehicleImage();
							
							$self.e('input[name="chbs_vehicle_id"]').val(0);
							$self.createSummaryPriceElement();
						});
					}
					
					if($.inArray(name,['chbs_fixed_location_pickup_service_type_1','chbs_fixed_location_pickup_service_type_2']>-1))
					{
						$self.checkFixedLocationPickup(name);
					}					
					
					if($.inArray(name,['chbs_fixed_location_dropoff_service_type_1']>-1))
					{
						$self.checkFixedLocationPickup('chbs_fixed_location_pickup_service_type_1');
					}  
					
					if($.inArray(name,['chbs_fixed_location_dropoff_service_type_2']>-1))
					{
						$self.checkFixedLocationPickup('chbs_fixed_location_pickup_service_type_2');
					}
					
					if($.inArray(name,['chbs_fixed_location_pickup_service_type_1','chbs_fixed_location_dropoff_service_type_1','chbs_fixed_location_pickup_service_type_2','chbs_fixed_location_dropoff_service_type_3']>-1))
					{
						$self.googleMapSetAddress($(this),function()
						{
							$self.googleMapCreate();
							$self.googleMapCreateRoute();   
						});
					}
						
					/***/
				}
			});
						
			$self.e('.ui-selectmenu-button .ui-icon.ui-icon-triangle-1-s').attr('class','chbs-meta-icon-arrow-vertical-large'); 
			
			$self.checkFixedLocationPickup('chbs_fixed_location_pickup_service_type_1');
			$self.checkFixedLocationPickup('chbs_fixed_location_pickup_service_type_2');
			
			var fInedx=[1,3];
			
			for(var i in fInedx)
			{
				var transferType=$self.e('[name="chbs_transfer_type_service_type_'+fInedx[i]+'"]');
				if(transferType.length===1)
				{
					if(parseInt(transferType.val(),10)===3)
					{
						var returnDate=$self.e('[name="chbs_return_date_service_type_'+fInedx[i]+'"]');
						returnDate.parents('.chbs-form-field').parent('.chbs-clear-fix').removeClass('chbs-hidden');
					}
				}	
			}
		};
		
		/**********************************************************************/
			
		this.getAutocompleteSource=function(item)
		{
			var source=[];
			$(item).next('select').find('option:not([disabled="disabled"])').each(function(index2,item2)
			{
				source.push({label:item2.text,value:item2.value});
			});	  
		   
			return(source);
		};
		
		/**********************************************************************/
		
		this.createAutocomplete=function(selector)
		{
			$self.e(selector).each(function(index,item)
			{
				var source=$self.getAutocompleteSource(item);
				if(source.length)
				{
					$(item).autocomplete(
					{
						appendTo:$this,
						source:source,
						minLength:0,
						focus:function(event,ui)
						{
							event.preventDefault();
						},
						select:$self.handleAutocompleteChange,
						change:$self.handleAutocompleteChange,
						create:function(event,ui)
						{
							var helper=new CHBSHelper();
							
							var select=$(event.target).next('select');
							
							var selectedId=select.children('option[selected="selected"]').val();
							
							var label=select.children('option[value="'+parseInt(selectedId,10)+'"]').text();
							
							if(!helper.isEmpty(label)) $(event.target).val(label);
						}
					}).focus(function()
					{
						$(this).autocomplete('search');
					});
					
					$.ui.autocomplete.filter=function(array,term)
					{
						var matcher=new RegExp($.ui.autocomplete.escapeRegex(term),'i');
						return $.grep(array,function(value) 
						{
							return(matcher.test(value.label || value.value || value));
						});
					};
				}
			});
		};
		
		/**********************************************************************/
		
		this.handleAutocompleteChange=function(event,ui)
		{		
			event.preventDefault();
			
			var $select=$(event.target).next('select'),name=$select.attr('name');

			if(ui.item==null)
			{
				$(event.target).val('');
				$select.val('');
			}
			else
			{
				$(event.target).val(ui.item.label);
				$select.val(ui.item.value);
			}
						
			if($.inArray(name,['chbs_fixed_location_pickup_service_type_1','chbs_fixed_location_dropoff_service_type_1','chbs_fixed_location_pickup_service_type_2','chbs_fixed_location_dropoff_service_type_3']>-1))
			{
				$self.googleMapSetAddress($select,function()
				{
					$self.googleMapCreate();
					$self.googleMapCreateRoute(); 
				});
			}

			if($.inArray(name,['chbs_fixed_location_pickup_service_type_1','chbs_fixed_location_pickup_service_type_2']>-1))
			{
				$self.checkFixedLocationPickup(name);
			}					

			if($.inArray(name,['chbs_fixed_location_dropoff_service_type_1']>-1))
			{
				$self.checkFixedLocationPickup('chbs_fixed_location_pickup_service_type_1');
			}  

			if($.inArray(name,['chbs_fixed_location_dropoff_service_type_2']>-1))
			{
				$self.checkFixedLocationPickup('chbs_fixed_location_pickup_service_type_2');
			}
			
			return(true);
		};
		
		/**********************************************************************/
		
		this.checkFixedLocationPickup=function(pickupLocationFieldName)
		{
			var dropoffLocationFieldName=pickupLocationFieldName.replace('pickup','dropoff');
			
			var dropoffLocationField=$self.e('[name="'+dropoffLocationFieldName+'"]');
			
			dropoffLocationField.children('option').removeAttr('disabled');
			
			try
			{
				dropoffLocationField.selectmenu('refresh');
			}
			catch(e) {}
			
			/***/
			
			var dataPickupLocation=$self.e('select[name="'+pickupLocationFieldName+'"]').children('option:selected').attr('data-location');
			if(typeof(dataPickupLocation)=='undefined') return;
			
			dataPickupLocation=JSON.parse(dataPickupLocation);
			
			if(!dataPickupLocation.dropoff_disable.length) return;
			
			for(var i in dataPickupLocation.dropoff_disable)
				dropoffLocationField.children('option[value="'+dataPickupLocation.dropoff_disable[i]+'"]').attr('disabled','disabled').removeAttr('selected');
			
			try
			{
				dropoffLocationField.selectmenu('refresh');
			}
			catch(e) {}
			
			$self.e('.chbs-form-field-location-fixed-autocomplete').each(function(index,item)
			{
				var fixedLocation=$self.getAutocompleteSource(item);
				$(item).autocomplete({source:fixedLocation});
			});
		};
		
		/**********************************************************************/
		/**********************************************************************/
		
		this.setMainNavigation=function()
		{
			var step=parseInt($self.e('input[name="chbs_step"]').val(),10);
	 
			var element=$self.e('.chbs-main-navigation-default').find('li');
			
			element.removeClass('chbs-state-selected').removeClass('chbs-state-completed');
			
			element.filter('[data-step="'+step+'"]').addClass('chbs-state-selected');

			var i=0;
			element.each(function()
			{
				if((++i)>=step) return;
				
				$(this).addClass('chbs-state-completed');
			});
		};
		
		/**********************************************************************/
		
		this.getServiceTypeId=function()
		{
			return(parseInt($self.e('.ui-tabs .ui-tabs-active').attr('data-id'),10));
		};
		
		/**********************************************************************/
		
		this.setServiceTypeId=function(serviceTypeId)
		{
			$self.e('input[name="chbs_service_type_id"]').val(serviceTypeId);
		};
		
		/**********************************************************************/
		/**********************************************************************/

		this.setAction=function(name)
		{
			$self.e('input[name="action"]').val('chbs_'+name);
		};

		/**********************************************************************/
		
		this.e=function(selector)
		{
			return($this.find(selector));
		};

		/**********************************************************************/

		this.recalculateVehiclePrice=function(response,previousStep)
		{
			if(((parseInt(response.booking_summary_hide_fee,10)===1) || (parseInt(response.booking_summary_hide_fee,10)===2)) && (parseInt(previousStep,10)===1))
			{
				var vehicle=[];

				$self.e('.chbs-vehicle-list>ul>li').each(function()
				{
					var helper=new CHBSHelper();
					var parent=$(this).children('div:first');

					if((!helper.isEmpty(parent.attr('data-base_location_cooridnate_lat'))) && (!helper.isEmpty(parent.attr('data-base_location_cooridnate_lng'))))
						vehicle.push({id:parent.attr('data-id'),lat:parent.attr('data-base_location_cooridnate_lat'),lng:parent.attr('data-base_location_cooridnate_lng')});
				});

				if(vehicle.length)
				{
					$self.e('.chbs-vehicle-list').children().addClass('chbs-hidden');
					$self.e('.chbs-vehicle-list').addClass('chbs-preloader-1');
					
					var j=0;
					for(var i in vehicle)
					{
						$self.calculateBaseLocationDistance(function(baseLocationData)
						{
							j++;

							var vehicleElement=$self.e('.chbs-vehicle-list .chbs-vehicle[data-id="'+baseLocationData.id+'"]');

							vehicleElement.find('[name="chbs_base_location_vehicle_distance['+baseLocationData.id+']"]').val(baseLocationData.distance);
							vehicleElement.find('[name="chbs_base_location_vehicle_return_distance['+baseLocationData.id+']"]').val(baseLocationData.return_distance);

							if(j===vehicle.length)
							{
								$self.goToStep(0);
								return;
							}

						},vehicle[i]);
					}
				}
			}		 
		};

		/**********************************************************************/
		
		this.goToStep=function(stepDelta,callback,useRecaptcha=true,useScrollToTop=true,usePreloader=true)
		{   
			if(usePreloader)
			{
				$self.preloader(true);
			}
			
			var step=$self.e('input[name="chbs_step"]');
			var stepRequest=$self.e('input[name="chbs_step_request"]');
			
			if((parseInt($option.recaptcha.enable,10)===1) && ($self.getVehiclePriceCalculationStepFirst()===0))
			{
				var s=parseInt(step.val(),10)+stepDelta;
			
				if(s>=1)
				{
					if(useRecaptcha)
					{
						var grc=parseInt($option.recaptcha.api_type,10)===1 ? grecaptcha : grecaptcha.enterprise;
						
						grc.ready(function() 
						{
							grc.execute($option.recaptcha.site_key).then(function(token)
							{
								$self.e('[name="chbs_recaptcha_token"]').val(token);
								$self.goToStep(stepDelta,callback,false);
							});
						});
						
						return;
					}
				}
			}	
			
			$self.setAction('go_to_step');
			
			$self.setServiceTypeId($self.getServiceTypeId());
			
			stepRequest.val(parseInt(step.val(),10)+stepDelta);
			
			/***/
			
			var serviceTypeId=$self.getServiceTypeId();
			
			if(parseInt(stepRequest.val(),10)===2)
			{
				var sum=parseInt($self.e('[name="chbs_passenger_adult_service_type_'+serviceTypeId+'"]').val(),10)+parseInt($self.e('[name="chbs_passenger_children_service_type_'+serviceTypeId+'"]').val(),10);
				if(sum>0) $self.e('[name="chbs_vehicle_passenger_count"]').val(sum);
			}
			
			if(parseInt(stepRequest.val(),10)===3)
			{
				if($iti)
				{
					var field=$self.e('input[name="chbs_client_contact_detail_phone_number"]');
					var number=$iti.getNumber();

					field.val(number);
				}
			}
			
			$self.post($self.e('form[name="chbs-form"]').serialize(),function(response)
			{   
				var previousStep=$self.e('input[name="chbs_step"]').val();
				
				if($self.getVehiclePriceCalculationStepFirst()!==0)
				{	
					response.step=1;
					
					if((typeof(response.vehicle_price_calculation_step_first)!=='undefined') && (typeof(response.vehicle_price_calculation_step_first.message)!=='undefined')  && (parseInt(response.vehicle_price_calculation_step_first.show,10)===1))
					{
						$self.e('.chbs-notice-fixed').removeClass('chbs-hidden');
						$self.e('.chbs-notice-fixed span:first').html(response.vehicle_price_calculation_step_first.message);
					}
					else
					{
						$self.e('.chbs-notice-fixed').addClass('chbs-hidden');
					}
					
					if(typeof(callback)!=='undefined') callback();
					
					return;
				}
				
				$self.e('.chbs-notice-fixed').addClass('chbs-hidden');
				
				response.step=parseInt(response.step,10);
			
				$self.getGlobalNotice().addClass('chbs-hidden');
					
				$self.e('.chbs-main-content>div').css('display','none');
				$self.e('.chbs-main-content>div:eq('+(response.step-1)+')').css('display','block');
				
				$self.e('input[name="chbs_step"]').val(response.step);			

				$self.setMainNavigation();
				
				$self.googleMapDuplicate(-1);
				
				google.maps.event.trigger($googleMap,'resize');
				
				$self.e('select[name="chbs_navigation_responsive"]').val(response.step);
				$self.e('select[name="chbs_navigation_responsive"]').selectmenu('refresh');
				  
				if(parseInt(response.step,10)===1)
					$self.googleMapStartCustomizeHeight();
				else $self.googleMapStopCustomizeHeight();
			 
				switch(parseInt(response.step,10))
				{
					case 2:

						if(typeof(response.redirect_to_url_address)!=='undefined')
						{
							$('body').css({'display':'none'});
							window.location.href=response.redirect_to_url_address;
							break;
						}

						$self.e('.chbs-vehicle-filter>.chbs-form-field>select[name="chbs_vehicle_passenger_count"]').replaceWith(response.vehicle_passenger_filter_field);

						if(typeof(response.vehicle)!=='undefined')
						{
							$self.e('.chbs-vehicle-list').removeClass('chbs-preloader-1');
							$self.e('.chbs-vehicle-list').html(response.vehicle);

							$self.recalculateVehiclePrice(response,previousStep);
						}

						if(typeof(response.booking_extra)!=='undefined')
							$self.e('.chbs-booking-extra').html(response.booking_extra);

						if(typeof(response.summary)!=='undefined')
							$self.e('.chbs-main-content-step-2>.chbs-layout-25x75 .chbs-layout-column-left:first').html(response.summary[0]);

						if((typeof(response.client_form_sign_in)!=='undefined') && (typeof(response.client_form_sign_up)!=='undefined'))
						{
							$self.e('.chbs-main-content-step-2 .chbs-client-form').html('');

							if(typeof(response.client_form_sign_in)!=='undefined')
								$self.e('.chbs-main-content-step-2 .chbs-client-form').prepend(response.client_form_sign_in);

							if(typeof(response.client_form_sign_up)!=='undefined')
								$self.e('.chbs-main-content-step-2 .chbs-client-form').append(response.client_form_sign_up);

							if(typeof(response.payment_form)!=='undefined')
								$self.e('.chbs-main-content-step-2 .chbs-payment-form').html(response.payment_form);
						}

						$self.e('.chbs-agreement-header,.chbs-agreement').remove();

						if(typeof(response.agreement_html)!=='undefined')
						{
							$self.e('.chbs-main-content-step-2 .chbs-payment-form').after(response.agreement_html);
						}

						$self.setPayment();
						$self.createFileField();
						$self.preloadVehicleImage();
						$self.createVehicleGallery();

						$self.createSelectMenu();

						$self.createAutocomplete('.chbs-form-field-location-fixed-autocomplete');
						$self.createAutocomplete('.chbs-form-field-route-autocomplete');

						$self.createFormElementFieldDatePicker();
						$self.createFormElementFieldTimePicker();
						$self.createFormElementFieldAutocomplete();

						$self.manageBookingExtra();

					break;

					case 3:
						// Thank you / payment confirmation step
					break;
				}
				
				$self.createPhoneField();
				$self.createLabelTooltip();
				
				$self.createStickySidebar();
				
				if($.inArray(response.step,[2])>-1) 
				{
					$self.createCurrencySwitcher();
				}
				
				$(window).scroll();
				
				if(false)   
					$self.googleMapDuplicate(response.step);
				
				$('.qtip').remove();
				
				if(typeof(response.error)!=='undefined')
				{
					if(typeof(response.error.global[0])!=='undefined')
						$self.getGlobalNotice().removeClass('chbs-hidden').html(response.error.global[0].message);
					
					if(typeof(response.error.local)!=='undefined')
					{
						for(var index in response.error.local)
						{
							var selector,object;
							
							var sName=response.error.local[index].field.split('-');

							if(isNaN(sName[1])) selector='[name="'+sName[0]+'"]:eq(0)';
							else selector='[name="'+sName[0]+'[]"]:eq('+sName[1]+')';									
									
							object=$self.e(selector).prevAll('label');
								 
							object.qtip(
							{
								show:
								{ 
									target:$(this) 
								},
								style:
								{ 
									classes:(response.error===1 ? 'chbs-qtip chbs-qtip-error' : 'chbs-qtip chbs-qtip-success')
								},
								content:
								{ 
									text:response.error.local[index].message 
								},
								position:
								{ 
									my:($option.rtl_mode ? 'bottom right' : 'bottom left'),
									at:($option.rtl_mode ? 'top right' : 'top left'),
									container:object.parents('[name="chbs-form"]')
								}
							}).qtip('show');	
						}
					}
				}
				
				if(parseInt(response.step,10)===3)
				{
					$self.addGAEvent('booking_send',{'booking_id':response.booking_id});
					
					$self.e('.chbs-main-navigation-default').addClass('chbs-hidden');
					$self.e('.chbs-main-navigation-responsive').addClass('chbs-hidden');
					
					if(typeof(response.error)!=='undefined')
					{
						$self.getGlobalNotice().removeClass('chbs-hidden').html(response.error.global[0].message);	
					}
					else
					{
						var helper=new CHBSHelper();
						
						if((typeof(response.redirect_to_woocommerce_cart_enable)!=='undefined') && (parseInt(response.redirect_to_woocommerce_cart_enable,10)===1))
						{
							$('body').css('display','none');
							window.location.href=response.redirect_to_woocommerce_cart_url_address;
						}
						
						if(parseInt(response.payment_id,10)>0)
						{
							if(!helper.isEmpty(response.payment_info))
								$self.e('.chbs-booking-complete-payment-'+response.payment_prefix).append('<p>'+response.payment_info+'</p>');
						}

						switch(parseInt(response.payment_id,10))
						{
							case -2:
								
								if(!helper.isEmpty(response.payment_disable_success_url_address))
								{
									$('body').css('display','none');
									window.location.href=response.payment_disable_success_url_address;									
								}

								$self.e('.chbs-booking-complete-payment-disable').css('display','block');
							
							break;
							
							case -1:
								
								if(parseInt(response.thank_you_page_enable,10)!==1)
								{
									$('body').css('display','none');
									window.location.href=response.payment_url;
								}
								else
								{
									$self.e('.chbs-booking-complete-payment-woocommerce').css('display','block');
									$self.e('.chbs-booking-complete-payment-woocommerce>a').attr('href',response.payment_url);
								}

							break;

							case 1:

								if(!helper.isEmpty(response.payment_cash_success_url_address))
								{
									$('body').css('display','none');
									window.location.href=response.payment_cash_success_url_address;									
								}

								$self.e('.chbs-booking-complete-payment-cash').css('display','block');

							break;

							case 2:

								$('body').css('display','none');

								$.getScript('https://js.stripe.com/v3/',function() 
								{								
									var stripe=Stripe(response.stripe_publishable_key);
									var section=$self.e('.chbs-booking-complete-payment-stripe');

									$self.e('.chbs-booking-complete').on('click','.chbs-booking-complete-payment-stripe a',function(e)
									{
										e.preventDefault();

										stripe.redirectToCheckout(
										{
											sessionId:response.stripe_session_id
										}).then(function(result) 
										{

										});
									});

									var counter=parseInt(response.stripe_redirect_duration,10);

									if(counter<=0)
									{
										section.find('a').trigger('click');
									}
									else
									{
										$('body').css('display','block');

										section.css('display','block');

										var interval=setInterval(function()
										{
											counter--;
											section.find('div>span').html(counter);

											if(counter===0)
											{
												clearInterval(interval);
												section.find('a').trigger('click');
											}

										},1000);  
									}
								});

							break;

							case 3:

								$('body').css('display','none');

								var section=$self.e('.chbs-booking-complete-payment-paypal');

								$self.e('.chbs-booking-complete').on('click','.chbs-booking-complete-payment-paypal a',function(e)
								{
									e.preventDefault();

									var form=$self.e('form[name="chbs-form-paypal"]');

									for(var i in response.form)
										form.find('input[name="'+i+'"]').val(response.form[i]);

									form.submit();
								});

								var counter=parseInt(response.payment_paypal_redirect_duration,10);

								if(counter<=0)
								{
									section.find('a').trigger('click');
								}
								else
								{
									$('body').css('display','block');

									section.css('display','block');

									var interval=setInterval(function()
									{
										counter--;
										section.find('div>span').html(counter);

										if(counter===0)
										{
											clearInterval(interval);
											section.find('a').trigger('click');
										}

									},1000);  
								}

							break;
							
							case 6:

								$('body').css('display','none');

								var tpaySection=$self.e('.chbs-booking-complete-payment-tpay');

								$self.e('.chbs-booking-complete').on('click','.chbs-booking-complete-payment-tpay a',function(e)
								{
									e.preventDefault();
									window.location.href=response.payment_tpay_redirect_url;
								});

								var tpayCounter=parseInt(response.payment_tpay_redirect_duration,10);

								if(tpayCounter<=0)
								{
									tpaySection.find('a').trigger('click');
								}
								else
								{
									$('body').css('display','block');

									tpaySection.css('display','block');

									var tpayInterval=setInterval(function()
									{
										tpayCounter--;
										tpaySection.find('div>span').html(tpayCounter);

										if(tpayCounter===0)
										{
											clearInterval(tpayInterval);
											tpaySection.find('a').trigger('click');
										}

									},1000);
								}

							break;

							case 4:
								
								if(!helper.isEmpty(response.payment_wire_transfer_success_url_address))
								{
									$('body').css('display','none');
									window.location.href=response.payment_wire_transfer_success_url_address;									
								}

								$self.e('.chbs-booking-complete-payment-wire-transfer').css('display','block');

							break;

							case 5:

								if(!helper.isEmpty(response.payment_credit_card_pickup_success_url_address))
								{
									$('body').css('display','none');
									window.location.href=response.payment_credit_card_pickup_success_url_address;									
								}

								$self.e('.chbs-booking-complete-payment-credit-card-pickup').css('display','block');

							break;
							
							default:
							
								if(!helper.isEmpty(response.callback_function))
								{
									var object=new window[response.callback_object];
									object[response.callback_function](response,$this);
								}
						}
					}
				}
				
				if(useScrollToTop)
				{
					var offset=20;

					if($('#wpadminbar').length===1)
						offset+=$('#wpadminbar').height();

					$.scrollTo($this,{offset:-1*offset});
				}
			
				if(typeof(callback)!=='undefined') callback();
			
				if(usePreloader)
				{
					$self.preloader(false);
				}
			});
		};
		
		/**********************************************************************/
		
		this.createPhoneField=function()
		{
			var field=$self.e('input[name="chbs_client_contact_detail_phone_number"]');
			var fieldPlaceholder=$self.e('input[name="chbs_client_contact_detail_phone_number_placeholder"]');
			
			if(parseInt(fieldPlaceholder.length,10)===1)
			{
				if(parseInt($option.phone_number_iti_library_enable,10)===1) 
				{
					$iti=window.intlTelInput(fieldPlaceholder[0],
					{
						initialCountry:$option.client_country_code,
						separateDialCode:true
					});

					$iti.setNumber(field.val());

					fieldPlaceholder[0].addEventListener('countrychange',function()
					{
						var number=$iti.getNumber();
						field.val(number);
					});
				}
				else
				{
					fieldPlaceholder.on('change',function()
					{
						var number=fieldPlaceholder.val();
						field.val(number);
					});		
				}
				
				fieldPlaceholder.trigger('change');
			}
		};
		
		/**********************************************************************/
		
		this.manageBookingExtra=function()
		{
			var bookingExtraList=$self.e('.chbs-booking-extra-list');
			var bookingExtraCategoryList=$self.e('.chbs-booking-extra-category-list');
			
			if(bookingExtraList.length!==1) return;
						
			if(parseInt(bookingExtraCategoryList.length)===1)
			{
				bookingExtraList.addClass('chbs-hidden');

				bookingExtraCategoryList.find('>div>a').off('click');

				bookingExtraCategoryList.find('>div>a').on('click',function(e)
				{
					e.preventDefault();

					var categoryId=$(this).attr('data-category_id');

					bookingExtraList=$self.e('.chbs-booking-extra-list');

					if($(this).next('.chbs-booking-extra-list').length===1)
					{
						if(!$(this).next('.chbs-booking-extra-list').hasClass('chbs-hidden'))
						{
							$(this).next('.chbs-booking-extra-list').addClass('chbs-hidden');
							return;
						}
					}

					bookingExtraList.find('li').each(function()
					{
						if($.inArray(categoryId,$(this).attr('data-category_id').split(','))>-1)
							$(this).removeClass('chbs-hidden-by-category');
						else $(this).addClass('chbs-hidden-by-category');
					});

					$(this).after(bookingExtraList);

					bookingExtraList.removeClass('chbs-hidden');
				});
			}
			
			var vehicleIdSelected=$self.e('[name="chbs_vehicle_id"]').val();
			
			bookingExtraList=$self.e('.chbs-booking-extra-list');
			
			bookingExtraList.find('li:not(.chbs-hidden-by-category)').each(function()
			{
				var vehicleId=$(this).attr('data-vehicle_id').split(',');
				if(($.inArray('-1',vehicleId)>-1) || ($.inArray(vehicleIdSelected,vehicleId)>-1))
				{
					$(this).removeClass('chbs-hidden');
				}
				else
				{
					$(this).addClass('chbs-hidden');
					$(this).find('.chbs-button.chbs-button-style-2').removeClass('.chbs-state-selected');
				}
			});
			
			
			var data=$self.getSelectedBookingExtra(false);
						
			var t=[data.join(','),$self.e('input[name="chbs_booking_extra_id"]').val()];
			
			if(new String(t[0])!==new String(t[1]))
			{
				$self.e('input[name="chbs_booking_extra_id"]').val(t[0]);
				$self.createSummaryPriceElement();
			}
		};
		
		/**********************************************************************/
		
		this.getSelectedBookingExtra=function(set=true)
		{
			var data=[];
			
			var bookingExtraList=$self.e('.chbs-booking-extra-list');

			var vehicleIdSelected=parseInt($self.e('[name="chbs_vehicle_id"]').val(),10);
			
			bookingExtraList.find('.chbs-button.chbs-button-style-2').each(function()
			{
				var vehicleIdBookingExtra=$(this).parents('li:first').attr('data-vehicle_id').split(',').map(function(val) { return parseInt(val,10); });
				
				if($(this).hasClass('chbs-state-selected'))
				{
					if($.inArray(-1,vehicleIdBookingExtra)>-1)
					{
						data.push($(this).attr('data-value'));
					}
					else if(vehicleIdSelected>0)
					{
						if($.inArray(vehicleIdSelected,vehicleIdBookingExtra)>-1)
						{
							data.push($(this).attr('data-value'));
						}
					}
				}
			});
			
			if(set) 
			{	
				$self.e('input[name="chbs_booking_extra_id"]').val(data.join(','));
			}
			
			return(data);
		};

		/**********************************************************************/
		
		this.post=function(data,callback)
		{
			$.post($option.ajax_url,data,function(response)
			{ 
				callback(response); 
			},'json');
		};	
		
		/**********************************************************************/
		
		this.preloader=function(action,forcePreloader=false)
		{
			if(!forcePreloader)
			{
				if(parseInt($option.form_preloader_enable,10)!==1) return;
				if($self.getVehiclePriceCalculationStepFirst()!==0) return;
			}
		
			$self.e('#chbs-preloader').css('display',(action ? 'block' : 'none'));
		};
		
		/**********************************************************************/
		
		this.preloadVehicleImage=function()
		{
			try
			{
				$self.e('.chbs-vehicle-list .chbs-vehicle-image img').one('load',function()
				{
					$(this).parent('.chbs-vehicle-image').animate({'opacity':1},300);
				}).each(function() 
				{
					if(this.complete) $(this).load();
				});				
			}
			catch(e) {}
		};
		
		/**********************************************************************/
		
		this.createVehicleGallery=function()
		{
			$self.e('.chbs-main-content-step-2').on('click','.chbs-vehicle-list .chbs-vehicle-image img',function(e)
			{
				e.preventDefault();
				
				var gallery=$(this).parents('.chbs-vehicle-image:first').nextAll('.chbs-vehicle-gallery');
				
				if(parseInt(gallery.length,10)===1)
				{
					$.fancybox({'type': 'iframe','scrolling': 'no'});
					
					$.fancybox.open(gallery.find('img'));
				}
			});
		};
		
		/**********************************************************************/
		/**********************************************************************/
	   
		this.googleMapStartCustomizeHeight=function()
		{
			if(parseInt($option.widget.mode,10)===1) return;
			if(parseInt($self.e('input[name="chbs_step"]').val(),10)!==1) return;
			
			if($googleMapHeightInterval>0) return;
			
			$googleMapHeightInterval=window.setInterval(function()
			{
				$self.googleMapCustomizeHeight();
			},500);
		};
		
		/**********************************************************************/
	   
		this.googleMapStopCustomizeHeight=function()
		{
			if(parseInt($option.widget.mode,10)===1) return;
			if(parseInt($self.e('input[name="chbs_step"]').val(),10)!==1) return;
			
			clearInterval($googleMapHeightInterval);
			$self.e('#chbs_google_map').height('420px');
			
			$googleMapHeightInterval=0;
		};		
		
		/**********************************************************************/
	   
		this.googleMapCustomizeHeight=function()
		{
			if(parseInt($option.widget.mode,10)===1) return;
			
			if(parseInt($self.e('input[name="chbs_step"]').val(),10)!==1) return;
			
			var rideInfo=$self.e('.chbs-ride-info');
			var columnLeft=$self.e('.chbs-main-content-step-1>.chbs-layout-50x50>.chbs-layout-column-left');

			var helper=new CHBSHelper();
			
			if(helper.isMobile())
			{
				$self.e('#chbs_google_map').height('420px');
			}
			else
			{
				$self.e('#chbs_google_map').height(parseInt(columnLeft.actual('height'),10)-parseInt(rideInfo.actual('height'),10));
			}
			
			google.maps.event.trigger($googleMap,'resize');
		};
	   
		/**********************************************************************/
		/**********************************************************************/
	   
		this.googleMapDuplicate=function(step)
		{
			if(step===4)
			{
				var map=$self.e('.chbs-google-map>#chbs_google_map');
				if(map.children('div').length)
					$self.e('.chbs-google-map-summary').append(map);		
				
				$self.e('#chbs_google_map').height('420px');
			}
			else
			{
				var map=$self.e('.chbs-google-map-summary>#chbs_google_map');
				if(map.children('div').length)
					$self.e('.chbs-google-map').append(map);
			}
			
			for(var i in $marker)
				$marker[i].setMap($googleMap);
			
			var bound=new google.maps.LatLngBounds();
			for(var i in $marker)
				bound.extend($marker[i].position);
			$googleMap.fitBounds(bound);
			
			google.maps.event.trigger($googleMap,'resize');
		};
		
		/**********************************************************************/
		
		this.googleMapSetAddress=function(field,callback,setField=false)
		{
			var coordinate;
			var helper=new CHBSHelper();
			
			if(field.prop('tagName').toLowerCase()==='select')
			{
				callback();
				return;
			}
			else coordinate=JSON.parse(field.val());
			
			if((helper.isEmpty(coordinate.lat)) || (helper.isEmpty(coordinate.lng))) return;
			
			var geocoder=new google.maps.Geocoder;
			
			geocoder.geocode({'location':new google.maps.LatLng(coordinate.lat,coordinate.lng)},function(result,status) 
			{
				if((status==='OK') && (result[0]))
				{
					coordinate.address=$self.removeDoubleQuote(result[0].formatted_address);
						
					if(setField)
					{
                        var textField=field.parent('.chbs-form-field-location-autocomplete').children('input[type="text"]');
                        if(textField.length===1) textField.val(coordinate.address);						
					}
					
					field.val(JSON.stringify(coordinate));
					callback();
				}
			});			
		};
		
		/**********************************************************************/
		
		this.isPointInCircle=function(point,pointCenter,radius)
		{
			return(google.maps.geometry.spherical.computeDistanceBetween(point,pointCenter)<=radius);
		}
		
		/**********************************************************************/
		
		this.getGoogleMapAutocompleteOption=function(fieldName,reset=0)
		{
			var option={};
			var setting={};
			var helper=new CHBSHelper();
			
			var fieldType,fieldTypeReverse;
			
			/***/
		
			if(parseInt($option.driving_zone.location_field_relation_type,10)!==2)
			{
				reset=0;
			}
			
			/***/
			
			if(fieldName.indexOf('pickup')>-1)
			{
				fieldType='pickup';
				fieldTypeReverse='dropoff';
				setting=$option.driving_zone.pickup;
			}
			if(fieldName.indexOf('dropoff')>-1)
			{
				fieldType='dropoff';
				fieldTypeReverse='pickup';
				setting=$option.driving_zone.dropoff;
			}
			if(fieldName.indexOf('waypoint')>-1)
			{
				fieldType='waypoint';
				fieldTypeReverse='waypoint';
				setting=$option.driving_zone.waypoint;
			}
			
			/***/
			
			option.bounds=null;
			option.strictBounds=false;
			
			option.componentRestrictions={};
			option.componentRestrictions.country=[];
			
			option.fieldName=fieldName;
			option.fieldNameCoordinate=fieldName.replace('location','location_coordinate');
			
			option.fieldNameReverse=fieldName.replace(fieldType,fieldTypeReverse);
			option.fieldNameReverseCoordinate=option.fieldNameReverse.replace('location','location_coordinate');
			
			option.fieldType=fieldType;
			option.fieldTypeReverse=fieldTypeReverse;
			
			option.restrictionType=0;
			
			option.fields=['address_components','geometry'];
			
			/***/
			
			if((parseInt(setting.enable,10)===1) && (parseInt(reset,10)===0))
			{
				if((!helper.isEmpty(setting.area.coordinate.lat)) && (!helper.isEmpty(setting.area.coordinate.lat)) && (parseInt(setting.area.radius,10)>=0))
				{
					var circle=new google.maps.Circle(
					{
						center:new google.maps.LatLng(setting.area.coordinate.lat,setting.area.coordinate.lng),
						radius:setting.area.radius*1000
					});

					option.circle=circle;

					option.strictBounds=true;
					option.bounds=circle.getBounds();
					
					option.restrictionType=1;
				}

				if(setting.country.length)
				{
					if($.inArray(-1,setting.country)===-1)
					{
						option.componentRestrictions={};
						option.componentRestrictions.country=setting.country;
						
						option.restrictionType+=2;
					}
				}
			}	
			
			/***/
			
			return(option);
		};
		
		/**********************************************************************/
		
		this.setGoogleMapAutocompleteOption=function(fieldName)
		{
			if(parseInt($option.driving_zone.location_field_relation_type,10)!==2) return;
			
			var Helper=new CHBSHelper();
				
			var fieldOption=$self.getGoogleMapAutocompleteOption(fieldName);
			
			var fieldReverseOption=$self.getGoogleMapAutocompleteOption(fieldOption.fieldNameReverse);
			
			var field=$self.e('[name="'+fieldName+'"]');
			var fieldReverse=$self.e('[name="'+fieldOption.fieldNameReverse+'"]');
			
			if((Helper.isEmpty(field.val())) && (Helper.isEmpty(fieldReverse.val())))
			{
				if(fieldName in $autocomplete)
				{	
					$autocomplete[fieldName].setOptions($self.getGoogleMapAutocompleteOption(fieldName,1));
				}
				if(fieldOption.fieldNameReverse in $autocomplete)
				{	
					$autocomplete[fieldOption.fieldNameReverse].setOptions($self.getGoogleMapAutocompleteOption(fieldOption.fieldNameReverse,1));
				}
			}
			else if((!Helper.isEmpty(field.val())) && (Helper.isEmpty(fieldReverse.val())))
			{
				var fieldCoordinate=JSON.parse($self.e('[name="'+fieldOption.fieldNameCoordinate+'"').val());
				
				var r1=true;
				var r2=true;
				
				if(fieldOption.restrictionType==1 || fieldOption.restrictionType==3)
					r1=$self.isPointInCircle(new google.maps.LatLng(fieldCoordinate.lat,fieldCoordinate.lng),fieldOption.circle.center,fieldOption.circle.radius);
				if(fieldOption.restrictionType==2 || fieldOption.restrictionType==3)
				{
					if($.inArray(fieldCoordinate.country_code,fieldOption.componentRestrictions.country)==-1) r2=false;
				}
							
				if(fieldOption.fieldNameReverse in $autocomplete)
				{	
					$autocomplete[fieldOption.fieldNameReverse].setOptions($self.getGoogleMapAutocompleteOption(fieldOption.fieldNameReverse,(r1 && r2 ? 1 : 0)));
				}
		
				if(fieldOption.fieldName in $autocomplete)
				{	
					$autocomplete[fieldOption.fieldName].setOptions($self.getGoogleMapAutocompleteOption(fieldOption.fieldName,0));
				}
			}
			else if((Helper.isEmpty(field.val())) && (!Helper.isEmpty(fieldReverse.val())))
			{
				var fieldCoordinate=JSON.parse($self.e('[name="'+fieldReverseOption.fieldNameCoordinate+'"').val());
				
				var r1=true;
				var r2=true;
				
				if(fieldReverseOption.restrictionType==1 || fieldReverseOption.restrictionType==3)
					r1=$self.isPointInCircle(new google.maps.LatLng(fieldCoordinate.lat,fieldCoordinate.lng),fieldReverseOption.circle.center,fieldReverseOption.circle.radius);
				if(fieldReverseOption.restrictionType==2 || fieldReverseOption.restrictionType==3)
				{
					if($.inArray(fieldCoordinate.country_code,fieldReverseOption.componentRestrictions.country)==-1) r2=false;
				}
				
				if(fieldOption.fieldName in $autocomplete)
				{	
					$autocomplete[fieldOption.fieldName].setOptions($self.getGoogleMapAutocompleteOption(fieldOption.fieldName,(r1 && r2 ? 1 : 0)));
				}
				
				if(fieldOption.fieldNameReverse in $autocomplete)
				{	
					$autocomplete[fieldOption.fieldNameReverse].setOptions($self.getGoogleMapAutocompleteOption(fieldOption.fieldNameReverse,0));
				}
			}
		};
		
		/**********************************************************************/
		
		this.googleMapAutocompleteCreate=function(text)
		{
			if(text.is('[readonly]')) return;
			
			var id='chbs_location_'+(new CHBSHelper()).getRandomString(16);
				
			text.attr('id',id).on('keypress',function(e)
			{
				if(e.which===13)
				{
					e.preventDefault();
					return(false);
				}
			});
			
			var event='change';
			
			if(parseInt($option.driving_zone.location_field_relation_type,10)===2)
				event+=' blur';
			
			text.on(event,function()
			{
				if(!$.trim($(this).val()).length)
				{
					var fieldName=new String(text.attr('name'));
					var hiddenField=text.siblings('input[type="hidden"]');

					hiddenField.val('');

					$self.resetLocationSelection(fieldName,text,hiddenField,{resetCountry:true,clearRoute:true,clearPickupTime:true});
					
					$self.googleMapCreate();
					$self.googleMapCreateRoute();					
				}
			});

			text.on('input',function()
			{
				$self.clearFieldError(text);
			});

			var fieldName=new String(text.attr('name'));
			var option=$self.getGoogleMapAutocompleteOption(fieldName,1);

			$autocomplete[fieldName]=new google.maps.places.Autocomplete(document.getElementById(id),option);
			$autocomplete[fieldName].addListener('place_changed',function()
			{							
				var fieldName=this.fieldName;
				
				var place=$autocomplete[fieldName].getPlace();
								
				/***/

				if(!place.geometry)
				{
					$self.showFieldError(text,$option.message.place_geometry_error);
					$self.resetLocationSelection(fieldName,text,text.siblings('input[type="hidden"]'),{resetCountry:true,clearRoute:true,clearPickupTime:true,focus:true});

					$self.setGoogleMapAutocompleteOption(fieldName);

					return(false);
				}

				/***/

				var placeLat=place.geometry.location.lat();
				var placeLng=place.geometry.location.lng();

				if(!$self.isCompleteAddress(place))
				{
					$self.showFieldError(text,$option.message.address_incomplete);
					$self.resetLocationSelection(fieldName,text,text.siblings('input[type="hidden"]'),{resetCountry:true,clearRoute:true,clearPickupTime:true,focus:true});
					return(false);
				}

				var placeData=
				{
					lat:placeLat,
					lng:placeLng,
					address:$self.removeDoubleQuote(text.val()),
					zip_code:$self.getElementFromPlace(place,'postal_code'),
					country_code:$self.getElementFromPlace(place,'country','short_name'),
				};
				
				/***/
				
				var newLocation=$self.getLocationReplace(place.geometry.location.lat(),place.geometry.location.lng());
				
				if(newLocation!==false)
				{
					placeData.lat=newLocation.coordinate.lat;
					placeData.lng=newLocation.coordinate.lng;
				}
				
				/***/
	
				for(var i in place.address_components)
				{
					if($.inArray('locality',place.address_components[i].types)>-1)
					{
						placeData.locality=place.address_components[i].long_name;
						break;
					}
				}

				var field=text.siblings('input[type="hidden"]');

				if(!$self.applyDrivingZoneGeofence(fieldName,placeData,text,field)) return(false);
				if(!$self.setPickupDropoffCountry(fieldName,placeData.country_code,text,field)) return(false);
				if(!$self.applyPickupTimeGeofence(fieldName,placeLat,placeLng,text,field)) return(false);
				if(!$self.applyDropoffLocationGeofence(fieldName,placeLat,placeLng,text,field)) return(false);
				$self.setDefaultPickupTime(fieldName);

				field.val(JSON.stringify(placeData));
				
				$self.clearFieldError(text);

				$self.setGoogleMapAutocompleteOption(fieldName);
		 
				//$self.googleMapCreate();
				$self.googleMapCreateRoute();	
			});					   
		};

		/**********************************************************************/

		this.showFieldError=function(textField,message)
		{
			var field=textField.closest('.chbs-form-field');
			var label=field.children('label').first();

			field.addClass('chbs-form-field-error');

			if(label.length===1)
			{
				if(label.data('qtip')) label.qtip('destroy',true);

				label.qtip(
				{
					show:
					{
						ready:true,
						target:label
					},
					style:
					{
						classes:'chbs-qtip chbs-qtip-error'
					},
					content:
					{
						text:message
					},
					position:
					{
						my:($option.rtl_mode ? 'bottom right' : 'bottom left'),
						at:($option.rtl_mode ? 'top right' : 'top left'),
						container:field.parents('[name="chbs-form"]')
					}
				}).qtip('show');
			}
			else
			{
				$self.getGlobalNotice().removeClass('chbs-hidden').html(message);
			}
		};

		/**********************************************************************/

		this.clearFieldError=function(textField)
		{
			var field=textField.closest('.chbs-form-field');
			var label=field.children('label').first();

			field.removeClass('chbs-form-field-error');

			if(label.length===1 && label.data('qtip'))
			{
				label.qtip('destroy',true);
			}
		};

		/**********************************************************************/

		this.resetLocationSelection=function(fieldName,textField,hiddenField,options)
		{
			var settings=$.extend(
			{
				resetCountry:true,
				clearRoute:true,
				clearPickupTime:true,
				focus:false
			},options);

			textField.val('');
			hiddenField.val('');

			if(settings.resetCountry)
				$self.resetLocationCountryCode(fieldName);

			if(settings.clearPickupTime && fieldName.indexOf('pickup')>-1)
				$self.clearPickupTimeField();

			if(settings.clearRoute)
				$self.resetRouteData();

			$self.setGoogleMapAutocompleteOption(fieldName);

			if(settings.focus)
				textField.focus();
		};

		/**********************************************************************/

		this.resetLocationCountryCode=function(fieldName)
		{
			if(fieldName.indexOf('pickup')>-1)
				$pickupCountryCode='';
			else if(fieldName.indexOf('dropoff')>-1)
				$dropoffCountryCode='';
		};

		/**********************************************************************/

		this.clearPickupTimeField=function()
		{
			var serviceTypeId=$self.getServiceTypeId();
			var pickupTimeField=$self.e('[name="chbs_pickup_time_service_type_'+serviceTypeId+'"]');

			if(pickupTimeField.length===1)
				pickupTimeField.val('');
		};

		/**********************************************************************/

		this.resetPickupTimeOnLoad=function()
		{
			for(var i=1;i<=3;i++)
			{
				var pickupTimeField=$self.e('[name="chbs_pickup_time_service_type_'+i+'"]');
				if(pickupTimeField.length!==1) continue;

				if(!$self.hasValidPickupLocation(i))
					pickupTimeField.val('');
			}
		};

		/**********************************************************************/

		this.isCompleteAddress=function(place)
		{
			if(!place || !place.address_components) return(false);

			var hasStreetNumber=false;
			var hasRoute=false;
			var hasLocality=false;

			for(var i in place.address_components)
			{
				var component=place.address_components[i];

				if($.inArray('street_number',component.types)>-1) hasStreetNumber=true;
				if($.inArray('route',component.types)>-1) hasRoute=true;
				if($.inArray('locality',component.types)>-1 || $.inArray('postal_town',component.types)>-1) hasLocality=true;
			}

			return(hasStreetNumber && hasRoute && hasLocality);
		};

		/**********************************************************************/

		this.setDefaultPickupTime=function(fieldName)
		{
			if(fieldName.indexOf('pickup')===-1) return;

			var serviceTypeId=$self.getServiceTypeId();
			var helper=new CHBSHelper();

			if(!$self.hasValidPickupLocation(serviceTypeId)) return;

			var pickupTimeField=$self.e('[name="chbs_pickup_time_service_type_'+serviceTypeId+'"]');
			var pickupDateField=$self.e('[name="chbs_pickup_date_service_type_'+serviceTypeId+'"]');

			if(pickupTimeField.length!==1) return;
			if(!helper.isEmpty(pickupTimeField.val())) return;
			if(pickupDateField.length!==1 || helper.isEmpty(pickupDateField.val())) return;

			var dateValue=pickupDateField.val();
			var date;

			try
			{
				date=$.datepicker.parseDate($option.date_format_js,dateValue);
			}
			catch(e)
			{
				date=null;
			}

			if(!date) return;

			var dayWeek=parseInt(date.getDay(),10);
			if(dayWeek===0) dayWeek=7;

			if(typeof($option.business_hour[dayWeek])!=='undefined' && typeof($option.business_hour[dayWeek].default_format)!=='undefined')
				pickupTimeField.val($option.business_hour[dayWeek].default_format);
		};

		/**********************************************************************/

		this.hasValidPickupLocation=function(serviceTypeId)
		{
			var helper=new CHBSHelper();
			var coordinateField=$self.e('[name="chbs_pickup_location_coordinate_service_type_'+serviceTypeId+'"]');

			if(coordinateField.length===1 && !helper.isEmpty(coordinateField.val()))
			{
				try
				{
					var coordinate=JSON.parse(coordinateField.val());
					if(!helper.isEmpty(coordinate.lat) && !helper.isEmpty(coordinate.lng))
						return(true);
				}
				catch(e) {}
			}

			var fixedSelect=$self.e('select[name="chbs_fixed_location_pickup_service_type_'+serviceTypeId+'"]');
			if(fixedSelect.length===1)
			{
				var selected=fixedSelect.children('option:selected');
				if(selected.length===1 && parseInt(selected.val(),10)>0)
					return(true);
			}

			return(false);
		};

		/**********************************************************************/

		this.resetRouteData=function()
		{
			$self.e('input[name="chbs_route_data"]').val('');
			$self.e('input[name="chbs_distance_map"]').val(0);
			$self.e('input[name="chbs_duration_map"]').val(0);
			$self.e('input[name="chbs_distance_sum"]').val(0);
			$self.e('input[name="chbs_duration_sum"]').val(0);

			$self.googleMapClearRoute();
			$self.googleMapClearMarker();
			$self.googleMapReInit();
		};

		/**********************************************************************/

		this.applyDrivingZoneGeofence=function(fieldName,placeData,textField,hiddenField)
		{
			if((fieldName.indexOf('pickup')===-1) && (fieldName.indexOf('dropoff')===-1)) return(true);

			var drivingZoneOption=$self.getGoogleMapAutocompleteOption(fieldName,0);

			if(parseInt(drivingZoneOption.restrictionType,10)===0) return(true);

			var inCircle=true;
			var inCountry=true;

			if(drivingZoneOption.restrictionType==1 || drivingZoneOption.restrictionType==3)
			{
				inCircle=$self.isPointInCircle(new google.maps.LatLng(placeData.lat,placeData.lng),drivingZoneOption.circle.center,drivingZoneOption.circle.radius);
			}

			if(drivingZoneOption.restrictionType==2 || drivingZoneOption.restrictionType==3)
			{
				inCountry=$.inArray(placeData.country_code,drivingZoneOption.componentRestrictions.country)>-1;
			}

			if(!(inCircle && inCountry))
			{
				if($option.message.pickup_dropoff_out_of_range)
					$self.showFieldError(textField,$option.message.pickup_dropoff_out_of_range);

				$self.resetLocationSelection(fieldName,textField,hiddenField,{resetCountry:true,clearRoute:true,clearPickupTime:true,focus:true});
				return(false);
			}

			return(true);
		};

		/**********************************************************************/

		this.applyPickupTimeGeofence=function(fieldName,coordinateLat,coordinateLng,textField,hiddenField)
		{
			if(fieldName.indexOf('pickup')===-1) return(true);
			if(fieldName.indexOf('service_type_1')===-1) return(true);
			if(!$.isArray($option.pickup_time_geofence)) return(true);
			if(parseInt($option.pickup_time_geofence.length,10)===0) return(true);

			var pickupTime=$self.getPickupTimeByGeofence(coordinateLat,coordinateLng);

			if(pickupTime===false)
			{
				if($option.message.pickup_time_geofence_out_of_range)
					$self.showFieldError(textField,$option.message.pickup_time_geofence_out_of_range);

				$self.clearPickupTimeField();
				$self.resetLocationSelection(fieldName,textField,hiddenField,{resetCountry:true,clearRoute:true,clearPickupTime:true,focus:true});
				return(false);
			}

			var pickupTimeField=$self.e('[name="chbs_pickup_time_service_type_1"]');
			if(pickupTimeField.length===1)
				pickupTimeField.val(pickupTime);

			return(true);
		};

		/**********************************************************************/

		this.getPickupTimeByGeofence=function(coordinateLat,coordinateLng)
		{
			if(!$.isArray($option.pickup_time_geofence)) return(false);
			if(parseInt($option.pickup_time_geofence.length,10)===0) return(false);

			var Helper=new CHBSHelper();

			for(var i in $option.pickup_time_geofence)
			{
				if(typeof($option.pickup_time_geofence[i].geofence_shape_coordinate)==='undefined') continue;
				for(var j in $option.pickup_time_geofence[i].geofence_shape_coordinate)
				{
					var coordinate=[];
					var point=[coordinateLng,coordinateLat];

					for(var k in $option.pickup_time_geofence[i].geofence_shape_coordinate[j])
					{
						coordinate.push([$option.pickup_time_geofence[i].geofence_shape_coordinate[j][k].lng,$option.pickup_time_geofence[i].geofence_shape_coordinate[j][k].lat]);
					}

					var result=Helper.coordinateInsidePolygon(point,coordinate);

					if(result===true)
						return($option.pickup_time_geofence[i].time);
				}
			}

			return(false);
		};

		/**********************************************************************/

		this.applyDropoffLocationGeofence=function(fieldName,coordinateLat,coordinateLng,textField,hiddenField)
		{
			if(fieldName.indexOf('dropoff')===-1) return(true);
			if(fieldName.indexOf('service_type_1')===-1) return(true);
			if(!$.isArray($option.pickup_time_geofence)) return(true);
			if(parseInt($option.pickup_time_geofence.length,10)===0) return(true);

			var Helper=new CHBSHelper();
			var inGeofence=false;

			for(var i in $option.pickup_time_geofence)
			{
				if(typeof($option.pickup_time_geofence[i].geofence_shape_coordinate)==='undefined') continue;
				for(var j in $option.pickup_time_geofence[i].geofence_shape_coordinate)
				{
					var coordinate=[];
					var point=[coordinateLng,coordinateLat];

					for(var k in $option.pickup_time_geofence[i].geofence_shape_coordinate[j])
					{
						coordinate.push([$option.pickup_time_geofence[i].geofence_shape_coordinate[j][k].lng,$option.pickup_time_geofence[i].geofence_shape_coordinate[j][k].lat]);
					}

					if(Helper.coordinateInsidePolygon(point,coordinate)===true)
					{
						inGeofence=true;
						break;
					}
				}
				if(inGeofence) break;
			}

			if(!inGeofence)
			{
				if($option.message.pickup_dropoff_out_of_range)
					$self.showFieldError(textField,$option.message.pickup_dropoff_out_of_range);

				$self.resetLocationSelection(fieldName,textField,hiddenField,{resetCountry:true,clearRoute:true,clearPickupTime:false,focus:true});
				return(false);
			}

			return(true);
		};

		/**********************************************************************/

		this.setPickupDropoffCountry=function(fieldName,countryCode,textField,hiddenField)
		{
			if(fieldName.indexOf('pickup')>-1)
				$pickupCountryCode=countryCode;
			else if(fieldName.indexOf('dropoff')>-1)
				$dropoffCountryCode=countryCode;

			if($pickupCountryCode && $dropoffCountryCode && $pickupCountryCode===$dropoffCountryCode)
			{
				if($option.message.pickup_dropoff_same_country)
					$self.showFieldError(textField,$option.message.pickup_dropoff_same_country);

				$self.resetLocationSelection(fieldName,textField,hiddenField,{resetCountry:true,clearRoute:true,clearPickupTime:true,focus:true});

				return(false);
			}

			return(true);
		};
		
		/**********************************************************************/
		
		this.getLocationReplace=function(coordinateLat,coordinateLng)
		{
			if(parseInt($option.location_replace.length,10)===0) return(false);
			
			var Helper=new CHBSHelper();
			
			for(var i in $option.location_replace)
			{
				for(var j in $option.location_replace[i].geofence_shape_coordinate)
				{
					var coordinate=[];
					var point=[coordinateLng,coordinateLat];
					
					for(var k in $option.location_replace[i].geofence_shape_coordinate[j])
					{
						coordinate.push([$option.location_replace[i].geofence_shape_coordinate[j][k].lng,$option.location_replace[i].geofence_shape_coordinate[j][k].lat]);
					}
				
					var result=Helper.coordinateInsidePolygon(point,coordinate);
		
					if(result===true)
					{
						return($option.location_replace[i].location);
					}
				}
			}
			
			return(false);
		};
		
		/**********************************************************************/
		
		this.getElementFromPlace=function(place,element,elementType='long_name')
		{
			for(var i=0;i<place.address_components.length;i++)
			{
				for(var j=0;j<place.address_components[i].types.length;j++)
				{
					if(place.address_components[i].types[j]==element)
					{
						var c=place.address_components[i];
						return(elementType=='long_name' ? c.long_name : c.short_name);
					}
				}
			}	
			
			return('');
		};
		
		/**********************************************************************/
		/**********************************************************************/		
		
		this.googleMapInit=function()
		{
			if(!$self.googleMapExist()) return;
			
			if(parseInt($option.google_map_option.default_location.type,10)===1)
			{
				if(navigator.geolocation) 
				{
					$self.googleMapSetDefaultLocation();
					
					navigator.geolocation.getCurrentPosition(function(position)
					{
						try
						{
							$startLocation=new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
							$googleMap.setCenter($startLocation);
						}
						catch(e) {}
					},
					function()
					{
						$self.googleMapSetDefaultLocation();
					});
				} 
				else
				{
					$self.googleMapSetDefaultLocation();
				}
			}
			else $self.googleMapSetDefaultLocation();
		};
		
		/**********************************************************************/
		
		this.googleMapSetDefaultLocation=function()
		{
			if(typeof($startLocation)==='undefined')
				$startLocation=new google.maps.LatLng($option.google_map_option.default_location.coordinate.lat,$option.google_map_option.default_location.coordinate.lng);
			
			if($self.getServiceTypeId()===3) return;
			
			var helper=new CHBSHelper();
					 
			var coordinate=[];
			
			coordinate[0]=$self.e('[name="chbs_pickup_location_coordinate_service_type_'+$self.getServiceTypeId()+'"]').val();
			coordinate[1]=$self.e('[name="chbs_dropoff_location_coordinate_service_type_'+$self.getServiceTypeId()+'"]').val();
			
			if((!helper.isEmpty(coordinate[0])) && (!helper.isEmpty(coordinate[1])))
				$startLocation=new google.maps.LatLng(coordinate[0],coordinate[1]);

            try
            {
                $googleMap.setCenter($startLocation); 
            }
            catch(e) {}
		};
		
		/**********************************************************************/
		
		this.googleMapCreate=function()
		{
			if($self.e('#chbs_google_map').length!==1) return;
			
			var Helper=new CHBSHelper();
								  
			var option= 
			{
				draggable:Helper.mapToBool($option.google_map_option.draggable.enable),
				scrollwheel:Helper.mapToBool($option.google_map_option.scrollwheel.enable),
				mapTypeId:google.maps.MapTypeId[$option.google_map_option.map_control.id],
				mapTypeControl:Helper.mapToBool($option.google_map_option.map_control.enable),
				mapTypeControlOptions:
				{
					style:google.maps.MapTypeControlStyle[$option.google_map_option.map_control.style],
					position:google.maps.ControlPosition[$option.google_map_option.map_control.position]
				},
				zoom:$option.google_map_option.zoom_control.level,
				zoomControl:Helper.mapToBool($option.google_map_option.zoom_control.enable),
				zoomControlOptions:
				{
					position:google.maps.ControlPosition[$option.google_map_option.zoom_control.position]
				},
				streetViewControl:false,
				styles:$option.google_map_option.style,
				mapId:$option.google_map_option.map_id
			};
					 
			$googleMap=new google.maps.Map($self.e('#chbs_google_map')[0],option);
		
			if(parseInt($option.google_map_option.traffic_layer.enable,10)===1)
			{
				var trafficLayer=new google.maps.TrafficLayer();
				trafficLayer.setMap($googleMap);
			}
		};
		
		/**********************************************************************/
		
		this.getCoordinate=function(includeDropoffLocation=false)
		{
			var helper=new CHBSHelper();
			var coordinate=new Array();
			
			var serviceTypeId=$self.getServiceTypeId();
			var panelField=$self.e('#panel-'+(serviceTypeId)).children('.chbs-form-field-location-autocomplete,.chbs-form-field-location-fixed');
			
			if(serviceTypeId===1 || serviceTypeId===2)
			{
				panelField.each(function()
				{
					if((serviceTypeId===2) && ($(this).hasClass('chbs-form-field-location-autocomplete')) && (!includeDropoffLocation))
					{
						if($(this).children('input[name="chbs_dropoff_location_service_type_2"]').length===1) return(true);
					}
			
					var c;
					
					try
					{
						if($(this).hasClass('chbs-form-field-location-autocomplete'))
							c=JSON.parse($(this).children('input[type="hidden"]').val());
						else 
						{
							if(($(this).find('input.chbs-form-field-location-fixed-autocomplete').length===0) || ($(this).find('input.chbs-form-field-location-fixed-autocomplete').val().length))
							{
								c=JSON.parse($(this).find('select>option:selected').attr('data-location'));
							}
							else c={lat:'',lng:''};
						}
					}
					catch(e)
					{
						c={lat:'',lng:''};
					}
				
					if((!helper.isEmpty(c.lat)) && (!helper.isEmpty(c.lng)))
						coordinate.push(new google.maps.LatLng(c.lat,c.lng));
				});
			}
			else
			{								
				/***/
				
				var data=[];
				
				var p=true;
				var option=$self.e('select[name="chbs_route_service_type_3"]>option:selected');
				
				if(parseInt($self.e('[name="chbs_route_service_type_3_autocomplete"]').length,10)===1)
					p=!helper.isEmpty($self.e('[name="chbs_route_service_type_3_autocomplete"]').val());
				
				if((parseInt(option.val(),10)>0) && (p)) 
					data=JSON.parse(option.attr('data-coordinate'));

				/***/
				
				var field=$self.e('[name="chbs_pickup_location_coordinate_service_type_3"]');
				if(parseInt(field.length,10)===1)
				{
					if(!helper.isEmpty(field.val()))
						data.unshift(JSON.parse(field.val()));
				}
				
				/***/

				var field=$self.e('[name="chbs_dropoff_location_coordinate_service_type_3"]');
				if(parseInt(field.length,10)===1)
				{
					if(!helper.isEmpty(field.val()))
						data.push(JSON.parse(field.val()));
				}	
				
				/***/
					
				for(var i in data)
				{
					if((!helper.isEmpty(data[i].lat)) && (!helper.isEmpty(data[i].lng)))
						coordinate.push(new google.maps.LatLng(data[i].lat,data[i].lng));					
				}
			}	
			
			return(coordinate);
		};
		
		/**********************************************************************/

		this.googleMapExist=function()
		{
			return(typeof($googleMap)==='undefined' ? false : true); 
		};
	
		/**********************************************************************/
		
		this.googleMapCreateRoute=function(callback)
		{ 
			var serviceTypeId=$self.getServiceTypeId();
			
			if(!$self.googleMapExist())
			{
				if(typeof(callback)!=='undefined') callback();
				return;
			}
			
			var request;
			
			var panelField=$self.e('#panel-'+(serviceTypeId)).children('.chbs-form-field-location-autocomplete');
		   
			var coordinate=$self.getCoordinate();
			var length=coordinate.length;
		
			if(length===0)
			{
				$self.googleMapClearRoute();
				$self.googleMapClearMarker();
				$self.googleMapReInit();
				
				if(typeof(callback)!=='undefined') callback();
				return;
			}

			if(serviceTypeId===2)
			{
				if(length===2)
				{
					coordinate=[coordinate[0]];
					length=1;
				}
			}

			if(length>2)
			{
				var waypoint=new Array();
				
				coordinate.forEach(function(item,i) 
				{
					if((i>0) && (i<length-1))
						waypoint.push({location:item,vehicleStopover:true});
				});
				
				request= 
				{
					origin:coordinate[0],
					intermediates:waypoint,
					destination:coordinate[length-1],
					travelMode:'drive'
				};			
			}
			else if(length===2)
			{
				request= 
				{
					origin:coordinate[0],
					destination:coordinate[length-1],
					travelMode:'drive'
				};		  
			}
			else
			{
				request= 
				{
					origin:coordinate[length-1],
					destination:coordinate[length-1],
					travelMode:'drive'
				};			  
			}
			
			request.routeModifiers={};
			
			request.routeModifiers.avoidTolls=$.inArray('tolls',$option.google_map_option.route_avoid)>-1 ? true : false;
			request.routeModifiers.avoidFerries=$.inArray('ferries',$option.google_map_option.route_avoid)>-1 ? true : false;
			request.routeModifiers.avoidHighways=$.inArray('highways',$option.google_map_option.route_avoid)>-1 ? true : false;
			
			if(parseInt($option.google_map_option.route_type,10)===2)
			{
				request.computeAlternativeRoutes=true;
			}
			
			$GoogleMapAPI.computeRoutes(request,function(response)
			{			  
				if($GoogleMapAPI.hasRoute(response))
				{
					$self.googleMapCreate();
					$self.googleMapClearRoute();
					$self.googleMapClearMarker();
					
					if(parseInt($option.google_map_option.route_type,10)===2)
					{
						var distanceMin=-1;
						
						for(var i in response.routes)
						{
							var d=$self.formatDistance(response.routes[i].distanceMeters);
							
							if((d<distanceMin) || (distanceMin===-1)) 
							{	
								distanceMin=d;
								$routeIndex=i;
							}
						}
					}
	
					for(var i in response.routes[$self.getRouteIndex()].legs)
					{
						var leg=response.routes[$self.getRouteIndex()].legs[i];

						$self.googleMapCreateMarker(leg.startLocation);
						$self.googleMapCreateMarker(leg.endLocation); 
					}
					
					/***/
					
					if(parseInt($option.tax_rate_geofence_enable,10)===1)
					{
						var routeData=[];

						for(var i in response.routes[$self.getRouteIndex()].legs)
						{
							var leg=response.routes[$self.getRouteIndex()].legs[i];
							
							for(var j in leg.steps)
							{
								var step=leg.steps[j];
								routeData.push([step.startLocation,step.endLocation,step.distanceMeters,step.startLocation]);
							}
						}

						$self.e('input[name="chbs_route_data"]').val(JSON.stringify(routeData));
					}
					
					/***/

					try
					{
						$googleMap.fitBounds($GoogleMapAPI.getBound(response,$self.getRouteIndex()));
					}
					catch(e) {}

					var routePolyline=$GoogleMapAPI.drawRoute(response,$self.getRouteIndex(),$googleMap);
					if(routePolyline)
						$routePolyline.push(routePolyline);
					
					$self.calculateRoute(response,callback);
				}
				else
				{
					if($.inArray(serviceTypeId,[1,3])>-1)
					{
						alert($option.message.designate_route_error);
						
						panelField.each(function()
						{
							$(this).children('input[type="text"]').val('');
							$(this).children('input[type="hidden"]').val('');
						}); 
						
						if($.inArray(serviceTypeId,[1])>-1)
						{
							$self.googleMapReInit();
						}
						else if($.inArray(serviceTypeId,[3])>-1)
						{
							$self.triggerOnSelectDatePicker('[name="chbs_pickup_date_service_type_3"]');

							$self.googleMapCreate();
							$self.googleMapCreateRoute();
						}
					}
				}
				
				if(typeof(callback)!=='undefined') callback();
			});			
		};
		
		/**********************************************************************/
		
		this.googleMapClearMarker=function()
		{
			for(var i in $marker)
				$marker[i].setMap(null);
			
			$marker=[];
		};

		/**********************************************************************/

		this.googleMapClearRoute=function()
		{
			for(var i in $routePolyline)
			{
				$routePolyline[i].setMap(null);
			}

			$routePolyline=[];
		};
		
		/**********************************************************************/
		
		this.googleMapCreateMarker=function(position)
		{	
			var position=new google.maps.LatLng(position.latLng.latitude,position.latLng.longitude);

			var label=$marker.length+1;
			
			var marker=new google.maps.marker.AdvancedMarkerElement(
			{
				position:position,
				map:$googleMap,
				title:''+label
			});	 
			
			$marker.push(marker);
		};
		
		/**********************************************************************/
		
		this.googleMapReInit=function()
		{
			if(!$self.googleMapExist()) return;
			
			$googleMap.setZoom($option.google_map_option.zoom_control.level);
							
			$self.calculateRoute();
				
			try
			{
				if($startLocation!==null)
					$googleMap.setCenter($startLocation);
			}
			catch(e) {}
		};

		/**********************************************************************/
		
		this.calculateRoute=function(response,callback)
		{
			var distance=0;
			var duration=0;
			
			if($GoogleMapAPI.hasRoute(response))
			{
				distance=parseInt(response.routes[$self.getRouteIndex()].distanceMeters,10);
				duration=parseInt(response.routes[$self.getRouteIndex()].duration,10);
			}
			
			distance=$self.formatDistance(distance);
			duration=$self.formatDuration(duration);
			
			if($option.ride_time_rounding>0.00)
			{
				duration=Math.ceil(duration/$option.ride_time_rounding)*$option.ride_time_rounding;
			}
			
			$self.e('input[name="chbs_distance_map"]').val(distance);
			$self.e('input[name="chbs_duration_map"]').val(duration*$option.ride_time_multiplier);
			
			$self.reCalculateRoute(callback);
		};
		
		/**********************************************************************/
		
		this.reCalculateRoute=function(callback)
		{
			var duration=0;
			var durationWaypoint=0;
			
			var distance=0;
			
			var serviceTypeId=parseInt($self.e('input[name="chbs_service_type_id"]').val(),10);
			
			distance=$self.e('input[name="chbs_distance_map"]').val();
			
			switch(serviceTypeId)
			{
				case 1:
					
					var helper=new CHBSHelper();
				
					duration=$self.e('select[name="chbs_extra_time_service_type_1"]').val();
					if(isNaN(duration)) duration=0; 
					
					duration*=($option.extra_time_unit===2 ? 60 : 1);
					
					$self.e('input[name="chbs_waypoint_location_service_type_1[]"]').each(function()
					{
						var coordinate={};
						
						try
						{
							var coordinate=JSON.parse($(this).next('input').val());
						}
						catch(e)
						{
							coordinate.lat=null;
							coordinate.lng=null;
						}
						
						if((!helper.isEmpty(coordinate.lat)) && (!helper.isEmpty(coordinate.lng)))
						{
							var field=$(this).parent('.chbs-form-field:first').next('.chbs-form-field-waypoint-duration').children('select');
							
							if(parseInt(field.length,10)===1)
							{
								durationWaypoint+=parseInt(field.val(),10);
							}
						}
					});
					
				break;
				
				case 2:
					
					duration=$self.e('select[name="chbs_duration_service_type_2"]').val();
					if(isNaN(duration)) duration=0;
					
					duration*=60;
					
				break;
				
				case 3:
					
					duration=$self.e('select[name="chbs_extra_time_service_type_3"]').val();
					if(isNaN(duration)) duration=0; 
					
					duration*=($option.extra_time_unit===2 ? 60 : 1);
					
				break;
			}
			
			if($.inArray(serviceTypeId,[1,3])>-1)
			{
				var transferType=$self.e('select[name="chbs_transfer_type_service_type_'+serviceTypeId+'"]');
				var transferTypeValue=transferType.length===1 ? ($.inArray(parseInt(transferType.val(),10),[2,3])>-1 ? 2 : 1) : 1;
				
				duration+=(parseInt($self.e('input[name="chbs_duration_map"]').val(),10)*transferTypeValue)+(durationWaypoint*transferTypeValue);
				distance*=transferTypeValue;
			}
			
			$self.e('input[name="chbs_distance_sum"]').val(distance);
			$self.e('input[name="chbs_duration_sum"]').val(duration);
			
			var sDuration=$self.splitTime(duration);
			
			distance=$self.formatLength(distance);
				
			$self.e('.chbs-ride-info>div:eq(0)>span:eq(2)>span:eq(0)').html(distance);
			$self.e('.chbs-ride-info>div:eq(1)>span:eq(2)>span:eq(0)').html(sDuration[0]);
			$self.e('.chbs-ride-info>div:eq(1)>span:eq(2)>span:eq(2)').html(sDuration[1]);  
			
			$self.calculateBaseLocationDistance(callback);
		};
		
		/**********************************************************************/
		
		this.formatLength=function(length)
		{
			if($option.length_unit===2)
			{   
				length/=1.609344;
				length=Math.round(length*10)/10;
			}
			
			return(length);
		};
		
		/**********************************************************************/
		
		this.splitTime=function(time)
		{
			return([Math.floor(time/60),time%60]);
		};
		
		/**********************************************************************/
		
		this.setWidthClass=function()
		{
			if(parseInt($option.widget.mode,10)===1)
			{
				if($this.hasClass('chbs-widget-style-1')) return;
			}
			
			var Helper=new CHBSHelper();
			
			var width=Helper.setWidthClass($this);

			if($self.prevWidth!==width)
			{
				$self.prevWidth=width;
				$(window).resize();
				
				$self.createStickySidebar();
				
				if(parseInt($option.widget.mode,10)!==1)
				{
					if($.inArray(width,['300','480'])>-1)
						$self.googleMapStopCustomizeHeight();
					else $self.googleMapStartCustomizeHeight();
				}
			};
			
			$self.e('.chbs-notice-fixed>div').css('width',parseInt($this.css('width'),10));
						
			setTimeout($self.setWidthClass,500);
		};
	   
		/**********************************************************************/
		
		this.getValueFromClass=function(object,pattern)
		{
			try
			{
				var reg=new RegExp(pattern);
				var className=$(object).attr('class').split(' ');

				for(var i in className)
				{
					if(reg.test(className[i]))
						return(className[i].substring(pattern.length));
				}
			}
			catch(e) {}

			return(false);		
		};
		
		/**********************************************************************/
		
		this.createSummaryPriceElement=function()
		{
			$self.setAction('create_summary_price_element');
  
			$self.post($self.e('form[name="chbs-form"]').serialize(),function(response)
			{	
				$self.e('.chbs-summary-price-element').replaceWith(response.html);
				
				$self.createSelectMenu();
			
				$(window).scroll();
			});   
		};
		
		/**********************************************************************/
		
		this.createStickySidebar=function()
		{
			if(parseInt($option.summary_sidebar_sticky_enable,10)!==1) return;
			
			var className=$self.getValueFromClass($this,'chbs-width-');
			
			if($.inArray(className,['300','480','768'])>-1)
			{
				$self.removeStickySidebar();
				return;
			}	   
			
			if($this.hasClass('chbs-hidden')) return;
			
			var step=parseInt($self.e('input[name="chbs_step"]').val(),10);
			
			$sidebar=$self.e('.chbs-main-content>.chbs-main-content-step-'+step+'>.chbs-layout-25x75 .chbs-layout-column-left:first').theiaStickySidebar({'additionalMarginTop':40,'additionalMarginBottom':40});
		};
		
		/**********************************************************************/
		
		this.removeStickySidebar=function()
		{
			if(parseInt($option.summarySidebarStickyEnable,10)!==1) return;
			try
			{
				$sidebar.destroy();
			}
			catch(e) {}
		};
		
		/**********************************************************************/
		
		this.getGlobalNotice=function()
		{
			var step=parseInt($self.e('input[name="chbs_step"]').val(),10);
			return($self.e('.chbs-main-content-step-'+step+' .chbs-notice'));
		};
		
		/**********************************************************************/
		
		this.calculateBaseLocationDistance=function(callback,coordinate)
		{
			if(typeof(coordinate)=='undefined') coordinate=false;
			
			var helper=new CHBSHelper();
			
			var baseLocation;
			var baseLocationData={distance:0,duration:0,return_distance:0,return_duration:0};
			
			if(coordinate===false)
			{
				$self.e('input[name="chbs_base_location_distance"]').val(0);
				$self.e('input[name="chbs_base_location_duration"]').val(0);
				$self.e('input[name="chbs_base_location_return_distance"]').val(0);
				$self.e('input[name="chbs_base_location_return_duration"]').val(0);

				baseLocation={coordinate:{lat:$option.base_location.coordinate.lat,lng:$option.base_location.coordinate.lng}};

				var vehicleId=$self.e('input[name="chbs_vehicle_id"]').val();
				var vehicle=$self.e('.chbs-vehicle-list .chbs-vehicle[data-id="'+vehicleId+'"]');

				if(vehicle.length===1)
				{
					if((!helper.isEmpty(vehicle.attr('data-base_location_cooridnate_lat'))) && (!helper.isEmpty(vehicle.attr('data-base_location_cooridnate_lng'))))
					{
						baseLocation.coordinate.lat=vehicle.attr('data-base_location_cooridnate_lat');
						baseLocation.coordinate.lng=vehicle.attr('data-base_location_cooridnate_lng');
					}
				}
			}
			else
			{
				baseLocationData.id=coordinate.id;
				
				baseLocation={coordinate:{lat:coordinate.lat,lng:coordinate.lng}};
			}
		   		   
			if((helper.isEmpty(baseLocation.coordinate.lat)) || (helper.isEmpty(baseLocation.coordinate.lng)))
			{
				$self.doVehiclePriceCalculationStepFirst();
				$self.callback(callback,baseLocationData);
				return(baseLocationData);
			}
			
			var request;
			var routeCoordinate=$self.getCoordinate(true);
			
			/***/
			
			if(parseInt(routeCoordinate.length,10)===0)
			{
				$self.doVehiclePriceCalculationStepFirst();
				$self.callback(callback,baseLocationData);
				return(baseLocationData);
			}
			
			request= 
			{
				origin:new google.maps.LatLng(baseLocation.coordinate.lat,baseLocation.coordinate.lng),
				destination:routeCoordinate[0],
				travelMode:'drive'
			};   
			
			$GoogleMapAPI.computeRoutes(request,function(response,status)
			{
				if($GoogleMapAPI.hasRoute(response))
				{
					var distance=parseInt(response.routes[0].distanceMeters,10);
					var duration=parseInt(response.routes[0].duration,10);
					
					distance=$self.formatDistance(distance);
					duration=$self.formatDuration(duration);
								
					if(coordinate===false)
					{
						$self.e('input[name="chbs_base_location_distance"]').val(distance);
						$self.e('input[name="chbs_base_location_duration"]').val(duration);
					}
					else
					{
						baseLocationData.distance=distance;
						baseLocationData.duration=duration;
					}
					
					if(routeCoordinate.length>1)
					{
						var transferTypeId=1;
						var serviceTypeId=$self.getServiceTypeId();

						if($.inArray(serviceTypeId,[1,3])>-1)
						{
							var transferType=$self.e('select[name="chbs_transfer_type_service_type_'+serviceTypeId+'"]');
							transferTypeId=transferType.length===1 ? parseInt(transferType.val(),10) : 1;
						}

						request= 
						{
							origin:$.inArray(transferTypeId,[1,3])>-1 ? routeCoordinate[routeCoordinate.length-1] : routeCoordinate[0],
							destination:new google.maps.LatLng(baseLocation.coordinate.lat,baseLocation.coordinate.lng),
							travelMode:'drive'
						};   
						
						$GoogleMapAPI.computeRoutes(request,function(response,status)
						{
							if($GoogleMapAPI.hasRoute(response))
							{
								var distance=parseInt(response.routes[0].distanceMeters,10);
								var duration=parseInt(response.routes[0].duration,10);

								distance=$self.formatDistance(distance);
								duration=$self.formatDuration(duration);

								if(coordinate===false)
								{
									$self.e('input[name="chbs_base_location_return_distance"]').val(distance);
									$self.e('input[name="chbs_base_location_return_duration"]').val(duration);
								}
								else
								{
									baseLocationData.return_distance=distance;
									baseLocationData.return_duration=duration;
								}
							}
							
							$self.doVehiclePriceCalculationStepFirst();
							$self.callback(callback,baseLocationData);
						});
					}
					else 
					{		
						$self.doVehiclePriceCalculationStepFirst();
						$self.callback(callback,baseLocationData);
					}
				}
				else
				{
					$self.doVehiclePriceCalculationStepFirst();
					$self.callback(callback,baseLocationData);
				}
			});
			
			return(baseLocationData);
			
			/***/
		};
		
		/**********************************************************************/
		
		this.getRouteIndex=function()
		{
			return(isNaN($routeIndex) ? 0 : $routeIndex);
		};
		
		/**********************************************************************/
		
		this.removeDoubleQuote=function(value)
		{
			return(value.replace(/"/g,''));
		};
		
		/**********************************************************************/
		
		this.callback=function(callback,arg)
		{
			if(typeof(callback)!=='undefined') callback(arg);
		};
		
		/**********************************************************************/
		
		this.createButtonRadio=function(selector)
		{
			$self.e(selector).on('click','.chbs-button-radio a',function(e)
			{
				e.preventDefault();
				
				var field=$(this).parent('.chbs-button-radio').find('input[type="hidden"]');
				
				$(this).siblings('a').removeClass('chbs-state-selected');
				
				if($(this).hasClass('chbs-state-selected'))
				{
					field.val(-1);
					$(this).removeClass('chbs-state-selected');
				}
				else 
				{	
					field.val($(this).attr('data-value'));		 
					$(this).addClass('chbs-state-selected');
				}
			});		  
		};
		
		/**********************************************************************/
		
		this.formatDistance=function(distance)
		{
			if(isNaN(distance)) distance=0;
			
			distance/=1000;
			distance=Math.round(distance*10)/10;
			
			if(distance<0) distance=0;
			
			return(distance);
		};
		
		/**********************************************************************/
		
		this.formatDuration=function(duration)
		{
			if(isNaN(duration)) duration=0;
			
			duration=Math.ceil(duration/60);	
			
			if(duration<0) duration=0;	
			
			return(duration);
		};
		
		/**********************************************************************/
		
		this.addGAEvent=function(eventName,argument)
		{
			if(typeof(chbseGAEvent)!=="undefined") 
			{
				chbseGAEvent.addEvent(eventName,argument);
			}
		};
		
		/**********************************************************************/
		
		this.createCurrencySwitcher=function()
		{
			if(typeof(CHBSECSCurrencySwitcher)!=="undefined") 
			{
				CHBSECSCurrencySwitcher.setup($self);
			}
		};
				
		/**********************************************************************/
		/**********************************************************************/
	};
	
	/**************************************************************************/
	
	$.fn.chauffeurBookingForm=function(option) 
	{
		console.log('--------------------------------------------------------------------------------------------');
		console.log('Chauffer Booking System for WordPress ver. '+option.plugin_version+(parseInt(option.license_verified,10)===1 ? ' (license verified)' : ' (license unverified)'));
		console.log('https://1.envato.market/chauffeur-booking-system-for-wordpress-preview');
		console.log('--------------------------------------------------------------------------------------------');
		
		var form=new ChauffeurBookingForm(this,option);
		return(form);
	};
	
	/**************************************************************************/

})(jQuery,document,window);

/******************************************************************************/
/******************************************************************************/
