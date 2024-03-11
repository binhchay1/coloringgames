<?php
/**
 * BuddyPress Contest Content Template
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Get user ID
$user_id = bp_displayed_user_id();
?>
<div id="myscores_bp_wrap">

  <?php echo apply_filters( 'myscores_bp_user_medals_title', "<h2>" . __("Medals", 'myscorespresenter') . "</h2>" ); ?>

  <?php do_action( 'myscores_bp_before_user_medals' ); ?>

  <?php
  // Display latest user scores
  $medals = myscore_get_users_medals( $user_id, apply_filters( 'myscore_bp_user_medals', 10 ) );

  if ( ! $medals ) {
    ?>
    <div class="myscore-template-notice">
      <p><?php printf ( __( "%s hasn&rsquo;t achieved any medals.", 'myscorespresenter'), get_the_author_meta( 'display_name', $user_id ) ); ?></p>
    </div>
    <?php
  }
  else {
    ?>
    <table class="myscore-bp-user-medals">
      <thead>
        <tr>
          <th class="thgame" scope="col"><?php _e("Game", 'myscorespresenter'); ?></th>
          <th scope="col">&nbsp;</th>
          <th class="thmedal" scope="col"><?php _e("Medal", 'myscorespresenter'); ?></th>
          <th scope="col"><?php _e("Description", 'myscorespresenter'); ?></th>
          <th class="thscore" scope="col"><?php _e("Score", 'myscorespresenter'); ?></th>
        </tr>
      </thead>

      <tbody>
      <?php
      foreach ( $medals as $medal ) {
        $post_id = myscore_get_post_id_by_tag( $medal->game_tag );
        if ( ! $post_id ) continue;

        // Get contest's permalink
        $permalink  = get_permalink( $post_id );
        $post_title = get_the_title( $post_id );
        ?>
        <tr id="myscore-bp-user-medal-<?php echo $post_id; ?>">
          <td class="tdgamethumb">
            <a href="<?php echo $permalink; ?>" title="<?php echo $post_title; ?>">
              <img src="<?php echo myscore_get_thumbnail_url( $post_id ); ?>" width="45" height="45" alt="<?php echo $post_title; ?>" />
            </a>
          </td>
          <td>
            <a href="<?php echo $permalink; ?>" title="<?php echo $post_title; ?>">
              <?php echo apply_filters( 'myscore_bp_game_name', $post_title ); ?>
            </a>
          </td>
          <td class="tdmedalthumb">
            <?php if ( !empty( $medal->thumbnail ) ) : ?>
              <img src="<?php echo $medal->thumbnail; ?>" width="45" height="45" alt="<?php echo $medal->name; ?>" title="<?php echo $medal->name; ?>" />
            <?php else: ?>
            <?php endif; ?>
          </td>
          <td class="tdmedalname"><?php echo $medal->name; ?></td>
          <td class="tdscore"><?php echo $medal->score; ?></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
    </table>
  <?php } ?>

  <?php echo apply_filters( 'myscores_bp_user_highscores_title', "<h2>" . __("High Scores", 'myscorespresenter') . "</h2>" ); ?>

  <?php do_action( 'myscore_bp_before_user_highscores' ); ?>

  <?php
  // Display latest user scores
  $highscores = myscore_get_users_highscore_per_game( $user_id, apply_filters( 'myscore_bp_user_highscores', 10 ) );

  if ( ! $highscores ) {
    ?>
    <div class="myscore-template-notice">
      <p><?php printf ( __( "%s hasn&rsquo;t achieved any high scores.", 'myscorespresenter'), get_the_author_meta( 'display_name', $user_id ) ); ?></p>
    </div>
    <?php
  }
  else {
    ?>
    <table class="myscore-bp-user-scores">
      <thead>
        <tr>
          <th class="thgame" scope="col"><?php _e("Game", 'myscorespresenter'); ?></th>
          <th scope="col">&nbsp;</th>
          <th class="thscore" scope="col"><?php _e("Score", 'myscorespresenter'); ?></th>
        </tr>
      </thead>

      <tbody>
      <?php
      foreach ( $highscores as $score ) {
        $post_id = myscore_get_post_id_by_tag( $score->game_tag );
        if ( ! $post_id ) continue;

        // Get contest's permalink
        $permalink  = get_permalink( $post_id );
        $post_title = get_the_title( $post_id );
        ?>
        <tr id="myscore-bp-user-scores-<?php echo $post_id; ?>">
          <td class="tdgamethumb">
            <a href="<?php echo $permalink; ?>" title="<?php echo $post_title; ?>">
              <img src="<?php echo myscore_get_thumbnail_url( $post_id ); ?>" width="45" height="45" alt="<?php echo $post_title; ?>" />
            </a>
          </td>
          <td>
            <a href="<?php echo $permalink; ?>" title="<?php echo $post_title; ?>">
              <?php echo apply_filters( 'myscore_bp_game_name', $post_title ); ?>
            </a>
          </td>
          <td class="tdscore"><?php echo $score->score; ?></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
    </table>
  <?php } ?>

  <?php echo apply_filters( 'myscores_bp_user_scores_title', "<h2>" . __("Latest Scores", 'myscorespresenter') . "</h2>" ); ?>

  <?php do_action( 'myscore_bp_before_user_scores' ); ?>

  <?php
  // Display latest user scores
  $latest_scores = myscore_get_users_latest_scores( $user_id, apply_filters( 'myscore_bp_user_scores', 10 ) );

  if ( ! $latest_scores ) {
    ?>
    <div class="myscore-template-notice">
      <p><?php printf ( __( "%s hasn&rsquo;t submitted any scores.", 'myscorespresenter'), get_the_author_meta( 'display_name', $user_id ) ); ?></p>
    </div>
    <?php
  }
  else {
    ?>
    <table class="myscore-bp-user-scores">
      <thead>
        <tr>
          <th class="thgame" id="myscore-bp-user-scores-image" scope="col"><?php _e("Game", 'myscorespresenter'); ?></th>
          <th id="myscore-bp-user-scores-name" scope="col">&nbsp;</th>
          <th class="thscore" id="myscore-bp-user-scores-score" scope="col"><?php _e("Score", 'myscorespresenter'); ?></th>
        </tr>
      </thead>

      <tbody>
      <?php
      foreach ( $latest_scores as $score ) {
        $post_id = myscore_get_post_id_by_tag( $score->game_tag );
        if ( ! $post_id ) continue;

        // Get contest's permalink
        $permalink  = get_permalink( $post_id );
        $post_title = get_the_title( $post_id );
        ?>
        <tr id="myscore-bp-user-scores-<?php echo $post_id; ?>">
          <td class="tdgamethumb">
            <a href="<?php echo $permalink; ?>" title="<?php echo $post_title; ?>">
              <img src="<?php echo myscore_get_thumbnail_url( $post_id ); ?>" width="45" height="45" alt="<?php echo $post_title; ?>" />
            </a>
          </td>
          <td>
            <a href="<?php echo $permalink; ?>" title="<?php echo $post_title; ?>">
              <?php echo apply_filters( 'myscore_bp_game_name', $post_title ); ?>
            </a>
          </td>
          <td class="tdscore"><?php echo $score->score; ?></td>
        </tr>
        <?php
      }
      ?>
      </tbody>
    </table>
  <?php } ?>

  <?php do_action( 'myscore_bp_after_user_scores' ); ?>
</div>
