<?php get_header(); ?>

<?php 
global $wpdb;
$category = get_category( get_query_var( 'cat' ) );
$cat_id = $category->cat_ID;
$getCustomCategory = $wpdb->get_results("SELECT * FROM wp_category_custom WHERE category_id = '" . $cat_id . "'");
?>

  <?php if($getCustomCategory[0]->title != null && $getCustomCategory[0]->description) { ?>
  <div id="content-header" class="rounded bg-white">
    <h1 style="color: #461662;"><?php echo $getCustomCategory[0]->title ?></h1>
    <p style="margin-top: 10px; font-weight: bold; font-size: 14px;"><?php echo $getCustomCategory[0]->description ?></p>
  </div>
  <?php } ?>

<div class="ft_gameshowcase">

  <?php if(have_posts()) : ?>
    <div class="games_title">
      <h1 class="catpage_title"><?php
      if (is_category()) {
        echo single_cat_title(),' Games';
      } elseif (is_day()) {
        _e("Archive for", "tricera"); ?> <?php the_time('F jS, Y');
      } elseif (is_month()) {
        _e("Archive for", "tricera"); ?> <?php the_time('F, Y');
      } elseif (is_year()) {
        _e("Archive for", "tricera"); ?> <?php the_time('Y');
      } elseif (is_author()) {
        _e("Author Archive", "tricera");
      } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
        _e("Blog Archives", "tricera");
      } elseif (is_tag() ) {
        _e("Browse by tag:", "tricera"); ?> <?php echo single_tag_title( '', false );
      } else {
        _e("Blog Archives", "tricera");
      }
      ?></h1></div>

      <?php

      if ( tricera_get_option('cat_desc_position') == '2' && tricera_get_option('cat_desc') == '1' ) {
        echo tricera_category_description();
      }
      ?>

    <?php if ( tricera_get_option( 'category_page_header_banner' ) ) : ?>
     <div class="leader_adspace_top" style="margin-top:0px;">
      <?php echo tricera_get_option( 'category_page_header_banner' ); ?>
    </div>
    <?php endif; ?>

      <div id="content_tricera">

        <?php
        global $wp_query;

        $prevlink = str_replace(array("<a href=\"","\" ></a>"),"",get_previous_posts_link(""));
        $nextlink = str_replace(array("<a href=\"","\" ></a>"),"",get_next_posts_link(""));
        ?>
        <span id="pagination_link">
          <?php if(get_query_var('paged') > 1) { ?>
            <span class="pagi_left">
              <a class="side_left" href="<?php echo $prevlink; ?>"><?php _e( 'Previous', 'tricera' ); ?></a>
            </span>
          <?php } ?>
          <?php if(get_query_var('paged') < $wp_query->max_num_pages && $wp_query->max_num_pages > 1) { ?>
            <span class="pagi_right">
              <a class="side_right" href="<?php echo $nextlink; ?>"><?php _e( 'Next', 'tricera' ); ?></a>
            </span>
          <?php } ?>
        </span>
        <ul class="gametab">
          <?php
          $postcount = 0;
          $diplay_count = ( wp_is_mobile() ) ? 1 : 10;

          while (have_posts()) :
            the_post();

            $postcount++;

            if ( tricera_get_option('category_game_banner') ) {
              if ( $postcount == $diplay_count ) {
                ?>
                <li class="adspace">
                  <div class="adblock">
                    <?php echo ( tricera_get_option('category_game_banner') ); ?>
                  </div>
                </li>
                <?php
              }
            }
            ?>

            <li class="is-new"><a href="<?php the_permalink() ?>" class="tooltip" title='<span>Play <?php the_title_attribute(); ?></span><br />'><span class="framespan"></span><?php myarcade_thumbnail( 100, 100, 'ft_gameimg' ); ?></a></li>

          <?php endwhile; ?>
        </ul>

      </div>
      
    <?php if ( tricera_get_option( 'category_page_footer_banner' ) ) : ?>
    <div class="leader_adspace_bottom">
      <?php echo tricera_get_option( 'category_page_footer_banner' ); ?>
    </div>
    <?php endif; ?>

      <?php

       if ( tricera_get_option('cat_desc_position') == '1' && tricera_get_option('cat_desc') == '1' ) {

        echo tricera_category_description();

      }
      ?>
    </div>

    <?php tricera_navigation(); ?>

    <?php else: ?>
      <div class="ft_gameshowcase">
        <div id="content_tricera">
          <p><?php _e("Sorry, but you are looking for something that isn't here.", "tricera"); ?></p>
        </div>
      </div>
    <?php endif; ?>



    <?php get_footer(); ?>