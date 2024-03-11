<?php
global $box_title;
?>

<div class="prevnext-post-thumb">
	<a href="<?php the_permalink(); ?>"><?php myarcade_thumbnail( 100, 100, 'post-gameimg' ); ?></a>
</div>

<div class="pr-post_info">
	<h4>
		<?php echo $box_title; ?> <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php myarcade_title(16); ?></a>
	</h4>

	<p><?php myarcade_excerpt(100); ?></p>

	<a href="<?php the_permalink(); ?>" class="button button-pink"><?php _e( 'Play', 'tricera' ); ?></a>
</div>