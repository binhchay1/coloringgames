<?php
/**
 * Widget MyScoresPresenter Game Scores
 */
class WP_Widget_MyScore_Game_Scores extends WP_Widget {

  // Constructor
  function __construct() {

    $widget_ops   = array('description' => __('Shows the scores of a game. This widget can only be displayed on single post template.', 'myscorespresenter') );

    parent::__construct('myscore_game_scores', __('(MyScore) Game Scores', 'myscorespresenter'), $widget_ops);
  }

  // Display Widget
  function widget($args, $instance) {
    global $mypostid;

    //var_dump($mypostid); die();

    if ( ( is_singular() || is_page() ) && !empty($mypostid) ) {

      // Check if this game supports scores
      if ( ! get_post_meta( $mypostid, 'mabp_leaderboard', true ) )
        return;

      extract($args);

      $title = apply_filters('widget_title', esc_attr($instance['title']));
      $limit = intval($instance['limit']);

      echo $before_widget.$before_title.$title.$after_title;
      echo '<ul class="widget-game-scores">'."\n";
      myscore_get_game_scores($limit);
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

    $instance = wp_parse_args( (array) $instance, array('title' => __('Game Scores', 'myscorespresenter'), 'limit' => 10));

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
register_widget('WP_Widget_MyScore_Game_Scores');
