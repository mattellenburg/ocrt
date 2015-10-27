<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="explore">
    <div id="message">
        <p><?php echo $message ?><p>
    </div>
    <div id="instructions">
        <h2>Explore</h2>
        <p>Click on the map to submit a new location. Click on a point to get more information about that location.</p>
        <div id="googleMap">
        </div>
    </div>
    <div id="pointInformation">
        <h2>Information</h2>
    </div>
    <div id="filters">
        <?php if (isset($_SESSION['user_id'])) : ?>
            <h2>Filters</h2>
            <?php echo $filter ?>
            <?= form_open() ?>
            <p>
                Average Rating: 
                <input type="radio" id="filterrating" name="filterrating" value="1" />
                <input type="radio" id="filterrating" name="filterrating" value="2" />
                <input type="radio" id="filterrating" name="filterrating" value="3" />
                <input type="radio" id="filterrating" name="filterrating" value="4" />
                <input type="radio" id="filterrating" name="filterrating" value="5" />
                or higher
            </p>
            <p>Submitted by Me: <input type="checkbox" id="mysubmissions" name="mysubmissions"></p>
            <p>Title/Description Search: <input type="text" id="search" name="search" /></p>
            <p><input type="submit" value="Search" /></p>
            </form>
        <?php endif; ?>
    </div>
</div>

<script src="http://maps.googleapis.com/maps/api/js"></script>
<script>
function initialize() {
    var sessionid = " <?php if (isset($_SESSION['user_id'])) { echo $_SESSION['user_id']; } else { echo 0; } ?> ";
    var mapProp = {
        zoom:15,
        mapTypeId:google.maps.MapTypeId.ROADMAP
    };
    var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
                var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                };	

                loadPoints(map, sessionid);

                map.setCenter(pos);
        }, function() {
                handleLocationError(true, infoWindow, map.getCenter());
        });
    } 
    else {
        handleLocationError(false, infoWindow, map.getCenter());
    }

    if (sessionid > 0) {
        addMapClickEvent(map);
    }
}

function loadPoints(map, sessionid) {
    var locations = new Array();
    var i = 0;
    <?php foreach ($points as $point): ?>
        var title = " <?php echo $point['title'] ?> ";
        var description = " <?php echo $point['description'] ?> ";
        var latitude = " <?php echo $point['latitude'] ?> ";
        var longitude = " <?php echo $point['longitude'] ?> ";
        var icon = " <?php echo $point['icon'] ?> ";
        var type = 'confirmed';
        var pointid = " <?php echo $point['id'] ?> ";
        var userrating = " <?php echo $point['userrating'] ?> ";
        var avgrating = " <?php echo $point['avgrating'] ?> ";
        var keywords = " <?php echo $point['keywords'] ?> ";

        var point = new Array(title, latitude, longitude, description, icon, type, pointid, userrating, avgrating, keywords);

        locations[i] = point;

        i++;
    <?php endforeach; ?>

    <?php foreach ($points_pending as $point): ?>
        var title = " <?php echo $point['title'] ?> ";
        var description = " <?php echo $point['description'] ?> ";
        var latitude = " <?php echo $point['latitude'] ?> ";
        var longitude = " <?php echo $point['longitude'] ?> ";
        var icon = " <?php echo $point['icon'] ?> ";
        var type = 'pending';

        var point = new Array(title, latitude, longitude, description, icon, type);

        locations[i] = point;

        i++;
    <?php endforeach; ?>

    var marker, i;
    var infoWindow = new google.maps.InfoWindow();
    var image;

    for (i = 0; i < locations.length; i++) { 
        if (locations[i][5] == 'pending') {
            image = " <?php echo base_url('assets/images/star.png') ?> ";
        }
        else if (locations[i][4] == 1) {
            image = " <?php echo base_url('assets/images/Playground-50.png') ?> ";
        }
        else if (locations[i][4] == 2) {
            image = " <?php echo base_url('assets/images/Pullups Filled-50.png') ?> ";
        }
        else if (locations[i][4] == 3) {
            image = " <?php echo base_url('assets/images/City Bench-50.png') ?> ";
        }
        else if (locations[i][4] == 4) {
            image = " <?php echo base_url('assets/images/Weight-50.png') ?> ";
        }
        else if (locations[i][4] == 5) {
            image = " <?php echo base_url('assets/images/Pushups-50.png') ?> ";
        }
        else if (locations[i][4] == 6) {
            image = " <?php echo base_url('assets/images/Stadium-50.png') ?> ";
        }
        else if (locations[i][4] == 7) {
            image = " <?php echo base_url('assets/images/Trekking-50.png') ?> ";
        }
        else if (locations[i][4] == 8) {
            image = " <?php echo base_url('assets/images/Climbing Filled-50.png') ?> ";
        }
        else if (locations[i][4] == 9) {
            image = " <?php echo base_url('assets/images/Wakeup Hill on Stairs-50.png') ?> ";
        }
        else {
            image = " <?php echo base_url('assets/images/Marker-50.png') ?> ";
        }

        marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            icon: image,
            map: map
        });

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infoWindow.setContent('<b>Average Rating:</b> ' + parseFloat(Math.round(locations[i][8])).toFixed(1));
                infoWindow.open(map, marker);

                document.getElementById('pointInformation').innerHTML = '<h2>Information</h2>';
                document.getElementById('pointInformation').innerHTML += '<h3>' + locations[i][0] + '</h3>';
                document.getElementById('pointInformation').innerHTML += '<p>' + locations[i][3] + '</p>';

                if (locations[i][5] === 'confirmed') {
                    if (sessionid>0) {
                        var form = '<form method="post" action=' + "<?= base_url('index.php/explore/index') ?>" + '/' + locations[i][6].trim() + '>';
                        var userrating = '<p>Your Rating: ' + locations[i][7] + '</p>';
                        
                        var ratingsystem = '<p>Rate this location</p>';
                        ratingsystem += '<input id="rating" name="rating" type="radio" value=1 class="star"/> <input name="rating" type="radio" value=2 class="star"/> <input name="rating" type="radio" value=3 class="star"/> <input name="rating" type="radio" value=4 class="star"/> <input name="rating" type="radio" value=5 class="star"/>';

                        var keywordlist = '<div id="keywords">';
                        <?php foreach ($keywords as $keyword): ?>
                            var checked='';
                            if (locations[i][9].indexOf("<?php echo $keyword->keyword ?>") >= 0) {
                                checked = 'checked';
                            }
                            keywordlist += '<input id="keywords[]" name="keywords[]" type="checkbox" value=" <?php echo $keyword->id ?> "' + checked + ' />' + " <?php echo $keyword->keyword ?> " + '<br>';
                        <?php endforeach; ?>
                        keywordlist += '</div>';
                        
                        var submit = '<input type="submit" value="Submit Rating and Keywords"></form>';

                        document.getElementById('pointInformation').innerHTML += form + userrating + ratingsystem + keywordlist + submit;
                    }
                }
            }
        })(marker, i));

        google.maps.event.addListener(marker, 'rightclick', (function(marker, i) {
            return function() {
                infoWindow.close();
                document.getElementById('pointInformation').innerHTML = '';
            }
        })(marker, i));
    }		
}

