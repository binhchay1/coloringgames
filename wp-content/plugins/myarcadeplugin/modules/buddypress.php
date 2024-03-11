<?php
/**
 * MyArcadePlugin BuddyPress Integration
 *
 * @author Daniel Bakovic <contact@myarcadeplugin.com>
 */

// Proceed only if BuddyPress is installed
if ( defined( 'BP_VERSION' ) ) :

	/* Add a new activity stream item for when people change their Profile Picute */

	/**
	 * Add a new activity stream item on different MyArcadePlugin actions
	 *
	 * @params array $args
	 * @return boolean
	 */
	function myarcade_new_bp_activity($args) {
		bp_activity_add( array(
		'user_id'   => $args['user_id'],
		'action'    => $args['action'],
		'content'   => $args['content'],
		'component' => 'profile',
		'type'      => $args['type']
	 ) );
	}

	function myarcade_bp_activity_new_score($score) {
		global $wpdb;

		if ( !function_exists( 'bp_activity_add' ) ) {
			return false;
		}
		if ( !bp_is_active( 'activity' ) ) {
			return false;
		}

		$submit = get_user_meta($score['user_id'], 'myarcade_bp_activity_score_submit', true);

		if ( !$submit ) {
			update_user_meta($score['user_id'], 'myarcade_bp_activity_score_submit', 'yes');
		}

		if ( $submit == 'no') {
			return;
		}

		$post_ID = $wpdb->get_var( $wpdb->prepare(
			"SELECT p.ID FROM {$wpdb->posts} AS p
				INNER JOIN {$wpdb->postmeta} AS m
				 ON m.post_id = p.ID
					WHERE m.meta_key = 'mabp_game_tag'
					 AND  m.meta_value = %s", $score['game_tag'] )
		);

		if ( ! $post_ID ) {
			// Get post id.
			$post_ID = $wpdb->get_var( $wpdb->prepare( "SELECT postid FROM {$wpdb->prefix}myarcadegames WHERE game_tag = %s", $score['game_tag'] ) );
		}

		if ( $post_ID ) {
			$post = get_post($post_ID);
			$permalink = get_permalink($post_ID);
			$game_link = '<a href="'.$permalink.'" title="">'.$post->post_title.'</a>';

			$userlink = bp_core_get_userlink( $score['user_id'] );

			$message_filter = apply_filters('myarcade_bp_new_score_message',  __( 'I just played %s and got %s points!', 'myarcadeplugin') );
			$action_filter = apply_filters('myarcade_bp_new_score_action', __( '%s submitted new score', 'myarcadeplugin'));

			$message = sprintf( $message_filter, $game_link, $score['score'] ) ;
			$action  = sprintf( $action_filter, $userlink );

			myarcade_new_bp_activity( array( 'user_id' => $score['user_id'], 'content' => $message, 'action' => $action, 'type' => 'new_score' ));
		}
	}
	add_action( 'myarcade_new_score', 'myarcade_bp_activity_new_score' );


	function myarcade_bp_activity_new_medal($medaldata) {
		global $wpdb;

		if ( !function_exists( 'bp_activity_add' ) ) {
			return false;
		}

		if ( !bp_is_active( 'activity' ) ) {
			return false;
		}

		$submit = get_user_meta($medaldata['user_id'], 'myarcade_bp_activity_medal_achieved', true);

		if ( !$submit ) {
			update_user_meta($medaldata['user_id'], 'myarcade_bp_activity_medal_achieved', 'yes');
		}

		if ( $submit == 'no') {
			return;
		}

		// Get post id.
		$post_ID = $wpdb->get_var( $wpdb->prepare( "SELECT postid FROM {$wpdb->prefix}myarcadegames WHERE game_tag = %s", $medaldata['game_tag'] ) );

		if ($post_ID) {
			$post = get_post($post_ID);
			$permalink = get_permalink($post_ID);
			$game_link = '<a href="'.$permalink.'" title="">'.$post->post_title.'</a>';

			$userlink = bp_core_get_userlink( $medaldata['user_id'] );

			$message_filter = apply_filters('myarcade_bp_new_medal_message',  __( 'I just achieved a %s on %s', 'myarcadeplugin') );
			$action_filter = apply_filters('myarcade_bp_new_medal_action', __( '%s submitted new achivement', 'myarcadeplugin'));

			$message = sprintf( $message_filter , $medaldata['name'] , $game_link );
			$action  = sprintf( $action_filter, $userlink );

			myarcade_new_bp_activity( array( 'user_id' => $medaldata['user_id'], 'content' => $message, 'action' => $action, 'type' => 'new_achivement' ));
		}
	}
	add_action( 'myarcade_new_medal', 'myarcade_bp_activity_new_medal' );

	function myarcade_bp_core_general_settings() {
		if ( !bp_is_active( 'activity' ) ) {
			return;
		}

		$user_id = bp_displayed_user_id();
		?>
		<br />
		<h3><?php _e( 'Activity Settings', 'myarcadeplugin'); ?></h3>
		<p><?php _e("Post to my Activty Stream when:", 'myarcadeplugin'); ?></p>
		<table class="notification-settings" id="groups-notification-settings">
			<thead>
					<tr>
							<th class="icon"></th>
							<th class="title"><?php _e( 'Arcade Activities', 'myarcadeplugin' ); ?></th>
							<th class="yes"><?php _e( 'Yes', 'myarcadeplugin' ); ?></th>
							<th class="no"><?php _e( 'No', 'myarcadeplugin' ); ?></th>
					</tr>
			</thead>
			<tbody>
				<tr>
						<td></td>
						<td><?php _e("I submit scores", 'myarcadeplugin'); ?></td>
						<td class="yes"><input type="radio" name="myarcade_score_submit" value="yes" <?php if (get_user_meta($user_id, 'myarcade_bp_activity_score_submit', true) == 'yes') echo ' checked="checked"'; ?>></td>
						<td class="no"><input type="radio" name="myarcade_score_submit" value="no" <?php if (get_user_meta($user_id, 'myarcade_bp_activity_score_submit', true) == 'no') echo ' checked="checked"'; ?></td>
				</tr>
				<tr>
						<td></td>
						<td><?php _e("I achieve medala", 'myarcadeplugin'); ?></td>
						<td class="yes"><input type="radio" name="myarcade_medal_achieved" value="yes" <?php if (get_user_meta($user_id, 'myarcade_bp_activity_medal_achieved', true) == 'yes') echo ' checked="checked"'; ?>></td>
						<td class="no"><input type="radio" name="myarcade_medal_achieved" value="no" <?php if (get_user_meta($user_id, 'myarcade_bp_activity_medal_achieved', true) == 'no') echo ' checked="checked"'; ?></td>
				</tr>
				<tr>
						<td></td>
						<td><?php _e("I add games to favorites", 'myarcadeplugin'); ?></td>
						<td class="yes"><input type="radio" name="myarcade_game_favorite" value="yes" <?php if (get_user_meta($user_id, 'myarcade_bp_activity_game_favorite', true) == 'yes') echo ' checked="checked"'; ?>></td>
						<td class="no"><input type="radio" name="myarcade_game_favorite" value="no" <?php if (get_user_meta($user_id, 'myarcade_bp_activity_game_favorite', true) == 'no') echo ' checked="checked"'; ?></td>
				</tr>
				<tr>
						<td></td>
						<td><?php _e("I remove games from favorites", 'myarcadeplugin'); ?></td>
						<td class="yes"><input type="radio" name="myarcade_game_favorite_remove" value="yes" <?php if (get_user_meta($user_id, 'myarcade_bp_activity_game_favorite_remove', true) == 'yes') echo ' checked="checked"'; ?>></td>
						<td class="no"><input type="radio" name="myarcade_game_favorite_remove" value="no" <?php if (get_user_meta($user_id, 'myarcade_bp_activity_game_favorite_remove', true) == 'no') echo ' checked="checked"'; ?></td>
				</tr>
			</tbody>
		</table>
		<?php
	}
	add_action('bp_notification_settings', 'myarcade_bp_core_general_settings', 99);


	function myarcade_save_bp_core_settings() {
		if ( !bp_is_active( 'activity' ) ) {
			return false;
		}

		$user_id = bp_displayed_user_id();

		if ( $user_id && isset( $_POST['notifications'] ) ) {
			update_user_meta($user_id, 'myarcade_bp_activity_score_submit' , $_POST['myarcade_score_submit']);
			update_user_meta($user_id, 'myarcade_bp_activity_medal_achieved' , $_POST['myarcade_medal_achieved']);
			update_user_meta($user_id, 'myarcade_bp_activity_game_favorite' , $_POST['myarcade_game_favorite']);
			update_user_meta($user_id, 'myarcade_bp_activity_game_favorite_remove' , $_POST['myarcade_game_favorite_remove']);
		}
	}
	add_action('bp_core_notification_settings_after_save', 'myarcade_save_bp_core_settings');
endif;
