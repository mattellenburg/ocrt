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

    <?php
    function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
    ?>
    <?php if (isMobile()) {?>
        <link href="<?= base_url('assets/css/defaultmobile.css') ?>" rel="stylesheet">
    <?php } else {?>
        <link href="<?= base_url('assets/css/default.css') ?>" rel="stylesheet">
    <?php }?>
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
                <li><a href="#">Train</a></li>
                <li><a href="#">Race</a></li>    
                <li><a href="<?= base_url('index.php/contact/index') ?>">Contact</a></li>
                <li><a href="<?= base_url('index.php/about/index') ?>">About</a></li>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['logged_in'] && $_SESSION['is_confirmed']) : ?>
                    <?php if ($_SESSION['is_admin']) : ?>
                        <li><a href="<?= base_url('index.php/admin/index') ?>">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="<?= base_url('index.php/user/logout') ?>">Logout</a></li>
                <?php else : ?>
                    <li><a href="<?= base_url('index.php/user/register') ?>">Register</a></li>
                    <li><a href="<?= base_url('index.php/user/login') ?>">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div id="main">
    
		
