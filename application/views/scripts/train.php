<?php include 'map.php';?>
<script>
    function initialize() { 
        var url = 'index.php/train/index/';
        var map = initializeMap(url);
        var sessionid = " <?php if (isset($_SESSION['user_id'])) { echo $_SESSION['user_id']; } else { echo 0; } ?> ";
        getMapCenter(url, map, sessionid);    

        var waypoints = [];

        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer;
        directionsDisplay.setMap(map);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                map.setCenter(new google.maps.LatLng(position.coords.latitude,position.coords.longitude));

                var center = new Array();       
                center.lat = map.getCenter().lat();
                center.lng = map.getCenter().lng();
                
                var marker = new google.maps.Marker({
                    position: center,
                    map: map
                });
                
                marker.addListener('click', function() {
                    var latlng = center.lat.toString() + ', ' + center.lng.toString();
                    var waypoint = {location: latlng, stopover: true};

                    displayRoute(waypoints, waypoint, directionsService, directionsDisplay);
                });
            });
        }
        
        google.maps.event.addListener(map, 'click', function(event) {
            var latlng = event.latLng.lat() + ', ' + event.latLng.lng();
            var waypoint = {location: latlng, stopover: true};

            displayRoute(waypoints, waypoint, directionsService, directionsDisplay);
        });	
  
        directionsDisplay.addListener('directions_changed', function() {
            if (waypoints.length === 1) {
                document.getElementById('route').innerHTML = '<p><b>Total Distance: <span id="total"></span></b></p><p>Location A - 0.00 mi</p>';
            }
            else if (waypoints.length > 1) {
                computeTotalDistance(directionsDisplay.getDirections(), waypoints);
            }
        });
        
        var locations = new Array();
        var i = 0;
        <?php if (isset($points)) { ?>
            <?php foreach ($points as $point): ?>
                var title = " <?php echo $point['title'] ?> ";
                var description = " <?php echo $point['description'] ?> ";
                var latitude = " <?php echo $point['latitude'] ?> ";
                var longitude = " <?php echo $point['longitude'] ?> ";
                var icon = " <?php echo $point['icon'] ?> ";
                var pointid = " <?php echo $point['id'] ?> ";

                var point = new Array(title, latitude, longitude, description, icon, pointid);

                locations[i] = point;

                i++;
            <?php endforeach; ?>
        <?php } ?>

        var marker, i;

        for (i = 0; i < locations.length; i++) { 
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                icon: getImage('', locations[i][4]),
                map: map
            });
            
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    var waypoint = {location: locations[i][1].toString() + ', ' + locations[i][2].toString(), stopover: true};
                    displayRoute(waypoints, waypoint, directionsService, directionsDisplay);
                }
            })(marker, i));
        }
    }

    function displayRoute(waypoints, waypoint, service, display) {
        if (waypoints.length < 10) { 
            var waypoints2;
            waypoints.push(waypoint); 

            var origin = waypoints[0].location;
            var destination = origin;
            if (waypoints.length > 1) {
                destination = waypoints[waypoints.length - 1].location;
            }
            if (waypoints.length > 2) {
                waypoints2 = waypoints.slice(1,waypoints.length - 1);
            }

            service.route({
                origin: origin,
                destination: destination,
                waypoints: waypoints2,
                travelMode: google.maps.TravelMode.WALKING
            }, function(response, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    display.setDirections(response);
                } 
                else {
                    alert('Could not display directions due to: ' + status);
                }
            });
        }
    }

    function computeTotalDistance(result, waypoints) {
        var total = 0;
        var myroute = result.routes[0];

        for (var i = 0; i < myroute.legs.length; i++) {
            total += myroute.legs[i].distance.value;
            
            if (parseInt(i) === parseInt(myroute.legs.length - 1)) {
                document.getElementById('route').innerHTML += '<p>Location ' + String.fromCharCode(97 + i + 1).toUpperCase() + ' - ' + parseFloat(myroute.legs[i].distance.value/1000*.621371).toFixed(2) + ' mi</p>';
            }
        }

        var route='';
        for (var i = 0; i < waypoints.length; i++) {
            route += waypoints[i].location + ';';
        }
        
        document.getElementById('total').innerHTML = parseFloat(total/1000*.621371).toFixed(2) + ' mi <input type="text" id="routename" name="routename" placeholder="Enter a route name"><input type="submit" id="submitroute" name="submitroute" value="Submit Route" /><input type="hidden" name="route" id="route" value="' + route + '" /></form>';
    }
    
    google.maps.event.addDomListener(window, 'load', initialize); 
</script>
