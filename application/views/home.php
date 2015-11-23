<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="home">
    <div id="message">
        Welcome to ocrt4me.com, a tool for people interested in obstacle course racing.  
    </div>
    <div class="homeblock">
        <a href="<?= base_url('index.php/explore/index') ?>">
            <img src="<?= base_url('/assets/images/explore.png') ?>"</img>
            <h2>Explore</h2>
            <p>Find locations to train.  Explore the map or search for specific exercises and obstacles.</p>
        </a>
    </div>
    <div class="homeblock">
        <a href="<?= base_url('index.php/train/index') ?>">
            <img src="<?= base_url('/assets/images/train.png') ?>"</img>
            <h2>Train</h2>
            <p>Explore exercises by obstacle.</p>
        </a>
    </div>
    <div class="homeblock">
        <a href="<?= base_url('index.php/race/index') ?>">
            <img src="<?= base_url('/assets/images/race.png') ?>"</img>
            <h2>Race</h2>
            <p>View a list of obstacle course races.</p>
        </a>
    </div>
</div>

