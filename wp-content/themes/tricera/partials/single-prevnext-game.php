<?php if ( tricera_get_option('previous_next_game_box') == '1' ) : ?>

<div id="post-nav">
  <?php
  global $box_title, $post;

  // Get next game from the same cateogry
  $game_post = get_previous_post(true);

  if ( $game_post instanceof WP_Post ) {
    $post = $game_post;

    $box_title = __( 'Previous Game:', 'tricera' );
    ?>

    <div class="previous-post">
      <?php get_template_part( 'partials/single', 'prevnext-game-box' ); ?>
    </div>
    <?php
    wp_reset_postdata();
  }

  $game_post = get_next_post(true);

  if ( $game_post instanceof WP_Post ) {
    $post = $game_post;

    $box_title = __( 'Next Game:', 'tricera' );
    ?>

    <div class="next-post">
      <?php get_template_part( 'partials/single', 'prevnext-game-box' ); ?>
    </div>
    <?php
    wp_reset_postdata();
  }
?>
</div>
<?php endif; ?>