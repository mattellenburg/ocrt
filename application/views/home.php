<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="home">
    <div class="homeblock">
        <a href="<?= base_url('index.php/explore/index') ?>">
            <img src="<?= base_url('/assets/images/explore.png') ?>"</img>
            <h2>Explore</h2>
        </a>
    </div>
    <div class="homeblock">
        <a href="<?= base_url('index.php/train/index') ?>">
            <img src="<?= base_url('/assets/images/train.png') ?>"</img>
            <h2>Train</h2>
        </a>
    </div>
    <div class="homeblock">
        <a href="<?= base_url('index.php/race/index') ?>">
            <img src="<?= base_url('/assets/images/race.png') ?>"</img>
            <h2>Race</h2>
        </a>
    </div>
</div>

