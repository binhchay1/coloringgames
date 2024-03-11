<?php
/**
* Template Name: Tricera Favorites
*/

if ( ! tricera_wp_fav_post() ) {
  wp_die( __('Please install WP Favorite Posts Plugin in order to display favorited games' ) );
}
?>

<?php get_header(); ?>

<div class="ft_gameshowcase">

      <div class="games_title">
      <h1 class="catpage_title">
        <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
          <?php the_title(); ?>
        </a>
      </h1>
      <div class="wpfp_clear_list"><?php wpfp_clear_list_link(); ?></div>
    </div>

  <div id="content_tricera">

    <?php if ( tricera_get_option( 'favorite_game_header_banner' ) ) : ?>
    <div class="leader_adspace_top" style="margin-top:0px;">
      <?php echo tricera_get_option( 'favorite_game_header_banner' ); ?>
    </div>
    <?php endif; ?>


    <?php

    $favorite_post_ids = wpfp_get_user_meta();

    if ( $favorite_post_ids ) :
      $favorite_post_ids = array_reverse($favorite_post_ids);
      $post_per_page = 30;
      $page = intval(get_query_var('paged'));

      $favorites = new WP_Query( array(
        'post__in' => $favorite_post_ids,
        'posts_per_page'=> $post_per_page,
        'orderby' => 'post__in',
        'paged' => $page,
      ) );

      $prevlink = str_replace(array("<a href=\"","\" ></a>"),"",get_previous_posts_link(""));
      $nextlink = str_replace(array("<a href=\"","\" ></a>"),"",get_next_posts_link(""));
      ?>

        <ul id="gametab" class="gametab">
          <?php
          $postcount = 0;
          $diplay_count = ( wp_is_mobile() ) ? 1 : 10;

          while ($favorites->have_posts()) {
            $favorites->the_post();
            ?>

            <li>
              <a class="tooltip" href="<?php the_permalink(); ?>" title="<span><?php echo esc_attr( the_title_attribute() ); ?></span>">
                <span class="framespan"></span>
                <?php myarcade_thumbnail( 100, 100,'ft_gameimg' ); ?>
              </a>
              <div class="remove-link"><?php wpfp_remove_favorite_link(get_the_ID());?></div>
            </li>

            <?php
            if ( tricera_get_option('favorite_game_page_banner') ) {

              if ( $postcount == $diplay_count ) { ?>
              <div class="adspace_fav"><div class="adblock">
                <?php echo tricera_get_option('favorite_game_page_banner'); ?>
              </div></div>
              <?php }
            }
          }

          wp_reset_query();
          ?>
        </ul>

        <?php
        $total_pages = $favorites->max_num_pages;

        if ($total_pages > 1) {
          ?>
          <span id="pagination_link">
            <?php if(get_query_var('paged') > 1) { ?>
              <span class="pagi_left">
                <a class="side_left" href="<?php echo $prevlink; ?>"><?php _e( 'Previous', 'tricera' ); ?></a>
              </span>
            <?php } ?>
            <?php if(get_query_var('paged') < $total_pages) { ?>
              <span class="pagi_right">
                <a class="side_right" href="<?php echo $nextlink; ?>"><?php _e( 'Next', 'tricera' ); ?></a>
              </span>
            <?php } ?>
          </span>
          <?php
        }
        ?>
    <?php else : ?>
      <div class="nofavs"></div>
    <?php endif; ?>


    <?php if ( tricera_get_option( 'favorite_game_footer_banner' ) ) : ?>
    <div class="leader_adspace_bottom">
      <?php echo tricera_get_option( 'favorite_game_footer_banner' ); ?>
    </div>
    <?php endif; ?>

    <div class="paginationbox">
      <?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?>
    </div>

    <?php wpfp_cookie_warning(); ?>
  </div>
</div>

<?php get_footer(); ?>