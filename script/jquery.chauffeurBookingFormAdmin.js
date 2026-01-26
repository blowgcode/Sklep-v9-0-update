/******************************************************************************/
/******************************************************************************/

;(function($,doc,win) 
{
	"use strict";
	
	var ChauffeurBookingFormAdmin=function(object,option)
	{
		/**********************************************************************/
		
        var $self=this;
		var $this=$(object);
		
        var $map;
        
        var $drawingManager;
        
        var $circle;
        
        var $startLocation;
        
		var $optionDefault;
		var $option=$.extend($optionDefault,option);
                
        /**********************************************************************/
        
        this.init=function()
        {            
            if(navigator.geolocation) 
            {
                navigator.geolocation.getCurrentPosition(function(position)
                {
                    var data=$self.getWaypointArea();
                    if(data===false)
                    {
                        $startLocation=new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
                        $map.setCenter($startLocation);
                    }
                },
                function()
                {
                    $self.useDefaultLocation();
                });
            } 
            else
            {
                $self.useDefaultLocation();
            } 
        };
        
        /**********************************************************************/
        
        this.useDefaultLocation=function()
        {
            var data=$self.getWaypointArea();
            
            if(data===false)
            {
                $startLocation=new google.maps.LatLng($option.userDefaultCoordinate.lat,$option.userDefaultCoordinate.lng);
                $map.setCenter($startLocation);
            }
        };
        
        /**********************************************************************/
        
		this.create=function()
		{
           
        };
        
        /**********************************************************************/
	};
	
	/**************************************************************************/
	
	$.fn.chauffeurBookingFormAdmin=function(option) 
	{      
        var chauffeurBookingFormAdmin=new ChauffeurBookingFormAdmin(this,option);
        return(chauffeurBookingFormAdmin);            
	};
	
	/**************************************************************************/

})(jQuery,document,window);

/******************************************************************************/
/******************************************************************************/