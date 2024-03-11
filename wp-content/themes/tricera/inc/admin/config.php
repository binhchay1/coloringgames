<?php
/**
 * Options Config file
 * @uses ReduxFramework
 */

/**
 * Checks if redux is installed and deletes tgmpa notice dismissed if framework is missing
 *
 * @version 4.1.0
 * @since   4.1.0
 * @return  void
 */
function myarcade_redux_check() {
  if ( ! class_exists( 'Redux' ) ) {
    delete_user_meta( get_current_user_id(), 'tgmpa_dismissed_notice_tricera' );
  }
}
add_action( 'init', 'myarcade_redux_check' );

if ( class_exists( 'Redux' ) ) :

/** remove redux menu under the tools **/
function tricera_remove_redux_menu() {
  remove_submenu_page( 'tools.php', 'redux-about' );
}
add_action( 'admin_menu', 'tricera_remove_redux_menu', 12 );

// Deactivate News Flash
$GLOBALS['redux_notice_check'] = 1;

// This is your option name where all the Redux data is stored.
$opt_name = "Tricera";

// Add compiler filter
add_filter( 'redux/options/' . $opt_name . '/compiler', 'tricera_compiler_action' );

/**
 * Compile custom css settings
 *
 * @version 1.3.0
 * @since   1.0.0
 * @access  public
 * @param   array $options
 * @param   array $css
 * @param   bool $changed_values
 * @return  array
 */


function tricera_compiler_action( $options ) {
  $filename = get_stylesheet_directory(). '/create.css';
  global $wp_filesystem;
  if( empty( $wp_filesystem ) ) {
    require_once( ABSPATH .'/wp-admin/includes/file.php' );
    WP_Filesystem();
  }

  $css = $options['css_custom']."\n";

  if( $wp_filesystem ) {
    $wp_filesystem->put_contents( $filename, $css, FS_CHMOD_FILE );
  }
}


// Get theme settings
$theme = wp_get_theme();

$args = array(
  // TYPICAL -> Change these values as you need/desire
  'opt_name'             => $opt_name,
  // This is where your data is stored in the database and also becomes your global variable name.
  'display_name'         => $theme->get( 'Name' ),
  // Name that appears at the top of your panel
  'display_version'      => $theme->get( 'Version' ),
  // Version that appears at the top of your panel
  'menu_type'            => 'submenu',
  //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
  'allow_sub_menu'       => true,
  // Show the sections below the admin menu item or not
  'menu_title'           => __( 'Theme Options', 'tricera' ),
  'page_title'           => __( 'Theme Options', 'tricera' ),
  // You will need to generate a Google API key to use this feature.
  // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
  'google_api_key'       => '',
  // Set it you want google fonts to update weekly. A google_api_key value is required.
  'google_update_weekly' => false,
  // Must be defined to add google fonts to the typography module
  'async_typography'     => true,
  // Use a asynchronous font on the front end or font string
  //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
  'admin_bar'            => true,
  // Show the panel pages on the admin bar
  'admin_bar_icon'       => 'dashicons-portfolio',
  // Choose an icon for the admin bar menu
  'admin_bar_priority'   => 50,
  // Choose an priority for the admin bar menu
  'global_variable'      => '',
  // Set a different name for your global variable other than the opt_name
  'dev_mode'             => false,
  /*'forced_dev_mode_off'  => true,*/
  'ajax_save'            => true,
  'allow_tracking'       => false,
  'tour'                 => false,
  // Show the time the page took to load, etc
  'update_notice'        => true,
  // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
  'customizer'           => true,
  // Enable basic customizer support
  //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
  //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

  // OPTIONAL -> Give you extra features
  'page_priority'        => null,
  // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
  'page_parent'          => 'themes.php',
  // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
  'page_permissions'     => 'manage_options',
  // Permissions needed to access the options panel.
  'menu_icon'            => '',
  // Specify a custom URL to an icon
  'last_tab'             => '',
  // Force your panel to always open to a specific tab (by id)
  'page_icon'            => 'icon-themes',
  // Icon displayed in the admin panel next to your menu_title
  'page_slug'            => 'tricera_options',
  // Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
  'save_defaults'        => true,
  // On load save the defaults to DB before user clicks save or not
  'default_show'         => false,
  // If true, shows the default value next to each field that is not the default value.
  'default_mark'         => '',
  // What to print by the field's title if the value shown is default. Suggested: *
  'show_import_export'   => true,
  // Shows the Import/Export panel when not used as a field.

  // CAREFUL -> These options are for advanced use only
  'transient_time'       => 60 * MINUTE_IN_SECONDS,
  'output'               => true,
  // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
  'output_tag'           => true,
  // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
  'footer_credit'     => __( 'tricera by <a href="http://myarcadeplugin.com/" title="WordPress Arcade" target="_blank">MyArcadePlugin</a>', 'tricera' ),

  // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
  'database'             => '',
  // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
  'use_cdn'              => true,
  // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.

  // HINTS
  'hints'                => array(
      'icon'          => 'el el-question-sign',
      'icon_position' => 'right',
      'icon_color'    => 'lightgray',
      'icon_size'     => 'normal',
      'tip_style'     => array(
          'color'   => 'red',
          'shadow'  => true,
          'rounded' => false,
          'style'   => 'bootstrap',
      ),
      'tip_position'  => array(
          'my' => 'top left',
          'at' => 'bottom right',
      ),
      'tip_effect'    => array(
          'show' => array(
              'effect'   => 'fade',
              'duration' => '100',
              'event'    => 'click mouseover',
          ),
          'hide' => array(
              'effect'   => 'slide',
              'duration' => '500',
              'event'    => 'click mouseleave',
          ),
      ),
  )
);

// SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
$args['share_icons'][] = array(
  'url'   => 'https://facebook.com/MyArcadePlugin',
  'title' => __('Like us on Facebook', 'tricera'),
  'icon'  => 'el-icon-facebook'
);

$args['share_icons'][] = array(
  'url'   => 'https://twitter.com/MyArcadePlugin',
  'title' => __('Follow us on Twitter', 'tricera'),
  'icon'  => 'el-icon-twitter'
);

$args['share_icons'][] = array(
  'url'   => 'http://www.youtube.com/user/NetReviewDE/videos',
  'title' => __('Visit us on YouTube', 'tricera'),
  'icon'  => 'el-icon-youtube'
);

// Set Arguments
Redux::setArgs( $opt_name, $args );

// Set Sections

// General Settings
Redux::setSection( $opt_name, array(
  'title'     => __('General Settings', 'tricera'),
  'desc'      => __('This section customizes the global theme options.', 'tricera'),
  'icon'      => 'el-icon-cog',
  'fields'    => array(


    array(
      'id' => 'smooth_scroll',
      'type' => 'switch',
      'title' => __('Smooth Scrolling', 'tricera'),
      'hint' => array(
        'title' => __("Smooth Scrolling", 'tricera'),
        'content' => __("If enabled, this will cause the scrollbar style to change and to slowly scroll down the page in a modern IPad feel.", 'tricera' )
      ),
      "default" => 0,
      'on' => __('Enabled', 'tricera'),
      'off' => __('Disabled', 'tricera'),
    ),

    array(
      'id' => 'back_top',
      'type' => 'switch',
      'title' => __('Back To Top', 'tricera'),
      'hint' => array(
        'title' => __("Back To Top", 'tricera'),
        'content' => __("If enabled, this will load a back to top button on the bottom right hand corner of the screen.", 'tricera' )
      ),
      "default" => 1,
      'on' => __('Enabled', 'tricera'),
      'off' => __('Disabled', 'tricera'),
    ),

    array(
      'id' => 'lazy_load',
      'type' => 'switch',
      'title' => __('Lazy Load', 'tricera'),
      'hint' => array(
        'title' => __("Lazy Load", 'tricera'),
        'content' => __("If enabled, this option will delay loading of images. Images outside the viewport are not loaded until user scolls to them.", 'tricera' )
      ),
      "default" => 1,
      'on' => __('Enabled', 'tricera'),
      'off' => __('Disabled', 'tricera'),
    ),

    array(
      'id' => 'lazy_load_animation',
      'type' => 'switch',
      'title' => __('Lazy Load Animation', 'tricera'),
      'hint' => array(
        'title' => __("Lazy Load Animation", 'tricera'),
        'content' => __("If enabled, an image loading animation will be displayed during the loading.", 'tricera' )
      ),
      "default" => 1,
      'on' => __('Enabled', 'tricera'),
      'off' => __('Disabled', 'tricera'),
    ),

    array(
      'id' => 'mobile',
      'type' => 'switch',
      'title' => __('Mobile Games', 'tricera'),
      'hint' => array(
        'title' => __("Mobile games for mobile devices", 'tricera'),
        'content' => __("If enabled, only games that are tagged with 'mobile' will be displayed if a mobile device has been detected. Otherwise all game types will be displayed for all devices.", 'tricera' )
      ),
      "default" => 1,
      'on' => __('Enabled', 'tricera'),
      'off' => __('Disabled', 'tricera'),
    ),

    array(
      'id' => 'favorite_game_page',
      'type' => 'select',
      'multi' => false,
      'data' => 'page',
      'default' => '',
      'title' => __('Favorites Game Page', 'tricera'),
      'hint' => array(
        'title' => __("Favorites Game Page", 'tricera'),
        'content' => __("Select the page you want to use for your favorites game page.", 'tricera' )
      ),
    ),

    array(
      'id' => 'popular_game_page',
      'type' => 'select',
      'multi' => false,
      'data' => 'page',
      'default' => '',
      'title' => __('Popular Game Page', 'tricera'),
      'hint' => array(
        'title' => __("Popular Game Page", 'tricera'),
        'content' => __("Select the page you want to use for your popular game page.", 'tricera' )
      ),
    ),

  )
) );

