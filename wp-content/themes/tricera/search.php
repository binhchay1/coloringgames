<?php get_header(); ?>

<div class="ft_gameshowcase">

  <div class="games_title">
    <h3 class="catpage_title">
      <?php _e("Search Results", "tricera"); ?>
    </h3>
  </div>

    <?php if ( tricera_get_option( 'homepage_header_banner' ) ) : ?>
    <div class="leader_adspace_top">
      <?php echo tricera_get_option( 'homepage_header_banner' ); ?>
    </div>
    <?php endif; ?>

  <?php if ( have_posts() ) :
    global $wp_query;

    $prevlink = str_replace(array("<a href=\"","\" ></a>"),"",get_previous_posts_link(""));
    $nextlink = str_replace(array("<a href=\"","\" ></a>"),"",get_next_posts_link(""));
    ?>
    <span id="pagination_link">
      <?php if( get_query_var('paged') > 1 ) { ?>
        <span class="pagi_left">
          <a class="side_left" href="<?php echo $prevlink; ?>"><?php _e( 'Previous', 'tricera' ); ?></a>
        </span>
      <?php } ?>

      <?php if( get_query_var('paged') < $wp_query->max_num_pages && $wp_query->max_num_pages > 1 ) { ?>
        <span class="pagi_right">
          <a class="side_right" href="<?php echo $nextlink; ?>"><?php _e( 'Next', 'tricera' ); ?></a>
        </span>
      <?php } ?>
    </span>

    <div id="content_tricera">
      <ul class="gametab">
        <?php while ( have_posts() ) : the_post(); ?>
          <li class="is-new">
            <a href="<?php the_permalink() ?>" class="tooltip" title='<span>Play <?php the_title_attribute(); ?></span>'>
              <span class="framespan"></span>
              <?php myarcade_thumbnail( 100, 100, 'ft_gameimg' ); ?>
            </a>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>

    <?php if ( tricera_get_option( 'homepage_footer_banner' ) ) : ?>
    <div class="leader_adspace_bottom">
      <?php echo tricera_get_option( 'homepage_footer_banner' ); ?>
    </div>
    <?php endif; ?>

    <?php tricera_navigation();
  else : ?>
    <div id="content_tricera">
      <p class="noresult_error"><?php _e("Sorry, but you are looking for something that isn't here.", "tricera"); ?></p>

      <p class="suggestgame_title">Games you may also like...</p>

      <?php get_template_part( 'partials/games', 'random' ); ?>
    </div>
  <?php endif; ?>

</div>

<?php get_footer(); ?>