function placeMarker(pos, map, message) {
    var marker=new google.maps.Marker();
    var infoWindow = new google.maps.InfoWindow();

    marker.setPosition(pos);
    marker.setMap(map);

    google.maps.event.clearListeners(map, 'click');

    google.maps.event.addListener(marker, 'rightclick', function(event) {
        marker.setMap(null);
        document.getElementById('pointInformation').innerHTML = '';
        addMapClickEvent(map);
    });

    return marker;
}

function addMapClickEvent(map) {
    google.maps.event.addListener(map, 'click', function(event) {
        placeMarker(event.latLng, map, 'Latitude: ' + event.latLng.lat() + '<br>Longitude: ' + event.latLng.lng());
        document.getElementById('pointInformation').innerHTML = buildForm(event.latLng.lat(), event.latLng.lng());
    });	
}

function buildForm(latitude, longitude) {
    var heading = '<p>Enter a title and description and click the button to submit your point for review.</p>';
    var latitude = '<label for="latitude">Latitude:</label> <input type="text" name="latitude" value=' + latitude + ' readonly><br>';
    var longitude = '<label for="longitude">Longitude:</label> <input type="text" name="longitude" value=' + longitude + ' readonly><br>';
    var title = '<label for="title">Title:</label> <input type="text" name="title"><br>';
    var description = '<p>Description:</p> <textarea name="description" rows="10"></textarea><br>';
    var icon = '<label for="icon">Icon:</label> <div id="divIcon"><input type="radio" name="icon" value="1" class="radioIcon"><img src=" <?php echo base_url('assets/images/Playground-50.png') ?> " /></input><input type="radio" name="icon" value="2" class="radioIcon"><img src=" <?php echo base_url('assets/images/Pullups Filled-50.png') ?> " /></input><input type="radio" name="icon" value="3" class="radioIcon"><img src=" <?php echo base_url('assets/images/City Bench-50.png') ?> " /></input><input type="radio" name="icon" value="4" class="radioIcon"><img src=" <?php echo base_url('assets/images/Weight-50.png') ?> " /></input><input type="radio" name="icon" value="5" class="radioIcon"><img src=" <?php echo base_url('assets/images/Pushups-50.png') ?> " /></input><input type="radio" name="icon" value="6" class="radioIcon"><img src=" <?php echo base_url('assets/images/Stadium-50.png') ?> " /></input><input type="radio" name="icon" value="7" class="radioIcon"><img src=" <?php echo base_url('assets/images/Trekking-50.png') ?> " /></input><input type="radio" name="icon" value="8" class="radioIcon"><img src=" <?php echo base_url('assets/images/Climbing Filled-50.png') ?> " /></input><input type="radio" name="icon" value="9" class="radioIcon"><img src=" <?php echo base_url('assets/images/Wakeup Hill on Stairs-50.png') ?> " /></input></div><br>';
    var submit = '<br><input type="submit" value="Submit">';

    return '<form action="index">' + heading + latitude + longitude + icon + title + description + submit + '</form>';
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>
