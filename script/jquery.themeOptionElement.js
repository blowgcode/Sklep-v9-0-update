/******************************************************************************/
/******************************************************************************/

;(function($,doc,win) 
{
	"use strict";
	
	var themeOptionElement=function(object,option)
	{
		/**********************************************************************/
		
		var $this=$(object);
		
		var $optionDefault=
		{
			init:false
		};
		
		var $option=$.extend($optionDefault,option);

		/**********************************************************************/
		
		this.create=function()
		{
			if(!$option.init) return;
			
			this.createDropkick();
			this.createButtonSet();
			this.createColorPicker();
			this.createInfieldLabel();
			this.createDatePicker();
			this.createTimePicker();
			this.createTab();
			this.createCopyToClipboard();

			$this.css({'display':'block'});
		};

		/**********************************************************************/
		
		this.createColorPicker=function()
		{
			var self=this;
			
			$this.find('.to-color-picker').each(function() 
			{
				var object=$(this);
				object.ColorPicker(
				{
					onChange:function(hsb,hex,rgb) 
					{
						object.val(hex.toUpperCase());
						object.parent().children('.to-color-picker-sample').css('background-color','#'+hex);	
					},
					onSubmit:function(hsb,hex,rgb,el)
					{
						$(el).val(hex);
						$(el).ColorPickerHide();
					},
					onBeforeShow:function()
					{
						$(this).ColorPickerSetColor(this.value);
					}
				});

				var colorSample=$(document.createElement('span'));

				colorSample.attr('class','to-color-picker-sample');
				colorSample.css({'background-color':self.getColor(object.val())});

				object.parent().append(colorSample);

				object.change(function() 
				{
					var value=$(this).val();
					var object=$(this).parent().children('.to-color-picker-sample');
					
					object.css({'background-color':self.getColor(value)});
				});
			});
		};
		
		/**********************************************************************/
		
		this.getColor=function(value)
		{
			if(/^[0-9A-Fa-f]{6}$/i.test(value)) return('#'+value);
			else if(/^[0-9A-Fa-f]{8}$/i.test(value))
			{
				var rgba=[];
						
				for(var i=0;i<8;i+=2)
				{
					var number=parseInt(value.charAt(i)+value.charAt(i+1),16);
					rgba.push((number>=549755813888) ? number-1099511627776 : number);
				}
				
				return('rgba('+rgba[0]+','+rgba[1]+','+rgba[2]+','+(rgba[3]/255)+')');
			}
			else return('#FFFFFF');				
		};
		
		/**********************************************************************/
		
		this.createDatePicker=function()
		{
			$this.on('focusin','.to-datepicker',function()
			{
				$(this).datepicker(
				{ 
					inline:true,
					dateFormat:'dd-mm-yy',
					prevText:'<',
					nextText:'>',
					beforeShow:function(date,instance)
					{
						$(instance.dpDiv).addClass('to-ui-datepicker');
					}
				});
			});
		};
		
		/**********************************************************************/
		
		this.createTimePicker=function()
		{
			$this.on('focusin','.to-timepicker',function()
			{
				var width=$(this).outerWidth();
				
				$(this).timepicker(
				{ 
					timeFormat:'H:i',
					appendTo:$(this).parent()
				});
				
				$(this).on('showTimepicker',function()
                {
					$(this).next('.ui-timepicker-wrapper').width(width);
				});
			});
		};
		
		/**********************************************************************/
		
		this.createInfieldLabel=function()
		{
			if($.fn.inFieldLabels)
				$this.find('.to-infield').inFieldLabels();
		};
		
		/**********************************************************************/
		
		this.createDropkick=function()
		{
			if($.fn.dropkick)
				$this.find('select:not(.to-dropkick-disable)').dropkick();
		};
		
		/**********************************************************************/
		
		this.createButtonSet=function()
		{
			var buttonset=$this.find('.to-radio-button:not(.to-jqueryui-buttonset-disable),.to-checkbox-button:not(.to-jqueryui-buttonset-disable)');
			if(buttonset.length)
			{
				buttonset.buttonset();
			}
		};
		
		/**********************************************************************/
		
		this.createSlider=function(selector,min,max,value,step)
		{
			var slider=$this.find(selector);
            
			if(!slider.length) return;
            
			slider.slider(
			{
				min:min,
				max:max,
				range:'min',
				value:value,
				step:(typeof(step)==='undefined' ? 1 : step),
				slide:function(event,ui) 
				{
					$(this).nextAll('input').val(ui.value);
				},
				create:function()
				{
					$(this).nextAll('input').val($(this).slider('value'));
				}
			});		
		};
		
		/**********************************************************************/
		
		this.bindBrowseMedia=function(selector,multiple=false,type=1,fileType='image')
		{
			$this.find(selector).bind('click',function()
			{
				var self=$(this);

				if(fileType=='all')
				{
					wp.media.frames.selectImageFrame=wp.media(
					{
						multiple:multiple
					});					
				}
				else
				{
					wp.media.frames.selectImageFrame=wp.media(
					{
						multiple:multiple,
						library: 
						{
						   type:fileType
						}
					});
				}

				wp.media.frames.selectImageFrame.on('open',function()
				{
					var selection=wp.media.frames.selectImageFrame.state().get('selection');

					var value=self.prev().val();

					if(parseInt(type,10)===2)
					{
						var data=value.split('.');

						for(var i in data)
							selection.add(wp.media.attachment(data[i]));
					}
				});

				wp.media.frames.selectImageFrame.on('select',function()
				{
					var selection=wp.media.frames.selectImageFrame.state().get('selection');

					if(!selection) return;

					var value='';

					selection.each(function(attachment)
					{
						if(type===1)
							value=attachment.attributes.url;
						else
							value+=attachment.attributes.id+'.';
					});
                    
					if(type===2)
						value=value.substr(0,value.length-1);
                    
					self.prev().val(value);
				});
                
				wp.media.frames.selectImageFrame.open();
			});
		};

		/**********************************************************************/

		this.createGoogleFontAutocomplete=function(selector)
		{
			$this.find(selector).autocomplete(
			{
				appendTo:$('.to:first'),
				source:function(request,response) 
				{
					$.ajax(
					{
						url:ajaxurl,
						dataType:'json',
						data: 
						{
							query:request.term,
							action:'autocomplete_google_font'
						},
						success:function(data) 
						{
							response($.map(data,function(item) 
							{
								return(item);
							}));
						}
					});
				},
				minLength:2
			});
		};
		
		/**********************************************************************/
		
		this.createTab=function()
		{
			function getTabPanelId(tab)
			{
				var panel=[];

				tab.children('div').each(function()
				{
					panel.push($(this).attr('id')); 
				});

				return(panel);
			}
            
			/***/
            
			function getTabPanelActive(tab)
			{
				var panelCurrent=getTabPanelId(tab);

				try
				{
					var panelStorage=JSON.parse(window.sessionStorage.getItem('to-tab-panel'));
				}
				catch(e)
				{
					panelStorage=[];
				}

				if($.isArray(panelStorage))
				{
					for(var i in panelCurrent)
					{
						if($.inArray(panelCurrent[i],panelStorage)>-1)
						{
							var listItem=tab.find('a[href="#'+panelCurrent[i]+'"]').parent('li');
							return(listItem.index());
						}
					}
				}

				return(0);
			}
            
			/***/
            
			$this.find('.ui-tabs').each(function()
			{
				$(this).tabs(
				{
					active:getTabPanelActive($(this)),
					activate:function(event,ui)
					{
						var panelCurrent=getTabPanelId($(this));

						var panelStorage=window.sessionStorage.getItem('to-tab-panel');

						if($.isArray(panelStorage))
						{
							for(var i in panelStorage)
							{
								if((position=$.inArray(panelStorage[i],panelCurrent))>-1)
								{
									panelStorage.splice(position,1);
								}
							}
						}
						else
						{
							panelStorage=[];
							panelStorage.push(ui.newTab.attr('aria-controls'));
						}

						window.sessionStorage.setItem('to-tab-panel',JSON.stringify(panelStorage));
					}
				});               
			});
		};
        
		/**********************************************************************/
        
		this.createCopyToClipboard=function()
		{
			try
			{
				if(!ClipboardJS.isSupported())
				{
					$('.to-copy-to-clipboard').css({display:'none'});
					return;
				}

				$('.to-copy-to-clipboard').bind('click',function(e)
				{
					e.preventDefault();
				});

				var clipboard=new ClipboardJS('.to-copy-to-clipboard');
				clipboard.on('success',function(e)
				{
					$(e.trigger).html($(e.trigger).attr('data-label-on-success'));
				});
			}
			catch(e) {}
		};
		
		/**********************************************************************/
	};
	
	/**************************************************************************/
	
	$.fn.themeOptionElement=function(option) 
	{
		var element=new themeOptionElement(this,option);
		element.create();
		
		return(element);
	};
	
	/**************************************************************************/

})(jQuery,document,window);

/******************************************************************************/
/******************************************************************************/