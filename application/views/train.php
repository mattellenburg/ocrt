<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="train">
    <h2>Train</h2>
    <div class="content">
        <ul>
            <li>
                Common Obstacles
                <ul>
                    <?php foreach ($obstacles as $obstacle): ?>
                        <li><?php echo $obstacle->keyword; ?></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li>
                Exercises
                <ul>
                    <?php foreach ($exercises as $exercise): ?>
                        <li><?php echo $exercise->keyword; ?></li>
                    <?php endforeach; ?>
                </ul>
            </li>
        </ul>
    </div>
</div>