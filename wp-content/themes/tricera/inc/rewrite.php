<?php
/**
 * Tricera Redirect / Rewrite Rules
 *
 * @package WordPress
 * @subpackage Tricera
 */

/**
 * Add rewrite rules on theme switching and flush rewrite rules
 *
 */
function tricera_adjust_permalinks( $oldname, $oldtheme = false ) {
  add_rewrite_endpoint( 'fullscreen', EP_PERMALINK );
  flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'tricera_adjust_permalinks', 0 );

/**
 * Add required rewrite rules
 *
 */
function tricera_add_rules() {
  // Check if fullscreen option is enabled
  if ( tricera_get_option('fullscreen_button') ) {
    add_rewrite_endpoint( 'fullscreen', EP_PERMALINK );
    add_action( 'template_redirect', 'tricera_fullscreen_template_redirect' );
  }
}
add_action( 'init', 'tricera_add_rules', 0 );


function tricera_fullscreen_template_redirect() {
  global $wp_query;

  // if this is not a fullscreen request then bail
  if ( ! is_singular() || ! isset( $wp_query->query_vars['fullscreen'] ) ) {
    return;
  }

  // Include fullscreen template
  get_template_part( "single", "fullscreen" );
  exit;
}
