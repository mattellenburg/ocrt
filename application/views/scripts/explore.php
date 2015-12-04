<?php include 'map.php';?>
<script>
    function initialize() {
        var url = 'index.php/explore/index/';
        var map = initializeMap(url);
        var sessionid = " <?php if (isset($_SESSION['user_id'])) { echo $_SESSION['user_id']; } else { echo 0; } ?> ";
        getMapCenter(url, map, sessionid);    

        if (sessionid > 0) {
            addMapClickEvent(url, map);
        }    
    }

    function placeMarker(url, pos, map) {
        var marker=new google.maps.Marker();

        marker.setPosition(pos);
        marker.setMap(map);

        google.maps.event.clearListeners(map, 'click');

        google.maps.event.addListener(marker, 'dblclick', function(event) {
            redirectURL(url, "<?php echo $mapview?>", "<?php echo $zoom?>", "<?php echo $latitude?>", "<?php echo $longitude?>", "<?php if (isset($pointid)) { echo $pointid; } ?>");
        });

        return marker;
    }

    function addMapClickEvent(url, map) {
        google.maps.event.addListener(map, 'click', function(event) {
            placeMarker(url, event.latLng, map);
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
