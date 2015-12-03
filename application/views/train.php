<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="train">
    <h2>Train</h2>
    <div class="content">
        <?php /*var_dump($points);*/ ?>
        <div id="map"></div>
        <div id="right-panel"></div>
    </div>
    <div class="content">
        <h3>Obstacles & Exercises <button id="all">Toggle All</button></h3>
        <div class="tree">
            <?php echo ul($obstacles); ?>
        </div>
    </div>
</div>

<script>
    function initMap() {        
        var waypoints = [];

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 13
        });

        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer;
        directionsDisplay.setMap(map);
//        var directionsDisplay = new google.maps.DirectionsRenderer({
//            draggable: true,
//            map: map,
//            panel: document.getElementById('right-panel')
//        });

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
                document.getElementById('right-panel').innerHTML = '<p>Total Distance: <span id="total"></span></p><p>A - 0.00 mi</p>';
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
                icon: getImage(locations[i][4]),
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

    function getImage(icon) {
        if (icon == 1) {
            image = " <?php echo base_url('assets/images/Playground-50.png') ?> ";
        }
        else if (icon == 2) {
            image = " <?php echo base_url('assets/images/Pullups Filled-50.png') ?> ";
        }
        else if (icon == 3) {
            image = " <?php echo base_url('assets/images/City Bench-50.png') ?> ";
        }
        else if (icon == 4) {
            image = " <?php echo base_url('assets/images/Weight-50.png') ?> ";
        }
        else if (icon == 5) {
            image = " <?php echo base_url('assets/images/Pushups-50.png') ?> ";
        }
        else if (icon == 6) {
            image = " <?php echo base_url('assets/images/Stadium-50.png') ?> ";
        }
        else if (icon == 7) {
            image = " <?php echo base_url('assets/images/Trekking-50.png') ?> ";
        }
        else if (icon == 8) {
            image = " <?php echo base_url('assets/images/Climbing Filled-50.png') ?> ";
        }
        else if (icon == 9) {
            image = " <?php echo base_url('assets/images/Wakeup Hill on Stairs-50.png') ?> ";
        }
        else {
            image = " <?php echo base_url('assets/images/Marker-50.png') ?> ";
        }
        
        return image;
    }

    function computeTotalDistance(result, waypoints) {
        var total = 0;
        var myroute = result.routes[0];

        for (var i = 0; i < myroute.legs.length; i++) {
            //var_dump(myroute.legs[i], document.getElementById('right-panel'));
            total += myroute.legs[i].distance.value;
            if (parseInt(i) === parseInt(myroute.legs.length - 1)) {
                document.getElementById('right-panel').innerHTML += '<p>' + String.fromCharCode(97 + i + 1).toUpperCase() + ' - ' + parseFloat(myroute.legs[i].distance.value/1000*.621371).toFixed(2) + ' mi</p>';
            }
        }
        
        document.getElementById('total').innerHTML = parseFloat(total/1000*.621371).toFixed(2) + ' mi';
    }
    
function var_dump(obj, element)
{
    var logMsg = objToString(obj, 0);
    if (element) // set innerHTML to logMsg
    {
        var pre = document.createElement('pre');
        pre.innerHTML = logMsg;
        element.innerHTML = '';
        element.appendChild(pre);
    }
    else // write logMsg to the console
    {
        console.log(logMsg);
    }
}

function objToString(obj, level)
{
    var out = '';
    for (var i in obj)
    {
        for (loop = level; loop > 0; loop--)
        {
            out += "    ";
        }
        if (obj[i] instanceof Object)
        {
            out += i + " (Object):\n";
            out += objToString(obj[i], level + 1);
        }
        else
        {
            out += i + ": " + obj[i] + "\n";
        }
    }
    return out;
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCLrhs-LKivwuYlINaomHmKyINdWKx5Z-Y&callback=initMap" async defer></script>