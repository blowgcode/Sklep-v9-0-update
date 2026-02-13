/******************************************************************************/
/******************************************************************************/

;(function($,doc,win)
{
	"use strict";
	
	/**************************************************************************/
		
	var CHBSEGAEvent=function(object,option)
	{
		/**********************************************************************/
		
		var $self=this;
		var $this=$(object);
		
		var $option=option;
		
		/**********************************************************************/
		
		this.setup=function()
		{
		
		};
		
		/**********************************************************************/
		
		this.addEvent=function(eventName,argument={})
		{
			if(parseInt($option.event[eventName].enable,10)===1)
			{
				eventName=$option.event_prefix+eventName;
				
				if(parseInt($option.event_debug_view_enable,10)===1)
					argument.debug_mode=true;
				
				if(parseInt($option.event_console_log_enable,10)===1)
				{
					console.log('Event name:',eventName);
					console.log('Event arguments:',$self.objectToString(argument));
				}
				
				gtag('event',eventName,argument);
			};
		};
		
		/**********************************************************************/
		
		this.objectToString=function(obj)
		{
			var str='';
			
			for(var p in obj)
			{
				if(Object.prototype.hasOwnProperty.call(obj,p)) 
				{
					if(parseInt(str.length,10)!==0) str+=', ';
					str+=p+': '+obj[p];
				}
			}
			
			return(str);
		};
		
		/**********************************************************************/
	};

	/**************************************************************************/
	
	$.fn.CHBSEGAEvent=function(option) 
	{
		var object=new CHBSEGAEvent(this,option);
		return(object);
	};
	
	/**************************************************************************/

})(jQuery,document,window);

/******************************************************************************/
/******************************************************************************/