<?php
/**
 * Tricera Setup
 *
 * @package WordPress
 * @subpackage Tricera
 */
 
 // Include backend stuff only on admin
if ( is_admin() ) {
  // Include widget form functions
  //get_template_part( 'inc/admin/widget-form-functions' );

  // Include option framework
  get_template_part( 'inc/admin/config' );

  // Include TGM Plugin Activation
  get_template_part( 'inc/admin/plugins' );

  // Theme update
  //get_template_part( 'inc/admin/theme-update' );
}


/**
 * Retrieve a theme option
 *
 * @version 1.0.0
 * @since   1.0.0
 * @param   string $option_name
 * @return  mixed
 */
function tricera_get_option( $option_name, $default = false ) {
  global $tricera;

  if ( empty( $tricera ) ) {
    $tricera = get_option( 'tricera' );
  }

  /** Original Code **/
  if ( ! isset( $tricera[ $option_name ] ) ) {
    return $default;
  }

  return $tricera[ $option_name ];

}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @version 4.0.0
 * @since   1.0.0
 * @return  void
 */
function tricera_setup() {

  // Make theme available for translation
  // Translations can be filed in the /lang/ directory
  load_theme_textdomain('tricera', get_template_directory() . '/languages');

  // Custom menu support
  add_theme_support( 'menus' );

  register_nav_menus( array(
    'footer' => __( 'Footer Navigation', 'tricera' ),
  ) );

  // Add default posts and comments RSS feed links to head.
  add_theme_support( 'automatic-feed-links' );

  // Let WordPress manage the document title.
  // By adding theme support, we declare that this theme does not use a
  // hard-coded <title> tag in the document head, and expect WordPress to
  // provide it for us.
  add_theme_support( 'title-tag' );

  // Add default posts and comments RSS feed links to head.
  add_theme_support( 'automatic-feed-links' );

  // We don't want to show our WordPress version
  remove_action('wp_head', 'wp_generator');

  // This theme uses post thumbnails
  add_theme_support('post-thumbnails');
  set_post_thumbnail_size( 100, 100, true );

  // Switch default core markup for search form, comment form, and comments
  // to output valid HTML5.
  add_theme_support( 'html5', array(
    'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
  ) );

  // Don't show the admin bar
  add_filter( 'show_admin_bar', '__return_false' );

  // Add shortcode support to text widget
  add_filter( 'widget_text', 'do_shortcode' );

  // Declare WooCommerce support
  add_theme_support( 'woocommerce' );

}
add_action( 'after_setup_theme', 'tricera_setup' );

/**
 * Enables MCE Editor For bbPress
 *
 * @version 4.0.0
 * @since   4.0.0
 * @param   array $args
 * @return  array
 */
function tricera_bbp_enable_visual_editor( $args = array() ) {
  $args['tinymce'] = true;
  $args['quicktags'] = false;
  return $args;
}
add_filter( 'bbp_after_get_the_content_parse_args', 'tricera_bbp_enable_visual_editor' );