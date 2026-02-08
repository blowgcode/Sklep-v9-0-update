
/******************************************************************************/
/******************************************************************************/

function CHBSGoogleMapAPI($APIKey)
{
    /**************************************************************************/
    
    this.APIKey=$APIKey;
    
	/**************************************************************************/
	
	this.computeRoutes=function(data,callback)
	{
		this.request('computeRoutes',data,callback);
	};
	
	/**************************************************************************/

	this.request=function(name,data,callback)
	{
		var url;
		
		if(name==='computeRoutes')
		{
			url='https://routes.googleapis.com/directions/v2:computeRoutes';
		}	
		
		data.origin=this.transformCoordinate(data.origin);
		data.destination=this.transformCoordinate(data.destination);
	
		if(typeof(data.intermediates)!=='undefined')
		{
			for(var i in data.intermediates)
			{
				data.intermediates[i]=this.transformCoordinate(data.intermediates[i].location);
			}
		}
	
		data=JSON.stringify(data);
		
		jQuery.ajax(
		{
			url:url,
			data:data,
			type:'post',
			dataType:'json',
			contentType:'application/json',
			headers: 
			{
				'X-Goog-Api-Key':this.APIKey,
				'X-Goog-FieldMask':'routes.duration,routes.distanceMeters,routes.legs,routes.polyline.encodedPolyline'
			},
			success:function(response)
			{
				callback(response);
			}
		});
	};
	
	/**************************************************************************/
	
	this.transformCoordinate=function(coordinate)
	{
		var data={};

		data.location=
		{
			'latLng':
			{
				'latitude':coordinate.lat(),
				'longitude':coordinate.lng(),
			}
		};

		return(data);
	};
	
	/**************************************************************************/
	
	this.hasRoute=function(response)
	{
		if(typeof(response)==='undefined') return(false);
		
		return(response.hasOwnProperty('routes') ? true : false);
	};
	
	/**************************************************************************/
	
	this.getBound=function(response,routeIndex=0)
	{
		var bound=new google.maps.LatLngBounds();

		for(var i in response.routes[routeIndex].legs)
		{
			var leg=response.routes[routeIndex].legs[i];

			var coordinateStart=leg.startLocation.latLng;
			var coordinateEnd=leg.endLocation.latLng;

			bound.extend(new google.maps.LatLng(coordinateStart.latitude,coordinateStart.longitude));
			bound.extend(new google.maps.LatLng(coordinateEnd.latitude,coordinateEnd.longitude));
		}

		return(bound);
	}
		
	/**********************************************************************/
		
	this.drawRoute=function(response,routeIndex,googleMap)
	{
		var line=response.routes[routeIndex].polyline.encodedPolyline;

		var path=google.maps.geometry.encoding.decodePath(line);

		return new google.maps.Polyline(
		{
			path:path,
			strokeColor:'#60A9F2',
			strokeOpacity:1.0,
			strokeWeight:4,
			map:googleMap
		});		
	};
	
	/**************************************************************************/
};

/******************************************************************************/
/******************************************************************************/
