<?php get_header(); ?>

<?php
$h1Homepage = get_option('h1_homepage');
$h1HomepageDescription = get_option('h1_homepage_description');
?>

<?php if($h1Homepage != null && $h1HomepageDescription != null) { ?>
<div id="content-header" class="rounded bg-white">
  <h1 style="color: #461662;"><?php echo $h1Homepage ?></h1>
  <p style="margin-top: 10px; font-weight: bold; font-size: 14px;"><?php echo $h1HomepageDescription ?></p>
</div>
<?php } ?>

<div class="ft_gameshowcase">

  <?php if (tricera_get_option('homepage_header_banner')) : ?>
    <div class="leader_adspace_top">
      <?php echo tricera_get_option('homepage_header_banner'); ?>
    </div>
  <?php endif; ?>

  <div id="content_tricera">
    <?php
    $recent = new WP_Query('posts_per_page=' . get_option('posts_per_page') . '&paged=' . get_query_var('paged') . tricera_mobile_tag() . '&meta_key=mabp_game_type');

    $prevlink = str_replace(array("<a href=\"", "\" ></a>"), "", get_previous_posts_link(""));
    $nextlink = str_replace(array("<a href=\"", "\" ></a>"), "", get_next_posts_link(""));
    ?>
    <span id="pagination_link">
      <?php if (get_query_var('paged') > 1) { ?>
        <span class="pagi_left">
          <a class="side_left" href="<?php echo $prevlink; ?>"><?php _e('Previous', 'tricera'); ?></a>
        </span>
      <?php } ?>
      <?php if (get_query_var('paged') < $recent->max_num_pages) { ?>
        <span class="pagi_right">
          <a class="side_right" href="<?php echo $nextlink; ?>"><?php _e('Next', 'tricera'); ?></a>
        </span>
      <?php } ?>
    </span>

    <ul id="gametab" class="gametab">
      <?php
      $postcount = 0;
      $diplay_count = (wp_is_mobile()) ? 1 : 10;

      while ($recent->have_posts()) :
        $recent->the_post();

        $postcount++;

        if (tricera_get_option('homepage_game_banner')) {
          if ($postcount == $diplay_count) {
      ?>
            <li class="adspace">
              <div class="adblock">
                <?php echo tricera_get_option('homepage_game_banner'); ?>
              </div>
            </li>
        <?php
          }
        }
        ?>

        <li>
          <a href="<?php the_permalink() ?>" class="tooltip" title='<span>Play <?php the_title_attribute(); ?></span><br />'>
            <span class="framespan"></span>
            <?php myarcade_thumbnail(100, 100, 'ft_gameimg'); ?>
          </a>
        </li>

      <?php
      endwhile;

      // Restore original Post Data
      wp_reset_postdata();
      ?>
    </ul>
  </div>

  <?php if (tricera_get_option('homepage_footer_banner')) : ?>
    <div class="leader_adspace_bottom">
      <?php echo tricera_get_option('homepage_footer_banner'); ?>
    </div>
  <?php endif; ?>

  <?php if (tricera_get_option('front_page_text_status', '1')) : ?>
    <div class="catpage_desc">
      <h2><?php echo tricera_get_option('front_page_text_title'); ?></h2>
      <p><?php echo tricera_get_option('front_page_text_content'); ?></p>
    </div>
  <?php endif; ?>

</div>

<?php tricera_navigation(); ?>

<?php get_footer(); ?>