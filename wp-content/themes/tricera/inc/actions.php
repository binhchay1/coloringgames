<?php
/**
 * FunGames action functions
 * 
 * @author Daniel Bakovic
 * @uri http://myarcadeplugin.com   
 * 
 * @package WordPress
 * @subpackage FunGames
 */

function tricera_init_actions() {
  /** Add WPSeoContentManager Compatibility **/
  if ( function_exists('get_WPSEOContent') ) {
    add_action('tricera_after_404_content', 'get_WPSEOContent');
    add_action('tricera_after_archive_content', 'get_WPSEOContent');
    add_action('tricera_after_index_content', 'get_WPSEOContent');
  }
  
  // Only if contest is activated
  if ( function_exists('myarcadecontest_init') ) {
    add_action('tricera_before_game', 'tricera_contest_alert');
  }
}
add_action('init', 'tricera_init_actions');

function tricera_action_after_404_content() {
  do_action('tricera_after_404_content');
}

function tricera_action_after_archive_content() {
  do_action('tricera_after_archive_content');
}

function tricera_action_after_index_content() {
  do_action('tricera_after_index_content');
}
?>