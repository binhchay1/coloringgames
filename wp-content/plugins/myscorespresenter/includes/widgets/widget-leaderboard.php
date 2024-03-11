<?php
/**
 * Widget MyScoresPresenter Leaderboard
 */
class WP_Widget_MyScore_Leaderboard extends WP_Widget {

  // Constructor
  function __construct() {

    $widget_ops   = array('description' => __('Show players who hold the most highscores.', 'myscorespresenter') );

    parent::__construct('myscore_leaderboard', __('(MyScore) Highscore Leaderboard', 'myscorespresenter'), $widget_ops);
  }

  // Display Widget
  function widget($args, $instance) {

    extract($args);

    $title = apply_filters('widget_title', esc_attr($instance['title']));
    //$limit = intval($instance['limit']);

    echo $before_widget.$before_title.$title.$after_title;
    echo '<ul>'."\n";
    myscore_get_leaderboard();
    echo '</ul>'."\n";
    echo $after_widget;
  }

  // Update Widget
  function update($new_instance, $old_instance) {

    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    //$instance['limit'] = intval($new_instance['limit']);

    return $instance;
  }

  // Display Widget Control Form
  function form($instance) {
    global $wpdb;

    $instance = wp_parse_args((array) $instance, array('title' => __('Leaderboard', 'myscorespresenter')));

    $title = esc_attr($instance['title']);
    //$limit = intval($instance['limit']);

    ?>

    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e("Title", 'myscorespresenter'); ?>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
      </label>
    </p>
   <?php
  }
}
register_widget('WP_Widget_MyScore_Leaderboard');
