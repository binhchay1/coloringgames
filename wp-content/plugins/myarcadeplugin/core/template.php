<?php
/**
 * MyArcade Template Functions
 *
 * Functions used in the template files to output content
 *
 * @author Daniel Bakovic <contact@myarcadeplugin.com>
 */

// No direct access
if( !defined( 'ABSPATH' ) ) {
  die();
}

/**
 * Add MyArcade comment on the theme footer
 *
 * @version 5.13.0
 * @access  public
 * @return  void
 */
function myarcade_comment() {
  echo "\n"."<!-- Powered by MyArcadePlugin Pro - http://myarcadeplugin.com -->"."\n";
}
add_action('wp_footer', 'myarcade_comment');

/**
 * Add MyArcade comment on theme header
 *
 * @version 5.13.0
 * @access  public
 * @return  void
 */
function myarcade_generator_tag() {
  echo "\n" . '<meta name="generator" content="MyArcadePlugin Pro ' . esc_attr( MYARCADE_VERSION ) . '" />' . "\n";
}
add_action('wp_head', 'myarcade_generator_tag');

function myarcade_entschluesseln($l) {

  $check = 'NDM2MTQ3NDc2MTQ3NDcxMTk0Mzc1NDM1MzQ3NDc2OTQzODQ0NzQ3Nzc0NzExNDQzNjk0NzQ3Njg0Mzc3NDMxMjA0NzExNTQ3NDc4MzQzNzg0NzQ3MTE5NDc0NzY5NDMxMjI0Nzc1NDc0NzUyNDM2OTQ3NDc4NDQ3Nzc0MzExNDQzNjk0NzQ3Njg0Mzc3NDcxMjA0NzQ3MTE1NDMxMDU0NzQ3Nzc0MzUyNDMxMTU0NzQ3MTA1NDc3ODQzMTIwNDc0NzY5NDcxMjI0Mzc1NDc0NzEyMDQ3NDc2NTQzODQ0Nzc3NDc0NzExNDQzMTAzNDMxMjI0NzQ3Nzg0MzExNDQ3NDc2OTQ3NDc4NDQzNzc0NzQ3MTIwNDcxMTU0MzY3NDc0Nzc4NDc0NzEyMDQzNjk0NzEyMjQ3NzU0MzExOTQ3NDcxMDM0NzQ3MTIyNDM3NTQ3NDcxMjI0NzQ3NjU0Mzg0NDM3NzQ3MTE0NDM2OTQ3NDc4NDQ3NDc3NzQzMTIwNDcxMTU0MzY3NDM3OTQ3NDcxMTk0NzQ3Njk0MzEyMjQ3NDc3NTQ3NDc1MDQzODk0NzQ3MTIyNDc3NTQzMTIwNDc0NzY1NDc0Nzg0NDM3NzQ3MTE0NDc0NzY1NDM2ODQ3Nzc0NzQ3MTIwNDMxMTU0NzQ3MTIxNDM3ODQzNTM0NzQ3MTE1NDc4MzQzNzk0NzQ3NTM0NzQ3MTE1NDM2NzQ3Nzg0NzQ3MTIwNDM2OTQ3NDcxMjI0Nzc1NDM0OTQzODk0NzQ3MTIyNDM3NTQ3NDcxMjA0NzczNDM4NDQ3Nzc0MzExNDQzOTk0NzEyMjQ3NDc3ODQzMTE0NDc0NzEwNzQ3NDc2ODQzNzg=';

  return ($l === $check ) ? 1 : 0;
}

/**
 * Weekly check
 *
 * @version 5.30.0
 * @return  void
 */
function myarcade_woechentliche_pruefung() {
  myarcade_get_license_data( myarcade_schluessel() );
}
add_action('myarcade_w', 'myarcade_woechentliche_pruefung');
?>