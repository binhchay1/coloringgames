<?php get_header(); ?>

<div class="ft_gameshowcase">

  <div class="games_title">
    <h1 class="catpage_title"><?php the_title(); ?></h1>
  </div>

  <div id="content" class="contentbox_page">

    <?php
    if (have_posts()) :
      while (have_posts()) : the_post();
        ?>
        <div class="singlepage inner" id="post-<?php the_ID(); ?>">

          <div class="cover">
            <div class="entry">
              <?php the_content(__('Read more..', 'tricera')); ?>
              <div class="clear"></div>
            </div>
          </div>
        </div>
        <?php
      endwhile;

      // Do some action after the page output
      do_action('tricera_after_page');
    else :
      ?>
      <h2 class="pagetitle">
        <?php _e("Sorry, Can't find that Game. But maybe you like one of these games:", "tricera"); ?>
      </h2>

      <?php
      // Get some random games
      get_template_part( 'partials/games', 'random' );
    endif;

    // Do some actions before the content wrap ends
    do_action('tricera_before_content_end');
    ?>

  </div>
</div>

<?php get_footer(); ?>