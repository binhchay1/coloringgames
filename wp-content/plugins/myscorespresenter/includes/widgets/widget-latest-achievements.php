<?php
/**
 * Widget MyScoresPresenter User Achievements
 */
class WP_Widget_MyScore_Latest_Achievements extends WP_Widget {

  // Constructor
  function __construct() {
    $widget_ops   = array('description' => __("Shows latest achievements.", 'myscorespresenter') );
    parent::__construct('myscore_latest_achievements', __('(MyScore) Latest Achievements', 'myscorespresenter'), $widget_ops);
  }

  // Display Widget
  function widget($args, $instance) {
    global $wpdb;

    extract($args);

    if ( empty( $instance['limit'] ) )
      $instance['limit'] = 10;

    $title = esc_attr( $instance['title'] );
    $limit = intval( $instance['limit'] );

    $achievements = $wpdb->get_results( "SELECT * FROM ".myscore_get_table_name('medal')." ORDER BY id DESC LIMIT ".$limit );

    if ( ! $achievements ) {
      return;
    }

    echo $before_widget.$before_title.$title.$after_title;
    echo "<p>";
    foreach ( $achievements as $achievement ) {
      $post_id = myscore_get_post_id_by_tag( $achievement->game_tag );
      $user = get_userdata( $achievement->user_id );
      echo '<a href="'.get_permalink( $post_id ).'" title="'.esc_attr( strip_tags( get_the_title( $post_id ) ) ).'">';
      echo '<img src="'.$achievement->thumbnail.'" title="'.sprintf( __("%s - %s at %s", 'myscorespresenter'), $user->display_name, $achievement->description, esc_attr( strip_tags( get_the_title( $post_id ) ) )  ).'" class="achievements" style="margin-right: 5px;" width="50" />';
      echo '</a>';
    }
    echo "</p>";
    echo $after_widget;
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

    $instance = wp_parse_args((array) $instance, array('title' => __('Latest Achievements', 'myscorespresenter'), 'limit' => 10 ) );

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
register_widget('WP_Widget_MyScore_Latest_Achievements');
