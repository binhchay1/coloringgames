<?php
/**
 * MyScoresPresenter Settings Page
 */
function myscore_options() {

  // Check, if we have to update templates..
  if ( isset( $_POST['myscore_update'] ) ) {
    update_option('myscore_template_today',       trim($_POST['myscore_template_today']));
    update_option('myscore_template_latest',      trim($_POST['myscore_template_latest']));
    update_option('myscore_template_muser',       trim($_POST['myscore_template_muser']));
    update_option('myscore_template_game',        trim($_POST['myscore_template_game']));
    update_option('myscore_template_play_duration', trim($_POST['myscore_template_play_duration']));
    update_option('myscore_template_leaderboard', trim($_POST['myscore_template_leaderboard']));

    $use_css = (isset($_POST['myscore_include_css'])) ? true : false;
    update_option('myscore_include_css', $use_css);

    echo '<div id="message" class="updated fade"><p><strong>' . __("Settings updated.", 'myscorespresenter') . '</strong></p></div>';
  }
?>
<div class="wrap">

  <div id="icon-options-general" class="icon32"><br /></div>
  <h2><?php _e("MyScoresPresenter", 'myscorespresenter'); ?></h2>

  <br /><br />
  <h3><?php _e("Available Variables", 'myscorespresenter'); ?></h3>

  <table class="widefat fixed">
    <thead>
    <tr>
      <th scope="col"><?php _e("Variable", 'myscorespresenter'); ?></th>
      <th scope="col"><?php _e("Description", 'myscorespresenter'); ?></th>
      <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>%AVATAR%</strong></td>
        <td><?php _e("Display user's avatar", 'myscorespresenter'); ?></td>
        <td></td>
      </tr>
      <tr>
        <td><strong>%USERNAME%</strong></td>
        <td><?php _e("Display the username", 'myscorespresenter'); ?></td>
        <td></td>
      </tr>
      <tr>
        <td><strong>%SCORE%</strong></td>
        <td><?php _e("Display the score of a game", 'myscorespresenter'); ?></td>
        <td></td>
      </tr>
      <tr>
        <td><strong>%GAMENAME%</strong></td>
        <td><?php _e("Display the name of a game (without link)", 'myscorespresenter'); ?></td>
        <td></td>
      </tr>
      <tr>
        <td><strong>%GAME%</strong></td>
        <td><?php _e("Display the game name with the game link", 'myscorespresenter'); ?></td>
        <td></td>
      </tr>
      <tr>
        <td><strong>%GAMEPLAYS%</strong></td>
        <td><?php _e("Display the game plays of a user", 'myscorespresenter'); ?></td>
        <td></td>
      </tr>
      <tr>
        <td><strong>%PLAY_DURATION%</strong></td>
        <td><?php _e("Display the play duration used on the leaderboard widget", 'myscorespresenter'); ?></td>
        <td></td>
      </tr>
    </tbody>
  </table>

  <h3><?php _e("Scores Templates", "myscorespresenter"); ?></h3>

  <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" name="myscore" id="myscore">
    <input type="hidden" name="myscore_update" id="myscore_update" value="true" />

    <table class="widefat fixed">
      <thead>
      <tr>
        <th scope="col"><?php _e("Name", 'myscorespresenter'); ?></th>
        <th scope="col"><?php _e("Value", 'myscorespresenter'); ?></th>
        <th scope="col"><?php _e("Allowed Placeholders", 'myscorespresenter'); ?></th>
      </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <h3><?php _e("Todays Scores", 'myscorespresenter'); ?></h3>
            <i><?php _e("Shows game scores that are scored today.", 'myscorespresenter'); ?></i>
          </td>
          <td>
            <textarea rows="5" style="width:90%" id="myscore_template_today" name="myscore_template_today"><?php echo htmlspecialchars(stripslashes(get_option('myscore_template_today'))); ?></textarea>
            <br />
            <?php _e("Default:", 'myscorespresenter'); ?> %USERNAME% <?php _e("on", 'myscorespresenter'); ?> %GAME% (%SCORE%)
          </td>
          <td>
            %AVATAR%<br />
            %USERNAME%<br />
            %SCORE%<br />
            %GAMENAME%<br />
            %GAME%<br />
          </td>
        </tr>

        <tr>
          <td>
            <h3><?php _e("Latest Scores", 'myscorespresenter'); ?></h3>
            <i><?php _e("Shows latest scores.", 'myscorespresenter'); ?></i>
          </td>
          <td>
            <textarea rows="5" style="width:90%" id="myscore_template_latest" name="myscore_template_latest"><?php echo htmlspecialchars(stripslashes(get_option('myscore_template_latest'))); ?></textarea>
            <br />
            <?php _e("Default:", 'myscorespresenter'); ?> &lt;strong&gt;%USERNAME%&lt;/strong&gt; <?php _e("on", 'myscorespresenter'); ?> %GAME%
          </td>
          <td>
            %AVATAR%<br />
            %USERNAME%<br />
            %SCORE%<br />
            %GAMENAME%<br />
            %GAME%<br />
          </td>
        </tr>

        <tr>
          <td>
            <h3><?php _e("Most active Players", 'myscorespresenter'); ?></h3>
            <i><?php _e("Shows players ordered by game plays.", 'myscorespresenter'); ?></i>
          </td>
          <td>
            <textarea rows="5" style="width:90%" id="myscore_template_muser" name="myscore_template_muser"><?php echo htmlspecialchars(stripslashes(get_option('myscore_template_muser'))); ?></textarea>
            <br />
            <?php _e("Default:", 'myscorespresenter'); ?> &lt;strong&gt;%USERNAME%&lt;/strong&gt; - %GAMEPLAYS% <?php _e("plays", 'myscorespresenter'); ?>
          </td>
          <td>
            %AVATAR%<br />
            %USERNAME%<br />
            %GAMEPLAYS%<br />
          </td>
        </tr>

        <tr>
          <td>
            <h3><?php _e("Single Game Scores", 'myscorespresenter'); ?></h3>
            <i><?php _e("Shows best player of a single game.", 'myscorespresenter'); ?></i>
          </td>
          <td>
            <textarea rows="5" style="width:90%" id="myscore_template_game" name="myscore_template_game"><?php echo htmlspecialchars(stripslashes(get_option('myscore_template_game'))); ?></textarea>
            <br />
            <?php _e("Default:", 'myscorespresenter'); ?> &lt;strong&gt;%USERNAME%&lt;/strong&gt; - %SCORE%
          </td>
          <td>
            %AVATAR%<br />
            %USERNAME%<br />
            %SCORE%<br />
            %GAMENAME%<br />
            %GAME%<br />
          </td>
        </tr>

        <tr>
          <td>
            <h3><?php _e("Leaderboard by Play Duration", 'myscorespresenter'); ?></h3>
            <i><?php _e("Shows players ordered by total play duration.", 'myscorespresenter'); ?></i>
          </td>
          <td>
            <textarea rows="5" style="width:90%" id="myscore_template_play_duration" name="myscore_template_play_duration"><?php echo htmlspecialchars(stripslashes(get_option('myscore_template_play_duration'))); ?></textarea>
            <br />
            <?php _e("Default:", 'myscorespresenter'); ?> &lt;strong&gt;%USERNAME%&lt;/strong&gt; - %PLAY_DURATION%
          </td>
          <td>
            %USERNAME%<br />
            %PLAY_DURATION%<br />
          </td>
        </tr>

        <tr>
          <td>
            <h3><?php _e("Leaderboards Todays, Weekly, Monthly", 'myscorespresenter'); ?></h3>
            <i><?php _e("Shows players ordered by total scores for a day, week or month.", 'myscorespresenter'); ?></i>
          </td>
          <td>
            <textarea rows="5" style="width:90%" id="myscore_template_play_duration" name="myscore_template_leaderboard"><?php echo htmlspecialchars(stripslashes(get_option('myscore_template_leaderboard'))); ?></textarea>
            <br />
            <?php _e("Default:", 'myscorespresenter'); ?> &lt;strong&gt;%USERNAME%&lt;/strong&gt; - %SCORE%
          </td>
          <td>
            %USERNAME%<br />
            %SCORE%<br />
          </td>
        </tr>

        <tr>
          <td>
            <h3><?php _e("Include MyScoresPresenter Styles", 'myscorespresenter'); ?></h3>
            <i><?php _e("Activate this to use the default MyScoresPresenter CSS stylings for Widgets.", 'myscorespresenter'); ?></i>
          </td>
          <td>
            <input type="checkbox" id="myscore_include_css" name="myscore_include_css" <?php if ( get_option('myscore_include_css') ) echo 'checked'; ?> /> <?php _e("Include CSS", 'myscorespresenter'); ?><br />
          </td>
          <td></td>
        </tr>

        <tr>
          <td colspan="3">
            <input type="submit" name="myscore_update" value="<?php _e("Save Settings", 'myscorespresenter'); ?>" class="button-primary" style="margin:20px;">
          </td>
        </tr>
      </tbody>
    </table>

  </form>



  <div style="clear:both"> </div>

  <h2><?php _e("Usage", 'myscorespresenter'); ?></h2>

  <h3><?php _e("Widgets", 'myscorespresenter'); ?></h3>
  <p><?php _e("The easiest way to display scores on your site is to use MyScorePresenter widgets. Go to Appearance -> Widgets and drop the desired widgets on your sidebar.", 'myscorespresenter'); ?></p>

  <h3><?php _e("Manual Theme Integration And Function Reference", 'myscorespresenter'); ?></h3>

  <p><?php _e("MyScoresPresenter can be integrated into your theme without widgets, but you will need to edit your template files.", 'myscorespresenter'); ?></p>

  <h4><?php _e("Display Todays Scores", 'myscorespresenter'); ?></h4>
  <p><?php _e("Use the function 'myscore_get_todays_scores(limit)' to show todays scores. The optional parameter limit can be used to set the count of displayed scores. Default value of limit is 10.", 'myscorespresenter'); ?></p>
  <p><strong><?php _e("Example:", 'myscorespresenter'); ?></strong></p>
  &lt;?php if (function_exists(myscore_get_todays_scores) ) : ?&gt;
  <br />
  &lt;h2&gt;<?php _e("Todays Scores", 'myscorespresenter'); ?>&lt;/h2&gt;
  <br />
  &lt;ul&gt;
  <br />
  &lt;?php echo myscore_get_todays_scores(); ?&gt;
  <br />
  &lt;/ul&gt;
  <br />
  &lt;?php endif; ?&gt;
  <br />

  <h4><?php _e("Display Most Active Players", 'myscorespresenter'); ?></h4>
  <p>
  <?php _e("Use the function 'myscore_get_most_active_users(limit)' to show most active players. The optional parameter limit can be used to set the count of displayed players. Default value of limit is 10.", 'myscorespresenter'); ?>
  </p>
  <p><strong><?php _e("Example:", 'myscorespresenter'); ?></strong></p>
  &lt;?php if (function_exists(myscore_get_most_active_users) ) : ?&gt;
  <br />
  &lt;h2&gt;<?php _e("Top Players", 'myscorespresenter'); ?>&lt;/h2&gt;
  <br />
  &lt;ul&gt;
  <br />
  &lt;?php echo myscore_get_most_active_users(); ?&gt;
  <br />
  &lt;/ul&gt;
  <br />
  &lt;?php endif; ?&gt;
  <br />

  <h4><?php _e("Display Most Active Players", 'myscorespresenter'); ?></h4>
  <p>
  <?php _e("Use the function 'myscore_get_most_active_users(limit)' to show the latest scores. The optional parameter limit can be used to set the count of displayed scores. Default value of limit is 10.", 'myscorespresenter'); ?>
  </p>
  <p><strong><?php _e("Example:", 'myscorespresenter'); ?></strong></p>
  &lt;?php if (function_exists(myscore_get_latest_scores) ) : ?&gt;
  <br />
  &lt;h2&gt;<?php _e("Latest Scores", 'myscorespresenter'); ?>&lt;/h2&gt;
  <br />
  &lt;ul&gt;
  <br />
  &lt;?php echo myscore_get_latest_scores(); ?&gt;
  <br />
  &lt;/ul&gt;
  <br />
  &lt;?php endif; ?&gt;
  <br />

  <h4><?php _e("Display Best Scores Of A Single Game", 'myscorespresenter'); ?></h4>
  <p>
  <?php _e("Use the function 'myscore_get_game_scores(limit)' to show the best scores of a single game. It make sense to use this function on a singe game page. The optional parameter limit can be used to set the count of displayed scores. Default value of limit is 10.", 'myscorespresenter'); ?>
  </p>
  <p><strong><?php _e("Example:", 'myscorespresenter'); ?></strong></p>
  &lt;?php if ( is_single() ) :?&gt;
  &lt;?php if (function_exists(myscore_get_game_scores) ) : ?&gt;
  <br />
  &lt;h2&gt;<?php _e("Best Players", 'myscorespresenter'); ?>&lt;/h2&gt;
  <br />
  &lt;ul&gt;
  <br />
  &lt;?php echo myscore_get_latest_scores(); ?&gt;
  <br />
  &lt;/ul&gt;
  <br />
  &lt;?php endif; ?&gt;
  <br />
  &lt;?php endif; ?&gt;
  <br />


  <div style="margin: 20px 0px 20px 0px;padding: 10px;text-align: right;">
    <p>
      <?php _e("MyScoresPresenter", 'myscorespresenter'); ?> v<?php echo MYSCORE_VERSION;?> |
      <a href="https://myarcadeplugin.com" title="WordPress Arcade" target="_blank">MyArcadePlugin</a>
    </p>
    </div>
  </div>
<div style="clear:both"> </div>
<?php
}
