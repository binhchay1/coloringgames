<?php
/**
 * Widget MyScoresPresenter User Achievements
 */
class WP_Widget_MyScore_User_Achievements extends WP_Widget {

  // Constructor
  function __construct() {
    $widget_ops   = array('description' => __("Shows user's achievements for a certain game on single post page.", 'myscorespresenter') );
    parent::__construct('myscore_user_achievements', __('(MyScore) Game Achievements', 'myscorespresenter'), $widget_ops);
  }

  // Display Widget
  function widget($args, $instance) {
    global $mypostid, $post, $wpdb;

    if ( ( is_singular() || is_page() ) && ! empty( $post->ID ) ) {

      // Check if this game supports scores
      if ( ! get_post_meta( $post->ID, 'mabp_leaderboard', true ) ) {
        return;
      }

      $user_ID = get_current_user_id();

      if ( ! $user_ID ) {
        return;
      }

      $game_tag = myscore_get_game_tag( $post->ID );

      $achievements = $wpdb->get_results( "SELECT * FROM ".myscore_get_table_name('medal')." WHERE user_id = '{$user_ID}' AND game_tag = '{$game_tag}' ORDER BY score ASC" );

      if ( ! $achievements ) {
        return;
      }

      extract($args);

      $title = apply_filters('widget_title', esc_attr($instance['title']));

      echo $before_widget.$before_title.$title.$after_title;
      echo "<p>";
      foreach ( $achievements as $achievement ) {
        echo '<img src="'.$achievement->thumbnail.'" title="'.$achievement->description.'" class="achievements" style="margin-right: 5px;" width="50" />';
      }
      echo "</p>";
      echo $after_widget;
    }
  }

  // Update Widget
  function update($new_instance, $old_instance) {

    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);

    return $instance;
  }

  // Display Widget Control Form
  function form($instance) {
    global $wpdb;

    $instance = wp_parse_args((array) $instance, array('title' => __('My Achievements', 'myscorespresenter') ) );

    $title = esc_attr($instance['title']);

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
register_widget('WP_Widget_MyScore_User_Achievements');
