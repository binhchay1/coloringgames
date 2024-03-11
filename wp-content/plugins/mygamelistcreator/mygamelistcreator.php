<?php
/*
Plugin Name:  MyGameListCreator
Plugin URI:   http://myarcadeplugin.com
Description:  Creates a static file including a list with defined number of games to save server performance. The list will only be refreshed if a game is added/modified/deleted.
Version:      2.3.1
Author:       Daniel Bakovic
Author URI:   htpt://myarcadeplugin.com
*/

/*
  [mygamelist]
  %TITLE%
  %TITLE_WITH_LINK%
  %THUMBNAIL%
  %THUMBNAIL_WITH_LINK%
  %DESCRIPTION%
  %INSTRUCTION%
*/


//----------------------------------------------------------------------------//
//---Config-------------------------------------------------------------------//
//----------------------------------------------------------------------------//
$mygamelistcreator_version = '2.3.1';


//----------------------------------------------------------------------------//
//---Hook---------------------------------------------------------------------//
//----------------------------------------------------------------------------//
add_action('publish_post', 'create_lists');
add_action('deleted_post', 'create_lists');
add_action('edit_post',    'create_lists');

register_activation_hook  ( __FILE__, 'mygame_list_install' );
register_deactivation_hook( __FILE__, 'mygame_list_uninstall' );

add_action('admin_menu', 'mygame_list_admin_menu');

add_shortcode('mygamelist', 'mygamelist_process');


function mygame_list_uninstall() {
  delete_option('mygamelistcreator');
}

//
// @brief: Adds some needed option values to wordpress db
//
function mygame_list_install() {

  $gamelist_settings = array (
    'list_limit'           => 200,
    'list_title'           => '<h1>200 Latest Games</h1>',
    'list_begin_wrap'      => '<div id="gamelist">',
    'list_end_wrap'        => '</div>',
    'list_item_begin_wrap' => '<li>',
    'list_item_end_wrap'   => '</li>',
    'list_char_limit'      => 22,
    'list_include_cats'    => '',
    'list_list_begin_wrap' => '<ul>',
    'list_list_end_wrap'   => '</ul>',
    'list_leading'         => 'No',
    'autocreate_list'      => 'Yes',
    'list_rows'            => 5);

  register_setting('mygamelistcreator', 'mygamelistcreator');
  $options = get_option('mygamelistcreator');

  if ( empty($options) ) {
    update_option( 'mygamelistcreator', $gamelist_settings);
  }
}

//
// @brief: Creates an admin link menu on settings section
//
function mygame_list_admin_menu() {
  if ( defined('MYARCADE_VERSION') ) {
    add_submenu_page('myarcade_admin.php',
      __('MyGameListCreator', 'mygamelistcreator') ,
      __('MyGameListCreator', 'mygamelistcreator'),
      'manage_options',  basename(__FILE__), 'mygamelistcreator_options');
  }
  else {
    add_options_page(
      __('MyGameListCreator', 'mygamelistcreator') ,
      __('MyGameListCreator', 'mygamelistcreator'),
      'manage_options' ,
      basename(__FILE__),
      'mygamelistcreator_options');
  }
}


//
// @brief: Header for the overview page (admin menu)
//
function mygamelistcreator_header() {

  ?>
  <script type="text/javascript">
  function checkAll(field) {
    for (i = 0; i < field.length; i++)
      field[i].checked = true ;
  }

  function selectall() {
    var theForm = document.dbgamelist;
    for (i=0; i<theForm.elements.length; i++) {
      if (theForm.elements[i].name=='gamecategs[]')
        theForm.elements[i].checked = 1;
    }
  }
  </script>
  <?php

  echo '<div class="wrap">';
  echo '<h2>MyGameListCreator Options</h2>';
}


//
// @brief: Footer for the overview page (admin menu)
//
function mygamelistcreator_footer() {
  global $mygamelistcreator_version;

  ?>
  <div style="margin: 20px 0px 20px 0px;padding: 10px;text-align: right;">
    <p>
      MyGameListCreator v<?php echo $mygamelistcreator_version;?> |
      <a href="http://myarcadeplugin.com" title="MyArcadePlugin" target="_blank">MyArcadePlugin</a>
    </p>
    </div>
  </div>
  <?php
}


