<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="profile">
    <h2>Profile</h2>
    <div class="content">
        <?php if (validation_errors()) : ?>
            <?= validation_errors() ?>
        <?php endif; ?>
        <?php if (isset($error)) : ?>
            <?= $error ?>
        <?php endif; ?>
        <?= form_open() ?>
        <p>* Indicates required fields</p>
        <p><label for="email">Email: *</label><input type="email" id="email" name="email" value="<?php echo $email; ?>" placeholder="Enter your email"></p>
        <p><label for="password">Password: </label><input type="password" id="password" name="password" placeholder="Enter a password"></p>
        <p><label for="password_confirm">Confirm Password: </label><input type="password" id="password_confirm" name="password_confirm" placeholder="Confirm your password"></p>
        <p><label for="runpace_minutes">Running Pace:</label><input type="text" id="runpace_minutes" name="runpace_minutes" value="<?php echo $runpace_minutes; ?>" placeholder="e.g. 8"><input type="text" id="runpace_seconds" name="runpace_seconds" value="<?php echo $runpace_seconds; ?>" placeholder="e.g. 30"></p>
        <p><input type="submit" value="Update Profile"></p>
        </form>
    </div>
</div>