<?php

/**
 * Tricera
 *
 * @author Daniel Bakovic
 *
 * @package WordPress
 * @subpackage Tricera
 */


// MyArcadePlugin Theme API
include_once(get_template_directory() . '/inc/myarcade_api.php');

// Include setup theme
include_once(get_template_directory() . '/inc/setup.php');

/** Include theme actions API **/
include_once(get_template_directory() . '/inc/actions.php');

// Include rewrite rules
include_once(get_template_directory() . '/inc/rewrite.php');


/**
 * Enqueue styles
 *
 * @version 4.0.0
 * @since   1.0.0
 * @return  void
 */
function tricera_stylesheet_init()
{

  wp_enqueue_style('tricera-style', get_stylesheet_uri());

  if (tricera_get_option('colorscheme', 1) == 1) {
    wp_register_style('tricera-purple', get_template_directory_uri() . '/css/purple.css');
    wp_enqueue_style('tricera-purple');
  }

  if (tricera_get_option('colorscheme', 1) == 2) {
    wp_register_style('tricera-blue', get_template_directory_uri() . '/css/blue.css');
    wp_enqueue_style('tricera-blue');
  }

  if (tricera_get_option('colorscheme', 1) == 3) {
    wp_register_style('tricera-pink', get_template_directory_uri() . '/css/pink.css');
    wp_enqueue_style('tricera-pink');
  }

  if (tricera_get_option('css_custom')) {
    wp_register_style('tricera-create', get_stylesheet_directory_uri() . '/create.css');
    wp_enqueue_style('tricera-create');
  }
}
add_action('wp_enqueue_scripts',  'tricera_stylesheet_init');

/**
 * Enqueue scripts
 *
 * @version 4.0.0
 * @since   1.0.0
 * @return  void
 */