//
// @brief: Creates an overview/settings page in admin backend
//
function mygamelistcreator_options() {

  mygamelistcreator_header();

  if (isset($_GET['dbcgl_action'])) {

    switch ($_GET['dbcgl_action']) {
      case 'dbcglmanually':
      {
        $result = create_mygame_list();
      }
      break;
    }


    if ($result) {
      echo '<div id="message" class="updated fade"><p><strong>New game list created!</strong></p></div>';
    } else {
      echo '<p id="message" class="error fade">Can\'t create a game list!</p>';
    }
  }

  if (isset($_POST['dbcgl_update'])) {

    if ( empty( $_POST['gamecategs'] ) ) {
      $selected_categories = '';
    }
    else {
      $selected_categories = implode( ",", $_POST['gamecategs'] );
    }

    $updated_options = array (
      'list_limit'           => filter_input( INPUT_POST, "limit", FILTER_SANITIZE_NUMBER_INT ),
      'list_title'           => esc_textarea( filter_input( INPUT_POST, "title" ) ),
      'list_begin_wrap'      => esc_textarea( filter_input( INPUT_POST, "begin_wrap" ) ),
      'list_end_wrap'        => esc_textarea( filter_input( INPUT_POST, "end_wrap" ) ),
      'list_item_begin_wrap' => esc_textarea( filter_input( INPUT_POST, "begin_item" ) ),
      'list_item_end_wrap'   => esc_textarea( filter_input( INPUT_POST, "end_item" ) ),
      'list_char_limit'      => filter_input( INPUT_POST, "charlimit", FILTER_SANITIZE_NUMBER_INT ),
      'list_include_cats'    => $selected_categories,
      'list_list_begin_wrap' => esc_textarea( filter_input( INPUT_POST, "begin_list" ) ),
      'list_list_end_wrap'   => esc_textarea( filter_input( INPUT_POST, "end_list" ) ),
      'list_leading'         => esc_textarea( filter_input( INPUT_POST, "leading" ) ),
      'autocreate_list'      => esc_textarea( filter_input( INPUT_POST, "autolist" ) ),
      'list_rows'            => filter_input( INPUT_POST, "rows", FILTER_SANITIZE_NUMBER_INT ),
    );

    update_option('mygamelistcreator', $updated_options);
    echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
  }

  $options = get_option('mygamelistcreator');

  if ( ! $options ) {
    mygame_list_install();
    $options = get_option('mygamelistcreator');
  }

  ?>
  <form method="post" name="dbgamelist" id="dbgamelist" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <input type="hidden" name="dbcgl_update" id="dbcgl_update" value="true" />
    <table cellpadding="2" cellspacing="2">
      <tr>
        <td>Game List Title:</td>
        <td><input type="text" size="50" name="title" value='<?php echo $options['list_title']; ?>'></td>
        <td>Set a title for your game list.</td>
      </tr>
      <tr>
        <td>Limit Showed Games:</td>
        <td><input type="text" size="50" name="limit" value='<?php echo $options['list_limit']; ?>'></td>
        <td>Set "-1" (without "") to create a list with all published games. Otherwise enter an integer.</td>
      </tr>
      <tr>
        <td>Limit Game Names:</td>
        <td><input type="text" size="50" name="charlimit" value='<?php echo $options['list_char_limit']; ?>'></td>
        <td>Set how many chars of a game name should be shown. Leave blank to show the entire name.</td>
      </tr>
      <tr>
        <td>Begin Wrap:</td>
        <td><input type="text" size="50" name="begin_wrap" value='<?php echo $options['list_begin_wrap']; ?>'></td>
        <td>Enter your global begin wrap here for the game list, i.e "&lt;div&gt;".</td>
      </tr>
      <tr>
        <td>End Wrap:</td>
        <td><input type="text" size="50" name="end_wrap" value='<?php echo $options['list_end_wrap']; ?>'></td>
        <td>Enter your global end wrap here for the game list, i.e "&lt;/div&gt;".</td>
      </tr>
      <tr>
        <td>Begin List Wrap:</td>
        <td><input type="text" size="50" name="begin_list" value='<?php echo $options['list_list_begin_wrap']; ?>'></td>
        <td>Enter your list begin wrap here, i.e "&lt;ul&gt;".</td>
      </tr>
      <tr>
        <td>End List Wrap:</td>
        <td><input type="text" size="50" name="end_list" value='<?php echo $options['list_list_end_wrap']; ?>'></td>
        <td>Enter your list end wrap here, i.e "&lt;/ul&gt;".</td>
      </tr>
      <tr>
        <td>Begin Item Wrap:</td>
        <td><input type="text" size="50" name="begin_item" value='<?php echo $options['list_item_begin_wrap']; ?>'></td>
        <td>Enter your begin wrap for a game in the list here, i.e "&lt;li&gt;"</td>
      </tr>
      <tr>
        <td>End Item Wrap:</td>
        <td><input type="text" size="50" name="end_item" value='<?php echo $options['list_item_end_wrap']; ?>'></td>
        <td>Enter your end wrap for a game in the list here, i.e "&lt;/li&gt;"</td>
      </tr>

      <tr valign="top">
        <td>Games Categories:</td>
        <td>
        <?php
          $dbgamelist_categs = explode (',', $options['list_include_cats'] );

          // Get all categories
          $categs = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false, ) );

          foreach ($categs as $key => $term ) {
            foreach ($dbgamelist_categs as $categ) {
              if ($term->term_id == $categ) {
                $check = 'checked'; break;
              }
              else {
                $check = '';
              }
            }

            echo '<input type="checkbox" name="gamecategs[]" value="'.$term->term_id.'" '.$check.'/>'.$term->name. '<br />';
          }
        ?>
        <br /><br />
        <input type="button" name="CheckAll" value="Check All" onClick="selectall()">
        <br /><br />
        </td>
        <td>Select categories to create a game list. If you wish to have games from each category included in the list, please select all categories.</td>
      </tr>

      <tr>
        <td>Create List With Leading Letters:</td>
        <td><input type="checkbox" name="leading" value="Yes" <?php if ( $options['list_leading'] == 'Yes') echo 'checked'; ?> /></td>
        <td>Check this option if you want to create an alphabetically ordered list with leading letters, like on Miniclip. If this option is not checked "default" list will be created where the games are ordered by the publish dates.</td>
      </tr>

      <tr>
        <td>Rows For Lists With Leading Letters:</td>
        <td><input type="text" size="50" name="rows" value='<?php echo $options['list_rows']; ?>'></td>
        <td>Enter the number of rows that should be created. Default value when using FunGames theme is <strong>5</strong></td>
      </tr>

      <tr>
        <td>Auto Create Game List:</td>
        <td><input type="checkbox" name="autolist" value="Yes" <?php if ( $options['autocreate_list'] == 'Yes') echo 'checked'; ?> /></td>
        <td>Check this option if you want to create the game list automatically.</td>
      </tr>
    </table>
    <br />
    <input type="submit" name="dbcgl_update" value="Save Settings" class="button" style="float:left;margin:20px;">
  </form>

  <input type="button" name="dbcglmanually" value="Create A Game List" onclick="location.href='<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin.php?page=mygamelistcreator.php&dbcgl_action=dbcglmanually'" class="button" style="float:left;margin:20px;" />

  <div style="clear:both"></div>
  <div>
    <h3>Description</h3>

    <h4>Cached Game List</h4>
    This plugin will create a static file called "gamelist.php" in the wordpress root directory.<br />
    It contains a list with available games on your wordpress blog.<br />
    This will save your server performance because a new game list will only be created if a post is
    published, edited or deleted.<br />
    You can also build a game list manually to check your settings.<br />

    <h5>Usage Example 1</h5>
    Use the following settings to create an unordered list using all available games:
    <br /><br />
    <ul>
      <li>Begin Wrap =  &lt;div id="yourstylehere"&gt;</li>
      <li>End Wrap   =  &lt;/div&gt;</li>
      <li>Begin List Wrap = &lt;ul&gt;</li>
      <li>End List Wrap = &lt;/ul&gt;</li>
      <li>Begin Item Wrap = &lt;li&gt;</li>
      <li>End Item Wrap = &lt;/li&gt;</li>
    </ul>
    <br />
    Your created list will look like this in html:<br /><br />
    &lt;div id="yourstylehere"&gt;<br />
    &lt;ul&gt;<br />
    &lt;li&gt;Game 1&lt;/li&gt;<br />
    &lt;li&gt;Game 2&lt;/li&gt;<br />
    &lt;li&gt;Game 3&lt;/li&gt;<br />
    &lt;li&gt;Game 4&lt;/li&gt;<br />
    &lt;li&gt;Game 5&lt;/li&gt;<br />
    &lt;/ul&gt;<br />
    &lt;/div&gt; <br /><br />

    <h5>Usage Example 2 - MiniClip Like</h5>
    If you want to creat a game list like on miniclip with leading letters then you can use the following settings:
    <br /><br />
    <ul>
      <li>Limit Game Name = 16</li>
      <li>Begin Wrap =  &lt;div id="gamelist"&gt;</li>
      <li>End Wrap   =  &lt;div class="clear"&gt;&lt;/div&gt;&lt;/div&gt;</li>
      <li>Begin List Wrap = &lt;ul style="width:128px;"&gt;</li>
      <li>End List Wrap = &lt;/ul&gt;</li>
      <li>Begin Item Wrap = &lt;li&gt;</li>
      <li>End Item Wrap = &lt;/li&gt;</li>
    </ul>
    <br />

    <h5>Theme Integration</h5>
    Add this code to the desired location in your theme files to display the created list:
    <br />
    <br />
    <strong>&lt;php if (function_exists('get_game_list')) { get_game_list(); } ?&gt;</strong>
    <br />
    <br />
    Here is an example of the game list on <a href="http://fungames24.net" target="_blank">FunGames24.net</a> created with this Plugin:
    <br />
    <br />
    <img src="<?php echo plugins_url('mygamelistcreator/gamelist_example.png') ?>">
  </div>


