<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="login">
    <?php if (validation_errors()) : ?>
        <?= validation_errors() ?>
    <?php endif; ?>
    <?php if (isset($error)) : ?>
        <?= $error ?>
    <?php endif; ?>

    <h1>Login</h1>
    <?= form_open() ?>
    <p><label for="username">Email:</label><input type="text" id="email" name="email" placeholder="Your email address"></p>
    <p><label for="password">Password:</label><input type="password" id="password" name="password" placeholder="Your password"></p>
    <p><input type="submit" value="Login"></p>
    </form>
</div>