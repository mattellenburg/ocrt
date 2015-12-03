<?php include 'map.php';?>
<script>
    function initialize() {
        var sessionid = " <?php if (isset($_SESSION['user_id'])) { echo $_SESSION['user_id']; } else { echo 0; } ?> ";

        var mapview = <?php if (isset($mapview)) { echo $mapview; } else { echo 1; } ?>;
        
        if (mapview === 1) {
            var map=new google.maps.Map(document.getElementById("googleMap"), {mapTypeId:google.maps.MapTypeId.ROADMAP});
        }
        else {
            var map=new google.maps.Map(document.getElementById("googleMap"), {mapTypeId:google.maps.MapTypeId.HYBRID});
        }
        
        google.maps.event.addListener(map,'dragstart',function(){
            this.set('dragging',true);          
        });

        google.maps.event.addListener(map,'dragend',function(){
            this.set('dragging',false);
            google.maps.event.trigger(this,'idle',{});
        });

        google.maps.event.addListener(map, 'maptypeid_changed', function() { 
            var zoom = "<?php echo $zoom?>";
            var latitude = "<?php echo $latitude?>";
            var longitude = "<?php echo $longitude?>";
            var pointid = "<?php echo $pointid?>";
            var querykeyword = "<?php echo $querykeyword?>";

            if (map.getMapTypeId() === 'roadmap') {
                redirectURL(1, zoom, latitude, longitude, pointid, querykeyword);
            }
            else {
                redirectURL(2, zoom, latitude, longitude, pointid, querykeyword);
            }
        });
        
        google.maps.event.addListener(map,'zoom_changed',function(){
            var mapview = "<?php echo $mapview?>";
            var zoom = "<?php echo $zoom?>";
            var latitude = "<?php echo $latitude?>";
            var longitude = "<?php echo $longitude?>";
            var pointid = "<?php echo $pointid?>";

            if (parseInt(zoom) !== map.getZoom()) { redirectURL(mapview, map.getZoom(), latitude, longitude, pointid); }
        });
        
        google.maps.event.addListener(map, 'idle', function() {
            if(!this.get('dragging') && this.get('oldCenter') && this.get('oldCenter')!==this.getCenter()) {
                var mapview = "<?php echo $mapview?>";
                var zoom = "<?php echo $zoom?>";
                var pointid = "<?php echo $pointid?>";
                redirectURL (mapview, zoom, map.getCenter().lat(), map.getCenter().lng(), pointid);
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

    function redirectURL (mapview, zoom, latitude, longitude, pointid, clearkeyword) {
        if (pointid === '') { pointid = 0; }
        var querykeyword = '';
        if (clearkeyword !== 1) {
            querykeyword = " <?php echo $querykeyword ?> ".trim();
            if (querykeyword === '') { 
                if (getQueryString('keyword') !== null) {
                    querykeyword = getQueryString('keyword');
                }   
            }
        }
        //alert (mapview + ',' + zoom + ',' + latitude + ',' + longitude + ',' + pointid + ',' + querykeyword);
            
        window.location = "<?= base_url('index.php/explore/index/') ?>" + '/' + mapview + '/' + zoom + '/' + latitude + '/' + longitude + '/' + pointid + '/' + querykeyword;
    }

    function getMapCenter(map, sessionid) {
        var mapview = "<?php echo $mapview?>";
        var zoom = "<?php echo $zoom?>";
        var latitude = "<?php echo $latitude?>";
        var longitude = "<?php echo $longitude?>";
        var pointid = "<?php echo $pointid?>";
        var querykeyword = "<?php echo $querykeyword?>";

        if (map.getCenter() === undefined && latitude === '' && longitude === '') {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    redirectURL(mapview, 13, position.coords.latitude, position.coords.longitude, pointid);
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
            redirectURL(mapview, 13, latitude, longitude, pointid, querykeyword);
        }
        else {
            map.setZoom(parseInt(zoom));
            map.setCenter(new google.maps.LatLng(latitude, longitude, pointid));

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

                var point = new Array(title, latitude, longitude, description, icon, type, pointid, userrating, avgrating, keywords, distance, 0);

                locations[i] = point;

                i++;
            <?php endforeach; ?>
        <?php } ?>

        <?php if (isset($points_pending)) { ?>           
            <?php foreach ($points_pending as $point): ?>
                var title = " <?php echo $point->title ?> ";
                var description = " <?php echo $point->description ?> ";
                var latitude = " <?php echo $point->latitude ?> ";
                var longitude = " <?php echo $point->longitude ?> ";
                var icon = " <?php echo $point->icon ?> ";
                var type = 'pending';
                var pointid = " <?php echo $point->pointid ?> ";
                var id = " <?php echo $point->id ?> ";

                var point = new Array(title, latitude, longitude, description, icon, type, pointid, 0, 0, '', 0, id);

                locations[i] = point;

                i++;
            <?php endforeach; ?>
        <?php } ?>

        var marker, i;

        for (i = 0; i < locations.length; i++) { 
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                icon: getImage(locations[i][5], locations[i][4]),
                map: map
            });

            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    var pointinformation = document.getElementById("pointInformation");
                    
                    var h2 = pointinformation.getElementsByTagName("h2");
                    
                    var content = pointinformation.getElementsByTagName("div");
                    
                    var p = content[0].getElementsByTagName("p");
                    p[0].innerHTML = locations[i][3];

                    var div = content[0].getElementsByTagName("div");

                    var pointid = 0;
                    if (locations[i][6] > 0) { pointid = parseInt(locations[i][6]); }
                    var pendingpointid = 0;
                    if (locations[i][11] > 0) { pendingpointid = locations[i][11]; }
                    var icon = 0;
                    if (locations[i][4] > 0) { icon = locations[i][4]; }
                    var title = locations[i][0].replace("'","''").trim();
                    var description = locations[i][3].replace("'","''").trim();

                    if (locations[i][5] === 'confirmed' && sessionid > 0) {
                        h2[0].innerHTML = locations[i][0] + ' (' + parseFloat(locations[i][8]).toFixed(1) + ' stars / ' + parseFloat(locations[i][10]).toFixed(1) + ' miles)';
                        var form = '<form method="post" action="' + "<?= base_url('index.php/explore/index/'.$mapview.'/'.$zoom.'/'.$latitude.'/'.$longitude) ?>" + '/' + pointid.toString() + '">';

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

                        var submit = '<input id="ratingkeywordssubmit" name="ratingkeywordssubmit" type="submit" value="Submit Rating and Keywords"><input type="button" value="Edit Location Information" onClick="editLocation(' + locations[i][1] + ',' + locations[i][2] + ',' + icon + ',' + String.fromCharCode(39) + title + String.fromCharCode(39) + ',' + String.fromCharCode(39) + description + String.fromCharCode(39) + ',' + pointid + ');"></form>';

                        div[0].innerHTML = form + ratingsystem + keywordlist + submit;

                        if (locations[i][7] > 0) {
                            stars('locationrating', parseInt(locations[i][7]));
                        }
                    }
                    else if (sessionid > 0) {
                        h2[0].innerHTML = title;
                        var currenticon = '<p>Icon: <img src="' + getImage('', locations[i][4]) + '" /></p>';
                        var form = '<form method="post" action="' + "<?= base_url('index.php/explore/index/'.$mapview.'/'.$zoom.'/'.$latitude.'/'.$longitude) ?>" + '/' + pointid + '">';
                        var submit = '<input type="button" value="Edit Location Information" onClick="editLocation(' + locations[i][1] + ',' + locations[i][2] + ',' + icon + ',' + String.fromCharCode(39) + title + String.fromCharCode(39) + ',' + String.fromCharCode(39) + description + String.fromCharCode(39) + ',' + pointid + ',' + pendingpointid + ');"><input type="button" value="Delete Location" onClick="deleteLocation(' + pendingpointid + ');"></form>';
                        div[0].innerHTML = currenticon + form + submit;
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
            document.getElementById('pointInformation').innerHTML = buildForm(event.latLng.lat(), event.latLng.lng(), 0, '', '', 0, 0);
        });	
    }
    
    function editLocation(latitude, longitude, icon, title, description, pointid, pendingpointid) {
        document.getElementById('pointInformation').innerHTML = buildForm(latitude, longitude, icon, title, description, pointid, pendingpointid);
    }

    function deleteLocation(pendingpointid) {
        var filters = document.getElementById("filters");
        filters.style.display = "none";

        var heading = '<h2>Delete Location</h2>';
        var divstart = '<div class="content">';
        var pointid = '<p><input type="hidden" id="pendingpointid" name="pendingpointid" value=' + pendingpointid + '></p>';
        var confirmation = '<p>Are you sure you wish to delete this location?</p>';
        var submit = '<p><input type="submit" id="deletelocation" name="deletelocation" value="Delete Location" /><input type="submit" value="Cancel" /></p>';
        var divend = '</div>';
        
        document.getElementById('pointInformation').innerHTML = '<form method="post" action="' + "<?= base_url('index.php/explore/index/'.$mapview.'/'.$zoom.'/'.$latitude.'/'.$longitude) ?>" + '">' + heading + divstart + pointid + confirmation + submit + divend + '</form>';
    }

    function getChecked(variable) {
        var checked = ['','','','','','','','',''];

        if (variable === 1) { checked[0] = 'checked'; }
        if (variable === 2) { checked[1] = 'checked'; }
        if (variable === 3) { checked[2] = 'checked'; }
        if (variable === 4) { checked[3] = 'checked'; }
        if (variable === 5) { checked[4] = 'checked'; }
        if (variable === 6) { checked[5] = 'checked'; }
        if (variable === 7) { checked[6] = 'checked'; }
        if (variable === 8) { checked[7] = 'checked'; }
        if (variable === 9) { checked[8] = 'checked'; }

        return checked;
    }
    
    function buildForm(latitude, longitude, icon, title, description, pointid, pendingpointid) {
        //alert(latitude + ', ' + longitude + ', ' + icon + ', ' + title + ', ' + description + ', ' + pointid + ', ' + pendingpointid);
        title = title.replace("''","'").trim();
        description = description.replace("''","'").trim();

        var checked = getChecked(icon);
        
        var filters = document.getElementById("filters");
        filters.style.display = "none";

        var heading = '<h2>Submit Location Information</h2>';
        var divstart = '<div class="content">';
        var pointid = '<p><input type="hidden" id="pointid" name="pointid" value=' + pointid + '><input type="hidden" id="pendingpointid" name="pendingpointid" value=' + pendingpointid + '></p>';
        var instructions = '<p>Enter a title and description and click the button to submit your location for review.</p>';
        var latlong = '<p><input type="hidden" name="latitude" value=' + latitude + '><input type="hidden" name="longitude" value=' + longitude + '></p>';
        var title = '<p><label for="title">Title:</label><input type="text" name="title" value="' + title + '"></p>';
        var description = '<p>Description:</p><textarea name="description" style="margin: 0px; width: 350px; height: 120px;">' + description + '</textarea></p>';
        var icon = '<div id="divIcon"><input type="radio" name="icon" value="1" class="radioIcon" ' + checked[0] + ' ><img src=" <?php echo base_url('assets/images/Playground-50.png') ?> " /></input><input type="radio" name="icon" value="2" class="radioIcon" ' + checked[1] + ' ><img src=" <?php echo base_url('assets/images/Pullups Filled-50.png') ?> " /></input><input type="radio" name="icon" value="3" class="radioIcon" ' + checked[2] + ' ><img src=" <?php echo base_url('assets/images/City Bench-50.png') ?> " /></input><input type="radio" name="icon" value="4" class="radioIcon" ' + checked[3] + ' ><img src=" <?php echo base_url('assets/images/Weight-50.png') ?> " /></input><input type="radio" name="icon" value="5" class="radioIcon" ' + checked[4] + ' ><img src=" <?php echo base_url('assets/images/Pushups-50.png') ?> " /></input><br><input type="radio" name="icon" value="6" class="radioIcon" ' + checked[5] + ' ><img src=" <?php echo base_url('assets/images/Stadium-50.png') ?> " /></input><input type="radio" name="icon" value="7" class="radioIcon" ' + checked[6] + ' ><img src=" <?php echo base_url('assets/images/Trekking-50.png') ?> " /></input><input type="radio" name="icon" value="8" class="radioIcon" ' + checked[7] + ' ><img src=" <?php echo base_url('assets/images/Climbing Filled-50.png') ?> " /></input><input type="radio" name="icon" value="9" class="radioIcon" ' + checked[8] + ' ><img src=" <?php echo base_url('assets/images/Wakeup Hill on Stairs-50.png') ?> " /></input></div>';
        var submit = '<p><input type="submit" id="submitlocation" name="submitlocation" value="Submit Location Information" /><input type="submit" id="requestdeletion" name="requestdeletion" value="Request Location Deletion" /><input type="submit" value="Cancel" /></p>';
        var divend = '</div>';
        
        return '<form method="post" action="' + "<?= base_url('index.php/explore/index/'.$mapview.'/'.$zoom.'/'.$latitude.'/'.$longitude) ?>" + '">' + heading + divstart + pointid + instructions + latlong + icon + title + description + submit + divend + '</form>';
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
