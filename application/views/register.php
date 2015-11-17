<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="register">
    <h2>Register</h2>
    <div class="content">
        <?php if (validation_errors()) : ?>
            <?= validation_errors() ?>
        <?php endif; ?>
        <?php if (isset($error)) : ?>
            <?= $error ?>
        <?php endif; ?>
        <?= form_open() ?>
        <p><label for="email">Email:</label><input type="email" id="email" name="email" placeholder="Enter your email"></p>
        <p><label for="password">Password:</label><input type="password" id="password" name="password" placeholder="Enter a password"></p>
        <p><label for="password_confirm">Confirm Password:</label><input type="password" id="password_confirm" name="password_confirm" placeholder="Confirm your password"></p>
        <p><input type="submit" value="Register"></p>
        </form>
    </div>
</div>