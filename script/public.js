"use strict";
/******************************************************************************/
/******************************************************************************/

jQuery(document).ready(function($) 
{	
	var tab=$('.chbs-tab-custom');
	
	tab.each(function() 
	{		
		var navigation=$('<ul></ul>').append($(this).children('a'));
		var content=$(this).find('>div');		
		
		$(this).append(navigation).append(content);

		$(this).find('ul>a').wrap('<li></li>');
		
		$('.chbs-tab-custom').tabs(
		{
			create:function()
			{
				$(window).resize();
			}
		});	
	});
	
	$(window).resize(function() 
	{
		tab.each(function() 
		{
			var width=$(this).width();
			var count=parseInt($(this).children('.ui-tabs-nav').children('li').length,10);
			
			if((width/count)>200)
			{
				$(this).removeClass('chbs-tab-custom-responsive');
			}
			else
			{
				$(this).addClass('chbs-tab-custom-responsive');
			}
		});	
	});
});