// Header & Footer
Redux::setSection( $opt_name, array(
  'icon'      => 'el-icon-website',
  'title'     => __('Header & Footer', 'tricera'),
  'fields'    => array(
    array(
      'id' => 'favicon',
      'type' => 'media',
      'title' => __('Favicon', 'tricera'),
      'hint' => array(
        'title'   => __( "Upload a custom favicon", 'tricera' ),
        'content' => __( "This option allows you to upload your own favorite icon.", 'tricera' ),
      ),
      'default'  => array(
        'url'=> get_template_directory_uri().'/images/favicon.ico',
      ),
    ),

    array(
      'id' => 'logohd',
      'type' => 'media',
      'preview'=> false,
      'title' => __('Logo', 'tricera'),
      'hint' => array(
        'title'   => __( "Upload a custom logo", 'tricera' ),
        'content' => __( "This option allows you to upload your own logo.", 'tricera' ),
      ),
      'default'  => array(
        'url'=> get_template_directory_uri().'/images/logo.png',
      ),
    ),

    array(
      'id' => 'logologin',
      'type' => 'media',
      'preview'=> false,
      'title' => __('Login Logo', 'tricera'),
      'hint' => array(
        'title'   => __( "Upload a custom login logo", 'tricera' ),
        'content' => __( "This option allows you to upload your own login logo. It you don't upload a custom login logo your main logo will be displayed on the login page.", 'tricera' ),
      ),
      'default'  => array(
        'url'=> get_template_directory_uri().'/images/logo.png',
      ),
    ),

    array(
      'id' => 'custom_header_code',
      'type' => 'ace_editor',
      'mode'     => 'html',
      'theme'    => 'chrome',
      'title' => __('Header Code', 'tricera'),
      'hint' => array(
        'title' => __('Header Code', 'tricera'),
        'content' => __("At this field you can add custom JavaScript, CSS or other code that should be added in the header.", 'tricera'),
      ),
      'default' => ''
      ),

    array(
      'id' => 'custom_footer_code',
      'type' => 'ace_editor',
      'mode'     => 'html',
      'theme'    => 'chrome',
      'title' => __('Footer Code', 'tricera'),
      'hint' => array(
        'title' => __('Footer Code', 'tricera'),
        'content' => __("At this field you can add custom JavaScript, CSS or other code that should be added in the footer.", 'tricera'),
      ),
      'default' => ''
    ),

    array(
      'id' => 'footer_copyright',
      'type' => 'text',
      'title' => __('Footer Copyright', 'tricera'),
      'hint' => array(
        'title' => __('Footer Copyright', 'tricera'),
        'content' => __("Here you can customize the copyright message displayed in the footer.", 'tricera'),
      ),
      'default' => sprintf( __('Powered by %sMyArcadePlugin%s', 'tricera'), '<a target="_blank" href="https://myarcadeplugin.com/" title="WordPress Arcade" itemprop="url">', '</a>' )
    ),

  )
) );

