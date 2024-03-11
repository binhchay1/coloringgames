<?php

/*
 * Shows mochi's global scores widget
 * 
 */

if ( !class_exists('WP_Widget_MABP_Mochi_Global_Scores') ) {
  class WP_Widget_MABP_Mochi_Global_Scores extends WP_Widget {
  
    // Constructor
    function WP_Widget_MABP_Mochi_Global_Scores() {
      
      $widget_ops = array('description' => 'Mochi Global Leaderboard Widget. This widget is only displayed on single pages.');      
      $this->WP_Widget('MABP_Mochi_Global_Scores', 'MyArcade Mochi Global Scores', $widget_ops);
    }
    
    // Display Widget
    function widget($args, $instance) {
      extract($args);
      
      global $mypostid, $wpdb;

      $title = apply_filters('widget_title', esc_attr($instance['title']));
      $width = intval($instance['width']);
      $height = intval($instance['height']);
      $bgcolor = esc_attr($instance['bgcolor']);
      
      if ( is_single() ) {
        $game_type = $wpdb->get_var("SELECT game_type FROM ".MYARCADE_GAME_TABLE." WHERE postid = '".$mypostid."' LIMIT 1");
        
        if ( $game_type == 'mochi' || empty($game_type) ) {
          $mochi_settings = get_option('myarcade_mochi');
          $lb_enable = $wpdb->get_var("SELECT leaderboard_enabled FROM ".MYARCADE_GAME_TABLE." WHERE postid = '".$mypostid."' LIMIT 1");
        
          if ( $mochi_settings['global_score'] && !empty($lb_enable) ) {
            $game_slug = $wpdb->get_var("SELECT slug FROM ".MYARCADE_GAME_TABLE." WHERE postid = '".$mypostid."'");
            
            if ( !empty($game_slug) ) {            
              echo $before_widget;
              
              if($title) {
                echo $before_title . $title . $after_title;
              }
              
              $output  = '<div id="leaderboard_widget"></div>';
              $output .= '<script type="text/javascript">';
              $output .= 'var options = {game: "'.$game_slug.'", width: '.$width.', height: '.$height.', id: "leaderboard_widget"};';
              $output .= 'options.backgroundColor = "'.$bgcolor.'";';
              $output .= 'Mochi.showLeaderboardWidget(options);';
              $output .= '</script>';
              echo $output;
              echo $after_widget;              
            }            
          }          
        }
      }
    }
    
    // Update Widget
    function update($new_instance, $old_instance) {
    
      $instance = $old_instance;
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['width'] = intval($new_instance['width']);
      $instance['height'] = intval($new_instance['height']);
      $instance['bgcolor'] = esc_attr($new_instance['bgcolor']);
      
      return $instance;
    }
    
    // Display Widget Control Form
    function form($instance) {
      global $wpdb;
      
      $instance = wp_parse_args((array) $instance, array('title' => 'Global Scores', 'width' => 335, 'height' => 500, 'bgcolor' => '#DFE3E6'));
      $title = esc_attr($instance['title']);
      $width = intval($instance['width']);
      $height = intval($instance['height']);
      $bgcolor = esc_attr($instance['bgcolor']);
      ?>
      
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>">
          Title 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </label>
      </p>
      <p>
        <label for="<?php echo $this->get_field_id('width'); ?>">
          Width 
          <input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
        </label>
      </p>
      <p>
        <label for="<?php echo $this->get_field_id('height'); ?>">
          Height 
          <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />
        </label>
      </p>       
      <p>
        <label for="<?php echo $this->get_field_id('bgcolor'); ?>">
          Background Color 
          <input class="widefat" id="<?php echo $this->get_field_id('bgcolor'); ?>" name="<?php echo $this->get_field_name('bgcolor'); ?>" type="text" value="<?php echo $bgcolor; ?>" />
        </label>
      </p>      
      <?php
    }
  }
}
?>