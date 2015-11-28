<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="explore">
    <div id="map">
        <h2>Map <input type="text" name="address" id="address" onkeydown="if (event.keyCode == 13) document.getElementById('go').click()" placeholder="Enter location" /> <input id="go" type="button" value="Go" onclick="getLatLong();" /></h2>
        <div class="content">
            <p>Click on the map to submit a new location.</p>
            <div id="googleMap"></div>
        </div>
    </div>
    <div id="pointInformation">
        <h2>Location Information</h2>
        <div class="content">
            <h3></h3>
            <p></p>
            <div class="scroll"></div>
        </div>
    </div>
    <div id="filters">
        <h2>Location Filters</h2>
        <div class="content">
            <div>
                <?php echo $filters ?>
                <?= form_open() ?>
                <p>
                    <label for="filterrating">Average Rating:</label>
                    <label><input type="radio" id="filterrating" name="filterrating" value="1" onclick="stars(this.name);" /><img id="filterratingstar1" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>
                    <label><input type="radio" id="filterrating" name="filterrating" value="2" onclick="stars(this.name);" /><img id="filterratingstar2" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>
                    <label><input type="radio" id="filterrating" name="filterrating" value="3" onclick="stars(this.name);" /><img id="filterratingstar3" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>
                    <label><input type="radio" id="filterrating" name="filterrating" value="4" onclick="stars(this.name);" /><img id="filterratingstar4" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>
                    <label><input type="radio" id="filterrating" name="filterrating" value="5" onclick="stars(this.name);" /><img id="filterratingstar5" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>
                    or higher
                </p>
                <label for="search">Title/Description Search:</label><input type="text" id="filtersearch" name="filtersearch" /></p>
           
                <label for="filterkeywords">Keywords:</label>
                <div class="keywords">
                <?php foreach ($keywords as $filterkeyword): ?>
                    <input id="filterkeywords[]" name="filterkeywords[]" type="checkbox" value=" <?php echo $filterkeyword->id ?> " /><?php echo $filterkeyword->keyword ?><br>
                <?php endforeach; ?>
                </div>
          
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <p><label for="mysubmissions">Submitted by Me:</label><input type="checkbox" id="mysubmissions" name="mysubmissions"></p>
                <?php endif; ?>
                <p><input type="submit" value="Search" /></p>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="http://maps.googleapis.com/maps/api/js"></script>
