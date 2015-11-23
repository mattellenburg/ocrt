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
    <?php } else {?>
    <?php }?>
    <link href="<?= base_url('assets/css/default.css') ?>" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.7.2.min.js" type="text/javascript" > </script>
    <script type="text/javascript">
        $( document ).ready( function( ) {
            $( '.tree li' ).each( function() {
                if( $( this ).children( 'ul' ).length > 0 ) {
                    $( this ).addClass( 'parent' );  
                }
            });

            $( '.tree li.parent > a' ).click( function( ) {
                $( this ).parent().toggleClass( 'active' );
                $( this ).parent().children( 'ul' ).slideToggle( 'fast' );
            });

            $( '#all' ).click( function() {
                $( '.tree li' ).each( function() {
                    $( this ).toggleClass( 'active' );
                    $( this ).children( 'ul' ).slideToggle( 'fast' );
                });
            });
        });
    </script>

</head>
<body>
    <div id="header">
        <div id="title">
            <h1>(O)bstacle (C)ourse (R)ace (T)raining</h1>
            <p>Explore, train, and race...</p>
        </div>
        <div id="navigation">
            <ul>
                <li><a href="<?= base_url('index.php/home/index') ?>">Home</a></li>
                <li><a href="<?= base_url('index.php/explore/index') ?>">Explore</a></li>
                <li><a href="<?= base_url('index.php/train/index') ?>">Train</a></li>
                <li><a href="<?= base_url('index.php/race/index') ?>">Race</a></li>    
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
        <div id="message">
            <p><?php if(isset($message)) { echo $message; } ?><p>
            <p><?php if(isset($pageinformation)) { echo $pageinformation; } ?><p>
        </div>

    
		
