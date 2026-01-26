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
		
        var $map;
        
        var $directionsRenderer;
        var $directionsService;
        
        var $startLocation=null;

		var $optionDefault;
		var $option=$.extend($optionDefault,option);

		/**********************************************************************/
		
        this.init=function()
        {            
            if(navigator.geolocation) 
            {
                navigator.geolocation.getCurrentPosition(function(position)
                {
                    $startLocation=new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
                    
                    if(!$self.getCoordinate().length)
                        $map.setCenter($startLocation);
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
                $map.setCenter($startLocation);           
        };
        
        /**********************************************************************/
        
		this.create=function()
		{
            $directionsRenderer=new google.maps.DirectionsRenderer();
            $directionsService=new google.maps.DirectionsService();
            
            var option= 
            {
                zoom:6,
                mapTypeId:google.maps.MapTypeId.ROADMAP
            };
        
            $map=new google.maps.Map(document.getElementById('to-google-map'),option);
            
            $directionsRenderer.setMap($map);

            $directionsRenderer.setOptions(
            {
                draggable   :  false
            });
        };

        /**********************************************************************/
        
        this.createRoute=function() 
        {            
            var coordinate=$self.getCoordinate();
           
            if(coordinate.length===0)
            {
                $directionsRenderer.setDirections({routes:[]});
                $map.setZoom(6);
                
                if($startLocation!==null)
                {
                    $map.setCenter($startLocation);
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
                    waypoints:waypoint,
                    optimizeWaypoints:true,
                    destination:c[length-1],
                    travelMode:google.maps.DirectionsTravelMode.DRIVING
                };                     
            }
            else if(length===2)
            {
                request= 
                {
                    origin:c[0],
                    destination:c[length-1],
                    travelMode:google.maps.DirectionsTravelMode.DRIVING
                };          
            }
            else
            {
                request= 
                {
                    origin:c[length-1],
                    destination:c[length-1],
                    travelMode:google.maps.DirectionsTravelMode.DRIVING
                };              
            }
            
            $directionsService.route(request,function(response,status)
            {
                if(status===google.maps.DirectionsStatus.OK)
                {
                    $directionsRenderer.setDirections(response);
                    $('#chbs_coordinate').val(JSON.stringify(coordinate));
                    $map.setCenter($directionsRenderer.getDirections().routes[0].bounds.getCenter());
                }
                else if(status===google.maps.DirectionsStatus.ZERO_RESULTS)
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
                    
                    $directionsRenderer.setDirections({routes:[]});
                    $map.setZoom(6);
                
                    if($startLocation!==null) $map.setCenter($startLocation);
                    
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
                    'data-lat'                                                  :   place.geometry.location.lat(),
                    'data-lng'                                                  :   place.geometry.location.lng()
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