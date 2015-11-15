<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (validation_errors()) : ?>
    <?= validation_errors() ?>
<?php endif; ?>
<?php if (isset($error)) : ?>
    <?= $error ?>
<?php endif; ?>
<h1>Races</h1>
<?= form_open() ?>
<p><label for="id">ID:</label><input type="text" id="id" name="id" value="<?php if (isset($race)) { echo $race->id; } ?>" readonly></p>
<p><label for="race">Race:</label><input type="text" id="race" name="race" placeholder="Enter race" value="<?php if (isset($race)) { echo $race->race; } ?>"></p>
<p><label for="date">Date:</label><input type="text" id="date" name="date" placeholder="Enter date" value="<?php if (isset($race)) { echo $race->date; } ?>"></p>
<p><label for="location">Location:</label><input type="text" id="location" name="location" placeholder="Enter location" value="<?php if (isset($race)) { echo $race->location; } ?>"></p>
<p><label for="description">Description:</label><input type="text" id="description" name="description" placeholder="Enter description" value="<?php if (isset($race)) { echo $race->description; } ?>"></p>
<p><input type="submit" value="Add/Update Race"></p>
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