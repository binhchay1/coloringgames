<?php
/*
Plugin Name:  Full Screen Mod for MyArcadePlugin Pro
Plugin URI:   http://myarcadeplugin.com
Description:  Show a game in full screen mode
Version:      2.0.0
Author:       Daniel Bakovic
Author URI:   http://netreview.de
*/
?>
<html>
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />

    <title><?php bloginfo('name'); ?> - Playing game: - <?php single_post_title(); ?></title>

    <?php wp_head(); ?>

    <style type="text/css">
    #fullgame {
      width:95%;
      margin:5px auto 0 auto;
      display:block;
      position: relative;
    }

    #fullgame h2 {
      background:#270d3b;
      line-height:40px;
      margin-bottom: 5px;
      -moz-border-radius: 4px;
      -webkit-border-radius: 4px;
      border-radius:4px;
    }

    #fullgame h2 a { color: #9c6fbb; text-decoration: none; }
    #fullgame h2 a:hover { color: #ffffff; }
    </style>

  </head>

  <body>
    <center>
      <div id="fullgame">
        <h2><a href="javascript:history.go(-1)">&laquo; <?php _e('Go Back To', 'tricera'); ?>: <?php bloginfo('name'); ?> </a></h2>
      </div>
        <?php
          global $mypostid, $post;
          $mypostid = $post->ID;
          // overwrite the post id
          $post->ID = $mypostid;
          if ( function_exists('myarcade_get_leaderboard_code') )  {
            echo myarcade_get_leaderboard_code();
          }
          echo get_game($mypostid, $fullsize = false, $preview = false, $fullscreen = true);
        ?>
    </center>

    <?php wp_footer(); ?>
  </body>
</html>