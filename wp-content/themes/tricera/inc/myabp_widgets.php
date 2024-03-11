<?php
if ( class_exists('WP_Widget') ) {  

 /**
  * Include Widgets
  */
 include_once 'widgets/widget_most_played.php';
 include_once 'widgets/widget_most_rated.php';
 include_once 'widgets/widget_user_panel.php';
 include_once 'widgets/widget_youtube_video.php';
 include_once 'widgets/widget_ads125.php';
 include_once 'widgets/widget_recent_games.php';
 include_once 'widgets/widget_random_games.php';
 include_once 'widgets/widget_advertisement.php';
 include_once 'widgets/widget_mochi_global_scores.php';
 
 
  if ( !function_exists('myabp_register_widgets') ) {
    function myabp_register_widgets() {
      register_widget('WP_Widget_MABP_Youtube_Video');
      register_widget('WP_Widget_MABP_User_Login');
      register_widget('WP_Widget_MABP_Recent_Games');
      register_widget('WP_Widget_MABP_Random_Games');
      register_widget('WP_Widget_MABP_Most_Rated');
      register_widget('WP_Widget_MABP_Most_Played');
      register_widget('WP_Widget_MABP_Banner_125');
      register_widget('WP_Widget_MABP_Advertisement');
      register_widget('WP_Widget_MABP_Mochi_Global_Scores');
    }
  }

  add_action('widgets_init', 'myabp_register_widgets'); 
}
?>