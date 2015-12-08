<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCLrhs-LKivwuYlINaomHmKyINdWKx5Z-Y"></script>
<script>
    function initializeMap(url) {
        var map=new google.maps.Map(document.getElementById("googleMap"), {mapTypeId:google.maps.MapTypeId.ROADMAP});
        if (<?php if (isset($mapview)) { echo $mapview; } else { echo 1; } ?> === 2) {
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
                redirectURL(url, 1, zoom, latitude, longitude, pointid, querykeyword);
            }
            else {
                redirectURL(url, 2, zoom, latitude, longitude, pointid, querykeyword);
            }
        });
        
        google.maps.event.addListener(map,'zoom_changed',function(){
            var mapview = "<?php echo $mapview?>";
            var zoom = "<?php echo $zoom?>";
            var latitude = "<?php echo $latitude?>";
            var longitude = "<?php echo $longitude?>";
            var pointid = "<?php echo $pointid?>";
 
            if (url === 'index.php/explore/index/' && parseInt(zoom) !== map.getZoom()) { redirectURL(url, mapview, map.getZoom(), latitude, longitude, pointid); }
        });
        
        google.maps.event.addListener(map, 'idle', function() {
            if(!this.get('dragging') && this.get('oldCenter') && this.get('oldCenter')!==this.getCenter()) {
                var mapview = "<?php echo $mapview?>";
                var zoom = "<?php echo $zoom?>";
                var pointid = "<?php echo $pointid?>";
                if (url === 'index.php/explore/index/') {
                    redirectURL (url, mapview, zoom, map.getCenter().lat(), map.getCenter().lng(), pointid);
                }
            }
            if(!this.get('dragging')){
                this.set('oldCenter',this.getCenter());
            }
        });    
        
        return map;
    }
    
    function getImage(pending, icon) {
        if (pending == 'pending') {
            image = " <?php echo base_url('assets/images/star.png') ?> ";
        }
        else if (icon == 1) {
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
    
    function getLocations() {
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
        
        return locations;
    }

    function loadPoints(url, map, sessionid) {
        var locations = getLocations();
            
        var marker, i;

        for (i = 0; i < locations.length; i++) { 
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                icon: getImage(locations[i][5], locations[i][4]),
                map: map
            });

            if (url === 'index.php/explore/index/') {
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
                        redirectURL(url, "<?php echo $mapview ?>", "<?php echo $zoom ?>", "<?php echo $latitude ?>", "<?php echo $longitude ?>", "<?php if (isset($pointid)) { echo $pointid; } ?>");
                    };
                })(marker, i));
            }
        }		
    }
    
    function redirectURL (url, mapview, zoom, latitude, longitude, pointid, clearkeyword) {
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
        //alert (url + ',' + mapview + ',' + zoom + ',' + latitude + ',' + longitude + ',' + pointid + ',' + querykeyword);
        
        if (url === 'index.php/explore/index/') {
            window.location = "<?= base_url('index.php/explore/index/') ?>" + '/' + mapview + '/' + zoom + '/' + latitude + '/' + longitude + '/' + pointid + '/' + querykeyword;
        }
        else if (url === 'index.php/train/index/') {
            window.location = "<?= base_url('index.php/train/index/') ?>" + '/' + mapview + '/' + zoom + '/' + latitude + '/' + longitude + '/' + pointid + '/' + querykeyword;
        }
    }

    function getMapCenter(url, map, sessionid) {
        var marker;
        
        var mapview = "<?php echo $mapview?>";
        var zoom = "<?php echo $zoom?>";
        var latitude = "<?php echo $latitude?>";
        var longitude = "<?php echo $longitude?>";
        var pointid = "<?php echo $pointid?>";
        var querykeyword = "<?php echo $querykeyword?>";

        if (map.getCenter() === undefined && latitude === '' && longitude === '') {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    redirectURL(url, mapview, 13, position.coords.latitude, position.coords.longitude, pointid);
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
            redirectURL(url, mapview, 13, latitude, longitude, pointid, querykeyword);
        }
        else {
            map.setZoom(parseInt(zoom));
            map.setCenter(new google.maps.LatLng(latitude, longitude, pointid));

            loadPoints(url, map, sessionid);

            marker = new google.maps.Marker({
                position: new google.maps.LatLng(latitude, longitude),
                map: map
            });
        }
        
        return marker;
    }

    function getLatLong(url) {
        var geocoder = new google.maps.Geocoder();

        geocoder.geocode( { 'address': document.getElementById("address").value}, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                redirectURL(url, "<?php echo $mapview?>", "<?php echo $zoom?>", results[0].geometry.location.lat(), results[0].geometry.location.lng(), "<?php if (isset($pointid)) { echo $pointid; } ?>");
            } 
        }); 
    }
    
    var getQueryString = function ( field, url ) {
        var href = url ? url : window.location.href;
        var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
        var string = reg.exec(href);
        return string ? string[1] : null;
    };
</script>