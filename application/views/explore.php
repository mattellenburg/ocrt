<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="explore">
    <?php 
        $data = new stdClass();
        $data->instructions = 'Click on the map to submit a new location.';
        $data->url = 'index.php/explore/index/';

        $this->load->view('viewparts/map.php', $data); 
    ?>
    <div id="pointInformation">
        <h2>Location Information</h2>
        <div class="content">
            <h3></h3>
            <p></p>
            <div class="scroll"></div>
        </div>
    </div>
    <?php 
        $this->load->view('viewparts/filters.php'); 
    ?>
</div>
<?php include 'scripts/explore.php';?>