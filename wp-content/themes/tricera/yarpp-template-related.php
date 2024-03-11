<?php
if ( (tricera_get_option('game_page_layout') == 'simple') && $related_query->have_posts()) { ?>

 <ul class="games-related_custom">
    <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
      <li>
       <?php $screen = get_post_meta($post->ID, 'mabp_thumbnail_url', true); ?>
          <a href="<?php the_permalink() ?>" class="tooltip" title='<span>Play <?php the_title_attribute(); ?></span><br />'>
            <span class="framespan"></span><img class="ft_gameimg" src="<?php echo $screen; ?>" alt="<?php the_title_attribute(); ?>" /> </a>
      </li>
    <?php endwhile; ?>
 </ul>
<?php
}
?>
<?php if ( ( tricera_get_option('game_page_layout') == 'complex') && $related_query->have_posts()) { ?>

 <ul class="games-related">
    <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
      <li>
       <?php $screen = get_post_meta($post->ID, 'mabp_thumbnail_url', true); ?>
          <a href="<?php the_permalink() ?>" class="tooltip" title='<span>Play <?php the_title_attribute(); ?></span><br />'>
            <span class="framespan"></span><img class="ft_gameimg" src="<?php echo $screen; ?>" alt="<?php the_title_attribute(); ?>" /> </a>
      </li>
    <?php endwhile; ?>
 </ul>
<?php
}
?>


