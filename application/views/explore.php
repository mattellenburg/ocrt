<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="explore">
    <div id="instructions">
        <h2>Explore <input type="text" name="address" id="address" onkeydown="if (event.keyCode == 13) document.getElementById('go').click()" placeholder="Enter location" /> <input id="go" type="button" value="Go" onclick="getLatLong();" /></h2>
        <p>Click on the map to submit a new location. Click on a point to get more information about that location.</p>
        <div id="googleMap">
        </div>
    </div>
    <div id="pointInformation">
        <h2>Location Information</h2>
        <h3></h3>
        <p></p>
        <div></div>
    </div>
    <div id="filters">
        <?php if (isset($_SESSION['user_id'])) : ?>
            <h2>Location Filters</h2>
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
        mapTypeId:google.maps.MapTypeId.ROADMAP
    };
    var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);

    google.maps.event.addListener(map,'dragstart',function(){
        this.set('dragging',true);          
    });

    google.maps.event.addListener(map,'dragend',function(){
        this.set('dragging',false);
        google.maps.event.trigger(this,'idle',{});
    });

    google.maps.event.addListener(map,'zoom_changed',function(){
        var latitude = "<?php echo $latitude?>";
        var longitude = "<?php echo $longitude?>";
        var zoom = "<?php echo $zoom?>";
        
        if (parseInt(zoom) !== map.getZoom()) { redirectURL(map.getZoom(), latitude, longitude); }
    });

    google.maps.event.addListener(map, 'idle', function() {
        if(!this.get('dragging') && this.get('oldCenter') && this.get('oldCenter')!==this.getCenter()) {
            var zoom = "<?php echo $zoom?>";
            redirectURL (zoom, map.getCenter().lat(), map.getCenter().lng());
        }
        if(!this.get('dragging')){
            this.set('oldCenter',this.getCenter())
        }
    });

    getMapCenter(map, sessionid);    
    
    if (sessionid > 0) {
        addMapClickEvent(map);
    }    
}

function redirectURL (zoom, latitude, longitude) {
    window.location = "<?= base_url('index.php/explore/index/') ?>" + '/' + zoom + '/' + latitude + '/' + longitude;
}

function getMapCenter(map, sessionid) {
    var latitude = "<?php echo $latitude?>";
    var longitude = "<?php echo $longitude?>";
    var zoom = "<?php echo $zoom?>";

    if (map.getCenter() === undefined && latitude === '' && longitude === '') {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                redirectURL(13, position.coords.latitude, position.coords.longitude);
            }, 
            function() {
                handleLocationError(true, new infoWindow, map.getCenter());
            });
        } 
        else {
            handleLocationError(false, new infoWindow, map.getCenter());
        }
    }
    else if (zoom === '') {
        redirectURL(13, latitude, longitude);
    }
    else {
        map.setZoom(parseInt(zoom));
        map.setCenter(new google.maps.LatLng(latitude, longitude));
        
        loadPoints(map, sessionid)
        
        new google.maps.Marker({
            position: new google.maps.LatLng(latitude, longitude),
            map: map,
        });
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
        var distance = " <?php echo $point['distance'] ?> ";
        
        var point = new Array(title, latitude, longitude, description, icon, type, pointid, userrating, avgrating, keywords, distance);

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
                var pointinformation = document.getElementById("pointInformation");
                var h3 = pointinformation.getElementsByTagName("h3");
                h3[0].innerHTML = locations[i][0] + ' (' + parseFloat(locations[i][8]).toFixed(1) + ' / ' + parseFloat(locations[i][10]).toFixed(1) + ' miles)';
                
                var p = pointinformation.getElementsByTagName("p");
                p[0].innerHTML = locations[i][3];

                var div = pointinformation.getElementsByTagName("div");

                if (locations[i][5] === 'confirmed') {
                    if (sessionid>0) {
                        var form = '<form method="post" action="' + "<?= base_url('index.php/explore/index/'.$zoom.'/'.$latitude.'/'.$longitude) ?>" + '/' + locations[i][6].trim() + '">';
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

                        div[0].innerHTML = form + userrating + ratingsystem + keywordlist + submit;
                    }
                }
            }
        })(marker, i));

        google.maps.event.addListener(marker, 'dblclick', (function(marker, i) {
            return function() {
                redirectURL("<?php echo $zoom?>", "<?php echo $latitude?>", "<?php echo $longitude?>");
            }
        })(marker, i));
    }		
}

