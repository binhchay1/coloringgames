<?php
/**
 * Edit game form
 *
 * @author Daniel Bakovic <contact@myarcadeplugin.com>
 * @package MyArcade/Game/Edit
 */

// Locate WordPress root folder.
$root = dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) );

if ( file_exists( $root . '/wp-load.php' ) ) {
	define( 'MYARCADE_DOING_ACTION', true );
	require_once $root . '/wp-load.php';
} else {
	// WordPress not found.
	die();
}

// Check user privilege.
if ( function_exists( 'current_user_can' ) && ! current_user_can( 'manage_options' ) ) {
	die();
}

// Load required MyArcadePlugin functions.
require_once MyArcade()->plugin_path() . '/core/myarcade_admin.php';
require_once MyArcade()->plugin_path() . '/core/addgames.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title><?php esc_html_e( 'Edit Game', 'myarcadeplugin' ); ?></title>


<link rel='stylesheet' href='<?php echo admin_url( "css/wp-admin.css" ); ?>' type='text/css' />
<link rel='stylesheet' href='<?php echo admin_url( "css/colors/blue/colors.css" ); ?>' type='text/css' />
<link rel='stylesheet' href='<?php echo includes_url( "css/buttons.min.css" ); ?>' type='text/css' />
<link rel='stylesheet' href='<?php echo includes_url( "css/dashicons.min.css" ); ?>' type='text/css' />

<link rel='stylesheet' href='<?php echo MyArcade()->plugin_url(); ?>/assets/css/myarcadeplugin.css' type='text/css' />

<script type="text/javascript" src="<?php echo get_option( 'siteurl' ) . '/' . WPINC . '/js/jquery/jquery.js'; ?>"></script>

<style type="text/css">
	.wrap { margin-left: 15px; }
</style>

</head>
<body>
<div class="wrap">
<p style="margin-top: 10px"><img src="<?php echo MyArcade()->plugin_url() . '/assets/images/logo.png'; ?>" alt="MyArcadePlugin" /></p>
<?php
$general       = get_option( 'myarcade_general' );
$editgame      = filter_input( INPUT_POST, 'editgame' );
$game_id       = filter_input( INPUT_POST, 'gameid' );
$leaderenable  = filter_input( INPUT_POST, 'leaderenable' );
$highscoretype = filter_input( INPUT_POST, 'highscoretype' );
$score_bridge  = filter_input( INPUT_POST, 'score_bridge' );
$published     = filter_input( INPUT_POST, 'published' );

if ( 'edit' === $editgame ) {
	?>
	<script type="text/javascript">
	jQuery(document).ready( function() {
		jQuery("#closelink").click( function() {
			jQuery("#gstatus_<?php echo esc_attr( $game_id ); ?>", top.document).html('<div style="color:red;">updated</div>');
		});
	});

	function publish_status() {
		jQuery("#gstatus_<?php echo esc_attr( $game_id ); ?>", top.document).html('<div style="color:red;">published</div>');
	}
	</script>

	<?php
	// Update game.
	$leaderbaord  = $leaderenable ? $leaderenable : '';
	$scoreorder   = $highscoretype ? $highscoretype : 'high';
	$score_bridge = ( $score_bridge ) ? $score_bridge : '';


	if ( '1' === $published ) {
		$query = $wpdb->prepare( "UPDATE {$wpdb->prefix}'myarcadegames' SET leaderboard_enabled = %s, highscore_type = %s, WHERE id = %d", $leaderbaord, $scoreorder, $game_id );
	} else {
		$name         = esc_sql( filter_input( INPUT_POST,'gamename' ) );
		$description  = esc_sql( filter_input( INPUT_POST, 'gamedescr' ) );
		$instructions = esc_sql( filter_input( INPUT_POST, 'gameinstr' ) );
		$game_type    = filter_input( INPUT_POST, 'gametype' );
		$tags         = esc_sql( filter_input( INPUT_POST, 'gametags' ) );
		$width        = ( filter_input( INPUT_POST, 'gamewidth' ) ) ? intval( filter_input( INPUT_POST,'gamewidth' ) ) : '';
		$height       = ( filter_input( INPUT_POST, 'gameheight' ) ) ? intval( filter_input( INPUT_POST,'gameheight' ) ) : '';

		// Transform category ids to names.
		$new_categs = array();

		if ( 'post' !== MyArcade()->get_post_type() && ! empty( $general['custom_category'] ) && taxonomy_exists( $general['custom_category'] ) ) {
			foreach ( $_POST['gamecategs'] as $category_id ) {
				$term         = get_term_by( 'id', $category_id, $general['custom_category'] );
				$new_categs[] = $term->name;
			}
		} else {
			foreach ( $_POST['gamecategs'] as $category_id ) {
				$new_categs[] = get_cat_name( $category_id );
			}
		}

		$categories = implode( ',', $new_categs );

		$query = "UPDATE {$wpdb->prefix}myarcadegames SET
			name                = '$name',
			game_type           = '$game_type',
			categories          = '$categories',
			description         = '$description',
			tags                = '$tags',
			instructions        = '$instructions',
			width               = '$width',
			height              = '$height',
			leaderboard_enabled = '$leaderbaord',
			highscore_type      = '$scoreorder',
			score_bridge        = '$score_bridge'
		 WHERE id = $game_id ";
	}

	$result = $wpdb->query( $query );

	if ( $result ) {
		echo '<div class="mabp_info">' . __( 'Game has been updated!', 'myarcadeplugin' ) . '</div>';
	} else {
		echo '<div class="mabp_error">' . __( "Can't update the game!", 'myarcadeplugin' ) . '</div>';
	}
} else {
	$game_id = filter_input( INPUT_GET, 'gameid' );
}

