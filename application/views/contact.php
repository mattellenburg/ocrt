<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="contact">
    <?php if (validation_errors()) : ?>
        <?= validation_errors() ?>
    <?php endif; ?>
    <?php if (isset($error)) : ?>
        <?= $error ?>
    <?php endif; ?>
    <h2>Contact</h2>
    <?= form_open() ?>
    <p><label for="name">Name:</label><input type="text" id="email" name="name" placeholder="Enter your name"></p>
    <p><label for="email">Email:</label><input type="email" id="email" name="email" placeholder="Enter your email"></p>
    <p><label for="message">Message:</label><input type="text" id="email" name="message" placeholder="Enter your messasge"></p>
    <p><input type="submit" value="Submit"></p>
    </form>
</div>

