<?php
/**
 * Widget MyScoresPresenter Total Playtime
 */
class WP_Widget_MyScore_Leaderboards extends WP_Widget {

  // Constructor
  public function __construct() {

    $widget_ops   = array( 'description' => __( 'Shows leaderboards based on specific criterias.', 'myscorespresenter' ) );

    parent::__construct( 'myscore_leaderboards', __('(MyScore) Leaderboards', 'myscorespresenter' ), $widget_ops );
  }

  // Display Widget Control Form
  public function form( $instance ) {

    $instance = wp_parse_args( (array) $instance, array(
      'title' => __('Leaderboards', 'myscorespresenter'),
      'type'  => 'total_score',
      'limit' => 10
    ) );

    myscore_form_text( array(
      'field_title' => __( "Title", 'myscorespresenter' ),
      'field_id'    => $this->get_field_id( 'title' ),
      'field_name'  => $this->get_field_name( 'title' ),
      'value'       => $instance['title'],
    ) );

    myscore_form_select( array(
      'field_title' => __( "Leaderboard Type", 'myscorespresenter' ),
      'field_id' => $this->get_field_id( 'type' ),
      'field_name' => $this->get_field_name( 'type' ),
      'options' => array(
        'daily_scores'   => __( 'Daily scores', 'myscorespresenter' ),
        'weekly_scores'  => __( 'Weekly scores (last 7 days)', 'myscorespresenter' ),
        'monthly_scores' => __( 'Monthly scores', 'myscorespresenter' ),
        'total_scores'   => __( 'Total scores', 'myscorespresenter' ),
        'total_playtime' => __( 'Total playtime', 'myscorespresenter' ),
      ),
      'selection' => $instance['type'],
    ));

    myscore_form_number( array(
      'field_title' => __( 'Player Count', 'myscorespresenter' ),
      'field_id'    => $this->get_field_id( 'limit' ),
      'field_name'  => $this->get_field_name( 'limit' ),
      'value'       => $instance['limit'],
    ) );
  }

  // Update Widget
  public function update( $new_instance, $old_instance ) {

    $instance          = $old_instance;
    $instance['title'] = strip_tags( $new_instance['title'] );
    $instance['type']  = strip_tags( $new_instance['type'] );
    $instance['limit'] = intval( $new_instance['limit'] );

    // Clean up transients
    //delete_transient( 'myscore_transient_most_active_players' );

    return $instance;
  }

  // Display Widget
  public function widget( $args, $instance ) {

    extract($args);

    $title = esc_attr( $instance['title']);
    $type  = $instance['type'];
    $limit = intval( $instance['limit'] );

    echo $before_widget.$before_title.$title.$after_title;
    echo '<ul class="myscore-widget-leaderboards '.$type.'">'."\n";

    $results  = array();
    $template = get_option('myscore_template_leaderboard');

    switch( $type ) {
      case 'daily_scores': {
        $results  = myscore_daily_leaderboard( $limit );
      } break;

      case 'weekly_scores': {
        $results  = myscore_weekly_leaderboard( $limit );
      } break;

      case 'monthly_scores': {
        $results  = myscores_monthly_leaderboard( $limit );
      } break;

      case 'total_scores': {
        $results  = myscore_total_leaderboard( $limit );
      } break;

      case 'total_playtime': {
        $results  = myscore_users_by_total_playtime($limit);
        // Overwrite Template
        $template = get_option('myscore_template_play_duration');
      } break;
    }

    foreach ($results as $result) {
      echo '<li>'.myscore_expand_template( $template, $result ).'</li>' . "\n";
    }

    echo '</ul>'."\n";
    echo $after_widget;
  }
}
register_widget( 'WP_Widget_MyScore_Leaderboards' );
