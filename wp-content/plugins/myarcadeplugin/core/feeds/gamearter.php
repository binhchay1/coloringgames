<?php
/**
 * GameArter - https://www.gamearter.com/export/v1/games
 *
 * @author Daniel Bakovic <contact@myarcadeplugin.com>
 */

/**
 * Save options
 *
 * @return void
 */
function myarcade_save_settings_gamearter() {

  myarcade_check_settings_nonce();

  $settings                       = array();
  $settings['feed']               = esc_sql( filter_input( INPUT_POST, 'gamearter_url' ) );
  $settings['cron_publish']       = filter_input( INPUT_POST, 'gamearter_cron_publish', FILTER_VALIDATE_BOOLEAN );
  $settings['cron_publish_limit'] = filter_input( INPUT_POST, 'gamearter_cron_publish_limit', FILTER_VALIDATE_INT, array( "options" => array( "default" => 1) ) );

  // Update settings.
  update_option( 'myarcade_gamearter', $settings );
}

/**
 * Display distributor settings on admin page.
 *
 * @return void
 */
function myarcade_settings_gamearter() {
  $gamearter = MyArcade()->get_settings( 'gamearter' );
  ?>
  <h2 class="trigger"><?php _e( "GameArter", 'myarcadeplugin'); ?></h2>
  <div class="toggle_container">
    <div class="block">
      <table class="optiontable" width="100%" cellpadding="5" cellspacing="5">
        <tr>
          <td colspan="2">
            <i>
            <?php printf( __( '%s distributes WEBGL and HTML5 games.', 'myarcadeplugin' ), '<a href="https://www.gamearter.com/games" target="_blank">GameArter</a>' ); ?>
            </i>
            <br /><br />
          </td>
        </tr>
        <tr><td colspan="2"><h3><?php _e("Feed URL", 'myarcadeplugin'); ?></h3></td></tr>
        <tr>
          <td>
            <input type="text" size="40"  name="gamearter_url" value="<?php echo esc_url( $gamearter['feed'] ); ?>" />
          </td>
          <td><i><?php _e("Edit this field only if Feed URL has been changed!", 'myarcadeplugin'); ?></i></td>
        </tr>

        <tr><td colspan="2"><h3><?php _e("Automated Game Publishing", 'myarcadeplugin'); ?></h3></td></tr>

        <tr>
          <td>
            <input type="checkbox" name="gamearter_cron_publish" value="true" <?php myarcade_checked($gamearter['cron_publish'], true); ?> /><label class="opt">&nbsp;<?php _e("Yes", 'myarcadeplugin'); ?></label>
          </td>
          <td><i><?php _e("Enable this if you want to publish games automatically. Go to 'General Settings' to select a cron interval.", 'myarcadeplugin'); ?></i></td>
        </tr>

        <tr><td colspan="2"><h4><?php _e("Publish Games", 'myarcadeplugin'); ?></h4></td></tr>

        <tr>
          <td>
            <input type="text" size="40" name="gamearter_cron_publish_limit" value="<?php echo esc_attr( $gamearter['cron_publish_limit'] ); ?>" />
          </td>
          <td><i><?php _e("How many games should be published on every cron trigger?", 'myarcadeplugin'); ?></i></td>
        </tr>

      </table>
      <input class="button button-primary" id="submit" type="submit" name="submit" value="<?php _e("Save Settings", 'myarcadeplugin'); ?>" />
    </div>
  </div>
  <?php
}

/**
 * Load default distributor settings
 *
 * @return  array Default settings
 */
function myarcade_default_settings_gamearter() {
  return array(
    'feed'                => 'https://www.gamearter.com/export/v1/games',
    'cron_publish'        => false,
    'cron_publish_limit'  => '1',
  );
}

/**
 * Retrieve available distributor's categories mapped to MyArcadePlugin categories
 *
 * @return  array Distributor categories
 */
function myarcade_get_categories_gamearter() {
  return array(
    "Action"      => true,
    "Adventure"   => false,
    "Arcade"      => false,
    "Board Game"  => false,
    "Casino"      => false,
    "Defense"     => false,
    "Customize"   => 'girls',
    "Dress-Up"    => false,
    "Driving"     => true,
    "Education"   => false,
    "Fighting"    => false,
    "Jigsaw"      => false,
    "Multiplayer" => 'mmo,multiplayer',
    "Other"       => '3D',
    "Puzzles"     => false,
    "Rhythm"      => false,
    "Shooting"    => false,
    "Sports"      => true,
    "Strategy"    => 'logic,strategy',
  );
}

