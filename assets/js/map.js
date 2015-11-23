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

    if ("<?php echo $edit; ?>" === "Edit Information") {
        document.getElementById('pointInformation').innerHTML = buildForm("<?php echo $latitude; ?>", "<?php echo $longitude; ?>", "<?php echo $title; ?>", "<?php echo $description; ?>", "<?php echo $icon; ?>");
    }
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

function redirectURL (zoom, latitude, longitude, keyword) {
    var keywordquery = '';
    if (keyword > '') {
        keywordquery = '?keyword=' + keyword
    }

    window.location = "<?= base_url('index.php/explore/index/') ?>" + '/' + zoom + '/' + latitude + '/' + longitude + keywordquery;
}

function getMapCenter(map, sessionid) {
    var latitude = "<?php echo $latitude?>";
    var longitude = "<?php echo $longitude?>";
    var zoom = "<?php echo $zoom?>";

    if (map.getCenter() === undefined && latitude === '' && longitude === '') {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                redirectURL(13, position.coords.latitude, position.coords.longitude, "<?php echo $keyword; ?>");
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
                        var form = '<form method="post" action="' + "<?= base_url('index.php/explore/index/'.$zoom.'/'.$latitude.'/'.$longitude) ?>" + '/' + locations[i][6].trim() + '">';

                        var ratingsystem = '<h4>Rate This Location:</h4>';
                        ratingsystem += '<label><input type="radio" id="locationrating" name="locationrating" value="1" onclick="stars(this.name);" /><img id="locationratingstar1" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>';
                        ratingsystem += '<label><input type="radio" id="locationrating" name="locationrating" value="2" onclick="stars(this.name);" /><img id="locationratingstar2" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>';
                        ratingsystem += '<label><input type="radio" id="locationrating" name="locationrating" value="3" onclick="stars(this.name);" /><img id="locationratingstar3" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>';
                        ratingsystem += '<label><input type="radio" id="locationrating" name="locationrating" value="4" onclick="stars(this.name);" /><img id="locationratingstar4" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>';
                        ratingsystem += '<label><input type="radio" id="locationrating" name="locationrating" value="5" onclick="stars(this.name);" /><img id="locationratingstar5" src="<?php echo base_url('assets/images/starwhite.png') ?>"/></label>';

                        var keywordlist = '<h4>Keywords:</h4><div class="keywords">';
                        <?php foreach ($keywords as $keyword): ?>
                            var checked='';
                            if (locations[i][9].indexOf("<?php echo $keyword->keyword ?>") >= 0) {
                                checked = 'checked';
                            }
                            keywordlist += '<input id="keywords[]" name="keywords[]" type="checkbox" value=" <?php echo $keyword->id ?> "' + checked + ' />' + " <?php echo $keyword->keyword ?> " + '<br>';
                        <?php endforeach; ?>
                        keywordlist += '</div>';

                        var hidden = '<input type="hidden" id="title" name="title" value="' + locations[i][0] + '"><input type="hidden" id="description" name="description" value="' + locations[i][3] + '"><input type="hidden" id="icon" name="icon" value="' + locations[i][4] + '">'
                        var submit = '<input type="submit" value="Submit Rating and Keywords"><input type="submit" id="edit" name="edit" value="Edit Information"></form>';

                        div[0].innerHTML = form + ratingsystem + keywordlist + hidden + submit;

                        if (locations[i][7] > 0) {
                            stars('locationrating', parseInt(locations[i][7]));
                        }
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
        document.getElementById('pointInformation').innerHTML = buildForm(event.latLng.lat(), event.latLng.lng(), '', '', '');
    });	
}

function buildForm(latitude, longitude, title, description, icon) {
    var h2;
    var submittext;
    if (title !== '') {
        h2 = 'Existing Location';
        submittext = 'Update Location';
    }
    else
    {
        h2 = 'New Location';
        submittext = 'Add Location';
    }

    var selected1 = '';
    if (icon == 1) {
        selected1 = 'checked';
    }
    var selected2 = '';
    if (icon == 2) {
        selected2 = 'checked';
    }
    var selected3 = '';
    if (icon == 3) {
        selected3 = 'checked';
    }
    var selected4 = '';
    if (icon == 4) {
        selected4 = 'checked';
    }
    var selected5 = '';
    if (icon == 5) {
        selected5 = 'checked';
    }
    var selected6 = '';
    if (icon == 6) {
        selected6 = 'checked';
    }
    var selected7 = '';
    if (icon == 7) {
        selected7 = 'checked';
    }
    var selected8 = '';
    if (icon == 8) {
        selected8 = 'checked';
    }

    var heading = '<h2>' + h2 + '</h2>';
    var divstart = '<div class="content">';
    var instructions = '<p>Enter a title and description and click the button to submit your point for review.</p>';
    var latlong = '<p><input type="hidden" name="latitude" value=' + latitude + '><input type="hidden" name="longitude" value=' + longitude + '></p>';
    var title = '<p><label for="title">Title:</label><input type="text" name="title" value="' + title + '"></p>';
    var description = '<p>Description:</p><textarea name="description" rows="5" cols="60">' + description + '</textarea></p>';
    var icon = '<p><label for="icon">Icon:</label><div id="divIcon"><input type="radio" name="icon" value="1" class="radioIcon" ' + selected1 + '><img src=" <?php echo base_url('assets/images/Playground-50.png') ?> " /></input><input type="radio" name="icon" value="2" class="radioIcon" ' + selected2 + '><img src=" <?php echo base_url('assets/images/Pullups Filled-50.png') ?> " /></input><input type="radio" name="icon" value="3" class="radioIcon" ' + selected3 + '><img src=" <?php echo base_url('assets/images/City Bench-50.png') ?> " /></input><input type="radio" name="icon" value="4" class="radioIcon" ' + selected4 + '><img src=" <?php echo base_url('assets/images/Weight-50.png') ?> " /></input><input type="radio" name="icon" value="5" class="radioIcon" ' + selected5 + '><img src=" <?php echo base_url('assets/images/Pushups-50.png') ?> " /></input><input type="radio" name="icon" value="6" class="radioIcon" ' + selected6 + '><img src=" <?php echo base_url('assets/images/Stadium-50.png') ?> " /></input><input type="radio" name="icon" value="7" class="radioIcon" ' + selected7 + '><img src=" <?php echo base_url('assets/images/Trekking-50.png') ?> " /></input><input type="radio" name="icon" value="8" class="radioIcon" ' + selected8 + '><img src=" <?php echo base_url('assets/images/Climbing Filled-50.png') ?> " /></input><input type="radio" name="icon" value="9" class="radioIcon"><img src=" <?php echo base_url('assets/images/Wakeup Hill on Stairs-50.png') ?> " /></input></div></p>';
    var submit = '<p><input type="submit" value="' + submittext + '"><input type="button" value="Cancel" onClick="redirectURL(' + <?php echo $zoom?> + ',' + <?php echo $latitude?> + ',' + <?php echo $longitude?> + ');"></p>';
    var divend = '</div>';

    return '<form action="' + "<?= base_url('index.php/explore/index/'.$zoom.'/'.$latitude.'/'.$longitude.'/'.$pointid) ?>" + '">' + heading + divstart + instructions + latlong + icon + title + description + submit + divend + '</form>';
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