function tricera_scripts()
{

  wp_enqueue_script('tricera-scripts', get_template_directory_uri() . '/js/tricera.js', array('jquery'), false, true);

  if (is_singular()) {

    if (tricera_get_option('lights_button')) {
      wp_enqueue_script('tricera_lights', get_template_directory_uri() . '/js/lights.js', array('jquery'), false, true);
    }

    wp_enqueue_script('tricera_favorites', get_template_directory_uri() . '/js/favorites.js', array('jquery'), false, false);
  }

  // Enqueue comments JS
  if (is_singular() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
}
add_action('wp_enqueue_scripts', 'tricera_scripts');


/**
 * Add a custom header code
 *
 * @version 1.0.0
 * @since   1.0.0
 * @access  public
 * @return  void
 */
function tricera_header()
{

  if (tricera_get_option('custom_header_code')) {
    echo stripslashes(tricera_get_option('custom_header_code')) . "\n";
  }

  $favicon = tricera_get_option('favicon');

  if (!empty($favicon['url'])) {
    echo "<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"" . $favicon['url'] . "\">" . "\n";
  }

  // Print game schema
  myarcade_schema();
}
add_action('wp_head', 'tricera_header');


/**
 * Display a custom footer code
 *
 * @version 1.0.0
 * @since   1.0.0
 * @access  public
 * @return  void
 */
function tricera_footer()
{
  echo stripslashes(tricera_get_option('custom_footer_code')) . "\n";
}
add_action('wp_footer', 'tricera_footer');


if (!is_admin()) {
  function SearchFilter($query)
  {
    if ($query->is_search) {
      $query->set('post_type', 'post');
    }
    return $query;
  }
  add_filter('pre_get_posts', 'SearchFilter');
}

/*Tricera Logo Output*/
function tricera_web_logo()
{
  $custom_logo = tricera_get_option('logohd');
  if ($custom_logo) {
    echo '<style>#logo a {background: url(' . $custom_logo['url'] . ') top left no-repeat;}</style>';
  }
?>
  <a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"></a>
<?php
}

/**
 * Filter posts per page option
 *
 * @version 1.0.0
 * @since   1.0.0
 * @access  public
 * @return  int   Number of posts that should be displayed.
 */
function tricera_posts_per_page($posts_per_page)
{
  if (is_front_page() || is_home()) {
    return tricera_get_option('posts_per_home', 36);
  } elseif (is_tag()) {
    return tricera_get_option('posts_per_tag_page', 36);
  } elseif (is_search()) {
    return tricera_get_option('posts_per_search_page', 36);
  }

  return tricera_get_option('posts_per_page', 36);
}
add_filter('option_posts_per_page', 'tricera_posts_per_page');

/**
 * Filter number of games per tag cloud
 *
 * @version 4.0.0
 * @since   4.0.0
 * @access  public
 * @return  int   Number of posts that should be displayed.
 */
function tag_widget_limit($args)
{
  //Check if taxonomy option inside widget is set to tags
  if (isset($args['taxonomy']) && $args['taxonomy'] == 'post_tag') {
    $args['number'] = tricera_get_option('posts_per_tag_cloud', 25);
  }

  //Check if taxonomy option inside widget is set to category
  if (isset($args['taxonomy']) && $args['taxonomy'] == 'category') {
    $args['number'] = tricera_get_option('posts_per_category_cloud', 25);
  }

  return $args;
}
add_filter('widget_tag_cloud_args', 'tag_widget_limit');


function tricera_category_description()
{
  if (is_category()) {

    global $wpdb;
    $table_category = $wpdb->prefix . 'category_custom';
    $category = get_category(get_query_var('cat'));
    $result = $wpdb->get_results("SELECT * FROM $table_category WHERE `category_id` = $category->term_id ");

    if (!empty($result)) {
      $description = $result[0]->content;
      remove_filter('term_description', 'wpautop');
      echo '<h2 class="catpage_desc">';
      echo '<div>';
      echo htmlspecialchars_decode($description);
      echo '</div>';
      echo '</h2>';
    } else {
      remove_filter('term_description', 'wpautop');
      echo '<h2 class="catpage_desc">';
      echo category_description();
      echo '</h2>';
    }
  }
}

function custom_categories_title($output)
{
  $search = '/ title=".+"/i';
  $replace = '';
  return preg_replace($search, $replace, $output);
}
add_filter('wp_list_categories', 'custom_categories_title');



function tricera_default_top_bar()
{
?>
  <div class="NPBP_btn_options">
    <p class="NOBP_category_btn"><a href="#" id="show_cat" title="Play games from Categories"><?php _e('Categories', 'tricera'); ?></a></p>

    <div id="dropcat" class="CATCUBE_NOBP">
      <div class="CATCUBE_TOP"></div>
      <div class="CATCUBE_BG">
        <ul>
          <?php wp_list_categories('sort_column=name&title_li=&use_desc_for_title=0&depth=4'); ?>

        </ul>
      </div>
      <div class="CATCUBE_BOTTOM"></div>
    </div>

    <?php if (function_exists('the_views')) : ?>

      <p class="NOBP_fav_btn">
        <a class="tooltip" href="<?php echo get_the_permalink(tricera_get_option('popular_game_page')); ?>" title="<span><?php _e('Popular Games', 'tricera'); ?></span>">
          <?php _e('Popular Games', 'tricera'); ?>
        </a>
      </p>

    <?php endif; ?>

    <p class="NOBP_newest_btn">
      <a class="tooltip" href="<?php echo home_url(); ?>" title="<span><?php _e('Latest Games', 'tricera'); ?></span>">
        <?php _e('Latest Games', 'tricera'); ?>
      </a>
    </p>

  </div>
<?php
}

function tricera_category()
{
?>
  <div class="sitebtn_options">
    <p class="newest_btn"><a href="<?php echo home_url(); ?>" title="<?php _e('Latest Games', 'tricera'); ?>"><?php _e('Latest Games', 'tricera'); ?></a></p>

    <?php if (function_exists('the_views')) { ?>
      <p class="popular_btn"><a href="<?php echo get_the_permalink(tricera_get_option('popular_game_page')); ?>" title="<?php _e('Popular Games', 'tricera'); ?>"><?php _e('Popular Games', 'tricera'); ?></a></p>
    <?php } ?>

    <p class="category_btn"><a href="#" id="show_cat" title="<?php _e('Play games from Categories', 'tricera'); ?>"><?php _e('Categories', 'tricera'); ?></a></p>
    <div id="dropcat" class="CATCUBE">
      <div class="CATCUBE_TOP"></div>
      <div class="CATCUBE_BG">
        <ul>
          <?php wp_list_categories('sort_column=name&title_li=&use_desc_for_title=0&depth=4'); ?>
        </ul>
      </div>
      <div class="CATCUBE_BOTTOM"></div>
    </div>
  </div>
<?php
}

function tricera_navigation()
{ ?>
  <div class="paginationbox">
    <span class="pagination_link">
      <?php
      if (function_exists('wp_pagenavi')) {
        wp_pagenavi();
      } else {
      ?>
        <?php next_posts_link(__('Older games &laquo;', 'tricera')) ?>
        <?php previous_posts_link(__('&raquo; Newer games', 'tricera')) ?>
      <?php
      }
      ?>
    </span>
  </div>
<?php
}

/**
 * Generate game manage buttons - only for admins
 *
 * @version 4.0.0
 * @since   4.0.0
 * @return  void
 */

function tricera_admin_links()
{
  global $post;

  if (current_user_can('delete_posts')) {
    // Show edit and delete links
    echo "<p><span style='color: #ffffff; font-weight:bold; font: 14px arial;'>Admin Actions: </span><a href='" . get_delete_post_link() . "'>Delete</a>";
    echo " | ";
    echo "<a href='" . get_edit_post_link() . "'>Edit</a></p>";
  }
}

function tricera_get_excluded_categories()
{
  $result = 'exclude=';
  $blog = get_cat_ID(tricera_get_option('blog_category'));
  if ($blog) {
    $result = 'exclude=' . $blog . ',';
  }

  $result .= tricera_get_option('exclude_front_cat');

  return $result;
}

/**
 * Generate game manage buttons - only for admins
 *
 * @global type $post
 */

function tricera_get_best_players($count = 5)
{
  global $wpdb;

  return $wpdb->get_results("SELECT h.user_id, COUNT(*) as highscores, u.plays as plays
                             FROM " . MYARCADE_HIGHSCORES_TABLE . " AS h
                               INNER JOIN " . MYARCADE_USER_TABLE . " AS u ON h.user_id=u.user_id
                                 GROUP BY h.user_id
                                 ORDER BY highscores DESC LIMIT " . $count);
}
function tricera_contest_alert()
{
  if (!function_exists('myarcadecontest_get_contest_id_for_this_game'))
    return;

  $contest_id = myarcadecontest_get_contest_id_for_this_game();
  $user_id    = get_current_user_id();

  if (!$contest_id || myarcadecontest_check_user_is_in_contest($contest_id, $user_id))
    return;

  $permalink_open = '<a href="' . get_permalink($contest_id) . '" title="' . get_the_title($contest_id) . '">';
  $permalink_close = '</a>';

?>
  <div class="info">
    <p>
      <strong><?php _e('Howdy!', 'tricera'); ?></strong> <?php echo sprintf(__('There is an active contest available for this game. Click %shere%s to join the contest!', 'tricera'), $permalink_open, $permalink_close); ?>
    </p>
  </div>
  <?php }

function button_shortcode($atts, $content = null)
{
  extract(shortcode_atts(array(
    'color' => 'blue',
    'link'  => '#',
    'title' => $content
  ), $atts));

  return '<a class="s_button s_button-' . $color . '" href="' . $link . '" title="' . $title . '">' . $content . '</a>';
}

add_shortcode('button', 'button_shortcode');

function my_login_stylesheet()
{
  echo '<link rel="stylesheet" id="custom_wp_admin_css"  href="' . get_bloginfo('stylesheet_directory') . '/style.css' . '" type="text/css" media="all" />';
}
add_action('login_enqueue_scripts', 'my_login_stylesheet');
function my_login_logo_url()
{
  return get_bloginfo('url');
}
add_filter('login_headerurl', 'my_login_logo_url');
function my_login_logo_url_title()
{
  return get_bloginfo('name');
}
add_filter('login_headertext', 'my_login_logo_url_title');
function mysite_login_redirect()
{
  return get_bloginfo('url');
}
add_action('login_redirect', 'mysite_login_redirect');
function pagination($pages = '', $range = 4)
{
  $showitems = ($range * 2) + 1;
  global $paged;
  if (empty($paged)) $paged = 1;
  if ($pages == '') {
    global $wp_query;
    $pages = $wp_query->max_num_pages;
    if (!$pages) {
      $pages = 1;
    }
  }
  if ($pages > 1) {
    echo "<div class=\"pagination\">";
    //echo "<span>Page ".$paged." of ".$pages."</span>";
    //if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
    //if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";
    for ($i = 1; $i <= $pages; $i++) {
      if (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems) {
        $pgclass = "pagi_normal";
        if ($paged == $i) {
          $pgclass .= " pagi_on";
        }
        echo "<a href='" . get_pagenum_link($i) . "' class=\"" . $pgclass . "\">" . $i . "</a>";
      }
    }
    //if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">Next &rsaquo;</a>";
    //if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
    echo "</div>\n";
  }
}


if (!function_exists('tricera_favorite_link')) {
  function tricera_favorite_link($post_id, $opt, $action)
  {
    $img = get_template_directory_uri() . '/images/' . $action . '.png';
    $link  = "<img src='" . get_bloginfo('stylesheet_directory') . "/images/loading_small.gif' alt='Loading' title='Loading' style='display:none;margin-top:10px' class='tricera-fav-img' />";
    $link .= "<a href='?wpfpaction=" . $action . "&amp;postid=" . $post_id . "' title='<span>" . $opt . "</span>' rel='nofollow' class='tricera-favorites-link tooltip'><img src='" . $img . "' title='" . $opt . "' alt='" . $opt . "' class='favoritos tricera-fav-link' /></a>";
    $link = apply_filters('wpfp_link_html', $link);
    return $link;
  }
}


if (!function_exists('tricera_favorite')) {
  function tricera_favorite()
  {
    global $post, $action;
    // Works only when WP Favorite Post is active
    if (function_exists('wpfp_link')) {
      if ($action == "remove") {
        $str = tricera_favorite_link($post->ID, wpfp_get_option('remove_favorite'), "remove");
      } elseif ($action == "add") {
        $str = tricera_favorite_link($post->ID, wpfp_get_option('add_favorite'), "add");
      } elseif (wpfp_check_favorited($post->ID)) {
        $str = tricera_favorite_link($post->ID, wpfp_get_option('remove_favorite'), "remove");
      } else {
        $str = tricera_favorite_link($post->ID, wpfp_get_option('add_favorite'), "add");
      }
      echo $str;
    }
  }
}

/**
 * Check if at least one button is active and return true / false
 *
 * @return bool
 */

function tricera_display_buttons()
{

  if (tricera_get_option('fullscreen_button') || tricera_get_option('lights_button') || tricera_get_option('favorite_button')) {
    return true;
  }
  return false;
}

function tricera_wp_fav_post()
{

  $requirements = array(
    'wpfp_get_user_meta',
    'wpfp_remove_favorite_link',
    'wpfp_clear_list_link',
    'wpfp_cookie_warning',
  );

  foreach ($requirements as $function_name) {
    if (!function_exists($function_name)) {
      return false;
    }
  }

  return true;
}

/**
 *
 * Adds JSON LD MarkUp
 *
 * @version 4.0.0
 * @since   4.0.0
 * @return  void
 */
function myarcade_schema()
{

  // Skip if we are not on the single page
  if (!is_singular('post')) {
    return;
  }

  if (function_exists('is_game') && is_game()) {
    if (!function_exists('the_ratings')) {
      // Skip if WP-PostRatings isn't installed
      return;
    }

    $category = get_the_category();
  ?>
    <script type="application/ld+json">
      {
        "@context": "http://schema.org/",
        "type": "VideoGame",
        "aggregateRating": {
          "type": "aggregateRating",
          "ratingValue": "<?php echo get_post_meta(get_the_ID(), 'ratings_average', true); ?>",
          "reviewCount": "<?php echo get_post_meta(get_the_ID(), 'ratings_users', true); ?>",
          "bestRating": "5",
          "worstRating": "1"
        },
        "applicationCategory": "Game",
        "description": "<?php myarcade_excerpt(490); ?>",
        "genre": "<?php echo $category[0]->cat_name; ?>",
        "image": "<?php echo myarcade_thumbnail_url(); ?>",
        "name": "<?php myarcade_title(); ?>",
        "operatingSystem": "Web Browser",
        "url": "<?php the_permalink(); ?>"
      }
    </script>
  <?php
  } else {
    // Regular blog post/page
    $logo = tricera_get_option('logohd');
  ?>
    <script type="application/ld+json">
      {
        "@context": "http://schema.org",
        "@type": "Article",
        "headline": "<?php the_title(); ?>",
        "author": {
          "@type": "Person",
          "name": "<?php the_author(); ?>"
        },
        "datePublished": "<?php the_date('Y-n-j'); ?>",
        "dateModified": "<?php the_modified_date('Y-n-j'); ?>",
        "url": "<?php the_permalink(); ?>",
        "image": "<?php echo wp_get_attachment_url(get_post_thumbnail_id()); ?>",
        "description": "<?php the_excerpt(); ?>",

        "publisher": {
          "@type": "Organization",
          "name": "<?php bloginfo('name'); ?>",
          "logo": " <?php echo $logo['url']; ?>"
        },

        "mainEntityOfPage": {
          "@type": "WebPage",
          "url": "<?php the_permalink(); ?>"
        }
      }
    </script>
<?php
  }
}

/**
 * Get mobile query tag is the site is access by a mobile device
 *
 * @return  string
 */
function tricera_mobile_tag()
{

  if (wp_is_mobile() && tricera_get_option('mobile')) {
    return '&tag=mobile';
  }

  return false;
}

/**
 * Filter search query if mobile games function has been enabled
 *
 * @param   object $query
 * @return  void
 */
function tricera_pre_get_posts($query)
{
  // Proceed only if main query and not on admin
  if (is_admin() || !$query->is_main_query()) {
    return;
  }

  // Add mobile tag to query if enable
  if (tricera_mobile_tag()) {
    $query->set('tag_slug__and', 'mobile');
  }
}
add_action('pre_get_posts', 'tricera_pre_get_posts', 9);