function placeMarker(pos, map) {
    var marker=new google.maps.Marker();

    marker.setPosition(pos);
    marker.setMap(map);

    google.maps.event.clearListeners(map, 'click');

    google.maps.event.addListener(marker, 'dblclick', function(event) {
        redirectURL("<?php echo $zoom?>", "<?php echo $latitude?>", "<?php echo $longitude?>");
    });

    return marker;
}

function addMapClickEvent(map) {
    google.maps.event.addListener(map, 'click', function(event) {
        placeMarker(event.latLng, map);
        document.getElementById('pointInformation').innerHTML = buildForm(event.latLng.lat(), event.latLng.lng());
    });	
}

function buildForm(latitude, longitude) {
    var heading = '<h2>New Location</h2><p>Enter a title and description and click the button to submit your point for review.</p>';
    var latitude = '<label for="latitude">Latitude:</label> <input type="text" name="latitude" value=' + latitude + ' readonly><br>';
    var longitude = '<label for="longitude">Longitude:</label> <input type="text" name="longitude" value=' + longitude + ' readonly><br>';
    var title = '<label for="title">Title:</label> <input type="text" name="title"><br>';
    var description = '<p>Description:</p> <textarea name="description" rows="10"></textarea><br>';
    var icon = '<label for="icon">Icon:</label> <div id="divIcon"><input type="radio" name="icon" value="1" class="radioIcon"><img src=" <?php echo base_url('assets/images/Playground-50.png') ?> " /></input><input type="radio" name="icon" value="2" class="radioIcon"><img src=" <?php echo base_url('assets/images/Pullups Filled-50.png') ?> " /></input><input type="radio" name="icon" value="3" class="radioIcon"><img src=" <?php echo base_url('assets/images/City Bench-50.png') ?> " /></input><input type="radio" name="icon" value="4" class="radioIcon"><img src=" <?php echo base_url('assets/images/Weight-50.png') ?> " /></input><input type="radio" name="icon" value="5" class="radioIcon"><img src=" <?php echo base_url('assets/images/Pushups-50.png') ?> " /></input><br><input type="radio" name="icon" value="6" class="radioIcon"><img src=" <?php echo base_url('assets/images/Stadium-50.png') ?> " /></input><input type="radio" name="icon" value="7" class="radioIcon"><img src=" <?php echo base_url('assets/images/Trekking-50.png') ?> " /></input><input type="radio" name="icon" value="8" class="radioIcon"><img src=" <?php echo base_url('assets/images/Climbing Filled-50.png') ?> " /></input><input type="radio" name="icon" value="9" class="radioIcon"><img src=" <?php echo base_url('assets/images/Wakeup Hill on Stairs-50.png') ?> " /></input></div><br>';
    var submit = '<br><input type="submit" value="Submit"><input type="button" value="Cancel" onClick="redirectURL(' + <?php echo $zoom?> + ',' + <?php echo $latitude?> + ',' + <?php echo $longitude?> + ');">';

    return '<form action="' + "<?= base_url('index.php/explore/index/'.$zoom.'/'.$latitude.'/'.$longitude) ?>" + '">' + heading + latitude + longitude + icon + title + description + submit + '</form>';
}

function getLatLong() {
    var geocoder = new google.maps.Geocoder();

    geocoder.geocode( { 'address': document.getElementById("address").value}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            redirectURL("<?php echo $zoom?>", results[0].geometry.location.lat(), results[0].geometry.location.lng());
        } 
    }); 
}
google.maps.event.addDomListener(window, 'load', initialize);
</script>
