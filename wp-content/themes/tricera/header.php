<!doctype html>
<!--[if IE 8]><html class="ie-8" <?php language_attributes(); ?>><![endif]-->
<!--[if IE 9]><html class="ie-9" <?php language_attributes(); ?>><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html <?php language_attributes(); ?>><!--<![endif]-->

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name=viewport content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="<?php echo tricera_get_option('toolbar-color'); ?>">
  <meta name="msapplication-navbutton-color" content="<?php echo tricera_get_option('toolbar-color'); ?>">
  <meta name="apple-mobile-web-app-status-bar-style" content="<?php echo tricera_get_option('ios-toolbar-color', 'default'); ?>">

  <?php wp_head(); ?>
  <!--[if IE]><link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/IE.css" media="screen" /><![endif]-->
</head>

<body <?php body_class(); ?>>
  <div class="topbar">
    <div class="topbar_wrapper">
      <div class="topbarbox">

        <div class="logobox">
          <?php tricera_web_logo(); ?>
        </div>

        <div class="searchbox">
          <form method="post" action="<?php echo home_url('/search/'); ?>" id="search">
            <input type="text" class="searchINPUT" value="" id="search_textbox" name="s">
            <input type="submit" class="searchBTN" value="Submit Search">
          </form>
        </div>

        <?php
        if (is_user_logged_in()) {
          get_template_part('partials/header', 'user');
        } else {
          tricera_default_top_bar();
        }
        ?>
      </div>
    </div>
  </div>

  <?php
  if (is_user_logged_in() && defined('BP_VERSION')) {
    $class = 'contentbody';
  } else {
    $class = 'contentbody_NOBP';
  }
  ?>

  <div class="<?php echo $class; ?>">
    <div class="contentbody_wrapper">

      <?php
      if (is_user_logged_in() && defined('BP_VERSION')) {
        tricera_category();
      }
      ?>