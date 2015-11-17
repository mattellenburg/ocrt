<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<h2>Keywords</h2>
<div class="content">
    <?php if (validation_errors()) : ?>
        <?= validation_errors() ?>
    <?php endif; ?>
    <?php if (isset($error)) : ?>
        <?= $error ?>
    <?php endif; ?>
    <?= form_open() ?>
    <p><label for="id">ID:</label><input type="text" id="id" name="id" value="<?php if (isset($keyword)) { echo $keyword->id; } ?>" readonly></p>
    <p><label for="keyword">Keyword:</label><input type="text" id="keyword" name="keyword" placeholder="Enter keyword" value="<?php if (isset($keyword)) { echo $keyword->keyword; } ?>"></p>
    <p><label for="exercise">Exercise:</label><input type="checkbox" id="exercise" name="exercise" value="1" <?php if (isset($keyword)) { if ($keyword->exercise) { echo 'checked'; } } ?>></p>
    <p><label for="obstacle">Obstacle:</label><input type="checkbox" id="obstacle" name="obstacle" value="1" <?php if (isset($keyword)) { if ($keyword->obstacle) { echo 'checked'; } } ?>></p>
    <p><input type="submit" value="Add/Update Keyword"></p>
    </form>    
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