// Front Page
Redux::setSection( $opt_name, array(
  'icon'      => 'el-icon-home',
  'title'     => __('Front Page', 'tricera'),
  'fields'    => array(

   array(
      'id' => 'posts_per_home',
      'type' => 'spinner',
      'title' => __( "Homepage Game Count", 'tricera'),
      'hint' => array(
        'title' => __( "Homepage Game Count", 'tricera'),
        'content' => __( "Set how many games should be displayed on the homepage.", 'tricera'),
      ),
      "default" => 36,
      "min" => "1",
      "step" => "1",
      "max" => "200"
    ),

    array(
      'id' => 'front_page_text_status',
      'type' => 'switch',
      'title' => __('Front Page Text', 'tricera'),
      'hint' => array(
        'title' => __('Front Page Text', 'tricera'),
        'content' => __( 'Here you can add some text for SEO purpose or just to introduce your site.', 'tricera' ),
      ),

      'on' => __('Show', 'tricera'),
      'off' => __('Hide', 'tricera'),
      "default" => 1
    ),

    array(
      'id' => 'section_front_page_text_start',
      'type' => 'section',
      'indent' => true,
      'required' => array( 'front_page_text_status', 'equals', '1' ),
    ),

    array(
      'id' => 'front_page_text_title',
      'type' => 'text',
      'title' => __('Title', 'tricera'),
      'hint' => array(
        'title' => __('Title', 'tricera'),
        'content' => __( 'Title that should be displayed above the text.', 'tricera' ),
      ),
      'default' => __('Front Page Text', 'tricera' ),
    ),

    array(
      'id' => 'front_page_text_content',
      'type' => 'editor',
      'title' => __( 'Content', 'tricera'),
      'hint' => array(
        'title' => __('Content', 'tricera'),
        'content' => __( 'Content of the front page text box.', 'tricera' ),
      ),
      'default' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
    ),

   array(
      'id' => 'section_front_page_text_end',
      'type' => 'section',
      'indent' => false,
      'required' => array( 'front_page_text_status', 'equals', '1' ),
    ),

  )
) );

