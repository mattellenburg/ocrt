<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Obstacle Course Race Training</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">

    <!-- css -->
    <link href="<?= base_url('assets/css/default.css') ?>" rel="stylesheet">
</head>
<body>
    <div id="header">
        <div id="title">
            <h1><a href="#">Obstacle Course Race Training</a></h1>
        </div>
        <div id="navigation">
            <ul>
                <li><a href="<?= base_url('index.php/home/index') ?>">Home</a></li>
                <li><a href="<?= base_url('index.php/explore/index') ?>">Explore</a></li>
                <li><a href="<?= base_url('index.php/train/index') ?>">Train</a></li>
                <li><a href="<?= base_url('index.php/race/index') ?>">Race</a></li>    
                <li><a href="<?= base_url('index.php/about/index') ?>">About</a></li>
                <li><a href="<?= base_url('index.php/contact/index') ?>">Contact</a></li>
                <?php if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true) : ?>
                    <?php if ($_SESSION['user_id'] == 1) : ?>
                        <li><a href="<?= base_url('index.php/admin/index') ?>">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="<?= base_url('index.php/logout') ?>">Logout</a></li>
                <?php else : ?>
                    <li><a href="<?= base_url('index.php/register') ?>">Register</a></li>
                    <li><a href="<?= base_url('index.php/login') ?>">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div id="main">
    
		
