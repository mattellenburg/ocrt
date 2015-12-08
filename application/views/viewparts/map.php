<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="map">
    <h2>Map <input type="text" name="address" id="address" onkeydown="if (event.keyCode === 13) document.getElementById('go').click()" placeholder="Enter location" /> <input id="go" type="button" value="Go" onclick="getLatLong('<?php echo $url; ?>');" /></h2>
    <div class="content">
        <p><?php echo $instructions; ?></p>
        <div id="googleMap"></div>
    </div>
</div>