<?php

  mygamelistcreator_footer();
}


function create_lists() {
  $options = get_option('mygamelistcreator');

  if ($options['autocreate_list'] == 'Yes') {
    create_mygame_list();
  }
}


//
// @brief: Creates a game list and sore it into gamelist.php
//
function create_mygame_list() {
  global $wpdb;

  // Set timeout
  if( !ini_get('safe_mode') ) { set_time_limit(0); }

  // Get Options
  $options = get_option('mygamelistcreator');

  if (empty($options['list_include_cats'])) {
    $categs = '';
  }
  else {
    $categs = 'cat='.$options['list_include_cats'].'&';
  }

  // file to write
  $game_file = ABSPATH. '/gamelist.php';
  $fp = fopen($game_file, 'w');

  if ( $options['list_leading'] == 'Yes') {
    // Miniclip style
    $games = get_posts(''.$categs.'numberposts='.$options['list_limit'].'&order=ASC&orderby=title');
    $count = count($games);
    $games_per_row = ($count + 36) / intval($options['list_rows']);
    $last_letter = '';
    $actual_letter = '';
    $game_count = 0;

    if ($games) {
      $str  = $options['list_begin_wrap']."\n".$options['list_title']."\n".$options['list_list_begin_wrap']."\n";
      foreach ($games as $game) {
        $game_count++;

        $actual_letter = strtoupper(substr($game->post_title, 0, 1));

        if ( $actual_letter != strtoupper($last_letter) ) {
          $last_letter = $actual_letter;
          $str .= $options['list_item_begin_wrap'].'<font style="font-weight: bold;color:#000;">'.$actual_letter.'</font>'.$options['list_item_end_wrap']."\n";
          $game_count++;
        }

        if ((strlen($game->post_title) > $options['list_char_limit'])) {
          $gametitle = substr($game->post_title, 0, $options['list_char_limit'])."..";
        }
        else {
          $gametitle = $game->post_title;
        }

        $str .= $options['list_item_begin_wrap'].'<a href="';
        $str .= get_permalink($game->ID);
        $str .= '" title="'.$game->post_title.'">' .ucwords(strtolower($gametitle)). '</a>';
        $str .= $options['list_item_end_wrap']."\n";

        if ($game_count >= $games_per_row) {
          $game_count = 0;
          // create new ul
          $str .= $options['list_list_end_wrap']."\n".$options['list_list_begin_wrap']."\n";
        }
      }
      $str .= $options['list_list_end_wrap']."\n".$options['list_end_wrap']."\n";
    }
  }
  else {
    // Default style
    $games = get_posts(''.$categs.'numberposts='.$options['list_limit'].'&order=DESC&orderby=date');

    if ($games) {
      $str  = "\n".$options['list_begin_wrap']."\n".$options['list_title']."\n".$options['list_list_begin_wrap']."\n";

      foreach ($games as $game) {
        if ((strlen($game->post_title) > $options['list_char_limit'])) {
          $gametitle = substr($game->post_title, 0, $options['list_char_limit'])."..";
        }
        else {
          $gametitle = $game->post_title;
        }

        $str .= $options['list_item_begin_wrap'].'<a href="';
        $str .= get_permalink($game->ID);
        $str .= '" title="'.$game->post_title.'">' .ucwords(strtolower($gametitle)). '</a>';
        $str .= $options['list_item_end_wrap']."\n";
      }

      $str .= $options['list_list_end_wrap']."\n"."<div style='clear:both'></div>".$options['list_end_wrap']."\n";
    }
  }

  // Write the list to file
  $write_result = fwrite($fp, htmlspecialchars_decode( $str ) );
  fclose($fp);

  // Check the result
  if (!$write_result) { $result = false; } else { $result = true; }

  return $result;
}

/**
 * @brief Creates a Gamelist in a post or page
 */
function mygamelist_process($atts) {
  // Include the precompiled gamelist for the posts
  if ( file_exists(ABSPATH. '/gamepostlist.php') ) {
    return include (ABSPATH. '/gamepostlist.php');
  }
  else {
    return 'Please Pre-Compile A Game List!';
  }
}


//
// @brief: Shows the game list in users theme
//
function get_game_list() {
  if ( file_exists(ABSPATH.'/gamelist.php') ) {
    include (ABSPATH.'/gamelist.php');
  }
  else {
    // Can't find a game list
    return "Can't find a game list! Please create a new game list...";
  }
}
?>