<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<style type="text/css" media="screen">
    #map_wrapper {
    height: 400px;
}

#map_canvas {
    width: 100%;
    height: 100%;
}
</style>

<script>

$(function() {
    // Asynchronously Load the map API 
    var script = document.createElement('script');
    script.src = "//maps.googleapis.com/maps/api/js?callback=initialize";
    document.body.appendChild(script);
});

function getColor(value) {
    // #337ab7 //notice
    // #5cb85c //success
    // #5bc0de //info
    // #f0ad4e //warn
    // #d9534f //dang 

    value = (parseInt(value) / 10000) * 100;
    if(0 < value && 25 >= value) {
        return '#5cb85c'; //
    } else if(25 < value && 50 >= value) {
        return '#5bc0de';
    } else if(50 < value && 75 >= value) {
        return '#f0ad4e';
    } else if(75 < value ) {
        return '#d9534f';
    } else {
        return '#337ab7'
    }
}

function initialize() {
    var map;
    var bounds = new google.maps.LatLngBounds();
    var mapOptions = {
        mapTypeId: 'roadmap',
         zoom: 13,
        center: {lat: 40.657172, lng: -4.70512}
    };
                    
    // Display a map on the page
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    map.setTilt(45);
   
//     oMarker = new google.maps.Marker({
//     position: {lat: 40.657100054346, lng: -4.7050251627066},
//     sName: "Marker Name",
//     map: map,
//     icon: {
//         path: google.maps.SymbolPath.CIRCLE,
//         scale: 8.5,
//         fillColor: "#F00",
//         fillOpacity: 0.4,
//         strokeWeight: 0.4
//     },
// });


    var markers = [];
    var infoWindowContent = [];
    $.ajax({
        url: 'http://cotwo-api.com/v1/regions',
        method: 'GET',
        dataType: 'json',
        async: true,
        success: function (data) {      
            // Region
            var regionF = data.region;
            var region = [
                {lat: regionF.B[0], lng: regionF.B[1]},
                {lat: regionF.A[0], lng: regionF.A[1]},
                {lat: regionF.C[0], lng: regionF.C[1]},
                {lat: regionF.D[0], lng: regionF.D[1]}
            ];

            var polyRegion = new google.maps.Polygon({
              paths: region,
              strokeColor: '#ababab',
              strokeOpacity: 0.8,
              strokeWeight: 3,
              fillColor: '#ababab',
              fillOpacity: 0.35
            });
            polyRegion.setMap(map);


            var regionLines = data.dividers;
            var regionL = [
                {lat: regionLines.A2[0], lng: regionLines.A2[1]},
                {lat: regionLines.A1[0], lng: regionLines.A1[1]},

                {lat: regionLines.B1[0], lng: regionLines.B1[1]},
                {lat: regionLines.B2[0], lng: regionLines.B2[1]}, 

                {lat: regionLines.C2[0], lng: regionLines.C2[1]},
                {lat: regionLines.C1[0], lng: regionLines.C1[1]},
              
                {lat: regionLines.D1[0], lng: regionLines.D1[1]},
                {lat: regionLines.D2[0], lng: regionLines.D2[1]},

                {lat: regionLines.X2[0], lng: regionLines.X2[1]},
                {lat: regionLines.X1[0], lng: regionLines.X1[1]},

                {lat: regionLines.Y1[0], lng: regionLines.Y1[1]},
                {lat: regionLines.Y2[0], lng: regionLines.Y2[1]},

                {lat: regionLines.W2[0], lng: regionLines.W2[1]},
                {lat: regionLines.W1[0], lng: regionLines.W1[1]},


                {lat: regionLines.Z1[0], lng: regionLines.Z1[1]},
                {lat: regionLines.Z2[0], lng: regionLines.Z2[1]},
                
               
            ];

            var polyRegionL = new google.maps.Polygon({
              paths: regionL,
              strokeColor: '#000',
              strokeOpacity: 0.5,
              strokeWeight: 1,
              fillColor: '#000',
              fillOpacity: 0
            });
            polyRegionL.setMap(map);
        }
    });
    //     $.ajax({
    //     url: 'http://cotwo-api.com/v1/sensors',
    //     method: 'GET',
    //     dataType: 'json',
    //     async: true,
    //     success: function (data) {      
    //     // Sensors
    //         $.each(data, function (index, value) {
    //             markers.push([index, value.lat,value.long]);
    //             infoWindowContent.push([
    //                 '<div class="info_content">' +
    //                 '<h3>'+index+'</h3>' +
    //                 '<p>Valor promedio: '+index+' ppm</p>' +        '</div>'
    //             ]);
    //         });
    //     }
    // });
       markers = [];
       markerssource = [];
       markerssource2 = [];
    $.ajax({
        url: 'http://cotwo-api.com/v1/sensors',
        method: 'GET',
        dataType: 'json',
        async: false,
        success: function (data) {      
        // Sensors
            $.each(data, function (index, value) {
                markers.push([index, value.lat,value.long]);
                infoWindowContent.push([
                    '<div class="info_content">' +
                    '<h3>'+index+'</h3></div>'
                ]);
            });
        }
    });
    var infoWindow = new google.maps.InfoWindow(), marker, i;
    
    // Loop through our array of markers & place each one on the map  
    for( i = 0; i < markers.length; i++ ) {
        var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
        bounds.extend(position);
        marker = new google.maps.Marker({
            position: position,
            map: map,
            title: markers[i][0]
        });
        
        // Allow each marker to have an info window    
        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infoWindow.setContent(infoWindowContent[i][0]);
                infoWindow.open(map, marker);
            }
        })(marker, i));

        // Automatically center the map fitting all markers on the screen
        // map.fitBounds(bounds);
    }
