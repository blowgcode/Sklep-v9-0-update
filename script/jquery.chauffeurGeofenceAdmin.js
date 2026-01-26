/******************************************************************************/
/******************************************************************************/

;(function($,doc,win) 
{
	"use strict";
	
	var ChauffeurGeofenceAdmin=function(object,option)
	{
		/**********************************************************************/
		
        var $self=this;
		
        var $map;
        
        var $drawingManager;
        
        var $startLocation=null;

		var $optionDefault;
		var $option=$.extend($optionDefault,option);

        var $shapeSelected;
        var $shapeCoordinate;
        
		/**********************************************************************/
		
        this.init=function()
        {       
            $self.createMap();
            var draw=$self.createDraw();
            
            if(navigator.geolocation) 
            {
                navigator.geolocation.getCurrentPosition(function(position)
                {
					if(!draw)
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
            
            $self.createAutoComplete($('input[name="chbs_google_map_autocomplete"]'));
        };
        
        /**********************************************************************/
        
        this.useDefaultLocation=function()
        {
            $startLocation=new google.maps.LatLng($option.coordinate.lat,$option.coordinate.lng);
            $map.setCenter($startLocation);           
        };
        
        /**********************************************************************/
        
		this.createMap=function()
		{
            var option= 
            {
                zoom:6,
                mapTypeId:google.maps.MapTypeId.ROADMAP
            };
        
            $map=new google.maps.Map(document.getElementById('to-google-map'),option);
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
 
                var location=new google.maps.LatLng(place.geometry.location.lat(),place.geometry.location.lng());
                
                $map.setCenter(location); 
                $map.setZoom(10); 
            });                       
        };
        
        /**********************************************************************/
        
        this.addEvent=function(shape)
        {
            google.maps.event.addListener(shape.getPath(),'insert_at',function() 
            {
                $self.shapeCoordinateGet(shape);
            });
            google.maps.event.addListener(shape.getPath(),'set_at',function()
            {
                $self.shapeCoordinateGet(shape);
            });
            google.maps.event.addListener(shape.getPath(),'remove_at',function() 
            {
                $self.shapeCoordinateGet(shape);
            });
            google.maps.event.addListener(shape,'click',function()
            {
                $self.shapeSelect(shape);
            }); 
            google.maps.event.addListener(shape,'drag',function()
            {
                $self.shapeCoordinateGet(shape);
            });           
        };
        
        /**********************************************************************/
        
        this.shapeSelect=function(shape)
        {
            $self.shapeUnselect();
            $shapeSelected=shape;
            shape.setEditable(true);       
        };
        
        /**********************************************************************/
        
        this.shapeUnselect=function()
        {
            if($shapeSelected)
            {
                $shapeSelected.setEditable(false);
                $shapeSelected=null;
            }        
        };
        
        /**********************************************************************/
        
        this.shapeRemove=function()
        {
            if($shapeSelected)
            {
                delete $shapeCoordinate[$shapeSelected.id];
                $self.shapeCoordinateFieldSet();
                $shapeSelected.setMap(null);
                $self.shapeUnselect();
            }
        };
        
        /**********************************************************************/
        
        this.shapeCoordinateFieldSet=function()
        {
            $('#chbs_shape_coordinate').val(JSON.stringify($shapeCoordinate));
        };
        
        /**********************************************************************/
        
        this.shapeCoordinateGet=function(shape)
        {
            var id=shape.id;
            var length=shape.getPath().getLength();
                
            $shapeCoordinate[id]=[];
                
            for(var i=0;i<length;i++) 
                $shapeCoordinate[id][i]={lat:shape.getPath().getAt(i).lat(),lng:shape.getPath().getAt(i).lng()};

            $self.shapeCoordinateFieldSet();
        };        
        
        /**********************************************************************/
        
        this.createDraw=function()
        {
			var draw=false;
			
            var polygonOption=
            {
                clickable:true,
                draggable:true,
                editable:true,
                fillColor:'#000000',
                fillOpacity:0.5,
                map:$map
            };
    
            $shapeCoordinate={};
            
            try
            {
                var bounds=new google.maps.LatLngBounds();
                
                $shapeCoordinate=JSON.parse($('#chbs_shape_coordinate').val());
			
                for(var i in $shapeCoordinate)
                {
                    for(var j in $shapeCoordinate[i])
					{
                        bounds.extend(new google.maps.LatLng($shapeCoordinate[i][j].lat,$shapeCoordinate[i][j].lng));
					}
					
                    var polygon=new google.maps.Polygon(polygonOption);
                    
                    polygon.setPaths($shapeCoordinate[i]);
                    
                    polygon.id=i;
       
                    $self.addEvent(polygon);
					
					draw=true;
                }
 
                $map.fitBounds(bounds);
                
            }
            catch(e)
            {
                $shapeCoordinate={};
            }
            
            if($shapeCoordinate.length===0)
                $shapeCoordinate={};
            
            $drawingManager=new google.maps.drawing.DrawingManager(
            {
                drawingMode:google.maps.drawing.OverlayType.POLYGON,
                drawingControl:true,
                drawingControlOptions:
                {
                    position:google.maps.ControlPosition.TOP_CENTER,
                    drawingModes:[google.maps.drawing.OverlayType.POLYGON]
                },
                markerOptions:
                {
                    icon:''
                },
                polygonOptions:polygonOption
            });  
            
            $drawingManager.setMap($map);
                        
            /***/
            
            google.maps.event.addListener($map,'polygoncomplete',function(event) 
            {
                $self.shapeCoordinateGet(event.getPath());
                
                google.maps.event.addListener(event.getPath(),'insert_at',function() 
                {
                    $self.shapeCoordinateGet(event);
                });
                google.maps.event.addListener(event.getPath(),'set_at',function()
                {
                    $self.shapeCoordinateGet(event);
                });
                google.maps.event.addListener(event.getPath(),'remove_at',function() 
                {
                    $self.shapeCoordinateGet(event);
                });
            });
            
            google.maps.event.addListener($drawingManager,'overlaycomplete',function(event)
            {
                if(event.type!==google.maps.drawing.OverlayType.MARKER)
                {
                    var helper=new CHBSHelper();
                    
                    $drawingManager.setDrawingMode(null);
           
                    var shape=event.overlay;
                    
                    shape.type=event.type;
                    shape.id=helper.getRandomString(16);
                    
                    google.maps.event.addListener(shape,'click',function()
                    {
                        $self.shapeSelect(shape);
                    });                    
                    
                    google.maps.event.addListener(shape,'drag',function() 
                    {
                        $self.shapeCoordinateGet(shape);
                    });
                    
                    $self.shapeSelect(shape);
                }
                
                $self.shapeCoordinateGet(shape);
            });
            
            $('#chbs_shape_remove').on('click',function(e)
            {
                e.preventDefault();
                $self.shapeRemove();
            });
			
			return(draw);
        };
        
        /**********************************************************************/
	};
	
	/**************************************************************************/
	
	$.fn.chauffeurGeofenceAdmin=function(option) 
	{       
		var chauffeurGeofenceAdmin=new ChauffeurGeofenceAdmin(this,option);
		return(chauffeurGeofenceAdmin);
	};
	
	/**************************************************************************/

})(jQuery,document,window);

/******************************************************************************/
/******************************************************************************/