// Pre-Game Page
Redux::setSection( $opt_name, array(
  'title'     => __('Game Page', 'myarcadetheme'),
  'desc'      => sprintf( __('%sThis section customizes the game page presentation your site.%s', 'myarcadetheme'), '<p class="description">', '</p>'),
  'icon'      => 'el-icon-eye-open',
  'fields'    => array(

    array(
      'id'        => 'game_page_layout',
      'type'      => 'select',
      'title'     => __('Game Page Layout Style', 'tricera'),
      'subtitle'  => __('Select the layout for the game page.', 'tricera'),
      'options'   => array(
        'simple'           => __( 'Simple', 'tricera' ),
        'complex'          => __( 'Complex', 'tricera' ),
      ),
      'default'   => 'simple',
      'desc'      => __( 'This will change the layout of the game page.', 'tricera' )
      ),

    array(
      'id' => 'previous_next_game_box',
      'type' => 'switch',
      'title' => __('Previous & Next Game Box', 'myarcadetheme'),
      'desc'=> __("Enable or disable the next and previous games box in game play page.", 'myarcadetheme'),
      "default" => 1,
      'on' => __('Enabled', 'myarcadetheme'),
      'off' => __('Disabled', 'myarcadetheme'),
    ),

    array(
      'id' => 'display_screenshots',
      'type' => 'switch',
      'title' => __('Screen Shots', 'myarcadetheme'),
      'desc'=> __("Enable this if you want to display game screenshots on single game pages (only when available)", 'myarcadetheme'),
      "default" => 1,
      'on' => __('Enabled', 'myarcadetheme'),
      'off' => __('Disabled', 'myarcadetheme'),
    ),

    array(
      'id' => 'game_embed_box',
      'type' => 'switch',
      'title' => __('Game Embed Box', 'myarcadetheme'),
      'desc'=> __("Enable or disable the game embed box ('Embed this game on your site..').", 'myarcadetheme'),
      "default" => 1,
      'on' => __('Enabled', 'myarcadetheme'),
      'off' => __('Disabled', 'myarcadetheme'),
    ),

    array(
      'id' => 'fullscreen_button',
      'type' => 'switch',
      'title' => __('Fullscreen Button', 'myarcadetheme'),
      'desc'=> __("Enable this if you want to display the fullscreen button.", 'myarcadetheme'),
      "default" => 1,
      'on' => __('Enabled', 'myarcadetheme'),
      'off' => __('Disabled', 'myarcadetheme'),
    ),

    array(
      'id' => 'lights_button',
      'type' => 'switch',
      'title' => __('Lights Button', 'myarcadetheme'),
      'desc'=> __("Enable this if you want to display the lights button.", 'myarcadetheme'),
      "default" => 1,
      'on' => __('Enabled', 'myarcadetheme'),
      'off' => __('Disabled', 'myarcadetheme'),
    ),

    array(
      'id' => 'favorite_button',
      'type' => 'switch',
      'title' => __('Favorite Button', 'myarcadetheme'),
      'desc'=> __("Enable this if you want to display the favorite button.", 'myarcadetheme'),
      "default" => 1,
      'on' => __('Enabled', 'myarcadetheme'),
      'off' => __('Disabled', 'myarcadetheme'),
    ),

  )
) );


// Category Pages
Redux::setSection( $opt_name, array(
  'id' => 'category_options',
  'title'     => __('Category Page', 'tricera'),
  'desc'      => sprintf( __('%sThis section customizes the category page of your site.%s', 'tricera'), '<p class="description">', '</p>'),
  'icon'      => 'el-icon-folder',
  'fields'    => array(
    array(
      'id' => 'posts_per_page',
      'type' => 'spinner',
      'title' => __( "Game count", 'tricera'),
      'hint' => array(
        'title' => __( "Games per category page", 'tricera'),
        'content' => __( "Set how many games should be displayed per category page. This overrides the WordPress option 'Blog pages show at most'.", 'tricera'),
      ),
      "default" => get_option('posts_per_page'),
      "min" => "1",
      "step" => "1",
      "max" => "200"
    ),

    array(
      'id' => 'posts_per_category_cloud',
      'type' => 'spinner',
      'title' => __( "Category Cloud Game Count", 'tricera'),
      'hint' => array(
        'title' => __( "Category Cloud Games Count", 'tricera'),
        'content' => __( "Set how many games should be displayed on the category cloud widget.", 'tricera'),
      ),
      "default" => 25,
      "min" => "1",
      "step" => "1",
      "max" => "200"
    ),

    array(
      'id' => 'cat_desc',
      'type' => 'switch',
      'title' => __('Category Description', 'tricera'),
      'hint' => array(
        'title' => __( "Category Description", 'tricera'),
        'content' => __("If enabled, a category description will be displayed on game categpry pages.", 'tricera' )
      ),
      "default" => 1,
      'on' => __('Enabled', 'tricera'),
      'off' => __('Disabled', 'tricera'),
    ),

    array(
      'id'        => 'cat_desc_position',
      'type'      => 'select',
      'required' => array('cat_desc','equals','1'),
      'title'     => __('Category Description Position', 'tricera'),
      'subtitle'  => __('Select where the category description appears.', 'tricera'),
      'options'   => array(1 => __('Below', 'tricera'), 2 => __('Above', 'tricera')),
      'default'   => 1,
      'desc' => __('Show category description above or below the games.', 'tricera')
      ),

    array(
      'id' => 'cat_breadcrumbs',
      'type' => 'switch',
      'title' => __('Category Breadcrumbs', 'tricera'),
      'hint' => array(
        'title' => __( "Category Breadcrumbs", 'tricera'),
        'content' => __("If enabled, enabled breadcrumbs will be displayed on the category pages.", 'tricera' )
      ),
      "default" => 1,
      'on' => __('Enabled', 'tricera'),
      'off' => __('Disabled', 'tricera'),
    ),


  )
) );

