<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="train">
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