setInterval(function(){
       // polyActiveSensor.setMap(null); 

        // Multiple Markers
        $.ajax({
            url: 'http://cotwo-api.com/v1/movements',
            method: 'GET',
            dataType: 'json',
            async: true,
            success: function (data) {

                // var activeSensors = data.active_sensors['0001'].quadrants.sub_quads[0].sub_quad;
                $.each(data.active_sensors, function (index, value) {
                    if ('quadrants' in value) {
                        // console.log(value)
                        var activeSensors = value.quadrants.sub_quads
                        $.each(activeSensors, function (index2, active) {
                             activeSensorsArea = [
                                {lat: active.sub_quad[0][0], lng: active.sub_quad[0][1]},
                                {lat: active.sub_quad[1][0], lng: active.sub_quad[1][1]},
                                {lat: active.sub_quad[2][0], lng: active.sub_quad[2][1]},
                                {lat: active.sub_quad[3][0], lng: active.sub_quad[3][1]}
                            ];
                            var stroke = getColor(value.group.avg);
                             var polyActiveSensor = new google.maps.Polygon({
                              paths: activeSensorsArea,
                              strokeColor: stroke,
                              strokeOpacity: 0.8,
                              strokeWeight: 3,
                              fillColor: stroke,        
                              fillOpacity: 0.35
                            });
                            polyActiveSensor.setMap(map);
                            setTimeout(function(){ polyActiveSensor.setMap(null); }, 3000);
                        });
                    }
                });

                if (data.source_ubication.source_events.source_location != null) {
                    $.each(data.source_ubication.source_events.source_location, function (index, value) {
                        markerssource.push([index, value[0],value[1]]);
                        infoWindowContent.push([
                            '<div class="info_content">' +
                            '<h3>'+index+'</h3></div>'
                        ]);
                    });

                    // var marker = new google.maps.Marker({
                    //   position: { 
                    //     'lat': data.source_ubication.source_events.source_location[0], 
                    //     'lng':data.source_ubication.source_events.source_location[1]
                    //     },
                    //   map: map,
                    //   icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
                    //   title: 'Hello World!'
                    // });
               
                    // var infowindow = new google.maps.InfoWindow({
                    //   content: 'Informacion'
                    // });

                    // marker.addListener('click', function() {
                    //   infowindow.open(map, marker);
                    // });
                  
                    // setTimeout(function(){   markers.setMap(null); }, 3000);
                }

                 polyActiveSensor = null;
                    data = null;
            }
        });



    var infoWindowSource = new google.maps.InfoWindow(), marker, i;
    // Loop through our array of markers & place each one on the map  
    for( i = 0; i < markerssource.length; i++ ) {
        var position = new google.maps.LatLng(markerssource[i][1], markerssource[i][2]);
        bounds.extend(position);
        marker = new google.maps.Marker({
            position: position,
            map: map,
            icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
            // title: markerssource[i][0]
        });
    setTimeout(function(){  marker.setMap(null); }, 100);
      
        // Allow each marker to have an info window    
        // google.maps.event.addListener(marker, 'click', (function(marker, i) {
        //     return function() {
        //         infoWindowSource.setContent(infoWindowContent[i][0]);
        //         infoWindowSource.open(map, marker);
        //     }
        // })(marker, i));

        // Automatically center the map fitting all markers on the screen
        // map.fitBounds(bounds);
    }   


    //  for( i = 0; i < markerssource.length; i++ ) {
    //     map.removeOverlay(markerssource[i]);
       
    //     // Allow each marker to have an info window    
    //     // google.maps.event.addListener(marker, 'click', (function(marker, i) {
    //     //     return function() {
    //     //         infoWindowSource.setContent(infoWindowContent[i][0]);
    //     //         infoWindowSource.open(map, marker);
    //     //     }
    //     // })(marker, i));

    //     // Automatically center the map fitting all markers on the screen
    //     map.fitBounds(bounds);
    // }   

    //  setTimeout(function(){ 
    //   markerssource = [];
    // }, 3000);
 
}, 3000);
     
    // Display multiple markers on a map
    var infoWindow = new google.maps.InfoWindow(), marker, i;
    
    // Loop through our array of markers & place each one on the map  
    for( i = 0; i < markers.length; i++ ) {
        var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
        bounds.extend(position);
        marker = new google.maps.Marker({
            position: position,
            map: map,
            title: markers[i][0]
        });
        
        // Allow each marker to have an info window    
        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infoWindow.setContent(infoWindowContent[i][0]);
                infoWindow.open(map, marker);
            }
        })(marker, i));

        // Automatically center the map fitting all markers on the screen
        map.fitBounds(bounds);
    }

    // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
        this.setZoom(19);
        google.maps.event.removeListener(boundsListener);
    });

}

  
</script>
<div id="map_wrapper">
    <div id="map_canvas" class="mapping"></div>
</div>