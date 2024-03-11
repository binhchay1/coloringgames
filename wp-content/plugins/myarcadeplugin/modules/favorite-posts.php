<?php
/**
 * Module:       MyArcadePlugin Favorite-Posts BuddyPress Integration
 * @author Daniel Bakovic <contact@myarcadeplugin.com>
*/

// Proceed only if WP Favorite Posts is installed
if ( defined( 'WPFP_PATH' ) ) :

function myarcade_bp_activity_new_favorite($postid = false) {

  if ( !function_exists('bp_activity_add')
    || !bp_is_active('activity')
    || !function_exists('myarcade_new_bp_activity')
    || !$postid )
  {
    return false;
  }

  $current_user = wp_get_current_user();

  if ( ! $current_user->ID ) {
    return false;
  }

  $submit = get_user_meta( $current_user->ID, 'myarcade_bp_activity_game_favorite', true);
  if ( !$submit ) {
    update_user_meta( $current_user->ID, 'myarcade_bp_activity_game_favorite', 'yes');
  }

  if ( $submit == 'no') {
    return;
  }

  $post = get_post($postid);
  $game_link = '<a href="'.get_permalink($postid).'" title="">'.$post->post_title.'</a>';
  $userlink = bp_core_get_userlink( $current_user->ID );

  $message_filter = apply_filters('myarcade_bp_new_favorite_message',  __( 'I have marked %s as one of my favorite games!', 'myarcadeplugin') );
  $action_filter = apply_filters('myarcade_bp_new_favorite_action', __( '%s added to favorites', 'myarcadeplugin'));

  $message = sprintf( $message_filter, $game_link);
  $action  = sprintf( $action_filter, $userlink );

  myarcade_new_bp_activity( array(
    'user_id' => $current_user->ID,
    'content' => $message,
    'action' => $action,
    'type' => 'new_favorite' )
  );
}
add_action('wpfp_after_add', 'myarcade_bp_activity_new_favorite');


function myarcade_bp_activity_remove_favorite($postid = false) {

  if ( !function_exists('bp_activity_add')
    || !bp_is_active('activity')
    || !function_exists('myarcade_new_bp_activity')
    || !$postid )
  {
    return false;
  }

  $current_user = wp_get_current_user();

  if ( ! $current_user->ID ) {
    return false;
  }

  $submit = get_user_meta( $current_user->ID, 'myarcade_bp_activity_game_favorite_remove', true);
  if ( !$submit ) {
    update_user_meta( $current_user->ID, 'myarcade_bp_activity_game_favorite_remove', 'yes');
  }

  if ( $submit == 'no') {
    return;
  }

  $post = get_post($postid);
  $game_link = '<a href="'.get_permalink($postid).'" title="">'.$post->post_title.'</a>';
  $userlink = bp_core_get_userlink( $current_user->ID );

  $message_filter = apply_filters('myarcade_bp_remove_favorite_message',  __( 'I have removed %s from my favorites!', 'myarcadeplugin') );
  $action_filter = apply_filters('myarcade_bp_remove_favorite_action', __( '%s removed from favorites', 'myarcadeplugin'));

  $message = sprintf( $message_filter, $game_link);
  $action  = sprintf( $action_filter, $userlink );

  myarcade_new_bp_activity( array(
    'user_id' => $current_user->ID,
    'content' => $message,
    'action' => $action,
    'type' => 'remove_favorite' )
  );
}
add_action('wpfp_after_remove', 'myarcade_bp_activity_remove_favorite');
endif;
?>