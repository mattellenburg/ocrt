<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="train">
    <h2>Train</h2>
    <div class="content">
        <h3>Obstacles & Exercises <button id="all">Toggle All</button></h3>
        <div class="tree">
            <?php echo ul($obstacles); ?>
        </div>
    </div>
</div>