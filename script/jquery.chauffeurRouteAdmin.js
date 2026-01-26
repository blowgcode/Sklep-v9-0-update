/******************************************************************************/
/******************************************************************************/

;(function($,doc,win) 
{
	"use strict";
	
	var ChauffeurRouteAdmin=function(object,option)
	{
		/**********************************************************************/
		
        var $self=this;
		var $this=$(object);
		
        var $googleMap;
        
        var $startLocation=null;

		var $optionDefault;
		var $option=$.extend($optionDefault,option);
		
		var $GoogleMapAPI;
		
		var $marker=[];

		/**********************************************************************/
		
        this.init=function()
        {          
			$GoogleMapAPI=new CHBSGoogleMapAPI($option.google_map_api_key);
			
            if(navigator.geolocation) 
            {
                navigator.geolocation.getCurrentPosition(function(position)
                {
                    $startLocation=new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
                    
                    if(!$self.getCoordinate().length)
                        $googleMap.setCenter($startLocation);
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
            
            $('#to-table-route tr:gt(1)').each(function()
            {
                $self.createAutoComplete($(this).find('input[type="text"]'));
            });
        };
        
        /**********************************************************************/
        
        this.useDefaultLocation=function()
        {
            $startLocation=new google.maps.LatLng($option.coordinate.lat,$option.coordinate.lng);
            
            if(!$self.getCoordinate().length)
                $googleMap.setCenter($startLocation);           
        };
        
        /**********************************************************************/
        
		this.create=function()
		{            
            var option= 
            {
                zoom:6,
				mapId:$option.google_map_map_id,
                mapTypeId:google.maps.MapTypeId.ROADMAP
            };
        
            $googleMap=new google.maps.Map(document.getElementById('to-google-map'),option);
        };
		
		/**********************************************************************/
		
		this.createMarker=function(position)
		{	
			position=new google.maps.LatLng(position.latLng.latitude,position.latLng.longitude);

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
		
		this.clearMarker=function()
		{
			for(var i in $marker)
				$marker[i].setMap(null);
			
			$marker=[];
		};

        /**********************************************************************/
        
        this.createRoute=function() 
        {       
            var coordinate=$self.getCoordinate();
           
            if(coordinate.length===0)
            {
                $googleMap.setZoom(6);
                
                if($startLocation!==null)
                {
                    $googleMap.setCenter($startLocation);
                }
                
                $('#chbs_coordinate').val('');
                
                return;
            }
            
            var request;
            var length=coordinate.length;
            
            var c=[];
            for(var i in coordinate)
                c.push(new google.maps.LatLng(coordinate[i]['lat'],coordinate[i]['lng']));
            
            if(length>2)
            {
                var waypoint=new Array();
                for(var i in c)
                {
                    if((i===0) && (i===length-1)) continue;
                    waypoint.push({location:c[i],stopover:true});
                }
                
                request= 
                {
                    origin:c[0],
                    intermediates:waypoint,
                    destination:c[length-1],
                    travelMode:'drive'
                };                     
            }
            else if(length===2)
            {
                request= 
                {
                    origin:c[0],
                    destination:c[length-1],
                    travelMode:'drive'
                };          
            }
            else
            {
                request= 
                {
                    origin:c[length-1],
                    destination:c[length-1],
                    travelMode:'drive'
                };              
            }
            
			$GoogleMapAPI.computeRoutes(request,function(response)
			{
				$self.clearMarker();
				
				if($GoogleMapAPI.hasRoute(response))
                {	
                    $('#chbs_coordinate').val(JSON.stringify(coordinate));
			
					for(var i in response.routes[0].legs)
					{
						var leg=response.routes[0].legs[i];

						$self.createMarker(leg.startLocation);
						$self.createMarker(leg.endLocation); 
					}		
		
					$googleMap.fitBounds($GoogleMapAPI.getBound(response));
					
					$GoogleMapAPI.drawRoute(response,0,$googleMap);			
                }
                else
                {
                    alert($option.message.designate_route_error);
                       
                    var i=0;
                       
                    $('#to-table-route tr:gt(1)').each(function()
                    {
                        i++;
                        
                        if(i===1) 
                        {
                            $(this).find('input[type="text"]').val('');
                            $(this).removeAttr('data-lat').removeAttr('data-lng');
                        }
                        else $(this).remove();
                    });
                    
                    $googleMap.setZoom(6);
                
                    if($startLocation!==null) $googleMap.setCenter($startLocation);
                    
                    $('#chbs_coordinate').val('');
                
                    return;
                }
            });
        };
        
        /**********************************************************************/
        
        this.createAutoComplete=function(text)
        {
            var id=(new CHBSHelper()).getRandomString(16);
                
            text.attr('id',id).on('keypress',function(e)
            {
                if(e.which===13)
                {
                    e.preventDefault();
                    return(false);
                }
            });
            
            var autocomplete=new google.maps.places.Autocomplete(document.getElementById(id));
            autocomplete.addListener('place_changed',function()
            {
                var place=autocomplete.getPlace();
           
                text.parents('tr').attr(
                {
                    'data-lat':place.geometry.location.lat(),
                    'data-lng':place.geometry.location.lng()
                });
                
                $self.setAddress(text.parents('tr'),function()
                {
                    $self.create();
                    $self.createRoute();
                });
            });                       
        };
        
        /**********************************************************************/
        
        this.getCoordinate=function()
        {
            var helper=new CHBSHelper();
            var coordinate=new Array();
            
            $('#to-table-route tr:gt(1)').each(function()
            {
                var lat=$(this).attr('data-lat');
                var lng=$(this).attr('data-lng');
                var address=$(this).attr('data-address');

                if(!(helper.isEmpty(lat) && helper.isEmpty(lng)))
                    coordinate.push({lat:lat,lng:lng,address:$self.removeDoubleQuote(address)});
            });
            
            return(coordinate);
        };
        
        /**********************************************************************/
        
        this.setAddress=function(field,callback)
        {
            var helper=new CHBSHelper();
            
            var lat=field.attr('data-lat');
            var lng=field.attr('data-lng');
            
            if((helper.isEmpty(lat)) || (helper.isEmpty(lng))) return;
            
            var geocoder=new google.maps.Geocoder;
            
            geocoder.geocode({'location':new google.maps.LatLng(lat,lng)},function(result,status) 
            {
                if((status==='OK') && (result[0]))
                {
                    field.attr('data-address',result[0].formatted_address);
                    callback();
                }
            });            
        };
        
        /**********************************************************************/
        
        this.removeDoubleQuote=function(value)
        {
            return(value.replace(/"/g,''));
        };
        
        /**********************************************************************/
	};
	
	/**************************************************************************/
	
	$.fn.chauffeurRouteAdmin=function(option) 
	{       
		var chauffeurRouteAdmin=new ChauffeurRouteAdmin(this,option);
		return(chauffeurRouteAdmin);
	};
	
	/**************************************************************************/

})(jQuery,document,window);

/******************************************************************************/
/******************************************************************************/