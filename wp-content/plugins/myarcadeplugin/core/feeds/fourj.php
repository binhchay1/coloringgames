<?php
/**
 * 4J - http://w.4j.com
 *
 * @author Daniel Bakovic <contact@myarcadeplugin.com>
 */

/**
 * Save options
 *
 * @return  void
 */
function myarcade_save_settings_fourj() {

  myarcade_check_settings_nonce();

  $settings = array();
  $settings['feed'] = esc_sql( filter_input( INPUT_POST, 'fourj_url' ) );
  $settings['advertisements'] = filter_input( INPUT_POST, 'fourj_advertisements', FILTER_VALIDATE_BOOLEAN );
  $settings['copyright'] = filter_input( INPUT_POST, 'fourj_copyright', FILTER_VALIDATE_BOOLEAN );

  $settings['cron_fetch'] = filter_input( INPUT_POST, 'fourj_cron_fetch', FILTER_VALIDATE_BOOLEAN );
  $settings['cron_fetch_limit'] = filter_input( INPUT_POST, 'fourj_cron_fetch_limit', FILTER_VALIDATE_INT, array( "options" => array( "default" => 1) ) );

  $settings['cron_publish'] = filter_input( INPUT_POST, 'fourj_cron_publish', FILTER_VALIDATE_BOOLEAN );
  $settings['cron_publish_limit'] = filter_input( INPUT_POST, 'fourj_cron_publish_limit', FILTER_VALIDATE_INT, array( "options" => array( "default" => 1) ) );

  // Update settings
  update_option( 'myarcade_fourj', $settings );
}

/**
 * Display distributor settings on admin page
 *
 * @return  void
 */
