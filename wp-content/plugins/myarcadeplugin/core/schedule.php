<?php
/**
 * Automated fetching and publishing
 *
 * @package MyArcadePlugin/Cron
 */

// No direct Access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Automated game fetching
 */
function myarcade_cron_fetching() {

	if ( myarcade_schluessel() ) {

		// Build the cron array.
		$crons = array();

		$distributors = MyArcade()->distributors();

		foreach ( $distributors as $key => $name ) {
			$option = get_option( 'myarcade_' . $key );

			if ( $option && isset( $option['cron_fetch'] ) && ( true === $option['cron_fetch'] ) ) {
				$limit         = ( ! empty( $option['cron_fetch_limit'] ) ) ? intval( $option['cron_fetch_limit'] ) : 1;
				$crons[ $key ] = array(
					'echo'     => false,
					'settings' => array(
						'limit' => $limit,
					),
				);
			}
		}

		if ( count( $crons ) > 0 ) {
			foreach ( $crons as $key => $args ) {
				$fetch_function = 'myarcade_feed_' . $key;

				// Get distributor integration file.
				MyArcade()->load_distributor( $key );

				if ( function_exists( $fetch_function ) ) {
					$fetch_function( $args );
				}
			}
		}
	}
}
add_action( 'cron_fetching', 'myarcade_cron_fetching' );

/**
 * Automated game publishing
 */
function myarcade_cron_publishing() {
	global $wpdb;

	if ( ! myarcade_schluessel() ) {
		return;
	}

	// Build the cron array.
	$crons = array();

	$distributors      = MyArcade()->distributors();
	$custom_game_types = MyArcade()->custom_game_types();

	// Game distributors.
	foreach ( $distributors as $key => $name ) {
		$option = get_option( 'myarcade_' . $key );

		if ( $option && isset( $option['cron_publish'] ) && ( true === $option['cron_publish'] ) ) {
			$limit         = ( ! empty( $option['cron_publish_limit'] ) ) ? intval( $option['cron_publish_limit'] ) : 1;
			$crons[ $key ] = $limit;
		}
	}

	$general = get_option( 'myarcade_general' );

	// Custom game types.
	if ( $general['cron_publish_limit'] > 0 ) {
		foreach ( $custom_game_types as $key => $name ) {
			$limit         = ( ! empty( $general['cron_publish_limit'] ) ) ? intval( $general['cron_publish_limit'] ) : 1;
			$crons[ $key ] = $limit;
		}
	}

	// Proceed with game publishing.
	if ( count( $crons ) > 0 ) {
		// Go trough all distributors.
		foreach ( $crons as $type => $limit ) {
			// Publish games for each distributor.
			for ( $x = 0; $x < $limit; $x++ ) {
				// Get game id.
				$game_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}myarcadegames WHERE game_type = %s AND status = 'new' ORDER BY id LIMIT 1", $type ) );

				if ( $game_id ) {
					myarcade_add_games_to_blog(
						array(
							'game_id'     => $game_id,
							'post_status' => 'publish',
							'echo'        => false,
						)
					);
				}
			}
		}
	}
}
add_action( 'cron_publishing', 'myarcade_cron_publishing' );
