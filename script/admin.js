/******************************************************************************/
/******************************************************************************/

;(function($,doc,win) 
{
	"use strict";
    
    /**************************************************************************/
    
    if(parseInt(chbsData.jqueryui_buttonset_enable,10)!==1)
    {
        $('.to .to-radio-button,.to .to-checkbox-button').addClass('to-jqueryui-buttonset-disable');
    }
    
    /**************************************************************************/

    var $GET=[];
    
    document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g,function() 
    {
        function decode(s) 
        {
            return(decodeURIComponent(s.split('+').join(' ')));
        }
        $GET[decode(arguments[1])]=decode(arguments[2]);
    });
    
    /**************************************************************************/
    
    if(($GET['taxonomy']==='chbs_vehicle_c') && ($GET['post_type']==='chbs_booking'))
    {
        var link=$('#menu-posts-chbs_booking').find('a[href="edit-tags.php?taxonomy=chbs_vehicle_c&post_type=chbs_booking"]');
        
        if(link.length===1)
        {
            link.addClass('current');
            link.parent('li').addClass('current');
        }
    }
        
    /**************************************************************************/
    
    var menu=jQuery('#menu-posts-chbs_booking .wp-menu-name');
    if(menu.text()==='Chauffeur Booking System')
        menu.html('Chauffeur<br/>Booking System');
    
    /**************************************************************************/
    
})(jQuery,document,window);

/******************************************************************************/

function toCreateCustomDateTimePicker(dateFormat,timeFormat)
{
    jQuery('body').on('focusin','.to-timepicker-custom',function()
    {
        var width=jQuery(this).outerWidth();

        jQuery(this).timepicker(
        { 
            timeFormat:timeFormat,
            appendTo:jQuery(this).parent()
        });

        jQuery(this).on('showTimepicker',function()
        {
            jQuery(this).next('.ui-timepicker-wrapper').width(width);
        });
    }); 

    jQuery('body').on('focusin','.to-datepicker-custom',function()
    {
        jQuery(this).datepicker(
        { 
            inline:true,
            dateFormat:dateFormat,
            prevText:'<',
            nextText:'>'
        });
		
		jQuery('.ui-datepicker').addClass('to-ui-datepicker');
    });        
};

/******************************************************************************/

function toCreateAutocomplete(field)
{
    jQuery(field).each(function()
    {
        var $field=jQuery(this);
        var id=(new CHBSHelper()).getRandomString(16);
        
        $field.attr('id',id).on('keypress',function(e)
        {
            if(e.which===13)
            {
                e.preventDefault();
                return(false);
            }
        });
        
        $field.on('change',function()
        {
            if(!jQuery.trim(jQuery(this).val()).length)
            {
                var name=jQuery(this).attr('name');
                jQuery('input[name="'+name+'_coordinate_lat"]').val('');
                jQuery('input[name="'+name+'_coordinate_lng"]').val('');                   
            }
        });

        var autocomplete=new google.maps.places.Autocomplete(document.getElementById(id));
        autocomplete.addListener('place_changed',function()
        {
            var name=$field.attr('name');
            var place=autocomplete.getPlace();
            
            jQuery('input[name="'+name+'_coordinate_lat"]').val(place.geometry.location.lat());
            jQuery('input[name="'+name+'_coordinate_lng"]').val(place.geometry.location.lng());
        });
    });
};

/******************************************************************************/

function toPreventCheckbox(object,type=1)
{
    object.on('change',function()
    {
        var checkbox=jQuery(this).parents(':first').find('input');

		if(type===1)
		{
			var value=parseInt(jQuery(this).val());
			if(value===-1)
			{
				checkbox.prop('checked',false);
				checkbox.first().prop('checked',true);
			}
			else checkbox.first().prop('checked',false);
		}
	
        var checked=[];
        checkbox.each(function()
        {
            if(jQuery(this).is(':checked'))
                checked.push(parseInt(jQuery(this).val(),10));
        });

		if(type===1)
		{
			if(checked.length===0)
			{
				checkbox.prop('checked',false);
				checkbox.first().prop('checked',true);
			}
		}
		else if(type===2)
		{
			if(parseInt(checked.length,10)===0)
			{
				jQuery(this).prop('checked',true);
			}
		}
		
		checkbox.button('refresh');
    });    
};

/******************************************************************************/

function toTogglePriceType(radioSelector,priceTypeSectionSelector)
{
    jQuery(radioSelector).on('change',function()
    {
        var value=parseInt(jQuery(this).val());
        
        var section= jQuery(priceTypeSectionSelector).find('.to-price-type-1,.to-price-type-2');
        
        section.removeClass('to-state-disabled');
        section.find('input').removeAttr('disabled');
        
        section=jQuery(priceTypeSectionSelector).find('.to-price-type-'+(value===1 ? 2 : 1));
        
        section.addClass('to-state-disabled');
        section.find('input').attr('disabled','disabled');
    });
};

/******************************************************************************/
/******************************************************************************/