<script>
    function initialize() {
        var sessionid = " <?php if (isset($_SESSION['user_id'])) { echo $_SESSION['user_id']; } else { echo 0; } ?> ";

        var map=new google.maps.Map(document.getElementById("googleMap"), {mapTypeId:google.maps.MapTypeId.ROADMAP});

        google.maps.event.addListener(map,'dragstart',function(){
            this.set('dragging',true);          
        });

        google.maps.event.addListener(map,'dragend',function(){
            this.set('dragging',false);
            google.maps.event.trigger(this,'idle',{});
        });

        google.maps.event.addListener(map,'zoom_changed',function(){
            var mapview = "<?php echo $mapview?>";
            var zoom = "<?php echo $zoom?>";
            var latitude = "<?php echo $latitude?>";
            var longitude = "<?php echo $longitude?>";
            var pointid = "<?php echo $pointid?>";

            if (parseInt(zoom) !== map.getZoom()) { redirectURL(mapview, map.getZoom(), latitude, longitude, pointid, ''); }
        });
        
        google.maps.event.addListener(map, 'idle', function() {
            if(!this.get('dragging') && this.get('oldCenter') && this.get('oldCenter')!==this.getCenter()) {
                var mapview = "<?php echo $mapview?>";
                var zoom = "<?php echo $zoom?>";
                var pointid = "<?php echo $pointid?>";
                redirectURL (mapview, zoom, map.getCenter().lat(), map.getCenter().lng(), pointid, '');
            }
            if(!this.get('dragging')){
                this.set('oldCenter',this.getCenter());
            }
        });

        getMapCenter(map, sessionid);    

        if (sessionid > 0) {
            addMapClickEvent(map);
        }    
    }

    function redirectURL (mapview, zoom, latitude, longitude, pointid, keyword) {
        //alert (mapview + ',' + zoom + ',' + latitude + ',' + longitude + ',' + pointid + ',' + keyword);
        var keywordquery = '';
        if (keyword > '') {
            keywordquery = '?keyword=' + keyword;
        }
            
        window.location = "<?= base_url('index.php/explore/index/') ?>" + '/' + mapview + '/' + zoom + '/' + latitude + '/' + longitude + '/' + pointid + keywordquery;
    }

    function getMapCenter(map, sessionid) {
        var mapview = "<?php echo $mapview?>";
        var zoom = "<?php echo $zoom?>";
        var latitude = "<?php echo $latitude?>";
        var longitude = "<?php echo $longitude?>";

        if (map.getCenter() === undefined && latitude === '' && longitude === '') {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    redirectURL(mapview, 13, position.coords.latitude, position.coords.longitude, '', '');
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
            redirectURL(mapview, 13, latitude, longitude, '', '');
        }
        else {
            map.setZoom(parseInt(zoom));
            map.setCenter(new google.maps.LatLng(latitude, longitude));

            loadPoints(map, sessionid);

            new google.maps.Marker({
                position: new google.maps.LatLng(latitude, longitude),
                map: map
            });
        }
    }

    function loadPoints(map, sessionid) {
        var locations = new Array();
        var i = 0;
        <?php if (isset($points)) { ?>
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
        <?php } ?>

        <?php if (isset($points_pending)) { ?>           
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
        <?php } ?>

        var marker, i;
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
                    
                    var h2 = pointinformation.getElementsByTagName("h2");
                    h2[0].innerHTML = locations[i][0] + ' (' + parseFloat(locations[i][8]).toFixed(1) + ' stars / ' + parseFloat(locations[i][10]).toFixed(1) + ' miles)';
                    
                    var content = pointinformation.getElementsByTagName("div");
                    
                    var p = content[0].getElementsByTagName("p");
                    p[0].innerHTML = locations[i][3];

                    var div = content[0].getElementsByTagName("div");

                    if (locations[i][5] === 'confirmed') {
                        if (sessionid>0) {
                            var form = '<form method="post" action="' + "<?= base_url('index.php/explore/index/'.$mapview.'/'.$zoom.'/'.$latitude.'/'.$longitude) ?>" + '/' + locations[i][6].trim() + '">';

                            var selected1 = '';
                            var selected2 = '';
                            var selected3 = '';
                            var selected4 = '';
                            var selected5 = '';
                            
                            if (locations[i][7] == 1) { selected1 = 'selected'; }
                            if (locations[i][7] == 2) { selected2 = 'selected'; }
                            if (locations[i][7] == 3) { selected3 = 'selected'; }
                            if (locations[i][7] == 4) { selected4 = 'selected'; }
                            if (locations[i][7] == 5) { selected5 = 'selected'; }
                            
                            var ratingsystem = '<h4>Rate Location:</h4>';
                            ratingsystem += '<label><input type="radio" id="locationrating" name="locationrating" value="1" onclick="stars(this.name);" ' + selected1 + ' /><img id="locationratingstar1" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>';
                            ratingsystem += '<label><input type="radio" id="locationrating" name="locationrating" value="2" onclick="stars(this.name);" ' + selected2 + ' /><img id="locationratingstar2" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>';
                            ratingsystem += '<label><input type="radio" id="locationrating" name="locationrating" value="3" onclick="stars(this.name);" ' + selected3 + ' /><img id="locationratingstar3" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>';
                            ratingsystem += '<label><input type="radio" id="locationrating" name="locationrating" value="4" onclick="stars(this.name);" ' + selected4 + ' /><img id="locationratingstar4" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>';
                            ratingsystem += '<label><input type="radio" id="locationrating" name="locationrating" value="5" onclick="stars(this.name);" ' + selected5 + ' /><img id="locationratingstar5" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>';

                            var keywordlist = '<h4>Keywords:</h4><div class="keywords">';
                            <?php foreach ($keywords as $keyword): ?>
                                var checked='';
                                if (locations[i][9].indexOf("<?php echo $keyword->keyword ?>") >= 0) {
                                    checked = 'checked';
                                }
                                keywordlist += '<input id="locationkeywords[]" name="locationkeywords[]" type="checkbox" value=" <?php echo $keyword->id ?> "' + checked + ' />' + " <?php echo $keyword->keyword ?> " + '<br>';
                            <?php endforeach; ?>
                            keywordlist += '</div>';

                            var submit = '<input id="ratingkeywordssubmit" name="ratingkeywordssubmit" type="submit" value="Submit Rating and Keywords"><input type="button" value="Edit Location Information" onClick="editLocation(<?php echo $latitude ?>,<?php echo $longitude ?>,\"' + locations[i][7] + '\",\"' + locations[i][0] + '\",\"' + locations[i][3] + '\");"></form>';

                            div[0].innerHTML = form + ratingsystem + keywordlist + submit;

                            if (locations[i][7] > 0) {
                                stars('locationrating', parseInt(locations[i][7]));
                            }
                        }
                    }
                };
            })(marker, i));

            google.maps.event.addListener(marker, 'dblclick', (function(marker, i) {
                return function() {
                    redirectURL("<?php echo $mapview ?>", "<?php echo $zoom ?>", "<?php echo $latitude ?>", "<?php echo $longitude ?>", "<?php if (isset($pointid)) { echo $pointid; } ?>");
                };
            })(marker, i));
        }		
    }
    
    function placeMarker(pos, map) {
        var marker=new google.maps.Marker();

        marker.setPosition(pos);
        marker.setMap(map);

        google.maps.event.clearListeners(map, 'click');

        google.maps.event.addListener(marker, 'dblclick', function(event) {
            redirectURL("<?php echo $mapview?>", "<?php echo $zoom?>", "<?php echo $latitude?>", "<?php echo $longitude?>", "<?php if (isset($pointid)) { echo $pointid; } ?>");
        });

        return marker;
    }

    function addMapClickEvent(map) {
        google.maps.event.addListener(map, 'click', function(event) {
            placeMarker(event.latLng, map);
            document.getElementById('pointInformation').innerHTML = buildForm(event.latLng.lat(), event.latLng.lng());
        });	
    }
    
    function editLocation(latitude, longitude, icon, title, description) {
        alert(title);
        document.getElementById('pointInformation').innerHTML = buildForm(latitude, longitude, icon, title, description);
    }

    function buildForm(latitude, longitude, icon, title, description) {
        var filters = document.getElementById("filters");
        filters.style.display = "none";

        var heading = '<h2>New Location</h2>';
        var divstart = '<div class="content">';
        var instructions = '<p>Enter a title and description and click the button to submit your location for review.</p>';
        var latlong = '<p><input type="hidden" name="latitude" value=' + latitude + '><input type="hidden" name="longitude" value=' + longitude + '></p>';
        var title = '<p><label for="title">Title:</label><input type="text" name="title" value="' + title + '"></p>';
        var description = '<p>Description:</p><textarea name="description" style="margin: 0px; width: 350px; height: 120px;"></textarea></p>';
        var icon = '<div id="divIcon"><input type="radio" name="icon" value="1" class="radioIcon"><img src=" <?php echo base_url('assets/images/Playground-50.png') ?> " /></input><input type="radio" name="icon" value="2" class="radioIcon"><img src=" <?php echo base_url('assets/images/Pullups Filled-50.png') ?> " /></input><input type="radio" name="icon" value="3" class="radioIcon"><img src=" <?php echo base_url('assets/images/City Bench-50.png') ?> " /></input><input type="radio" name="icon" value="4" class="radioIcon"><img src=" <?php echo base_url('assets/images/Weight-50.png') ?> " /></input><input type="radio" name="icon" value="5" class="radioIcon"><img src=" <?php echo base_url('assets/images/Pushups-50.png') ?> " /></input><br><input type="radio" name="icon" value="6" class="radioIcon"><img src=" <?php echo base_url('assets/images/Stadium-50.png') ?> " /></input><input type="radio" name="icon" value="7" class="radioIcon"><img src=" <?php echo base_url('assets/images/Trekking-50.png') ?> " /></input><input type="radio" name="icon" value="8" class="radioIcon"><img src=" <?php echo base_url('assets/images/Climbing Filled-50.png') ?> " /></input><input type="radio" name="icon" value="9" class="radioIcon"><img src=" <?php echo base_url('assets/images/Wakeup Hill on Stairs-50.png') ?> " /></input></div>';
        var submit = '<p><input type="submit" value="Submit"><input type="button" value="Cancel" onClick="redirectURL(' + <?php echo $mapview?> + ',' + <?php echo $zoom?> + ',' + <?php echo $latitude?> + ',' + <?php echo $longitude?> + ',' + <?php if (isset($pointid)) { echo $pointid; } ?> + ');"></p>';
        var divend = '</div>';
        
        return '<form action="' + "<?= base_url('index.php/explore/index/'.$mapview.'/'.$zoom.'/'.$latitude.'/'.$longitude) ?>" + '">' + heading + divstart + instructions + latlong + icon + title + description + submit + divend + '</form>';
    }

    function getLatLong() {
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode( { 'address': document.getElementById("address").value}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                redirectURL("<?php echo $mapview?>", "<?php echo $zoom?>", results[0].geometry.location.lat(), results[0].geometry.location.lng(), "<?php if (isset($pointid)) { echo $pointid; } ?>");
            } 
        }); 
    }

    function stars(name, rating) {
        if (typeof rating === 'undefined') {
            rating = document.querySelector('input[name = "' + name + '"]:checked').value;
        }

        for(i=1; i<=5; i++) {
            var star = name + 'star' + i.toString()

            if (i<=rating) {
                document.getElementById(star).src="<?php echo base_url('assets/images/starblack.png') ?>";    
            }
            else {
                document.getElementById(star).src="<?php echo base_url('assets/images/starwhite.png') ?>";            
            }
        }
    }

    google.maps.event.addDomListener(window, 'load', initialize);    
</script>