// Tag Pages
Redux::setSection( $opt_name, array(
  'id' => 'tag_options',
  'title'     => __('Tag Page', 'tricera'),
  'desc'      => sprintf( __('%sThis section customizes the tag page of your site.%s', 'tricera'), '<p class="description">', '</p>'),
  'subsection' => true,
  'icon'      => 'el-icon-tag',
  'fields'    => array(
    array(
      'id' => 'posts_per_tag_page',
      'type' => 'spinner',
      'title' => __( "Game count", 'tricera'),
      'hint' => array(
        'title' => __( "Games per tag page", 'tricera'),
        'content' => __( "Set how many games should be displayed per tag page. This overrides the WordPress option 'Blog pages show at most'.", 'tricera'),
      ),
      "default" => get_option('posts_per_page'),
      "min" => "1",
      "step" => "1",
      "max" => "100"
    ),

    array(
      'id' => 'posts_per_tag_cloud',
      'type' => 'spinner',
      'title' => __( "Tag Cloud Game Count", 'tricera'),
      'hint' => array(
        'title' => __( "Tag Cloud Games Count", 'tricera'),
        'content' => __( "Set how many games should be displayed on the tag cloud widget.", 'tricera'),
      ),
      "default" => 25,
      "min" => "1",
      "step" => "1",
      "max" => "200"
    ),

   array(
      'id' => 'tag_desc',
      'type' => 'switch',
      'title' => __('Tag Description', 'tricera'),
      'hint' => array(
        'title' => __( "Tag Description", 'tricera'),
        'content' => __("If enabled, a tag description will be displayed on game tag pages.", 'tricera' )
      ),
      "default" => 0,
      'on' => __('Enabled', 'tricera'),
      'off' => __('Disabled', 'tricera'),
    ),

    array(
      'id' => 'tag_breadcrumbs',
      'type' => 'switch',
      'title' => __('Tag Breadcrumbs', 'tricera'),
      'hint' => array(
        'title' => __( "Tag Breadcrumbs", 'tricera'),
        'content' => __("If enabled, enabled breadcrumbs will be displayed on the tag pages.", 'tricera' )
      ),
      "default" => 1,
      'on' => __('Enabled', 'tricera'),
      'off' => __('Disabled', 'tricera'),
    ),
  )
) );

// Search Pages
Redux::setSection( $opt_name, array(
  'id' => 'search_options',
  'title'     => __('Search Page', 'tricera'),
  'desc'      => sprintf( __('%sThis section customizes the search page of your site.%s', 'tricera'), '<p class="description">', '</p>'),
  'subsection' => true,
  'icon'      => 'el-icon-search',
  'fields'    => array(
    array(
      'id' => 'posts_per_search_page',
      'type' => 'spinner',
      'title' => __( "Game count", 'tricera'),
      'hint' => array(
        'title' => __( "Games per tag page", 'tricera'),
        'content' => __( "Set how many games should be displayed per tag page. This overrides the WordPress option 'Blog pages show at most'.", 'tricera'),
      ),
      "default" => get_option('posts_per_page'),
      "min" => "1",
      "step" => "1",
      "max" => "200"
    ),

    array(
      'id' => 'search_breadcrumbs',
      'type' => 'switch',
      'title' => __('Search Breadcrumbs', 'tricera'),
      'hint' => array(
        'title' => __( "Search Breadcrumbs", 'tricera'),
        'content' => __("If enabled, enabled breadcrumbs will be displayed on search pages.", 'tricera' )
      ),
      "default" => 1,
      'on' => __('Enabled', 'tricera'),
      'off' => __('Disabled', 'tricera'),
    ),

 )
) );

