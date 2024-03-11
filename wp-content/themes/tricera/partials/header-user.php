<?php
/**
 * User Panel
 */

// Show Buddypress links if user is logged in
if ( defined('BP_VERSION') ) {
	global $bp;

	$logoutlink = wp_nonce_url(site_url("/wp-login.php?action=logout&redirect_to=".get_option('siteurl'), 'login'),'log-out');
	?>

	<div class="loginbox">
		<?php if (bp_is_active('messages')) { ?>
			<a class="msg_btn tooltip" title="<span>Messages</span>" href="<?php echo $bp->loggedin_user->domain.BP_MESSAGES_SLUG.'/'; ?>">Messages</a>
		<?php } ?>

		<a class="profile_btn tooltip" title="<span>Profile</span>" href="<?php echo $bp->loggedin_user->domain ?>"><?php _e('Edit Profile', 'tricera');?></a>

		<?php if(bp_is_active('activity')) { ?>
			<a class="activity_btn tooltip" title="<span>Activity</span>" href="<?php echo $bp->loggedin_user->domain . BP_ACTIVITY_SLUG . '/'; ?>"><?php _e('Activity', 'tricera'); ?></a>
		<?php } ?>

		<?php if ( tricera_wp_fav_post() ) : ?>
		<a class="topfav_btn tooltip" title="<span>Favorite Games</span>" href="<?php echo get_the_permalink( tricera_get_option( 'favorite_game_page') ); ?>"><?php _e('Favorites', 'tricera' ); ?></a>
		<?php endif; ?>

		<a class="logout_btn tooltip" title="<span>Logout</span>" href="<?php echo apply_filters('loginout', $logoutlink); ?>"><?php _e('Logout', 'tricera' ); ?></a>
	</div>

	<?php $logoutlink = wp_nonce_url(site_url("/wp-login.php?action=logout&redirect_to=".get_option('siteurl'), 'login'),'log-out'); ?>

  <div class="avatarbox">
    <a href="<?php echo $bp->loggedin_user->domain."profile/change-avatar/"; ?>"><?php

	  	// Populate user vars
    	echo get_avatar( get_current_user_id(), '49' );
  	  ?>
	  </a>
  </div>
	<?php
}
else {
	// User logged in but no buddypress installed
	tricera_default_top_bar();
}
