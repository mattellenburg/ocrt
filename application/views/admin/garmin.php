<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h2>Garmin Activities</h2>
<div class="content">
    <?php
    $tmpl = array (
        'table_open'          => '<table border="1" cellpadding="2" cellspacing="0">',

        'heading_row_start'   => '<tr>',
        'heading_row_end'     => '</tr>',
        'heading_cell_start'  => '<th>',
        'heading_cell_end'    => '</th>',

        'row_start'           => '<tr>',
        'row_end'             => '</tr>',
        'cell_start'          => '<td width="100px">',
        'cell_end'            => '</td>',

        'row_alt_start'       => '<tr>',
        'row_alt_end'         => '</tr>',
        'cell_alt_start'      => '<td>',
        'cell_alt_end'        => '</td>',

        'table_close'         => '</table>'
    );

    $this->table->set_template($tmpl);

    echo $this->table->generate();          
    ?>
    <a href="<?= base_url('index.php/admin/garmin/1')?>"><<</a>&nbsp;&nbsp;&nbsp;<a href="<?= base_url('index.php/admin/garmin/'.floor($start-1))?>"><</a>&nbsp;&nbsp;&nbsp;Activities <?= $start*10 - 9 ?> - <?= $start*10 ?> of <?= $activities?>&nbsp;&nbsp;&nbsp;<a href="<?= base_url('index.php/admin/garmin/'.floor($start+1))?>">></a>&nbsp;&nbsp;&nbsp;<a href="<?= base_url('index.php/admin/garmin/'.floor($activities/10))?>">>></a>
</div>
