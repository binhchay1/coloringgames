<?php get_header(); ?>

<div class="ft_gameshowcase">
  <div id="content_tricera">

    <p class="noresult_error"><?php _e("Sorry, but you are looking for something that isn't here.", "tricera"); ?></p>

    <p class="suggestgame_title"><?php _e('But maybe you like one of these games', 'tricera' ); ?></p>

    <?php get_template_part( 'partials/games', 'random' ); ?>
  </div>
</div>

<?php get_footer(); ?>