<?php
if ( have_posts() ) {
  while( have_posts() ) {
    the_post();
    ?>
    <div class="gamepagebox">

      <div class="gameshow">
        <h1>
          <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
            <?php the_title(); ?>
          </a>
        </h1>
      </div>

      <div class="leader_ab_sone">
	   <?php if ( tricera_get_option( 'game_page_top_banner' ) ) : ?>
	   <div class="leader_adspace_gamepage_top_ad">
	     <?php echo tricera_get_option( 'game_page_top_banner' ); ?>
	  </div>
	  <?php endif; ?>
      </div>

      <div class="game_content_wrap">

        <div class="related-games-wrap">
          <div class="related-games-box">

            <?php if ( function_exists('related_entries') ) {
              related_entries();
            }
            else {
              $tags = wp_get_post_tags($post->ID);

              if ($tags) {
                $tag_ids = array();

                foreach($tags as $individual_tag)
                  $tag_ids[] = $individual_tag->term_id;

                $args=array(
                  'tag__in' => $tag_ids,
                  'post__not_in' => array($post->ID),
                  'showposts'=>6,
                  'ignore_sticky_posts'=>true
                );

                $my_query = new wp_query($args);

                if ( $my_query->have_posts() ) {
                  echo '<ul class="games-related_custom">';

                  while ( $my_query->have_posts() ) {
                    $my_query->the_post();
                    ?>
                    <li>
                      <a href="<?php the_permalink() ?>" class="tooltip" title='<span>Play <?php the_title_attribute(); ?></span><br />'>
                        <span class="framespan"></span>
                        <?php myarcade_thumbnail( 100, 100, 'ft_gameimg' ); ?>
                      </a>
                    </li>
                    <?php
                  }

                  wp_reset_query();

                  echo '</ul>';
                }
              }
            }
            ?>
          </div>


        <div class="adblock" style="float: left;">
         <?php echo tricera_get_option( 'game_page_banner' ); ?>
        </div>

        </div>

        <div class="gameplay_smallbox">

          <?php if ( tricera_display_buttons() ) : ?>
            <div class="single-one-options">
              <div id="game_buttons" style="padding-top:0;">

                <?php if ( tricera_get_option('fullscreen_button') == '1' ) : ?>
                  <?php // Display Fullscreen button ?>

                    <a href="<?php echo get_permalink() . 'fullscreen'; ?>" class="fullscreen tooltip" title="<span><?php _e('Play Fullscreen', 'tricera'); ?></span>"><div class="command"></div></a>
                <?php endif; ?>

                <?php if ( tricera_get_option('lights_button') == '1' ) : ?>
                  <?php // Display Lights-Switch ?>
                  <div class="command interruptor">
                    <a href="#" title="<span><?php _e('Turn lights off and on', 'tricera'); ?></span>" class="interruptor tooltip"></a>
                  </div>
                <?php endif; ?>

                <?php if ( tricera_get_option('favorite_button') == '1' ) : ?>
                  <div class="command">
                  <?php if ( is_user_logged_in() ) {
                    tricera_favorite();
                  } else {
                    echo '<img class="favlogin" src="'.get_template_directory_uri().'/images/favlogin.png" alt="Login to favorite" />';
                  }
                  ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>

          <?php get_template_part( 'partials/games', 'play' ); ?>
        </div>

      </div>

      <?php get_template_part( 'partials/single', 'prevnext-game' ); ?>

	   <?php if ( tricera_get_option( 'game_page_bottom_banner' ) ) : ?>
	   <div class="leader_adspace_bottom">
	     <?php echo tricera_get_option( 'game_page_bottom_banner' ); ?>
	  </div>
	  <?php endif; ?>
    </div>

    <div id="turnoff"></div>

    <div class="normal_box">
      <div class="norm_left" id="tabs">
        <div class="tabcontent" id="tab-1">
          <div class="gamedesc_info">
            <h2>
              <?php
              // Display Description of game
              $tricera_desc = get_post_meta($post->ID, 'mabp_description', true);
              if($tricera_desc) { echo $tricera_desc; }
              ?>
            </h2>
          </div>

          <div class="gameinfo_functions">
            <?php if (function_exists('the_views')) { ?>
              <p class="gamemiscstats"><span style="color: #ffffff; font-weight:bold; font: 12px arial;"><?php the_views(); ?></span>
            <?php } ?>

            <p class="gpcats">
              <span style="color: #ffffff; font-weight:bold; font: 14px arial;"><?php _e("Game Categories", "tricera"); ?></span><br />
              <span class="catbtn cat-orange"><?php the_category('</span><span class="catbtn cat-orange"> '); ?></span>
            </p>

            <div class="gptags">
              <span style="color: #ffffff; font-weight:bold; font: 14px arial;">Game tags</span><br /><?php the_tags('<span class="tagbtn tag-blue">','</span><span class="tagbtn tag-blue">','</span>'); ?>
            </div>
          </div>

          <?php if ( (tricera_get_option('display_screenshots') == '1') && myarcade_count_screenshots() ) {
            ?>
            <h3 class="screenshot_title"><?php the_title(''); ?> <?php _e('Screen Shots', 'tricera'); ?></h3>

            <div class="screenshot_box">
              <?php myarcade_all_screenshots( 130, 130, 'screen_thumb' ); ?>
            </div>
            <?php
          }
          ?>

          <div class="admin_actions">
            <?php tricera_admin_links(); ?>
          </div>

          <br />

          <?php
          // Show the game share box
          if ( (tricera_get_option('game_embed_box') == '1')) {
            ?>
            <div class="emdbedcodebox">
              <h2><?php _e("Do You Like This Game?", "tricera"); ?></h2>
              <p><?php _e("Embed this game on your website:", "tricera"); ?></p>
              <form name="select_all"><textarea name="text_area" onClick="javascript:this.form.text_area.focus();this.form.text_area.select();"><a href="<?php echo home_url();?>"><?php bloginfo('name'); ?></a><br /><?php echo get_game($post->ID); ?></textarea>
              </form>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
    </div>

    <?php if (comments_open()){ ?>
      <div class="normal_box">
        <div class="norm_left" id="tabs">
          <div class="tabcontent" id="tab-1">

            <div class="allcomments">
              <?php comments_template(); ?>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
    <?php
  }
}
else {
  ?>
  <div class="ft_gameshowcase">
    <div id="content_tricera">
      <p><?php _e("I'm Sorry, you are looking for something that is not here. Try a different search.", "tricera"); ?></p>
    </div>
  </div>
  <?php
 } ?>