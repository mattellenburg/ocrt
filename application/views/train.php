<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="train">
    <?php 
        $data = new stdClass();
        $data->instructions = 'Click on the map or a waypoint to create a route.';
        $this->load->view('viewparts/map.php', $data); 
    ?>
    <div id="routeInformation">
        <h2>Route Information</h2>
        <div class="content">
            <div class="scroll">
                <div id="route"></div>
            </div>
        </div>
    </div>
    <?php 
        $this->load->view('viewparts/filters.php'); 
    ?>

    <div id="obstaclesexercises">
        <h2>Obstacles & Exercises <button id="all">Toggle All</button></h2>
        <div class="content">
            <div class="tree">
                <?php echo ul($obstacles); ?>
            </div>
        </div>
    </div>
</div>
<?php include 'scripts/train.php';?>
