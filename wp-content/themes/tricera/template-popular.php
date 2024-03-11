<?php
/**
 * Template name: Tricera Popular
 */
?>

<?php get_header(); ?>

      <div class="ft_gameshowcase">
            <div class="games_title">
               <h1 class="catpage_title">
               <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
              <?php the_title(); ?>
            </a>
               </h1>
      </div>

    <?php if ( tricera_get_option( 'popular_game_header_banner' ) ) : ?>
    <div class="leader_adspace_top" style="margin-top:0px;">
      <?php echo tricera_get_option( 'popular_game_header_banner' ); ?>
    </div>
    <?php endif; ?>
               <?php if (function_exists('the_views')) { ?>
        <div id="content_tricera">

      <?php
      wp_reset_query();
      $recent = new WP_Query("cat=&posts_per_page&v_sortby=views&v_orderby=desc");
			$prevlink = str_replace(array("<a href=\"","\" ></a>"),"",get_previous_posts_link(""));
			$nextlink = str_replace(array("<a href=\"","\" ></a>"),"",get_next_posts_link(""));




		?>

         <ul class="gametab">
         <?php
          $postcount = 0;
					while($recent->have_posts()) : $recent->the_post();
				?>
              <li class="is-new"><a href="<?php the_permalink() ?>" class="tooltip" title='<span>Play <?php the_title_attribute(); ?></span><br />'><span class="framespan"></span><?php myarcade_thumbnail( 100, 100,'ft_gameimg' ); ?></a></li>

              <?php
if ( tricera_get_option('popular_game_page_banner') ) {

if (++$postcount == 9){ ?>
<li class="adspace"><div class="adblock">
 <?php echo tricera_get_option( 'popular_game_page_banner' ); ?>
</div>
</li>
<?php }
}
?>
<?php endwhile; ?>

         </ul>

         </div>
    
    <?php if ( tricera_get_option( 'popular_game_footer_banner' ) ) : ?>
    <div class="leader_adspace_bottom">
      <?php echo tricera_get_option( 'popular_game_footer_banner' ); ?>
    </div>
    <?php endif; ?>

              </div>

         <?php tricera_navigation(); ?>

        <?php } else { ?>
        <div class="ft_gameshowcase">
        <div id="content_tricera">
          <p>Sorry, But the page you're trying to access is not available.</p>
        </div>
        </div>
        <?php } ?>



 <?php get_footer(); ?>