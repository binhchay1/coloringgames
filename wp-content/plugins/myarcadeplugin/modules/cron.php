<?php
/**
 * Real Cron Trigger - Call this if you use "real" cron job on your server
 *
 * @author Daniel Bakovic <contact@myarcadeplugin.com>
 */

$myarcade_cron_key = filter_input( INPUT_GET, 'apikey' );
$myarcade_cron_action = filter_input( INPUT_GET, 'action' );

if ( $myarcade_cron_key && $myarcade_cron_action ) {

  // Determinate the WordPress root folder
  $root = dirname( dirname( dirname( dirname( dirname(__FILE__)))));

  if ( file_exists($root . '/wp-load.php') ) {
    define('MYARCADE_DOING_ACTION', true);
    require_once($root . '/wp-load.php');
  } else {
    // WordPress not found
    die();
  }

  if ( class_exists( 'MyArcadePlugin' ) ) {
    // Verify API key
    if ( MyArcade()->get_api_key() == sanitize_key( $myarcade_cron_key ) )  {
      // What should we do?
      switch ( $myarcade_cron_action ) {
        case 'publish': {
          myarcade_cron_publishing();
        } break;

        case 'fetch': {
          myarcade_cron_fetching();
        } break;

        default:
          // Do nothing
        break;
      }
    }
  }
}
?>