// Styling Options
Redux::setSection( $opt_name, array(
  'icon'      => 'el-icon-brush',
  'title'     => __('Styling Options', 'tricera'),
  'fields'    => array(
    array(
      'id'        => 'colorscheme',
      'type'      => 'select',
      'title'     => __('Theme Stylesheet', 'tricera'),
      'subtitle'  => __('Select your themes alternative color scheme.', 'tricera'),
      'options'   => array(1 => __('Purple', 'tricera'), 2 => __('Blue', 'tricera'), 3 => __('Pink', 'tricera')),
      'default'   => 1,
      'desc' => __('For everything to work properly with the chosen color, off Custom CSS', 'tricera')
      ),

    array(
      'id'        => 'toolbar-color',
      'type'      => 'color',
      'compiler'  => false,
      'title'     => __('Toolbar Color', 'tricera'),
      'subtitle'  => __('Choose the toolbar color for mobile devices.', 'tricera'),
      'transparent' => false,
      'default'  => '#9E6AC1',
      ),

    array(
      'id'        => 'ios-toolbar-color',
      'type'      => 'select',
      'title'     => __('IOS Status Bar Color', 'tricera'),
      'subtitle'  => __('Select the color for the IOS Status Bar.', 'tricera'),
      'options'   => array(
        'default'           => __( 'Default', 'tricera' ),
        'black'             => __( 'Black', 'tricera' ),
        'black-translucent' => __( 'Black-Translucent', 'tricera' ),
      ),
      'default'   => 'default',
      'desc'      => __( 'This will change the status bar color for IOS devices. Avoid using Black Translucent as it hampers user experience.', 'tricera' )
      ),

    array(
      'id'        => 'css_custom',
      'type'      => 'ace_editor',
      'compiler'    => true,
      'title'     => __('Custom CSS', 'tricera'),
      'subtitle'  => __('Quickly add some CSS to your theme by adding it to this block.', 'tricera'),
      'mode'      => 'css',
      'theme'     => 'monokai',
      'default'   => "",
    ),
  )
) );