function myarcade_settings_fourj() {
  $fourj = MyArcade()->get_settings( 'fourj' );
  ?>
  <h2 class="trigger"><?php _e( "4J", 'myarcadeplugin'); ?></h2>
  <div class="toggle_container">
    <div class="block">
      <table class="optiontable" width="100%" cellpadding="5" cellspacing="5">
        <tr>
          <td colspan="2">
            <i>
               <?php printf( __( "%s distributes Flash, WebGL, Unity3D and HTML5 games.", 'myarcadeplugin' ), '<a href="http://w.4j.com/" target="_blank">4J</a>' ); ?>
            </i>
            <br /><br />
          </td>
        </tr>
        <tr><td colspan="2"><h3><?php _e("Feed URL", 'myarcadeplugin'); ?></h3></td></tr>
        <tr>
          <td>
            <input type="text" size="40"  name="fourj_url" value="<?php echo esc_url( $fourj['feed'] ); ?>" />
          </td>
          <td><i><?php _e("Edit this field only if Feed URL has been changed!", 'myarcadeplugin'); ?></i></td>
        </tr>

        <tr><td colspan="2"><h3><?php _e("Without Advertisements", 'myarcadeplugin'); ?></h3></td></tr>
        <tr>
          <td>
            <input type="checkbox" name="fourj_advertisements" value="true" <?php myarcade_checked( $fourj['advertisements'], true); ?> /><label class="opt">&nbsp;<?php _e("Yes", 'myarcadeplugin'); ?></label>
          </td>
          <td><i><?php _e("Enable this if you want to fetch only games without advertisements.", 'myarcadeplugin'); ?></i></td>
        </tr>

        <tr><td colspan="2"><h3><?php _e("Contain Copyrighted Content", 'myarcadeplugin'); ?></h3></td></tr>
        <tr>
          <td>
            <input type="checkbox" name="fourj_copyright" value="true" <?php myarcade_checked( $fourj['copyright'], true); ?> /><label class="opt">&nbsp;<?php _e("Yes", 'myarcadeplugin'); ?></label>
          </td>
          <td><i><?php _e("Enable this if you want to fetch games that also contain copyrighted content.", 'myarcadeplugin'); ?></i></td>
        </tr>

        <tr><td colspan="2"><h3><?php _e("Automated Game Fetching", 'myarcadeplugin'); ?></h3></td></tr>
        <tr>
          <td>
            <input type="checkbox" name="fourj_cron_fetch" value="true" <?php myarcade_checked( $fourj['cron_fetch'], true); ?> /><label class="opt">&nbsp;<?php _e("Yes", 'myarcadeplugin'); ?></label>
          </td>
          <td><i><?php _e("Enable this if you want to fetch games automatically. Go to 'General Settings' to select a cron interval.", 'myarcadeplugin'); ?></i></td>
        </tr>

        <tr><td colspan="2"><h4><?php _e("Fetch Games", 'myarcadeplugin'); ?></h4></td></tr>

        <tr>
          <td>
            <input type="text" size="40"  name="fourj_cron_fetch_limit" value="<?php echo esc_attr( $fourj['cron_fetch_limit'] ); ?>" />
          </td>
          <td><i><?php _e("How many games should be fetched on every cron trigger?", 'myarcadeplugin'); ?></i></td>
        </tr>

        <tr><td colspan="2"><h3><?php _e("Automated Game Publishing", 'myarcadeplugin'); ?></h3></td></tr>

        <tr>
          <td>
            <input type="checkbox" name="fourj_cron_publish" value="true" <?php myarcade_checked($fourj['cron_publish'], true); ?> /><label class="opt">&nbsp;<?php _e("Yes", 'myarcadeplugin'); ?></label>
          </td>
          <td><i><?php _e("Enable this if you want to publish games automatically. Go to 'General Settings' to select a cron interval.", 'myarcadeplugin'); ?></i></td>
        </tr>

        <tr><td colspan="2"><h4><?php _e("Publish Games", 'myarcadeplugin'); ?></h4></td></tr>

        <tr>
          <td>
            <input type="text" size="40" name="fourj_cron_publish_limit" value="<?php echo esc_attr( $fourj['cron_publish_limit'] ); ?>" />
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
function myarcade_default_settings_fourj() {
  return array(
    'feed'                => 'http://w.4j.com/games.php',
    'limit'               => '100',
    'advertisements'      => true,
    'copyright'           => false,
    'cron_fetch'          => false,
    'cron_fetch_limit'    => '1',
    'cron_publish'        => false,
    'cron_publish_limit'  => '1',
  );
}

/**
 * Generate an options array with submitted fetching parameters
 *
 * @return  array Fetching options
 */
function myarcade_get_fetch_options_fourj() {

  // Get distributor settings
  $settings = MyArcade()->get_settings( 'fourj' );
  $defaults = myarcade_default_settings_fourj();
  $settings = wp_parse_args( $settings, $defaults );

  $settings['method'] = 'latest';
  $settings['offset'] = 1;

  if ( 'start' == filter_input( INPUT_POST, 'fetch' ) ) {
    $settings['limit']   = filter_input( INPUT_POST, 'limitfourj', FILTER_VALIDATE_INT, array( "options" => array( "default" => 100 ) ) );
    $settings['method']  = filter_input( INPUT_POST, 'fetchmethodfourj', FILTER_UNSAFE_RAW, array( "options" => array( "default" => 'latest') ) );
    $settings['offset']  = filter_input( INPUT_POST, 'offsetfourj', FILTER_UNSAFE_RAW, array( "options" => array( "default" => '1') ) );
  }

  return $settings;
}

/**
 * Display distributor fetch games options
 *
 * @return  void
 */
function myarcade_fetch_settings_fourj() {

  $fourj = myarcade_get_fetch_options_fourj();
  ?>

  <div class="myarcade_border white hide mabp_680" id="fourj">
    <div style="float:left;width:150px;">
      <input type="radio" name="fetchmethodfourj" value="latest" <?php myarcade_checked($fourj['method'], 'latest');?>>
    <label><?php _e("Latest Games", 'myarcadeplugin'); ?></label>
    <br />
    <input type="radio" name="fetchmethodfourj" value="offset" <?php myarcade_checked($fourj['method'], 'offset');?>>
    <label><?php _e("Use Offset", 'myarcadeplugin'); ?></label>
    </div>
    <div class="myarcade_border" style="float:left;padding-top: 5px;background-color: #F9F9F9">
      <?php printf( esc_html__( 'Fetch %s games %sfrom page %s', 'myarcadeplugin' ), '<input type="number"  min="50" name="limitfourj" value="' . esc_attr( $fourj['limit'] ) . '" />', '<span id="offsfourj" class="hide">', '<input id="radiooffsfourj" type="number" name="offsetfourj" value="' . esc_attr( $fourj['offset'] ) . '" /> </span>' ); ?>
    </div>
    <div class="clear"></div>
  </div>
  <?php
}

/**
 * Retrieve available distributor's categories mapped to MyArcadePlugin categories
 *
 * @return  array Distributor categories
 */
function myarcade_get_categories_fourj() {
  return array(
    "Action"      => true,
    "Adventure"   => true,
    "Arcade"      => true,
    "Board Game"  => false,
    "Casino"      => true,
    "Defense"     => true,
    "Customize"   => false,
    "Dress-Up"    => "girl",
    "Driving"     => true,
    "Education"   => false,
    "Fighting"    => true,
    "Jigsaw"      => true,
    "Multiplayer" => true,
    "Other"       => "3D,cooking,other",
    "Puzzles"     => "puzzle",
    "Rhythm"      => "music,rhythm",
    "Shooting"    => true,
    "Sports"      => true,
    "Strategy"    => "escape,strategy,platform,physics",
  );
}

/**
 * Fetch games
 *
 * @param   array  $args Fetching parameters
 * @return  void
 */
function myarcade_feed_fourj( $args = array() ) {

  $defaults = array(
    'echo'     => false,
    'settings' => array(),
  );

  $args = wp_parse_args( $args, $defaults );
  extract($args);

  $new_games = 0;
  $add_game = false;

  $fourj = myarcade_get_fetch_options_fourj();
  $fourj_categories = myarcade_get_categories_fourj();
  $feedcategories = MyArcade()->get_settings( 'categories' );
  $general = MyArcade()->get_settings( 'general' );

  // Init settings var's
  if ( ! empty($settings) ) {
    $settings = array_merge( $fourj, $settings );
  }
  else {
    $settings = $fourj;
  }

  if ( ! isset($settings['method']) ) {
    $settings['method'] = 'latest';
  }

  $feed = add_query_arg( array( "format" => "0" ), trim( $settings['feed'] ) );

  if ( isset( $general['types'] ) && 'mobile' == $general['types'] ) {
    $feed = add_query_arg( array( "platform" => "0" ), $feed );
  }

  // Check if there is a feed limit. If not, feed all games
  if ( ! empty( $settings['limit'] ) ) {
    $feed = add_query_arg( array( "num" => $settings['limit'] ), $feed );
  }

  if ( $settings['method'] == 'offset' && isset( $settings['offset'] ) ) {
    $feed = add_query_arg( array("page" => $settings['offset'] ), $feed );
  }
  else {
    $feed = add_query_arg( array("page" => 1 ), $feed );
  }

  if ( $settings['advertisements'] ) {
    $feed = add_query_arg( array("ad" => 0 ), $feed );
  }

  if ( ! $settings['copyright'] ) {
    $feed = add_query_arg( array("copyright" => 0 ), $feed );
  }

  // Include required fetch functions
  require_once( MYARCADE_CORE_DIR . '/fetch.php' );

  // Fetch games
  $json_games = myarcade_fetch_games( array( 'url' => trim( $feed ), 'service' => 'json', 'echo' => $echo ) );

  //====================================
  if ( !empty($json_games ) ) {
    foreach ( $json_games as $game_obj ) {

      $game = new stdClass();
      $game->uuid     = crc32( $game_obj->name ) . '_fourj';
      // Generate a game tag for this game
      $game->game_tag = md5( $game_obj->name . 'fourj' );

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
            if ( ! empty( $fourj_categories[ $feedcat['Name'] ] ) ) {
              // Set category name to check
              if ( $fourj_categories[ $feedcat['Name'] ] === true ) {
                $cat_name = $feedcat['Name'];
              }
              else {
                $cat_name = $fourj_categories[ $feedcat['Name'] ];
              }
            }

            // mb_stripos - case insensitive
            if ( mb_stripos( $cat_name, $gamecat ) !== false ) {
              $add_game = true;
              $categories_string = $feedcat['Name'];
              break 2;
            }
          }
        }
      } // END - Category-Check


      if ( ! $add_game ) {
        continue;
      }

      $extension = pathinfo( $game_obj->file , PATHINFO_EXTENSION );

      switch ( $extension ) {
        case 'swf': {
          $game->type = "custom";
        } break;

        case 'dcr': {
          $game->type = "dcr";
        } break;

        case 'unity3d': {
          $game->type = 'unity';
        } break;

        default: {
          $game->type = "iframe";
        } break;
      }

      $game->name           = esc_sql( $game_obj->name );
      $game->slug           = myarcade_make_slug( $game_obj->name );
      $game->description    = esc_sql( $game_obj->description );
      $game->instructions   = esc_sql( $game_obj->control );
      $game->categs         = $categories_string;
      $game->tags           = esc_sql( $game_obj->tags );
      $game->swf_url        = esc_sql( $game_obj->file );
      $game->thumbnail_url  = esc_sql( $game_obj->thumb );

      if ( ! empty( $game_obj->m_width ) && ! empty( $game_obj->m_height ) ) {
        $game->width  = intval( $game_obj->m_width );
        $game->height = intval( $game_obj->m_height );
      }
      else {
        $game->width  = intval( $game_obj->width );
        $game->height = intval( $game_obj->height );
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