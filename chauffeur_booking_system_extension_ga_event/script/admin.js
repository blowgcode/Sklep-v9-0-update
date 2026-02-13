/******************************************************************************/
/******************************************************************************/

;(function($,doc,win) 
{
	"use strict";
    
})(jQuery,document,window);

/******************************************************************************/

function chbsegaeRunThemeOption()
{
	jQuery('.to').themeOption({afterSubmit:function(response)
	{
		if(typeof(response.global.reload)!='undefined')
			location.reload();
							
		return(false);
	}});
						
	var element=jQuery('.to').themeOptionElement({init:true});
	element.bindBrowseMedia('.to-button-browse');
};

/******************************************************************************/