// Get game.
$game = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}myarcadegames WHERE id =  %d LIMIT 1", $game_id ), ARRAY_A );

$disabled = '';

$publish = "<button class=\"button-secondary\" onclick = \"jQuery('#gstatus_$game_id').html('<div class=\'gload\'> </div>');jQuery.post('" . admin_url( 'admin-ajax.php' ) . "',{action:'myarcade_handler',gameid:'$game_id',func:'publish'},function(data){jQuery('#gstatus_$game_id').html(data);});jQuery('#gstatus_$game_id', top.document).html('<div style=\'color:red;\'>published</div>');self.parent.tb_remove();\">" . __( 'Publish', 'myarcadeplugin' ) . '</button>&nbsp;';
?>

<div id="myabp_import">
	<form enctype="multipart/form-data" class="niceform" method="post" name="FormEditGame">

		<input class="button-secondary" id="submit" type="submit" name="submit" value="<?php _e( 'Save Changes', 'myarcadeplugin' ); ?>" />
		<?php echo $publish; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
		<div style="float:right">
			<button class="button-secondary" id="closelink" onclick="self.parent.tb_remove();return false;">Close</button>
		</div>

		<input type="hidden" name="gameid" value="<?php echo esc_attr( $game_id ); ?>" />
		<input type="hidden" name="editgame" value="edit" />
		<input type="hidden" name="published" value="<?php if ( 'published' === $game['status'] ) { echo '1'; } else { echo '0'; } ?>" />

		<h2><?php _e( 'Edit Game', 'myarcadeplugin' ); ?></h2>

		<div class="container">
			<div class="block">
				<table class="optiontable" width="100%">
					<?php if ( 'published' === $game['status'] ) : ?>
					<?php $disabled = ' disabled'; ?>
					<tr>
						<td colspan="2"><div class="myerror fade"><?php _e( 'You are about to edit a published game. Thereby, will only be able to change the score settings.', 'myarcadeplugin' ); ?></div></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td><h3><?php _e( 'Name', 'myarcadeplugin' ); ?> <small>(<?php _e( 'required', 'myarcadeplugin' ); ?>)</small></h3></td>
					</tr>
					<tr>
						<td>
							<input name="gamename" size="50" type="text" value="<?php echo stripslashes( $game['name'] ); ?>" <?php echo esc_attr( $disabled ); ?>/>
						</td>
					</tr>
				</table>
			</div>
		</div>

	<div class="container">
		<div class="block">
			<table class="optiontable" width="100%">
				<tr>
					<td colspan="2"><h3><?php _e( 'Game Dimensions', 'myarcadeplugin' ); ?></h3></td>
				</tr>
				<tr>
					<td>
						<?php _e( 'Game width (px)', 'myarcadeplugin' ); ?>: <input id="gamewidth" name="gamewidth" type="text" size="20" value="<?php echo esc_attr( $game['width'] ); ?>" />
					</td>
					<td>
						<?php _e( 'Game height (px)', 'myarcadeplugin' ); ?>: <input id="gameheight" name="gameheight" type="text" size="20" value="<?php echo esc_attr( $game['height'] ); ?>" />
					</td>
				</tr>
			</table>
		</div>
	</div>

		<div class="container">
			<div class="block">
				<table class="optiontable" width="100%">
					<tr>
						<td><h3><?php _e( 'Game Description', 'myarcadeplugin' ); ?> <small>(<?php _e( 'required', 'myarcadeplugin' ); ?>)</small></h3></td>
					</tr>
					<tr>
						<td>
							<textarea rows="6" cols="80" name="gamedescr" <?php echo esc_attr( $disabled ); ?>><?php echo esc_html( stripslashes( $game['description'] ) ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="container">
			<div class="block">
				<table class="optiontable" width="100%">
					<tr>
						<td><h3><?php _e( 'Game Instructions', 'myarcadeplugin' ); ?></h3></td>
					</tr>
					<tr>
						<td>
							<textarea rows="6" cols="80" name="gameinstr" <?php echo esc_attr( $disabled ); ?>><?php echo esc_html( stripslashes( $game['instructions'] ) ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="container">
			<div class="block">
				<table class="optiontable" width="100%">
					<tr>
						<td><h3><?php _e( 'Tags', 'myarcadeplugin' ); ?></h3></td>
					</tr>
					<tr>
						<td>
							<input name="gametags" type="text" size="50" value="<?php echo esc_attr( $game['tags'] ); ?>" <?php echo esc_attr( $disabled ); ?>/>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="container">
			<div class="block">
				<table class="optiontable" width="100%">
					<tr>
						<td><h3><?php _e( 'Category', 'myarcadeplugin' ); ?> <small>(<?php _e( 'required', 'myarcadeplugin' ); ?>)</small></h3></td>
					</tr>
					<tr>
						<td>
						<?php
						$game_categories = explode( ',', $game['categories'] );
						$categs          = array();

						if ( 'post' !== MyArcade()->get_post_type() && ! empty( $general['custom_category'] ) && taxonomy_exists( $general['custom_category'] ) ) {

							$custom_terms = get_terms( $general['custom_category'], array( 'hide_empty' => 0 ) );

							// Build the category array.
							foreach ( $custom_terms as $custom_term ) {
								$categs[ $custom_term->term_id ] = $custom_term->name;
							}
						} else {
							// Get all categories.
							$all_categories = get_terms(
								'category',
								array(
									'fields' => 'ids',
									'get'    => 'all',
								)
							);

							foreach ( $all_categories as $all_cat_id ) {
								$categs[ $all_cat_id ] = get_cat_name( $all_cat_id );
							}
						}

						$i = count( $categs );

						foreach ( $categs as $category_id => $cat_name ) {
							foreach ( $game_categories as $game_cat ) {
								if ( $game_cat === $cat_name ) {
									$checked = 'checked';
									break;
								} else {
									$checked = '';
								}
							}

							$i--;
							$br = '';
							if ( $i > 0 ) {
								$br = '<br />';
							}

							echo '<input type="checkbox" name="gamecategs[]" value="' . esc_attr( $category_id ) . '" ' . esc_attr( $checked ) . ' ' . esc_attr( $disabled ) . '/><label class="opt">&nbsp;' . esc_html( $cat_name ) . '</label>' . $br;
						}
						?>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="container">
			<div class="block">
				<table class="optiontable" width="100%">
					<tr>
						<td><h3><?php _e( 'Highscore Settings', 'myarcadeplugin' ); ?></h3></td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" name="leaderenable" value="1" <?php if ( '1' === $game['leaderboard_enabled'] ) { echo 'checked'; } ?> /><label class="opt">&nbsp;<?php _e( 'Yes - This game is able to submit scores', 'myarcadeplugin' ); ?></label>
						</td>
					</tr>
					<tr>
						<td>
							<p>
							<?php _e( 'Score Order (Highscore Type)', 'myarcadeplugin' ); ?>
							<select size="1" name="highscoretype" id="highscoretype">
								<option value="high" <?php if ( 'high' === $game['highscore_type'] ) { echo 'selected'; } ?>><?php _e( 'DESC (High to Low)', 'myarcadeplugin' ); ?></option>
								<option value="low" <?php if ( 'low' === $game['highscore_type'] ) { echo 'selected'; } ?>><?php _e( 'ASC (Low to High)', 'myarcadeplugin' ); ?></option>
							</select>
							</p>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="container">
			<div class="block">
				<table class="optiontable" width="100%">
					<tr>
						<td><h3><?php _e( 'Game Type', 'myarcadeplugin' ); ?></h3></td>
					</tr>
					<tr>
						<td>
							<select size="1" name="gametype" id="gametype">
								<?php
								$distributors      = MyArcade()->distributors();
								$custom_game_types = MyArcade()->custom_game_types();
								$game_types        = array_merge( $distributors, $custom_game_types );

								foreach ( $game_types as $slug => $name ) :
									?>
									<option value="<?php echo esc_attr( $slug ); ?>" <?php myarcade_selected( $game['game_type'], $slug ); ?>><?php echo esc_html( $name ); ?></option>
									<?php
								endforeach;
								?>
							</select>
							<br />
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="container">
			<div class="block">
				<input class="button-secondary" id="submit" type="submit" name="submit" value="<?php _e( 'Save Changes', 'myarcadeplugin' ); ?>" />
				<?php echo $publish; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
				<div style="float:right">
					<button class="button-secondary" id="closelink" onclick="self.parent.tb_remove();return false;"><?php _e( 'Close', 'myarcadetheme' ); ?></button>
				</div>
			</div>
		</div>
	</form>
</div>
</div>
</body>
</html>
