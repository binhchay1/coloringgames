<?php
/**
 * Widget MyScoresPresenter User Scores
 */
class WP_Widget_MyScore_User_Scores extends WP_Widget {

  // Constructor
  function __construct() {

    $widget_ops   = array('description' => __('Shows latest scores of an user (BuddyPress required).', 'myscorespresenter') );

    parent::__construct('myscore_user_scores', __('(MyScore) User Scores', 'myscorespresenter'), $widget_ops);
  }

  // Display Widget
  function widget($args, $instance) {

    // Show this only when we are on the users profile pages
    $userid = bp_displayed_user_id();
    if ( !empty($userid) ) {

      extract($args);

      $title = apply_filters('widget_title', esc_attr($instance['title']));
      $limit = intval($instance['limit']);

      echo $before_widget.$before_title.$title.$after_title;
      echo '<ul class="widget-user-scores">'."\n";
      myscore_show_users_scores($userid, $limit);
      echo '</ul>'."\n";
      echo $after_widget;
    }
  }

  // Update Widget
  function update($new_instance, $old_instance) {

    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['limit'] = intval($new_instance['limit']);

    return $instance;
  }

  // Display Widget Control Form
  function form($instance) {
    global $wpdb;

    $instance = wp_parse_args((array) $instance, array('title' => __('Latest User Scores', 'myscorespresenter'), 'limit' => 10));

    $title = esc_attr($instance['title']);
    $limit = intval($instance['limit']);

    ?>

    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e("Title", 'myscorespresenter'); ?>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
      </label>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('limit'); ?>">
        <?php _e("Limit", 'myscorespresenter'); ?>
        <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo $limit; ?>" />
      </label>
    </p>

    <?php
  }
}
register_widget('WP_Widget_MyScore_User_Scores');
