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
            <?= form_open() ?>
                <p><?php echo form_dropdown('route', $routes); ?><input type="submit" id="submitloadroute" name="submitloadroute" value="Load Route"/><input type="submit" id="submitclearroute" name="submitclearroute" value="Clear Route"/></p>
                <div id="route"></div>
            </form>
        </div>
    </div>
    <div id="locationworkouts">
        <h2>Location Workouts</h2>
        <div class="content">
            <?= form_open() ?>
            <div id="workout"></div>
            </form>
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
