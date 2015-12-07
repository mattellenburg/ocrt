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

        var existingroute = false;
        if (<?php if (sizeof($routewaypoints)>0) { echo 1; } else { echo 0; } ?>) {
            waypoints = loadExistingWaypoints();
            existingroute = displayExistingRoute(waypoints, directionsService, directionsDisplay);
        }
        else {
            google.maps.event.addListener(map, 'click', function(event) {
                var latlng = event.latLng.lat() + ', ' + event.latLng.lng();
                var waypoint = {location: latlng, stopover: true, title: '', titlelink: '', pointid: 0};

                displayRoute(waypoints, waypoint, directionsService, directionsDisplay);
            });	
        }

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
                
                if (!existingroute) {
                    marker.addListener('click', function() {
                        var latlng = center.lat.toString() + ', ' + center.lng.toString();
                        var waypoint = {location: latlng, stopover: true, title: '', titlelink: '', pointid: 0};

                        displayRoute(waypoints, waypoint, directionsService, directionsDisplay);
                    });
                }
            });
        }
                  
        directionsDisplay.addListener('directions_changed', function() {
            displayRouteInformation(directionsDisplay.getDirections(), waypoints, existingroute);
        });

        var locations = getLocations();

        var marker, i;

        for (i = 0; i < locations.length; i++) { 
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                icon: getImage(locations[i][5], locations[i][4]),
                map: map
            });
            
            if (!existingroute) {
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        displayRoute(waypoints, {location: locations[i][1].toString() + ', ' + locations[i][2].toString(), stopover: true, title: ' - ' + locations[i][0], titlelink: ' - <a onClick="loadWorkoutInformation(' + parseInt(locations[i][6]) + ');" href="#">' + locations[i][0] + '</a>', pointid: locations[i][6]}, directionsService, directionsDisplay);
                    }
                })(marker, i));
            }
        }
    }
    
    function displayExistingRoute(waypoints, service, display) {
        var waypointparameters = createWaypointsParameters(waypoints);
        loadRoute(waypointparameters.origin, waypointparameters.destination, waypointparameters.waypoints, service, display);
        
        return true;
    }

    function displayRoute(waypoints, waypoint, service, display) {
        if (waypoints.length < 10) { 
            waypoints.push(waypoint);
            var waypointparameters = createWaypointsParameters(waypoints);
            loadRoute(waypointparameters.origin, waypointparameters.destination, waypointparameters.waypoints, service, display);
        }
        else {
            alert('You may only enter a maximum of 10 waypoints.');
        }
    }

    function createWaypointsParameters(waypoints) {
        var origin = waypoints[0].location;
        var destination = origin;
        var waypoints2 = [];
        
        if (waypoints.length > 1) {
            destination = waypoints[waypoints.length - 1].location;
        }
        if (waypoints.length > 2) {
            for(i=1; i<waypoints.length-1; i++) {
                waypoints2.push({location: waypoints[i].location, stopover: true});
            }
        }

        return {origin: origin, destination: destination, waypoints: waypoints2};
    }
    
    function loadRoute(origin, destination, waypoints, service, display) {
        service.route({
            origin: origin,
            destination: destination,
            waypoints: waypoints,
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
    
    function displayRouteInformation(result, waypoints, existing) {
        var total = 0;
        var myroute = result.routes[0];

        for (var i = 0; i < myroute.legs.length; i++) {
            total += myroute.legs[i].distance.value;

            if (existing) {
                if (i===0) {
                    document.getElementById('route').innerHTML = '<p><b>Total Distance: <span id="total"></span></b></p><p>Location A - 0.00 mi' + waypoints[0].titlelink + '</p>';
                }
                document.getElementById('route').innerHTML += '<p>Location ' + String.fromCharCode(97 + i + 1).toUpperCase() + ' - ' + parseFloat(myroute.legs[i].distance.value/1000*.621371).toFixed(2) + ' mi' + waypoints[i+1].titlelink + '</p>';
            }
            else {
                if (waypoints.length === 1) {
                    document.getElementById('route').innerHTML = '<p><b>Total Distance: <span id="total"></span></b></p><p>Location A - 0.00 mi' + waypoints[i].title + '</p>';
                }
                else if (waypoints.length === 2 || i === waypoints.length - 2) {
                    document.getElementById('route').innerHTML += '<p>Location ' + String.fromCharCode(97 + i + 1).toUpperCase() + ' - ' + parseFloat(myroute.legs[i].distance.value/1000*.621371).toFixed(2) + ' mi' + waypoints[i+1].title + '</p>';
                }
            }
        }

        var route='';
        for (var i = 0; i < waypoints.length; i++) {
            route += waypoints[i].location + ',' + waypoints[i].pointid + ';';
        }

        if (waypoints.length > 1 && !existing && <?php if (isset($_SESSION['user_id'])) { echo 1; } else { echo 0; } ?>) {
            document.getElementById('total').innerHTML = parseFloat(total/1000*.621371).toFixed(2) + ' mi <input type="text" id="routename" name="routename" placeholder="Enter a route name"><input type="submit" id="submitroute" name="submitroute" value="Submit Route" /><input type="hidden" name="route" id="route" value="' + route + '" /></form>';
        }
        else if (waypoints.length > 1) {         
            document.getElementById('total').innerHTML = parseFloat(total/1000*.621371).toFixed(2) + ' mi';
        }
    }
    
    function loadWorkoutInformation(pointid) {
        var locations = getLocations();
        var location = [];
        for (var i=0; i < locations.length; i++) {
            if (parseInt(locations[i][6]) === pointid) {
                location = {id: locations[i][6], title: locations[i][0], description: locations[i][3]};
            }
        }

        var workouts = [];
        <?php foreach ($workouts as $pointworkouts): ?>
            <?php foreach ($pointworkouts as $workout): ?>
                <?php if (sizeof($workout) > 0): ?>
                    if (pointid === parseInt(" <?php echo $workout->pointid; ?> ")) {
                        workouts.push(" <?php echo $workout->workout; ?> ");
                    }
                <?php endif ?>
            <?php endforeach;?>
        <?php endforeach;?>
    
        document.getElementById('workout').innerHTML = '<h3>' + location.title + '</h3>';
        document.getElementById('workout').innerHTML += '<p><input type="hidden" name="pointid" id="pointid" value="' + location.id + '"/><textarea id="workoutdescription" name="workoutdescription" style="width: 100%"></textarea><input type="submit" name="submitworkout" id="submitworkout" value="Create Workout" /></p>';
        
        for (var i=0; i<workouts.length; i++) {
            document.getElementById('workout').innerHTML += '<h4>Workout #' + (i+1).toString() + '</h4>';
            document.getElementById('workout').innerHTML += '<p>' + workouts[i] + '</p>';
        }
    }
    
    function loadExistingWaypoints() {
        var waypoints = [];
        <?php foreach ($routewaypoints as $routewaypoint): ?>
            var title = '';
            var titlelink = '';

            if (parseInt(" <?php echo $routewaypoint->pointid ?> ") > 0) {
                titlelink = ' - <a onClick="loadWorkoutInformation(' + parseInt(" <?php echo $routewaypoint->pointid ?> ") + ');" href="#">' + " <?php echo $routewaypoint->title ?> " + '</a>';
            }
            waypoints.push({location: <?php echo $routewaypoint->latitude; ?> + ', ' + <?php echo $routewaypoint->longitude; ?>, stopover: true, title: ' - ' + title, titlelink: titlelink});
        <?php endforeach; ?>   

        return waypoints;
    }
    
    google.maps.event.addDomListener(window, 'load', initialize); 
</script>
