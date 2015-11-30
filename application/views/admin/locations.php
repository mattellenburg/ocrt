<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h2>Approve Points</h2>
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
</div>

