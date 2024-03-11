<?php

/*
 * Shows an advertisement banner
 */

if ( !class_exists('WP_Widget_MABP_Advertisement') ) {
  class WP_Widget_MABP_Advertisement extends WP_Widget {

      // Constructor
      function WP_Widget_MABP_Advertisement() {

        $widget_ops   = array('description' => 'Show advertisements where ever you want in your sidebar.');

        $this->WP_Widget('MABP_Advertisement', 'MyArcade Advertisement', $widget_ops);
      }

      // Display Widget
      function widget($args, $instance) {
        extract($args);

        $title = apply_filters('widget_title', esc_attr($instance['title']));
        $adcode = $instance['adcode'];

        echo $before_widget;

        if($title) {
          echo $before_title . $title . $after_title;
        }
        // <-- START --> HERE COMES THE OUPUT

        ?>      
        <div class="adwidget">
          <?php echo $adcode; ?>
        </div>
        <?php

        // <-- END --> HERE COMES THE OUPUT
        echo $after_widget;
      }

      // Update Widget
      function update($new_instance, $old_instance) {

        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['adcode'] = $new_instance['adcode'];

        return $instance;
      }

      // Display Widget Control Form
      function form($instance) {
        global $wpdb;

        $instance = wp_parse_args((array) $instance, array('title' => 'Advertisement', 'adcode' => ''));

        $title = esc_attr($instance['title']);
        $adcode = $instance['adcode'];

        ?>

        <p>
          <label for="<?php echo $this->get_field_id('title'); ?>">
            Title 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
          </label>
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('adcode'); ?>">
            336x280px Banner Code
            <textarea rows="10" class="widefat" id="<?php echo $this->get_field_id('adcode'); ?>"  name="<?php echo $this->get_field_name('adcode'); ?>"><?php echo $adcode; ?></textarea>
          </label>
        </p>

        <?php
      }
    }
}
?>