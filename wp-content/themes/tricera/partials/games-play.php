<?php
	global $post;
?>
<div id="game_wrap">
  <?php
  // Check if progress bar is enabled.
  if ( get_option('tricera_progressbarstatus') == 'enable' ) {
    // Progress bar has been activated.
    // Resize the game to 0px while the progress bar is displayed
    // This will load the game in the background...
    $gamesize='0px';

    // Include the progress bar javascript code
    get_template_part('/inc/myabp_progressbar');
    //include (TEMPLATEPATH . '/inc/myabp_progressbar.php');
    ?>

    <center>
    <div id="showprogressbar" style="display:block; margin: 15px 0px;">
      <?php
      // Show the progress bar banner if available
      $banner_progress = stripslashes( get_option('tricera_loadinggameadcode') );
      if ( !empty($banner_progress) ) {
        ?>
        <div id="loadinggame_ad" style="margin: 15px auto;">
          <?php echo $banner_progress; ?>
        </div>
        <?php
      }
      ?>

      <?php // Display the progress bar ?>
      <div id="progressbar">
        <span id="progresstext">0%</span>
        <div id="progressbarloadbg">&thinsp;</div>
      </div>
    </div> <?php // end id showprogressbar ?>
    </center>

    <div id="progressbarloadtext" style="display:none; text-align: center; margin: 20px auto;"  onclick="window.hide();">
      <?php echo get_option('tricera_progressbartextload'); ?>
    </div>
    <?php
  }
  else {
    // Progressbar is disabled.
    // Set game size to 100%
    $gamesize='100%';
  }
  ?>

  <div class="clear"></div>

  <?php // Display the game ?>
  <div id="my_game" style="overflow:hidden; height: <?php echo $gamesize; ?>; width: <?php echo $gamesize; ?>;">
	<div class="cont1">
      <div class="cont2">
        <div>
          <div id="escenario">
            <div id="play_game">
              <?php
              if (function_exists('get_game')) { ?>
                <div id="bordeswf">
                  <?php
                  $embedcode = get_game($post->ID);
                  global $mypostid; $mypostid = $post->ID;
                  echo myarcade_get_leaderboard_code();
                  echo $embedcode;
                  ?>
                </div>
              <?php } ?>
            </div><?php // end play_game ?>
          </div>
        </div>
      </div>
    </div>
  </div><?php // end id my_game ?>
</div> <?php // end game wrap ?>