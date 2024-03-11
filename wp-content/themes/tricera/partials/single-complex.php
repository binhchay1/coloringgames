<?php
if (have_posts()) {
  while (have_posts()) {
    the_post();
?>
    <div class="gamepagebox">
      <div class="gameshow">
        <h1>
          <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">
            <?php the_title(); ?>
          </a>
        </h1>

        <div class="options_panel">
          <div class="leader_gamepage">
            <?php if (tricera_get_option('game_page_top_banner')) : ?>
              <div class="leader_adspace_gamepage_top_ad">
                <?php echo tricera_get_option('game_page_top_banner'); ?>
              </div>
            <?php endif; ?>

            <?php if (tricera_display_buttons()) : ?>
              <div id="game_buttons">

                <?php if (tricera_get_option('fullscreen_button') == '1') : ?>
                  <?php // Display Fullscreen button 
                  ?>

                  <a href="<?php echo get_permalink() . 'fullscreen'; ?>" class="fullscreen tooltip" title="<span><?php _e('Play Fullscreen', 'tricera'); ?></span>">
                    <div class="command"></div>
                  </a>
                <?php endif; ?>

                <?php if (tricera_get_option('lights_button') == '1') : ?>
                  <?php // Display Lights-Switch 
                  ?>
                  <div class="command interruptor">
                    <a href="#" title="<span><?php _e('Turn lights off and on', 'tricera'); ?></span>" class="interruptor tooltip"></a>
                  </div>
                <?php endif; ?>

                <?php if (tricera_get_option('favorite_button') == '1') : ?>
                  <div class="command">
                    <?php if (is_user_logged_in()) {
                      tricera_favorite();
                    } else {
                      echo '<img class="favlogin" src="' . get_template_directory_uri() . '/images/favlogin.png" alt="Login to favorite" />';
                    }
                    ?>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="<?php if ((tricera_get_option('game_page_side_banner'))) {    ?>gameplayshow<?php } else { ?>gameplayshow_adoff<?php } ?>">
        <div class="gamebox">
          <?php get_template_part('partials/games', 'play'); ?>
        </div>
      </div>

      <?php if (tricera_get_option('game_page_side_banner')) : ?>
        <div class="vertical_adbox">
          <?php echo tricera_get_option('game_page_side_banner'); ?>
        </div>
      <?php endif; ?>

      <div class="nextprevgames">

        <?php get_template_part('partials/single', 'prevnext-game'); ?>

        <div class="adblock" style="display: none;">
          <?php echo tricera_get_option('game_page_banner'); ?>
        </div>

        <div class="random_gameinc" style="float:right;">

          <?php if (function_exists('related_entries')) {
            related_entries();
          } else {
            $tags = wp_get_post_tags($post->ID);

            if ($tags) {
              $tag_ids = array();

              foreach ($tags as $individual_tag)
                $tag_ids[] = $individual_tag->term_id;

              $args = array(
                'tag__in' => $tag_ids,
                'post__not_in' => array($post->ID),
                'showposts' => 12,  // Number of related posts that will be shown.
                'ignore_sticky_posts' => true
              );

              $my_query = new wp_query($args);

              if ($my_query->have_posts()) {
                echo '<ul class="games-related custom-related">';

                while ($my_query->have_posts()) {
                  $my_query->the_post();
          ?>

                  <li>
                    <a href="<?php the_permalink() ?>" class="tooltip" title='<span>Play <?php the_title_attribute(); ?></span><br />'>
                      <span class="framespan"></span><?php myarcade_thumbnail(100, 100, 'ft_gameimg'); ?></a>
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
      </div>
    </div>

    <div id="turnoff"></div>

    <div class="normal_box">
      <div class="norm_left" id="tabs">
        <div class="tabcontent" id="tab-1">
          <div style="margin-bottom: 20px;">
            <h2 class="text-game-title">Detailed description of the <?php echo single_post_title() ?></h2>
          </div>
          <div class="gamedesc_info">

            <?php
            $get_post_meta = get_post_meta(get_the_ID());
            $date = get_the_modified_date('F j, Y', get_the_ID());
            $dateTime = get_the_modified_date('Y-m-d', get_the_ID());

            ?>
            <style>
              .stats-wrapper {
                display: flex;
                flex-wrap: wrap;
                margin: 0 0 0 90px;
              }

              .game-meta {
                display: table;
                flex-grow: 1;
                line-height: 2;
              }

              .meta-row {
                display: table-row;
                width: 100%;
              }

              .meta-label {
                border-top: #ddd solid 1px;
              }

              .meta-value {
                display: table-cell;
                border-top: #ddd solid 1px;
                padding: 0 10px;
                text-align: left;
                width: 50%;
              }
            </style>
            <div class="stats-wrapper">
              <ul class="game-meta info">
                <li class="meta-row">
                  <div class="meta-label subtle">Game Type</div>
                  <div class="meta-value"><?php echo $get_post_meta['mabp_game_type'][0] ?></div>
                </li>
                <li class="meta-row">
                  <div class="meta-label subtle">Technology</div>
                  <div class="meta-value"><?php echo $get_post_meta['mabp_technology'][0] ?></div>
                </li>
                <li class="meta-row">
                  <div class="meta-label subtle">Supported Devices</div>
                  <div class="meta-value"><?php echo $get_post_meta['mabp_supported'][0] ?></div>
                </li>
                <li class="meta-row">
                  <div class="meta-label subtle">Game Resolution</div>
                  <div class="meta-value"><?php echo $get_post_meta['mabp_width'][0] ?> x <?php echo $get_post_meta['mabp_height'][0] ?></div>
                </li>
              </ul>
              <ul class="game-meta info">
                <li class="meta-row">
                  <div class="meta-label subtle">Last Updated</div>
                  <div class="meta-value">
                    <time datetime="<?php echo $dateTime ?>"><?php echo $date ?></time>
                  </div>
                </li>
                <li class="meta-row">
                  <div class="meta-label subtle">Play Count</div>
                  <div class="meta-value"><?php echo $get_post_meta['myarcade_plays'][0] ?></div>
                </li>
                <li class="meta-row">
                  <div class="meta-label subtle">Platform</div>
                  <div class="meta-value"><?php echo $get_post_meta['mabp_platform'][0] ?></div>
                </li>
                <li class="meta-row">
                  <div class="meta-label subtle">Rating</div>
                  <div class="meta-value"><?php echo $get_post_meta['mabp_rating'][0] ?>%</div>
                </li>
              </ul>
            </div>
          </div>
          <h2><?php
              // Display Description of game
              $tricera_desc = get_post_meta($post->ID, 'mabp_description', true);
              if ($tricera_desc) {
                echo $tricera_desc;
              }
              ?>
          </h2>

          <div class="gameinfo_functions" style="margin-top: 20px">
            <?php if (function_exists('the_views')) { ?>
              <div class="gamemiscstats">
                <span style="color: #ffffff; font-weight:bold; font: 12px arial;"><?php the_views(); ?></span>
              </div>
            <?php } ?>

            <div class="gpcats">
              <span style="color: #ffffff; font-weight:bold; font: 14px arial;"><?php _e("Game Categories", "tricera"); ?></span><br />
              <span class="catbtn cat-orange"><?php the_category('</span><span class="catbtn cat-orange"> '); ?></span>
            </div>

            <div class="gptags">
              <span style="color: #ffffff; font-weight:bold; font: 14px arial;"><?php _e('Game tags', 'tricera'); ?></span><br />
              <?php the_tags('<span class="tagbtn tag-blue">', '</span><span class="tagbtn tag-blue">', '</span>'); ?>
            </div>
          </div>

          <?php if ((tricera_get_option('display_screenshots') == '1') && myarcade_count_screenshots()) {
          ?>
            <h3 class="screenshot_title"><?php the_title(''); ?> <?php _e('Screen Shots', 'tricera'); ?></h3>
            <div class="screenshot_box">
              <?php myarcade_all_screenshots(130, 130, 'screen_thumb'); ?>
            </div>
          <?php
          }
          ?>

          <div class="random_gameinc" style="margin-top: 20px;">

            <?php if (function_exists('related_entries')) {
              related_entries();
            } else {
              $tags = wp_get_post_tags($post->ID);

              if ($tags) {
                $tag_ids = array();

                foreach ($tags as $individual_tag)
                  $tag_ids[] = $individual_tag->term_id;

                $args = array(
                  'tag__in' => $tag_ids,
                  'post__not_in' => array($post->ID),
                  'showposts' => 12,  // Number of related posts that will be shown.
                  'ignore_sticky_posts' => true
                );

                $my_query = new wp_query($args);

                if ($my_query->have_posts()) {
                  echo '<p style="font-size: 18px; color: white">You may like</p>';
                  echo '<ul class="games-related custom-related" style="background: none !important; padding-top: 20px !important">';

                  while ($my_query->have_posts()) {
                    $my_query->the_post();
            ?>

                    <li>
                      <a href="<?php the_permalink() ?>" class="tooltip" title='<span>Play <?php the_title_attribute(); ?></span><br />'>
                        <span class="framespan"></span><?php myarcade_thumbnail(100, 100, 'ft_gameimg'); ?></a>
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

          <div class="admin_actions">
            <?php tricera_admin_links(); ?>
          </div>

          <br />

          <?php
          // Show the game share box
          if ((tricera_get_option('game_embed_box') == '1')) {
          ?>
            <div class="emdbedcodebox">
              <h2><?php _e("Do You Like This Game?", "tricera"); ?></h2>
              <p><?php _e("Embed this game on your website:", "tricera"); ?></p>
              <form name="select_all"><textarea name="text_area" onClick="javascript:this.form.text_area.focus();this.form.text_area.select();"><a href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a><br /><?php echo get_game($post->ID); ?></textarea>
              </form>
            </div>
          <?php
          }
          ?>

          <?php if (tricera_get_option('game_page_bottom_banner')) : ?>
            <div class="leader_adspace_bottom">
              <?php echo tricera_get_option('game_page_bottom_banner'); ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php if (comments_open()) { ?>
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
  } // end while
} else {
  ?>
  <div class="ft_gameshowcase">
    <div id="content_tricera">
      <p><?php _e("I'm Sorry, you are looking for something that is not here. Try a different search.", "tricera"); ?></p>
    </div>
  </div>
<?php } ?>