<ul class="games-related">
  <?php
  $blog = get_cat_ID( get_option('tricera_blog_category') );
  $exclude = '';

  if ( $blog ) {
    $exclude = '&exclude='.$blog.',';
  }

  $relatedgames = new WP_Query ('showposts=18&orderby=rand'.$exclude);

  if ( $relatedgames->have_posts() ) {
    while ($relatedgames->have_posts()) :
      $relatedgames->the_post();
      ?>
      <li class="is-new">
        <?php $screen = get_post_meta($post->ID, 'mabp_thumbnail_url', true); ?>
        <a href="<?php the_permalink() ?>" class="tooltip" title='<span>Play <?php the_title_attribute(); ?></span><br />'>
          <span class="framespan"></span><img class="ft_gameimg" src="<?php echo $screen; ?>" alt="<?php the_title_attribute(); ?>"/>
        </a>
      </li>
    <?php endwhile;
  }
  wp_reset_query();
  ?>
  </ul>