// Advertisement Banner
Redux::setSection( $opt_name, array(
  'icon'      => 'el-icon-bullhorn',
  'title'     => __('Advertisement Banner', 'tricera'),
  'fields'    => array(

    array(
      'id' => 'homepage_header_banner',
      'type' => 'textarea',
      'title' => __('Homepage Header Banner', 'tricera'),
      'subtitle' => __('Put your code for 728x90 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr728.gif" alt="bnr" />',
      ),

    array(
      'id' => 'homepage_footer_banner',
      'type' => 'textarea',
      'title' => __('Homepage Footer Banner', 'tricera'),
      'subtitle' => __('Put your code for 728x90 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr728.gif" alt="bnr" />',
      ),

    array(
      'id' => 'homepage_game_banner',
      'type' => 'textarea',
      'title' => __('Homepage Game Banner', 'tricera'),
      'subtitle' => __('Put your code for 300x250 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr300.gif" alt="bnr" />',
      ),

  )
) );


// Game Page Advertisements
Redux::setSection( $opt_name, array(
  'id' => 'game_page_advertisement_options',
  'title'     => __('Game Page', 'tricera'),
  'desc'      => sprintf( __('%sThis section controls ads exclusively for the game page.%s', 'tricera'), '<p class="description">', '</p>'),
  'subsection' => true,
  'fields'    => array(

    array(
      'id' => 'game_page_banner',
      'type' => 'textarea',
      'title' => __('Game Banner', 'tricera'),
      'subtitle' => __('Put your code for 300x250 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr300.gif" alt="bnr" />',
      ),

    array(
      'id' => 'game_page_top_banner',
      'type' => 'textarea',
      'title' => __('Header Banner', 'tricera'),
      'subtitle' => __('Put your code for 728x90 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr728.gif" alt="bnr" />',
      ),

    array(
      'id' => 'game_page_bottom_banner',
      'type' => 'textarea',
      'title' => __('Bottom Banner', 'tricera'),
      'subtitle' => __('Put your code for 728x90 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr728.gif" alt="bnr" />',
      ),

    array(
      'id' => 'game_page_side_banner',
      'type' => 'textarea',
      'title' => __('Side Banner', 'tricera'),
      'subtitle' => __('Put your code for 160x600 banner here.', 'tricera'),
      'default' => '',
      ),

  )
) );

// Category Page Advertisements
Redux::setSection( $opt_name, array(
  'id' => 'category_page_advertisement_options',
  'title'     => __('Category Game Page', 'tricera'),
  'desc'      => sprintf( __('%sThis section controls ads exclusively for the category page.%s', 'tricera'), '<p class="description">', '</p>'),
  'subsection' => true,
  'fields'    => array(

    array(
      'id' => 'category_page_header_banner',
      'type' => 'textarea',
      'title' => __('Header Banner', 'tricera'),
      'subtitle' => __('Put your code for 728x90 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr728.gif" alt="bnr" />',
      ),

    array(
      'id' => 'category_page_footer_banner',
      'type' => 'textarea',
      'title' => __('Footer Banner', 'tricera'),
      'subtitle' => __('Put your code for 728x90 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr728.gif" alt="bnr" />',
      ),

    array(
      'id' => 'category_game_banner',
      'type' => 'textarea',
      'title' => __('Game Banner', 'tricera'),
      'subtitle' => __('Put your code for 300x250 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr300.gif" alt="bnr" />',
      ),

  )
) );

// Popular Page Advertisements
Redux::setSection( $opt_name, array(
  'id' => 'popular_page_advertisement_options',
  'title'     => __('Popular Game Page', 'tricera'),
  'desc'      => sprintf( __('%sThis section controls ads exclusively for the popular game page.%s', 'tricera'), '<p class="description">', '</p>'),
  'subsection' => true,
  'fields'    => array(

    array(
      'id' => 'popular_game_header_banner',
      'type' => 'textarea',
      'title' => __('Header Banner', 'tricera'),
      'subtitle' => __('Put your code for 728x90 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr728.gif" alt="bnr" />',
      ),

    array(
      'id' => 'popular_game_footer_banner',
      'type' => 'textarea',
      'title' => __('Footer Banner', 'tricera'),
      'subtitle' => __('Put your code for 728x90 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr728.gif" alt="bnr" />',
      ),

    array(
      'id' => 'popular_game_page_banner',
      'type' => 'textarea',
      'title' => __('Game Banner', 'tricera'),
      'subtitle' => __('Put your code for 300x250 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr300.gif" alt="bnr" />',
      ),

  )
) );

// Favorite Page Advertisements
Redux::setSection( $opt_name, array(
  'id' => 'favorite_page_advertisement_options',
  'title'     => __('Favorite Game Page', 'tricera'),
  'desc'      => sprintf( __('%sThis section controls ads exclusively for the favorite game page.%s', 'tricera'), '<p class="description">', '</p>'),
  'subsection' => true,
  'fields'    => array(

    array(
      'id' => 'favorite_game_header_banner',
      'type' => 'textarea',
      'title' => __('Header Banner', 'tricera'),
      'subtitle' => __('Put your code for 728x90 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr728.gif" alt="bnr" />',
      ),

    array(
      'id' => 'favorite_game_footer_banner',
      'type' => 'textarea',
      'title' => __('Footer Banner', 'tricera'),
      'subtitle' => __('Put your code for 728x90 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr728.gif" alt="bnr" />',
      ),

    array(
      'id' => 'favorite_game_page_banner',
      'type' => 'textarea',
      'title' => __('Game Banner', 'tricera'),
      'subtitle' => __('Put your code for 300x250 banner here.', 'tricera'),
      'default' => '<img src="'.get_template_directory_uri().'/images/cnt/bnr300.gif" alt="bnr" />',
      ),

  )
) );

endif;