/**
 * Fetch games
 *
 * @param   array  $args Fetching parameters
 * @return  void
 */
function myarcade_feed_gamearter( $args = array() ) {

  $defaults = array(
    'echo'     => false,
    'settings' => array(),
  );

  $args = wp_parse_args( $args, $defaults );
  extract($args);

  $new_games = 0;
  $add_game  = false;

  $gamearter            = MyArcade()->get_settings( 'gamearter' );
  $gamearter_categories = myarcade_get_categories_gamearter();
  $feedcategories       = MyArcade()->get_settings( 'categories' );

  // Init settings var's
  if ( ! empty($settings) ) {
    $settings = array_merge( $gamearter, $settings );
  }
  else {
    $settings = $gamearter;
  }

  // Include required fetch functions
  require_once( MYARCADE_CORE_DIR . '/fetch.php' );

  // Fetch games
  $json_games = myarcade_fetch_games( array( 'url' => trim( $settings['feed'] ), 'service' => 'json', 'echo' => $echo ) );

  //====================================
  if ( ! empty( $json_games ) ) {
    foreach ( $json_games as $game_obj ) {

      $game = new stdClass();
      $game->uuid     = crc32( $game_obj->name ) . '_gamearter';
      // Generate a game tag for this game
      $game->game_tag = md5( $game_obj->name . 'gamearter' );

      $add_game   = false;

      // Map categories
      if ( ! empty( $game_obj->category ) ) {
        $categories = explode( ',', $game_obj->category );
        $categories = array_map( 'trim', $categories );
      }
      else {
        $categories = array( 'Other' );
      }

      // Initialize the category string
      $categories_string = 'Other';

      foreach( $categories as $gamecat ) {
        $gamecat = htmlspecialchars_decode( $gamecat );

        foreach ( $feedcategories as $feedcat ) {
          if ( $feedcat['Status'] == 'checked' ) {
            if ( $gamearter_categories[ $feedcat['Name'] ] ) {
              // Set category name to check
              if ( $gamearter_categories[ $feedcat['Name'] ] === true ) {
                $cat_name = $feedcat['Name'];
              }
              else {
                $cat_name = $gamearter_categories[ $feedcat['Name'] ];
              }

              // mb_stripos - case insensitive
              if ( mb_stripos( $cat_name, $gamecat ) !== false ) {
                $add_game = true;
                $categories_string = $feedcat['Name'];
                break 2;
              }
            }
          }
        }
      } // END - Category-Check


      if ( ! $add_game ) {
        continue;
      }

      // Some GameArter games don't provide Thumbnails... skip those games
      if ( empty( $game_obj->thumbnail ) ) {
        continue;
      }

      $game->type           = "gamearter";
      $game->name           = esc_sql( $game_obj->name );
      $game->slug           = myarcade_make_slug( $game_obj->name );
      $game->description    = esc_sql( $game_obj->description );
      $game->instructions   = esc_sql( $game_obj->controls );
      $game->categs         = $categories_string;
      $game->swf_url        = esc_url( $game_obj->url );
      $game->thumbnail_url  = esc_sql( $game_obj->thumbnail );
      $game->width          = '100%';
      $game->height         = '100%';

      if ( ! empty( $game_obj->video ) ) {
        $game->video_url = esc_url( $game_obj->video );
      }

      if ( ! empty( $game_obj->image ) ) {
        $game->screen1_url = esc_url( $game_obj->image );
      }

      // Add game to the database
      if ( myarcade_add_fetched_game( $game, $args ) ) {
        $new_games++;
      }
    }
  }

  // Show, how many games have been fetched
  myarcade_fetched_message( $new_games, $echo );
}

/**
 * Return game embed method
 *
 * @return  string Embed Method
 */
function myarcade_embedtype_gamearter() {
  return 'iframe';
}

/**
 * Return if games can be downloaded by this distirbutor
 *
 * @return  bool True if games can be downloaded
 */
function myarcade_can_download_gamearter() {
  return false;
}
